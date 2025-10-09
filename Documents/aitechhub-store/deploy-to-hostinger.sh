#!/bin/bash

# ============================================
# AITechHub Store - Hostinger Deployment
# ============================================
# Deploy production containers to Hostinger server
# Last Updated: 2025-10-09

set -e

# Colors
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
BLUE='\033[0;34m'
NC='\033[0m'

# Hostinger server details
HOSTINGER_IP="72.60.238.18"
HOSTINGER_PORT="65002"
HOSTINGER_USER="u631122123"
DOMAIN="aitechhub.store"
PASSWORD="Sasinikhilesh\$03"

echo -e "${BLUE}============================================${NC}"
echo -e "${BLUE}  AITechHub Store - Hostinger Deployment${NC}"
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
# Step 1: Test SSH Connection
# ============================================
echo "Step 1: Testing SSH connection to Hostinger..."
if sshpass -p "$PASSWORD" ssh -p $HOSTINGER_PORT -o StrictHostKeyChecking=no -o ConnectTimeout=10 \
    $HOSTINGER_USER@$HOSTINGER_IP "echo 'SSH connection successful'" 2>/dev/null; then
    print_success "SSH connection established"
else
    print_error "Failed to connect to Hostinger server"
    echo "Please check your SSH credentials"
    exit 1
fi
echo ""

# ============================================
# Step 2: Check Server Requirements
# ============================================
echo "Step 2: Checking server requirements..."

sshpass -p "$PASSWORD" ssh -p $HOSTINGER_PORT $HOSTINGER_USER@$HOSTINGER_IP << 'ENDSSH'
echo "Checking Docker installation..."
if command -v docker &> /dev/null; then
    echo "✓ Docker is installed: $(docker --version)"
else
    echo "✗ Docker is NOT installed"
    echo "Installing Docker..."
    curl -fsSL https://get.docker.com -o get-docker.sh
    sh get-docker.sh
fi

echo "Checking Docker Compose..."
if command -v docker-compose &> /dev/null; then
    echo "✓ Docker Compose is installed: $(docker-compose --version)"
else
    echo "✗ Docker Compose is NOT installed"
    echo "Installing Docker Compose..."
    curl -L "https://github.com/docker/compose/releases/latest/download/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose
    chmod +x /usr/local/bin/docker-compose
fi

echo "Checking Git..."
if command -v git &> /dev/null; then
    echo "✓ Git is installed: $(git --version)"
else
    echo "✗ Git is NOT installed"
    echo "Installing Git..."
    apt-get update && apt-get install -y git
fi
ENDSSH

print_success "Server requirements checked"
echo ""

# ============================================
# Step 3: Clone/Update Repository
# ============================================
echo "Step 3: Deploying code to server..."

sshpass -p "$PASSWORD" ssh -p $HOSTINGER_PORT $HOSTINGER_USER@$HOSTINGER_IP << 'ENDSSH'
REPO_DIR="/home/u631122123/aitechhub-store"

if [ -d "$REPO_DIR" ]; then
    echo "Repository exists, pulling latest changes..."
    cd "$REPO_DIR"
    git fetch origin
    git reset --hard origin/main
    git pull origin main
else
    echo "Cloning repository..."
    git clone https://github.com/ramesh10779/claudeproject.git "$REPO_DIR"
fi

echo "✓ Code deployed to server"
ENDSSH

print_success "Code deployed to server"
echo ""

print_success "Deployment completed!"
echo ""
