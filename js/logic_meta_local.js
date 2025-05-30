// File: js/logic_meta_local.js
/**
 * Generiert meta.txt für alle Dateien (rekursiv), deren lastModified
 * im Zeitfenster [startDate, endDate] liegt.
 * @returns {number} Anzahl der eingetragenen Dateien
 */
export async function generateMetaInRange(
  srcDirHandle,
  destDirHandle,
  startDate,
  endDate
) {
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

  const entries = [];
  const startMs = startDate.getTime();
  const endMs = endDate.getTime();

  for await (const [relPath, handle] of walkFiles(srcDirHandle)) {
    const file = await handle.getFile();
    const mtime = file.lastModified;
    if (mtime >= startMs && mtime <= endMs) {
      entries.push({
        name: relPath,
        size: file.size,
        lastModified: new Date(mtime).toLocaleString(),
      });
    }
  }

  const metaHandle = await destDirHandle.getFileHandle("meta.txt", {
    create: true,
  });
  const writable = await metaHandle.createWritable();

  const header = "Name\tSize\tLastModified";
  const lines = entries.map((e) => `${e.name}\t${e.size}\t${e.lastModified}`);
  await writable.write([header, ...lines].join("\n"));
  await writable.close();

  return entries.length;
}
