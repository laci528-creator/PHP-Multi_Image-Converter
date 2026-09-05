<?php
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!doctype html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="img/favicon.png" />
    <title>PHP Image Toolkit</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/water.css@2/out/dark.css">
    <link rel="stylesheet" href="css/common.css">
</head>
<body>
<header class="site-header">
    <nav class="main-nav">
        <a href="index.php" class="nav-brand">
            <img
                src="img/favicon.png"
                alt=""
                class="nav-logo"
            >
            <span>PHP Image Toolkit</span>
        </a>

        <div class="nav-links">
            <a href="index.php" class="<?= $currentPage === 'index.php' ? 'active' : '' ?>">Startseite</a>
            <a href="resize.php" class="<?= $currentPage === 'resize.php' ? 'active' : '' ?>">Skalieren</a>
            <a href="convert.php" class="<?= $currentPage === 'convert.php' ? 'active' : '' ?>">Format konvertieren</a>
            <a href="watermark.php" class="<?= $currentPage === 'watermark.php' ? 'active' : '' ?>">Wasserzeichen</a>
        </div>
    </nav>
</header>