document.addEventListener("DOMContentLoaded", () => {
  const container         = document.getElementById("sectionCards");
  const searchInput       = document.getElementById("sectionSearch"); 
  const editModal         = document.getElementById("editSectionModal");
  const closeEditBtn      = document.getElementById("closeEditSectionModal");
  const editForm          = document.getElementById("editSectionForm");
  const editSectionId     = document.getElementById("editSectionId");
  const editSectionName   = document.getElementById("editSectionName");
  const editStartYear     = document.getElementById("editStartYear");
  const editEndYear       = document.getElementById("editEndYear");
  const editTeacherSelect = document.getElementById("editTeacherSelect"); 

  const IS_ADMIN = (window.userRole === "ADMIN" || document.body.dataset.role === "ADMIN");

  if (!IS_ADMIN) {
    const stripAdminActions = (root) => {
      const scope = root || container || document;
      const candidates = scope.querySelectorAll('.section-card .btn-edit, .section-card .btn-delete, .section-card button');
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
            stripAdminActions(m.target || container);
            break;
          }
        }
      }).observe(container, { childList: true, subtree: true });
    }
  }

  // Make input uppercase automatically
  if (editSectionName) {
    editSectionName.style.textTransform = "uppercase";
    editSectionName.addEventListener("input", () => {
      const val = editSectionName.value || "";
      const up = val.toUpperCase();
      if (val !== up) editSectionName.value = up;
    });
  }

  // Populate start/end year dropdowns
  if (editStartYear && editEndYear) {
    const thisYear = new Date().getFullYear();
    editStartYear.innerHTML = '<option value="">— Select Year —</option>';
    editEndYear.innerHTML   = '<option value="">— Select Year —</option>';
    for (let y = thisYear - 1; y <= thisYear + 5; y++) {
      editStartYear.add(new Option(y, y));
      editEndYear.add(new Option(y, y));
    }
    editStartYear.addEventListener("change", () => {
      const start = parseInt(editStartYear.value, 10);
      if (!isNaN(start)) {
        const target = (start + 1).toString();
        if ([...editEndYear.options].some(o => o.value === target)) {
          editEndYear.value = target;
        }
      }
    });
  }

  // Close Edit Modal
  if (closeEditBtn && editModal) {
    closeEditBtn.onclick = () => editModal.style.display = "none";
    window.addEventListener("click", e => {
      if (e.target === editModal) editModal.style.display = "none";
    });
  }

  const teachersMap = new Map();
  let teachersLoaded = null;

  function setTeacherDefaultOption(text = "— Unassigned —") {
    if (!editTeacherSelect) return;
    editTeacherSelect.innerHTML = "";
    const o = document.createElement("option");
    o.value = "";
    o.textContent = text;
    editTeacherSelect.appendChild(o);
  }

  function loadTeachersOnce() {
    if (!editTeacherSelect) {
      return Promise.resolve();
    }
    if (teachersLoaded) return teachersLoaded;

    setTeacherDefaultOption();

    teachersLoaded = fetch("index.php?r=api/v1/teachers_list", { headers: { Accept: "application/json" } })
      .then(async (res) => {
        if (!res.ok) throw new Error("HTTP " + res.status);
        const txt = await res.text();
        try {
          return JSON.parse(txt);
        } catch (err) {
          console.warn("teachers_list: JSON parse failed — raw response follows:\n", txt);
          throw new Error("Invalid JSON from teachers_list");
        }
      })
      .then(json => {
        if (!json || json.ok !== true || !Array.isArray(json.data)) {
          console.warn("teachers_list returned unexpected payload:", json);
          return;
        }
        setTeacherDefaultOption();
        json.data.forEach(t => {
          const id = String(t.id || "");
          const fname = (t.first_name || "").trim();
          const lname = (t.last_name || "").trim();
          const username = (t.username || "").trim();
          const display = (fname || lname)
            ? `${(fname + " " + lname).trim()} (${username})`
            : (username || `Teacher ${id}`);
          teachersMap.set(id, display);
          const opt = document.createElement("option");
          opt.value = id;
          opt.textContent = display;
          editTeacherSelect.appendChild(opt);
        });
      })
      .catch(err => {
        console.warn("Could not load teachers list:", err);
        setTeacherDefaultOption("— (unable to load teachers) —");
      });

    return teachersLoaded;
  }

  loadTeachersOnce();

