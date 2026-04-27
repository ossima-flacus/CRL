# Guide de Démarrage du Projet CRL

## 📋 Prérequis

- **XAMPP** installé avec PHP 7.4+
- **MySQL/MariaDB** actif
- **Navigateur web moderne** (Chrome, Firefox, Edge, Safari)

## 🚀 Étapes d'Installation Rapide

### 1. Vérifier l'Emplacement du Projet

Le projet doit être dans: `c:\xampp\htdocs\CRL\`

### 2. Initialiser la Base de Données

#### Option A: Utiliser le Script d'Initialisation

```bash
# Ouvrez une console et naviguez vers le dossier
cd c:\xampp\htdocs\CRL\scripts

# Exécutez le script (Windows)
php init-db.php

# Ou (Linux/Mac)
php scripts/init-db.php
```

#### Option B: Initialisation Manuelle

```bash
# Ouvrez MySQL
mysql -u root -p

# Importez le schéma
USE crl_db;
SOURCE c:\xampp\htdocs\CRL\backend\schema.sql;
```

### 3. Configurer les Variables d'Environnement

Le fichier `.env` a déjà été créé avec les valeurs par défaut:

```env
DB_HOST=localhost
DB_PORT=3306
DB_NAME=crl_db
DB_USER=root
DB_PASSWORD=
APP_URL=http://localhost/CRL
```

**Modifiez-le si vos paramètres MySQL sont différents.**

### 4. Tester la Configuration

```bash
# Exécutez le test complet
php c:\xampp\htdocs\CRL\scripts\test-complete.php
```

Vous devriez voir: ✓ TOUS LES TESTS RÉUSSIS

### 5. Accéder à l'Application

Ouvrez votre navigateur et allez à:

```
http://localhost/CRL
```

## 👤 Comptes de Test

Après l'initialisation, les comptes suivants sont disponibles:

### Admin

- **Nom d'utilisateur:** admin
- **Email:** admin@crl.com
- **Mot de passe:** admin123

### Enseignant

- **Nom d'utilisateur:** teacher
- **Email:** teacher@crl.com
- **Mot de passe:** teacher123

### Étudiant

- **Nom d'utilisateur:** student
- **Email:** student@crl.com
- **Mot de passe:** student123

## 📚 Fonctionnalités Disponibles

### Pour les Administrateurs & Secrétaires

- ✓ Gestion des utilisateurs
- ✓ Gestion des classes
- ✓ Gestion des étudiants
- ✓ Gestion des inscriptions
- ✓ Tableau de bord complet

### Pour les Parents & Apprenants

- ✓ Accès au tableau de bord personnel
- ✓ Consultation des bulletins (à implémenter)
- ✓ Accès au blog (à implémenter)

## 🐛 Dépannage

### Erreur: "Impossible de se connecter à la base de données"

1. **Vérifiez que MySQL est en cours d'exécution:**

   ```bash
   mysql -u root -p
   ```

2. **Vérifiez les paramètres .env:**
   - `DB_HOST` correct (par défaut: localhost)
   - `DB_USER` correct (par défaut: root)
   - `DB_PASSWORD` correct

3. **Réexécutez le test complet:**
   ```bash
   php scripts/test-complete.php
   ```

### Erreur: "Impossibilité d'accéder au projet"

1. **Vérifiez que XAMPP Apache est actif:**
   - Ouvrez XAMPP Control Panel
   - Vérifiez que Apache a un bouton "Stop"
   - Si ce n'est pas le cas, cliquez sur "Start"

2. **Vérifiez le fichier .htaccess:**
   - Le fichier `c:\xampp\htdocs\CRL\.htaccess` doit exister
   - Vérifiez que AllowOverride All est actif dans Apache

3. **Réessayez:**
   ```
   http://localhost/CRL
   ```

### Erreur: "La table n'existe pas"

1. **Exécutez le script d'initialisation:**

   ```bash
   php scripts/init-db.php
   ```

2. **Ou importez le schéma SQL manuellement:**
   ```bash
   mysql -u root crl_db < backend/schema.sql
   ```

## 📝 Structure des Fichiers Importants

```
CRL/
├── .env                          # Configuration (À compléter)
├── backend/
│   ├── public/index.php         # Routeur principal
│   ├── app/
│   │   ├── controllers/         # Logique métier
│   │   ├── models/              # Accès BD
│   │   └── middleware/          # Authentification
│   └── schema.sql               # Schéma BD
├── frontend/
│   ├── login.html               # Page de connexion
│   ├── index.html               # Accueil
│   └── assets/
│       ├── js/                  # Scripts
│       └── css/                 # Styles
└── scripts/
    ├── init-db.php              # Initialiser BD
    └── test-complete.php        # Tests complets
```

## 🔗 URL Principales

- **Accueil:** `http://localhost/CRL`
- **Connexion:** `http://localhost/CRL/login.html`
- **Dashboard:** `http://localhost/CRL/dashboard.html`
- **API Login:** `http://localhost/CRL/backend/public/index.php?route=auth/login`

## 📞 Support

Consulter les fichiers:

- `docs/ARCHITECTURE.md` - Architecture système
- `docs/API.md` - Référence API
- `README.md` - Documentation générale
- `DEPLOYMENT.md` - Guide de déploiement

---

**Dernière mise à jour:** 27 avril 2026
