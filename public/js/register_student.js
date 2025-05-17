// public/js/register_student.js

document.addEventListener("DOMContentLoaded", () => {
  // Only the three name inputs exist now
  const givenName  = document.querySelector('input[name="given_name"]');
  const middleName = document.querySelector('input[name="middle_name"]');
  const lastName   = document.querySelector('input[name="last_name"]');
  const sectionSel = document.getElementById("section_id");
  const toggle     = document.getElementById("togglePassword");
  const pwInput    = document.getElementById("password");

  // Uppercase only the name fields
  [givenName, middleName, lastName].forEach(input => {
    if (!input) return;
    input.addEventListener("input", () => {
      input.value = input.value.toUpperCase();
    });
  });

  // Toggle password visibility
  if (toggle && pwInput) {
    toggle.addEventListener("click", () => {
      const isPwd = pwInput.type === "password";
      pwInput.type = isPwd ? "text" : "password";
      toggle.textContent = isPwd ? "Hide" : "Show";
    });
  }

  // Populate the Section dropdown
  if (sectionSel) {
    fetch("/api/v1/sections.php", { headers: { "Accept":"application/json" } })
      .then(res => {
        if (!res.ok) throw new Error(`HTTP ${res.status}`);
        return res.json();
      })
      .then(json => {
        if (!json.success) throw new Error(json.message || "API error");
        sectionSel.innerHTML = '<option value="">Select section…</option>';
        json.sections.forEach(sec => {
          const opt = document.createElement("option");
          opt.value       = sec.id;
          opt.textContent = `${sec.section_name} (${sec.start_school_year}–${sec.end_school_year})`;
          sectionSel.appendChild(opt);
        });
      })
      .catch(err => {
        console.error("Error loading sections:", err);
        sectionSel.innerHTML = '<option value="">Unable to load sections</option>';
      });
  }
});
