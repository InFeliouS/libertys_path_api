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

// === Alert helpers ===
(function () {
  const alertBox = document.getElementById("loginAlert");
  if (!alertBox) return;

  function showAlert(msg, kind = "error") {
    alertBox.textContent = msg;
    // preserve base "alert" class if you already added layout classes
    alertBox.classList.add("alert");
    alertBox.classList.remove("alert--success", "alert--error");
    alertBox.classList.add(kind === "success" ? "alert--success" : "alert--error");
  }

  // Expose for other functions below
  window.__loginShowAlert = showAlert;

  // If backend redirects back with ?err=user|pass|both (legacy behavior)
  const params = new URLSearchParams(window.location.search);
  const err = params.get("err");

  if (err === "user") {
    // Username not found -> per requirement: show invalid username & password
    showAlert("Invalid username and password.");
  } else if (err === "pass") {
    showAlert("Wrong password.");
  } else if (err === "both") {
    showAlert("Invalid username and password.");
  }

  // Optional: if server echoes a plain string via ?msg=
  const msg = params.get("msg");
  if (!err && msg) showAlert(msg);
})();

// === AJAX submit to prevent navigation on errors ===
(function () {
  const form = document.querySelector(".login-form");
  if (!form) return;

  const btn = form.querySelector('button[type="submit"]') || null;
  const usernameEl = document.getElementById("username");
  const passwordEl = document.getElementById("password");

  form.addEventListener("submit", async (e) => {
    e.preventDefault(); // STOP page navigation for errors; we decide when to redirect

    const showAlert = window.__loginShowAlert || function(m){ alert(m); };

    const username = (usernameEl?.value || "").trim();
    const password = (passwordEl?.value || "");

    if (!username || !password) {
      showAlert("Please enter your username and password.");
      return;
    }

    // Lock UI
    const originalBtnText = btn ? btn.textContent : "";
    if (btn) { btn.disabled = true; btn.textContent = "Please wait..."; }

    try {
      // Use action from form
      const baseAction = form.getAttribute("action") || "index.php?r=login/process";
      const url = baseAction.includes("?") ? baseAction + "&ajax=1" : baseAction + "?ajax=1";

      const res = await fetch(url, {
        method: "POST",
        body: new FormData(form),
        credentials: "same-origin",
        headers: { "Accept": "application/json, text/html;q=0.9, */*;q=0.8" }
      });

      const ct = (res.headers.get("content-type") || "").toLowerCase();

      // 1) Preferred: JSON contract
      if (ct.includes("application/json")) {
        const data = await res.json();

        if (data.ok) {
          showAlert("Login successful. Redirecting…", "success");
          // Only success triggers a redirect
          setTimeout(() => {
            window.location.href = data.redirect || "index.php";
          }, 500);
          return;
        }

        // Map known reasons
        switch (data.reason) {
          case "invalid_username":
          case "invalid_username_password":
            showAlert("Invalid username and password.");
            break;
          case "wrong_password":
            showAlert("Wrong password.");
            break;
          default:
            showAlert("Invalid username or password.");
            break;
        }
        // IMPORTANT: No redirect on errors
        return;
      }

      // 2) HTML path (legacy): infer from final URL or content WITHOUT navigating
      const finalUrl = res.url || "";

      // If server would normally redirect to dashboard on success, detect that URL
      if (/(dashboard|home|board|portal)/i.test(finalUrl) && !/login/i.test(finalUrl)) {
        showAlert("Login successful. Redirecting…", "success");
        setTimeout(() => { window.location.href = finalUrl; }, 300);
        return;
      }

      // If server appends ?err=... to login, mirror the same UI alerts here
      try {
        const u = new URL(finalUrl, window.location.origin);
        const err = u.searchParams.get("err");
        if (err === "user") { showAlert("Invalid username and password."); return; }
        if (err === "pass") { showAlert("Wrong password."); return; }
        if (err === "both") { showAlert("Invalid username and password."); return; }
      } catch(_) { /* ignore parse errors */ }

      // Last resort: read HTML and pattern-match
      const text = await res.text();
      if (/username/i.test(text) && /(invalid|not\s*found)/i.test(text)) {
        showAlert("Invalid username and password.");
      } else if (/password/i.test(text) && /(wrong|invalid|mismatch)/i.test(text)) {
        showAlert("Wrong password.");
      } else {
        showAlert("Invalid username or password.");
      }
      // No redirect on errors

    } catch (err) {
      console.error(err);
      showAlert("Network error. Please try again.");
    } finally {
      if (btn) { btn.disabled = false; btn.textContent = originalBtnText; }
    }
  });
})();
