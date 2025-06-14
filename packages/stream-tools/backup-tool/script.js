const safeExtensions = [
  ".mp4",
  ".mov",
  ".mkv",
  ".avi",
  ".webm",
  ".flv",
  ".wmv",
  ".vob",
  ".ts",
  ".mxf",
  ".braw",
  ".wav",
  ".flac",
  ".mp3",
  ".aac",
  ".ogg",
  ".m4a",
  ".aiff",
  ".jpg",
  ".jpeg",
  ".png",
  ".heic",
  ".webp",
  ".tiff",
  ".gif",
  ".doc",
  ".docx",
  ".xls",
  ".xlsx",
  ".ppt",
  ".pptx",
  ".odt",
  ".ods",
  ".pdf",
  ".txt",
  ".rtf",
  ".md",
  ".json",
  ".xml",
  ".zip",
  ".7z",
];

const dangerousExtensions = [
  ".exe",
  ".bat",
  ".cmd",
  ".vbs",
  ".scr",
  ".js",
  ".jar",
  ".com",
  ".msi",
  ".lnk",
  ".pif",
  ".wsf",
  ".sh",
  ".iso",
];

function isAllowedFile(fileName) {
  const lower = fileName.toLowerCase();
  return safeExtensions.some((ext) => lower.endsWith(ext));
}

function isDangerousFile(fileName) {
  const lower = fileName.toLowerCase();
  return dangerousExtensions.some((ext) => lower.endsWith(ext));
}

let dirOriginal = null;
let dirBackup = null;
let dirLog = null;
let logLines = [];
let errorFiles = [];
let checkpointKey = "last_successful_file";
let skipUntil = null;

const logOutput = document.getElementById("logOutput");
const bar = document.getElementById("bar");

function getSettings() {
  return {
    silent: document.getElementById("optSilent").checked,
    showProgress: document.getElementById("optProgress").checked,
    saveLog: document.getElementById("optLogFile").checked,
  };
}

function log(msg, color = "white") {
  logLines.push(msg);
  if (!getSettings().silent) {
    console.log(`%c${msg}`, `color: ${color}`);
    logOutput.textContent += `\n${msg}`;
    logOutput.scrollTop = logOutput.scrollHeight;
  }
}

function saveCheckpoint(filePath) {
  localStorage.setItem(checkpointKey, filePath);
}

function clearCheckpoint() {
  localStorage.removeItem(checkpointKey);
}

async function chooseOriginal() {
  dirOriginal = await window.showDirectoryPicker();
  log("📂 Original-Ordner gewählt.");
}
async function chooseBackup() {
  dirBackup = await window.showDirectoryPicker();
  log("📂 Backup-Ordner gewählt.");
}
async function chooseLogFolder() {
  dirLog = await window.showDirectoryPicker();
  log("🗂 Log-Ordner gewählt.");
}

async function getAllFilesRecursive(dirHandle, path = "") {
  let files = [];
  for await (const entry of dirHandle.values()) {
    const entryPath = path + "/" + entry.name;
    if (entry.kind === "file") {
      files.push({ handle: entry, path: entryPath });
    } else if (entry.kind === "directory") {
      const subDirHandle = await dirHandle.getDirectoryHandle(entry.name);
      const subFiles = await getAllFilesRecursive(subDirHandle, entryPath);
      files = files.concat(subFiles);
    }
  }
  return files;
}

async function ensureSubfolderStructure(baseDir, relativePath) {
  const parts = relativePath.split("/").filter((p) => p);
  parts.pop();
  let currentDir = baseDir;
  for (const part of parts) {
    currentDir = await currentDir.getDirectoryHandle(part, { create: true });
  }
  return currentDir;
}

async function generateMetaFile(file, destFolder, baseName) {
  const metadata = {
    filename: file.name,
    size: file.size,
    lastModified: new Date(file.lastModified).toISOString(),
    type: file.type || "unknown",
  };
  const metaName = baseName.replace(/(\.[^.]+)$/, "_meta.txt");
  const handle = await destFolder.getFileHandle(metaName, { create: true });
  const writable = await handle.createWritable();
  const content = Object.entries(metadata)
    .map(([k, v]) => `${k}: ${v}`)
    .join("\n");
  await writable.write(content);
  await writable.close();
}

