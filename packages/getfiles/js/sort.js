
function sortList(order) {
  const ul = document.querySelector('#file-list ul');
  if (!ul) return;
  const items = Array.from(ul.querySelectorAll('li'));
  items.sort((a, b) => {
    const aText = a.textContent.trim().toLowerCase();
    const bText = b.textContent.trim().toLowerCase();
    return order === 'asc' ? aText.localeCompare(bText) : bText.localeCompare(aText);
  });
  ul.innerHTML = '';
  items.forEach(li => ul.appendChild(li));
}
