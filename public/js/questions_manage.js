(function () {
  const API_BASE = 'api/v1/guard_questions';

  const els = {
    qId: document.getElementById('qId'),
    question_text: document.getElementById('question_text'),
    c1: document.getElementById('choice1'),
    c2: document.getElementById('choice2'),
    c3: document.getElementById('choice3'),
    c4: document.getElementById('choice4'),
    correct_index: document.getElementById('correct_index'),
    form: document.getElementById('qForm'),
    formMsg: document.getElementById('formMsg'),
    formTitle: document.getElementById('formTitle'),
    submitBtn: document.getElementById('submitBtn'),
    cancelEditBtn: document.getElementById('cancelEditBtn'),
    tbody: document.getElementById('qTbody')
  };

  function escapeHtml(s) {
    return String(s).replace(/[&<>"']/g, m => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[m]));
  }

  async function listQuestions() {
    const res = await fetch(`${API_BASE}/list.php`);
    const json = await res.json();
    if (!json.success) { els.tbody.innerHTML = `<tr><td colspan="5">Failed to load.</td></tr>`; return; }
    els.tbody.innerHTML = '';
    json.data.forEach(q => {
      const tr = document.createElement('tr');
      const choices = [
        `0: ${escapeHtml(q.choice1)}`,
        `1: ${escapeHtml(q.choice2)}`,
        `2: ${escapeHtml(q.choice3)}`,
        `3: ${escapeHtml(q.choice4)}`
      ].join('<br>');
      tr.innerHTML = `
        <td>${q.id}</td>
        <td>${escapeHtml(q.question_text)}</td>
        <td>${choices}</td>
        <td>${q.correct_index}</td>
        <td class="row-actions">
          <button data-id="${q.id}" class="btn-ghost edit">Edit</button>
          <button data-id="${q.id}" class="btn-ghost delete">Delete</button>
        </td>`;
      els.tbody.appendChild(tr);
    });
  }

  function readForm() {
    return {
      id: els.qId.value ? parseInt(els.qId.value, 10) : null,
      question_text: els.question_text.value.trim(),
      choice1: els.c1.value.trim(),
      choice2: els.c2.value.trim(),
      choice3: els.c3.value.trim(),
      choice4: els.c4.value.trim(),
      correct_index: parseInt(els.correct_index.value, 10)
    };
  }

  function resetForm() {
    els.qId.value = '';
    els.question_text.value = '';
    els.c1.value = els.c2.value = els.c3.value = els.c4.value = '';
    els.correct_index.value = '';
    els.formTitle.textContent = 'Add New Question';
    els.submitBtn.textContent = 'Create';
    els.cancelEditBtn.classList.add('hidden');
    els.formMsg.textContent = '';
  }

  els.form.addEventListener('submit', async (e) => {
    e.preventDefault();
    const data = readForm();
    els.formMsg.textContent = 'Saving...';
    const isEdit = !!data.id;
    const url = isEdit ? `${API_BASE}/update.php` : `${API_BASE}/create.php`;
    const res = await fetch(url, {
      method: 'POST',
      headers: {'Content-Type': 'application/json'},
      body: JSON.stringify(data)
    });
    const json = await res.json();
    if (json.success) {
      resetForm();
      await listQuestions();
      els.formMsg.textContent = isEdit ? 'Updated.' : 'Created.';
    } else {
      els.formMsg.textContent = json.error || 'Something went wrong.';
    }
  });

  els.cancelEditBtn.addEventListener('click', resetForm);

  els.tbody.addEventListener('click', async (e) => {
    const btn = e.target.closest('button');
    if (!btn) return;
    const id = parseInt(btn.dataset.id, 10);

    if (btn.classList.contains('edit')) {
      // read values from row (no extra API)
      const row = btn.closest('tr').children;
      els.qId.value = id;
      els.question_text.value = row[1].textContent;
      const lines = row[2].innerHTML.split('<br>');
      els.c1.value = lines[0].slice(3);
      els.c2.value = lines[1].slice(3);
      els.c3.value = lines[2].slice(3);
      els.c4.value = lines[3].slice(3);
      els.correct_index.value = row[3].textContent.trim();
      els.formTitle.textContent = 'Edit Question';
      els.submitBtn.textContent = 'Save Changes';
      els.cancelEditBtn.classList.remove('hidden');
    }

    if (btn.classList.contains('delete')) {
      if (!confirm('Delete this question?')) return;
      const res = await fetch(`${API_BASE}/delete.php`, {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({ id })
      });
      const json = await res.json();
      if (json.success) { listQuestions(); }
    }
  });

  listQuestions();
})();
