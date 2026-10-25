# Sukhi Manush - Production Deployment Checklist & Runbook

This document details the complete deployment, hardening, and verification steps required to take the **Sukhi Manush** healthcare platform live in production with zero downtime, optimal performance, and high availability.

---

## 1. System Requirements & Server Sizing

### Minimum Sizing (Up to 5,000 Daily Active Users)
- **OS**: Ubuntu 22.04 LTS / 24.04 LTS or Debian 12
- **CPU**: 2 vCPUs (Compute-optimized or General Purpose)
- **RAM**: 4 GB (2 GB reserved for PHP-FPM / Redis, 1.5 GB for MySQL)
- **Storage**: 50 GB NVMe SSD (with automated backup retention)

### Recommended Sizing (5,000 - 50,000 DAU)
- **CPU**: 4 vCPUs
- **RAM**: 8 GB to 16 GB
- **Database**: Managed MySQL 8.0+ (AWS RDS, DigitalOcean Managed Database, or dedicated instance)
- **Redis**: Managed Redis 7.0+ for session store, rate limits, and queue broker

---

## 2. Server Software & PHP 8.2 Configuration

### Required Packages
```bash
sudo apt update && sudo apt upgrade -y
sudo apt install -y nginx git curl unzip supervisor redis-server mysql-client \
    php8.2-fpm php8.2-cli php8.2-mysql php8.2-redis php8.2-curl php8.2-gd \
    php8.2-mbstring php8.2-xml php8.2-zip php8.2-bcmath php8.2-intl
```

### PHP.ini Directives (`/etc/php/8.2/fpm/php.ini`)
Apply the following settings for file upload handling (medical reports up to 20MB) and OPcache acceleration:
```ini
upload_max_filesize = 25M
post_max_size = 25M
memory_limit = 256M
max_execution_time = 60
max_input_time = 60
expose_php = Off

; OPcache Settings for Production
opcache.enable = 1
opcache.enable_cli = 0
opcache.memory_consumption = 128
opcache.interned_strings_buffer = 16
opcache.max_accelerated_files = 10000
opcache.revalidate_freq = 0
opcache.validate_timestamps = 0
opcache.save_comments = 1
```

Restart PHP-FPM:
```bash
sudo systemctl restart php8.2-fpm
```

---

## 3. Directory & File Permissions

The web server user (`www-data`) must have write permissions for storage and cache, but read-only permissions for application code:

```bash
cd /var/www/sukhimanush
sudo chown -R $USER:www-data .
sudo find . -type f -exec chmod 644 {} \;
sudo find . -type d -exec chmod 755 {} \;

# Writable directories for Laravel
sudo chown -R www-data:www-data storage bootstrap/cache public/build
sudo chmod -R 775 storage bootstrap/cache
```

---

## 4. Environment Variables (`.env`) Verification

Create `.env` from template:
```bash
cp .env.example .env
php artisan key:generate
```

Verify the following production values:
- [ ] `APP_ENV=production`
- [ ] `APP_DEBUG=false`
- [ ] `APP_URL=https://sukhimanush.com`
- [ ] `TRUSTED_PROXIES="*"` (or reverse proxy CIDRs)
- [ ] `LOG_CHANNEL=daily`
- [ ] `LOG_DAILY_DAYS=14`
- [ ] `LOG_LEVEL=info`
- [ ] `DB_CONNECTION=mysql` with strong password and dedicated user
- [ ] `CACHE_STORE=redis`
- [ ] `SESSION_DRIVER=redis`
- [ ] `SESSION_DOMAIN=.sukhimanush.com`
- [ ] `SESSION_SECURE_COOKIE=true`
- [ ] `QUEUE_CONNECTION=redis`
- [ ] `SANCTUM_STATEFUL_DOMAINS="sukhimanush.com,www.sukhimanush.com"`
- [ ] Production credentials for **bKash**, **Nagad**, and **SSLCommerz** (`*_SANDBOX=false`)
- [ ] Production API keys for AI services (`GEMINI_API_KEY`, etc.)

---

## 5. Background Queue Workers & Crontab

### Supervisor Queue Worker Pool
Copy the provided supervisor configuration:
```bash
sudo cp deployment/supervisor/sukhimanush-worker.conf /etc/supervisor/conf.d/
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start sukhimanush-worker:*
sudo supervisorctl status
```

### Laravel Scheduler (Crontab)
Add the scheduled task runner to the `www-data` or server crontab:
```bash
sudo crontab -u www-data -e
```
Add line:
```cron
* * * * * cd /var/www/sukhimanush && php artisan schedule:run >> /dev/null 2>&1
```

### Daily Database & Storage Backup
Set up daily backups at 02:00 AM using `deployment/backup.sh`:
```bash
sudo crontab -e
```
Add line:
```cron
0 2 * * * /bin/bash /var/www/sukhimanush/deployment/backup.sh >> /var/log/sukhimanush_backup.log 2>&1
```

---

## 6. Nginx & SSL Setup

Copy the virtual host configuration and obtain Let's Encrypt certificates:
```bash
sudo cp deployment/nginx/sukhimanush.conf /etc/nginx/sites-available/sukhimanush.conf
sudo ln -s /etc/nginx/sites-available/sukhimanush.conf /etc/nginx/sites-enabled/
sudo apt install -y certbot python3-certbot-nginx
sudo certbot --nginx -d sukhimanush.com -d www.sukhimanush.com
sudo nginx -t && sudo systemctl reload nginx
```

---

## 7. Zero-Downtime Deployment Execution

Execute the automated deployment script:
```bash
bash deployment/deploy.sh main
```

The script automatically executes:
1. `php artisan down --secret=...`
2. `git pull origin main`
3. `composer install --no-dev --optimize-autoloader`
4. `npm ci && npm run build`
5. `php artisan migrate --force`
6. `php artisan optimize:clear && php artisan config:cache && php artisan route:cache && php artisan view:cache && php artisan event:cache`
7. `php artisan queue:restart`
8. `php artisan up`

---

## 8. Post-Deployment Verification Steps

Immediately after deployment, run the following verification steps:
1. **Health Check Endpoint**:
   ```bash
   curl -I https://sukhimanush.com/up
   # Expected: HTTP/1.1 200 OK
   ```
2. **API Sanity Check**:
   ```bash
   curl -s https://sukhimanush.com/api/home-data | head -n 10
   # Expected: Valid JSON with status 200
   ```
3. **Queue Health**:
   ```bash
   php artisan queue:failed
   # Expected: No failed jobs
   ```
4. **Log Inspection**:
   ```bash
   tail -n 50 storage/logs/laravel-$(date +%F).log
   # Verify no unexpected exceptions
   ```

---

## 9. Emergency Rollback Procedure

If a critical issue occurs after release, execute:
```bash
# Roll back to the previous stable git commit
bash deployment/rollback.sh HEAD~1

# Or specify a specific release tag
bash deployment/rollback.sh v1.0.0
```
