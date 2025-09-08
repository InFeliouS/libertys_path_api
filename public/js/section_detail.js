// public/js/section_detail.js
document.addEventListener('DOMContentLoaded', () => {
  const searchInput = document.getElementById('searchLastname');
  const tbody       = document.querySelector('#studentsTable tbody');
  const deleteBtn   = document.getElementById('deleteBtn');
  const downloadBtn = document.getElementById('downloadCsvBtn');
  const selectAllCb = document.getElementById('selectAll');

  // 1) Filter by last name
  searchInput.addEventListener('input', () => {
    const filter = searchInput.value.toUpperCase();
    tbody.querySelectorAll('tr').forEach(tr => {
      const last = tr.cells[1].textContent.toUpperCase();
      tr.style.display = last.includes(filter) ? '' : 'none';
    });
  });

  // 2) Select All
  selectAllCb.addEventListener('change', () => {
    tbody.querySelectorAll('.student-checkbox')
      .forEach(cb => cb.checked = selectAllCb.checked);
  });

  // 3) Delete Selected
  deleteBtn.addEventListener('click', () => {
    const ids = Array.from(tbody.querySelectorAll('.student-checkbox:checked'))
      .map(cb => cb.value);
    if (!ids.length) return alert('No students selected.');
    if (!confirm('Delete selected students?')) return;

    fetch('api/v1/delete_students.php', {
      method: 'POST',
      headers: {'Content-Type':'application/json'},
      body: JSON.stringify({student_ids: ids})
    })
    .then(res => res.json())
    .then(json => {
      if (json.success) location.reload();
      else alert('Delete failed: ' + (json.error||'Unknown error'));
    });
  });

  // 4) Download CSV
  downloadBtn.addEventListener('click', () => {
    const rows = Array.from(document.querySelectorAll('#studentsTable tr'));
    const csv  = rows.map(row => {
      return Array.from(row.querySelectorAll('th,td'))
        .map(cell => `"${cell.textContent.replace(/"/g,'""')}"`)
        .join(',');
    }).join('\r\n');

    const blob = new Blob([csv], {type:'text/csv'});
    const url  = URL.createObjectURL(blob);
    const a    = document.createElement('a');
    a.href     = url;
    a.download = 'section_<?= $section_id ?>_students.csv';
    a.click();
    URL.revokeObjectURL(url);
  });
});
