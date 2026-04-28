// Authentification API base URL
// Ajuste le chemin en fonction de la profondeur du dossier
const getApiBase = () => {
  const currentPath = window.location.pathname;

  // Si on est dans un sous-dossier (pages/), remonter d'un niveau
  if (currentPath.includes("/pages/")) {
    return "../../backend/public/index.php?route=";
  }

  // Sinon, on est dans le dossier racine frontend/
  return "../backend/public/index.php?route=";
};

const API_BASE = getApiBase();

// Show message
function showMessage(message, type = "info") {
  const msgDiv = document.getElementById("loginMessage");
  if (!msgDiv) return;
  msgDiv.textContent = message;
  msgDiv.className = `message ${type}`;
}

// Toggle forms
document.addEventListener("DOMContentLoaded", () => {
  const showRegister = document.getElementById("showRegister");
  const showLogin = document.getElementById("showLogin");
  const loginForm = document.getElementById("loginForm");
  const registerForm = document.getElementById("registerForm");

  if (showRegister) {
    showRegister.addEventListener("click", (e) => {
      e.preventDefault();
      loginForm.style.display = "none";
      registerForm.style.display = "block";
    });
  }

  if (showLogin) {
    showLogin.addEventListener("click", (e) => {
      e.preventDefault();
      registerForm.style.display = "none";
      loginForm.style.display = "block";
    });
  }

  // Login form
  const loginFormEl = document.getElementById("loginForm");
  if (loginFormEl) {
    loginFormEl.addEventListener("submit", async (e) => {
      e.preventDefault();
      const username = document.getElementById("username").value;
      const password = document.getElementById("password").value;

      try {
        const response = await fetch(`${API_BASE}auth/login`, {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({ username, password }),
        });

        const data = await response.json();

        if (data.success) {
          showMessage("Connexion réussie ! Redirection...", "success");
          setTimeout(() => {
            window.location.href = "index.html";
          }, 1000);
        } else {
          showMessage(data.message || "Erreur de connexion", "error");
        }
      } catch (error) {
        showMessage("Erreur réseau. Vérifiez votre connexion.", "error");
      }
    });
  }

  // Register form
  const registerFormEl = document.getElementById("registerForm");
  if (registerFormEl) {
    registerFormEl.addEventListener("submit", async (e) => {
      e.preventDefault();
      const username = document.getElementById("regUsername").value;
      const email = document.getElementById("regEmail").value;
      const password = document.getElementById("regPassword").value;
      const roleElement = document.getElementById("regRole");
      const role = roleElement ? roleElement.value : "apprenant";

      try {
        const response = await fetch(`${API_BASE}auth/register`, {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({ username, email, password, role }),
        });

        const data = await response.json();

        if (data.success) {
          showMessage(
            "Compte créé ! Vous pouvez maintenant vous connecter.",
            "success",
          );
          setTimeout(() => {
            registerForm.style.display = "none";
            loginForm.style.display = "block";
          }, 2000);
        } else {
          showMessage(data.message || "Erreur d'inscription", "error");
        }
      } catch (error) {
        showMessage("Erreur réseau. Vérifiez votre connexion.", "error");
      }
    });
  }
});
