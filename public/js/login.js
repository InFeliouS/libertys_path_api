// public/js/login.js

// Toggle password
const togglePassword = document.getElementById("togglePassword");
const passwordInput = document.getElementById("password");

if (togglePassword && passwordInput) {
  togglePassword.addEventListener("click", () => {
    const isPassword = passwordInput.type === "password";
    passwordInput.type = isPassword ? "text" : "password";
    togglePassword.textContent = isPassword ? "Hide" : "Show";
  });
}

// ---- Background rotator ----

// 1) Use RELATIVE paths (they resolve to /libertys_path_api/public/assets/*)
const backgrounds = [
  "assets/libertys-path-bg-design-1.png",
  "assets/libertys-path-bg-design-2.png",
  "assets/libertys-path-bg-design-3.png",
];

// 2) Pick a visible container. If your HTML has a wrapper, e.g. <div class="login-bg">,
// we'll use that. Otherwise we fall back to <body>.
const container =
  document.querySelector(".login-bg") ||
  document.querySelector(".login-page") ||
  document.body;

// 3) Ensure the background is actually visible
Object.assign(container.style, {
  backgroundRepeat: "no-repeat",
  backgroundSize: "cover",
  backgroundPosition: "center",
});

// 4) Preload images so network requests are visible and swaps are instant
const preloaded = [];
backgrounds.forEach((src) => {
  const img = new Image();
  img.src = src;
  preloaded.push(img);
});

let currentIndex = 0;

function applyBackground() {
  const url = backgrounds[currentIndex];
  // Inline style overrides stylesheet backgrounds
  container.style.backgroundImage = `url("${url}")`;
}

function changeBackground() {
  currentIndex = (currentIndex + 1) % backgrounds.length;
  applyBackground();
}

// Only run on pages that contain the login form
if (document.querySelector(".login-form")) {
  // Initial paint
  applyBackground();

  // Rotate every 10s (adjust as you like)
  setInterval(changeBackground, 10000);
}
