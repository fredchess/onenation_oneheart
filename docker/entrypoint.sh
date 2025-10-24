#!/bin/sh
set -e

echo "🚀 Initialisation de Laravel..."

php artisan optimize
php artisan migrate --force
php artisan storage:link

# Exécute la commande passée au container
exec "$@"