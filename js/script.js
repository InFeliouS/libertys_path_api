const togglePassword = document.getElementById("togglePassword");
const passwordInput = document.getElementById("password");

togglePassword.addEventListener("click", () => {
  const isPassword = passwordInput.type === "password";
  passwordInput.type = isPassword ? "text" : "password";
  togglePassword.textContent = isPassword ? "Show" : "Hide";
});

//js for bg-changes-start
const backgroundImages = [
  '../assets/libertys-path-bg-design-1.png',
  '../assets/libertys-path-bg-design-2.png',
  '../assets/libertys-path-bg-design-3.png'
];

let currentIndex = 0;

function changeBackground() {
  document.body.style.backgroundImage = `url('${backgroundImages[currentIndex]}')`;
  currentIndex = (currentIndex + 1) % backgroundImages.length;
}

changeBackground();
setInterval(changeBackground, 5000);
//js for bg-changes-end