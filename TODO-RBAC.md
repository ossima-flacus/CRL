# TODO: Implémentation RBAC Multi-Rôles + Dashboard Premium

## Status Backend

- ✅ DB `backend/schema.sql` fusion (users multi-rôles + bulletins/publicites).
- ✅ User model (role param).
- ✅ AuthController (role in register).
- ✅ RoleMiddleware.

## Étapes (1/12)

### 1. [x] DB fusion + roles ENUM

### 2. [ ] Update AuthMiddleware use RoleMiddleware

### 3. [ ] DashboardController (role data)

### 4. [ ] BulletinController (personal for parent/apprenant)

### 5. [ ] PubliciteController (add for secretaire/comptable)

### 6. [ ] Router update routes + role protection

### 7. [ ] Frontend auth.js (role storage)

### 8. [ ] Dashboard.html (role sections, logout)

### 9. [ ] Dashboard.css (glassmorphism, charts)

### 10. [ ] Dashboard.js (auth check, role UI, Chart.js)

### 11. [ ] Test all roles

### 12. [ ] DB execute + seed test users

**Prochaine: Étape 2 - Update middleware/router.**
