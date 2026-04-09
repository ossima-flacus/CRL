// Admin user management (only admin)
const API_BASE = "../backend/public/index.php";

async function loadUsers() {
  try {
    const response = await fetch(`${API_BASE}?route=users/list`);
    const data = await response.json();
    if (data.success) {
      displayUsers(data.users);
    }
  } catch (e) {
    console.error("Load users error", e);
  }
}

function displayUsers(users) {
  const container = document.getElementById("usersList");
  container.innerHTML = users
    .map(
      (user) => `
        <div class="user-card">
            <div><strong>${user.username}</strong> (${user.role})</div>
            <div>${user.email}</div>
            <button onclick="deleteUser(${user.id})">Supprimer</button>
        </div>
    `,
    )
    .join("");
}

async function createUser() {
  const username = document.getElementById("newUsername").value;
  const email = document.getElementById("newEmail").value;
  const role = document.getElementById("newRole").value;
  const password = document.getElementById("newPassword").value;

  try {
    const response = await fetch(`${API_BASE}?route=users/create`, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ username, email, password, role }),
    });
    const data = await response.json();
    if (data.success) {
      loadUsers();
      showMessage("Utilisateur créé!");
    }
  } catch (e) {
    console.error(e);
  }
}

async function deleteUser(id) {
  if (confirm("Supprimer cet utilisateur?")) {
    try {
      const response = await fetch(`${API_BASE}?route=users/delete?id=${id}`, {
        method: "DELETE",
      });
      const data = await response.json();
      if (data.success) loadUsers();
    } catch (e) {
      console.error(e);
    }
  }
}

function showMessage(msg) {
  const msgEl = document.getElementById("message");
  msgEl.textContent = msg;
  msgEl.style.opacity = 1;
  setTimeout(() => (msgEl.style.opacity = 0), 3000);
}
