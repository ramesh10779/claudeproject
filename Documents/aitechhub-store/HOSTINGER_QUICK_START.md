# Hostinger Quick Start Guide for aitechhub.store

## Your FTP Details ✅

```
FTP IP:       72.60.238.18
FTP Hostname: aitechhub.store (or ftp.aitechhub.store)
FTP Username: u631122123.aitechhub.store
FTP Password: [Your password from Hostinger panel]
Upload Path:  /public_html
```

## Step 1: Add GitLab CI/CD Variables (3 Required)

Go to: **Your GitLab Project → Settings → CI/CD → Variables** (expand)

### Add These 3 Variables:

**Variable 1:**
- **Key:** `HOSTINGER_FTP_HOST`
- **Value:** `72.60.238.18` (or `aitechhub.store`)
- ✅ Protect variable
- ✅ Mask variable

**Variable 2:**
- **Key:** `HOSTINGER_FTP_USER`
- **Value:** `u631122123.aitechhub.store`
- ✅ Protect variable
- ✅ Mask variable

**Variable 3:**
- **Key:** `HOSTINGER_FTP_PASS`
- **Value:** `[Your FTP password]`
- ✅ Protect variable
- ✅ Mask variable

## Step 2: Set Up Database in Hostinger

1. Login to **Hostinger Panel** (hpanel.hostinger.com)
2. Go to **Databases → MySQL Databases**
3. Click **Create Database**
4. Fill in:
   - **Database name:** `u631122123_aitechhub`
   - **Username:** `u631122123_admin`
   - **Password:** [Generate strong password]
5. **Save the credentials** - you'll need them for .env file

## Step 3: Create .env File

Create a file named `.env` in your `/public_html/` directory with these contents:

```env
APP_NAME="AITechHub Store"
APP_ENV=production
APP_KEY=base64:GENERATE_THIS_KEY_USING_ARTISAN
APP_DEBUG=false
APP_URL=https://aitechhub.store

LOG_CHANNEL=stack
LOG_LEVEL=error

# Database from Step 2
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=u631122123_aitechhub
DB_USERNAME=u631122123_admin
DB_PASSWORD=your_database_password_here

# Session & Cache
BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

# Admin API (local Docker)
ADMIN_API_URL=http://admin:8000/api

# Important: Set this in production
ADMIN_API_KEY=your-secure-api-key-here
```

### Generate APP_KEY:
After uploading files, SSH to Hostinger and run:
```bash
cd /public_html
php artisan key:generate --show
```
Copy the output and update your .env file.

## Step 4: Configure Domain Root

In **Hostinger Panel:**
1. Go to **Domains → aitechhub.store**
2. Click **Manage**
3. Set **Document Root** to: `/public_html/public` (important!)
4. Save changes

## Step 5: Deploy Using GitLab CI/CD (Recommended)

Once you've added the 3 GitLab variables:

```bash
git add .
git commit -m "Configure Hostinger deployment"
git push origin main
```

**Watch deployment:**
- Go to: GitLab Project → CI/CD → Pipelines
- You'll see the build and deploy stages
- Takes about 5-10 minutes

## Step 6: Run Database Migrations

After first deployment, SSH to Hostinger:

```bash
ssh u631122123.aitechhub.store@aitechhub.store
cd /public_html
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## Step 7: Test Your Site

Visit: **https://aitechhub.store**

You should see the customer homepage!

## Alternative: Manual Deployment

If you prefer manual deployment instead of GitLab CI/CD:

1. Update password in `deploy-to-hostinger.sh`:
   ```bash
   FTP_PASS="your_actual_password"
   ```

2. Run the script:
   ```bash
   chmod +x deploy-to-hostinger.sh
   ./deploy-to-hostinger.sh
   ```

## Project Structure on Hostinger

```
/public_html/
├── app/                    ← Laravel application
├── bootstrap/
├── config/
├── database/
├── public/                 ← Document root (web accessible)
│   ├── index.php          ← Entry point
│   ├── css/
│   ├── js/
│   └── images/
├── resources/
├── routes/
├── storage/
├── vendor/
├── .env                    ← Environment config
├── artisan                 ← CLI tool
└── composer.json
```

## Troubleshooting

### Issue: 500 Internal Server Error

**Solution:**
1. Check file permissions:
   ```bash
   chmod -R 755 /public_html
   chmod -R 775 /public_html/storage
   chmod -R 775 /public_html/bootstrap/cache
   ```

2. Check .env file exists:
   ```bash
   ls -la /public_html/.env
   ```

3. Check logs:
   ```bash
   tail -f /public_html/storage/logs/laravel.log
   ```

### Issue: Database Connection Error

**Solution:**
1. Verify database credentials in .env
2. Check database exists in Hostinger panel
3. Test connection:
   ```bash
   php artisan tinker
   DB::connection()->getPdo();
   ```

### Issue: Assets Not Loading (CSS/JS)

**Solution:**
1. Verify document root points to `/public_html/public`
2. Run:
   ```bash
   php artisan storage:link
   ```

### Issue: GitLab CI/CD Fails

**Solution:**
1. Check GitLab variables are set correctly
2. Verify FTP credentials work (test with FileZilla)
3. Check CI/CD logs in GitLab → CI/CD → Pipelines

## Security Checklist

Before going live:

- [ ] Change `APP_DEBUG=false` in .env
- [ ] Set strong `ADMIN_API_KEY` in .env
- [ ] Use strong database password
- [ ] Enable HTTPS (Force HTTPS in Hostinger panel)
- [ ] Set up automatic backups in Hostinger
- [ ] Configure firewall rules (if available)
- [ ] Set proper file permissions (755/775)

## Performance Optimization

After deployment:

```bash
cd /public_html

# Cache configuration
php artisan config:cache

# Cache routes
php artisan route:cache

# Cache views
php artisan view:cache

# Optimize autoloader
composer install --optimize-autoloader --no-dev
```

## Updating the Site

### Via GitLab CI/CD (Automatic):
```bash
git add .
git commit -m "Update feature X"
git push origin main
```
GitLab will automatically deploy!

### Via Manual Script:
```bash
./deploy-to-hostinger.sh
```

## Support Resources

- **Hostinger Support:** https://www.hostinger.com/support
- **Laravel Docs:** https://laravel.com/docs
- **Project Docs:** See README.md

## Next Steps

1. ✅ Add GitLab variables
2. ✅ Create database
3. ✅ Upload .env file
4. ✅ Set document root
5. ✅ Push to GitLab (auto-deploys)
6. ✅ Run migrations via SSH
7. ✅ Test site at https://aitechhub.store

**Your deployment is configured and ready! 🚀**
