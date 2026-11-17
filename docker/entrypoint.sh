#!/bin/sh
set -e

echo "🚀 Initialisation de Laravel..."

php artisan optimize
php artisan migrate --force
php artisan storage:link

# Index de recherche Typesense (upsert idempotent ; n'empêche pas le démarrage si Typesense est indisponible)
if [ "$SCOUT_DRIVER" = "typesense" ]; then
    php artisan scout:import "App\Models\Orphanage" || echo "⚠️  Import Typesense impossible, la recherche utilisera MySQL."
fi

# Exécute la commande passée au container
exec "$@"