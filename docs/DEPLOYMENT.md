# Configuration et Déploiement

## Variables d'Environnement

Créer un fichier `.env` à la racine du projet basé sur `.env.example`:

```bash
cp .env.example .env
```

### Variables disponibles

| Variable      | Description                            | Exemple              |
| ------------- | -------------------------------------- | -------------------- |
| `DB_HOST`     | Hôte de la base de données             | localhost            |
| `DB_PORT`     | Port MySQL                             | 3306                 |
| `DB_NAME`     | Nom de la base de données              | crl_db               |
| `DB_USER`     | Utilisateur MySQL                      | root                 |
| `DB_PASSWORD` | Mot de passe MySQL                     | (vide)               |
| `APP_ENV`     | Environnement (development/production) | development          |
| `APP_DEBUG`   | Mode debug (true/false)                | true                 |
| `APP_URL`     | URL de l'application                   | http://localhost/CRL |

## Installation

### 1. Base de Données

```bash
mysql -u root -p < backend/schema.sql
```

### 2. Configuration Apache

Éditer `httpd.conf`:

```apache
<Directory "/xampp/htdocs/CRL/backend/public">
    AllowOverride All
    Require all granted

    <IfModule mod_rewrite.c>
        RewriteEngine On
        RewriteCond %{REQUEST_FILENAME} !-f
        RewriteCond %{REQUEST_FILENAME} !-d
        RewriteRule ^ index.php [QSA,L]
    </IfModule>
</Directory>
```

### 3. Permissions

```bash
chmod 755 backend/public
chmod 644 backend/public/index.php
chmod 755 frontend
```

## Déploiement Production

### Checklist

- [ ] `APP_DEBUG=false`
- [ ] Activer HTTPS
- [ ] Configurer CORS strictement
- [ ] Sauvegarder la base de données
- [ ] Configurer les logs
- [ ] Tester les endpoints
- [ ] Configurer les emails (si applicable)

### SSL/HTTPS

```apache
<VirtualHost *:443>
    ServerName example.com
    SSLEngine on
    SSLCertificateFile /path/to/cert.crt
    SSLCertificateKeyFile /path/to/key.key

    DocumentRoot /path/to/CRL/frontend
</VirtualHost>
```

## Sauvegarde

```bash
mysqldump -u root -p crl_db > backup_$(date +%Y%m%d).sql
```

## Logs

Les erreurs sont loggées dans:

- Apache: `/var/log/apache2/error.log` (Linux) ou `logs/error.log` (XAMPP)
- PHP: Voir `php.ini`
