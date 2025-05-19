// public/js/section_detail.js
document.addEventListener('DOMContentLoaded', () => {
  const searchInput = document.getElementById('searchLastname');
  const table       = document.getElementById('studentsTable');
  const selectAll   = document.getElementById('selectAll');
  const deleteBtn   = document.getElementById('deleteBtn');
  const editModal   = document.getElementById('editModal');
  const closeModal  = document.getElementById('closeModal');
  const editForm    = document.getElementById('editForm');

  // Helper to get all row checkboxes
  const checkboxes = () =>
    Array.from(document.querySelectorAll('.student-checkbox'));

  // 1) FORCE UPPERCASE on the search box
  searchInput.style.textTransform = 'uppercase';
  searchInput.addEventListener('input', () => {
    // always keep it uppercase
    searchInput.value = searchInput.value.toUpperCase();

    // then filter rows by last-name
    const filter = searchInput.value.trim();
    table.querySelectorAll('tbody tr').forEach(tr => {
      const last = tr.cells[1].innerText.trim().toUpperCase();
      tr.style.display = last.includes(filter) ? '' : 'none';
    });
  });

  // 2) SELECT ALL handler
  selectAll.addEventListener('change', () => {
    checkboxes().forEach(cb => cb.checked = selectAll.checked);
  });

  // 3) DELETE SELECTED handler (unchanged)
  deleteBtn.addEventListener('click', () => {
    const toDel = checkboxes().filter(cb => cb.checked).map(cb => cb.value);
    if (!toDel.length) return alert('Select at least one student.');
    if (!confirm(`Delete ${toDel.length} student(s)?`)) return;

    fetch('/sections/delete', {
      method: 'POST',
      headers: { 'Content-Type':'application/json' },
      body: JSON.stringify({ student_ids: toDel })
    })
    .then(r => r.json())
    .then(d => {
      if (!d.success) throw new Error(d.error||'Delete failed');
      // remove rows
      toDel.forEach(id => {
        const row = table.querySelector(`.student-checkbox[value="${id}"]`)
                         .closest('tr');
        if (row) row.remove();
      });
      selectAll.checked = false;
    })
    .catch(err => alert(err.message || 'Network error'));
  });

  // 4) EDIT MODAL (unchanged logic from before)
  function openEditModal(row, id) {
    document.getElementById('editStudentId').value = id;
    document.getElementById('editLastName').value   = row.cells[1].innerText.trim();
    document.getElementById('editGivenName').value  = row.cells[2].innerText.trim();
    document.getElementById('editMiddleName').value = row.cells[3].innerText.trim();
    document.getElementById('editBirthSex').value   = row.cells[4].innerText.trim();
    editModal.style.display = 'flex';
  }

  table.querySelectorAll('.editBtn').forEach(btn => {
    btn.addEventListener('click', () => {
      openEditModal(btn.closest('tr'), btn.dataset.id);
    });
  });

  closeModal.addEventListener('click', () => editModal.style.display = 'none');
  window.addEventListener('click', e => {
    if (e.target === editModal) editModal.style.display = 'none';
  });

  editForm.addEventListener('submit', e => {
    e.preventDefault();
    const data = Object.fromEntries(new FormData(editForm).entries());
    // uppercase enforcement
    data.last_name   = data.last_name.toUpperCase();
    data.given_name  = data.given_name.toUpperCase();
    data.middle_name = data.middle_name.toUpperCase();

    fetch('/students/update', {
      method: 'POST',
      headers: { 'Content-Type':'application/json' },
      body: JSON.stringify(data)
    })
    .then(r => r.json())
    .then(d => {
      if (!d.success) throw new Error(d.error||'Update failed');
      const row = table.querySelector(
        `.student-checkbox[value="${data.student_id}"]`
      ).closest('tr');
      row.cells[1].innerText = data.last_name;
      row.cells[2].innerText = data.given_name;
      row.cells[3].innerText = data.middle_name;
      row.cells[4].innerText = data.birth_sex;
      editModal.style.display = 'none';
    })
    .catch(err => alert(err.message || 'Network error'));
  });
});
