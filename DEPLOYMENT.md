# Script de déploiement et maintenance

## Initialisation

### Windows (PowerShell)

```powershell
php scripts\init-db.php
php scripts\test-db.php
```

### Linux/Mac (Bash)

```bash
php scripts/init-db.php
php scripts/test-db.php
```

## Commandes disponibles

### Tester la base de données

```bash
php scripts/test-db.php
```

### Réinitialiser la base de données

```bash
php scripts/init-db.php
```

## API Endpoints

### Test avec cURL

#### Login

```bash
curl -X POST http://localhost/CRL/backend/public/index.php?route=auth/login \
  -H "Content-Type: application/json" \
  -d '{"username":"admin","password":"Admin@123"}'
```

#### Lister les classes

```bash
curl -X GET "http://localhost/CRL/backend/public/index.php?route=class.list"
```

#### Créer une classe

```bash
curl -X POST http://localhost/CRL/backend/public/index.php?route=class.create \
  -H "Content-Type: application/json" \
  -d '{"name":"Classe C","level":"5e","description":"Classe de 5ème année"}'
```

## Troubleshooting

### Erreur: "Connexion refusée"

- Vérifier que MySQL est en cours d'exécution
- Vérifier les paramètres DB_HOST, DB_USER, DB_PASSWORD dans .env

### Erreur: "Authentification échouée"

- Vérifier les identifiants: admin/Admin@123
- Créer un nouvel utilisateur avec init-db.php

### Erreur 404 - Routes non trouvées

- Vérifier que mod_rewrite est activé dans Apache
- Vérifier le fichier .htaccess dans backend/public/

### Erreur 500 - Internal Server Error

- Vérifier les logs d'erreur Apache
- Vérifier que tous les fichiers PHP ont la bonne syntaxe
- Vérifier les permissions des fichiers
