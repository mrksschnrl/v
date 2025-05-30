// File: /v/packages/file-sort/js/copy-workflow.js

import { getDateRange } from "./utils-date.js";
import { copyAndVerifyFile, moveIfVerified } from "./logic_file_ops.js";

/** Recursiver Dateiwalk über Unterordner */
async function* walkFiles(dirHandle, prefix = "") {
  for await (const [name, handle] of dirHandle.entries()) {
    const rel = prefix ? `${prefix}/${name}` : name;
    if (handle.kind === "file") {
      yield [rel, handle];
    } else if (handle.kind === "directory") {
      yield* walkFiles(handle, rel);
    }
  }
}

/**
 * Führt Tasks mit maximal `limit` gleichzeitig aus.
 * @param {Function[]} tasks – Array async-Funktionen ohne Argumente
 * @param {number} limit
 */
async function parallelLimit(tasks, limit = 6) {
  const executing = new Set();
  const results = [];
  for (const task of tasks) {
    const p = Promise.resolve().then(() => task());
    results.push(p);
    executing.add(p);
    const clean = () => executing.delete(p);
    p.then(clean, clean);
    if (executing.size >= limit) {
      await Promise.race(executing);
    }
  }
  return Promise.all(results);
}

/**
 * Initialisiert Copy-/Move-/Upload-Workflow mit sichtbarem Fortschritt.
 */
export function initCopyWorkflow({
  btnSortFiles,
  fromInput,
  toInput,
  modeSelect,
  toggleFileLog,
  fileListUI,
  copyProgressUI,
  progressBar,
  logMessage,
  getSrcDirHandle,
  getDestDirHandle,
  useServerOutput,
  serverPath,
  uploadToServer,
}) {
  btnSortFiles.addEventListener("click", async () => {
    const src = getSrcDirHandle();
    const dest = getDestDirHandle();
    if (!src || !dest) {
      alert("Bitte Quell- und Zielordner wählen.");
      return;
    }

    btnSortFiles.disabled = true;
    fileListUI.innerHTML = "";
    copyProgressUI.innerHTML = "";
    logMessage(
      `🚀 Start ${modeSelect.value === "move" ? "Verschieben" : "Kopieren"}`
    );

    // 1. Datum-Intervalle ermitteln
    let startDate, endDate;
    try {
      ({ startDate, endDate } = getDateRange("range", {
        rangeFrom: fromInput.value,
        rangeTo: toInput.value,
      }));
    } catch (err) {
      alert(err.message);
      btnSortFiles.disabled = false;
      return;
    }
    const startMs = startDate.getTime();
    const endMs = endDate.getTime();

    // 2. Dateien rekursiv sammeln und nach Datum filtern
    const files = [];
    for await (const [relPath, handle] of walkFiles(src)) {
      const file = await handle.getFile();
      if (file.lastModified >= startMs && file.lastModified <= endMs) {
        files.push({ relPath, handle });
      }
    }

    const total = files.length;
    progressBar.max = total;
    progressBar.value = 0;

    // 3. Progress-Info-Label neben der Bar
    let progressInfo = document.getElementById("progress-info");
    if (!progressInfo) {
      progressInfo = document.createElement("div");
      progressInfo.id = "progress-info";
      progressBar.parentNode.insertBefore(
        progressInfo,
        progressBar.nextSibling
      );
    }
    progressInfo.textContent = `0 / ${total}`;

    // 4. Tasks für Parallelisierung aufbauen
    const tasks = files.map(({ relPath, handle }) => async () => {
      // reiner Dateiname ohne Verzeichnisanteile
      const baseName = relPath.split(/[/\\]/).pop();

      // a) Logging im UI
      if (toggleFileLog.checked) {
        const li = document.createElement("li");
        li.textContent = relPath;
        copyProgressUI.appendChild(li);
      }

      // b) Copy/Move/Upload
      try {
        if (useServerOutput) {
          const file = await handle.getFile();
          await uploadToServer(file, serverPath);
          logMessage(`🌐 Hochgeladen: ${relPath}`);
          if (modeSelect.value === "move") {
            logMessage("⚠️ Verschieben im Servermodus nicht unterstützt");
          }
        } else if (modeSelect.value === "move") {
          await moveIfVerified(handle, src, dest, baseName);
          logMessage(`🚚 Verschoben (Quelldatei blieb): ${baseName}`);
        } else {
          await copyAndVerifyFile(handle, dest, baseName);
          logMessage(`📄 Kopiert: ${baseName}`);
        }
      } catch (err) {
        console.error(err);
        logMessage(`❌ Fehler bei ${baseName}: ${err.message}`);
      }

      // c) Fortschritt aktualisieren
      progressBar.value++;
      progressInfo.textContent = `${progressBar.value} / ${total}`;
    });

    // 5. Tasks ausführen mit Limit
    await parallelLimit(tasks, 6);

    logMessage(`🏁 Vorgang abgeschlossen (${total} Dateien)`);
    btnSortFiles.disabled = false;
  });
}
