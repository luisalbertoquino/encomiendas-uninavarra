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

if [ -f package.json ] && command -v npm >/dev/null 2>&1; then
  echo "== npm install & build =="
  # Cache fuera de /var/www: ese directorio es inmutable (chattr +i) y bloquea crear /var/www/.npm
  npm install --cache /tmp/npm-cache-www-data
  npm run build --cache /tmp/npm-cache-www-data
else
  echo "== npm no disponible en el servidor: se omite build de assets (compilar en local y subir public/build) =="
fi

echo "== migraciones =="
php artisan migrate --force

echo "== cache =="
php artisan config:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "Deploy completo."
