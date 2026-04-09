// Common nav with dashboard link + auth check
const NAV_BASE = "../backend/public/index.php";

async function checkAuth() {
  try {
    const response = await fetch(`${NAV_BASE}?route=auth/me`);
    const data = await response.json();
    if (!data.success) {
      window.location.href = "login.html";
      return false;
    }
    return data.user.role;
  } catch {
    window.location.href = "login.html";
    return false;
  }
}

function addDashboardLink() {
  const navs = document.querySelectorAll("nav, .header-bar, .nav-links");
  navs.forEach((nav) => {
    let link = nav.querySelector('a[href="dashboard.html"], .dashboard-link');
    if (!link) {
      link = document.createElement("a");
      link.href = "dashboard.html";
      link.className = "dashboard-link nav-item";
      link.innerHTML = '<i class="fas fa-tachometer-alt"></i> Dashboard';
      nav.appendChild(link);
    }
  });
}

function initNav() {
  checkAuth().then((role) => {
    if (role) addDashboardLink();
  });
}

// Auto-init on all pages
if (document.readyState === "loading") {
  document.addEventListener("DOMContentLoaded", initNav);
} else {
  initNav();
}

// Logout function
window.logout = async () => {
  await fetch(`${NAV_BASE}?route=auth/logout`, { method: "POST" });
  window.location.href = "login.html";
};
