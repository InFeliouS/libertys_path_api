/* create_section_form.js
   - Populates year dropdowns
   - Validates empty fields
   - Shows red "Required" badge INSIDE the input/select when invalid
   - Removes badge when corrected
*/

(function () {
  const form = document.getElementById("create-section-form");

  const sectionInput = document.getElementById("section_name");
  const startSelect = document.getElementById("start_school_year");
  const endSelect = document.getElementById("end_school_year");

  const errorSection = document.getElementById("error-section_name");
  const errorStart = document.getElementById("error-start_school_year");
  const errorEnd = document.getElementById("error-end_school_year");

  /* -------------------------------
      POPULATE YEAR SELECTS
      NOTE: end range is intentionally one year higher than start range
      so that End = Start + 1 is always available.
  --------------------------------*/
  function populateYears() {
    const now = new Date();
    const thisYear = now.getFullYear();
    const startMin = thisYear;
    const startMax = thisYear + 5;
    const endMax = startMax + 1; // end will range up to one year higher than startMax

    // helper to (re)build start options
    function buildStartOptions() {
      startSelect.innerHTML = '<option value="">— Select Year —</option>';
      for (let y = startMin; y <= startMax; y++) {
        const optStart = document.createElement("option");
        optStart.value = String(y);
        optStart.textContent = String(y);
        startSelect.appendChild(optStart);
      }
      startSelect.value = "";
    }

    // helper to build end options from a minimum year (inclusive)
    function buildEndOptions(minYear) {
      endSelect.innerHTML = '<option value="">— Select Year —</option>';
      const min = Number(minYear);
      // ensure we start at least from thisYear+1 if minYear is smaller/invalid
      const start = Number.isFinite(min) && min > thisYear ? min : thisYear + 1;
      for (let y = start; y <= endMax; y++) {
        const optEnd = document.createElement("option");
        optEnd.value = String(y);
        optEnd.textContent = String(y);
        endSelect.appendChild(optEnd);
      }
      endSelect.value = "";
    }

    // initial build
    buildStartOptions();
    buildEndOptions(thisYear + 1);

    // start with end disabled until a start year is chosen
    endSelect.disabled = true;

    // when start changes: rebuild end options starting at start+1, then set end = start+1
    startSelect.addEventListener("change", () => {
      clearInvalid(startSelect.parentElement, errorStart);

      const s = parseInt(startSelect.value, 10);
      if (isNaN(s)) {
        // reset end to default range and disable
        buildEndOptions(thisYear + 1);
        endSelect.disabled = true;
        return;
      }

      const minEnd = s + 1;
      buildEndOptions(minEnd);
      endSelect.disabled = false;

      // auto-select start+1
      if ([...endSelect.options].some((o) => o.value === String(minEnd))) {
        endSelect.value = String(minEnd);
        clearInvalid(endSelect.parentElement, errorEnd);
      } else {
        // fallback: pick first numeric option
        if (endSelect.options.length > 1) endSelect.selectedIndex = 1;
      }
    });
  }

  /* -------------------------------
      ADD INVALID STYLE
  --------------------------------*/
  function markInvalid(inputWrapperEl, errorEl, message) {
    inputWrapperEl.classList.add("invalid");
    errorEl.textContent = message;
    errorEl.classList.add("visible");
  }

  /* -------------------------------
      CLEAR INVALID STYLE
  --------------------------------*/
  function clearInvalid(inputWrapperEl, errorEl) {
    inputWrapperEl.classList.remove("invalid");
    errorEl.textContent = "";
    errorEl.classList.remove("visible");
  }

  /* -------------------------------
      VALIDATION FUNCTION
  --------------------------------*/
  function validate() {
    let valid = true;

    const sectionWrapper = sectionInput.parentElement;
    const startWrapper = startSelect.parentElement;
    const endWrapper = endSelect.parentElement;

    // SECTION NAME
    if (sectionInput.value.trim() === "") {
      markInvalid(
        sectionWrapper,
        errorSection,
        "Please fill in the box above."
      );
      valid = false;
    } else {
      clearInvalid(sectionWrapper, errorSection);
    }

    // START YEAR
    if (startSelect.value.trim() === "") {
      markInvalid(startWrapper, errorStart, "Please select a start year.");
      valid = false;
    } else {
      clearInvalid(startWrapper, errorStart);
    }

    // END YEAR
    if (endSelect.value.trim() === "") {
      markInvalid(endWrapper, errorEnd, "Please select an end year.");
      valid = false;
    } else {
      clearInvalid(endWrapper, errorEnd);
    }

    // YEAR LOGIC VALIDATION
    if (startSelect.value && endSelect.value) {
      const start = Number(startSelect.value);
      const end = Number(endSelect.value);

      if (end !== start + 1) {
        markInvalid(endWrapper, errorEnd, "End year must be Start year + 1.");
        valid = false;
      }
    }

    return valid;
  }

  /* -------------------------------
      EVENT LISTENERS TO REMOVE BADGE WHEN TYPING/CHANGING
  --------------------------------*/
  sectionInput.addEventListener("input", () => {
    const wrapper = sectionInput.parentElement;
    clearInvalid(wrapper, errorSection);

    // Keep uppercase
    const pos = sectionInput.selectionStart;
    sectionInput.value = sectionInput.value.toUpperCase();
    sectionInput.setSelectionRange(pos, pos);
  });

  endSelect.addEventListener("change", () => {
    clearInvalid(endSelect.parentElement, errorEnd);
  });

  /* -------------------------------
      FORM SUBMIT
  --------------------------------*/
  form.addEventListener("submit", function (e) {
    if (!validate()) {
      e.preventDefault();
      return false;
    }
  });

  /* -------------------------------
      INITIALIZE
  --------------------------------*/
  populateYears();
})();

// AUTO-GENERATE 4-letter SECTION CODE on page open
document.addEventListener("DOMContentLoaded", () => {
  const codeInput = document.getElementById("section_code");
  if (!codeInput) return;

  const genCode = () => {
    let out = "";
    for (let i = 0; i < 4; i++) {
      out += String.fromCharCode(65 + Math.floor(Math.random() * 26)); // A-Z
    }
    return out;
  };

  // generate immediately when page loads
  codeInput.value = genCode();

  // optional: if you want a fresh code when admin changes section name, uncomment:
  // document.getElementById('section_name').addEventListener('input', () => { codeInput.value = genCode(); });
});
