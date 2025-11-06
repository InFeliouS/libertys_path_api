// js/teachers_view.js
(function () {
  document.addEventListener("DOMContentLoaded", () => {
    const content = document.querySelector(".content-pane");
    if (!content) {
      console.warn("teachers_view: .content-pane not found");
      return;
    }

    // Modal elements present in teachers_view.html
    const editModal = document.getElementById("teacherEditModal");
    const editClose = document.getElementById("teacherEditClose");
    const editCancel = document.getElementById("teacherEditCancel");
    const editForm = document.getElementById("teacherEditForm");
    const fldId = document.getElementById("edit_teacher_id");
    const fldUsername = document.getElementById("edit_username");
    const fldFirst = document.getElementById("edit_first_name");
    const fldLast = document.getElementById("edit_last_name");
    const fldPassword = document.getElementById("edit_password");

    // Create (or reuse) a wrapper for the teachers table so we don't disturb other content
    let tableWrap = content.querySelector(".tv-table-wrap");
    if (!tableWrap) {
      tableWrap = document.createElement("div");
      tableWrap.className = "tv-table-wrap";
      // Insert at top of content pane, but don't remove other content
      content.insertBefore(tableWrap, content.firstChild);
    }

    // helpers to open/close modal
    function openEditModal() {
      if (editModal) editModal.style.display = "block";
    }
    function closeEditModal() {
      if (editModal) editModal.style.display = "none";
      if (fldPassword) fldPassword.value = "";
    }

    // wire modal close / cancel
    if (editClose) editClose.addEventListener("click", closeEditModal);
    if (editCancel) editCancel.addEventListener("click", closeEditModal);
    window.addEventListener("click", (ev) => {
      if (ev.target === editModal) closeEditModal();
    });

    // Utility: escape HTML for safe insertion
    function escapeHtml(s) {
      return String(s || "")
        .replaceAll("&", "&amp;")
        .replaceAll("<", "&lt;")
        .replaceAll(">", "&gt;")
        .replaceAll('"', "&quot;")
        .replaceAll("'", "&#039;");
    }

    // Load teachers via API and render table
    async function loadTeachers() {
      tableWrap.innerHTML = `<div class="tv-loading">Loading teachers…</div>`;
      try {
        const res = await fetch("index.php?r=api/v1/teachers_list", {
          headers: { Accept: "application/json" },
          credentials: "same-origin"
        });
        if (!res.ok) throw new Error("HTTP " + res.status);
        const json = await res.json();

        // Some endpoints use ok/data or success/sections — handle both patterns
        const data =
          Array.isArray(json?.data) ? json.data
          : Array.isArray(json?.teachers) ? json.teachers
          : Array.isArray(json?.rows) ? json.rows
          : [];

        // Only include non-admin rows (defensive)
        const rows = data.filter((r) => String((r.role || "")).toUpperCase() !== "ADMIN");

        renderTable(rows);
      } catch (err) {
        console.error("Could not load teachers:", err);
        tableWrap.innerHTML = `<div class="tv-error">Could not load teachers. Check console.</div>`;
      }
    }

    // Render toolbar + table
    function renderTable(rows) {
      const header = `
        <div class="tv-toolbar" style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;">
          <h2 style="margin:0">Teachers</h2>
          <div>
            <button id="tvReloadBtn" class="btn small">Reload</button>
          </div>
        </div>
      `;

      const tableHead = `
        <table class="tv-table" role="grid" aria-live="polite" style="width:100%;border-collapse:collapse;">
          <thead>
            <tr style="background:#A35905;color:#fff;">
              <th style="padding:10px 12px;border-radius:6px 6px 0 0;">#</th>
              <th style="padding:10px 12px;">Username</th>
              <th style="padding:10px 12px;">First name</th>
              <th style="padding:10px 12px;">Last name</th>
              <th style="padding:10px 12px;">Role</th>
              <th style="padding:10px 12px;">Actions</th>
            </tr>
          </thead>
          <tbody>
            ${rows.length === 0 ? `<tr><td colspan="6" style="padding:18px;text-align:center;">No teachers found.</td></tr>` : rows.map((r, i) => {
              const id = Number(r.id || 0);
              // store full teacher object on the edit button for reliable retrieval
              const teacherData = encodeURIComponent(JSON.stringify({
                id: id,
                username: r.username || "",
                first_name: r.first_name || "",
                last_name: r.last_name || "",
                role: r.role || ""
              }));
              return `
                <tr data-id="${id}" style="border-bottom:1px solid rgba(0,0,0,0.06);">
                  <td class="mono" style="padding:12px 10px;">${i + 1}</td>
                  <td class="td-username" style="padding:12px 10px;">${escapeHtml(r.username || "")}</td>
                  <td style="padding:12px 10px;">${escapeHtml(r.first_name || "")}</td>
                  <td style="padding:12px 10px;">${escapeHtml(r.last_name || "")}</td>
                  <td style="padding:12px 10px;">${escapeHtml(r.role || "")}</td>
                  <td class="tv-actions-col" style="padding:12px 10px;">
                    <button class="btn btn-edit" data-id="${id}" data-teacher="${teacherData}" style="margin-right:8px;">Edit</button>
                    <button class="btn btn-delete" data-id="${id}">Delete</button>
                  </td>
                </tr>
              `;
            }).join("")}
          </tbody>
        </table>
      `;

      tableWrap.innerHTML = header + tableHead;

      // Wire reload
      const reloadBtn = tableWrap.querySelector("#tvReloadBtn");
      if (reloadBtn) reloadBtn.addEventListener("click", loadTeachers);

      // Wire edit buttons - use e.currentTarget and data-teacher first (most reliable)
      tableWrap.querySelectorAll(".btn-edit").forEach((btn) => {
        btn.addEventListener("click", async (e) => {
          const current = e.currentTarget;
          // prefer full teacher object stored on button
          let teacher = null;
          try {
            if (current.dataset && current.dataset.teacher) {
              teacher = JSON.parse(decodeURIComponent(current.dataset.teacher));
            }
          } catch (err) {
            console.debug("Failed to parse data-teacher, will fallback to row parsing", err);
            teacher = null;
          }

          // If teacher object available, fill from it
          if (teacher && teacher.id) {
            fldId.value = Number(teacher.id);
            fldUsername.value = teacher.username || "";
            fldFirst.value = teacher.first_name || "";
            fldLast.value = teacher.last_name || "";
            if (fldPassword) fldPassword.value = "";
            openEditModal();
            return;
          }

          // fallback: use the row's data (legacy)
          const id = Number(current.getAttribute("data-id") || 0);
          if (!id) return;
          const tr = tableWrap.querySelector(`tr[data-id="${id}"]`);
          if (tr) {
            fldId.value = id;
            fldUsername.value = tr.querySelector(".td-username").textContent.trim() || "";
            fldFirst.value = tr.children[2].textContent.trim() || "";
            fldLast.value = tr.children[3].textContent.trim() || "";
            if (fldPassword) fldPassword.value = "";
            openEditModal();
            return;
          }

          // final fallback: request single teacher record (if available)
          try {
            const r = await fetch(`index.php?r=api/v1/teacher&teacher_id=${encodeURIComponent(id)}`, {
              headers: { Accept: "application/json" },
              credentials: "same-origin"
            });
            if (r.ok) {
              const j = await r.json();
              fldId.value = j.id || id;
              fldUsername.value = j.username || "";
              fldFirst.value = j.first_name || "";
              fldLast.value = j.last_name || "";
            } else {
              fldId.value = id;
            }
          } catch (ex) {
            fldId.value = id;
          }
          openEditModal();
        });
      });

      // Wire delete buttons
      tableWrap.querySelectorAll(".btn-delete").forEach((btn) => {
        btn.addEventListener("click", async () => {
          const id = Number(btn.getAttribute("data-id") || 0);
          if (!id) return;
          if (!confirm("Delete this teacher? This cannot be undone.")) return;
          try {
            const res = await fetch("index.php?r=teachers/delete", {
              method: "POST",
              headers: { "Content-Type": "application/json", Accept: "application/json" },
              credentials: "same-origin",
              body: JSON.stringify({ teacher_id: id }),
            });
            if (!res.ok) {
              const txt = await res.text();
              throw new Error(txt || "Delete failed");
            }
            const json = await res.json();
            if (!json.success) throw new Error(json.error || "Delete failed");
            // refresh table after deletion
            await loadTeachers();
            // small non-blocking notice (you can replace with toast)
            console.info("Teacher deleted:", id);
          } catch (err) {
            console.error("Delete error:", err);
            alert("Error deleting teacher: " + (err.message || err));
          }
        });
      });
    }

    // Edit form submit: POST JSON to teachers/update and refresh table
    if (editForm) {
      editForm.addEventListener("submit", async (ev) => {
        ev.preventDefault();
        const id = Number(fldId.value || 0);
        if (!id) return alert("Missing teacher id.");

        const payload = {
          teacher_id: id,
          username: (fldUsername.value || "").trim(),
          first_name: (fldFirst.value || "").trim(),
          last_name: (fldLast.value || "").trim(),
        };
        const pw = (fldPassword.value || "").trim();
        if (pw) payload.password = pw;

        // debug: show payload before sending (remove in production if you want)
        console.debug("Updating teacher payload:", payload);

        try {
          const res = await fetch("index.php?r=teachers/update", {
            method: "POST",
            headers: { "Content-Type": "application/json", Accept: "application/json" },
            credentials: "same-origin",
            body: JSON.stringify(payload),
          });
          if (!res.ok) {
            const txt = await res.text();
            throw new Error(txt || "Update failed");
          }
          const json = await res.json();
          if (!json.success) throw new Error(json.error || "Update failed");

          // refresh table and close modal
          await loadTeachers();
          closeEditModal();
          console.info("Teacher updated.", json.teacher || id);
        } catch (err) {
          console.error("Update error:", err);
          alert("Error updating teacher: " + (err.message || err));
        }
      });
    }

    // initial load
    loadTeachers();
  });
})();
