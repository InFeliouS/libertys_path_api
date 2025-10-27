// public/js/dashboard_cards.js
document.addEventListener("DOMContentLoaded", () => {
  const container         = document.getElementById("sectionCards");
  const editModal         = document.getElementById("editSectionModal");
  const closeEditBtn      = document.getElementById("closeEditSectionModal");
  const editForm          = document.getElementById("editSectionForm");
  const editSectionId     = document.getElementById("editSectionId");
  const editSectionName   = document.getElementById("editSectionName");
  const editStartYear     = document.getElementById("editStartYear");
  const editEndYear       = document.getElementById("editEndYear");

  // --- ROLE DETECTION (no HTML changes needed) ---
  const IS_ADMIN = !!document.querySelector('.div_sidebar_left .top-control');

  // --- OPTIONAL SAFETY NET: if Teacher, strip Edit/Delete by label, now and on future renders
  if (!IS_ADMIN) {
    const stripAdminActions = (root) => {
      const scope = root || container || document;
      const candidates = scope.querySelectorAll('#sectionCards button, #sectionCards a, #sectionCards .btn, #sectionCards .button');
      candidates.forEach((el) => {
        const label = (el.textContent || '').trim().toLowerCase();
        if (label === 'edit' || label === 'delete') el.remove();
      });
    };
    stripAdminActions();
    if (container) {
      new MutationObserver((muts) => {
        for (const m of muts) {
          if (m.addedNodes && m.addedNodes.length) {
            stripAdminActions();
            break;
          }
        }
      }).observe(container, { childList: true, subtree: true });
    }
  }

  // 1) Force uppercase on Section Name in modal
  if (editSectionName) {
    editSectionName.style.textTransform = "uppercase";
    editSectionName.addEventListener("input", () => {
      editSectionName.value = editSectionName.value.toUpperCase();
    });
  }

  // 2) Populate year dropdowns
  if (editStartYear && editEndYear) {
    const thisYear = new Date().getFullYear();
    for (let y = thisYear - 1; y <= thisYear + 5; y++) {
      editStartYear.add(new Option(y, y));
      editEndYear.add(new Option(y, y));
    }
    // 3) Auto-advance end year when start changes
    editStartYear.addEventListener("change", () => {
      const start = parseInt(editStartYear.value, 10);
      const target = (start + 1).toString();
      if ([...editEndYear.options].some(o => o.value === target)) {
        editEndYear.value = target;
      }
    });
  }

  // 4) Modal open/close handlers
  if (closeEditBtn && editModal) {
    closeEditBtn.onclick = () => editModal.style.display = "none";
    window.addEventListener("click", e => {
      if (e.target === editModal) editModal.style.display = "none";
    });
  }

  // 5) Handle edit form submission (admin-only usage; safe to keep defined)
  if (editForm) {
    editForm.addEventListener("submit", e => {
      e.preventDefault();
      const payload = {
        section_id:        parseInt(editSectionId.value, 10),
        section_name:      editSectionName.value.trim(),
        start_school_year: editStartYear.value,
        end_school_year:   editEndYear.value
      };

      fetch("index.php?r=sections/update", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(payload)
      })
      .then(r => r.json())
      .then(json => {
        if (!json.success) throw new Error(json.error || "Update failed");
        // reflect changes on the card
        const card = document.querySelector(`.section-card[data-id="${payload.section_id}"]`);
        if (card) {
          const titleEl = card.querySelector("h2");
          const yearsEl = card.querySelector("p.section-years");
          if (titleEl) titleEl.textContent = payload.section_name.toUpperCase();
          if (yearsEl) yearsEl.textContent = `${payload.start_school_year} – ${payload.end_school_year}`;
        }
        if (editModal) editModal.style.display = "none";
      })
      .catch(err => alert(err.message));
    });
  }

  // Small utilities for teacher name formatting
  const titleCase = (s) => {
    if (!s) return "";
    return String(s)
      .toLowerCase()
      .replace(/\b([a-z])/g, (m, c) => c.toUpperCase());
  };
  const resolveTeacherName = (sec) => {
    // Try common field patterns coming from typical APIs
    const fn =
      sec.teacher_first_name ??
      sec.first_name ??
      sec.adviser_first_name ??
      sec.handler_first_name ??
      sec.teacherFname ??
      sec.adviserFname ??
      null;

    const ln =
      sec.teacher_last_name ??
      sec.last_name ??
      sec.adviser_last_name ??
      sec.handler_last_name ??
      sec.teacherLname ??
      sec.adviserLname ??
      null;

    if (fn || ln) {
      return `${titleCase(fn || "")} ${titleCase(ln || "")}`.trim();
    }

    // Single combined name fallbacks
    const combined =
      sec.teacher_name ??
      sec.adviser_name ??
      sec.assigned_teacher ??
      sec.handler_name ??
      null;

    return combined ? titleCase(combined) : null;
  };

  // 6) Fetch & render all section cards
  fetch("api/v1/sections.php", { headers: { "Accept":"application/json" } })
    .then(r => r.json())
    .then(data => {
      if (!data.success) throw new Error(data.message || "Load failed");
      if (!container) return;

      container.innerHTML = ""; // clear “Loading…”
      data.sections.forEach(sec => {
        const card = document.createElement("div");
        card.className = "section-card";
        card.dataset.id = sec.id;

        // Title
        const h2 = document.createElement("h2");
        h2.textContent = (sec.section_name || "").toUpperCase();
        card.appendChild(h2);

        // Years
        const years = document.createElement("p");
        years.className = "section-years";
        years.textContent = `${sec.start_school_year} – ${sec.end_school_year}`;
        card.appendChild(years);

        // TEACHER INDICATOR (FirstName + LastName)
        const teacherName = resolveTeacherName(sec);
        const t = document.createElement("p");
        t.className = "section-teacher";
        t.textContent = teacherName ? `Handled by: ${teacherName}` : "Handled by: (No assigned teacher)";
        card.appendChild(t);

        // View (visible to all)
        const viewBtn = document.createElement("button");
        viewBtn.textContent = "View Students";
        viewBtn.onclick = () =>
          location.href = `index.php?r=sections/detail&section_id=${sec.id}`;
        card.appendChild(viewBtn);

        // Admin-only buttons
        if (IS_ADMIN) {
          // Edit
          const editBtn = document.createElement("button");
          editBtn.textContent = "Edit";
          editBtn.className = "btn-edit";
          editBtn.onclick = () => {
            if (!editModal) return;
            editSectionId.value      = sec.id;
            editSectionName.value    = sec.section_name;
            editStartYear.value      = sec.start_school_year;
            editEndYear.value        = sec.end_school_year;
            editModal.style.display  = "flex";
          };
          card.appendChild(editBtn);

          // Delete
          const delBtn = document.createElement("button");
          delBtn.textContent = "Delete";
          delBtn.className = "btn-delete";
          delBtn.onclick = () => {
            if (!confirm(
              `Delete section "${sec.section_name}"?\nAll students in it will be removed.`
            )) return;

            fetch("index.php?r=sections/deleteSection", {
              method: "POST",
              headers: { "Content-Type":"application/json" },
              body: JSON.stringify({ section_id: sec.id })
            })
            .then(r => r.json())
            .then(json => {
              if (!json.success) throw new Error(json.error || "Deletion failed");
              card.remove();
            })
            .catch(err => alert(err.message));
          };
          card.appendChild(delBtn);
        }

        container.appendChild(card);
      });
    })
    .catch(err => {
      console.error(err);
      if (container) container.innerHTML = "<p class='error'>Could not load sections.</p>";
    });
});