if (editForm) {
  editForm.addEventListener("submit", async (e) => {
    e.preventDefault();

    const payload = {
      section_id:        parseInt(editSectionId.value, 10),
      section_name:      (editSectionName.value || "").trim(),
      start_school_year: editStartYear.value,
      end_school_year:   editEndYear.value
    };

    const teacherSelect = document.getElementById("editTeacherSelect");
    if (teacherSelect) {
      const v = teacherSelect.value;
      payload.teacher_id = v === "" ? "" : v;
    }

    try {
      const res = await fetch("index.php?r=sections/update", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(payload)
      });
      const json = await res.json();
      if (!json || !json.success) {
        throw new Error(json?.error || "Update failed");
      }

      await loadSections();

      if (editModal) editModal.style.display = "none";

    } catch (err) {
      console.error("Update error:", err);
      alert(err.message || "Could not save changes");
    }
  });
}

  const titleCase = (s) => s ? String(s).toLowerCase().replace(/\b([a-z])/g, (m, c) => c.toUpperCase()) : "";
  const resolveTeacherName = (sec) => {
    const fn = sec?.teacher_first_name || sec?.first_name || sec?.adviser_first_name || sec?.handler_first_name || sec?.teacherFname || sec?.adviserFname || "";
    const ln = sec?.teacher_last_name  || sec?.last_name  || sec?.adviser_last_name  || sec?.handler_last_name  || sec?.teacherLname || sec?.adviserLname || "";
    const fnClean = (fn || "").trim();
    const lnClean = (ln || "").trim();
    if (fnClean || lnClean) return `${titleCase(fnClean)} ${titleCase(lnClean)}`.trim();
    const combined = sec?.teacher_name || sec?.adviser_name || sec?.assigned_teacher || sec?.handler_name || "";
    return combined ? titleCase(String(combined).trim()) : null;
  };

let allSections = [];

async function loadSections() {
  if (!container) return;
  container.innerHTML = "<p class='muted'>Loading…</p>";
  try {
    const res = await fetch("api/v1/sections.php", { headers: { "Accept": "application/json" } });
    if (!res.ok) throw new Error("HTTP " + res.status);
    const data = await res.json();
    if (!data || !data.success) throw new Error(data?.message || "Load failed");
    allSections = Array.isArray(data.sections) ? data.sections : [];
    renderSections(allSections);
  } catch (err) {
    console.error("Could not load sections:", err);
    if (container) container.innerHTML = "<p class='error'>Could not load sections.</p>";
  }
}

