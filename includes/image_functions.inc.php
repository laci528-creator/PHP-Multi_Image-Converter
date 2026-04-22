
<?php

function Convert_Bild(string $pfad, int $size_neu): bool {
    $ok = false;

    if (file_exists($pfad) && $size_neu > 0) {
        $bildinfos = getimagesize($pfad);
        if ($bildinfos === false) {
            return false;
        }

        $breite_alt = $bildinfos[0];
        $hoehe_alt = $bildinfos[1];

        if ($breite_alt <= 0 || $hoehe_alt <= 0) {
            return false;
        }

        $seitenverhaltnis = $breite_alt / $hoehe_alt;

        if ($seitenverhaltnis > 1) {
            $breite_neu = $size_neu;
            $hoehe_neu = (int) floor($breite_neu / $seitenverhaltnis);
        } else {
            $hoehe_neu = $size_neu;
            $breite_neu = (int) floor($hoehe_neu * $seitenverhaltnis);
        }

        $dateiinfos = pathinfo($pfad);
        $nameOhneExt = $dateiinfos["filename"];
        $ext = $dateiinfos["extension"];
        $neuerDateiname = $nameOhneExt . "_" . $size_neu . "px." . $ext;

        $verzeichnis = $dateiinfos["dirname"] . "/" . $size_neu . "/";
        $pfad_neu = $verzeichnis . $neuerDateiname;

        if (!file_exists($verzeichnis)) {
            $ok = mkdir($verzeichnis, 0755, true);
        } else {
            $ok = true;
        }

        if ($ok) {
            switch ($bildinfos["mime"]) {
                case "image/jpeg":
                    $resource_alt = imagecreatefromjpeg($pfad);
                    $resource_neu = imagescale($resource_alt, $breite_neu, $hoehe_neu);
                    $ok = imagejpeg($resource_neu, $pfad_neu);
                    break;

                case "image/png":
                    $resource_alt = imagecreatefrompng($pfad);
                    $resource_neu = imagescale($resource_alt, $breite_neu, $hoehe_neu);
                    $ok = imagepng($resource_neu, $pfad_neu);
                    break;

                case "image/webp":
                    $resource_alt = imagecreatefromwebp($pfad);
                    $resource_neu = imagescale($resource_alt, $breite_neu, $hoehe_neu);
                    $ok = imagewebp($resource_neu, $pfad_neu);
                    break;

                case "image/avif":
                    $resource_alt = imagecreatefromavif($pfad);
                    $resource_neu = imagescale($resource_alt, $breite_neu, $hoehe_neu);
                    $ok = imageavif($resource_neu, $pfad_neu);
                    break;

                case "image/gif":
                    $resource_alt = imagecreatefromgif($pfad);
                    $resource_neu = imagescale($resource_alt, $breite_neu, $hoehe_neu);
                    $ok = imagegif($resource_neu, $pfad_neu);
                    break;
            }
        }
    }

    if (isset($resource_alt) && $resource_alt instanceof GdImage) {
        imagedestroy($resource_alt);
    }

    if (isset($resource_neu) && $resource_neu instanceof GdImage) {
        imagedestroy($resource_neu);
    }

    return $ok;
}

function buildPreview(string $mappe, string $originalName, int $groesse): string {
    $info = pathinfo($originalName);
    $neuerDateiname = $info["filename"] . "_" . $groesse . "px." . $info["extension"];

    return '<img style="max-width:300px;height:auto;" src="' . $mappe . $groesse . '/' . rawurlencode($neuerDateiname) . '"><br><p>' . htmlspecialchars($neuerDateiname) . '</p>';
}


?>