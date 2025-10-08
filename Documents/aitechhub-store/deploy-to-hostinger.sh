#!/bin/bash

# Manual Deployment Script to Hostinger
# Use this if you want to deploy without GitLab CI/CD

echo "========================================"
echo "AITechHub Store - Hostinger Deployment"
echo "========================================"
echo ""

# Configuration (update these)
FTP_HOST="72.60.238.18"  # or use: aitechhub.store
FTP_USER="u631122123.aitechhub.store"
FTP_PASS="your_password_here"
FTP_DIR="/public_html"

echo "⚠️  WARNING: Update FTP credentials in this script before running!"
echo ""
read -p "Have you updated FTP credentials? (yes/no): " CONFIRMED

if [ "$CONFIRMED" != "yes" ]; then
    echo "❌ Please update FTP_HOST, FTP_USER, and FTP_PASS in this script"
    exit 1
fi

echo ""
echo "Step 1: Building customer app..."
cd customer

# Install dependencies
echo "Installing Composer dependencies..."
composer install --no-dev --optimize-autoloader --no-interaction

echo "Installing NPM dependencies..."
npm ci

echo "Building frontend assets..."
npm run build

echo "✅ Build complete!"
echo ""

# Create deployment package (exclude unnecessary files)
echo "Step 2: Creating deployment package..."
cd ..
mkdir -p deploy
rsync -av --exclude='.git' \
          --exclude='node_modules' \
          --exclude='tests' \
          --exclude='storage/logs/*' \
          --exclude='storage/framework/cache/*' \
          --exclude='.env' \
          customer/ deploy/

echo "✅ Deployment package ready!"
echo ""

# Deploy via FTP
echo "Step 3: Uploading to Hostinger via FTP..."
echo "This may take several minutes..."
echo ""

if ! command -v lftp &> /dev/null; then
    echo "⚠️  LFTP not found. Installing..."

    # Check OS
    if [[ "$OSTYPE" == "darwin"* ]]; then
        # macOS
        brew install lftp
    elif [[ "$OSTYPE" == "linux-gnu"* ]]; then
        # Linux
        sudo apt-get update && sudo apt-get install -y lftp
    else
        echo "❌ Please install LFTP manually: https://lftp.yar.ru/"
        exit 1
    fi
fi

# Upload via LFTP
lftp -u $FTP_USER,$FTP_PASS -e "
set ftp:ssl-allow no;
set ssl:verify-certificate no;
open $FTP_HOST;
mirror --reverse --delete --verbose --exclude .git/ --exclude storage/logs/ deploy/ $FTP_DIR/;
bye
"

if [ $? -eq 0 ]; then
    echo "✅ Upload complete!"
else
    echo "❌ Upload failed. Check your FTP credentials."
    exit 1
fi

echo ""
echo "========================================"
echo "🎉 Deployment Successful!"
echo "========================================"
echo ""
echo "Next steps:"
echo "1. Upload .env file manually to /public_html/.env"
echo "2. In Hostinger control panel, set document root to: /public_html/public"
echo "3. SSH to Hostinger and run:"
echo "   cd /public_html"
echo "   php artisan migrate --force"
echo "   php artisan config:cache"
echo "   php artisan route:cache"
echo ""
echo "Your site should be live at: https://aitechhub.store"
echo ""

# Cleanup
rm -rf deploy
