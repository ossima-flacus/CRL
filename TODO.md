# TODO: Implémentation Système de Connexion (Login pour Admins)

## Plan Approuvé

- Système de connexion admin intégré dans le projet existant (sessions protégées).
- Structure MVC backend, frontend login/dashboard.
- Pas de modifications demandées.

## Étapes à Compléter (1/10)

### 1. [x] Ajouter table `users` dans schema

- Schema édité, exécuter: `mysql -u root -p crl_db < backend/schema.sql`

### 2. [x] Créer `backend/app/models/User.php` OK

### 3. [x] Créer `backend/app/controllers/AuthController.php` OK

### 4. [x] Créer `backend/app/middleware/AuthMiddleware.php` OK

### 5. [x] Mettre à jour `backend/public/index.php`

- Routes auth ajoutées (/auth/login, /auth/register, /auth/logout, /auth/me).
- Routes protégées (class._, student._) avec AuthMiddleware.

### 6. [ ] Créer `frontend/login.html` + assets (CSS/JS)

### 7. [ ] Mettre à jour `frontend/dashboard.html` (session check)

### 8. [ ] Créer `frontend/assets/js/auth.js`

### 9. [ ] Tester endpoints (login/register/logout)

### 10. [ ] Tester frontend + seed user test

**Prochaine étape: Commencer par DB schema (étape 1).**
