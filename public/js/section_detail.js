// public/js/section_detail.js
document.addEventListener('DOMContentLoaded', () => {
  const searchInput = document.getElementById('searchLastname');
  const table       = document.getElementById('studentsTable');
  const selectAll   = document.getElementById('selectAll');
  const deleteBtn   = document.getElementById('deleteBtn');
  const editModal   = document.getElementById('editModal');
  const closeModal  = document.getElementById('closeModal');
  const editForm    = document.getElementById('editForm');
  const downloadBtn = document.getElementById('downloadCsvBtn');

  // ── CSV Download Button ──
  if (downloadBtn) {
    const params = new URLSearchParams(window.location.search);
    const secId  = params.get('section_id');
    if (secId) {
      downloadBtn.addEventListener('click', () => {
        // open download in new tab
        const url = `/download_section_students_csv.php?section_id=${encodeURIComponent(secId)}`;
        window.open(url, '_blank');
      });
    }
  }

  // ── FORCE UPPERCASE on the search box ──
  if (searchInput) {
    searchInput.style.textTransform = 'uppercase';
    searchInput.addEventListener('input', () => {
      searchInput.value = searchInput.value.toUpperCase();
      const filter = searchInput.value.trim();
      table.querySelectorAll('tbody tr').forEach(tr => {
        const last = tr.cells[1].innerText.trim().toUpperCase();
        tr.style.display = last.includes(filter) ? '' : 'none';
      });
    });
  }

  // ── SELECT ALL handler ──
  if (selectAll) {
    selectAll.addEventListener('change', () => {
      document.querySelectorAll('.student-checkbox')
        .forEach(cb => cb.checked = selectAll.checked);
    });
  }

  // ── DELETE SELECTED handler ──
  if (deleteBtn) {
    deleteBtn.addEventListener('click', () => {
      const toDelete = Array.from(document.querySelectorAll('.student-checkbox'))
        .filter(cb => cb.checked)
        .map(cb => cb.value);
      if (!toDelete.length) return alert('No students selected.');
      if (!confirm('Are you sure you want to delete the selected students?')) return;

      fetch('/delete_students.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ student_ids: toDelete })
      })
      .then(r => r.json())
      .then(d => {
        if (!d.success) throw new Error(d.error || 'Delete failed');
        toDelete.forEach(id => {
          const row = document.querySelector(`.student-checkbox[value="${id}"]`).closest('tr');
          if (row) row.remove();
        });
        selectAll.checked = false;
      })
      .catch(err => alert(err.message || 'Network error'));
    });
  }

  // ── OPEN EDIT MODAL ──
  table.addEventListener('click', e => {
    if (!e.target.classList.contains('editBtn')) return;
    const id  = e.target.dataset.id;
    const row = e.target.closest('tr');
    document.getElementById('editStudentId').value = id;
    document.getElementById('editLastName').value   = row.cells[1].innerText.trim();
    document.getElementById('editGivenName').value  = row.cells[2].innerText.trim();
    document.getElementById('editMiddleName').value = row.cells[3].innerText.trim();
    document.getElementById('editBirthSex').value   = row.cells[4].innerText.trim();
    editModal.style.display = 'block';
  });

  // ── CLOSE EDIT MODAL ──
  closeModal.addEventListener('click', () => editModal.style.display = 'none');
  window.addEventListener('click', e => {
    if (e.target === editModal) editModal.style.display = 'none';
  });

  // ── SUBMIT EDIT FORM ──
  editForm.addEventListener('submit', e => {
    e.preventDefault();
    const data = Object.fromEntries(new FormData(editForm).entries());
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
      if (!d.success) throw new Error(d.error || 'Update failed');
      const cells = document.querySelector(`.student-checkbox[value="${data.student_id}"]`)
                          .closest('tr').cells;
      cells[1].innerText = data.last_name;
      cells[2].innerText = data.given_name;
      cells[3].innerText = data.middle_name;
      cells[4].innerText = data.birth_sex;
      editModal.style.display = 'none';
    })
    .catch(err => alert(err.message || 'Network error'));
  });
});
