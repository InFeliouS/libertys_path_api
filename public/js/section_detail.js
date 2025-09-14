// public/js/section_detail.js
(function () {
  const qs = new URLSearchParams(location.search);
  const sectionId = Number(qs.get('section_id') || 0);

  // ---------- Students block ----------
  const sectionTitleEl  = document.getElementById('sectionTitle');
  const searchInput     = document.getElementById('searchLastName');
  const tbody           = document.getElementById('studentsTbody');
  const checkAll        = document.getElementById('checkAll');
  const btnDelete       = document.getElementById('btnDeleteSelected');
  const btnCsv          = document.getElementById('btnDownloadCsv');
  const lastUpdated     = document.getElementById('lastUpdated');

  if (!sectionId) {
    if (tbody) tbody.innerHTML = `<tr><td colspan="5" class="muted">Missing section_id.</td></tr>`;
    return;
  }

  const sectionNameFromQuery = qs.get('section_name');
  if (sectionNameFromQuery && sectionTitleEl && (!sectionTitleEl.textContent || sectionTitleEl.textContent.trim() === 'Section')) {
    sectionTitleEl.textContent = sectionNameFromQuery;
  }

  if (btnCsv) btnCsv.href = `index.php?r=dashboard/download_section_students_csv&section_id=${sectionId}`;

  let rosterRows = [];
  const fmt = (v) => (v ?? '').toString().trim() || '—';

  function renderRoster() {
    const q = (searchInput?.value || '').trim().toLowerCase();
    const filtered = q
      ? rosterRows.filter(r => (r.last_name || '').toLowerCase().includes(q))
      : rosterRows;

    if (!filtered.length) {
      tbody.innerHTML = `<tr><td colspan="5" class="muted">No students found.</td></tr>`;
      return;
    }

    tbody.innerHTML = filtered.map(r => `
      <tr data-id="${r.id}">
        <td data-label=""><input type="checkbox" class="rowCheck"></td>
        <td data-label="Last Name">${fmt(r.last_name)}</td>
        <td data-label="First Name">${fmt(r.given_name)}</td>
        <td data-label="Middle Name">${fmt(r.middle_name)}</td>
        <td data-label="Username">${fmt(r.username)}</td>
      </tr>
    `).join('');
  }

  async function loadRoster() {
    tbody.innerHTML = `<tr><td colspan="5" class="muted">Loading…</td></tr>`;
    try {
      const res = await fetch(`api/v1/sections_students.php?section_id=${sectionId}`, { headers: { 'Accept':'application/json' }});
      const ct = res.headers.get('content-type') || '';
      if (!ct.includes('application/json')) {
        const txt = await res.text();
        throw new Error(txt.slice(0, 200));
      }
      const j = await res.json();
      if (!j.ok) throw new Error(j.error || 'Failed to load');
      rosterRows = j.data || [];
      renderRoster();
      if (lastUpdated) lastUpdated.textContent = `Last updated: ${new Date().toLocaleString()}`;
    } catch (e) {
      tbody.innerHTML = `<tr><td colspan="5" class="muted">Error: ${e.message}</td></tr>`;
    }
  }

  searchInput?.addEventListener('input', renderRoster);

  checkAll?.addEventListener('change', () => {
    document.querySelectorAll('#studentsTbody .rowCheck').forEach(cb => cb.checked = checkAll.checked);
  });

  btnDelete?.addEventListener('click', async () => {
    const ids = Array.from(document.querySelectorAll('#studentsTbody .rowCheck'))
      .filter(cb => cb.checked)
      .map(cb => cb.closest('tr').dataset.id);

    if (!ids.length) return alert('Select students to delete.');
    if (!confirm(`Delete ${ids.length} selected student(s)?`)) return;

    const res = await fetch('index.php?r=dashboard/delete_students', {
      method: 'POST',
      headers: { 'Content-Type':'application/json' },
      body: JSON.stringify({ ids })
    });
    if (!res.ok) return alert('Delete failed');
    await loadRoster();
    checkAll.checked = false;
  });

  // ---------- Leaderboard block ----------
  const lbSubtitle     = document.getElementById('lbSubtitle');
  const lbLimitSel     = document.getElementById('lbLimit');
  const lbPerfectOnly  = document.getElementById('lbPerfectOnly');
  const lbAutoRefresh  = document.getElementById('lbAutoRefresh');
  const lbRefreshBtn   = document.getElementById('lbRefreshBtn');
  const lbBody         = document.getElementById('lbBody');
  const lbUpdated      = document.getElementById('lbUpdated');
  const lbRows         = document.getElementById('lbRows');

  let lbTimer = null;
  let lbRowsData = [];
  let lbSectionName = '';

  function secToClock(sec) {
    const s = Math.max(0, Number(sec) || 0);
    const m = Math.floor(s / 60);
    const r = s % 60;
    return `${m}:${r.toString().padStart(2,'0')}`;
  }
  function noteFrom(row) {
    if (Number(row.perfect) === 1) return 'Perfect';
    if (Number(row.mistakes) > 0)  return 'Used 2nd life';
    return '';
  }
  function renderLB() {
    if (!lbRowsData.length) {
      lbBody.innerHTML = `<tr><td colspan="7" class="lb-muted">No results yet.</td></tr>`;
      lbRows.textContent = 'Rows: 0';
      return;
    }
    lbBody.innerHTML = lbRowsData.map((r, i) => `
      <tr>
        <td>${i + 1}</td>
        <td>${fmt(r.player1_name)} &amp; ${fmt(r.player2_name)}</td>
        <td>${fmt(r.score)}</td>
        <td>${secToClock(r.time_left)}</td>
        <td>${fmt(r.correct)}</td>
        <td>${noteFrom(r)}</td>
        <td>${fmt(new Date(r.created_at).toLocaleString())}</td>
      </tr>
    `).join('');
    lbRows.textContent = `Rows: ${lbRowsData.length}`;
  }
  async function loadLB() {
    lbBody.innerHTML = `<tr><td colspan="7" class="lb-muted">Loading…</td></tr>`;
    try {
      const params = new URLSearchParams({
        section_id: String(sectionId),
        limit: lbLimitSel?.value || '50',
        perfect_only: lbPerfectOnly?.checked ? '1' : '0'
      });
      const res = await fetch(`api/v1/leaderboard/team_top_by_section.php?${params.toString()}`, { headers: { 'Accept':'application/json' }});
      const ct = res.headers.get('content-type') || '';
      if (!ct.includes('application/json')) {
        const txt = await res.text();
        throw new Error(txt.slice(0,200));
      }
      const j = await res.json();
      if (!j.ok) throw new Error(j.error || 'Failed to load leaderboard');
      lbSectionName = j.section_name || '';
      if (lbSubtitle) lbSubtitle.textContent = lbSectionName ? `Top results for “${lbSectionName}”` : 'Top results for this section';
      lbRowsData = j.data || [];
      renderLB();
      if (lbUpdated) lbUpdated.textContent = `Updated: ${new Date().toLocaleString()}`;
    } catch (e) {
      lbBody.innerHTML = `<tr><td colspan="7" class="lb-muted">Error: ${e.message}</td></tr>`;
      lbRows.textContent = '';
    }
  }
  function startAutoRefresh() { stopAutoRefresh(); lbTimer = setInterval(loadLB, 30000); }
  function stopAutoRefresh()  { if (lbTimer) { clearInterval(lbTimer); lbTimer = null; } }

  lbLimitSel?.addEventListener('change', loadLB);
  lbPerfectOnly?.addEventListener('change', loadLB);
  lbRefreshBtn?.addEventListener('click', loadLB);
  lbAutoRefresh?.addEventListener('change', () => lbAutoRefresh.checked ? startAutoRefresh() : stopAutoRefresh());

  // ---------- initial loads ----------
  loadRoster();
  loadLB();
})();
  