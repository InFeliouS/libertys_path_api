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

  // 1) Force uppercase on Section Name in modal
  editSectionName.style.textTransform = "uppercase";
  editSectionName.addEventListener("input", () => {
    editSectionName.value = editSectionName.value.toUpperCase();
  });

  // 2) Populate year dropdowns
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

  // 4) Modal open/close handlers
  closeEditBtn.onclick = () => editModal.style.display = "none";
  window.addEventListener("click", e => {
    if (e.target === editModal) editModal.style.display = "none";
  });

  // 5) Handle edit form submission
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
      const card = document.querySelector(
        `.section-card[data-id="${payload.section_id}"]`
      );
      card.querySelector("h2").textContent = payload.section_name.toUpperCase();
      card.querySelector("p").textContent  =
        `${payload.start_school_year} – ${payload.end_school_year}`;
      editModal.style.display = "none";
    })
    .catch(err => alert(err.message));
  });

  // 6) Fetch & render all section cards
  fetch("api/v1/sections.php", { headers: { "Accept":"application/json" } })
    .then(r => r.json())
    .then(data => {
      if (!data.success) throw new Error(data.message || "Load failed");
      container.innerHTML = ""; // clear “Loading…”
      data.sections.forEach(sec => {
        const card = document.createElement("div");
        card.className = "section-card";
        card.dataset.id = sec.id;

        // Title
        const h2 = document.createElement("h2");
        h2.textContent = sec.section_name.toUpperCase();
        card.appendChild(h2);

        // Years
        const p = document.createElement("p");
        p.textContent = `${sec.start_school_year} – ${sec.end_school_year}`;
        card.appendChild(p);

        // View
        const viewBtn = document.createElement("button");
        viewBtn.textContent = "View Students";
        viewBtn.onclick = () =>
          location.href = `index.php?r=sections/detail&section_id=${sec.id}`;
        card.appendChild(viewBtn);

        // Edit
        const editBtn = document.createElement("button");
        editBtn.textContent = "Edit";
        editBtn.onclick = () => {
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

        container.appendChild(card);
      });
    })
    .catch(err => {
      console.error(err);
      container.innerHTML = "<p class='error'>Could not load sections.</p>";
    });
});