async function startBackup() {
  const settings = getSettings();
  if (!dirOriginal || !dirBackup) {
    log("❌ Bitte zuerst beide Ordner auswählen.", "red");
    return;
  }

  const lastFile = localStorage.getItem(checkpointKey);
  if (
    lastFile &&
    confirm(`Letztes Backup wurde bei:
${lastFile}
abgebrochen.

Fortsetzen?`)
  ) {
    skipUntil = lastFile;
  } else {
    clearCheckpoint();
  }

  logLines = [];
  errorFiles = [];
  if (!settings.silent) logOutput.textContent = "🔄 Backup läuft...";
  log(`🔄 Backup gestartet: ${new Date().toLocaleString()}`);

  const fileList = await getAllFilesRecursive(dirOriginal);
  const total = fileList.length;
  let copied = 0;
  let skipping = !!skipUntil;

  for (const item of fileList) {
    const sourceFile = await item.handle.getFile();
    const relativePath = item.path;
    const fileName = relativePath.split("/").pop();
    const destFolder = await ensureSubfolderStructure(dirBackup, relativePath);

    if (isDangerousFile(fileName)) {
      log(`⛔ Ignoriert (potenziell gefährlich): ${fileName}`, "red");
      continue;
    }
    if (!isAllowedFile(fileName)) {
      log(`⏩ Übersprungen (nicht erlaubt): ${fileName}`, "gray");
      continue;
    }

    if (skipping) {
      if (relativePath === skipUntil) {
        skipping = false;
      } else {
        log(`⏩ Übersprungen (bereits gesichert): ${fileName}`, "gray");
        continue;
      }
    }

    try {
      let exists = true;
      let destFile;
      try {
        const existingHandle = await destFolder.getFileHandle(fileName);
        destFile = await existingHandle.getFile();
      } catch (err) {
        exists = false;
      }

      if (
        !exists ||
        (destFile && sourceFile.lastModified > destFile.lastModified)
      ) {
        const destHandle = await destFolder.getFileHandle(fileName, {
          create: true,
        });
        const writable = await destHandle.createWritable();
        await writable.write(await sourceFile.arrayBuffer());
        await writable.close();
        await generateMetaFile(sourceFile, destFolder, fileName);
        copied++;
        saveCheckpoint(relativePath);
        if (!settings.silent) log(`🟢 ${relativePath}`, "green");
      } else {
        if (!settings.silent) log(`🟠 ${relativePath}`, "orange");
      }
    } catch (err) {
      log(`❌ Fehler bei ${fileName}: ${err.message}`, "red");
      errorFiles.push(`${fileName}: ${err.message}`);
      continue;
    }

    if (settings.showProgress) {
      const percent = Math.round((copied / total) * 100);
      bar.style.width = percent + "%";
      bar.textContent = percent + "%";
    }
  }

  log(`✅ Backup abgeschlossen: ${copied}/${total} Dateien kopiert`);

  if (settings.saveLog) {
    await exportLog();
    if (errorFiles.length > 0) {
      const name = `log_errors_${
        new Date().toISOString().replace(/[:T]/g, "-").split(".")[0]
      }.txt`;
      const handle = await dirLog.getFileHandle(name, { create: true });
      const writable = await handle.createWritable();
      await writable.write(errorFiles.join("\n"));
      await writable.close();
      log(`⚠️ Fehlerlog gespeichert als ${name}`, "orange");
    }
  }

  clearCheckpoint();
}

async function exportLog() {
  if (!dirLog) {
    log("❌ Kein Log-Ordner gewählt.", "red");
    return;
  }
  const now = new Date().toISOString().replace(/[:T]/g, "-").split(".")[0];
  const name = `log_${now}.txt`;
  const handle = await dirLog.getFileHandle(name, { create: true });
  const writable = await handle.createWritable();
  await writable.write(logLines.join("\n"));
  await writable.close();
  log(`💾 Log gespeichert als ${name}`, "cyan");
}
