(function () {
  "use strict";

  // Elements
  const rows          = document.getElementById("rows");
  const sectionInput  = document.getElementById("section");
  const perfectOnly   = document.getElementById("perfectOnly");
  const limitSelect   = document.getElementById("limit");
  const refreshBtn    = document.getElementById("refresh");
  const exportBtn     = document.getElementById("exportCsv");
  const shareBtn      = document.getElementById("shareLink");
  const autoRefreshEl = document.getElementById("autoRefresh");
  const lastUpdatedEl = document.getElementById("lastUpdated");
  const rowCountEl    = document.getElementById("rowCount");

  let autoTimer = null;
  let lastData  = [];

  // Parse URL params to prefill filters (e.g., ?section=4A&perfect=1&limit=20)
  (function prefillFromQuery() {
    const u = new URL(window.location.href);
    const section = u.searchParams.get("section") ?? "";
    const perfect = u.searchParams.get("perfect") ?? "";
    const limit   = u.searchParams.get("limit") ?? "";
    if (section) sectionInput.value = section;
    if (perfect === "1") perfectOnly.checked = true;
    if (limit) {
      const opt = Array.from(limitSelect.options).find(o => o.value === String(limit));
      if (opt) limitSelect.value = String(limit);
    }
  })();

  // Wire events
  refreshBtn.addEventListener("click", load);
  limitSelect.addEventListener("change", load);
  perfectOnly.addEventListener("change", load);

  // Enter in Section input triggers refresh
  sectionInput.addEventListener("keydown", (e) => {
    if (e.key === "Enter") {
      e.preventDefault();
      load();
    }
  });

  // Auto refresh handling
  autoRefreshEl.addEventListener("change", () => {
    if (autoRefreshEl.checked) {
      if (autoTimer) clearInterval(autoTimer);
      autoTimer = setInterval(load, 30_000); // 30s
    } else {
      if (autoTimer) clearInterval(autoTimer);
      autoTimer = null;
    }
  });

  // CSV export
  exportBtn.addEventListener("click", () => {
    if (!lastData || !lastData.length) return;
    const header = ["Rank","Player 1","Player 2","Score","Time Left (s)","Correct","Mistakes","Perfect","Section","Date"];
    const lines = [
      header.join(","),
      ...lastData.map((r, i) => {
        const rank = r.rank ?? (i + 1);
        const t = [
          rank,
          csvSafe(r.player1_name),
          csvSafe(r.player2_name),
          r.score,
          Number(r.time_left || 0),
          Number(r.correct || 0),
          Number(r.mistakes || 0),
          Number(r.perfect || 0),
          csvSafe(r.section || ""),
          csvSafe(r.created_at || "")
        ];
        return t.join(",");
      })
    ];
    const blob = new Blob([lines.join("\n")], { type: "text/csv;charset=utf-8" });
    const url  = URL.createObjectURL(blob);
    const a    = document.createElement("a");
    a.href = url;
    a.download = "leaderboard.csv";
    document.body.appendChild(a);
    a.click();
    a.remove();
    URL.revokeObjectURL(url);
  });

  // Share link
  shareBtn.addEventListener("click", async () => {
    const u = new URL(window.location.href);
    const { section, perfect, limit } = currentQueryParams();
    if (section) u.searchParams.set("section", section);
    else u.searchParams.delete("section");
    if (perfect) u.searchParams.set("perfect", "1");
    else u.searchParams.delete("perfect");
    u.searchParams.set("limit", String(limit));
    try {
      await navigator.clipboard.writeText(u.toString());
      toast("Link copied!");
    } catch {
      toast("Copy failed. Long-press to copy the address bar.");
    }
  });

  // Initial load
  load();

  // ===== Core loader =====
  async function load() {
    const q = currentQueryParams();
    rows.innerHTML = `<tr><td colspan="8" class="lb-center lb-muted lb-pad">Loading…</td></tr>`;

    try {
      // Build router URL. Section: "all" when empty for server convenience.
      const params = new URLSearchParams();
      params.set("limit", String(q.limit));
      params.set("section", q.section ? q.section : "all");
      if (q.perfect) params.set("perfect", "1");

      const res  = await fetch(`?r=api/leaderboard/team/top&${params.toString()}`, {
        headers: { "Accept": "application/json" },
        cache: "no-store"
      });

      if (!res.ok) throw new Error(`HTTP ${res.status}`);
      const json = await res.json();

      // API may return { data: { items: [...] } } or just { items: [...] }
      const items = (json && json.data && Array.isArray(json.data.items))
        ? json.data.items
        : (Array.isArray(json.items) ? json.items : []);

      render(items);
      lastData = items;
      updateMeta();
    } catch (err) {
      console.error(err);
      rows.innerHTML = `<tr><td colspan="8" class="lb-center lb-muted lb-pad">Error loading leaderboard.</td></tr>`;
    }
  }

  function render(items) {
    if (!Array.isArray(items) || items.length === 0) {
      rows.innerHTML = `<tr><td colspan="8" class="lb-center lb-muted lb-pad">No results</td></tr>`;
      return;
    }
    rows.innerHTML = "";
    items.forEach((r, idx) => {
      const tr = document.createElement("tr");
      const rank = r.rank ?? (idx + 1);

      const notes = [
        Number(r.perfect) ? "Perfect" : "",
        Number(r.mistakes) ? "Used 2nd life" : ""
      ].filter(Boolean).join(", ");

      tr.innerHTML = `
        <td class="mono">${rank}</td>
        <td>${esc(r.player1_name)} <span class="lb-pill">&amp;</span> ${esc(r.player2_name)}</td>
        <td class="mono">${numFmt(r.score)}</td>
        <td class="mono">${mmss(Number(r.time_left || 0))}</td>
        <td class="mono">${Number(r.correct || 0)}</td>
        <td>${esc(notes)}</td>
        <td>${esc(r.section || "")}</td>
        <td>${fmtDate(r.created_at)}</td>
      `;
      rows.appendChild(tr);
    });
  }

  function updateMeta() {
    const now = new Date();
    lastUpdatedEl.textContent = `Last updated: ${now.toLocaleString()}`;
    rowCountEl.textContent = `Rows: ${lastData?.length ?? 0}`;
  }

  // ===== Helpers =====
  function currentQueryParams() {
    return {
      section: (sectionInput.value || "").trim(),
      perfect: !!perfectOnly.checked,
      limit:   Number(limitSelect.value || 50)
    };
  }

  function esc(s) {
    return String(s ?? "").replace(/[&<>"']/g, c => ({ "&":"&amp;","<":"&lt;",">":"&gt;","\"":"&quot;","'":"&#039;" }[c]));
  }
  function csvSafe(s) {
    const t = String(s ?? "");
    return /[,"\n]/.test(t) ? `"${t.replace(/"/g, '""')}"` : t;
  }
  function numFmt(n) {
    const x = Number(n || 0);
    try { return x.toLocaleString(); } catch { return String(x); }
  }
  function mmss(total) {
    total = Math.max(0, Number(total || 0));
    const m = Math.floor(total / 60);
    const s = total % 60;
    return `${String(m).padStart(2,"0")}:${String(s).padStart(2,"0")}`;
  }
  function fmtDate(x) {
    // Accepts ISO or mysql timestamp
    const d = new Date(x || "");
    return isNaN(d.getTime()) ? "" : d.toLocaleString();
  }
  function toast(msg) {
    // Minimal inline toast
    const t = document.createElement("div");
    t.textContent = msg;
    t.style.cssText = "position:fixed;left:50%;bottom:24px;transform:translateX(-50%);background:#111624;color:#fff;border:1px solid #2b3244;padding:10px 14px;border-radius:10px;box-shadow:0 10px 24px rgba(0,0,0,.35);z-index:9999;font-weight:600;";
    document.body.appendChild(t);
    setTimeout(() => t.remove(), 1500);
  }
})();
