// Common nav with dashboard link + auth check
const NAV_BASE = "../backend/public/index.php";

async function checkAuth() {
  try {
    const response = await fetch(`${NAV_BASE}?route=auth/me`);

    if (!response.ok) {
      return false;
    }

    const contentType = response.headers.get("content-type");
    if (!contentType || !contentType.includes("application/json")) {
      console.error("Invalid response type from auth/me");
      return false;
    }

    const data = await response.json();
    if (!data.success) {
      return false;
    }

    return data.user.role;
  } catch (error) {
    console.error("Auth check error:", error);
    return false;
  }
}

function addDashboardLink() {
  const navs = document.querySelectorAll("nav, .header-bar, .nav-links");
  navs.forEach((nav) => {
    let link = nav.querySelector('a[href="index.html"], .dashboard-link');
    if (!link) {
      link = document.createElement("a");
      link.href = "index.html";
      link.className = "dashboard-link nav-item";
      link.innerHTML = '<i class="fas fa-tachometer-alt"></i> Dashboard';
      nav.appendChild(link);
    }
  });
}

function initNav() {
  checkAuth().then((role) => {
    if (role) addDashboardLink();
    else window.location.href = "login.html";
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
  try {
    await fetch(`${NAV_BASE}?route=auth/logout`, { method: "POST" });
  } catch (error) {
    console.error("Logout error:", error);
  }
  window.location.href = "login.html";
};
