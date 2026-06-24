#!/usr/bin/env bash
# Yaiza Translate — deploy helper for the VPS (CyberPanel / OpenLiteSpeed, PHP 8.3).
# Run from the project root on the server: ./deploy.sh
set -euo pipefail

PHP=${PHP:-/usr/local/lsws/lsphp83/bin/php}
COMPOSER=${COMPOSER:-/usr/local/bin/composer}

echo "→ git pull"
git pull origin "$(git rev-parse --abbrev-ref HEAD)"

echo "→ composer install (no-dev)"
"$PHP" "$COMPOSER" install --no-dev --optimize-autoloader

echo "→ migrate"
"$PHP" artisan migrate --force

# First deploy only: seed plans/languages/admin (safe to re-run, uses updateOrCreate)
echo "→ seed (idempotent)"
"$PHP" artisan db:seed --force || true

echo "→ storage link"
"$PHP" artisan storage:link || true

echo "→ rebuild caches"
"$PHP" artisan optimize:clear
"$PHP" artisan config:cache
"$PHP" artisan route:cache
"$PHP" artisan view:cache

# Permissions: if artisan ran as root, hand ownership back to the site user.
OWNER=$(stat -c '%U' "$(pwd)/..")
chown -R "$OWNER:$OWNER" storage bootstrap/cache || true
chmod -R 775 storage bootstrap/cache || true

echo "✓ Deploy complete. Hard refresh with Ctrl+F5."
