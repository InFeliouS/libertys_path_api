function showLogoutConfirmation() {
  document.querySelector(".logout-modal").style.display = "flex";
}

function closeLogoutConfirmation() {
  document.querySelector(".logout-modal").style.display = "none";
}

function confirmLogout() {
  window.location.href = "index.php?r=logout";
}
