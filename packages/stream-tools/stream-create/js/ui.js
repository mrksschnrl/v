
function setTheme(theme) {
    document.body.className = theme;
    localStorage.setItem('theme', theme);
}
function pingServer() {
    const url = document.getElementById("serverUrl").value;
    fetch(url, { method: "HEAD" })
        .then(res => {
            document.getElementById("serverStatus").innerText = res.ok
                ? "✅ Server reachable."
                : "⚠️ Server responded but not OK (" + res.status + ")";
        })
        .catch(err => {
            document.getElementById("serverStatus").innerText = "❌ Could not connect.";
        });
}
document.getElementById("enableEditor").addEventListener("change", function () {
    document.getElementById("editorPanel").style.display = this.checked ? "block" : "none";
});
document.getElementById("outputFolder").addEventListener("blur", function () {
    const folder = this.value;
    fetch("folder_check.php?check=" + encodeURIComponent(folder))
        .then(res => res.text())
        .then(msg => {
            const warning = document.getElementById("folderWarning");
            if (msg.includes("EXISTS")) {
                warning.innerText = "⚠️ Warning: This folder already exists. Files may be overwritten.";
                warning.style.display = "block";
            } else {
                warning.innerText = "";
                warning.style.display = "none";
            }
        });
});

function startConversion() {
    fetch("convert_to_m3u8.php")
        .then(res => res.text())
        .then(txt => {
            document.getElementById("conversionStatus").innerText = "✅ Conversion triggered.";
        })
        .catch(() => {
            document.getElementById("conversionStatus").innerText = "❌ Failed to trigger conversion.";
        });
}
function toggleListener() {
    alert("To start the folder listener, double-click tools/folder_listener.bat.\nTo stop it, delete folder_listener_running.txt");
}

function refreshLog() {
    fetch("logs/stream-creator.log?" + Date.now())
        .then(res => res.text())
        .then(log => {
            document.getElementById("logOutput").innerText = log || "No logs yet.";
        })
        .catch(() => {
            document.getElementById("logOutput").innerText = "⚠️ Could not load logs.";
        });
}

window.onload = () => {
    if (localStorage.getItem('theme')) {
        setTheme(localStorage.getItem('theme'));
    }
    refreshLog();
};

function checkListenerStatus() {
    fetch("listener_status.php?" + Date.now())
        .then(res => res.text())
        .then(status => {
            document.getElementById("listenerStatus").innerText = status;
        })
        .catch(() => {
            document.getElementById("listenerStatus").innerText = "⚠️ Could not check listener status.";
        });
}
window.onload = () => {
    if (localStorage.getItem('theme')) {
        setTheme(localStorage.getItem('theme'));
    }
    refreshLog();
    checkListenerStatus();
};
