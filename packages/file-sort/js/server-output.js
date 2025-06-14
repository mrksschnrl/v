document.addEventListener("DOMContentLoaded", () => {
  const outputBtn = document.getElementById("set-server-output");
  const revertBtn = document.getElementById("revert-local-output");
  const destInput = document.getElementById("destination-manual");
  const destPath = document.getElementById("destination-path");
  const log = document.getElementById("log");

  const logMessage = (msg) => {
    if (log) {
      log.textContent += "\n" + msg;
      log.scrollTop = log.scrollHeight;
    }
  };

  outputBtn?.addEventListener("click", () => {
    fetch("/v/api/get_username.php")
      .then((res) => res.json())
      .then((data) => {
        if (data.username) {
          const user = data.username;
          const serverPath = `/v/users/accounts/${user}/uploads`;
          destInput.value = serverPath;
          if (destPath) destPath.textContent = "📁 Zielordner: " + serverPath;
          logMessage("✅ Server-Zielordner gesetzt auf: " + serverPath);
        } else {
          logMessage("❌ Kein Benutzer erkannt.");
        }
      })
      .catch((err) => {
        logMessage("❌ Fehler beim Abrufen des Benutzers: " + err.message);
      });
  });

  revertBtn?.addEventListener("click", () => {
    destInput.value = "";
    if (destPath) destPath.textContent = "📁 Zielordner: –";
    logMessage("↩️ Zielordner zurückgesetzt auf lokalen Pfad.");
  });
});
