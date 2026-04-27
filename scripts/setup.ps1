# Setup CRL Project
# Script PowerShell pour initialiser le projet CRL

Write-Host ""
Write-Host "  ================================================" -ForegroundColor Cyan
Write-Host "  CRL - Initialisation du Projet" -ForegroundColor Cyan
Write-Host "  ================================================" -ForegroundColor Cyan
Write-Host ""

# Vérifier que PHP existe
try {
    $phpVersion = php --version 2>$null
    if ($LASTEXITCODE -eq 0) {
        Write-Host "✅ PHP trouvé" -ForegroundColor Green
        Write-Host "   $($phpVersion.Split([Environment]::NewLine)[0])" -ForegroundColor Gray
    } else {
        Write-Host "❌ PHP n'est pas accessible" -ForegroundColor Red
        exit 1
    }
} catch {
    Write-Host "❌ PHP n'est pas trouvé dans le PATH" -ForegroundColor Red
    exit 1
}

Write-Host ""

# Initialiser la base de données
Write-Host "📦 Initialisation de la base de données..." -ForegroundColor Yellow
php scripts/init-db.php

if ($LASTEXITCODE -ne 0) {
    Write-Host ""
    Write-Host "❌ Erreur lors de l'initialisation" -ForegroundColor Red
    exit 1
}

Write-Host ""
Write-Host "  ================================================" -ForegroundColor Cyan
Write-Host "  ✅ Initialisation Terminée!" -ForegroundColor Cyan
Write-Host "  ================================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "🌐 Application disponible à:" -ForegroundColor Green
Write-Host "   http://localhost/CRL/frontend/login.html" -ForegroundColor Cyan
Write-Host ""
Write-Host "📝 Identifiants de test:" -ForegroundColor Green
Write-Host "   • Username: admin" -ForegroundColor Gray
Write-Host "   • Password: Admin@123" -ForegroundColor Gray
Write-Host ""
Write-Host "📚 Documentation:" -ForegroundColor Green
Write-Host "   • QUICK_START.md - Guide de démarrage" -ForegroundColor Gray
Write-Host "   • DEVELOPMENT.md - Guide de développement" -ForegroundColor Gray
Write-Host "   • API.md - Documentation API" -ForegroundColor Gray
Write-Host ""
