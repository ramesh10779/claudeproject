# Shared Hosting Deployment Guide

## AITechHub Store - Hostinger Shared Hosting

**Last Updated:** 2025-10-09
**Hosting Type:** Shared Hosting (PHP/MySQL)
**Domain:** aitechhub.store

---

## 📋 Table of Contents

1. [Overview](#overview)
2. [What We're Deploying](#what-were-deploying)
3. [Prerequisites](#prerequisites)
4. [Quick Deployment](#quick-deployment)
5. [Manual Deployment Steps](#manual-deployment-steps)
6. [Database Setup](#database-setup)
7. [Environment Configuration](#environment-configuration)
8. [SSL Certificate](#ssl-certificate)
9. [Post-Deployment](#post-deployment)
10. [Troubleshooting](#troubleshooting)

---

## 🎯 Overview

Since Hostinger shared hosting doesn't support Docker, we're deploying the Laravel customer application directly using:

- ✅ Laravel 12 (Customer Frontend)
- ✅ MySQL Database (Hostinger)
- ✅ File-based sessions and cache
- ✅ FTP/File Manager upload
- ✅ Free SSL certificate

**Note:** Admin app will connect to same database but runs separately (can be deployed to subdomain if needed).

---

## 📦 What We're Deploying

### Customer Frontend Application
- **Framework:** Laravel 12
- **Features:** Product catalog, shopping cart, checkout, reviews, orders
- **Database:** MySQL
- **URL:** https://aitechhub.store

### File Structure After Deployment
```
/public_html/
├── app/
├── bootstrap/
├── config/
├── database/
├── public/          ← Document root points here
│   ├── index.php
│   ├── .htaccess
│   └── assets/
├── resources/
├── routes/
├── storage/
├── vendor/
├── .env            ← Configuration file
└── artisan
```

---

## 📋 Prerequisites

### Local Machine
- Composer installed
- Node.js and NPM installed
- FTP client (or use Hostinger File Manager)

### Hostinger Account
- Active hosting plan
- Domain: aitechhub.store
- FTP access credentials
- MySQL database access

---

## 🚀 Quick Deployment

### Option 1: Automated Script (Recommended)

```bash
cd /Users/rameshgnanasekaran/Documents/aitechhub-store
./deploy-shared-hosting.sh
```

This script will:
1. Build customer application
2. Install dependencies
3. Compile assets
4. Create deployment package
5. Generate configuration files
6. Optionally upload via FTP

**Duration:** 10-15 minutes

### Option 2: Manual Deployment

Follow the detailed steps below.

---

## 📝 Manual Deployment Steps

### Step 1: Build Application Locally

```bash
cd /Users/rameshgnanasekaran/Documents/aitechhub-store/customer

# Install PHP dependencies
composer install --no-dev --optimize-autoloader

# Install Node dependencies
npm ci

# Build frontend assets
npm run build
```

### Step 2: Prepare Deployment Package

```bash
cd ..

# Create deployment folder
mkdir -p deploy

# Copy files (excluding unnecessary ones)
rsync -av \
    --exclude='.git' \
    --exclude='node_modules' \
    --exclude='tests' \
    --exclude='.env' \
    customer/ deploy/

# Create storage directories
mkdir -p deploy/storage/framework/{cache,sessions,views}
mkdir -p deploy/storage/logs
mkdir -p deploy/bootstrap/cache
```

### Step 3: Upload to Hostinger

**Method A: File Manager (Easiest)**

1. Login to Hostinger hPanel: https://hpanel.hostinger.com
2. Go to: **Files → File Manager**
3. Navigate to `/public_html/`
4. Upload all files from `deploy/` folder
5. If uploaded as ZIP, extract it

**Method B: FTP Client (FileZilla)**

1. Download FileZilla: https://filezilla-project.org/
2. Connect with these details:
   - **Host:** ftp.aitechhub.store (or 72.60.238.18)
   - **Username:** u631122123
   - **Password:** Sasinikhilesh$03
   - **Port:** 21
3. Upload all files from `deploy/` to `/public_html/`

**Method C: Command Line FTP**

```bash
lftp -u u631122123,Sasinikhilesh\$03 ftp://72.60.238.18 << 'FTPCOMMANDS'
mirror --reverse deploy/ /public_html/
bye
FTPCOMMANDS
```

---

## 🗄️ Database Setup

### Step 1: Create Database

1. Login to Hostinger hPanel
2. Go to: **Databases → MySQL Databases**
3. Click **"Create Database"**
4. Database Name: `u631122123_aitechhub`
5. Click **"Create"**

### Step 2: Create Database User

1. In MySQL Databases section
2. Find **"MySQL Users"**
3. Click **"Create User"**
4. Username: `u631122123_dbuser`
5. Password: Generate strong password (save it!)
6. Click **"Create User"**

### Step 3: Assign User to Database

1. Find **"Add User To Database"**
2. Select user: `u631122123_dbuser`
3. Select database: `u631122123_aitechhub`
4. Grant **ALL PRIVILEGES**
5. Click **"Add"**

**Save these credentials - you'll need them for .env file!**

---

## ⚙️ Environment Configuration

### Step 1: Set Document Root

**CRITICAL:** Laravel requires document root to point to `public` folder!

1. In hPanel, go to: **Websites → Manage**
2. Find **"Advanced"** section
3. Click **"Document Root"** or **"Website Settings"**
4. Set path to: `/public_html/public`
5. Save changes

### Step 2: Create .env File

1. In File Manager, navigate to `/public_html/`
2. Create new file: `.env`
3. Add this configuration:

```env
APP_NAME="AITechHub Store"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://aitechhub.store

LOG_CHANNEL=stack
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=u631122123_aitechhub
DB_USERNAME=u631122123_dbuser
DB_PASSWORD=YOUR_DATABASE_PASSWORD_HERE

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

MAIL_MAILER=smtp
MAIL_HOST=smtp.hostinger.com
MAIL_PORT=587
MAIL_USERNAME=noreply@aitechhub.store
MAIL_PASSWORD=
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@aitechhub.store"
MAIL_FROM_NAME="${APP_NAME}"
```

### Step 3: Generate Application Key

**Method A: SSH (if available)**
```bash
cd /public_html
php artisan key:generate
```

**Method B: Manual Generation**
1. Visit: https://generate-random.org/laravel-key-generator
2. Copy the generated key (including `base64:`)
3. Add to .env: `APP_KEY=base64:YOUR_GENERATED_KEY`

### Step 4: Set Permissions

Set folder permissions to **755**:
- `storage/`
- `storage/framework/`
- `storage/logs/`
- `bootstrap/cache/`

In File Manager:
1. Right-click folder
2. Select "Permissions" or "Change Permissions"
3. Set to: 755

---

## 🔒 SSL Certificate

### Enable Free SSL

1. In hPanel, go to: **Security → SSL**
2. Find your domain: **aitechhub.store**
3. Click **"Enable SSL"** or **"Install SSL"**
4. Hostinger will install free Let's Encrypt SSL
5. Wait 5-10 minutes for activation

### Force HTTPS

Add to `public/.htaccess` (after `RewriteEngine On`):

```apache
# Force HTTPS
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
```

---

## 🚀 Post-Deployment

### Step 1: Run Database Migrations

**If SSH available:**
```bash
cd /public_html
php artisan migrate --force
```

**If no SSH:**
- Use Hostinger's PHP Terminal (Advanced → Terminal)
- Or manually import SQL files via phpMyAdmin

### Step 2: Clear Caches

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Step 3: Seed Database (Optional)

```bash
php artisan db:seed --class=CategorySeeder
php artisan db:seed --class=ProductSeeder
```

### Step 4: Test Your Site

Visit: **https://aitechhub.store**

Expected result:
- ✅ Homepage loads
- ✅ Products display
- ✅ No errors
- ✅ SSL certificate active (padlock icon)

---

## 🔧 Troubleshooting

### Issue: 500 Internal Server Error

**Causes & Solutions:**

1. **Missing .env file**
   - Create .env file with proper configuration
   - Check file exists in `/public_html/`

2. **No APP_KEY**
   - Generate key: `php artisan key:generate`
   - Or add manually from generator

3. **Wrong permissions**
   - Set storage/ to 755
   - Set bootstrap/cache/ to 755

4. **Document root not set**
   - Verify document root points to `/public_html/public`

### Issue: Database Connection Error

**Solutions:**

1. Check .env database credentials:
   ```
   DB_HOST=localhost
   DB_DATABASE=u631122123_aitechhub
   DB_USERNAME=u631122123_dbuser
   DB_PASSWORD=correct_password
   ```

2. Verify database exists in Hostinger panel

3. Verify user has permissions on database

4. Test connection from PHP:
   ```php
   <?php
   $conn = mysqli_connect('localhost', 'u631122123_dbuser', 'password', 'u631122123_aitechhub');
   if ($conn) echo "Connected!";
   ?>
   ```

### Issue: Blank White Page

**Solutions:**

1. Enable debug mode temporarily:
   ```env
   APP_DEBUG=true
   ```

2. Check PHP version:
   - Should be 8.2 or higher
   - Set in Hostinger panel: Advanced → PHP Configuration

3. Check error logs:
   - `/public_html/storage/logs/laravel.log`

4. Verify document root points to `public` folder

### Issue: Assets Not Loading (CSS/JS)

**Solutions:**

1. Check asset URLs in browser console

2. Verify APP_URL in .env:
   ```env
   APP_URL=https://aitechhub.store
   ```

3. Clear cache:
   ```bash
   php artisan config:cache
   php artisan view:cache
   ```

4. Check public/build/ folder exists

### Issue: Routes Not Working (404 errors)

**Solutions:**

1. Verify `.htaccess` exists in `public/` folder

2. Check .htaccess content (should have Laravel rewrite rules)

3. Verify mod_rewrite is enabled (ask Hostinger support)

4. Clear route cache:
   ```bash
   php artisan route:clear
   php artisan route:cache
   ```

---

## 📊 Performance Optimization

### Enable OPcache

1. In hPanel: **Advanced → PHP Configuration**
2. Find **OPcache** section
3. Enable OPcache
4. Set memory: 128MB

### Enable Gzip Compression

Add to `public/.htaccess`:

```apache
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/html text/plain text/xml text/css text/javascript application/javascript
</IfModule>
```

### Browser Caching

Add to `public/.htaccess`:

```apache
<IfModule mod_expires.c>
    ExpiresActive On
    ExpiresByType image/jpg "access plus 1 year"
    ExpiresByType image/jpeg "access plus 1 year"
    ExpiresByType image/gif "access plus 1 year"
    ExpiresByType image/png "access plus 1 year"
    ExpiresByType text/css "access plus 1 month"
    ExpiresByType application/javascript "access plus 1 month"
</IfModule>
```

---

## 🔄 Updating Your Site

### Quick Update Process

1. Pull latest code locally:
   ```bash
   cd /Users/rameshgnanasekaran/Documents/aitechhub-store
   git pull origin main
   ```

2. Rebuild application:
   ```bash
   ./deploy-shared-hosting.sh
   ```

3. Upload changed files to Hostinger

4. Run migrations (if any):
   ```bash
   php artisan migrate --force
   ```

5. Clear caches:
   ```bash
   php artisan cache:clear
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

---

## 📈 Monitoring

### Check Error Logs

**Laravel Log:**
- `/public_html/storage/logs/laravel.log`

**PHP Error Log:**
- `/public_html/error_log` (if exists)

**Hostinger Error Logs:**
- Available in hPanel: Advanced → Error Logs

### Monitor Traffic

1. In hPanel: **Statistics → Website Statistics**
2. Or use Google Analytics

---

## 🔐 Security Best Practices

### 1. Keep .env Secure
```
- Never commit .env to Git
- Use strong database passwords
- Keep APP_KEY secret
```

### 2. Disable Debug Mode
```env
APP_DEBUG=false
```

### 3. Regular Updates
```bash
composer update
npm update
```

### 4. Monitor Logs
```bash
tail -f storage/logs/laravel.log
```

---

## 📞 Support Resources

### Hostinger Support
- **Email:** support@hostinger.com
- **Live Chat:** https://www.hostinger.com/contact
- **Knowledge Base:** https://support.hostinger.com

### Laravel Documentation
- **Official Docs:** https://laravel.com/docs
- **Deployment:** https://laravel.com/docs/deployment

### Repository
- **GitHub:** https://github.com/ramesh10779/claudeproject.git
- **GitLab:** https://gitlab.com/ramesh10779-group/ramesh10779-project.git

---

## ✅ Deployment Checklist

- [ ] Application built locally
- [ ] Dependencies installed (Composer + NPM)
- [ ] Assets compiled
- [ ] Files uploaded to Hostinger
- [ ] Document root set to `/public_html/public`
- [ ] Database created
- [ ] Database user created and assigned
- [ ] .env file configured
- [ ] APP_KEY generated
- [ ] Folder permissions set (755)
- [ ] Migrations run
- [ ] SSL certificate enabled
- [ ] Site accessible via HTTPS
- [ ] No errors in browser console
- [ ] All features tested

---

**Status:** Ready for Deployment
**Next Action:** Run `./deploy-shared-hosting.sh`

---

**Last Updated:** 2025-10-09
**Deployment Type:** Shared Hosting (No Docker)
**Framework:** Laravel 12
