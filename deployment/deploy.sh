#!/usr/bin/env bash
# ==============================================================================
# Sukhi Manush - Zero-Downtime Production Deployment Script
# ==============================================================================
# Usage:
#   bash deployment/deploy.sh [branch_name]
# Example:
#   bash deployment/deploy.sh main
# ==============================================================================

set -euo pipefail

# Configuration
APP_DIR="${APP_DIR:-/var/www/sukhimanush}"
DEPLOY_BRANCH="${1:-main}"
PHP_BIN="${PHP_BIN:-php}"
COMPOSER_BIN="${COMPOSER_BIN:-composer}"
NPM_BIN="${NPM_BIN:-npm}"
DEPLOY_SECRET="deploy-$(openssl rand -hex 8)"

echo "=========================================================="
echo " Starting Deployment for Sukhi Manush (${DEPLOY_BRANCH})"
echo " Timestamp: $(date '+%Y-%m-%d %H:%M:%S')"
echo "=========================================================="

cd "${APP_DIR}"

# Ensure cleanup and re-enable application if an error occurs
cleanup_on_failure() {
    local exit_code=$?
    if [ ${exit_code} -ne 0 ]; then
        echo ""
        echo " [ERROR] Deployment failed with exit code ${exit_code}! Bringing app back UP..."
        ${PHP_BIN} artisan up || true
        echo " Application brought back UP. Please inspect the log above."
    fi
    exit ${exit_code}
}
trap cleanup_on_failure EXIT

# Step 1: Put application in maintenance mode (allows bypass with secret)
echo "-> [1/9] Putting application into maintenance mode..."
${PHP_BIN} artisan down --secret="${DEPLOY_SECRET}" --retry=60 || true
echo "    Bypass URL: /${DEPLOY_SECRET}"

# Step 2: Pull latest code
echo "-> [2/9] Fetching latest changes from git (${DEPLOY_BRANCH})..."
git fetch origin "${DEPLOY_BRANCH}"
git checkout "${DEPLOY_BRANCH}"
git pull origin "${DEPLOY_BRANCH}"

# Step 3: Install PHP production dependencies
echo "-> [3/9] Installing Composer production dependencies..."
${COMPOSER_BIN} install --no-dev --prefer-dist --optimize-autoloader --no-interaction --quiet

# Step 4: Build Frontend Assets (Vite + Vue 3)
echo "-> [4/9] Building production frontend bundle..."
if [ -f "package-lock.json" ]; then
    ${NPM_BIN} ci --prefer-offline --no-audit --quiet
else
    ${NPM_BIN} install --prefer-offline --no-audit --quiet
fi
${NPM_BIN} run build

# Step 5: Run Database Migrations
echo "-> [5/9] Running database migrations..."
${PHP_BIN} artisan migrate --force

# Step 6: Optimize and Warm Application Caches
echo "-> [6/9] Clearing and warming application caches..."
${PHP_BIN} artisan optimize:clear
${PHP_BIN} artisan config:cache
${PHP_BIN} artisan route:cache
${PHP_BIN} artisan view:cache
${PHP_BIN} artisan event:cache

# Step 7: Fix Directory Permissions
echo "-> [7/9] Ensuring proper directory permissions..."
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache public/build 2>/dev/null || true

# Step 8: Restart Background Queue Workers
echo "-> [8/9] Restarting background queue workers..."
${PHP_BIN} artisan queue:restart
if command -v supervisorctl &> /dev/null; then
    supervisorctl restart sukhimanush-worker:* || true
fi

# Step 9: Bring application back online
echo "-> [9/9] Bringing application back online..."
${PHP_BIN} artisan up

# Disable error trap since deployment succeeded
trap - EXIT

# Step 10: Health Check Verification
echo ""
echo "-> Verifying application health endpoint (/up)..."
HEALTH_STATUS=$(${PHP_BIN} artisan route:list --path=up | grep -c "up" || true)
if [ "${HEALTH_STATUS}" -gt 0 ]; then
    echo "    Health route registered and ready."
fi

echo "=========================================================="
echo " Deployment Successfully Completed!"
echo " Timestamp: $(date '+%Y-%m-%d %H:%M:%S')"
echo "=========================================================="
