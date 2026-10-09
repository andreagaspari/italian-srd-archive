<!doctype html>
<html lang="it">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Archivio locale SRD di Dungeons & Dragons 5.2.1">
    <title><?= e($pageTitle) ?> · SRD Italia</title>
    <base href="<?= e(appUrl()) ?>">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="app-shell">
        <?php require __DIR__ . '/partials/header.php'; ?>

        <div class="app-layout">
            <?php require __DIR__ . '/partials/sidebar.php'; ?>

            <main class="main-content">
                <?php require $contentView; ?>

                <?php require __DIR__ . '/partials/footer.php'; ?>
            </main>
        </div>
    </div>

    <script src="assets/js/app.js?v=12"></script>
</body>
</html>
