// logic_upload_to_server.js – Upload von Dateien mit Fortschritt + Fehleranzeige

export async function uploadFilesToServer(sourceHandle, username) {
  const log = document.getElementById("log");
  const showEach = document.getElementById("toggle-file-log")?.checked;
  const list = document.getElementById("copy-progress");
  const bar = document.getElementById("progress-bar");

  const logMessage = (msg) => {
    log.textContent += "\n" + msg;
    log.scrollTop = log.scrollHeight;
  };

  if (!sourceHandle || !username) {
    logMessage("❌ Quellordner oder Benutzername fehlt.");
    return;
  }

  const allowedExtensions = [
    ".mp4",
    ".mkv",
    ".mov",
    ".avi",
    ".mp3",
    ".wav",
    ".aac",
    ".flac",
    ".jpg",
    ".jpeg",
    ".png",
    ".webp",
    ".heic",
  ];
  const isAllowedMediaFile = (name) =>
    allowedExtensions.some((ext) => name.toLowerCase().endsWith(ext));

  const entries = [];
  for await (const entry of sourceHandle.values()) {
    if (entry.kind === "file" && isAllowedMediaFile(entry.name)) {
      entries.push(entry);
    }
  }

  if (entries.length === 0) {
    logMessage("⚠️ Keine gültigen Dateien zum Hochladen gefunden.");
    return;
  }

  let count = 0;
  for (const entry of entries) {
    try {
      const file = await entry.getFile();
      const formData = new FormData();
      formData.append("username", username);
      formData.append("file", file);

      const response = await fetch("/v/api/upload_file.php", {
        method: "POST",
        body: formData,
      });

      const result = await response.json();

      if (result.status === "success") {
        count++;
        const percent = Math.round((count / entries.length) * 100);
        bar.value = percent;

        if (showEach) {
          const li = document.createElement("li");
          li.textContent = `✅ Hochgeladen: ${file.name}`;
          list.appendChild(li);
        }
      } else {
        const msg = result.message || result.error || "Unbekannter Fehler";
        logMessage(`❌ Fehler bei ${file.name}: ${msg}`);
      }
    } catch (err) {
      logMessage(`❌ Upload-Fehler bei ${entry.name}: ${err.message}`);
    }
  }

  logMessage(
    `✅ Upload abgeschlossen. ${count}/${entries.length} Dateien übertragen.`
  );
  bar.value = 100;
}
