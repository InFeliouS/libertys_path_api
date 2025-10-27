// public/js/teacher_create.js
// 1) Load sections into #tcSections
// 2) Client-side validation with right-side red messages
// 3) Live password checklist (✓ green when satisfied, ✕ red when not)

(function () {
  // ------- Sections loader -------
  const container = document.getElementById('tcSections');
  if (container) {
    function renderSections(items) {
      if (!Array.isArray(items) || items.length === 0) {
        container.innerHTML = '<div class="tc-hint">No unassigned sections found.</div>';
        return;
      }
      container.innerHTML = items.map(s => {
        const id   = Number(s.id || s.section_id || 0);
        const name = String(s.section_name || '');
        const sy1  = s.start_school_year ?? s.sy_start ?? '';
        const sy2  = s.end_school_year ?? s.sy_end ?? '';
        return `
          <label class="tc-section-item">
            <input type="checkbox" name="section_ids[]" value="${id}">
            <span>${escapeHtml(name)} (${sy1}–${sy2})</span>
          </label>
        `;
      }).join('');
    }

    function escapeHtml(str) {
      return String(str)
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;')
        .replaceAll("'", '&#039;');
    }

    fetch('api/v1/sections_available.php', { method: 'GET' })
      .then(r => {
        if (!r.ok) throw new Error(`HTTP ${r.status}`);
        return r.json();
      })
      .then(data => {
        const items = Array.isArray(data?.items) ? data.items
                    : Array.isArray(data?.sections) ? data.sections
                    : [];
        renderSections(items);
      })
      .catch(err => {
        console.error('Failed to load sections:', err);
        container.innerHTML = '<div class="tc-hint">Failed to load sections.</div>';
      });
  }

  // ------- Validation -------
  const form = document.getElementById('tcForm');
  if (!form) return;

  const fields = {
    first_name: document.getElementById('first_name'),
    last_name : document.getElementById('last_name'),
    username  : document.getElementById('username'),
    password  : document.getElementById('password'),
    confirm   : document.getElementById('confirm'),
  };

  // Map for error spans
  const errors = {};
  Object.keys(fields).forEach(key => {
    const el = document.querySelector(`.tc-error[data-error-for="${key}"]`);
    if (el) errors[key] = el;
  });

  // Password rules + aliases
  const pwRules = {
    // length
    len    : (v) => v.length >= 8,
    length : (v) => v.length >= 8,
    min8   : (v) => v.length >= 8,

    // uppercase
    upper     : (v) => /[A-Z]/.test(v),
    uppercase : (v) => /[A-Z]/.test(v),

    // lowercase
    lower     : (v) => /[a-z]/.test(v),
    lowercase : (v) => /[a-z]/.test(v),

    // digit / number
    digit  : (v) => /\d/.test(v),
    number : (v) => /\d/.test(v),

    // special / symbol (spaces count as special; tweak if you want to exclude)
    special : (v) => /[^A-Za-z0-9]/.test(v),
    symbol  : (v) => /[^A-Za-z0-9]/.test(v),
  };

  const pwRegex =
    /^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[^A-Za-z0-9]).{8,}$/;

  // Checklist elements
  const checklist = document.getElementById('pwChecklist');
  const ruleEls = checklist
    ? Array.from(checklist.querySelectorAll('.pw-item'))
    : [];

  function updatePwChecklist(val) {
    if (!ruleEls.length) return;
    ruleEls.forEach(li => {
      const rule = li.getAttribute('data-rule');
      const fn = pwRules[rule];
      // Unknown rule names = treat as failed (❌)
      const ok = typeof fn === 'function' ? fn(val) : false;
      li.classList.toggle('ok', ok);
      li.classList.toggle('bad', !ok);
    });
  }

  function setError(key, message) {
    const input = fields[key];
    const badge = errors[key];
    if (!input || !badge) return;
    if (message) {
      input.classList.add('tc-invalid');
      badge.textContent = message;
      badge.style.display = 'inline-block';
    } else {
      input.classList.remove('tc-invalid');
      badge.textContent = '';
      badge.style.display = 'none';
    }
  }

  function validateField(key) {
    const v = (fields[key]?.value || '').trim();

    if (key === 'first_name' || key === 'last_name' || key === 'username') {
      if (!v) { setError(key, 'Required'); return false; }
      setError(key, ''); return true;
    }

    if (key === 'password') {
      updatePwChecklist(v);
      if (!v) { setError(key, 'Required'); return false; }
      if (!pwRegex.test(v)) {
        setError(key, 'Min 8 + upper/lower/number/special');
        return false;
      }
      setError(key, '');
      validateField('confirm'); // re-check confirm when password changes
      return true;
    }

    if (key === 'confirm') {
      const p = (fields.password.value || '').trim();
      if (!v) { setError(key, 'Required'); return false; }
      if (v !== p) { setError(key, 'Passwords do not match'); return false; }
      setError(key, ''); return true;
    }

    return true;
  }

  // Live validation on blur & input
  Object.keys(fields).forEach(key => {
    const input = fields[key];
    if (!input) return;

    // Initialize checklist once (and show/hide based on initial value)
    if (key === 'password') {
      const val = input.value || '';
      updatePwChecklist(val);
      if (checklist) checklist.classList.toggle('show', val.length > 0);
    }

    input.addEventListener('blur', () => validateField(key));
    input.addEventListener('input', () => {
      if (key === 'password') {
        const val = input.value || '';
        updatePwChecklist(val);
        if (checklist) checklist.classList.toggle('show', val.length > 0);
      }
      validateField(key);
    });
  });

  // Block submit if anything invalid
  form.addEventListener('submit', (e) => {
    let ok = true;
    ok = validateField('first_name') && ok;
    ok = validateField('last_name')  && ok;
    ok = validateField('username')   && ok;
    ok = validateField('password')   && ok;
    ok = validateField('confirm')    && ok;

    if (!ok) {
      e.preventDefault();
      for (const key of ['first_name','last_name','username','password','confirm']) {
        if (fields[key].classList.contains('tc-invalid')) {
          fields[key].focus();
          break;
        }
      }
    }
  });
})();
