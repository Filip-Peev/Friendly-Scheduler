<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quick Templates</title>
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='0.9em' font-size='90'>⚡</text></svg>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .template-card {
            cursor: pointer;
            transition: transform 0.15s, box-shadow 0.15s;
        }
        .template-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 .5rem 1rem rgba(0,0,0,.15) !important;
        }
        .template-card:active {
            transform: translateY(0);
        }
    </style>
</head>

<body class="bg-light">
    <div class="container my-4" style="max-width: 600px;">

        <h1 class="text-center mb-2">
            <a href="index.php" class="text-decoration-none text-dark">⚡ Quick Templates</a>
        </h1>
        <p class="text-center text-muted mb-4">One click to schedule. All set for today.</p>

        <div class="row g-3">

            <div class="col-12">
                <form method="POST" action="index.php">
                    <input type="hidden" name="action" value="new_meeting">
                    <input type="hidden" name="title" value="Кратко Кафе">
                    <input type="hidden" name="date" id="date1">
                    <input type="hidden" name="time" value="18:30">
                    <input type="hidden" name="details" value="Фантастико На Йерусалим">
                    <button type="submit" class="template-card card shadow-sm border-0 w-100 text-start">
                        <div class="card-body">
                            <div class="d-flex align-items-center gap-3">
                                <span class="fs-1">☕</span>
                                <div>
                                    <h5 class="card-title mb-0 text-primary">Кратко Кафе</h5>
                                    <small class="text-muted">Фантастико На Йерусалим · 18:30</small>
                                </div>
                            </div>
                        </div>
                    </button>
                </form>
            </div>

            <div class="col-12">
                <form method="POST" action="index.php">
                    <input type="hidden" name="action" value="new_meeting">
                    <input type="hidden" name="title" value="Обяд Заедно">
                    <input type="hidden" name="date" id="date2">
                    <input type="hidden" name="time" value="12:30">
                    <input type="hidden" name="details" value="Ресторант Капри">
                    <button type="submit" class="template-card card shadow-sm border-0 w-100 text-start">
                        <div class="card-body">
                            <div class="d-flex align-items-center gap-3">
                                <span class="fs-1">🍽️</span>
                                <div>
                                    <h5 class="card-title mb-0 text-primary">Обяд Заедно</h5>
                                    <small class="text-muted">Ресторант Капри · 12:30</small>
                                </div>
                            </div>
                        </div>
                    </button>
                </form>
            </div>

            <div class="col-12">
                <form method="POST" action="index.php">
                    <input type="hidden" name="action" value="new_meeting">
                    <input type="hidden" name="title" value="Вечеря">
                    <input type="hidden" name="date" id="date3">
                    <input type="hidden" name="time" value="19:00">
                    <input type="hidden" name="details" value="Ресторант Син Синор">
                    <button type="submit" class="template-card card shadow-sm border-0 w-100 text-start">
                        <div class="card-body">
                            <div class="d-flex align-items-center gap-3">
                                <span class="fs-1">🍷</span>
                                <div>
                                    <h5 class="card-title mb-0 text-primary">Вечеря</h5>
                                    <small class="text-muted">Ресторант Син Синор · 19:00</small>
                                </div>
                            </div>
                        </div>
                    </button>
                </form>
            </div>

            <div class="col-12">
                <form method="POST" action="index.php">
                    <input type="hidden" name="action" value="new_meeting">
                    <input type="hidden" name="title" value="Разходка в Парка">
                    <input type="hidden" name="date" id="date4">
                    <input type="hidden" name="time" value="17:00">
                    <input type="hidden" name="details" value="Борисова Градина, вход от Южния парк">
                    <button type="submit" class="template-card card shadow-sm border-0 w-100 text-start">
                        <div class="card-body">
                            <div class="d-flex align-items-center gap-3">
                                <span class="fs-1">🌳</span>
                                <div>
                                    <h5 class="card-title mb-0 text-primary">Разходка в Парка</h5>
                                    <small class="text-muted">Борисова Градина · 17:00</small>
                                </div>
                            </div>
                        </div>
                    </button>
                </form>
            </div>

            <div class="col-12">
                <form method="POST" action="index.php">
                    <input type="hidden" name="action" value="new_meeting">
                    <input type="hidden" name="title" value="Игрална Вечер">
                    <input type="hidden" name="date" id="date5">
                    <input type="hidden" name="time" value="19:30">
                    <input type="hidden" name="details" value="Носете любими игри! Закуски осигурени.">
                    <button type="submit" class="template-card card shadow-sm border-0 w-100 text-start">
                        <div class="card-body">
                            <div class="d-flex align-items-center gap-3">
                                <span class="fs-1">🎲</span>
                                <div>
                                    <h5 class="card-title mb-0 text-primary">Игрална Вечер</h5>
                                    <small class="text-muted">Носете любими игри! · 19:30</small>
                                </div>
                            </div>
                        </div>
                    </button>
                </form>
            </div>

            <div class="col-12">
                <form method="POST" action="index.php">
                    <input type="hidden" name="action" value="new_meeting">
                    <input type="hidden" name="title" value="Филм Вечер">
                    <input type="hidden" name="date" id="date6">
                    <input type="hidden" name="time" value="20:00">
                    <input type="hidden" name="details" value="Кино Лъки, или вкъщи с проектор">
                    <button type="submit" class="template-card card shadow-sm border-0 w-100 text-start">
                        <div class="card-body">
                            <div class="d-flex align-items-center gap-3">
                                <span class="fs-1">🎬</span>
                                <div>
                                    <h5 class="card-title mb-0 text-primary">Филм Вечер</h5>
                                    <small class="text-muted">Кино Лъки · 20:00</small>
                                </div>
                            </div>
                        </div>
                    </button>
                </form>
            </div>

        </div>

        <div class="text-center mt-4">
            <a href="index.php" class="btn btn-outline-secondary">← Back to Scheduler</a>
        </div>

    </div>

    <script>
        var today = new Date().toISOString().split('T')[0];
        for (var i = 1; i <= 6; i++) {
            var el = document.getElementById('date' + i);
            if (el) el.value = today;
        }
    </script>
</body>

</html>
