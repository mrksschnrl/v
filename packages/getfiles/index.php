<?php
// ⚠️ Make sure there is NOTHING above this line — no blank lines, no spaces!
session_start();

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
  header("Location: login.php");
  exit;
}

$pageTitle = 'GETFILES';

include '/var/www/html/v/includes/header.php';
include '/var/www/html/v/includes/footer.php';
?>






<div class="file-list-wrapper">

  <div class="sort-links">
    <a href="#" onclick="sortList('asc'); return false;">Sort A–Z</a>
    <a href="#" onclick="sortList('desc'); return false;">Sort Z–A</a>
  </div>

  <div id="file-list" class="file-list"></div>

</div>

<script src="js/sort.js"></script>
<script>
  fetch('files.php')
    .then(response => response.json())
    .then(files => {
      const container = document.getElementById('file-list');
      if (!files.length) {
        container.innerHTML = '<p>No files found.</p>';
        return;
      }
      const list = document.createElement('ul');
      list.classList.add('file-list-items');
      files.forEach(file => {
        const li = document.createElement('li');
        li.classList.add('file-list-item');
        li.innerHTML = `📄 <a href="files/${file}" download>${file}</a>`;
        list.appendChild(li);
      });
      container.appendChild(list);
    });
</script>

</body>

</html>