
let parsedEntries = [];

function handleFileList(fileList) {
  parsedEntries = [];
  const resultsList = document.getElementById('results-list');
  resultsList.innerHTML = '';

  Array.from(fileList).forEach(file => {
    if (file.type === 'text/plain') {
      const reader = new FileReader();
      reader.onload = function (e) {
        const content = e.target.result;
        const entries = parseTxtSearch2(content);
        parsedEntries.push(...entries);
        displayResults(parsedEntries);
      };
      reader.readAsText(file);
    }
  });
}

document.getElementById('file-input-txt').addEventListener('change', function (event) {
  handleFileList(event.target.files);
});

document.getElementById('file-input-dir').addEventListener('change', function (event) {
  handleFileList(event.target.files);
});

document.getElementById('search-button').addEventListener('click', function () {
  const query = document.getElementById('search-input').value.toLowerCase();
  const filtered = parsedEntries.filter(entry =>
    entry.header.toLowerCase().includes(query) ||
    entry.fullText.toLowerCase().includes(query)
  );
  displayResults(filtered);
});

function displayResults(entries) {
  const resultsList = document.getElementById('results-list');
  resultsList.innerHTML = '';
  entries.forEach(entry => {
    const li = document.createElement('li');
    li.textContent = entry.header + ': ' + entry.preview;
    li.title = entry.fullText;
    resultsList.appendChild(li);
  });
}

document.addEventListener('click', function (event) {
  if (event.target.matches('#results-list li')) {
    const fullText = event.target.title;
    const header = event.target.textContent.split(':')[0];
    document.getElementById('modal-text').textContent = header + "\n\n" + fullText;
    document.getElementById('modal-overlay').style.display = 'block';
  }

  if (event.target.matches('#modal-close') || event.target.matches('#modal-overlay')) {
    document.getElementById('modal-overlay').style.display = 'none';
  }
});

document.addEventListener('keydown', function (event) {
  if (event.key === 'Escape') {
    document.getElementById('modal-overlay').style.display = 'none';
  }
});

function parseTxtSearch2(text) {
  const blocks = [];
  const regex = /(\d{8}-mrks)([\s\S]*?)(?=\/\/cut)/g;
  let match;

  while ((match = regex.exec(text)) !== null) {
    const header = match[1].trim();
    const body = match[2].trim();
    const preview = body.split('\n')[0].slice(0, 100); // first line/preview
    blocks.push({ header, preview, fullText: body });
  }

  return blocks;
}
