#!/bin/bash

# ============================================
# AITechHub Store - Shared Hosting Deployment
# ============================================
# Deploy Laravel customer app to Hostinger shared hosting
# Last Updated: 2025-10-09

set -e

# Colors
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
BLUE='\033[0;34m'
NC='\033[0m'

# Hostinger FTP/SFTP details
FTP_HOST="72.60.238.18"
FTP_USER="u631122123"
FTP_PASS="Sasinikhilesh\$03"
FTP_PORT="21"
DOMAIN="aitechhub.store"
REMOTE_DIR="/public_html"

echo -e "${BLUE}============================================${NC}"
echo -e "${BLUE}  AITechHub - Shared Hosting Deployment${NC}"
echo -e "${BLUE}============================================${NC}"
echo ""

print_success() {
    echo -e "${GREEN}✓ $1${NC}"
}

print_warning() {
    echo -e "${YELLOW}⚠ $1${NC}"
}

print_error() {
    echo -e "${RED}✗ $1${NC}"
}

print_info() {
    echo -e "${BLUE}ℹ $1${NC}"
}

# ============================================
# Step 1: Build Customer Application
# ============================================
echo "Step 1: Building customer application for production..."

cd customer

# Install PHP dependencies
print_info "Installing Composer dependencies..."
if ! command -v composer &> /dev/null; then
    print_error "Composer not found. Please install Composer first."
    exit 1
fi

composer install --no-dev --optimize-autoloader --no-interaction
print_success "Composer dependencies installed"

# Install Node dependencies and build assets
print_info "Installing NPM dependencies..."
if ! command -v npm &> /dev/null; then
    print_error "NPM not found. Please install Node.js first."
    exit 1
fi

npm ci --production=false
print_success "NPM dependencies installed"

print_info "Building frontend assets..."
npm run build
print_success "Frontend assets built"

cd ..
echo ""

# ============================================
# Step 2: Prepare Deployment Package
# ============================================
echo "Step 2: Preparing deployment package..."

# Create deployment directory
rm -rf deploy
mkdir -p deploy

print_info "Copying files to deployment package..."

# Copy customer app files
rsync -av --progress \
    --exclude='.git' \
    --exclude='.env' \
    --exclude='node_modules' \
    --exclude='tests' \
    --exclude='storage/logs/*' \
    --exclude='storage/framework/cache/*' \
    --exclude='storage/framework/sessions/*' \
    --exclude='storage/framework/views/*' \
    --exclude='.editorconfig' \
    --exclude='.gitattributes' \
    --exclude='.gitignore' \
    --exclude='phpunit.xml' \
    --exclude='package.json' \
    --exclude='package-lock.json' \
    --exclude='vite.config.js' \
    customer/ deploy/

print_success "Deployment package prepared"

# Create necessary directories with proper permissions
mkdir -p deploy/storage/framework/{cache,sessions,views}
mkdir -p deploy/storage/logs
mkdir -p deploy/bootstrap/cache

print_success "Storage directories created"
echo ""

# ============================================
# Step 3: Create .htaccess for Laravel
# ============================================
echo "Step 3: Creating .htaccess configuration..."

cat > deploy/public/.htaccess << 'HTACCESS'
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    # Redirect Trailing Slashes If Not A Folder...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    # Send Requests To Front Controller...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
HTACCESS

print_success ".htaccess created"
echo ""

# ============================================
# Step 4: Create .env.example for manual setup
# ============================================
echo "Step 4: Creating environment configuration template..."

cat > deploy/.env.hostinger << 'ENVFILE'
APP_NAME="AITechHub Store"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://aitechhub.store

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=u631122123_aitechhub
DB_USERNAME=u631122123_dbuser
DB_PASSWORD=YourDatabasePassword

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

MEMCACHED_HOST=127.0.0.1

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=smtp.hostinger.com
MAIL_PORT=587
MAIL_USERNAME=noreply@aitechhub.store
MAIL_PASSWORD=
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@aitechhub.store"
MAIL_FROM_NAME="${APP_NAME}"
ENVFILE

print_success "Environment template created (.env.hostinger)"
echo ""

# ============================================
# Step 5: Create deployment instructions
# ============================================
cat > deploy/DEPLOYMENT_INSTRUCTIONS.txt << 'INSTRUCTIONS'
╔════════════════════════════════════════════════════════════════╗
║         AITechHub Store - Hostinger Deployment Steps           ║
╚════════════════════════════════════════════════════════════════╝

FILES READY FOR UPLOAD!

STEP 1: Upload Files via File Manager or FTP
────────────────────────────────────────────────────────────────
Upload all files in the 'deploy' folder to: /public_html/

Using Hostinger File Manager:
1. Login to Hostinger hPanel
2. Go to: Files → File Manager
3. Navigate to /public_html/
4. Upload all files from 'deploy' folder
5. Extract if uploaded as ZIP

Using FTP:
- Host: ftp.aitechhub.store (or 72.60.238.18)
- Username: u631122123
- Password: Sasinikhilesh$03
- Port: 21


STEP 2: Set Document Root
────────────────────────────────────────────────────────────────
IMPORTANT: Point domain to the 'public' folder!

