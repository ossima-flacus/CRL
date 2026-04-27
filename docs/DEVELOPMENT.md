# Guide de Développement

## Commandes Utiles

### Base de Données

```bash
# Importer le schéma
mysql -u root -p crl_db < backend/schema.sql

# Exporter les données
mysqldump -u root -p crl_db > backup.sql

# Accéder à la BD
mysql -u root -p crl_db
```

### Gestion du Projet

```bash
# Initialiser Git
git init
git add .
git commit -m "Initial commit"

# Cloner le repo
git clone <repo-url>
cd CRL
```

## Conventions de Codage

### PHP

```php
<?php
// Nom de classe en PascalCase
class UserController {
    // Méthodes en camelCase
    public function getUserProfile($id) {
        // Code...
    }
}

// Constantes en UPPER_SNAKE_CASE
const DB_TIMEOUT = 30;

// Variables en snake_case ou camelCase
$user_email = "user@example.com";
$userName = "John Doe";
```

### JavaScript

```javascript
// Fichiers en kebab-case
// admin-users.js

// Fonctions en camelCase
function getUserData(id) {
  // Code...
}

// Constantes en UPPER_CASE
const API_BASE_URL = "http://localhost/CRL/backend/public";

// Variables en camelCase
let currentUser = null;
```

### CSS

```css
/* Classe BEM (Block Element Modifier) */
.button {
}
.button--primary {
}
.button__icon {
}

/* Nommage descriptif */
.form-group {
}
.modal-header {
}
```

## Workflow de Développement

### 1. Créer une branche

```bash
git checkout -b feature/user-management
```

### 2. Développer et tester

```bash
# Faire les changements
vim backend/app/controllers/UserController.php

# Tester localement
# http://localhost/CRL/backend/public/user/list
```

### 3. Commit

```bash
git add backend/app/controllers/UserController.php
git commit -m "feat: add user listing feature"
```

### 4. Push et Pull Request

```bash
git push origin feature/user-management
# Créer PR sur GitHub
```

## Debugging

### PHP Errors

Ajouter au début de `index.php`:

```php
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

### Network Requests

Utiliser les DevTools du navigateur (F12) pour déboguer les requêtes.

### Logging

Créer un fichier `logs/app.log`:

```php
file_put_contents('logs/app.log',
    date('Y-m-d H:i:s') . ' - ' . $message . PHP_EOL,
    FILE_APPEND
);
```

## Testing

### Manuel

Tester via Postman ou curl:

```bash
curl -X POST http://localhost/CRL/backend/public/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@example.com","password":"password123"}'
```

### Unitaire

```bash
# Avec PHPUnit (si installé)
composer require --dev phpunit/phpunit
vendor/bin/phpunit tests/
```

## Performance

- Utiliser l'onglet Network dans DevTools
- Monitorer les requêtes DB lentes
- Optimiser les requêtes avec INDEX
- Minifier CSS/JS en production

## Problèmes Courants

| Problème                   | Solution                                           |
| -------------------------- | -------------------------------------------------- |
| 404 Not Found              | Vérifier `mod_rewrite` activé, `.htaccess` présent |
| Connexion DB échouée       | Vérifier DB_HOST, DB_USER, DB_PASSWORD en `.env`   |
| Sessions ne persistent pas | Vérifier permissions dossier session PHP           |
| CORS error                 | Configurer `Access-Control-Allow-*` headers        |
| Fichiers uploadés échouent | Vérifier permissions dossier upload                |
