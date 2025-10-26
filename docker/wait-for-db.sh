#!/bin/sh
set -e

MAX_TRIES=${MAX_TRIES:-30}
TRY=0

until php artisan migrate:status --no-interaction >/dev/null 2>&1; do
  TRY=$((TRY+1))
  echo "Waiting for DB to be ready... (attempt: $TRY)"
  sleep 3
  if [ "$TRY" -ge "$MAX_TRIES" ]; then
    echo "Max attempts reached waiting for DB; continuing anyway."
    break
  fi
done

exec "$@"
