// js/teachers_view.js — full drop-in (load rows, update, delete)
(() => {
  const $ = (sel, ctx = document) => ctx.querySelector(sel);
  const $$ = (sel, ctx = document) => Array.from(ctx.querySelectorAll(sel));
  const on = (el, ev, fn) => { if (!el) return; el.addEventListener(ev, fn); };

  // modal/query refs (set after DOM ready)
  let editModal, editCloseBtn, editCancelBtn, editForm, editInputs, editError;
  let deleteModal, deleteCloseBtn, deleteCancelBtn, deleteConfirmBtn, deleteIdInput, deleteTeacherName, deleteError;
  let tvStatus;

  function show(el) {
    if (!el) return;
    el.style.display = 'block';
    el.setAttribute('aria-hidden', 'false');
    el.style.zIndex = 9999;
  }
  function hide(el) {
    if (!el) return;
    el.style.display = 'none';
    el.setAttribute('aria-hidden', 'true');
  }

  function setStatus(msg, isError = false, timeout = 3500) {
    tvStatus = tvStatus || $('#tvStatus');
    if (!tvStatus) {
      if (isError) console.error(msg); else console.info(msg);
      return;
    }
    tvStatus.textContent = msg;
    tvStatus.style.color = isError ? '#a00' : '#166534';
    if (timeout > 0) setTimeout(() => { if (tvStatus) tvStatus.textContent = ''; }, timeout);
  }

  // parse a teacher from a row (prefers data-json on edit button)
  function parseTeacherFromRow(row) {
    if (!row) return null;
    const editBtn = row.querySelector('.btn-edit');
    const jsonAttr = editBtn ? editBtn.getAttribute('data-json') : null;
    if (jsonAttr) {
      try {
        const obj = JSON.parse(jsonAttr);
        return {
          id: obj.id || row.dataset.id || '',
          username: obj.username || '',
          first_name: obj.first_name || '',
          last_name: obj.last_name || ''
        };
      } catch (e) { /* ignore */ }
    }
    const cells = row.querySelectorAll('td');
    return {
      id: row.dataset.id || (cells[0] && cells[0].textContent.trim()) || '',
      first_name: (cells[1] && cells[1].textContent.trim()) || '',
      last_name: (cells[2] && cells[2].textContent.trim()) || '',
      username: (cells[3] && cells[3].textContent.trim()) || ''
    };
  }

  function fillEditFormWithRow(row) {
    const t = parseTeacherFromRow(row);
    if (!t) return;
    editInputs.id.value = t.id || '';
    editInputs.username.value = t.username || '';
    editInputs.first_name.value = t.first_name || '';
    editInputs.last_name.value = t.last_name || '';
    editInputs.password.value = '';
    clearError(editError);
    show(editModal);
    setTimeout(() => { try { editInputs.username.focus(); } catch(e) {} }, 40);
  }

  function showEditError(msg) {
    if (!editError) {
      editError = document.createElement('div');
      editError.style.color = '#a00';
      editError.style.marginTop = '8px';
      editError.style.fontSize = '0.95rem';
      editForm.prepend(editError);
    }
    editError.textContent = msg;
  }

  function clearError(el) {
    if (!el) return;
    el.textContent = '';
  }

  function fillDeleteModalWithRow(row) {
    const t = parseTeacherFromRow(row);
    if (!t) return;
    deleteIdInput.value = t.id || '';
    deleteTeacherName.textContent = (t.username && `@${t.username}`) || (`ID ${t.id}`) || 'this teacher';
    clearError(deleteError);
    show(deleteModal);
    setTimeout(() => { try { deleteConfirmBtn.focus(); } catch(e) {} }, 40);
  }

  function showDeleteError(msg) {
    if (!deleteError) {
      deleteError = document.createElement('div');
      deleteError.style.color = '#a00';
      deleteError.style.marginTop = '8px';
      deleteError.style.fontSize = '0.95rem';
      deleteConfirmBtn.parentNode.insertBefore(deleteError, deleteConfirmBtn.nextSibling);
    }
    deleteError.textContent = msg;
  }

  function wireButtons() {
    $$('.btn-edit').forEach(btn => {
      on(btn, 'click', (ev) => {
        ev.preventDefault();
        const row = btn.closest('tr');
        fillEditFormWithRow(row);
      });
    });

    $$('.btn-delete').forEach(btn => {
      on(btn, 'click', (ev) => {
        ev.preventDefault();
        const row = btn.closest('tr');
        fillDeleteModalWithRow(row);
      });
    });
  }

  // Fetch server-rendered rows and inject
  function loadTableRows() {
    const url = 'index.php?r=teachers/view_table';
    const tbody = document.querySelector('#teachersTbody') || document.querySelector('table.tv-table tbody');
    if (!tbody) {
      console.warn('teachers_view: tbody not found.');
      return Promise.resolve();
    }

    tbody.innerHTML = '<tr><td colspan="5" style="padding:12px;text-align:center;color:#666;">Loading…</td></tr>';

    return fetch(url, { credentials: 'same-origin' })
      .then(resp => {
        if (!resp.ok) throw new Error('Network response not ok: ' + resp.status);
        return resp.text();
      })
      .then(html => {
        tbody.innerHTML = html;
      })
      .catch(err => {
        console.error('Failed to load teacher rows:', err);
        tbody.innerHTML = '<tr><td colspan="5" style="padding:18px;text-align:center;color:#a00;">Failed to load data.</td></tr>';
        setStatus('Failed to load teacher rows', true);
      });
  }

  // Helper: update DOM row with new data (id, first_name, last_name, username)
  function updateDomRow(id, newData) {
    const row = document.querySelector(`tr[data-id="${id}"]`);
    if (!row) return;
    const cells = row.querySelectorAll('td');
    // expected order: 0:id,1:first,2:last,3:username,4:actions
    if (cells[1]) cells[1].textContent = newData.first_name || '';
    if (cells[2]) cells[2].textContent = newData.last_name || '';
    if (cells[3]) cells[3].textContent = newData.username || '';
    // update data-json on edit button (if exists)
    const editBtn = row.querySelector('.btn-edit');
    if (editBtn) {
      const obj = {
        id: parseInt(id, 10),
        username: newData.username || '',
        first_name: newData.first_name || '',
        last_name: newData.last_name || ''
      };
      try {
        editBtn.setAttribute('data-json', JSON.stringify(obj));
      } catch (e) { /* ignore */ }
    }
  }

// Submit update via fetch (robust JSON parsing & fallback to text)
function submitUpdate(formEl) {
  const formData = new FormData(formEl);
  formData.append('action', 'update');

  const submitBtn = formEl.querySelector('button[type="submit"]');
  if (submitBtn) submitBtn.disabled = true;

  return fetch('index.php?r=teachers/view', {
    method: 'POST',
    credentials: 'same-origin',
    body: formData,
    headers: { 'Accept': 'application/json, text/plain, */*' }
  })
  .then(resp => resp.text().then(text => ({ ok: resp.ok, status: resp.status, text })))
  .then(result => {
    if (submitBtn) submitBtn.disabled = false;

    // try parse JSON first
    let json = null;
    try { json = JSON.parse(result.text); } catch (e) { /* not-json */ }

    if (!result.ok) {
      // server returned non-2xx — prefer JSON message, else raw text
      const errMsg = (json && json.error) ? json.error : (result.text || ('HTTP ' + result.status));
      throw new Error(errMsg);
    }

    if (!json || !json.ok) {
      const errMsg = (json && json.error) ? json.error : ('Unexpected server response: ' + result.text);
      throw new Error(errMsg);
    }

    // success — update DOM row
    setStatus('Teacher updated', false);
    updateDomRow(formData.get('id'), {
      first_name: formData.get('first_name'),
      last_name: formData.get('last_name'),
      username: formData.get('username')
    });
    hide(editModal);
  })
  .catch(err => {
    if (submitBtn) submitBtn.disabled = false;
    const msg = (err && err.message) ? err.message : 'Server error';
    showEditError(msg);
    setStatus('Update failed', true);
    console.error('Update error:', err);
  });
}


  // Submit delete via fetch (robust JSON/text handling)
function submitDelete(id) {
  const data = new FormData();
  data.append('action', 'delete');
  data.append('id', id);

  if (deleteConfirmBtn) deleteConfirmBtn.disabled = true;

  return fetch('index.php?r=teachers/view', {
    method: 'POST',
    credentials: 'same-origin',
    body: data,
    headers: { 'Accept': 'application/json, text/plain, */*' }
  })
  .then(resp => resp.text().then(text => ({ ok: resp.ok, status: resp.status, text })))
  .then(result => {
    if (deleteConfirmBtn) deleteConfirmBtn.disabled = false;

    let json = null;
    try { json = JSON.parse(result.text); } catch (e) { /* not-json */ }

    if (!result.ok) {
      const errMsg = (json && json.error) ? json.error : (result.text || ('HTTP ' + result.status));
      throw new Error(errMsg);
    }

    if (!json || !json.ok) {
      const errMsg = (json && json.error) ? json.error : ('Unexpected server response: ' + result.text);
      throw new Error(errMsg);
    }

    // success
    const row = document.querySelector(`tr[data-id="${id}"]`);
    if (row) row.remove();
    setStatus('Teacher deleted', false);
    hide(deleteModal);
  })
  .catch(err => {
    if (deleteConfirmBtn) deleteConfirmBtn.disabled = false;
    const msg = (err && err.message) ? err.message : 'Server error';
    showDeleteError(msg);
    setStatus('Delete failed', true);
    console.error('Delete error:', err);
  });
}


  // Setup DOM ready
  document.addEventListener('DOMContentLoaded', () => {
    // query modal elements
    editModal = $('#teacherEditModal');
    editCloseBtn = $('#teacherEditClose');
    editCancelBtn = $('#teacherEditCancel');
    editForm = $('#teacherEditForm');
    editInputs = {
      id: $('#edit_id'),
      username: $('#edit_username'),
      first_name: $('#edit_first_name'),
      last_name: $('#edit_last_name'),
      password: $('#edit_password')
    };

    deleteModal = $('#teacherDeleteModal');
    deleteCloseBtn = $('#teacherDeleteClose');
    deleteCancelBtn = $('#teacherDeleteCancel');
    deleteConfirmBtn = $('#teacherDeleteConfirm');
    deleteIdInput = $('#delete_id');
    deleteTeacherName = $('#delete_teacher_name');

    tvStatus = $('#tvStatus');

    // ensure modals hidden
    if (editModal) hide(editModal);
    if (deleteModal) hide(deleteModal);

    // load rows then wire up handlers
    loadTableRows().then(() => {
      wireButtons();

      // modal close/cancel wiring
      if (editCloseBtn) on(editCloseBtn, 'click', () => { clearError(editError); hide(editModal); });
      if (editCancelBtn) on(editCancelBtn, 'click', () => { clearError(editError); hide(editModal); });

      if (deleteCloseBtn) on(deleteCloseBtn, 'click', () => { clearError(deleteError); hide(deleteModal); });
      if (deleteCancelBtn) on(deleteCancelBtn, 'click', () => { clearError(deleteError); hide(deleteModal); });

      // handle edit form submit => call API and update row
      if (editForm) {
        on(editForm, 'submit', (ev) => {
          ev.preventDefault();
          clearError(editError);
          // basic client validation
          const username = (editInputs.username.value || '').trim();
          if (!username) {
            showEditError('Username is required');
            return;
          }
          submitUpdate(editForm);
        });
      }

      // handle delete confirm
      if (deleteConfirmBtn) {
        on(deleteConfirmBtn, 'click', (ev) => {
          ev.preventDefault();
          clearError(deleteError);
          const id = deleteIdInput.value;
          if (!id) {
            showDeleteError('Invalid id');
            return;
          }
          submitDelete(id);
        });
      }
    });

    // escape key closes modals
    document.addEventListener('keydown', (ev) => {
      if (ev.key === 'Escape') {
        if (editModal && editModal.style.display !== 'none') { clearError(editError); hide(editModal); }
        if (deleteModal && deleteModal.style.display !== 'none') { clearError(deleteError); hide(deleteModal); }
      }
    });

    // backdrop click closes modals
    const wireBackdrop = (modalEl, closeFn) => {
      if (!modalEl) return;
      modalEl.addEventListener('click', (ev) => {
        if (ev.target === modalEl) closeFn();
      });
    };
    wireBackdrop(editModal, () => { clearError(editError); hide(editModal); });
    wireBackdrop(deleteModal, () => { clearError(deleteError); hide(deleteModal); });
  });
})();
