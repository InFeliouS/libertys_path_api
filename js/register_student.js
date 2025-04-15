document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("student-form");

    const givenName = document.querySelector('input[name="given_name"]');
    const middleName = document.querySelector('input[name="middle_name"]');
    const lastName = document.querySelector('input[name="last_name"]');
    const sectionName = document.querySelector('input[name="section_name"]');

    // Automatically convert to uppercase while typing
    [givenName, middleName, lastName, sectionName].forEach(input => {
        input.addEventListener('input', function () {
            this.value = this.value.toUpperCase();
        });
    });

    // Validate on submit
    form.addEventListener("submit", function (e) {
        const isAllCaps = (value) => value === value.toUpperCase();

        if (
            !isAllCaps(givenName.value) ||
            (middleName.value && !isAllCaps(middleName.value)) ||
            !isAllCaps(lastName.value) ||
            !isAllCaps(sectionName.value)
        ) {
            alert("Please make sure all name fields are in CAPITAL LETTERS.");
            e.preventDefault();
        }
    });
});

const togglePassword = document.getElementById("togglePassword")
const passwordInput = document.getElementById("password")

if (togglePassword && passwordInput) {
  togglePassword.addEventListener("click", () => {
    const isPassword = passwordInput.type === "password"
    passwordInput.type = isPassword ? "text" : "password"
    togglePassword.textContent = isPassword ? "Hide" : "Show"
  })
}