loadSections();

  function renderSections(list) {
    if (!container) return;
    container.innerHTML = "";

    if (!Array.isArray(list) || list.length === 0) {
      const msg = document.createElement("p");
      msg.className = "no-results";
      msg.textContent = "Sorry, no search found.";
      container.appendChild(msg);
      return;
    }

    list.forEach(sec => {
      const card = document.createElement("div");
      card.className = "section-card";
      card.dataset.id = String(sec.id ?? "");

      const h2 = document.createElement("h2");
      h2.textContent = (sec.section_name || "").toUpperCase();
      card.appendChild(h2);

      const years = document.createElement("p");
      years.className = "section-years";
      years.textContent = `${sec.start_school_year || ""} – ${sec.end_school_year || ""}`;
      card.appendChild(years);

      const teacherName = resolveTeacherName(sec);
      const t = document.createElement("p");
      t.className = "section-teacher";
      t.textContent = teacherName ? `TEACHER: ${teacherName}` : "TEACHER: N/A";
      card.appendChild(t);

      const actions = document.createElement("div");
      actions.className = "card-actions";

      const viewBtn = document.createElement("button");
      viewBtn.textContent = "View";
      viewBtn.className = "btn-view";
      viewBtn.onclick = () => {
        location.href = `index.php?r=sections/detail&section_id=${encodeURIComponent(sec.id)}`;
      };
      actions.appendChild(viewBtn);

      if (IS_ADMIN) {
        const editBtn = document.createElement("button");
        editBtn.textContent = "Edit";
        editBtn.className = "btn-edit edit-section-btn"; 
        editBtn.setAttribute("data-section-id", String(sec.id ?? ""));
        if (sec.teacher_id) editBtn.setAttribute("data-teacher-id", String(sec.teacher_id));
        if (sec.assigned_teacher_id) editBtn.setAttribute("data-teacher-id", String(sec.assigned_teacher_id));
        if (sec.adviser_id) editBtn.setAttribute("data-teacher-id", String(sec.adviser_id));

        editBtn.onclick = () => {
          if (!editModal) return;
          editSectionId.value      = sec.id ?? "";
          editSectionName.value    = sec.section_name ?? "";
          editStartYear.value      = sec.start_school_year ?? "";
          editEndYear.value        = sec.end_school_year ?? "";
          loadTeachersOnce().then(() => {
            const tid = String(sec.teacher_id ?? sec.assigned_teacher_id ?? sec.adviser_id ?? sec.teacher_id ?? "");
            if (tid && Array.from((editTeacherSelect || {options:[]}).options).some(o => o.value === tid)) {
              editTeacherSelect.value = tid;
            } else {
              editTeacherSelect.value = "";
            }
          }).catch(() => {
            if (editTeacherSelect) editTeacherSelect.value = "";
          });

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

    adjustContainerHeight();

    if (!IS_ADMIN) {
      const stripOnce = () => {
        const candidates = container.querySelectorAll('.section-card button');
        candidates.forEach((el) => {
          const label = (el.textContent || '').trim().toLowerCase();
          if (label === 'edit' || label === 'delete') el.remove();
        });
      };
      stripOnce();
    }
  }

  const deleteModal = document.getElementById("deleteSectionModal");
  let deleteTargetId = null;
  let deleteCardRef = null;

  window.showDeleteModal = (id, name, cardRef) => {
    deleteTargetId = id;
    deleteCardRef = cardRef;
    const modal = deleteModal;
    if (!modal) return;
    modal.style.display = "block";
    const p = modal.querySelector("p");
    if (p) p.textContent = `Are you sure you want to delete class section "${name}"?`;
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
      if (!json.success) throw new Error(json.error || "Deletion failed.");
      if (deleteCardRef && deleteCardRef.remove) deleteCardRef.remove();
      closeDeleteModal();
      adjustContainerHeight();
    })
    .catch(err => {
      alert(err.message || "Could not delete section.");
      closeDeleteModal();
    });
  };

  function adjustContainerHeight() {
    if (!container) return;
    container.style.height = "auto";
  }

  window.addEventListener("resize", adjustContainerHeight);

  function debounce(fn, delay = 200) {
    let t = null;
    return function (...args) {
      clearTimeout(t);
      t = setTimeout(() => fn.apply(this, args), delay);
    };
  }

  if (searchInput) {
    const doSearch = () => {
      const raw = (searchInput.value || "").trim().toLowerCase();
      if (!raw) {
        renderSections(allSections);
        return;
      }
      const query = raw.replace(/\s+/g, " "); 
      const filtered = allSections.filter(sec => {
        const teacher = (resolveTeacherName(sec) || "").toLowerCase();
        const name = (sec.section_name || "").toLowerCase();
        const startYear = String(sec.start_school_year || "").toLowerCase();
        const endYear = String(sec.end_school_year || "").toLowerCase();
        const combinedYears = `${startYear} – ${endYear}`.toLowerCase(); 
        const altCombined = `${startYear}-${endYear}`.toLowerCase();
        return (
          name.includes(query) ||
          teacher.includes(query) ||
          startYear.includes(query) ||
          endYear.includes(query) ||
          combinedYears.includes(query) ||
          altCombined.includes(query)
        );
      });
      renderSections(filtered);
    };
    searchInput.addEventListener("input", debounce(doSearch, 180));
  }
});
