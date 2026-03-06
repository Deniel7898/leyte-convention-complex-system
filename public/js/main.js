(function () {
  const sidebarNavWrapper = document.querySelector(".sidebar-nav-wrapper");
  const mainWrapper = document.querySelector(".main-wrapper");
  const menuToggleButton = document.querySelector("#menu-toggle");
  const overlay = document.querySelector(".overlay");

  // Load sidebar state from localStorage
  if (localStorage.getItem("sidebarActive") === "true") {
    sidebarNavWrapper.classList.add("active");
    mainWrapper.classList.add("active");
    overlay.classList.add("active");
  }

  menuToggleButton.addEventListener("click", () => {
    sidebarNavWrapper.classList.toggle("active");
    mainWrapper.classList.toggle("active");
    overlay.classList.toggle("active"); // toggle overlay instead of always adding

    // Save state to localStorage
    localStorage.setItem(
      "sidebarActive",
      sidebarNavWrapper.classList.contains("active")
    );

    // Optionally toggle icon (if using a <i> inside button)
    const menuIcon = menuToggleButton.querySelector("svg");
    if (menuIcon) {
      // Swap classes or styles if needed
      menuIcon.classList.toggle("bi-list");
      // You can add other icons like 'bi-x' for close
    }
  });

  overlay.addEventListener("click", () => {
    sidebarNavWrapper.classList.remove("active");
    mainWrapper.classList.remove("active");
    overlay.classList.remove("active");

    // Update localStorage
    localStorage.setItem("sidebarActive", "false");
  });
})(); 


// Custom Main Layout