1. Login to Hostinger hPanel
2. Go to: Websites → Manage → Advanced
3. Set Document Root to: /public_html/public
4. Save changes


STEP 3: Create Database
────────────────────────────────────────────────────────────────
1. In hPanel, go to: Databases → MySQL Databases
2. Click "Create Database"
3. Database Name: u631122123_aitechhub
4. Click "Create"
5. Create Database User:
   - Username: u631122123_dbuser
   - Password: [Generate strong password]
6. Add user to database with ALL PRIVILEGES


STEP 4: Configure Environment File
────────────────────────────────────────────────────────────────
1. In File Manager, rename .env.hostinger to .env
2. Edit .env file:
   - Set DB_DATABASE to your database name
   - Set DB_USERNAME to your database user
   - Set DB_PASSWORD to your database password
3. Generate APP_KEY:
   - Go to: Advanced → SSH Access
   - Run: cd /public_html && php artisan key:generate
   OR manually generate:
   - Use: https://generate-random.org/laravel-key-generator
   - Add to .env: APP_KEY=base64:YOUR_GENERATED_KEY


STEP 5: Set Permissions
────────────────────────────────────────────────────────────────
Set these folder permissions to 755:
- storage/
- storage/framework/
- storage/logs/
- bootstrap/cache/


STEP 6: Run Migrations
────────────────────────────────────────────────────────────────
If SSH access available:
cd /public_html && php artisan migrate --force

OR use Hostinger's built-in PHP Terminal:
1. Go to: Advanced → PHP Terminal (if available)
2. Run: php artisan migrate --force


STEP 7: Enable SSL Certificate
────────────────────────────────────────────────────────────────
1. In hPanel, go to: Security → SSL
2. Enable SSL for aitechhub.store
3. Hostinger provides free SSL (Let's Encrypt)
4. Wait 5-10 minutes for activation


STEP 8: Test Your Site
────────────────────────────────────────────────────────────────
Visit: https://aitechhub.store

Expected: Homepage should load correctly


TROUBLESHOOTING:
────────────────────────────────────────────────────────────────
Issue: 500 Internal Server Error
Fix:
- Check .env file exists and is configured
- Verify APP_KEY is set
- Check folder permissions (755 for storage)
- Check error logs in: storage/logs/laravel.log

Issue: Database Connection Error
Fix:
- Verify database credentials in .env
- Check database exists in Hostinger panel
- Verify user has permissions

Issue: Blank Page
Fix:
- Check document root points to /public_html/public
- Enable error display temporarily in .env: APP_DEBUG=true
- Check PHP version (should be 8.2 or higher)


SUPPORT:
────────────────────────────────────────────────────────────────
- Check storage/logs/laravel.log for errors
- Hostinger Support: https://www.hostinger.com/contact

═══════════════════════════════════════════════════════════════

Deployment prepared successfully!
Next: Upload files and follow steps above.
INSTRUCTIONS

print_success "Deployment instructions created"
echo ""

# ============================================
# Step 6: Upload via LFTP (Optional)
# ============================================
echo "Step 6: Upload files to Hostinger..."
print_warning "This will upload ~500MB and may take 10-20 minutes"

read -p "Upload now via FTP? (yes/no): " UPLOAD_NOW

if [ "$UPLOAD_NOW" = "yes" ]; then
    if ! command -v lftp &> /dev/null; then
        print_info "Installing lftp..."
        brew install lftp
    fi

    print_info "Uploading files to Hostinger..."

    lftp -u $FTP_USER,$FTP_PASS -e "
        set ftp:ssl-allow no;
        set ssl:verify-certificate no;
        open $FTP_HOST;
        mirror --reverse --delete --verbose --exclude .git/ --exclude storage/logs/ --exclude .env deploy/ $REMOTE_DIR/;
        bye
    "

    if [ $? -eq 0 ]; then
        print_success "Files uploaded successfully!"
    else
        print_error "Upload failed. Please upload manually."
    fi
else
    print_info "Skipping automatic upload"
    print_info "You can upload manually using File Manager or FTP"
fi

echo ""

# ============================================
# Summary
# ============================================
echo -e "${BLUE}============================================${NC}"
echo -e "${BLUE}  Deployment Summary${NC}"
echo -e "${BLUE}============================================${NC}"
echo ""
echo "✅ Customer application built"
echo "✅ Deployment package prepared in: ./deploy/"
echo "✅ Configuration files created"
echo "✅ Deployment instructions created"
echo ""
print_warning "NEXT STEPS:"
echo "1. Read: ./deploy/DEPLOYMENT_INSTRUCTIONS.txt"
echo "2. Upload files to Hostinger"
echo "3. Configure database in Hostinger panel"
echo "4. Set .env file with database credentials"
echo "5. Set document root to: /public_html/public"
echo "6. Run migrations"
echo "7. Enable SSL"
echo ""
print_info "Your site will be live at: https://aitechhub.store"
echo ""

# Cleanup option
read -p "Keep deployment package? (yes/no): " KEEP_DEPLOY
if [ "$KEEP_DEPLOY" != "yes" ]; then
    rm -rf deploy
    print_success "Deployment package cleaned up"
fi

print_success "Deployment preparation complete!"
