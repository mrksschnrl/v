// File: js/folder-picker.js

/**
 * Kapselt die Verzeichnis-Auswahl und hält die Handles global.
 * Registriert initFolderPicker als window.initFolderPicker, damit man es global nutzt.
 */
(function () {
  function initFolderPicker({
    selectSrcBtn,
    selectDestBtn,
    sourceManual,
    destManual,
    logMessage,
  }) {
    // Handles auch am window-Objekt (global) speichern!
    window.srcDirHandle = null;
    window.destDirHandle = null;

    selectSrcBtn.addEventListener("click", async () => {
      try {
        const dirHandle = await window.showDirectoryPicker();
        window.srcDirHandle = dirHandle;
        logMessage?.(`📂 Quellordner ausgewählt: ${dirHandle.name}`);
        if (sourceManual) sourceManual.value = dirHandle.name;
      } catch {
        /* abort */
      }
    });

    selectDestBtn.addEventListener("click", async () => {
      try {
        const dirHandle = await window.showDirectoryPicker();
        window.destDirHandle = dirHandle;
        logMessage?.(`📂 Zielordner ausgewählt: ${dirHandle.name}`);
        if (destManual) destManual.value = dirHandle.name;
      } catch {
        /* abort */
      }
    });

    sourceManual?.addEventListener("change", () => {
      logMessage?.(`🔤 Manueller Quellpfad: ${sourceManual.value}`);
      // Optional: Hier könntest du window.srcDirHandle zurücksetzen, falls manuell überschrieben wurde
    });
    destManual?.addEventListener("change", () => {
      logMessage?.(`🔤 Manueller Zielpfad: ${destManual.value}`);
      // Optional: Hier könntest du window.destDirHandle zurücksetzen, falls manuell überschrieben wurde
    });

    // Liefert weiterhin Zugriff für seltene Spezialfälle
    return {
      getSrcDirHandle: () => window.srcDirHandle,
      getDestDirHandle: () => window.destDirHandle,
    };
  }

  // Globale Registrierung
  window.initFolderPicker = initFolderPicker;
})();
