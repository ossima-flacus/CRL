/**
 * Configuration Frontend
 */

// Déterminer le chemin de base de l'API en fonction de la localisation du fichier
function getApiBasePath() {
  const currentPath = window.location.pathname;

  // Si on est dans /pages/, remonter d'un niveau
  if (currentPath.includes("/pages/")) {
    return "../../backend/public/index.php?route=";
  }

  // Sinon (racine frontend/)
  return "../backend/public/index.php?route=";
}

// Configuration API
const API_CONFIG = {
  BASE_URL: getApiBasePath(),
  TIMEOUT: 5000,
};

// Construire une URL d'API complète
function buildApiUrl(endpoint) {
  return API_CONFIG.BASE_URL + endpoint;
}

// Appel API utilitaire
async function apiCall(endpoint, options = {}) {
  const { method = "GET", body = null, headers = {} } = options;

  try {
    const response = await fetch(buildApiUrl(endpoint), {
      method,
      headers: {
        "Content-Type": "application/json",
        ...headers,
      },
      body: body ? JSON.stringify(body) : undefined,
    });

    const data = await response.json();

    if (!response.ok) {
      throw new Error(data.message || "Erreur API");
    }

    return data;
  } catch (error) {
    console.error("Erreur API:", error);
    throw error;
  }
}

// Gestion de la session utilisateur
const UserSession = {
  get user() {
    const userData = localStorage.getItem("user");
    return userData ? JSON.parse(userData) : null;
  },

  set user(userData) {
    if (userData) {
      localStorage.setItem("user", JSON.stringify(userData));
    } else {
      localStorage.removeItem("user");
    }
  },

  isAuthenticated() {
    return this.user !== null;
  },

  logout() {
    this.user = null;
    window.location.href = "login.html";
  },

  async checkAuth() {
    try {
      const response = await apiCall("auth/me");
      if (response.success) {
        this.user = response.data;
        return true;
      }
    } catch (error) {
      this.user = null;
    }
    return false;
  },
};

// Notification utilitaire
function showNotification(message, type = "info", duration = 3000) {
  const notification = document.createElement("div");
  notification.className = `notification notification--${type}`;
  notification.textContent = message;

  // Créer le conteneur s'il n'existe pas
  let container = document.getElementById("notificationContainer");
  if (!container) {
    container = document.createElement("div");
    container.id = "notificationContainer";
    container.style.cssText = `
      position: fixed;
      top: 20px;
      right: 20px;
      z-index: 9999;
      display: flex;
      flex-direction: column;
      gap: 10px;
    `;
    document.body.appendChild(container);
  }

  container.appendChild(notification);

  // Styles CSS pour les notifications
  if (!document.getElementById("notificationStyles")) {
    const style = document.createElement("style");
    style.id = "notificationStyles";
    style.textContent = `
      .notification {
        padding: 12px 16px;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 500;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        animation: slideIn 0.3s ease;
      }
      .notification--info {
        background-color: #3b82f6;
        color: white;
      }
      .notification--success {
        background-color: #10b981;
        color: white;
      }
      .notification--error {
        background-color: #ef4444;
        color: white;
      }
      .notification--warning {
        background-color: #f59e0b;
        color: white;
      }
      @keyframes slideIn {
        from {
          transform: translateX(400px);
          opacity: 0;
        }
        to {
          transform: translateX(0);
          opacity: 1;
        }
      }
    `;
    document.head.appendChild(style);
  }

  setTimeout(() => {
    notification.remove();
  }, duration);
}

// Log utilitaire
function log(message, data = null) {
  if (process.env.NODE_ENV !== "production") {
    console.log(`[CRL] ${message}`, data || "");
  }
}
