#!/usr/bin/env bash
# ==============================================================================
# Sukhi Manush - Production Emergency Rollback Script
# ==============================================================================
# Usage:
#   bash deployment/rollback.sh [target_git_commit_or_tag]
# Example:
#   bash deployment/rollback.sh HEAD~1
#   bash deployment/rollback.sh v1.0.4
# ==============================================================================

set -euo pipefail

APP_DIR="${APP_DIR:-/var/www/sukhimanush}"
TARGET="${1:-HEAD~1}"
PHP_BIN="${PHP_BIN:-php}"
COMPOSER_BIN="${COMPOSER_BIN:-composer}"
NPM_BIN="${NPM_BIN:-npm}"

echo "=========================================================="
echo " Emergency Rollback Initiated -> Rolling back to: ${TARGET}"
echo " Timestamp: $(date '+%Y-%m-%d %H:%M:%S')"
echo "=========================================================="

cd "${APP_DIR}"

# Step 1: Maintenance mode
echo "-> [1/6] Putting application into maintenance mode..."
${PHP_BIN} artisan down --retry=60 || true

# Step 2: Checkout target commit
echo "-> [2/6] Checking out target commit: ${TARGET}..."
git checkout "${TARGET}"

# Step 3: Restore dependencies & assets
echo "-> [3/6] Reinstalling dependencies & rebuilding assets..."
${COMPOSER_BIN} install --no-dev --prefer-dist --optimize-autoloader --no-interaction --quiet
${NPM_BIN} run build

# Step 4: Re-cache application
echo "-> [4/6] Re-caching configurations, routes, and views..."
${PHP_BIN} artisan optimize:clear
${PHP_BIN} artisan config:cache
${PHP_BIN} artisan route:cache
${PHP_BIN} artisan view:cache
${PHP_BIN} artisan event:cache

# Step 5: Restart workers
echo "-> [5/6] Restarting queue workers..."
${PHP_BIN} artisan queue:restart
if command -v supervisorctl &> /dev/null; then
    supervisorctl restart sukhimanush-worker:* || true
fi

# Step 6: Bring application back up
echo "-> [6/6] Bringing application back online..."
${PHP_BIN} artisan up

echo "=========================================================="
echo " Rollback completed to: $(git rev-parse --short HEAD)"
echo " Timestamp: $(date '+%Y-%m-%d %H:%M:%S')"
echo "=========================================================="
