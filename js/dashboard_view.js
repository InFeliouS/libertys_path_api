function showLogoutConfirmation() {
    document.getElementById("logoutModal").style.display = "block";
  }

  function closeLogoutConfirmation() {
    document.getElementById("logoutModal").style.display = "none";
  }

  function confirmLogout() {
    window.location.href = "/libertys_path_api/logout";
  }