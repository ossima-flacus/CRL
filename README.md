# CRL - Système de Gestion d'Établissements Scolaires

## 📋 Description

CRL est une application web de gestion d'établissements scolaires permettant de gérer les utilisateurs, les classes et les étudiants avec un système d'authentification sécurisé.

## 🚀 Caractéristiques

- ✅ Système d'authentification sécurisé (Login/Register)
- ✅ Gestion des utilisateurs avec rôles (Admin, Enseignant, Étudiant)
- ✅ Gestion des classes et des étudiants
- ✅ Dashboard intuitif
- ✅ API RESTful
- ✅ Middleware d'authentification et autorisation

## 📁 Structure du Projet

```
CRL/
├── backend/                    # API PHP
│   ├── app/
│   │   ├── config/            # Configuration (DB)
│   │   ├── controllers/       # Contrôleurs (logique métier)
│   │   ├── middleware/        # Middlewares (Auth, Role)
│   │   └── models/            # Modèles (User, Student, etc.)
│   ├── public/                # Point d'entrée (index.php)
│   ├── config.php             # Configuration globale
│   ├── register.php           # Enregistrement des routes
│   └── schema.sql             # Schéma base de données
│
├── frontend/                  # Interface Web
│   ├── pages/                 # Pages HTML principales
│   ├── assets/
│   │   ├── css/              # Styles CSS
│   │   ├── js/               # Scripts JavaScript
│   │   └── img/              # Images et médias
│   └── index.html            # Page d'accueil
│
├── docs/                      # Documentation
├── tests/                     # Tests unitaires
├── .env.example               # Configuration exemple
├── .gitignore                 # Git ignorance
├── composer.json              # Dépendances PHP
└── README.md                  # Ce fichier
```

## 🛠️ Installation

### Prérequis

- PHP 7.4+
- MySQL/MariaDB
- Apache avec mod_rewrite
- Composer (optionnel)

### Étapes

1. **Cloner le projet**

```bash
git clone <votre-repo>
cd CRL
```

2. **Configuration de la base de données**

```bash
mysql -u root -p < backend/schema.sql
```

3. **Configurer les variables d'environnement**

```bash
cp .env.example .env
# Éditer .env avec vos paramètres
```

4. **Configurer Apache**
   Ajouter à `httpd.conf` ou fichier VirtualHost:

```apache
<Directory "/xampp/htdocs/CRL">
    AllowOverride All
</Directory>
```

## 📚 API Documentation

### Authentification

#### Login

```
POST /auth/login
Content-Type: application/json

{
    "email": "user@example.com",
    "password": "password123"
}
```

#### Register

```
POST /auth/register
Content-Type: application/json

{
    "name": "Nom Complet",
    "email": "user@example.com",
    "password": "password123",
    "role": "student"
}
```

#### Me (Get Current User)

```
GET /auth/me
Authorization: Bearer <token>
```

#### Logout

```
POST /auth/logout
Authorization: Bearer <token>
```

### Classes

```
GET /class          # Lister les classes
POST /class         # Créer une classe
GET /class/{id}     # Détails d'une classe
PUT /class/{id}     # Modifier une classe
DELETE /class/{id}  # Supprimer une classe
```

### Étudiants

```
GET /student        # Lister les étudiants
POST /student       # Créer un étudiant
GET /student/{id}   # Détails d'un étudiant
PUT /student/{id}   # Modifier un étudiant
DELETE /student/{id} # Supprimer un étudiant
```

## 🔐 Sécurité

- Authentification par sessions sécurisées
- Middleware de validation des rôles
- Échappement des entrées utilisateur
- CSRF protection
- Hasage des mots de passe avec bcrypt

## 📝 Progression

Voir [TODO.md](TODO.md) et [TODO-RBAC.md](TODO-RBAC.md) pour les fonctionnalités en cours.

## 🤝 Contribution

1. Fork le projet
2. Créer une branche (`git checkout -b feature/AmazingFeature`)
3. Commit les changements (`git commit -m 'Add AmazingFeature'`)
4. Push vers la branche (`git push origin feature/AmazingFeature`)
5. Ouvrir une Pull Request

## 📄 Licence

MIT License

## 👥 Support

Pour toute question ou problème, contactez l'équipe CRL.

## 📅 Changelog

### v0.1.0 (Initial)

- Système de gestion de base
- Authentification
- CRUD Utilisateurs, Classes, Étudiants
