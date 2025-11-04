/* create_section_form.js
   - Populates year dropdowns
   - Validates empty fields
   - Shows red "Required" badge INSIDE the input/select when invalid
   - Removes badge when corrected
*/

(function () {
  const form = document.getElementById('create-section-form');

  const sectionInput = document.getElementById('section_name');
  const startSelect = document.getElementById('start_school_year');
  const endSelect = document.getElementById('end_school_year');

  const errorSection = document.getElementById('error-section_name');
  const errorStart = document.getElementById('error-start_school_year');
  const errorEnd = document.getElementById('error-end_school_year');

  /* -------------------------------
      POPULATE YEAR SELECTS
  --------------------------------*/
  function populateYears() {
    const now = new Date();
    const thisYear = now.getFullYear();
    const max = thisYear + 5;

    for (let y = thisYear; y <= max; y++) {
      const optStart = document.createElement('option');
      optStart.value = y;
      optStart.textContent = y;
      startSelect.appendChild(optStart);

      const optEnd = document.createElement('option');
      optEnd.value = y;
      optEnd.textContent = y;
      endSelect.appendChild(optEnd);
    }
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
        markInvalid(sectionWrapper, errorSection, "Section name is required.");
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

  startSelect.addEventListener("change", () => {
    clearInvalid(startSelect.parentElement, errorStart);

    const startYear = Number(startSelect.value);
    const candidate = String(startYear + 1);

    const hasOption = [...endSelect.options].some(opt => opt.value === candidate);
    if (hasOption) {
      endSelect.value = candidate;
      clearInvalid(endSelect.parentElement, errorEnd);
    }
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
