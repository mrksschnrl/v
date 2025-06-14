/**
 * File: js/ui_handlers.js
 *
 * Initialisiert allgemeine UI-Handler für File-Sort.
 * Abhängigkeiten (global):
 *   – window.initFolderPicker  (folder-picker.js)
 *   – window.initMetaWorkflow  (meta-workflow.js)
 */
(function () {
  document.addEventListener("DOMContentLoaded", () => {
    /* ───── 1. Helper ────────────────────────────────────────────── */
    const $ = (id) => {
      const el = document.getElementById(id);
      if (!el) console.warn(`[ui_handlers] Element #${id} nicht gefunden.`);
      return el;
    };

    /* ───── 2. Globale Ordner-Handles ────────────────────────────── */
    let srcDirHandle  = null;
    let destDirHandle = null;

    /* ───── 3. DOM-Elemente ──────────────────────────────────────── */
    const selectSrcBtn   = $("selectSrcBtn");
    const selectDestBtn  = $("selectDestBtn");
    const sourceManual   = $("sourceManual");
    const destManual     = $("destManual");

    const fromInput      = $("from-date");
    const toInput        = $("to-date");
    const fromRawInput   = $("from-raw");
    const toRawInput     = $("to-raw");
    const customInput    = $("custom-date");
    const customRawInput = $("custom-raw");

    const btnToday       = $("btn-today");
    const btnYesterday   = $("btn-yesterday");
    const btnLast7       = $("btn-last7");

    const btnGenMeta     = $("btn-gen-meta");
    const btnSortFiles   = $("btn-sort-files");

    const logUI          = $("log");

    /* ───── 4. Initialzustände ───────────────────────────────────── */
    if (btnGenMeta)   btnGenMeta.disabled   = true;
    if (btnSortFiles) btnSortFiles.disabled = true;

    /* ───── 5. Logging-Utility ───────────────────────────────────── */
    function logMessage(msg) {
      const time = new Date().toLocaleTimeString();
      if (logUI) {
        logUI.textContent += `\n[${time}] ${msg}`;
        logUI.scrollTop = logUI.scrollHeight;
      } else {
        console.log(`[${time}] ${msg}`);
      }
    }

    /* ───── 6. Date ⇄ Raw Synchronisation ───────────────────────── */
    function syncDateAndRaw(dateEl, rawEl) {
      if (!dateEl || !rawEl) return;
      dateEl.addEventListener("change", () => {
        rawEl.value = dateEl.value.replace(/-/g, "");
      });
      rawEl.addEventListener("input", () => {
        const v = rawEl.value;
        if (/^\d{8}$/.test(v)) {
          dateEl.value = `${v.slice(0, 4)}-${v.slice(4, 6)}-${v.slice(6)}`;
        }
      });
    }
    syncDateAndRaw(fromInput,   fromRawInput);
    syncDateAndRaw(toInput,     toRawInput);
    syncDateAndRaw(customInput, customRawInput);

    /* ───── 7. Schnellwahl-Buttons ──────────────────────────────── */
    btnToday?.addEventListener("click", () => {
      const today = new Date().toISOString().slice(0, 10);
      [fromInput, toInput].forEach(el => el && (el.value = today));
      [fromRawInput, toRawInput].forEach(el => el && (el.value = today.replace(/-/g, "")));
      fromInput?.dispatchEvent(new Event("change"));
      toInput?.dispatchEvent(new Event("change"));
      logMessage("🔘 Schnellwahl: Heute");
    });

    btnYesterday?.addEventListener("click", () => {
      const d = new Date();
      d.setDate(d.getDate() - 1);
      const day = d.toISOString().slice(0, 10);
      [fromInput, toInput].forEach(el => el && (el.value = day));
      [fromRawInput, toRawInput].forEach(el => el && (el.value = day.replace(/-/g, "")));
      fromInput?.dispatchEvent(new Event("change"));
      toInput?.dispatchEvent(new Event("change"));
      logMessage("🔘 Schnellwahl: Gestern");
    });

    btnLast7?.addEventListener("click", () => {
      const end   = new Date();
      const start = new Date();
      start.setDate(end.getDate() - 6);
      const startStr = start.toISOString().slice(0, 10);
      const endStr   = end.toISOString().slice(0, 10);
      if (fromInput)    fromInput.value    = startStr;
      if (toInput)      toInput.value      = endStr;
      if (fromRawInput) fromRawInput.value = startStr.replace(/-/g, "");
      if (toRawInput)   toRawInput.value   = endStr.replace(/-/g, "");
      logMessage("🔘 Schnellwahl: Letzte 7 Tage");
    });

    /* ───── 8. Folder-Picker integrieren ────────────────────────── */
    if (typeof window.initFolderPicker === "function") {
      window.initFolderPicker({
        selectSrcBtn,
        selectDestBtn,
        sourceManual,
        destManual,
        logMessage,
      });
    } else {
      console.error("[ui_handlers] initFolderPicker nicht definiert.");
    }

    /* ───── 9. Metadaten-Button freischalten ────────────────────── */
    function updateGenMetaState() {
      if (window.srcDirHandle)  srcDirHandle  = window.srcDirHandle;
      if (window.destDirHandle) destDirHandle = window.destDirHandle;
      if (btnGenMeta) btnGenMeta.disabled = !(srcDirHandle && destDirHandle);
    }

    /* Ereignisse, die Handles ändern könnten */
    window.addEventListener("focus", updateGenMetaState);                 // Dialog zu
    sourceManual?.addEventListener("input", updateGenMetaState);          // manuell
    destManual  ?.addEventListener("input", updateGenMetaState);
    selectSrcBtn ?.addEventListener("click", () => setTimeout(updateGenMetaState, 500));
    selectDestBtn?.addEventListener("click", () => setTimeout(updateGenMetaState, 500));

    /* ───── 10. Nur Logging für manuelle Pfade ──────────────────── */
    sourceManual?.addEventListener("change", () =>
      logMessage(`🔤 Manueller Quellpfad: ${sourceManual.value}`));
    destManual?.addEventListener("change", () =>
      logMessage(`🔤 Manueller Zielpfad: ${destManual.value}`));

    /* ───── 11. Meta-Workflow an meta-workflow.js übergeben ─────── */
    if (typeof window.initMetaWorkflow === "function") {
      window.initMetaWorkflow({
        btnGenMeta,
        btnSortFiles,
        fromInput,
        toInput,
        logMessage,
        getSrcDirHandle : () => srcDirHandle,
        getDestDirHandle: () => destDirHandle,
      });
    } else {
      console.error("[ui_handlers] initMetaWorkflow nicht gefunden.");
    }
  });
})();
