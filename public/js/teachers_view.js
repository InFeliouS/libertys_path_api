// js/teachers_view.js
(() => {
  const api = window.TEACHERS_API || 'teachers_view.php';
  const tbody = document.getElementById('teachersTbody');
  const reloadBtn = document.getElementById('tvReloadBtn');

  const modal = document.getElementById('teacherEditModal');
  const form = document.getElementById('teacherEditForm');
  const closeBtn = document.getElementById('teacherEditClose');
  const cancelBtn = document.getElementById('teacherEditCancel');

  function showModal() { modal.style.display = 'block'; }
  function hideModal() {
    modal.style.display = 'none';
    form.reset();
  }

  async function loadTeachers() {
    tbody.innerHTML = '<tr><td colspan="6" style="padding:18px;text-align:center;">Loading…</td></tr>';
    try {
      const res = await fetch(`${api}?action=list`, { credentials: 'same-origin' });
      const j = await res.json();
      if (!j.ok) {
        tbody.innerHTML = `<tr><td colspan="6" style="padding:18px;text-align:center;">Error: ${j.error || 'failed'}</td></tr>`;
        return;
      }
      renderRows(j.data || []);
    } catch (err) {
      tbody.innerHTML = `<tr><td colspan="6" style="padding:18px;text-align:center;">Network error</td></tr>`;
      console.error(err);
    }
  }

  function escapeHtml(s) {
    if (s === null || s === undefined) return '';
    return String(s).replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
  }

  function renderRows(rows) {
    if (!rows.length) {
      tbody.innerHTML = '<tr><td colspan="6" style="padding:18px;text-align:center;">No teachers found.</td></tr>';
      return;
    }
    tbody.innerHTML = rows.map((r, i) => {
      return `<tr data-id="${r.id}">
        <td class="mono" style="padding:12px 10px;">${i+1}</td>
        <td style="padding:12px 10px;" class="td-username">${escapeHtml(r.username)}</td>
        <td style="padding:12px 10px;">${escapeHtml(r.first_name)}</td>
        <td style="padding:12px 10px;">${escapeHtml(r.last_name)}</td>
        <td style="padding:12px 10px;">${escapeHtml(r.role)}</td>
        <td style="padding:12px 10px;">
          <button class="btn btn-edit" data-id="${r.id}" data-json='${escapeHtml(JSON.stringify(r))}'>Edit</button>
          <button class="btn btn-delete" data-id="${r.id}">Delete</button>
        </td>
      </tr>`;
    }).join('');
    // attach handlers
    document.querySelectorAll('.btn-edit').forEach(b => b.addEventListener('click', onEditClick));
    document.querySelectorAll('.btn-delete').forEach(b => b.addEventListener('click', onDeleteClick));
  }

  function onEditClick(e) {
    const b = e.currentTarget;
    let data = {};
    try { data = JSON.parse(b.getAttribute('data-json')); } catch (err) { console.error(err); }
    document.getElementById('edit_id').value = data.id || '';
    document.getElementById('edit_username').value = data.username || '';
    document.getElementById('edit_first_name').value = data.first_name || '';
    document.getElementById('edit_last_name').value = data.last_name || '';
    document.getElementById('edit_password').value = '';
    showModal();
  }

  async function onDeleteClick(e) {
    const id = e.currentTarget.getAttribute('data-id');
    if (!confirm('Delete this teacher? This cannot be undone.')) return;
    try {
      const fd = new FormData();
      fd.append('action', 'delete');
      fd.append('id', id);
      const res = await fetch(api, { method: 'POST', body: fd, credentials: 'same-origin' });
      const j = await res.json();
      if (j.ok) {
        loadTeachers();
      } else {
        alert('Delete failed: ' + (j.error || 'unknown'));
      }
    } catch (err) {
      console.error(err);
      alert('Network error');
    }
  }

  form.addEventListener('submit', async (ev)=> {
    ev.preventDefault();
    const fd = new FormData(form);
    fd.append('action', 'update');
    try {
      const res = await fetch(api, { method: 'POST', body: fd, credentials: 'same-origin' });
      const j = await res.json();
      if (j.ok) {
        hideModal();
        loadTeachers();
      } else {
        alert('Save failed: ' + (j.error || 'unknown'));
      }
    } catch (err) {
      console.error(err);
      alert('Network error');
    }
  });

  closeBtn.addEventListener('click', hideModal);
  cancelBtn.addEventListener('click', hideModal);
  reloadBtn.addEventListener('click', loadTeachers);

  // initial load
  loadTeachers();

  // click outside modal to close (optional UX)
  window.addEventListener('click', (ev) => {
    if (ev.target === modal) hideModal();
  });
})();
