<!doctype html>
<html lang="it">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Archivio localizzato in italiano del System Reference Document (SRD 5.2.1) di Wizards of the Coast">
    <title><?= e($pageTitle) ?> · SRD Italian Archive</title>
    <base href="<?= e(appUrl()) ?>">
    <script>
        (() => {
            const theme = window.localStorage.getItem('isa:theme')
                || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
            if (theme === 'dark' || theme === 'light') document.documentElement.dataset.theme = theme;
        })();
    </script>
    <link rel="stylesheet" href="assets/css/style.css?v=<?= e((string) filemtime(__DIR__ . '/../assets/css/style.css')) ?>">
    <link rel="icon" href="assets/images/italian_srd_archive.png" type="image/png">
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

    <button class="back-to-top" type="button" aria-label="Torna in cima" hidden>⌃</button>
    <script src="assets/js/app.js?v=20"></script>
</body>
</html>
