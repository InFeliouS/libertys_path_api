// public/js/create_section_form.js

document.addEventListener('DOMContentLoaded', () => {
  const sectionInput = document.getElementById('section_name');
  const startSelect  = document.getElementById('start_school_year');
  const endSelect    = document.getElementById('end_school_year');

  // Force uppercase on section name
  sectionInput.style.textTransform = 'uppercase';
  sectionInput.addEventListener('input', () => {
    sectionInput.value = sectionInput.value.toUpperCase();
  });

  // Populate year dropdowns
  const thisYear = new Date().getFullYear();
  for (let y = thisYear; y <= thisYear + 5; y++) {
    const o1 = document.createElement('option');
    o1.value = y;
    o1.textContent = y;
    startSelect.appendChild(o1);

    const o2 = document.createElement('option');
    o2.value = y;
    o2.textContent = y;
    endSelect.appendChild(o2);
  }

  // Auto-set end year to start+1 when start changes
  startSelect.addEventListener('change', () => {
    const start = parseInt(startSelect.value, 10);
    const target = (start + 1).toString();
    if ([...endSelect.options].some(o => o.value === target)) {
      endSelect.value = target;
    }
  });
});
