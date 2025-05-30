// Datei: js/ui_handlers.js

import { generateMetaInRange } from "./logic_meta_local.js";
import { copyAndVerifyFile, moveIfVerified } from "./logic_file_ops.js";

let srcDirHandle = null;
let destDirHandle = null;
let useServerOutput = false;
let serverPath = "";
let showFileLog = true;

// --- DOM-Elemente ---
const fromInput = document.getElementById("from-date");
const toInput = document.getElementById("to-date");
const fromRawInput = document.getElementById("from-date-raw");
const toRawInput = document.getElementById("to-date-raw");
const customInput = document.getElementById("btn-custom-date");
const customRawInput = document.getElementById("btn-custom-date-raw");
const btnToday = document.getElementById("btn-today");
const btnYesterday = document.getElementById("btn-yesterday");
const btnLast7 = document.getElementById("btn-last7");
const btnGenMeta = document.getElementById("generate-meta");
const btnSortFiles = document.getElementById("sort-files");
const modeSelect = document.getElementById("mode");
const toggleFileLogCb = document.getElementById("toggle-file-log");
const selectSrcBtn = document.getElementById("select-folder");
const sourceManual = document.getElementById("source-manual");
const selectDestBtn = document.getElementById("select-destination");
const destManual = document.getElementById("destination-manual");
const setServerBtn = document.getElementById("set-server-output");
const revertLocalBtn = document.getElementById("revert-local-output");
const fileListUI = document.getElementById("file-list");
const progressBar = document.getElementById("progress-bar");
const copyProgressUI = document.getElementById("copy-progress");
const logUI = document.getElementById("log");

// --- Initial State ---
btnGenMeta.disabled = true;
btnSortFiles.disabled = true;

// --- Utility-Logging ---
function logMessage(msg) {
  const time = new Date().toLocaleTimeString();
  logUI.textContent += `\n[${time}] ${msg}`;
  logUI.scrollTop = logUI.scrollHeight;
}

// --- Datum ↔ Raw synchronisieren ---
function syncDateAndRaw(dateInput, rawInput) {
  dateInput.addEventListener("change", () => {
    rawInput.value = dateInput.value.replace(/-/g, "");
  });
  rawInput.addEventListener("input", () => {
    const v = rawInput.value;
    if (/^\d{8}$/.test(v))
      dateInput.value = `${v.slice(0, 4)}-${v.slice(4, 6)}-${v.slice(6)}`;
  });
}
syncDateAndRaw(fromInput, fromRawInput);
syncDateAndRaw(toInput, toRawInput);
syncDateAndRaw(customInput, customRawInput);

// --- Schnellwahl-Buttons ---
btnToday.addEventListener("click", () => {
  const today = new Date().toISOString().slice(0, 10);
  fromInput.value = toInput.value = today;
  fromRawInput.value = toRawInput.value = today.replace(/-/g, "");
  // Trigger change to sync raw fields
  fromInput.dispatchEvent(new Event("change"));
  toInput.dispatchEvent(new Event("change"));
  logMessage("🔘 Schnellwahl: Heute");
});
btnYesterday.addEventListener("click", () => {
  const d = new Date();
  d.setDate(d.getDate() - 1);
  const day = d.toISOString().slice(0, 10);
  fromInput.value = toInput.value = day;
  fromRawInput.value = toRawInput.value = day.replace(/-/g, "");
  fromInput.dispatchEvent(new Event("change"));
  toInput.dispatchEvent(new Event("change"));
  logMessage("🔘 Schnellwahl: Gestern");
});
btnLast7.addEventListener("click", () => {
  // Letzte 7 Tage inklusive heute
  const end = new Date(); // heute
  const start = new Date();
  start.setDate(start.getDate() - 6); // 6 Tage davor
  const startStr = start.toISOString().slice(0, 10);
  const endStr = end.toISOString().slice(0, 10);
  fromInput.value = startStr;
  toInput.value = endStr;
  fromRawInput.value = startStr.replace(/-/g, "");
  toRawInput.value = endStr.replace(/-/g, "");
  logMessage("🔘 Schnellwahl: Letzte 7 Tage");
});
customInput.addEventListener("change", () => {
  fromInput.value = toInput.value = customInput.value;
  fromRawInput.value = toRawInput.value = customInput.value.replace(/-/g, "");
  fromInput.dispatchEvent(new Event("change"));
  toInput.dispatchEvent(new Event("change"));
  logMessage(`🔘 Bestimmter Tag: ${customInput.value}`);
});

