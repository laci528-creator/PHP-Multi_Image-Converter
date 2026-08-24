<?php
$currentPage = basename($_SERVER['PHP_SELF']);
?>

<nav class="main-nav">
    <a href="index.php" class="<?= $currentPage === 'index.php' ? 'active' : '' ?>">Startseite</a>
    <a href="resize.php" class="<?= $currentPage === 'resize.php' ? 'active' : '' ?>">Skalieren</a>
    <a href="convert.php" class="<?= $currentPage === 'convert.php' ? 'active' : '' ?>">Format konvertieren</a>
    <a href="watermark.php" class="<?= $currentPage === 'watermark.php' ? 'active' : '' ?>">Wasserzeichen</a>
</nav>