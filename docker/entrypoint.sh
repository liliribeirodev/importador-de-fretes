#!/bin/sh
set -e

chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache || true
chmod -R ug+rwx /var/www/html/storage /var/www/html/bootstrap/cache || true

if [ -d /var/www/html/storage/app/uploads ]; then
  chown -R www-data:www-data /var/www/html/storage/app/uploads || true
  chmod -R ug+rwx /var/www/html/storage/app/uploads || true
fi


cd /var/www/html || exit 1

if [ ! -f .env ] && [ -f .env.example ]; then
  cp .env.example .env
fi

if [ ! -d vendor ] || [ ! -f vendor/autoload.php ]; then
  composer install --no-interaction --prefer-dist --optimize-autoloader || true
fi

if ! grep -q "^APP_KEY=\S" .env 2>/dev/null; then
  php artisan key:generate --ansi --force || true
fi

MAX_TRIES=30
TRY=0
until php artisan migrate --force --no-interaction; do
  TRY=$((TRY+1))
  echo "Waiting for DB to be ready... (attempt: $TRY)"
  sleep 3
  if [ "$TRY" -ge "$MAX_TRIES" ]; then
    echo "Max attempts reached for migrations; continuing without migrating."
    break
  fi
done

php artisan db:seed --force --no-interaction || true

if [ ! -L public/storage ]; then
  php artisan storage:link || true
fi

exec "$@"
