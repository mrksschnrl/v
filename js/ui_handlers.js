// ui_handlers.js
import { initDateUI } from "./date-ui.js";
import { initMetaWorkflow } from "./meta-workflow.js";
// ggf. weitere Importe für andere Workflows

/**
 * Initialisiert alle UI-Handler, sobald das DOM bereit ist.
 */
document.addEventListener("DOMContentLoaded", () => {
  // Datumspicker und Schnellwahl
  const fromInput = document.getElementById("from-date");
  const toInput = document.getElementById("to-date");
  const fromRawInput = document.getElementById("from-raw");
  const toRawInput = document.getElementById("to-raw");
  const customInput = document.getElementById("custom-date");
  const customRawInput = document.getElementById("custom-raw");
  const btnToday = document.getElementById("btn-today");
  const btnYesterday = document.getElementById("btn-yesterday");
  const btnLast7 = document.getElementById("btn-last7");

  initDateUI({
    fromInput,
    toInput,
    fromRawInput,
    toRawInput,
    customInput,
    customRawInput,
    btnToday,
    btnYesterday,
    btnLast7,
  });

  // Metadaten-Workflow
  const btnGenMeta = document.getElementById("btn-gen-meta");
  const btnSortFiles = document.getElementById("btn-sort-files");

  // Beispiel-Implementierung: Ersetzt bei Bedarf mit File System Access API-Logik
  const getSrcDirHandle = () => window.srcDirHandle;
  const getDestDirHandle = () => window.destDirHandle;
  const logMessage = (msg) => {
    const log = document.getElementById("log");
    const p = document.createElement("p");
    p.textContent = msg;
    log.appendChild(p);
  };

  initMetaWorkflow({
    btnGenMeta,
    btnSortFiles,
    fromInput,
    toInput,
    logMessage,
    getSrcDirHandle,
    getDestDirHandle,
  });

  // Hier weitere Workflows initialisieren...
});
