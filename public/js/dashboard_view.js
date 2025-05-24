/**function showLogoutConfirmation() {
  document.getElementById("logoutModal").style.display = "flex";
}

function closeLogoutConfirmation() {
  document.getElementById("logoutModal").style.display = "none";
}

function confirmLogout() {
  window.location.href = "/logout";
}**/

  function showLogoutConfirmation() {
    document.querySelector(".logout-modal").style.display = "flex";
  }

  function closeLogoutConfirmation() {
    document.querySelector(".logout-modal").style.display = "none";
  }

  function confirmLogout() {
    window.location.href = "/libertys_path_api/logout";
  }