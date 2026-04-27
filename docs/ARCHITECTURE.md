# Architecture du Projet

## Vue d'ensemble

CRL suit une architecture MVC (Modèle-Vue-Contrôleur) avec une API RESTful.

```
┌─────────────┐
│  Frontend   │
│ (HTML/JS)   │
└──────┬──────┘
       │ HTTP
       ▼
┌─────────────────────┐
│  Backend (API)      │
│  - Controllers      │
│  - Models           │
│  - Middleware       │
└──────┬──────────────┘
       │
       ▼
┌─────────────────┐
│   Database      │
│   (MySQL)       │
└─────────────────┘
```

## Composants

### Backend

- **Controllers**: Gèrent les requêtes HTTP et retournent les réponses
- **Models**: Interagissent avec la base de données
- **Middleware**: Valident les requêtes (Auth, Autorisation)
- **Config**: Gestion de la configuration et connexion DB

### Frontend

- **Pages**: Pages HTML (Login, Dashboard, etc.)
- **Assets**: Styles CSS et scripts JavaScript

### Base de Données

- MySQL/MariaDB
- Schéma normalisé (Users, Classes, Students, etc.)

## Flux d'Authentification

```
1. Utilisateur se connecte (frontend)
   │
2. Requête POST /auth/login
   │
3. Controller valide les credentials
   │
4. Sessions créées
   │
5. Redirection vers dashboard
```

## Flux des Requêtes

```
Requête HTTP
    │
    ▼
Routing (index.php)
    │
    ▼
Middleware (Authentification, Autorisation)
    │
    ▼
Controller (Logique métier)
    │
    ▼
Model (Accès DB)
    │
    ▼
Réponse JSON/HTML
```

## Convention de Dénomination

- **Controllers**: `NomController.php` (ex: UserController)
- **Models**: `Nom.php` (ex: User)
- **Middleware**: `NomMiddleware.php` (ex: AuthMiddleware)
- **Routes**: `/resource` ou `/resource/{id}` (REST)

## Sécurité

- Sessions protégées par SessionMiddleware
- Rôles d'utilisateurs gérés par RoleMiddleware
- Entrées validées et échappées
- Mots de passe hashés avec password_hash()
