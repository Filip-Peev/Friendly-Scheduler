<?php
// 1. Connect to SQLite
$db = new PDO('sqlite:scheduler.db');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// 2. Create tables if they don't exist
$db->exec("CREATE TABLE IF NOT EXISTS meetings (
    id INTEGER PRIMARY KEY AUTOINCREMENT, 
    title TEXT, 
    proposed_date TEXT, 
    proposed_time TEXT,
    details TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
)");
$db->exec("CREATE TABLE IF NOT EXISTS responses (
    id INTEGER PRIMARY KEY AUTOINCREMENT, 
    meeting_id INTEGER, 
    friend_name TEXT, 
    status TEXT
)");
$db->exec("CREATE TABLE IF NOT EXISTS logs (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    message TEXT NOT NULL
)");

// Helper function to append to a log in sqlite
function writeToLog($message)
{
    global $db;

    $stmt = $db->prepare("INSERT INTO logs (message) VALUES (?)");
    $stmt->execute([$message]);
}

// 3. Handle Form Submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $today_str = date('Y-m-d');

    if (isset($_POST['action']) && $_POST['action'] === 'new_meeting') {
        $stmt = $db->prepare("INSERT INTO meetings (title, proposed_date, proposed_time, details) VALUES (?, ?, ?, ?)");
        $stmt->execute([$_POST['title'], $_POST['date'], $_POST['time'], trim($_POST['details'])]);

        writeToLog("NEW MEETING: '{$_POST['title']}' proposed for {$_POST['date']} at {$_POST['time']}.");
    } elseif (isset($_POST['action']) && $_POST['action'] === 'respond') {
        $meeting_id = $_POST['meeting_id'];
        $friend_name = trim($_POST['name']);
        $status = $_POST['status'];

        // Backend Protection: Check if the meeting date is in the past
        $mStmt = $db->prepare("SELECT title, proposed_date FROM meetings WHERE id = ?");
        $mStmt->execute([$meeting_id]);
        $meeting = $mStmt->fetch(PDO::FETCH_ASSOC);

        if ($meeting && $meeting['proposed_date'] < $today_str) {
            writeToLog("BLOCKED RSVP: '$friend_name' tried to RSVP '$status' to a past meeting '{$meeting['title']}' on {$meeting['proposed_date']}.");
        } else {
            $checkStmt = $db->prepare("SELECT id, status FROM responses WHERE meeting_id = ? AND LOWER(friend_name) = LOWER(?)");
            $checkStmt->execute([$meeting_id, $friend_name]);
            $existingResponse = $checkStmt->fetch(PDO::FETCH_ASSOC);

            if ($existingResponse) {
                $updateStmt = $db->prepare("UPDATE responses SET status = ?, friend_name = ? WHERE id = ?");
                $updateStmt->execute([$status, $friend_name, $existingResponse['id']]);
                writeToLog("RSVP UPDATED: '$friend_name' changed choice from '{$existingResponse['status']}' to '$status' for meeting '{$meeting['title']}'.");
            } else {
                $insertStmt = $db->prepare("INSERT INTO responses (meeting_id, friend_name, status) VALUES (?, ?, ?)");
                $insertStmt->execute([$meeting_id, $friend_name, $status]);
                writeToLog("NEW RSVP: '$friend_name' voted '$status' for meeting '{$meeting['title']}'.");
            }
        }
    } elseif (isset($_POST['action']) && $_POST['action'] === 'delete_meeting') {
        $meeting_id = $_POST['meeting_id'];

        // Backend Protection: Check if the meeting date is in the past
        $mStmt = $db->prepare("SELECT title, proposed_date FROM meetings WHERE id = ?");
        $mStmt->execute([$meeting_id]);
        $meeting = $mStmt->fetch(PDO::FETCH_ASSOC);

        if ($meeting && $meeting['proposed_date'] < $today_str) {
            writeToLog("BLOCKED DELETE: Attempted to delete a past meeting '{$meeting['title']}' on {$meeting['proposed_date']}.");
        } else {
            // Delete associated responses first
            $stmt1 = $db->prepare("DELETE FROM responses WHERE meeting_id = ?");
            $stmt1->execute([$meeting_id]);

            // Delete the meeting itself
            $stmt2 = $db->prepare("DELETE FROM meetings WHERE id = ?");
            $stmt2->execute([$meeting_id]);

            writeToLog("DELETED MEETING: Meeting '{$meeting['title']}' scheduled for {$meeting['proposed_date']} was deleted.");
        }
    }
    header("Location: index.php" . (isset($_GET['month']) ? "?month=" . urlencode($_GET['month']) : ""));
    exit;
}

