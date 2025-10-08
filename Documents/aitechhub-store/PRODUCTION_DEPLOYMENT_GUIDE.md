# Production Deployment Guide

## AITechHub Store - Production-Ready Containerized Deployment

**Last Updated:** 2025-10-09
**Version:** 1.0.0

---

## 📋 Table of Contents

1. [Overview](#overview)
2. [Architecture](#architecture)
3. [Prerequisites](#prerequisites)
4. [Quick Start](#quick-start)
5. [Configuration](#configuration)
6. [Container Details](#container-details)
7. [Security Features](#security-features)
8. [SSL Certificate Management](#ssl-certificate-management)
9. [Deployment Process](#deployment-process)
10. [Monitoring & Maintenance](#monitoring--maintenance)
11. [Troubleshooting](#troubleshooting)
12. [Rollback Procedures](#rollback-procedures)

---

## 🎯 Overview

This production deployment uses **multi-stage Docker containers** with:

- ✅ **Optimized Build**: Multi-stage builds for minimal image size
- ✅ **Security Hardening**: Non-root users, capability dropping, security policies
- ✅ **Database Security**: MySQL hardening with audit logging
- ✅ **Automatic SSL**: Let's Encrypt integration with auto-renewal
- ✅ **High Performance**: OPcache, Redis caching, JIT compilation
- ✅ **Health Monitoring**: Health checks for all services
- ✅ **Auto-Recovery**: Automatic container restart on failure

---

## 🏗️ Architecture

```
┌─────────────────────────────────────────────────────────┐
│                    Internet Traffic                      │
└─────────────────────┬───────────────────────────────────┘
                      │
                      ▼
         ┌────────────────────────┐
         │   SSL/Reverse Proxy    │
         │   (Nginx + Certbot)    │
         │   Port 80/443          │
         └───────┬────────────────┘
                 │
        ┌────────┴────────┐
        │                 │
        ▼                 ▼
┌──────────────┐   ┌──────────────┐
│   Customer   │   │    Admin     │
│   Frontend   │   │   Backend    │
│   Port 8000  │   │  Port 8001   │
└──────┬───────┘   └──────┬───────┘
       │                  │
       └────────┬─────────┘
                │
       ┌────────┴────────┐
       │                 │
       ▼                 ▼
┌──────────────┐   ┌──────────────┐
│   Database   │   │    Redis     │
│  MySQL 8.0   │   │   Cache      │
│  Port 3306   │   │  Port 6379   │
└──────────────┘   └──────────────┘
```

---

## 📦 Prerequisites

### System Requirements

- **OS**: Linux (Ubuntu 20.04+ recommended), macOS, or Windows with WSL2
- **CPU**: 2+ cores
- **RAM**: 4GB minimum, 8GB recommended
- **Disk**: 20GB free space
- **Docker**: 20.10+ with Docker Compose 1.29+

### Software Installation

```bash
# Ubuntu/Debian
sudo apt update
sudo apt install -y docker.io docker-compose

# Enable Docker service
sudo systemctl enable docker
sudo systemctl start docker

# Add current user to docker group
sudo usermod -aG docker $USER
```

---

## 🚀 Quick Start

### 1. Clone Repository

```bash
git clone https://gitlab.com/ramesh10779-group/ramesh10779-project.git aitechhub-store
cd aitechhub-store
```

### 2. Configure Environment

```bash
# Copy production environment template
cp .env.production.example .env.production

# Edit configuration
nano .env.production
```

**Required Changes:**
- `DB_PASSWORD` - Strong database password
- `DB_ROOT_PASSWORD` - Strong root password
- `REDIS_PASSWORD` - Strong Redis password
- `ADMIN_API_KEY` - Secure API key
- `APP_KEY` - Generate with `php artisan key:generate`
- `SSL_EMAIL` - Your email for Let's Encrypt

### 3. Deploy

```bash
# Make deployment script executable
chmod +x deploy-production.sh

# Run deployment
./deploy-production.sh
```

### 4. Verify

```bash
# Check container status
docker-compose -f docker-compose.production.yml ps

# Test customer frontend
curl http://localhost:8000/health

# Test admin backend
curl http://localhost:8001/health
```

---

## ⚙️ Configuration

### Environment Variables

| Variable | Description | Default |
|----------|-------------|---------|
| `DB_DATABASE` | Database name | `aitechhub_store` |
| `DB_USERNAME` | Database user | `aitechhub` |
| `DB_PASSWORD` | Database password | **CHANGE THIS** |
| `DB_ROOT_PASSWORD` | Root password | **CHANGE THIS** |
| `REDIS_PASSWORD` | Redis password | **CHANGE THIS** |
| `ADMIN_API_KEY` | Admin API key | **CHANGE THIS** |
| `DOMAIN` | Primary domain | `aitechhub.store` |
| `SSL_EMAIL` | SSL notification email | `admin@aitechhub.store` |
| `SSL_STAGING` | Use Let's Encrypt staging (0/1) | `0` |

### Security Configuration

**Update these in `.env.production`:**

```bash
# Strong passwords (minimum 16 characters)
DB_ROOT_PASSWORD=$(openssl rand -base64 32)
DB_PASSWORD=$(openssl rand -base64 24)
REDIS_PASSWORD=$(openssl rand -base64 24)
ADMIN_API_KEY=$(openssl rand -base64 32)

# Session security
SESSION_SECURE_COOKIE=true
SESSION_HTTP_ONLY=true
SESSION_SAME_SITE=strict
```

---

## 🐳 Container Details

### 1. Database Container (MySQL 8.0)

**Features:**
- Hardened MySQL configuration
- Security policies enforced
- Audit logging enabled
- Automatic backups support

**Image Size:** ~600MB
**Security:** Non-root, capability-limited

**Hardening Enabled:**
- Password policy: STRONG (12+ chars, mixed case, numbers)
- Failed login attempts: 5 before lockout
- Local infile disabled
- Anonymous users removed
- Test database removed

### 2. Admin Backend Container (Laravel 12)

**Features:**
- Multi-stage build (composer → final)
- PHP 8.2 with OPcache + JIT
- Supervisor process management
- Queue workers enabled

**Image Size:** ~250MB (optimized from ~600MB)
**Security:** Non-root user `admin` (UID 1001)

**Processes:**
- PHP-FPM (port 9000)
- Nginx (port 8001)
- Laravel Queue Workers (4 processes)
- Laravel Scheduler

### 3. Customer Frontend Container (Laravel 12)

**Features:**
- Multi-stage build (composer → node → final)
- PHP 8.2 with OPcache + JIT
- Node.js assets compiled
- Supervisor process management

**Image Size:** ~280MB (optimized from ~700MB)
**Security:** Non-root user `appuser` (UID 1000)

**Processes:**
- PHP-FPM (port 9000)
- Nginx (port 8000)
- Laravel Queue Workers (optional)

### 4. SSL/Reverse Proxy Container (Nginx + Certbot)

**Features:**
- Automatic Let's Encrypt certificates
- HTTP → HTTPS redirect
- TLS 1.2/1.3 only
- HSTS enabled
- Security headers

**Image Size:** ~50MB
**Ports:** 80, 443

### 5. Redis Container

**Features:**
- Cache and session storage
- Password protected
- Memory limit: 256MB
- LRU eviction policy

**Image Size:** ~30MB

---

## 🔒 Security Features

### Container Security

1. **Non-root Users**
   - All application containers run as non-root
   - Capabilities dropped (`cap_drop: ALL`)
   - Only required capabilities added

2. **Network Isolation**
   - Backend network: Internal only (database, redis)
   - Frontend network: External access (nginx, apps)

3. **Read-only Filesystems**
   - Configuration files mounted read-only
   - Writable volumes only where needed

### Database Security

1. **Access Control**
   - Application user: Limited privileges
   - Read-only user: SELECT only
   - Backup user: Localhost only

2. **Password Policies**
   - Minimum 12 characters
   - Mixed case required
   - Numbers required
   - Special characters required

3. **Audit Logging**
   - All data modifications logged
   - Security events tracked
   - Active sessions monitored

### Application Security

1. **Security Headers**
   - X-Frame-Options: SAMEORIGIN/DENY
   - X-Content-Type-Options: nosniff
   - X-XSS-Protection: enabled
   - HSTS: 2 years
   - CSP: Configured

2. **Rate Limiting**
   - Customer: 10 req/s
   - Admin: 5 req/s
   - Login: 3 req/min

3. **Session Security**
   - Secure cookies
   - HTTP-only
   - SameSite: Strict
   - CSRF protection

---

## 🔐 SSL Certificate Management

### Automatic Certificate Generation

SSL certificates are automatically generated on first deployment:

```bash
# Manual certificate generation
docker exec aitechhub-ssl-prod /usr/local/bin/generate-ssl.sh
```

**Process:**
1. Checks for existing certificates
2. Validates domain DNS
3. Generates Let's Encrypt certificate
4. Falls back to self-signed if Let's Encrypt fails
5. Configures Nginx with certificates
6. Reloads Nginx

### Automatic Renewal

Certificates auto-renew when < 30 days remaining:

```bash
# Manual renewal check
docker exec aitechhub-ssl-prod /usr/local/bin/renew-ssl.sh
```

**Cron Setup (inside container):**
```bash
0 0 * * * /usr/local/bin/renew-ssl.sh
```

### Testing Mode

For testing without rate limits:

```bash
# In .env.production
SSL_STAGING=1
```

This uses Let's Encrypt staging environment.

---

## 🚢 Deployment Process

### Full Deployment

```bash
./deploy-production.sh full
```

**Steps:**
1. Pre-deployment checks
2. Database backup
3. Build Docker images
4. Stop existing containers
5. Start new containers
6. Wait for database
7. Run migrations
8. Optimize caches
9. Generate SSL certificates
10. Health checks

**Duration:** 5-10 minutes

### Fresh Deployment

Skip database backup:

```bash
./deploy-production.sh fresh
```

### Manual Deployment

```bash
# Build images
docker-compose -f docker-compose.production.yml build --no-cache

# Start services
docker-compose -f docker-compose.production.yml up -d

# Run migrations
docker exec aitechhub-admin-prod php artisan migrate --force
docker exec aitechhub-customer-prod php artisan migrate --force

# Optimize
docker exec aitechhub-admin-prod php artisan optimize
docker exec aitechhub-customer-prod php artisan optimize

# Generate SSL
docker exec aitechhub-ssl-prod /usr/local/bin/generate-ssl.sh
```

---

## 📊 Monitoring & Maintenance

### View Logs

```bash
# All services
docker-compose -f docker-compose.production.yml logs -f

# Specific service
docker-compose -f docker-compose.production.yml logs -f customer
docker-compose -f docker-compose.production.yml logs -f admin
docker-compose -f docker-compose.production.yml logs -f database

# Last 100 lines
docker-compose -f docker-compose.production.yml logs --tail=100 customer
```

### Container Status

```bash
# List all containers
docker-compose -f docker-compose.production.yml ps

# Container resource usage
docker stats
```

### Database Backup

```bash
# Manual backup
docker exec aitechhub-db-prod mysqldump \
  -u root \
  -p"${DB_ROOT_PASSWORD}" \
  "${DB_DATABASE}" > backup-$(date +%Y%m%d).sql

# Restore from backup
docker exec -i aitechhub-db-prod mysql \
  -u root \
  -p"${DB_ROOT_PASSWORD}" \
  "${DB_DATABASE}" < backup-20251009.sql
```

### Health Checks

```bash
# Customer frontend
curl http://localhost:8000/health

# Admin backend
curl http://localhost:8001/health

# Database
docker exec aitechhub-db-prod mysqladmin ping -h localhost -u root -p"${DB_ROOT_PASSWORD}"

# Redis
docker exec aitechhub-redis-prod redis-cli -a "${REDIS_PASSWORD}" ping
```

### Performance Monitoring

```bash
# PHP OPcache status (admin)
docker exec aitechhub-admin-prod php -r "print_r(opcache_get_status());"

# Redis memory usage
docker exec aitechhub-redis-prod redis-cli -a "${REDIS_PASSWORD}" INFO memory

# MySQL performance
docker exec aitechhub-db-prod mysql -u root -p"${DB_ROOT_PASSWORD}" -e "SHOW GLOBAL STATUS LIKE 'Threads_connected';"
```

---

## 🔧 Troubleshooting

### Container Won't Start

```bash
# Check logs
docker-compose -f docker-compose.production.yml logs [service-name]

# Restart specific service
docker-compose -f docker-compose.production.yml restart [service-name]

# Rebuild and restart
docker-compose -f docker-compose.production.yml up -d --build [service-name]
```

### Database Connection Issues

```bash
# Verify database is running
docker exec aitechhub-db-prod mysqladmin ping -h localhost -u root -p"${DB_ROOT_PASSWORD}"

# Check database logs
docker-compose -f docker-compose.production.yml logs database

# Test connection from app
docker exec aitechhub-customer-prod php artisan tinker
>>> DB::connection()->getPdo();
```

### SSL Certificate Issues

```bash
# Check certificate status
docker exec aitechhub-ssl-prod openssl x509 -in /etc/nginx/ssl/fullchain.pem -noout -dates

# Regenerate certificates
docker exec aitechhub-ssl-prod /usr/local/bin/generate-ssl.sh

# Use staging for testing
# Set SSL_STAGING=1 in .env.production
```

### Performance Issues

```bash
# Clear application cache
docker exec aitechhub-customer-prod php artisan cache:clear
docker exec aitechhub-customer-prod php artisan config:clear
docker exec aitechhub-customer-prod php artisan route:clear
docker exec aitechhub-customer-prod php artisan view:clear

# Restart PHP-FPM
docker-compose -f docker-compose.production.yml restart customer admin

# Check resource usage
docker stats
```

---

## ⏪ Rollback Procedures

### Quick Rollback

```bash
# Stop current deployment
docker-compose -f docker-compose.production.yml down

# Restore from backup
docker exec -i aitechhub-db-prod mysql \
  -u root -p"${DB_ROOT_PASSWORD}" \
  "${DB_DATABASE}" < docker/database/backups/backup-YYYYMMDD-HHMMSS.sql

# Start containers
docker-compose -f docker-compose.production.yml up -d
```

### Git-based Rollback

```bash
# View previous commits
git log --oneline

# Checkout previous version
git checkout [commit-hash]

# Redeploy
./deploy-production.sh
```

---

## 📈 Scaling & Optimization

### Horizontal Scaling

Add more worker processes in `docker-compose.production.yml`:

```yaml
customer:
  deploy:
    replicas: 3
```

### Database Optimization

Edit `docker/database/mysql-hardened.cnf`:

```ini
# Increase buffer pool for more RAM
innodb_buffer_pool_size = 1G

# Increase connections
max_connections = 500
```

### Redis Optimization

```yaml
redis:
  command: redis-server --maxmemory 512mb --maxmemory-policy allkeys-lru
```

---

## 📞 Support & Resources

- **Documentation**: This file
- **GitLab Repository**: https://gitlab.com/ramesh10779-group/ramesh10779-project
- **Issues**: Create issue on GitLab
- **Security**: Report security issues privately

---

## ✅ Deployment Checklist

Before going live:

- [ ] Updated `.env.production` with strong passwords
- [ ] Configured correct domain name
- [ ] DNS A records pointing to server IP
- [ ] Firewall rules allow ports 80, 443
- [ ] SSL email configured
- [ ] Database backup schedule configured
- [ ] Monitoring alerts configured
- [ ] Tested all critical features
- [ ] Load tested application
- [ ] Security scan completed
- [ ] Documentation updated

---

**Deployment Complete! 🎉**

Access your application:
- Customer: https://aitechhub.store
- Admin: https://admin.aitechhub.store
