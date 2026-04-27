// Admin user management (only admin)
const API_BASE = "../backend/public/index.php";

async function loadUsers() {
  try {
    const response = await fetch(`${API_BASE}?route=users/list`);
    if (!response.ok) {
      throw new Error(`HTTP ${response.status}`);
    }
    const data = await response.json();
    if (data.success) {
      displayUsers(data.users);
    } else {
      console.error("Load users error:", data.message);
    }
  } catch (e) {
    console.error("Load users error", e);
    alert("Erreur lors du chargement des utilisateurs");
  }
}

function displayUsers(users) {
  const container = document.getElementById("usersList");
  if (!container) return;
  container.innerHTML = users
    .map(
      (user) => `
        <div class="user-card">
            <div><strong>${escapeHtml(user.username)}</strong> (${escapeHtml(user.role)})</div>
            <div>${escapeHtml(user.email)}</div>
            <button onclick="deleteUser(${user.id})">Supprimer</button>
        </div>
    `,
    )
    .join("");
}

async function createUser() {
  const username = document.getElementById("newUsername")?.value;
  const email = document.getElementById("newEmail")?.value;
  const role = document.getElementById("newRole")?.value;
  const password = document.getElementById("newPassword")?.value;

  if (!username || !email || !password || !role) {
    alert("Tous les champs sont requis");
    return;
  }

  try {
    const response = await fetch(`${API_BASE}?route=users/create`, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ username, email, password, role }),
    });
    if (!response.ok) {
      throw new Error(`HTTP ${response.status}`);
    }
    const data = await response.json();
    if (data.success) {
      loadUsers();
      showMessage("Utilisateur créé!");
      // Clear form
      document.getElementById("newUsername").value = "";
      document.getElementById("newEmail").value = "";
      document.getElementById("newPassword").value = "";
    } else {
      showMessage(data.message || "Erreur lors de la création", "error");
    }
  } catch (e) {
    console.error(e);
    showMessage("Erreur lors de la création", "error");
  }
}

async function deleteUser(id) {
  if (confirm("Supprimer cet utilisateur?")) {
    try {
      const response = await fetch(`${API_BASE}?route=users/delete&id=${id}`, {
        method: "GET",
      });
      if (!response.ok) {
        throw new Error(`HTTP ${response.status}`);
      }
      const data = await response.json();
      if (data.success) {
        showMessage("Utilisateur supprimé");
        loadUsers();
      } else {
        showMessage(data.message || "Erreur lors de la suppression", "error");
      }
    } catch (e) {
      console.error(e);
      showMessage("Erreur lors de la suppression", "error");
    }
  }
}

function showMessage(msg, type = "success") {
  const msgEl = document.getElementById("message");
  if (!msgEl) return;
  msgEl.textContent = msg;
  msgEl.className = type;
  msgEl.style.opacity = 1;
  setTimeout(() => (msgEl.style.opacity = 0), 3000);
}

function escapeHtml(text) {
  const map = {
    "&": "&amp;",
    "<": "&lt;",
    ">": "&gt;",
    '"': "&quot;",
    "'": "&#039;",
  };
  return text.replace(/[&<>"']/g, (m) => map[m]);
}
