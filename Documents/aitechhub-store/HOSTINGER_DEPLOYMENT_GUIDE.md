# Hostinger Deployment Guide

## AITechHub Store - Complete Deployment Documentation

**Last Updated:** 2025-10-09
**Server:** Hostinger VPS
**Domain:** aitechhub.store

---

## 📋 Table of Contents

1. [Server Information](#server-information)
2. [Prerequisites](#prerequisites)
3. [GitHub SSH Setup](#github-ssh-setup)
4. [Deployment Process](#deployment-process)
5. [DNS Configuration](#dns-configuration)
6. [SSL Certificate Setup](#ssl-certificate-setup)
7. [Post-Deployment Tasks](#post-deployment-tasks)
8. [Monitoring & Maintenance](#monitoring--maintenance)
9. [Troubleshooting](#troubleshooting)

---

## 🖥️ Server Information

### Hostinger VPS Details

| Parameter | Value |
|-----------|-------|
| **IP Address** | 72.60.238.18 |
| **SSH Port** | 65002 |
| **Username** | u631122123 |
| **Domain** | aitechhub.store |
| **OS** | Ubuntu/Linux |

### SSH Access

```bash
ssh -p 65002 u631122123@72.60.238.18
```

---

## 📦 Prerequisites

### Local Machine Requirements

- Git installed
- SSH client
- Bash shell (macOS/Linux) or WSL (Windows)

### Server Requirements

Will be installed automatically by deployment script:
- Docker 20.10+
- Docker Compose 1.29+
- Git

---

## 🔑 GitHub SSH Setup

### Step 1: SSH Key Generated

SSH key has been generated and added to GitHub:

```
ssh-ed25519 AAAAC3NzaC1lZDI1NTE5AAAAICEXl8/OhRH7KZJmpzUt1flQkKnA1B7tqlDN6/i7gV1l ramesh10779@github
```

**Key Fingerprint:** `SHA256:l/1y9l3uQg3wVw71kQKwdHPVGh+uKwqpphBYMlMyW0U`

### Step 2: Verify GitHub Access

```bash
ssh -T git@github.com
```

**Expected Output:**
```
Hi ramesh10779! You've successfully authenticated, but GitHub does not provide shell access.
```

✅ **Status:** GitHub SSH authentication configured and working

For detailed SSH setup documentation, see: [GITHUB_SSH_SETUP.md](GITHUB_SSH_SETUP.md)

---

## 🚀 Deployment Process

### Method 1: Automated Deployment (Recommended)

**Step 1: Run Deployment Script**

```bash
cd /Users/rameshgnanasekaran/Documents/aitechhub-store
./deploy-to-hostinger.sh
```

This script will:
1. Test SSH connection to Hostinger
2. Check/install Docker and Docker Compose
3. Clone repository from GitHub
4. Deploy production containers
5. Configure environment
6. Set up SSL certificates

**Duration:** ~10-15 minutes

### Method 2: Manual Deployment

**Step 1: Connect to Server**

```bash
ssh -p 65002 u631122123@72.60.238.18
```

**Step 2: Install Docker**

```bash
# Install Docker
curl -fsSL https://get.docker.com -o get-docker.sh
sh get-docker.sh

# Install Docker Compose
curl -L "https://github.com/docker/compose/releases/latest/download/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose
chmod +x /usr/local/bin/docker-compose

# Verify installations
docker --version
docker-compose --version
```

**Step 3: Clone Repository**

```bash
cd /home/u631122123
git clone https://github.com/ramesh10779/claudeproject.git aitechhub-store
cd aitechhub-store
```

**Step 4: Configure Environment**

```bash
# Copy environment template
cp .env.production.example .env.production

# Generate secure passwords
DB_ROOT_PASS=$(openssl rand -base64 32 | tr -d "=+/" | cut -c1-25)
DB_PASS=$(openssl rand -base64 24 | tr -d "=+/" | cut -c1-20)
REDIS_PASS=$(openssl rand -base64 24 | tr -d "=+/" | cut -c1-20)
API_KEY=$(openssl rand -base64 32 | tr -d "=+/" | cut -c1-32)

# Update .env.production with these values
nano .env.production
```

**Step 5: Deploy Containers**

```bash
# Build and start containers
docker-compose -f docker-compose.production.yml up -d --build

# Wait for database to initialize
sleep 30

# Run migrations
docker exec aitechhub-admin-prod php artisan migrate --force
docker exec aitechhub-customer-prod php artisan migrate --force

# Optimize caches
docker exec aitechhub-admin-prod php artisan optimize
docker exec aitechhub-customer-prod php artisan optimize

# Generate SSL certificates
docker exec aitechhub-ssl-prod /usr/local/bin/generate-ssl.sh
```

---

## 🌐 DNS Configuration

### Required DNS Records

Configure these at your domain registrar (e.g., Hostinger DNS):

| Type | Name | Value | TTL |
|------|------|-------|-----|
| A | @ | 72.60.238.18 | 3600 |
| A | admin | 72.60.238.18 | 3600 |
| A | www | 72.60.238.18 | 3600 |

### Configuration Steps

1. **Go to Hostinger DNS Management:**
   - Login to Hostinger account
   - Navigate to: Domains → aitechhub.store → DNS

2. **Add/Update Records:**
   ```
   Type: A
   Name: @
   Points to: 72.60.238.18
   TTL: 3600

   Type: A
   Name: admin
   Points to: 72.60.238.18
   TTL: 3600

   Type: A
   Name: www
   Points to: 72.60.238.18
   TTL: 3600
   ```

3. **Save Changes**

4. **Verify DNS Propagation:**
   ```bash
   # Check main domain
   dig aitechhub.store +short
   # Should return: 72.60.238.18

   # Check admin subdomain
   dig admin.aitechhub.store +short
   # Should return: 72.60.238.18
   ```

**Propagation Time:** 5 minutes to 24 hours (usually <1 hour)

---

## 🔒 SSL Certificate Setup

### Automatic SSL with Let's Encrypt

SSL certificates are automatically generated on first deployment.

**Certificate Files:**
- Certificate: `/etc/nginx/ssl/fullchain.pem`
- Private Key: `/etc/nginx/ssl/privkey.pem`
- DH Parameters: `/etc/nginx/ssl/dhparam.pem`

### Manual SSL Generation

If automatic generation fails:

```bash
# SSH to server
ssh -p 65002 u631122123@72.60.238.18

# Navigate to project
cd /home/u631122123/aitechhub-store

# Generate certificates
docker exec aitechhub-ssl-prod /usr/local/bin/generate-ssl.sh

# Check certificate status
docker exec aitechhub-ssl-prod openssl x509 -in /etc/nginx/ssl/fullchain.pem -noout -dates
```

### SSL Renewal

Certificates auto-renew when < 30 days remaining.

**Manual Renewal:**
```bash
docker exec aitechhub-ssl-prod /usr/local/bin/renew-ssl.sh
```

**Set up cron for automatic renewal:**
```bash
# Add to crontab
docker exec aitechhub-ssl-prod sh -c 'echo "0 0 * * * /usr/local/bin/renew-ssl.sh" | crontab -'
```

### Testing SSL

```bash
# Check SSL certificate
curl -vI https://aitechhub.store

# Test SSL Labs
# Visit: https://www.ssllabs.com/ssltest/analyze.html?d=aitechhub.store
```

---

## ✅ Post-Deployment Tasks

### 1. Verify Container Status

```bash
ssh -p 65002 u631122123@72.60.238.18
cd /home/u631122123/aitechhub-store
docker-compose -f docker-compose.production.yml ps
```

**Expected Output:**
```
NAME                       STATUS
aitechhub-admin-prod       Up (healthy)
aitechhub-customer-prod    Up (healthy)
aitechhub-db-prod          Up (healthy)
aitechhub-ssl-prod         Up (healthy)
aitechhub-redis-prod       Up
```

### 2. Health Checks

```bash
# Test customer frontend
curl -I http://72.60.238.18:8000/health
# Expected: HTTP/1.1 200 OK

# Test admin backend
curl -I http://72.60.238.18:8001/health
# Expected: HTTP/1.1 200 OK

# Test HTTPS
curl -I https://aitechhub.store
# Expected: HTTP/2 200
```

### 3. Seed Database (Optional)

```bash
# SSH to server
ssh -p 65002 u631122123@72.60.238.18
cd /home/u631122123/aitechhub-store

# Seed products
docker exec aitechhub-admin-prod php artisan db:seed --class=ProductSeeder

# Seed categories
docker exec aitechhub-admin-prod php artisan db:seed --class=CategorySeeder
```

### 4. Configure Firewall

```bash
# Allow HTTP/HTTPS
ufw allow 80/tcp
ufw allow 443/tcp

# Allow SSH (custom port)
ufw allow 65002/tcp

# Enable firewall
ufw enable

# Check status
ufw status
```

### 5. Set Up Monitoring

```bash
# View logs
docker-compose -f docker-compose.production.yml logs -f

# Monitor resources
docker stats

# Check disk usage
df -h
```

---

## 📊 Monitoring & Maintenance

### View Application Logs

```bash
# All services
docker-compose -f docker-compose.production.yml logs -f

# Specific service
docker-compose -f docker-compose.production.yml logs -f customer
docker-compose -f docker-compose.production.yml logs -f admin
docker-compose -f docker-compose.production.yml logs -f database
```

### Monitor Container Resources

```bash
# Real-time stats
docker stats

# Container health
docker ps
```

### Database Backup

```bash
# Create backup
docker exec aitechhub-db-prod mysqldump \
  -u root \
  -p"${DB_ROOT_PASSWORD}" \
  aitechhub_store > backup-$(date +%Y%m%d).sql

# Automated daily backup (cron)
0 2 * * * cd /home/u631122123/aitechhub-store && docker exec aitechhub-db-prod mysqldump -u root -p"${DB_ROOT_PASSWORD}" aitechhub_store > backups/backup-$(date +\%Y\%m\%d).sql
```

### Update Application

```bash
# Pull latest code
cd /home/u631122123/aitechhub-store
git pull origin main

# Rebuild containers
docker-compose -f docker-compose.production.yml up -d --build

# Run migrations
docker exec aitechhub-admin-prod php artisan migrate --force
docker exec aitechhub-customer-prod php artisan migrate --force

# Clear caches
docker exec aitechhub-admin-prod php artisan optimize
docker exec aitechhub-customer-prod php artisan optimize
```

---

## 🔧 Troubleshooting

### Issue: Containers Won't Start

```bash
# Check logs
docker-compose -f docker-compose.production.yml logs

# Restart containers
docker-compose -f docker-compose.production.yml restart

# Rebuild from scratch
docker-compose -f docker-compose.production.yml down
docker-compose -f docker-compose.production.yml up -d --build
```

### Issue: Database Connection Failed

```bash
# Check database container
docker ps | grep database

# Test database connection
docker exec aitechhub-db-prod mysql -u root -p -e "SELECT 1"

# Check environment variables
docker exec aitechhub-customer-prod env | grep DB_
```

### Issue: SSL Certificate Not Working

```bash
# Check certificate files
docker exec aitechhub-ssl-prod ls -la /etc/nginx/ssl/

# Regenerate certificate
docker exec aitechhub-ssl-prod /usr/local/bin/generate-ssl.sh

# Check Nginx logs
docker logs aitechhub-ssl-prod
```

### Issue: Site Not Accessible

1. **Check DNS:**
   ```bash
   dig aitechhub.store +short
   ```

2. **Check Firewall:**
   ```bash
   ufw status
   ```

3. **Check Nginx:**
   ```bash
   docker exec aitechhub-ssl-prod nginx -t
   docker logs aitechhub-ssl-prod
   ```

4. **Check Containers:**
   ```bash
   docker ps
   ```

---

## 📈 Performance Optimization

### Enable Redis Cache

Already enabled in production configuration:
- Session storage: Redis
- Application cache: Redis
- Query cache: Database

### Monitor Performance

```bash
# Check OPcache status
docker exec aitechhub-customer-prod php -r "print_r(opcache_get_status());"

# Check Redis memory
docker exec aitechhub-redis-prod redis-cli INFO memory

# Database performance
docker exec aitechhub-db-prod mysql -u root -p -e "SHOW GLOBAL STATUS LIKE 'Threads_connected';"
```

---

## 🔄 Rollback Procedure

If deployment fails:

```bash
# Stop new containers
docker-compose -f docker-compose.production.yml down

# Restore from backup
docker exec -i aitechhub-db-prod mysql -u root -p aitechhub_store < backups/backup-YYYYMMDD.sql

# Checkout previous version
git checkout [previous-commit-hash]

# Redeploy
docker-compose -f docker-compose.production.yml up -d --build
```

---

## 📞 Support & Resources

### Documentation
- [Production Deployment Guide](PRODUCTION_DEPLOYMENT_GUIDE.md)
- [Container Optimization Summary](CONTAINER_OPTIMIZATION_SUMMARY.md)
- [GitHub SSH Setup](GITHUB_SSH_SETUP.md)
- [Production Ready Summary](PRODUCTION_READY_SUMMARY.md)

### Access URLs
- **Customer Frontend**: https://aitechhub.store
- **Admin Backend**: https://admin.aitechhub.store
- **Customer (Dev)**: http://72.60.238.18:8000
- **Admin (Dev)**: http://72.60.238.18:8001

### Repository
- **GitHub**: https://github.com/ramesh10779/claudeproject.git

---

## ✅ Deployment Checklist

- [ ] GitHub SSH key configured
- [ ] Code pushed to GitHub
- [ ] Hostinger SSH access verified
- [ ] DNS A records configured
- [ ] Deployment script executed
- [ ] Containers running and healthy
- [ ] SSL certificates generated
- [ ] Database migrated
- [ ] Health checks passing
- [ ] Firewall configured
- [ ] Monitoring set up
- [ ] Backup cron configured
- [ ] Site accessible via HTTPS

---

**Last Updated:** 2025-10-09
**Status:** ✅ Ready for Deployment
**Next Action:** Run `./deploy-to-hostinger.sh`