// 4. Get all unique months available in the database for pagination tabs (Format: YYYY-MM)
$monthRows = $db->query("SELECT DISTINCT substr(proposed_date, 1, 7) as meeting_month FROM meetings ORDER BY meeting_month ASC")->fetchAll(PDO::FETCH_ASSOC);

// Determine which month to display
$selected_month = $_GET['month'] ?? '';

// On first visit (if no month is passed in the URL)
if (empty($selected_month)) {
    // Find the month of the LATEST created meeting
    $latestMeeting = $db->query("SELECT proposed_date FROM meetings ORDER BY created_at DESC LIMIT 1")->fetch(PDO::FETCH_ASSOC);

    if ($latestMeeting) {
        $selected_month = substr($latestMeeting['proposed_date'], 0, 7);
    } else {
        // Fallback to current real-world month if database is completely empty
        $selected_month = date('Y-m');
    }
}

// 5. Fetch meetings only for the selected month (Sorted by creation date, newest first)
$stmt = $db->prepare("SELECT * FROM meetings WHERE substr(proposed_date, 1, 7) = ? ORDER BY created_at DESC");
$stmt->execute([$selected_month]);
$meetings = $stmt->fetchAll(PDO::FETCH_ASSOC);

$meetingIds = array_column($meetings, 'id');
$responsesByMeeting = [];
if (!empty($meetingIds)) {
    $placeholders = implode(',', array_fill(0, count($meetingIds), '?'));
    $rStmt = $db->prepare("SELECT * FROM responses WHERE meeting_id IN ($placeholders)");
    $rStmt->execute($meetingIds);
    foreach ($rStmt->fetchAll(PDO::FETCH_ASSOC) as $r) {
        $responsesByMeeting[$r['meeting_id']][] = $r;
    }
}

