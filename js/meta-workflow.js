// meta-workflow.js

import { getDateRange } from "./utils-date.js";
import { generateMetaInRange } from "./logic_meta_local.js";
import { generateServerMeta } from "./logic_meta_server.js";

/**
 * Fragt Schreib-/Leseberechtigung für ein Verzeichnis ab und fordert sie ggf. an.
 * @param {FileSystemDirectoryHandle} dirHandle
 */
async function ensureWritePermission(dirHandle) {
  const opts = { mode: "readwrite" };
  if ((await dirHandle.queryPermission(opts)) === "granted") return;
  if ((await dirHandle.requestPermission(opts)) === "granted") return;
  throw new Error("Berechtigung für das Zielverzeichnis verweigert.");
}

/**
 * Initialisiert den „Metadaten erstellen“-Workflow.
 */
export function initMetaWorkflow({
  btnGenMeta,
  btnSortFiles,
  fromInput,
  toInput,
  logMessage,
  getSrcDirHandle,
  getDestDirHandle,
}) {
  btnGenMeta.addEventListener("click", async () => {
    console.debug("🟡 [DEBUG] Metadaten-Erstellung gestartet");

    // 1. Datum prüfen
    if (!fromInput.value || !toInput.value) {
      alert("Bitte sowohl Start- als auch End-Datum wählen.");
      console.warn("⚠️ [DEBUG] Datum fehlt");
      return;
    }

    // 2. Quelle prüfen
    const src = getSrcDirHandle();
    if (!src) {
      alert("Bitte Quellordner auswählen.");
      console.warn("⚠️ [DEBUG] Quelle fehlt");
      return;
    }

    // 3. Ziel prüfen und Berechtigung
    const dest = getDestDirHandle();
    try {
      await ensureWritePermission(dest);
    } catch (err) {
      alert(err.message);
      return;
    }

    // 4. Metadaten generieren
    try {
      const files = getDateRange(fromInput.value, toInput.value);
      let count;
      if (src.kind === "local") {
        count = await generateMetaInRange(src.handle, files, dest.handle);
      } else {
        count = await generateServerMeta(src.handle, files);
      }

      if (count > 0) {
        logMessage(`✅ meta.txt erstellt (${count} Dateien)`);
        btnSortFiles.disabled = false;
      } else {
        logMessage(`⚠️ Keine Dateien im Zeitraum gefunden.`);
        btnSortFiles.disabled = true;
      }
    } catch (err) {
      console.error("❌ [DEBUG] Fehler beim Erstellen der Metadaten:", err);
      alert("Fehler beim Erstellen der Metadaten: " + err.message);
    }
  });
}
