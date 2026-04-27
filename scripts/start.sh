#!/bin/bash
# Script de démarrage rapide pour développement local

echo "🚀 Démarrage du serveur CRL..."
echo ""

# Vérifier que PHP existe
if ! command -v php &> /dev/null; then
    echo "❌ PHP n'est pas installé ou n'est pas dans le PATH"
    exit 1
fi

# Vérifier que MySQL existe
if ! command -v mysql &> /dev/null; then
    echo "⚠️  MySQL n'est pas dans le PATH (ce n'est pas grave si vous utilisez XAMPP)"
fi

echo "✅ PHP trouvé: $(php --version | head -n 1)"
echo ""

# Initialiser la base de données si nécessaire
echo "📦 Préparation de la base de données..."
php scripts/init-db.php

echo ""
echo "✅ Initialisation terminée!"
echo ""
echo "🌐 Application disponible à:"
echo "   http://localhost/CRL/frontend/login.html"
echo ""
echo "📝 Utilisateur de test:"
echo "   • Username: admin"
echo "   • Password: Admin@123"
echo ""
echo "📚 Documentation:"
echo "   • QUICK_START.md - Guide de démarrage"
echo "   • DEVELOPMENT.md - Guide de développement"
echo "   • API.md - Documentation API"
echo ""
