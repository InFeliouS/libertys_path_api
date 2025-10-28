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

  // --- Role detection
  const IS_ADMIN = !!document.querySelector('.div_sidebar_left .top-control');

  // --- If Teacher, strip Edit/Delete
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

  // Force uppercase section name
  if (editSectionName) {
    editSectionName.style.textTransform = "uppercase";
    editSectionName.addEventListener("input", () => {
      editSectionName.value = editSectionName.value.toUpperCase();
    });
  }

  // Populate dropdowns
  if (editStartYear && editEndYear) {
    const thisYear = new Date().getFullYear();
    for (let y = thisYear - 1; y <= thisYear + 5; y++) {
      editStartYear.add(new Option(y, y));
      editEndYear.add(new Option(y, y));
    }
    editStartYear.addEventListener("change", () => {
      const start = parseInt(editStartYear.value, 10);
      const target = (start + 1).toString();
      if ([...editEndYear.options].some(o => o.value === target)) {
        editEndYear.value = target;
      }
    });
  }

  // Edit modal close logic
  if (closeEditBtn && editModal) {
    closeEditBtn.onclick = () => editModal.style.display = "none";
    window.addEventListener("click", e => {
      if (e.target === editModal) editModal.style.display = "none";
    });
  }

  // Edit form submit
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

  // Utility functions
  const titleCase = (s) => s ? String(s).toLowerCase().replace(/\b([a-z])/g, (m, c) => c.toUpperCase()) : "";
  const resolveTeacherName = (sec) => {
    const fn = sec.teacher_first_name ?? sec.first_name ?? sec.adviser_first_name ?? sec.handler_first_name ?? sec.teacherFname ?? sec.adviserFname ?? null;
    const ln = sec.teacher_last_name ?? sec.last_name ?? sec.adviser_last_name ?? sec.handler_last_name ?? sec.teacherLname ?? sec.adviserLname ?? null;
    if (fn || ln) return `${titleCase(fn || "")} ${titleCase(ln || "")}`.trim();
    const combined = sec.teacher_name ?? sec.adviser_name ?? sec.assigned_teacher ?? sec.handler_name ?? null;
    return combined ? titleCase(combined) : null;
  };

  // Fetch and render all section cards
  fetch("api/v1/sections.php", { headers: { "Accept": "application/json" } })
    .then(r => r.json())
    .then(data => {
      if (!data.success) throw new Error(data.message || "Load failed");
      if (!container) return;

      container.innerHTML = "";
      data.sections.forEach(sec => {
        const card = document.createElement("div");
        card.className = "section-card";
        card.dataset.id = sec.id;

        const h2 = document.createElement("h2");
        h2.textContent = (sec.section_name || "").toUpperCase();
        card.appendChild(h2);

        const years = document.createElement("p");
        years.className = "section-years";
        years.textContent = `${sec.start_school_year} – ${sec.end_school_year}`;
        card.appendChild(years);

        const teacherName = resolveTeacherName(sec);
        const t = document.createElement("p");
        t.className = "section-teacher";
        t.textContent = teacherName ? `TEACHER: ${teacherName}` : "TEACHER: N/A";
        card.appendChild(t);

        const actions = document.createElement("div");
        actions.className = "card-actions";

        const viewBtn = document.createElement("button");
        viewBtn.textContent = "View Students";
        viewBtn.className = "btn-view";
        viewBtn.onclick = () => location.href = `index.php?r=sections/detail&section_id=${sec.id}`;
        actions.appendChild(viewBtn);

        if (IS_ADMIN) {
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
          actions.appendChild(editBtn);

          const delBtn = document.createElement("button");
          delBtn.textContent = "Delete";
          delBtn.className = "btn-delete";
          delBtn.onclick = () => showDeleteModal(sec.id, sec.section_name, card);
          actions.appendChild(delBtn);
        }

        card.appendChild(actions);
        container.appendChild(card);
      });
    })
    .catch(err => {
      console.error(err);
      if (container) container.innerHTML = "<p class='error'>Could not load sections.</p>";
    });

  // ===== Delete Modal Logic =====
  const deleteModal = document.getElementById("deleteSectionModal");
  let deleteTargetId = null;
  let deleteCardRef = null;

  window.showDeleteModal = (id, name, cardRef) => {
    deleteTargetId = id;
    deleteCardRef = cardRef;
    const modal = deleteModal;
    if (!modal) return;
    modal.style.display = "block";
    modal.querySelector("p").textContent = `Are you sure you want to delete section "${name}"?`;
  };

  window.closeDeleteModal = () => {
    if (deleteModal) deleteModal.style.display = "none";
    deleteTargetId = null;
    deleteCardRef = null;
  };

  window.confirmDeleteSection = () => {
    if (!deleteTargetId) return;
    fetch("index.php?r=sections/deleteSection", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ section_id: deleteTargetId })
    })
    .then(r => r.json())
    .then(json => {
      if (!json.success) throw new Error(json.error || "Deletion failed");
      if (deleteCardRef) deleteCardRef.remove();
      closeDeleteModal();
    })
    .catch(err => {
      alert(err.message);
      closeDeleteModal();
    });
  };
});
