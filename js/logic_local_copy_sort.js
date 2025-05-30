// File: js/logic_local_copy_sort.js
// Exportierte Funktion, die aus staging (output/) in das übergebene Ziel kopiert

export async function processLocalMetaCopy(stagingHandle, destinationHandle) {
  // 1. Sicherstellen, dass stagingHandle vorhanden ist
  //    (stagingHandle kommt aus generateLocalMeta oder aus destinationHandle.getDirectoryHandle('output'))

  // 2. Alle Meta-Dateien aus staging → dest kopieren
  for await (const entry of stagingHandle.values()) {
    if (entry.kind === "file" && entry.name.endsWith("-meta.txt")) {
      const file = await entry.getFile();
      const mfHandle = await destinationHandle.getFileHandle(entry.name, {
        create: true,
      });
      const writable = await mfHandle.createWritable();
      await writable.write(await file.text());
      await writable.close();
      logMessage(`✅ Meta kopiert: ${entry.name}`);
    }
  }

  logMessage("🚀 Alle Meta-Dateien verschoben nach Zielordner.");
}

// Hilfsfunktion für Log-Ausgaben
function logMessage(msg) {
  const log = document.getElementById("log");
  log.textContent += `\n${msg}`;
  log.scrollTop = log.scrollHeight;
}
