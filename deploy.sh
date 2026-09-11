#!/bin/bash
# Script de deploy para el servidor de producción.
# Correr desde /var/www/encomiendas como usuario webmaster (con sudo si los permisos de archivo lo requieren).
# Uso: ./deploy.sh

set -e

cd "$(dirname "$0")"

echo "== git pull =="
git pull origin main

echo "== composer install =="
composer install --optimize-autoloader --no-dev

if [ -f package.json ]; then
  echo "== npm install & build =="
  npm install
  npm run build
fi

echo "== migraciones =="
php artisan migrate --force

echo "== cache =="
php artisan config:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "Deploy completo."