// --- Quell- & Zielordner wählen ---
selectSrcBtn.addEventListener("click", async () => {
  console.debug("selectSrcBtn clicked");
  try {
    srcDirHandle = await window.showDirectoryPicker();
    sourceManual.value = srcDirHandle.name || "Ordner gewählt";
    logMessage(`📂 Quellordner gewählt: ${sourceManual.value}`);
  } catch (err) {
    logMessage(`⚠️ Quellordner-Auswahl abgebrochen`);
    return;
  }
  if (destDirHandle) btnGenMeta.disabled = false;
});
selectDestBtn.addEventListener("click", async () => {
  console.debug("selectDestBtn clicked");
  try {
    destDirHandle = await window.showDirectoryPicker();
    destManual.value = destDirHandle.name || "Ordner gewählt";
    logMessage(`💾 Zielordner gewählt: ${destManual.value}`);
  } catch (err) {
    logMessage(`⚠️ Zielordner-Auswahl abgebrochen`);
    return;
  }
  if (srcDirHandle) btnGenMeta.disabled = false;
});

// --- Manuelle Pfadangaben ---
sourceManual.addEventListener("change", () => {
  logMessage(`🔤 Manueller Quellpfad eingegeben: ${sourceManual.value}`);
});
destManual.addEventListener("change", () => {
  serverPath = destManual.value;
  logMessage(`🔤 Manueller Zielpfad eingegeben: ${serverPath}`);
});

// --- Server-Output Switch ---
setServerBtn.addEventListener("click", () => {
  useServerOutput = true;
  logMessage("🌐 Server-Ziel aktiviert");
});
revertLocalBtn.addEventListener("click", () => {
  useServerOutput = false;
  logMessage("💾 Lokaler Zielordner aktiv");
});

// --- File-Log Toggle ---
toggleFileLogCb.addEventListener("change", () => {
  showFileLog = toggleFileLogCb.checked;
});

// --- Metadaten erzeugen ---
btnGenMeta.addEventListener("click", async () => {
  if (!srcDirHandle) return alert("Bitte Quellordner wählen.");
  if (!destDirHandle && !useServerOutput)
    return alert("Bitte Zielordner wählen.");
  if (!fromInput.value || !toInput.value)
    return alert("Bitte Start- und End-Datum wählen.");

  const startDate = new Date(fromInput.value);
  const endDate = new Date(toInput.value);
  try {
    await generateMetaInRange(srcDirHandle, destDirHandle, startDate, endDate);
    btnSortFiles.disabled = false;
  } catch (err) {
    console.error(err);
    alert("Fehler beim Erstellen der Meta-Datei: " + err.message);
  }
});

// --- Dateien kopieren/verschieben ---
btnSortFiles.addEventListener("click", async () => {
  if (!srcDirHandle) return alert("Quellordner fehlt.");
  if (!destDirHandle && !useServerOutput) return alert("Zielordner fehlt.");
  btnSortFiles.disabled = true;
  fileListUI.innerHTML = "";
  copyProgressUI.innerHTML = "";
  logMessage(
    `🚀 Start ${modeSelect.value === "move" ? "Verschieben" : "Kopieren"}`
  );

  const files = [];
  const start = new Date(fromInput.value).getTime();
  const end = new Date(toInput.value).getTime();
  for await (const [name, handle] of srcDirHandle.entries()) {
    if (handle.kind !== "file") continue;
    const file = await handle.getFile();
    if (file.lastModified >= start && file.lastModified <= end) {
      files.push({ name, handle });
    }
  }

  progressBar.max = files.length;
  progressBar.value = 0;

  for (const { name, handle } of files) {
    if (showFileLog) {
      const li = document.createElement("li");
      li.textContent = name;
      copyProgressUI.appendChild(li);
    }
    try {
      if (useServerOutput) {
        await window.uploadToServer(await handle.getFile(), serverPath);
        logMessage(`🌐 Hochgeladen: ${name}`);
      } else if (modeSelect.value === "move") {
        await moveIfVerified(handle, srcDirHandle, destDirHandle, name);
        logMessage(`🚚 Verschoben: ${name}`);
      } else {
        await copyAndVerifyFile(handle, destDirHandle, name);
        logMessage(`📄 Kopiert: ${name}`);
      }
    } catch (err) {
      console.error(err);
      logMessage(`❌ Fehler bei ${name}: ${err.message}`);
    }
    progressBar.value++;
  }
  logMessage(`🏁 Vorgang abgeschlossen (${files.length} Dateien)`);
});
