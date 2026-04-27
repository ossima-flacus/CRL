@echo off
REM Script de démarrage rapide pour développement local - Windows

echo.
echo   ================================================
echo   CRL - Initialisation du Projet
echo   ================================================
echo.

REM Vérifier que PHP existe
php --version >nul 2>&1
if errorlevel 1 (
    echo ❌ PHP n'est pas installé ou n'est pas dans le PATH
    pause
    exit /b 1
)

echo ✅ PHP trouvé
echo.

REM Initialiser la base de données
echo 📦 Initialisation de la base de données...
php scripts\init-db.php

if errorlevel 1 (
    echo ❌ Erreur lors de l'initialisation
    pause
    exit /b 1
)

echo.
echo   ================================================
echo   ✅ Initialisation Terminée!
echo   ================================================
echo.
echo 🌐 Application disponible à:
echo    http://localhost/CRL/frontend/login.html
echo.
echo 📝 Identifiants de test:
echo    • Username: admin
echo    • Password: Admin@123
echo.
echo 📚 Voir QUICK_START.md pour l'installation complète
echo.
pause
