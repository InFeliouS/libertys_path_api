// section_detail.js — Section page
// Surgical: restore original students API + field names, keep new pagination & the updated leaderboard.

(function () {
  // ====== helpers ======
  const qs = new URLSearchParams(location.search);
  const sectionId = Number(qs.get("section_id") || "0");

  const $ = (id) => document.getElementById(id);
  const fmt = (v) => (v === null || v === undefined) ? "" : String(v);
  const esc = (s) => String(s).replace(/&/g,"&amp;").replace(/</g,"&lt;")
                              .replace(/>/g,"&gt;").replace(/"/g,"&quot;");
  const secToClock = (sec) => {
    sec = Number(sec) || 0;
    const m = Math.floor(sec/60), s = sec % 60;
    return `${m}:${String(s).padStart(2,"0")}`;
  };
  const runText = (row) => (row && row.run_status && row.run_status.trim()!=="")
    ? row.run_status
    : (Number(row && row.life_used) === 0 ? "PERFECT RUN" : "ONE LIFE USED");

  // ====== DOM refs ======
  // Students
  const sectionTitleEl   = $("sectionTitle");
  const searchLastName   = $("searchLastName");
  const btnDeleteSelected= $("btnDeleteSelected");
  const btnDownloadCsv   = $("btnDownloadCsv");
  const checkAll         = $("checkAll");
  const studentsTbody    = $("studentsTbody");
  const studentsPageSizeSel = $("studentsPageSize");
  const studPrevBtn      = $("studPrevBtn");
  const studNextBtn      = $("studNextBtn");
  const studPageInfo     = $("studPageInfo");
  const lastUpdated      = $("lastUpdated");

  // Leaderboard
  const lbPageSizeSel    = $("lbPageSize");
  const lbPerfectOnly    = $("lbPerfectOnly");
  const lbAutoRefresh    = $("lbAutoRefresh");
  const lbRefreshBtn     = $("lbRefreshBtn");
  const lbBody           = $("lbBody");
  const lbPageInfo       = $("lbPageInfo");
  const lbPrevBtn        = $("lbPrevBtn");
  const lbNextBtn        = $("lbNextBtn");
  const lbUpdated        = $("lbUpdated");
  const lbRows           = $("lbRows");

  if (!sectionId) {
    if (studentsTbody) studentsTbody.innerHTML = `<tr><td colspan="5" class="muted">Missing section_id.</td></tr>`;
    if (lbBody) lbBody.innerHTML = `<tr><td colspan="6" class="lb-muted">Missing section_id.</td></tr>`;
    return;
  }

  // ====== state ======
  let studentsAll = [];      // raw list for this section
  let studentsFiltered = []; // filtered by search
  let studPage = 1;

  let lbAll = [];            // raw list from API (we pull up to 500)
  let lbPage = 1;

  // ====== STUDENTS ======
  function matchesSearch(stud, q) {
    if (!q) return true;
    const s = q.toLowerCase();
    return (stud.last_name || "").toLowerCase().includes(s);
  }

  function renderStudentsPage() {
    const size = Number(studentsPageSizeSel.value || "10");
    const pages = Math.max(1, Math.ceil(studentsFiltered.length / size));
    studPage = Math.min(Math.max(1, studPage), pages);

    const start = (studPage - 1) * size;
    const slice = studentsFiltered.slice(start, start + size);

    if (slice.length === 0) {
      studentsTbody.innerHTML = `<tr><td colspan="5" class="muted">No students.</td></tr>`;
    } else {
      studentsTbody.innerHTML = slice.map(st => `
        <tr data-id="${esc(st.id||"")}">
          <td style="text-align:center;">
            <input type="checkbox" class="rowCheck" data-username="${esc(st.username||"")}" />
          </td>
          <td data-label="LAST NAME">${esc(st.last_name || "—")}</td>
          <td data-label="FIRST NAME">${esc(st.given_name || st.first_name || "—")}</td>
          <td data-label="MIDDLE NAME">${esc(st.middle_name || "—")}</td>
          <td data-label="USERNAME">${esc(st.username || "—")}</td>
        </tr>
      `).join("");
    }

    studPageInfo.textContent = `Page ${studPage} of ${pages}`;
    studPrevBtn.disabled = (studPage <= 1);
    studNextBtn.disabled = (studPage >= pages);
  }

  async function loadStudents() {
    // RESTORE ORIGINAL ENDPOINT + JSON SHAPE
    // Your legacy API returns JSON from: api/v1/sections_students.php?section_id=...
    try {
      studentsTbody.innerHTML = `<tr><td colspan="5" class="muted">Loading…</td></tr>`;
      const url = `api/v1/sections_students.php?section_id=${sectionId}`;
      const res = await fetch(url, { headers: { "Accept":"application/json" }});
      const ct = (res.headers.get("content-type") || "");
      if (!ct.includes("application/json")) {
        throw new Error("Invalid response.");
      }
      const json = await res.json();

      // Accept either { ok:true, data:[...] } or just [...]
      let data = Array.isArray(json) ? json : (Array.isArray(json.data) ? json.data : []);
      // Normalize field names lightly (first name sometimes 'given_name')
      data = data.map(r => ({
        id: r.id,
        last_name: r.last_name,
        given_name: r.given_name ?? r.first_name,
        middle_name: r.middle_name,
        username: r.username
      }));

      studentsAll = data;
      studentsFiltered = studentsAll.slice();
      studPage = 1;
      renderStudentsPage();

      // Try to set section title if endpoint provided it via query param
      const sectionNameFromQuery = qs.get("section_name");
      if (sectionTitleEl && sectionNameFromQuery && sectionTitleEl.textContent.trim() === "Section") {
        sectionTitleEl.textContent = sectionNameFromQuery;
      }

      lastUpdated.textContent = `Last updated: ${new Date().toLocaleString()}`;
    } catch (e) {
      console.error(e);
      studentsTbody.innerHTML = `<tr><td colspan="5" class="muted">Load error.</td></tr>`;
    }
  }

  function applyStudentSearch() {
    const q = (searchLastName.value || "").trim();
    studentsFiltered = studentsAll.filter(st => matchesSearch(st, q));
    studPage = 1;
    renderStudentsPage();
  }

  // ====== LEADERBOARD ======
  function renderLbPage() {
    const size = Number(lbPageSizeSel.value || "50");
    const pages = Math.max(1, Math.ceil(lbAll.length / size));
    lbPage = Math.min(Math.max(1, lbPage), pages);

    const start = (lbPage - 1) * size;
    const rows = lbAll.slice(start, start + size);

    if (rows.length === 0) {
      lbBody.innerHTML = `<tr><td colspan="6" class="lb-muted">No results.</td></tr>`;
      lbRows.textContent = "0 rows";
    } else {
      lbBody.innerHTML = rows.map((r, i) => `
        <tr>
          <td class="mono">${start + i + 1}</td>
          <td>${esc(r.player1_name || "")} &amp; ${esc(r.player2_name || "")}</td>
          <td class="mono">${esc(String(r.score ?? ""))}</td>
          <td class="mono">${secToClock(r.time_left)}</td>
          <td>${esc(runText(r))}</td>
          <td class="mono">${new Date(r.created_at).toLocaleString()}</td>
        </tr>
      `).join("");
      lbRows.textContent = `${lbAll.length} row${lbAll.length===1?"":"s"} (showing ${rows.length})`;
    }

    lbPageInfo.textContent = `Page ${lbPage} of ${pages}`;
    lbPrevBtn.disabled = (lbPage <= 1);
    lbNextBtn.disabled = (lbPage >= pages);
  }

  async function loadLeaderboard() {
    try {
      lbBody.innerHTML = `<tr><td colspan="6" class="lb-muted">Loading…</td></tr>`;
      const perfectOnly = lbPerfectOnly && lbPerfectOnly.checked ? "1" : "0";
      // Keep the routed reader we already updated
      const url = `index.php?r=api/leaderboard/team/top_by_section&section_id=${sectionId}&limit=500&perfect_only=${perfectOnly}`;
      const res = await fetch(url, { headers: { "Accept":"application/json" } });
      const json = await res.json();

      if (!json || json.ok !== true) throw new Error(json && json.error || "Failed to load leaderboard");

      lbAll = Array.isArray(json.data) ? json.data : [];
      lbPage = 1;
      renderLbPage();
      lbUpdated.textContent = `Last updated: ${new Date().toLocaleString()}`;
    } catch (e) {
      console.error(e);
      lbBody.innerHTML = `<tr><td colspan="6" class="lb-muted">Load error.</td></tr>`;
    }
  }

  // ====== wiring ======
  // Students
  searchLastName && searchLastName.addEventListener("input", () => {
    clearTimeout(searchLastName._t); searchLastName._t = setTimeout(applyStudentSearch, 150);
  });
  studPrevBtn && studPrevBtn.addEventListener("click", () => { studPage--; renderStudentsPage(); });
  studNextBtn && studNextBtn.addEventListener("click", () => { studPage++; renderStudentsPage(); });
  studentsPageSizeSel && studentsPageSizeSel.addEventListener("change", () => { studPage = 1; renderStudentsPage(); });

  // Leaderboard
  lbRefreshBtn && lbRefreshBtn.addEventListener("click", loadLeaderboard);
  lbPerfectOnly && lbPerfectOnly.addEventListener("change", loadLeaderboard);
  lbPrevBtn && lbPrevBtn.addEventListener("click", () => { lbPage--; renderLbPage(); });
  lbNextBtn && lbNextBtn.addEventListener("click", () => { lbPage++; renderLbPage(); });
  lbPageSizeSel && lbPageSizeSel.addEventListener("change", () => { lbPage = 1; renderLbPage(); });

  // Auto refresh
  let timer = null;
  function armAutoRefresh(on) {
    if (timer) { clearInterval(timer); timer = null; }
    if (on) timer = setInterval(loadLeaderboard, 30000);
  }
  lbAutoRefresh && lbAutoRefresh.addEventListener("change", () => armAutoRefresh(lbAutoRefresh.checked));

  // ====== init ======
  loadStudents();
  loadLeaderboard();
  lbAutoRefresh && lbAutoRefresh.checked && armAutoRefresh(true);

  // Select-all checkbox behavior (preserved)
  checkAll && checkAll.addEventListener("change", () => {
    document.querySelectorAll("#studentsTbody .rowCheck").forEach(cb => cb.checked = checkAll.checked);
  });

  // Keep your delete/csv buttons wired to your server as before (unchanged here).
})();
