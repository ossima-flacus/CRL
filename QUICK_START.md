# Guide d'Installation Rapide - CRL

## 🚀 Démarrage en 5 minutes

### Prérequis

- PHP 7.4+
- MySQL/MariaDB
- Apache avec mod_rewrite
- XAMPP (inclut tout ce qu'il faut)

### Installation

#### 1. Vérifier la structure

```bash
cd c:\xampp\htdocs\CRL
```

Vous devriez voir:

```
backend/          # API PHP
frontend/         # Interface Web
docs/             # Documentation
scripts/          # Scripts utiles
.env              # Configuration
```

#### 2. Initialiser la base de données

**Windows (PowerShell):**

```powershell
php scripts\init-db.php
php scripts\test-db.php
```

**Linux/Mac:**

```bash
php scripts/init-db.php
php scripts/test-db.php
```

#### 3. Configurer Apache

Éditer `c:\xampp\apache\conf\httpd.conf`:

```apache
# Chercher la ligne <Directory "/xampp/htdocs">
# Ajouter après:

<Directory "/xampp/htdocs/CRL/backend/public">
    AllowOverride All
    Require all granted
</Directory>

# Vérifier que mod_rewrite est activé:
# La ligne doit être: LoadModule rewrite_module modules/mod_rewrite.so
```

Puis redémarrer Apache:

```powershell
# Dans XAMPP Control Panel, cliquer "Restart" pour Apache
```

#### 4. Accéder à l'application

Ouvrir: **http://localhost/CRL/frontend/login.html**

**Identifiants de test:**

- Utilisateur: `admin`
- Mot de passe: `Admin@123`

### Vérifier l'installation

```bash
# Tester la connexion à la base de données
php scripts/test-db.php

# Tester un endpoint API
curl -X POST http://localhost/CRL/backend/public/index.php?route=auth/login \
  -H "Content-Type: application/json" \
  -d "{\"username\":\"admin\",\"password\":\"Admin@123\"}"
```

### Utilisateurs de test créés

| Utilisateur | Mot de passe | Rôle       |
| ----------- | ------------ | ---------- |
| admin       | Admin@123    | admin      |
| secretaire  | Secr@123     | secretaire |
| teacher     | Teach@123    | comptable  |

### Que faire ensuite?

1. **Consulter la documentation:**
   - [Architecture](docs/ARCHITECTURE.md)
   - [API Reference](docs/API.md)
   - [Guide de développement](docs/DEVELOPMENT.md)

2. **Personnaliser:**
   - Modifier les utilisateurs dans la base de données
   - Ajouter des classes et étudiants
   - Customiser les styles CSS

3. **Déployer:**
   - Consulter [DEPLOYMENT.md](DEPLOYMENT.md)
   - Configurer HTTPS
   - Modifier les variables d'environnement

## Troubleshooting

### "Erreur 404 Not Found"

✅ Solution: Vérifier que mod_rewrite est activé et que .htaccess existe dans `backend/public/`

### "Connexion refusée" (MySQL)

✅ Solution:

- Vérifier que MySQL est démarré
- Vérifier les credentials dans `.env`
- Exécuter: `php scripts/test-db.php`

### "Authentification échouée"

✅ Solution:

- Vérifier les identifiants
- Réinitialiser la DB: `php scripts/init-db.php`

### Les requêtes API retournent 500

✅ Solution:

- Vérifier les logs Apache: `c:\xampp\apache\logs\error.log`
- Vérifier la syntaxe PHP: `php -l backend/app/controllers/*.php`

## Support

Pour plus d'aide:

1. Consulter [DEVELOPMENT.md](docs/DEVELOPMENT.md)
2. Vérifier les logs: `c:\xampp\apache\logs\error.log`
3. Tester avec cURL: voir section "Vérifier l'installation"
