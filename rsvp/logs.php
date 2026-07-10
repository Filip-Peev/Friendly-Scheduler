<?php
$db = new PDO('sqlite:scheduler.db');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$logs = $db->query("
    SELECT *
    FROM logs
    ORDER BY created_at DESC, id DESC
    LIMIT 500
")->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Application Logs</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container-fluid container-md my-4">

        <h2 class="mb-3 fs-3 text-center text-md-start">Application Logs</h2>

        <div class="table-responsive shadow-sm rounded bg-white">
            <table class="table table-hover table-sm mb-0 align-middle small text-nowrap text-md-wrap">
                <thead class="table-dark text-center">
                    <tr>
                        <th style="min-width: 140px; width: 160px;">Time</th>
                        <th style="min-width: 130px; width: 150px;">Action</th>
                        <th>Message Details</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($logs as $log):
                        $rawMessage = trim($log['message']);
                        $bgClass = '';
                        $inlineStyle = '';

                        // Parse Action Prefix and Clean Message
                        $action = 'SYSTEM';
                        $cleanMessage = $rawMessage;

                        if (strpos($rawMessage, ':') !== false) {
                            list($prefix, $rest) = explode(':', $rawMessage, 2);
                            $action = trim($prefix);
                            $cleanMessage = trim($rest);
                        }

                        // Determine Color Themes 
                        if ($action === 'DELETED MEETING') {
                            $bgClass = 'table-danger';  // Red
                        } elseif ($action === 'NEW MEETING') {
                            $bgClass = 'table-success'; // Green
                        } elseif ($action === 'NEW RSVP') {
                            $bgClass = 'table-primary'; // Blue
                        } elseif ($action === 'RSVP UPDATED') {
                            // Lowercase for foolproof matching independent of capitalization or weird quoting
                            $lowerMessage = strtolower($rawMessage);

                            if (strpos($lowerMessage, 'yes') !== false && strpos($lowerMessage, 'no') !== false) {
                                // Find out which word came first to determine direction
                                if (strpos($lowerMessage, 'yes') < strpos($lowerMessage, 'no')) {
                                    $inlineStyle = ' style="background-color: #ffe8cc !important; color: #000;"'; // Soft Orange
                                } else {
                                    $inlineStyle = ' style="background-color: #fff3cd !important; color: #000;"'; // Soft Yellow
                                }
                            } else {
                                // Fallback color if it's an update but doesn't mention yes/no specifically
                                $inlineStyle = ' style="background-color: #f8f9fa !important;"';
                            }
                        }
                    ?>
                        <tr class="<?= $bgClass ?>" <?= $inlineStyle ?>>
                            <td class="text-muted fw-semibold text-center" style="font-size: 0.85rem;">
                                <?= htmlspecialchars($log['created_at']) ?>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-dark bg-opacity-75 text-uppercase" style="letter-spacing: 0.5px; font-size: 0.75rem;">
                                    <?= htmlspecialchars($action) ?>
                                </span>
                            </td>
                            <td class="text-center" style="white-space: pre-wrap; word-break: break-word;">
                                <?= htmlspecialchars($cleanMessage) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

    </div>
</body>

</html>