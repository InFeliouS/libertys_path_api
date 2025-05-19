document.addEventListener('DOMContentLoaded', () => {
  const form        = document.getElementById('student-form');
  const givenInput  = document.getElementById('given_name');
  const middleInput = document.getElementById('middle_name');
  const lastInput   = document.getElementById('last_name');
  const sectionSel  = document.getElementById('section_id');

  const previewUser = document.getElementById('previewUsername');
  const previewPass = document.getElementById('previewPassword');

  let nextId = null;

  // Force uppercase on name fields
  [givenInput, middleInput, lastInput].forEach(inp =>
    inp.addEventListener('input', () => inp.value = inp.value.toUpperCase())
  );

  // Fetch next student ID
  fetch('/api/v1/next_student_id.php')
    .then(r => r.json())
    .then(json => {
      if (json.success) nextId = json.next_id;
    })
    .catch(console.error)
    .finally(updatePreview);

  // Load sections into dropdown
  sectionSel.innerHTML = '<option>Loading sections…</option>';
  fetch('/api/v1/sections.php')
    .then(r => r.json())
    .then(json => {
      if (json.success) {
        sectionSel.innerHTML = '<option value="">— Select a section —</option>' +
          json.sections.map(s =>
            `<option value="${s.id}">
               ${s.section_name} (${s.start_school_year}–${s.end_school_year})
             </option>`
          ).join('');
      } else {
        sectionSel.innerHTML = '<option>Error loading sections</option>';
      }
    })
    .catch(() => {
      sectionSel.innerHTML = '<option>Error fetching sections</option>';
    });

  // Preview every time inputs change
  [givenInput, middleInput, lastInput].forEach(i =>
    i.addEventListener('input', updatePreview)
  );

  function updatePreview() {
    if (!nextId) {
      previewUser.textContent = '—';
      previewPass.textContent = '—';
      return;
    }
    const g = givenInput.value.trim();
    const l = lastInput.value.trim();
    if (!g || !l) {
      previewUser.textContent = '—';
      previewPass.textContent = '—';
      return;
    }

    // username: first initial + lastname(without spaces) + id
    const uname = g.charAt(0).toLowerCase()
                + l.replace(/\s+/g,'').toLowerCase()
                + nextId;
    // password: middle or given (no spaces, capitalized) + id
    let src = (middleInput.value.trim() || g).replace(/\s+/g,'').toLowerCase();
    const pwd = src.charAt(0).toUpperCase() + src.slice(1) + nextId;

    previewUser.textContent = uname;
    previewPass.textContent = pwd;
  }

  // When form submits, let PHP handle creation, then show credentials in an alert
  form.addEventListener('submit', async e => {
    e.preventDefault();

    // gather form data
    const fd = new FormData(form);
    try {
      const resp = await fetch(form.action, {
        method: 'POST',
        body: fd,
        headers: { 'Accept': 'application/json' }
      });
      const json = await resp.json();

      if (resp.ok && json.success) {
        alert(
          `Student Registered!\n\nUsername: ${json.username}\n` +
          `Password: ${json.password}`
        );
        window.location.href = '/dashboard';
      } else {
        alert(`Registration failed:\n${json.error||'Unknown error'}`);
      }
    } catch (err) {
      alert(`Network error:\n${err.message}`);
    }
  });
});
