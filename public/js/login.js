const togglePassword = document.getElementById("togglePassword")
const passwordInput = document.getElementById("password")

if (togglePassword && passwordInput) {
  togglePassword.addEventListener("click", () => {
    const isPassword = passwordInput.type === "password"
    passwordInput.type = isPassword ? "text" : "password"
    togglePassword.textContent = isPassword ? "Hide" : "Show"
  })
}

// Background image rotation
const backgroundImages = [
  "/assets/libertys-path-bg-design-1.png",
  "/assets/libertys-path-bg-design-2.png",
  "/assets/libertys-path-bg-design-3.png",
]

let currentIndex = 0

function changeBackground() {
  document.body.style.backgroundImage = `url('${backgroundImages[currentIndex]}')`
  currentIndex = (currentIndex + 1) % backgroundImages.length
}

// Only run the background rotation on the login page
if (
  window.location.pathname.includes("/login") ||
  window.location.pathname.endsWith("/libertys_path_api/") ||
  window.location.pathname === "/libertys_path_api"
) {
  // Initialize background
  changeBackground()
  // Set interval for rotation
  setInterval(changeBackground, 5000)
}