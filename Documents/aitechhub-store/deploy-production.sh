#!/bin/bash

# ============================================
# AITechHub Store - Production Deployment
# ============================================
# Automated deployment script for production
# Last Updated: 2025-10-09

set -e

echo "============================================"
echo "  AITechHub Store - Production Deployment"
echo "============================================"
echo ""

# Colors for output
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

# ============================================
# Configuration
# ============================================
COMPOSE_FILE="docker-compose.production.yml"
ENV_FILE=".env.production"
BACKUP_DIR="./docker/database/backups"
DEPLOY_MODE="${1:-full}"

# ============================================
# Functions
# ============================================

print_success() {
    echo -e "${GREEN}✓ $1${NC}"
}

print_warning() {
    echo -e "${YELLOW}⚠ $1${NC}"
}

print_error() {
    echo -e "${RED}✗ $1${NC}"
}

# ============================================
# Pre-deployment Checks
# ============================================

echo "Step 1: Pre-deployment checks..."

# Check if .env.production exists
if [ ! -f "$ENV_FILE" ]; then
    print_error "Production environment file not found!"
    echo "Please copy .env.production.example to .env.production and configure it."
    exit 1
fi
print_success "Environment file found"

# Check if Docker is running
if ! docker info > /dev/null 2>&1; then
    print_error "Docker is not running!"
    exit 1
fi
print_success "Docker is running"

# Check if docker-compose is available
if ! command -v docker-compose &> /dev/null; then
    print_error "docker-compose is not installed!"
    exit 1
fi
print_success "docker-compose is available"

echo ""

# ============================================
# Backup Database (if exists)
# ============================================

if [ "$DEPLOY_MODE" != "fresh" ]; then
    echo "Step 2: Backing up database (if exists)..."

    if docker ps | grep -q "aitechhub-db-prod"; then
        mkdir -p "$BACKUP_DIR"
        BACKUP_FILE="$BACKUP_DIR/backup-$(date +%Y%m%d-%H%M%S).sql"

        print_warning "Creating database backup..."
        docker exec aitechhub-db-prod mysqldump \
            -u root \
            -p"${DB_ROOT_PASSWORD:-changeme_root_password}" \
            "${DB_DATABASE:-aitechhub_store}" > "$BACKUP_FILE" 2>/dev/null || true

        if [ -f "$BACKUP_FILE" ]; then
            print_success "Database backup created: $BACKUP_FILE"
        else
            print_warning "No existing database to backup"
        fi
    else
        print_warning "No existing database container found"
    fi
    echo ""
fi

# ============================================
# Build Docker Images
# ============================================

echo "Step 3: Building Docker images..."
docker-compose -f "$COMPOSE_FILE" build --no-cache
print_success "Docker images built successfully"
echo ""

# ============================================
# Stop Existing Containers
# ============================================

echo "Step 4: Stopping existing containers..."
docker-compose -f "$COMPOSE_FILE" down
print_success "Existing containers stopped"
echo ""

# ============================================
# Start Containers
# ============================================

echo "Step 5: Starting containers..."
docker-compose -f "$COMPOSE_FILE" up -d
print_success "Containers started"
echo ""

# ============================================
# Wait for Database
# ============================================

echo "Step 6: Waiting for database to be ready..."
MAX_TRIES=30
TRIES=0

while [ $TRIES -lt $MAX_TRIES ]; do
    if docker exec aitechhub-db-prod mysqladmin ping -h localhost -u root -p"${DB_ROOT_PASSWORD:-changeme_root_password}" --silent 2>/dev/null; then
        print_success "Database is ready"
        break
    fi
    TRIES=$((TRIES+1))
    echo "Waiting for database... ($TRIES/$MAX_TRIES)"
    sleep 2
done

if [ $TRIES -eq $MAX_TRIES ]; then
    print_error "Database failed to start"
    exit 1
fi
echo ""

# ============================================
# Run Migrations
# ============================================

echo "Step 7: Running database migrations..."

# Admin migrations
print_warning "Running admin migrations..."
docker exec aitechhub-admin-prod php artisan migrate --force
print_success "Admin migrations completed"

# Customer migrations
print_warning "Running customer migrations..."
docker exec aitechhub-customer-prod php artisan migrate --force
print_success "Customer migrations completed"

echo ""

# ============================================
# Clear & Optimize Caches
# ============================================

echo "Step 8: Optimizing application..."

# Admin optimization
print_warning "Optimizing admin application..."
docker exec aitechhub-admin-prod php artisan config:cache
docker exec aitechhub-admin-prod php artisan route:cache
docker exec aitechhub-admin-prod php artisan view:cache
print_success "Admin optimized"

# Customer optimization
print_warning "Optimizing customer application..."
docker exec aitechhub-customer-prod php artisan config:cache
docker exec aitechhub-customer-prod php artisan route:cache
docker exec aitechhub-customer-prod php artisan view:cache
print_success "Customer optimized"

echo ""

# ============================================
# Generate SSL Certificates
# ============================================

echo "Step 9: Generating SSL certificates..."
docker exec aitechhub-ssl-prod /usr/local/bin/generate-ssl.sh
print_success "SSL certificates generated"
echo ""

# ============================================
# Health Checks
# ============================================

echo "Step 10: Running health checks..."

# Wait for services to be fully ready
sleep 10

# Check admin health
if curl -f -s http://localhost:8001/health > /dev/null; then
    print_success "Admin backend is healthy"
else
    print_error "Admin backend health check failed"
fi

# Check customer health
if curl -f -s http://localhost:8000/health > /dev/null; then
    print_success "Customer frontend is healthy"
else
    print_error "Customer frontend health check failed"
fi

echo ""

# ============================================
# Display Status
# ============================================

echo "============================================"
echo "  Deployment Status"
echo "============================================"
docker-compose -f "$COMPOSE_FILE" ps
echo ""

echo "============================================"
echo "  Access URLs"
echo "============================================"
echo "Customer Frontend: http://localhost:8000"
echo "Admin Backend:     http://localhost:8001"
echo "Customer HTTPS:    https://aitechhub.store"
echo "Admin HTTPS:       https://admin.aitechhub.store"
echo ""

echo "============================================"
echo "  Deployment Complete!"
echo "============================================"
echo ""
echo "Next steps:"
echo "1. Test the application at http://localhost:8000"
echo "2. Access admin panel at http://localhost:8001"
echo "3. Verify SSL certificates are working"
echo "4. Monitor logs: docker-compose -f $COMPOSE_FILE logs -f"
echo ""

print_success "Production deployment successful!"
