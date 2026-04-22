# PHP-Multi_Image-Converter Englisch und Deutsche sprache

PHP Multi-Image Converter

A PHP-based web application for uploading, resizing, previewing, and downloading multiple images as a ZIP archive.

Features

  upload multiple images at once
  process up to 20 files per upload
  supported formats:
      JPG / JPEG
      GIF
      PNG
      WebP
      AVIF
  resize images based on the longer side
  server-side validation
  preview converted images
  ZIP download for all processed files
  temporary batch folder handling
  automatic cleanup after download

Tech Stack
  
  PHP 8+
  HTML5
  CSS3
  Water.css
  ZipArchive
  fileinfo

How It Works

  Select up to 20 image files.
  Enter the target size in pixels.
  The application validates the files and input.
  Images are uploaded into a temporary batch folder.
  The images are resized and previewed.
  A ZIP archive is generated from the converted files.
  After download, the batch folder is deleted automatically.

Project Structure

 index.php
 download_zip.php
 includes/
        config.inc.php
        common.inc.php
        image_functions.inc.php
        zip_functions.inc.php
        message_functions.inc.php
 uploads_bildconverter/

Validation

  at least 1 file is required
  maximum 20 files per upload
  resolution must be an integer
  resolution must be between 50 and 5000 pixels
  only allowed image MIME types are accepted

Notes

  uploaded files are grouped into temporary batch folders
  the ZIP file and related images are deleted after download
  files with the same name in the same batch may overwrite each other
  
Possible Improvements

  safer duplicate file handling
  finfo_file() based MIME validation
  CSRF protection
  drag and drop upload
  improved UI
  scheduled cleanup for abandoned batch folders

Author

Laszlo Haraszti
Personal learning / portfolio project.

------------------------------------------------------------------------------------------------------------------
PHP Mehrfach-Bildkonverter

Eine PHP-basierte Webanwendung zum Hochladen, Skalieren, Anzeigen und Herunterladen mehrerer Bilder als ZIP-Archiv.

Funktionen

  mehrere Bilder gleichzeitig hochladen
  bis zu 20 Dateien pro Upload verarbeiten
  unterstützte Formate:
    JPG / JPEG
    GIF
    PNG
    WebP
    AVIF
  Größenanpassung anhand der längeren Bildseite
  serverseitige Validierung
  Vorschau der konvertierten Bilder
  ZIP-Download aller verarbeiteten Dateien
  temporäre Batch-Ordner-Verwaltung
  automatische Bereinigung nach dem Download
  
Verwendete Technologien

  PHP 8+
  HTML5
  CSS3
  Water.css
  ZipArchive
  fileinfo

Funktionsweise

  Bis zu 20 Bilddateien auswählen.
  Die Zielgröße in Pixeln eingeben.
  Die Anwendung prüft Dateien und Eingaben.
  Die Bilder werden in einen temporären Batch-Ordner hochgeladen.
  Die Bilder werden skaliert und als Vorschau angezeigt.
  Aus den konvertierten Dateien wird ein ZIP-Archiv erstellt.
  Nach dem Download wird der Batch-Ordner automatisch gelöscht.
  
Projektstruktur
.
├── index.php
├── download_zip.php
├── includes/
│   ├── config.inc.php
│   ├── common.inc.php
│   ├── image_functions.inc.php
│   ├── zip_functions.inc.php
│   └── message_functions.inc.php
└── uploads_bildconverter/

Validierung

  mindestens 1 Datei ist erforderlich
  maximal 20 Dateien pro Upload
  die Auflösung muss eine Ganzzahl sein
  die Auflösung muss zwischen 50 und 5000 Pixel liegen
  nur erlaubte Bild-MIME-Typen werden akzeptiert
  
Hinweise

  hochgeladene Dateien werden in temporären Batch-Ordnern gespeichert
  die ZIP-Datei und die zugehörigen Bilder werden nach dem Download gelöscht
  Dateien mit gleichem Namen innerhalb desselben Batchs können einander überschreiben
  
Mögliche Erweiterungen

  sicherere Behandlung doppelter Dateinamen
  MIME-Prüfung mit finfo_file()
  CSRF-Schutz
  Drag-and-Drop-Upload
  verbesserte Benutzeroberfläche
  geplante Bereinigung nicht heruntergeladener Batch-Ordner
  
Autor

Laszlo Haraszti
Persönliches Lern- / Portfolio-Projekt.

