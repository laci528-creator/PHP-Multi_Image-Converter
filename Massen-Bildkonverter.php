<?php
require("includes/config.inc.php");
require("includes/common.inc.php");
require("includes/image_functions.inc.php");
require("includes/zip_functions.inc.php");
require("includes/message_functions.inc.php");


error_reporting(E_ALL);
ini_set('display_errors', 1);


$batchId = "";
$zielMappe = "";
$msg = "";
$msg2 = "";
$zipLink = "";
$convertedFiles = [];
$whitelist = ["image/jpeg","image/gif","image/png","image/webp","image/avif"];

if (isset($_POST["HC"])) {
    if (isset($_FILES["myUpload"]["name"][0]) && !empty($_FILES["myUpload"]["name"][0])) {
        if (!empty($_POST["neu_resolution"])) {
            $f = $_FILES["myUpload"];
            $neuResolution = filter_input(INPUT_POST, 'neu_resolution', FILTER_VALIDATE_INT);

            if ($neuResolution === false || $neuResolution === null || $neuResolution < 50 || $neuResolution > 5000) {
                $msg .= errorMessage("Bitte geben Sie eine gültige Auflösung zwischen 50 und 5000 Pixel an.");

            } elseif (count($f["name"]) > 20) {
                $msg .= errorMessage("Bitte laden Sie maximal 20 Bilddateien hoch.");

            } else {
                $batchId = uniqid();
                $zielMappe = "./uploads_bildconverter/" . $batchId . "/";
                if (!is_dir($zielMappe) && !mkdir($zielMappe, 0755, true)) {
                    $msg .= errorMessage("Der Zielordner konnte nicht erstellt werden.");

                } else {

                    for ($i = 0; $i < count($f["name"]); $i++) {
                        $originalName = $f["name"][$i];

                        if ($f["error"][$i] !== 0) {
                            $msg .= errorMessage("Leider ist beim Upload der Datei <strong>" . htmlspecialchars($originalName) . "</strong> ein Fehler aufgetreten.");
                            continue;
                        }

                        $echterMime = mime_content_type($f["tmp_name"][$i]);

                        if ($echterMime === false || !in_array($echterMime, $whitelist)) {
                            $msg .= errorMessage("Leider nicht erlaubte Datei <strong>" . htmlspecialchars($originalName) . "</strong>.");
                            continue;
                        }
                        
                        $zielDatei = $zielMappe . basename($originalName);
                        $okUpload = move_uploaded_file($f["tmp_name"][$i], $zielDatei);

                        if (!$okUpload) {
                            $msg .= errorMessage("Leider konnte die Datei <strong>" . htmlspecialchars($originalName) . "</strong> nicht gespeichert werden.");
                            continue;
                        }

                        $okConvert = Convert_Bild($zielDatei, $neuResolution);

                        if ($okConvert) {
                            $pathInfo = pathinfo($originalName);
                            $neuerDateiname = $pathInfo["filename"] . "_" . $neuResolution . "px." . $pathInfo["extension"];

                            $convertedPfad = $zielMappe . $neuResolution . "/" . $neuerDateiname;
                            $convertedFiles[] = $convertedPfad;


                            $msg .= successMessage("Die Datei <strong>" . htmlspecialchars($originalName) . "</strong> wurde erfolgreich hochgeladen und konvertiert.");
                            $msg2 .= buildPreview($zielMappe, $originalName, $neuResolution);
                        } else {
                            $msg .= errorMessage("Die Datei <strong>" . htmlspecialchars($originalName) . "</strong> wurde hochgeladen, aber die Konvertierung ist fehlgeschlagen.");
                        }
                    }
                }
            }
        } else {
            $msg .= errorMessage("Bitte schreiben Sie die gewünschte Auflösung.");
        }
    } else {
        $msg .= errorMessage("Bitte laden Sie mindestens eine Datei hoch.");
    }
}



if (!empty($convertedFiles)) {
    $zipName = createZip($convertedFiles, $zielMappe, $batchId);

    if ($zipName !== false) {
        $zipLink = '<a class="download-btn" href="download_zip.php?batch=' . rawurlencode($batchId) . '">Alle Bilder als ZIP herunterladen</a>';
    } else {
        $msg .= errorMessage("Die ZIP-Datei konnte nicht erstellt werden.");
    }
}


?>
<!doctype html>
<html lang="de">
	<head>
		<title>Bildkonverter</title>
		<meta charset="utf-8">
		<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/water.css@2/out/dark.css">
		<style>
            .success {
                    margin:0.5em 0;
                    padding:0.2em;
                    border-left:10px solid green;
                    font-style:italic;
                }
            .error {
                    margin:0.5em 0;
                    padding:0.2em;
                    border-left:10px solid red;
                    font-weight:bold;
                    color:red;
                }

            .download-btn {
                    display: inline-block;
                    padding: 0.6em 1em;
                    text-decoration: none;
                    border: 1px solid #888;
                    border-radius: 6px;
                }
        </style>
	</head>
	<body>
		<h1>Bildkonverter für mehrere Dateien</h1>
		
		<form method="post" enctype="multipart/form-data">
			<label>
				Bitte wählen Sie maximal 20 Bilddateien aus (JPG, GIF, PNG, WebP, AVIF):
				<input type="file" name="myUpload[]" multiple><br>
			</label><br>
            <label>
				Bitte wählen Sie die Länge (px) der längeren Seite des Bildes aus:
                <input type="number" name="neu_resolution">
            </label>
			<input type="submit" name="HC" value="Hochladen und Konvertieren">
		</form>
        <br>

		<?php echo($msg); ?>
        <br>
        
		<?php echo $zipLink; ?>

        <h2>Vorschau der konvertierten Bilder</h2>

		<?php
		echo $msg2; ?>

	</body>

</html>