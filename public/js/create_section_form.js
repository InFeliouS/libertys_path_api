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
    const startMax = thisYear + 5;   // original max for start
    const endMax = startMax + 1;     // ensure end has one extra year

    // clear any existing options (safe)
    startSelect.innerHTML = "";
    endSelect.innerHTML = "";

    // placeholder option for both selects
    const placeholderStart = document.createElement("option");
    placeholderStart.value = "";
    placeholderStart.textContent = "— Select Year —";
    startSelect.appendChild(placeholderStart);

    const placeholderEnd = document.createElement("option");
    placeholderEnd.value = "";
    placeholderEnd.textContent = "— Select Year —";
    endSelect.appendChild(placeholderEnd);

    // populate start years (ascending) from thisYear .. startMax
    for (let y = thisYear; y <= startMax; y++) {
      const optStart = document.createElement("option");
      optStart.value = String(y);
      optStart.textContent = String(y);
      startSelect.appendChild(optStart);
    }

    // populate end years (ascending) from thisYear+1 .. endMax
    // this guarantees that for any selected start (thisYear .. startMax),
    // start+1 is present in the endSelect options.
    for (let y = thisYear + 1; y <= endMax; y++) {
      const optEnd = document.createElement("option");
      optEnd.value = String(y);
      optEnd.textContent = String(y);
      endSelect.appendChild(optEnd);
    }

    // start with end disabled until a start year is chosen
    endSelect.disabled = true;

    // ensure selects show placeholder by default
    startSelect.value = "";
    endSelect.value = "";
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

  // Updated start change behavior: enable end, auto-select start+1 when available
  startSelect.addEventListener("change", () => {
    clearInvalid(startSelect.parentElement, errorStart);

    const startYear = Number(startSelect.value);
    if (!startSelect.value) {
      // if user cleared start, disable end again and clear selection
      endSelect.value = "";
      endSelect.disabled = true;
      return;
    }

    // enable end when start chosen
    endSelect.disabled = false;

    const candidate = String(startYear + 1);
    const hasOption = [...endSelect.options].some(
      (opt) => opt.value === candidate
    );
    if (hasOption) {
      endSelect.value = candidate;
      clearInvalid(endSelect.parentElement, errorEnd);
    } else {
      // if exact start+1 not present, pick the smallest year > start (defensive)
      const greater = [...endSelect.options]
        .map((o) => o.value)
        .filter((v) => v !== "" && Number(v) > startYear)
        .map(Number)
        .sort((a, b) => a - b);
      if (greater.length) {
        endSelect.value = String(greater[0]);
        clearInvalid(endSelect.parentElement, errorEnd);
      } else {
        endSelect.value = "";
        markInvalid(
          endSelect.parentElement,
          errorEnd,
          "No valid end year available for the selected start."
        );
      }
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
