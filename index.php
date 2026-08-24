<?php
require_once __DIR__ . "/includes/config.inc.php";
require_once __DIR__ . "/includes/common.inc.php";
?>
<!doctype html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP Image Toolkit</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/water.css@2/out/dark.css">
    <link rel="stylesheet" href="css/common.css">
</head>
<body>
    <?php require __DIR__ . "/includes/header.inc.php"; ?>

<main>

<h1>PHP Image Toolkit</h1>
<h2>Wählen Sie ein Bildbearbeitungs-Tool aus:</h2>

<div class="tool-grid">

    <a class="tool-card" href="resize.php">
        <h3>Bilder skalieren</h3>
        <p>Mehrere Bilder hochladen, skalieren und als ZIP herunterladen.</p>
    </a>

    <a class="tool-card" href="convert.php">
        <h3>Format konvertieren</h3>
        <p>Mehrere bilder in JPEG, PNG, WebP oder AVIF umwandeln.</p>
    </a>

    <a class="tool-card" href="watermark.php">
        <h3>Wasserzeichen hinzufügen</h3>
        <p>Mehrere JPG-Bilder mit einem PNG-Wasserzeichen versehen.</p>
    </a>

</div>

    <section class="about-section">

        <h2>Über dieses Projekt</h2>

        <p>
            Dieses Projekt entstand aus einem praktischen Problem
            bei der Webentwicklung. Bei meiner ersten Website musste
            ich viele große Bilder einzeln in Photoshop verkleinern
            und in webfreundliche Formate konvertieren. Das war
            zeitaufwendig und wiederholte sich bei weiteren Projekten.
        </p>

        <p>
            Deshalb habe ich das PHP Image Toolkit entwickelt,
            um diese Arbeit zu automatisieren. Mehrere Bilder können
            gleichzeitig konvertiert, verkleinert und mit einem
            Wasserzeichen versehen werden. Ziel des Projekts ist es,
            wiederkehrende Schritte bei der Vorbereitung von Bildern
            für Websites schneller und einfacher zu machen.
        </p>

        <p>
            Gleichzeitig nutzte ich das Projekt, um meine Kenntnisse
            in PHP, Dateiverarbeitung und der GD-Bibliothek praktisch
            zu vertiefen.
        </p>

    </section>
</main>

</body>
</html>