$default_date = date('Y-m-d');
$default_time = date('H:i');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Friendly Scheduler</title>
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='0.9em' font-size='90'>📅</text></svg>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container my-4" style="max-width: 600px;">

        <h1 class="text-center mb-4">
            <a href="index.php" class="text-decoration-none text-dark">📅 Meeting Scheduler</a>
        </h1>

        <div class="card mb-4 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="card-title m-0">Propose a New Meeting</h5>
                    <a href="templates.php" class="btn btn-sm btn-outline-primary">⚡ Quick Templates</a>
                </div>
                <form method="POST">
                    <input type="hidden" name="action" value="new_meeting">
                    <div class="mb-2">
                        <input type="text" name="title" class="form-control" placeholder="Event Name (e.g., Coffee, Board Games...)" required>
                    </div>
                    <div class="row g-2 mb-2">
                        <div class="col">
                            <input type="date" name="date" class="form-control" value="<?= $default_date ?>" required>
                        </div>
                        <div class="col">
                            <input type="time" name="time" class="form-control" value="<?= $default_time ?>" required>
                        </div>
                    </div>
                    <div class="mb-2">
                        <textarea name="details" class="form-control" rows="2" placeholder="Meeting details / notes (e.g., Bring snacks, host's house...)"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Add Date Option</button>
                </form>
            </div>
        </div>

        <?php if (!empty($monthRows)): ?>
            <ul class="nav nav-pills justify-content-center mb-4 gap-1">
                <?php foreach ($monthRows as $row):
                    $mTime = strtotime($row['meeting_month'] . "-01");
                    $monthLabel = date("M 'y", $mTime);
                    $isActive = ($row['meeting_month'] === $selected_month) ? 'active' : 'btn-outline-primary bg-white';
                ?>
                    <li class="nav-item">
                        <a class="nav-link <?= $isActive ?>" href="index.php?month=<?= urlencode($row['meeting_month']) ?>">
                            <?= htmlspecialchars($monthLabel) ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <h3 class="mb-3">
            Proposed: <?php echo date("F Y", strtotime($selected_month . "-01")); ?>
        </h3>

        <?php if (empty($meetings)): ?>
            <p class="text-muted text-center py-3">No meetings proposed for this month.</p>
        <?php endif; ?>

        <?php foreach ($meetings as $m):
            $is_past_meeting = ($m['proposed_date'] < date('Y-m-d'));
        ?>
            <div class="card mb-3 shadow-sm <?= $is_past_meeting ? 'opacity-75' : '' ?>">
                <div class="card-body">

                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h5 class="card-title text-primary m-0">
                            <?= htmlspecialchars($m['title']) ?>
                            <?= $is_past_meeting ? '<span class="badge bg-secondary ms-1 fs-6">Past Date Locked</span>' : '' ?>
                        </h5>

                        <?php if (!$is_past_meeting): ?>
                            <?php
                            $hasResponses = !empty($responsesByMeeting[$m['id']]);
                            ?>
                            <form method="POST" <?= $hasResponses ? 'onsubmit="return confirm(\'Are you sure you want to delete this meeting option?\');"' : '' ?>>
                                <input type="hidden" name="action" value="delete_meeting">
                                <input type="hidden" name="meeting_id" value="<?= $m['id'] ?>">
                                <button type="submit" class="btn btn-sm btn-outline-danger border-0 p-1" style="line-height: 1; font-size: 1.25rem;">
                                    &times;
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>

                    <?php if (!empty($m['details'])): ?>
                        <p class="text-muted small mb-2 text-wrap" style="white-space: pre-wrap;"><?= htmlspecialchars($m['details']) ?></p>
                    <?php endif; ?>

                    <p class="card-text mb-2">
                        <strong>When:</strong> <?= date("d-m-Y", strtotime($m['proposed_date'])) ?> @ <?= htmlspecialchars($m['proposed_time']) ?>
                    </p>

                    <div class="mb-3">
                        <strong>Responses:</strong>
                        <div class="d-flex flex-wrap gap-1 mt-1">
                            <?php
                            $responses = $responsesByMeeting[$m['id']] ?? [];
                            foreach ($responses as $r) {
                                $badgeColor = ($r['status'] === 'yes') ? 'bg-success' : 'bg-danger';
                                echo "<span class='badge {$badgeColor} p-2'>" . htmlspecialchars($r['friend_name']) . "</span>";
                            }
                            if (empty($responses)) echo "<span class='text-muted small'>None yet</span>";
                            ?>
                        </div>
                    </div>

                    <?php if (!$is_past_meeting): ?>
                        <form method="POST" class="row g-2 align-items-center">
                            <input type="hidden" name="action" value="respond">
                            <input type="hidden" name="meeting_id" value="<?= $m['id'] ?>">
                            <div class="col-6">
                                <input type="text" name="name" class="form-control form-control-sm" placeholder="Your Name" required>
                            </div>
                            <div class="col-6 d-flex gap-1">
                                <button type="submit" name="status" value="yes" class="btn btn-sm btn-outline-success w-100">Yes</button>
                                <button type="submit" name="status" value="no" class="btn btn-sm btn-outline-danger w-100">No</button>
                            </div>
                        </form>
                    <?php else: ?>
                        <p class="text-muted small m-0 italic">🔒 Editing responses is disabled for past events.</p>
                    <?php endif; ?>

                </div>
            </div>
        <?php endforeach; ?>
    </div>
</body>

</html>