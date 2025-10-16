// public/js/teacher_create.js
// Load sections from API and render checkboxes into #tcSections

(function () {
  const container = document.getElementById('tcSections');
  if (!container) return;

  // Helper to render
  function renderSections(items) {
    if (!Array.isArray(items) || items.length === 0) {
      container.innerHTML = '<div class="tc-hint">No unassigned sections found.</div>';
      return;
    }
    container.innerHTML = items.map(s => {
      const id  = Number(s.id || s.section_id || 0);
      const name = String(s.section_name || '');
      const sy1 = Number(s.start_school_year || '');
      const sy2 = Number(s.end_school_year || '');
      return `
        <label class="tc-section-item">
          <input type="checkbox" name="section_ids[]" value="${id}">
          <span>${escapeHtml(name)} (${sy1}–${sy2})</span>
        </label>
      `;
    }).join('');
  }

  // Basic HTML escape
  function escapeHtml(str) {
    return String(str)
      .replaceAll('&', '&amp;')
      .replaceAll('<', '&lt;')
      .replaceAll('>', '&gt;')
      .replaceAll('"', '&quot;')
      .replaceAll("'", '&#039;');
  }

  // Fetch sections — works with either {items:[...]} or {sections:[...]}
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
})();
