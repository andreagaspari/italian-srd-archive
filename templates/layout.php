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
            if (theme === 'dark' || theme === 'light') {
                document.documentElement.dataset.theme = theme;
                document.documentElement.dataset.bsTheme = theme;
            }
        })();
    </script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="assets/css/style.css?v=<?= e((string) filemtime(__DIR__ . '/../assets/css/style.css')) ?>">
    <link rel="icon" href="assets/images/italian_srd_archive.png" type="image/png">
</head>
<body class="bg-body">
    <div class="app-shell d-flex flex-column">
        <?php require __DIR__ . '/partials/header.php'; ?>

        <div class="app-layout d-flex flex-grow-1">
            <?php require __DIR__ . '/partials/sidebar.php'; ?>

            <main class="main-content flex-grow-1">
                <?php require $contentView; ?>

                <?php require __DIR__ . '/partials/footer.php'; ?>
            </main>
        </div>
    </div>

    <button class="back-to-top btn btn-outline-primary rounded-circle p-0" type="button" aria-label="Torna in cima" hidden>⌃</button>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="assets/js/app.js?v=23"></script>
</body>
</html>
