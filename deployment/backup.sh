#!/usr/bin/env bash
# ==============================================================================
# Sukhi Manush - Daily Automated Database & Uploads Backup Script
# ==============================================================================
# Suggested Cron (runs every midnight at 02:00 AM):
#   0 2 * * * /bin/bash /var/www/sukhimanush/deployment/backup.sh >> /var/log/sukhimanush_backup.log 2>&1
# ==============================================================================

set -euo pipefail

APP_DIR="${APP_DIR:-/var/www/sukhimanush}"
BACKUP_DIR="${BACKUP_DIR:-/var/backups/sukhimanush}"
TIMESTAMP="$(date +'%Y%m%d_%H%M%S')"
RETENTION_DAYS=14

mkdir -p "${BACKUP_DIR}"

# Source environment variables if .env exists
if [ -f "${APP_DIR}/.env" ]; then
    DB_HOST=$(grep "^DB_HOST=" "${APP_DIR}/.env" | cut -d '=' -f2- | tr -d '"' | tr -d "'")
    DB_PORT=$(grep "^DB_PORT=" "${APP_DIR}/.env" | cut -d '=' -f2- | tr -d '"' | tr -d "'")
    DB_DATABASE=$(grep "^DB_DATABASE=" "${APP_DIR}/.env" | cut -d '=' -f2- | tr -d '"' | tr -d "'")
    DB_USERNAME=$(grep "^DB_USERNAME=" "${APP_DIR}/.env" | cut -d '=' -f2- | tr -d '"' | tr -d "'")
    DB_PASSWORD=$(grep "^DB_PASSWORD=" "${APP_DIR}/.env" | cut -d '=' -f2- | tr -d '"' | tr -d "'")
fi

echo "=========================================================="
echo " Starting Backup: ${TIMESTAMP}"
echo " Database: ${DB_DATABASE:-novena}"
echo "=========================================================="

# 1. Dump MySQL Database (compressed)
SQL_DUMP="${BACKUP_DIR}/db_${DB_DATABASE}_${TIMESTAMP}.sql.gz"
echo "-> Backing up database to ${SQL_DUMP}..."
mysqldump --single-transaction --quick \
    -h "${DB_HOST:-127.0.0.1}" \
    -P "${DB_PORT:-3306}" \
    -u "${DB_USERNAME:-root}" \
    -p"${DB_PASSWORD:-}" \
    "${DB_DATABASE:-novena}" | gzip -9 > "${SQL_DUMP}"

# 2. Archive Private Patient Reports & Storage
STORAGE_ARCHIVE="${BACKUP_DIR}/storage_${TIMESTAMP}.tar.gz"
echo "-> Archiving patient reports & storage to ${STORAGE_ARCHIVE}..."
tar -czf "${STORAGE_ARCHIVE}" -C "${APP_DIR}" storage/app

# 3. Rotate Old Backups (> RETENTION_DAYS)
echo "-> Cleaning up backups older than ${RETENTION_DAYS} days..."
find "${BACKUP_DIR}" -type f -name "*.gz" -mtime +${RETENTION_DAYS} -delete

echo "=========================================================="
echo " Backup Completed Successfully!"
echo " Disk Usage:"
ls -lh "${BACKUP_DIR}"/*"${TIMESTAMP}"*
echo "=========================================================="
