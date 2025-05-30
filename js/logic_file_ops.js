// File: /v/packages/file-sort/js/logic_file_ops.js
/**
 * Kopier-Utilities – kein Löschen im Quellordner.
 * - Schließt den WritableStream nur einmal im finally-Block.
 * - Sanitisert Dateinamen und hängt bei Konflikten "-N" an.
 * - Verwendet pipeTo mit preventClose, um doppeltes Schließen zu vermeiden.
 */

const INVALID_FILENAME_CHARS = /[\/\\:*?"<>|]/g;

/**
 * Ermittelt einen eindeutigen Dateinamen im Zielordner,
 * indem bei Konflikt "-2", "-3", … angehängt wird.
 */
async function getUniqueName(dirHandle, name) {
  const dot = name.lastIndexOf(".");
  const base = dot !== -1 ? name.slice(0, dot) : name;
  const ext = dot !== -1 ? name.slice(dot) : "";

  let candidate = name;
  let counter = 2;
  while (true) {
    try {
      // existiert? (create: false)
      await dirHandle.getFileHandle(candidate);
      // ja → nächsten Versuch bauen
      candidate = `${base}-${counter}${ext}`;
      counter++;
    } catch {
      // nein → frei, zurückgeben
      return candidate;
    }
  }
}

export async function copyAndVerifyFile(srcHandle, destDirHandle, name) {
  // 1) Name säubern
  const safeName = name.replace(INVALID_FILENAME_CHARS, "_");

  // 2) Eindeutigen Namen im Ziel finden
  const uniqueName = await getUniqueName(destDirHandle, safeName);

  // 3) Zieldatei anlegen/öffnen
  const destHandle = await destDirHandle.getFileHandle(uniqueName, {
    create: true,
  });
  const writable = await destHandle.createWritable();

  try {
    // 4) Datei kopieren, verhindern dass pipeTo den Stream schließt
    const file = await srcHandle.getFile();
    await file.stream().pipeTo(writable, { preventClose: true });
    // 5) (Optional) Verifikation…
  } finally {
    // 6) Stream nur einmal hier schließen
    await writable.close();
  }

  return uniqueName;
}

export async function moveIfVerified(
  srcHandle,
  srcDirHandle,
  destDirHandle,
  name
) {
  // Copy (mit Unique-Logik) und Namen zurückbekommen
  const finalName = await copyAndVerifyFile(srcHandle, destDirHandle, name);
  console.log(`(Move-Modus) Quelldatei bleibt erhalten: ${finalName}`);
  return finalName;
}
