# Projet Laravel Inertia

## Installation

### Clonage du projet
```bash
git clone https://github.com/Pareilart/Ynov-PixelWar.git
cd votre-projet
```

### Installation des dépendances
```bash
composer install
npm install
```

### Configuration de l'environnement
```bash
cp .env.example .env
php artisan key:generate
```

### Configuration de la base de données
Éditez `.env` avec vos paramètres de base de données :
```
DB_CONNECTION=sqlite
```

### Migrations de base de données
```bash
php artisan migrate
```

### Script de démarrage `start.sh`
```bash
#!/bin/bash

# Arrête les processus existants
pkill -f "php artisan serve"
pkill -f "npm run dev"

# Lance le serveur PHP
php artisan serve --port=8000 &

# Lance le build frontend
npm run dev &

# Affiche un message de confirmation
echo "Serveur Laravel Inertia démarré sur http://localhost:8000"
```

### Utilisation du script
```bash
chmod +x start.sh
./start.sh
```

## Commandes importantes

### Développement
- `php artisan serve` : Lance le serveur de développement
- `npm run dev` : Compile les assets en mode développement
- `npm run build` : Prépare les assets pour la production

### Base de données
- `php artisan migrate` : Exécute les migrations
- `php artisan migrate:fresh` : Réinitialise et re-migre la base
- `php artisan migrate:rollback` : Annule la dernière migration

### Autres
- `php artisan make:model NomModel` : Crée un nouveau modèle
- `php artisan make:controller NomController` : Crée un nouveau contrôleur
- `php artisan route:list` : Liste toutes les routes

## Déploiement
1. `composer install --optimize-autoloader --no-dev`
2. `npm run build`
3. Configurez votre serveur web (Nginx/Apache)

## Troubleshooting
- Vérifiez les permissions des dossiers `storage` et `bootstrap/cache`
- Assurez-vous que tous les prérequis sont installés
- Consultez les logs dans `storage/logs`