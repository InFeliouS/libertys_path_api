document.addEventListener('DOMContentLoaded', () => {
  const form        = document.getElementById('student-form');
  const givenInput  = document.getElementById('given_name');
  const middleInput = document.getElementById('middle_name');
  const lastInput   = document.getElementById('last_name');
  const sectionSel  = document.getElementById('section_id');

  const previewUser = document.getElementById('previewUsername');
  const previewPass = document.getElementById('previewPassword');

  // Force uppercase on name fields (while typing)
  [givenInput, middleInput, lastInput].forEach(inp => {
    inp.addEventListener('input', () => {
      const pos = inp.selectionStart;
      inp.value = inp.value.toUpperCase();
      inp.setSelectionRange(pos, pos);
      updatePreview();
    });
  });

  // Load sections
  sectionSel.innerHTML = '<option>Loading sections…</option>';
  fetch('api/v1/sections.php')
    .then(r => r.json())
    .then(json => {
      if (!json.success) throw new Error('Failed to load sections');
      sectionSel.innerHTML =
        '<option value="">— Select a section —</option>' +
        json.sections.map(s =>
          `<option value="${s.id}">${s.section_name} (${s.start_school_year}–${s.end_school_year})</option>`
        ).join('');
    })
    .catch(() => { sectionSel.innerHTML = '<option>Error loading sections</option>'; });

  // Preview on any change
  [givenInput, middleInput, lastInput].forEach(i => i.addEventListener('input', updatePreview));
  sectionSel.addEventListener('change', updatePreview);
  updatePreview();

  function updatePreview() {
    const g = givenInput.value.trim();
    const m = middleInput.value.trim();
    const l = lastInput.value.trim();
    if (!g || !l) {
      previewUser.textContent = '—';
      previewPass.textContent = '—';
      return;
    }
    const base = (g[0] || '').toLowerCase()
               + (m ? m[0].toLowerCase() : '')
               + l.replace(/\s+|-|'/g,'').toLowerCase(); // jmcruz
    previewUser.textContent = base;

    // Keep your current password preview behavior: seed from middle/given + random hint
    const seed = (m || g).replace(/\s+/g,'').toLowerCase();
    previewPass.textContent = seed.charAt(0).toUpperCase() + seed.slice(1) + '####';
  }

  // Submit to teacher route (keeps teacher auth)
  form.addEventListener('submit', async (e) => {
    e.preventDefault();
    if (!sectionSel.value) { alert('Please select a section'); return; }

    const fd = new FormData(form);
    const resp = await fetch(form.action, { method: 'POST', body: fd });
    const json = await resp.json().catch(() => ({}));
    if (resp.ok && json.success) {
      alert(`Student Registered!\n\nUsername: ${json.username}\nPassword: ${json.password}`);
      window.location.href = 'index.php?r=dashboard';
    } else {
      alert(`Registration failed:\n${json.error || 'Unknown error'}`);
    }
  });
});
