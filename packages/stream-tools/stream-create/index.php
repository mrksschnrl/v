<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <title>STREAM-CREATOR</title>
    <link rel="stylesheet" href="css/styles.css">
</head>

<body>

    <div class="container">
        <h1>STREAM-CREATOR</h1>

        <!-- Upload Bereich -->
        <h2>Upload MP4 Dateien</h2>
        <form id="uploadForm" enctype="multipart/form-data">
            <input type="file" id="fileInput" name="files[]" multiple accept="video/mp4">
            <button type="submit">Hochladen</button>
        </form>
        <div id="uploadStatus"></div>

        <hr>

        <!-- Hier könnte später deine Konvertierungsliste erscheinen -->
        <div id="fileList">
            <!-- Dynamische Anzeige der hochgeladenen Dateien möglich -->
        </div>
    </div>

    <script src="js/ui.js"></script>

    <script>
        // Upload-Formular steuern mit Fortschrittsanzeige
        document.getElementById('uploadForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const files = document.getElementById('fileInput').files;
            if (files.length === 0) {
                alert('Bitte wähle mindestens eine Datei aus.');
                return;
            }

            const formData = new FormData();
            for (let file of files) {
                formData.append('files[]', file);
            }

            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'upload.php', true);

            // Fortschritt überwachen
            xhr.upload.onprogress = function(event) {
                if (event.lengthComputable) {
                    const percent = Math.round((event.loaded / event.total) * 100);
                    document.getElementById('uploadStatus').innerText = `Upload läuft: ${percent}% abgeschlossen`;
                }
            };

            xhr.onload = function() {
                if (xhr.status == 200) {
                    document.getElementById('uploadStatus').innerText = xhr.responseText;
                    loadUploadedFiles();
                } else {
                    document.getElementById('uploadStatus').innerText = 'Fehler beim Hochladen!';
                }
            };

            xhr.onerror = function() {
                document.getElementById('uploadStatus').innerText = 'Netzwerkfehler während des Uploads!';
            };

            xhr.send(formData);
        });

        // Dateien aus dem Server-Ordner laden und anzeigen
        async function loadUploadedFiles() {
            const response = await fetch('list_input_files.php');
            const files = await response.json();

            let html = '<h3>Hochgeladene MP4 Dateien:</h3><ul>';
            for (let file of files) {
                html += `<li>${file} 
            <button onclick="convertFile('${file}')">Jetzt konvertieren</button>
        </li>`;
            }
            html += '</ul>';

            document.getElementById('fileList').innerHTML = html;
        }

        // Funktion zum Konvertieren einer Datei
        async function convertFile(fileName) {
            if (!confirm(`Willst du wirklich ${fileName} konvertieren?`)) {
                return;
            }

            document.getElementById('uploadStatus').innerText = `Konvertiere ${fileName}...`;

            const response = await fetch(`convert_to_m3u8.php?file=${encodeURIComponent(fileName)}`);
            const result = await response.text();

            document.getElementById('uploadStatus').innerText = result;

            // Nach Konvertierung Liste neu laden (optional)
        }
    </script>



</body>

</html>