// public/js/section_detail.js

document.addEventListener('DOMContentLoaded', () => {
  const lastInput = document.getElementById('searchLastName');
  const table     = document.getElementById('studentTable');
  if (!lastInput || !table) return;

  lastInput.addEventListener('input', () => {
    const filter = lastInput.value.trim().toLowerCase();
    Array.from(table.tBodies[0].rows).forEach(row => {
      const lastName = row.cells[0].textContent.toLowerCase();
      row.style.display = lastName.includes(filter) ? '' : 'none';
    });
  });
});
