// section_detail.js — Section page
// Minimal surgical edits: remove Auto-refresh checkbox dependency and make auto-refresh always-on (5 minutes).
(function () {
  // ====== helpers ======
  const qs = new URLSearchParams(location.search);
  const sectionId = Number(qs.get("section_id") || "0");

  const $ = (id) => document.getElementById(id);
  const fmt = (v) => (v === null || v === undefined ? "" : String(v));
  const esc = (s) =>
    String(s)
      .replace(/&/g, "&amp;")
      .replace(/</g, "&lt;")
      .replace(/>/g, "&gt;")
      .replace(/"/g, "&quot;");
  const secToClock = (sec) => {
    sec = Number(sec) || 0;
    const m = Math.floor(sec / 60),
      s = sec % 60;
    return `${m}:${String(s).padStart(2, "0")}`;
  };
  const runText = (row) =>
    row && row.run_status && row.run_status.trim() !== ""
      ? row.run_status
      : Number(row && row.life_used) === 0
      ? "PERFECT RUN"
      : "ONE LIFE USED";

  // ====== DOM refs ======
  // Students
  const sectionTitleEl = $("sectionTitle");
  const searchLastName = $("searchLastName");
  const btnDeleteSelected = $("btnDeleteSelected");
  const btnDownloadCsv = $("btnDownloadCsv");
  const checkAll = $("checkAll");
  const studentsTbody = $("studentsTbody");
  const studentsPageSizeSel = $("studentsPageSize");
  const studPrevBtn = $("studPrevBtn");
  const studNextBtn = $("studNextBtn");
  const studPageInfo = $("studPageInfo");
  const lastUpdated = $("lastUpdated");
  const lbPageSizeSel = $("lbPageSize");
  const lbPerfectOnly = $("lbPerfectOnly");
  const lbRefreshBtn = $("lbRefreshBtn");
  const lbBody = $("lbBody");
  const lbPageInfo = $("lbPageInfo");
  const lbPrevBtn = $("lbPrevBtn");
  const lbNextBtn = $("lbNextBtn");
  const lbUpdated = $("lbUpdated");
  const lbRows = $("lbRows");
  const lbDatePicker = $("lbDatePicker");

  if (!sectionId) {
    if (studentsTbody)
      studentsTbody.innerHTML = `<tr><td colspan="5" class="muted">Missing section_id.</td></tr>`;
    if (lbBody)
      lbBody.innerHTML = `<tr><td colspan="6" class="lb-muted">Missing section_id.</td></tr>`;
    return;
  }

  // ====== state ======
  let studentsAll = []; // raw list for this section
  let studentsFiltered = []; // filtered by search
  let studPage = 1;

  // Leaderboard state: server-driven paging
  let lbPage = 1;
  let lbRowsCurrent = []; // rows for current page (from server or sliced client-side)
  let lbTotal = 0; // total rows reported by server or derived
  const LB_PAGE_SIZE = 10; // FIXED page size per request / display

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
      studentsTbody.innerHTML = slice
        .map(
          (st) => `
        <tr data-id="${esc(st.id || "")}">
          <td style="text-align:center;">
            <input type="checkbox" class="rowCheck" data-username="${esc(
              st.username || ""
            )}" />
          </td>
          <td data-label="LAST NAME">${esc(st.last_name || "—")}</td>
          <td data-label="FIRST NAME">${esc(
            st.given_name || st.first_name || "—"
          )}</td>
          <td data-label="MIDDLE NAME">${esc(st.middle_name || "—")}</td>
          <td data-label="USERNAME">${esc(st.username || "—")}</td>
        </tr>
      `
        )
        .join("");
    }

    studPageInfo.textContent = `PAGE ${studPage} OF ${pages}`;
    studPrevBtn.disabled = studPage <= 1;
    studNextBtn.disabled = studPage >= pages;
  }

  async function loadStudents() {
    try {
      studentsTbody.innerHTML = `<tr><td colspan="5" class="muted">Loading…</td></tr>`;
      const url = `api/v1/sections_students.php?section_id=${sectionId}`;
      const res = await fetch(url, { headers: { Accept: "application/json" } });
      const ct = res.headers.get("content-type") || "";
      if (!ct.includes("application/json")) {
        throw new Error("Invalid response.");
      }
      const json = await res.json();

      // Accept either { ok:true, data:[...] } or just [...]
      let data = Array.isArray(json)
        ? json
        : Array.isArray(json.data)
        ? json.data
        : [];
      // Normalize field names lightly (first name sometimes 'given_name')
      data = data.map((r) => ({
        id: r.id,
        last_name: r.last_name,
        given_name: r.given_name ?? r.first_name,
        middle_name: r.middle_name,
        username: r.username,
      }));

      studentsAll = data;
      studentsFiltered = studentsAll.slice();
      studPage = 1;
      renderStudentsPage();

      // Try to set section title if endpoint provided it via query param
      const sectionNameFromQuery = qs.get("section_name");
      if (
        sectionTitleEl &&
        sectionNameFromQuery &&
        sectionTitleEl.textContent.trim() === "Section"
      ) {
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
    studentsFiltered = studentsAll.filter((st) => matchesSearch(st, q));
    studPage = 1;
    renderStudentsPage();
  }

  function renderLbPage() {
    const size = LB_PAGE_SIZE;
    const pages = Math.max(1, Math.ceil(lbTotal / size));
    lbPage = Math.min(Math.max(1, lbPage), pages);

    const start = (lbPage - 1) * size;
    const rows = lbRowsCurrent || [];

    if (rows.length === 0) {
      lbBody.innerHTML = `<tr><td colspan="6" class="muted">No results.</td></tr>`;
      lbRows.textContent = `0 rows`;
    } else {
      lbBody.innerHTML = rows
        .map(
          (r, i) => `
        <tr>
          <td class="mono">${start + i + 1}</td>
          <td>${esc(r.player1_name || "")} &amp; ${esc(
            r.player2_name || ""
          )}</td>
          <td class="mono">${esc(String(r.score ?? ""))}</td>
          <td class="mono">${secToClock(r.time_left)}</td>
          <td>${esc(runText(r))}</td>
          <td class="mono">${new Date(r.created_at).toLocaleString()}</td>
        </tr>
      `
        )
        .join("");
      lbRows.textContent = `${lbTotal} row${
        lbTotal === 1 ? "" : "s"
      } (showing ${rows.length})`;
    }

    lbPageInfo.textContent = `PAGE ${lbPage} OF ${pages}`;
    lbPrevBtn.disabled = lbPage <= 1;
    lbNextBtn.disabled = lbPage >= pages;
  }

  // >>> drop-in: student table reload button wiring (paste into section_detail.js)
  function wireStudentReloadButton() {
    const sdReloadBtn = $("sdReloadBtn"); // make sure your HTML button uses id="sdReloadBtn"
    if (!sdReloadBtn) return;

    sdReloadBtn.addEventListener("click", async (ev) => {
      ev.preventDefault();
      // UX: reset to first page and show loading state
      try {
        sdReloadBtn.disabled = true;
        studPage = 1; // reset client paging
        // loadStudents() is the existing async function in your file that fetches + renders rows
        await loadStudents();
      } catch (err) {
        console.error("Failed to reload student table:", err);
        // optional: user-visible error
        alert("Failed to reload student table. Check console for details.");
      } finally {
        sdReloadBtn.disabled = false;
      }
    });
  }

  async function loadLeaderboard() {
    try {
      lbBody.innerHTML = `<tr><td colspan="6" class="lb-muted">Loading…</td></tr>`;

      const perfectOnly = lbPerfectOnly && lbPerfectOnly.checked ? "1" : "0";

      let url = `index.php?r=api/leaderboard/team/top_by_section&section_id=${sectionId}&page=${lbPage}&limit=${LB_PAGE_SIZE}&perfect_only=${perfectOnly}`;
      if (lbDatePicker && lbDatePicker.value) {
        url += `&date=${encodeURIComponent(lbDatePicker.value)}`;
      }

      const res = await fetch(url, { headers: { Accept: "application/json" } });
      if (!res.ok) throw new Error(`HTTP ${res.status}`);
      const json = await res.json();

      let data = [];
      let total = 0;
      let serverProvidedPage = false;

      if (Array.isArray(json)) {
        data = json;
        total = data.length;
      } else if (json && typeof json === "object") {
        if (json.ok === false) {
          throw new Error(json.error || "Server returned error");
        }
        if (Array.isArray(json.data)) {
          data = json.data;
        } else if (Array.isArray(json.rows)) {
          data = json.rows;
        } else {
          for (const k in json) {
            if (Array.isArray(json[k])) {
              data = json[k];
              break;
            }
          }
        }
        if (json.meta && Number.isFinite(Number(json.meta.total))) {
          serverProvidedPage = true;
          total = Number(json.meta.total);
        } else {
          total = data.length;
        }
      } else {
        throw new Error("Invalid JSON payload");
      }

      if (serverProvidedPage) {
        lbRowsCurrent = Array.isArray(data) ? data : [];
        lbTotal = Number.isFinite(Number(total))
          ? Number(total)
          : lbRowsCurrent.length;
        if (json.meta && Number.isFinite(Number(json.meta.page))) {
          lbPage = Math.max(1, Number(json.meta.page));
        }
      } else {
        const full = Array.isArray(data) ? data : [];
        lbTotal = full.length;
        const pages = Math.max(1, Math.ceil(lbTotal / LB_PAGE_SIZE));
        if (lbPage < 1) lbPage = 1;
        if (lbPage > pages) lbPage = pages;
        const start = (lbPage - 1) * LB_PAGE_SIZE;
        lbRowsCurrent = full.slice(start, start + LB_PAGE_SIZE);
      }

      const pages = Math.max(1, Math.ceil(lbTotal / LB_PAGE_SIZE));
      if (lbPage > pages) lbPage = pages;
      if (lbPage < 1) lbPage = 1;

      renderLbPage();
      lbUpdated.textContent = `Last updated: ${new Date().toLocaleString()}`;
    } catch (e) {
      console.error("Leaderboard load error:", e);
      lbBody.innerHTML = `<tr><td colspan="6" class="lb-muted">Load error.</td></tr>`;
      lbRows.textContent = "";
    }
  }

  // ====== wiring ======
  // Students
  searchLastName &&
    searchLastName.addEventListener("input", () => {
      clearTimeout(searchLastName._t);
      searchLastName._t = setTimeout(applyStudentSearch, 150);
    });
  studPrevBtn &&
    studPrevBtn.addEventListener("click", () => {
      studPage--;
      renderStudentsPage();
    });
  studNextBtn &&
    studNextBtn.addEventListener("click", () => {
      studPage++;
      renderStudentsPage();
    });
  studentsPageSizeSel &&
    studentsPageSizeSel.addEventListener("change", () => {
      studPage = 1;
      renderStudentsPage();
    });
  // === DELETE SELECTED STUDENTS ===
  btnDeleteSelected &&
    btnDeleteSelected.addEventListener("click", async () => {
      // Collect checked student ids from table rows
      const selected = Array.from(
        document.querySelectorAll("#studentsTbody .rowCheck:checked")
      )
        .map((cb) => cb.closest("tr")?.dataset?.id)
        .filter(Boolean)
        .map((id) => Number(id));

      if (!selected.length) {
        alert("Please select at least one student to delete.");
        return;
      }

      if (
        !confirm(
          `Delete ${selected.length} selected student(s)? This cannot be undone.`
        )
      ) {
        return;
      }

      // Disable button while request is in progress
      btnDeleteSelected.disabled = true;

      try {
        // send to router path so auth runs: requires routes.php case
        const res = await fetch(`index.php?r=sections/delete_students`, {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({
            ids: selected,
            section_id: sectionId, // helpful safety: server will only delete students in this section if implemented
          }),
        });

        if (!res.ok) {
          // network / HTTP-level error
          throw new Error("HTTP " + res.status);
        }

        const json = await res.json();

        // Success path: refresh immediately and DO NOT show a popup
        if (json && json.success) {
          // refresh students list to reflect deletion
          await loadStudents();
          // clear master checkbox
          if (checkAll) checkAll.checked = false;
          // also clear any selected checkboxes in DOM (if present)
          document
            .querySelectorAll("#studentsTbody .rowCheck:checked")
            .forEach((cb) => (cb.checked = false));
          return; // no popup on success
        }

        // If server responded but indicated failure, show one error popup
        const errMsg =
          json && (json.error || json.message)
            ? json.error || json.message
            : "Unknown error";
        alert("Error deleting students: " + errMsg);

        // Refresh anyway so UI is in sync with DB (in case some deletes succeeded)
        await loadStudents();
      } catch (err) {
        console.error("Delete students request failed:", err);
        alert("Error deleting students: " + (err.message || "Unknown error"));
        // attempt refresh to keep UI consistent
        try {
          await loadStudents();
        } catch (_) {}
      } finally {
        btnDeleteSelected.disabled = false;
      }
    });

  // Leaderboard
  lbRefreshBtn &&
    lbRefreshBtn.addEventListener("click", () => {
      lbPage = 1;
      loadLeaderboard();
    });
  lbPerfectOnly &&
    lbPerfectOnly.addEventListener("change", () => {
      lbPage = 1;
      loadLeaderboard();
    });

  lbDatePicker &&
    lbDatePicker.addEventListener("change", () => {
      lbPage = 1;
      loadLeaderboard();
    });

  lbPrevBtn &&
    lbPrevBtn.addEventListener("click", () => {
      const pages = Math.max(1, Math.ceil(lbTotal / LB_PAGE_SIZE));
      if (lbPage > 1) {
        lbPage = Math.max(1, lbPage - 1);
        loadLeaderboard();
      } else {
        renderLbPage();
      }
    });
  lbNextBtn &&
    lbNextBtn.addEventListener("click", () => {
      const pages = Math.max(1, Math.ceil(lbTotal / LB_PAGE_SIZE));
      if (lbPage < pages) {
        lbPage = Math.min(pages, lbPage + 1);
        loadLeaderboard();
      } else {
        renderLbPage();
      }
    });

  // Keep lbPageSizeSel in DOM but ignore its change (fixed page size).
  // lbPageSizeSel && lbPageSizeSel.addEventListener("change", () => { /* ignored on purpose */ });

  // Auto refresh: ALWAYS-ON (30s). We no longer depend on a checkbox.
  let timer = null;
  function armAutoRefresh(on) {
    if (timer) {
      clearInterval(timer);
      timer = null;
    }
    if (on) {
      timer = setInterval(() => {
        loadLeaderboard();
      }, 420000);
    }
  }
  // Start auto-refresh unconditionally
  armAutoRefresh(true);

  // ====== init ======
  loadStudents();
  loadLeaderboard();
  wireStudentReloadButton(); 
  // Select-all checkbox behavior (preserved)
  checkAll &&
    checkAll.addEventListener("change", () => {
      document
        .querySelectorAll("#studentsTbody .rowCheck")
        .forEach((cb) => (cb.checked = checkAll.checked));
    });
})();
