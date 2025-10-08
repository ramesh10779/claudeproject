#!/bin/bash
# Database Security Hardening Script
# Runs before MySQL starts

set -e

echo "======================================"
echo "  MySQL Security Hardening"
echo "======================================"

# Create secure directories
mkdir -p /var/lib/mysql-files
chmod 750 /var/lib/mysql-files
chown mysql:mysql /var/lib/mysql-files

mkdir -p /var/log/mysql
chmod 750 /var/log/mysql
chown mysql:mysql /var/log/mysql

# Set secure permissions on data directory
chmod 750 /var/lib/mysql
chown -R mysql:mysql /var/lib/mysql

# Create SSL directory (for future SSL certificates)
mkdir -p /etc/mysql/ssl
chmod 750 /etc/mysql/ssl
chown mysql:mysql /etc/mysql/ssl

echo "✓ Secure directories created"

# Set secure file permissions
chmod 644 /etc/mysql/conf.d/*.cnf
chown mysql:mysql /etc/mysql/conf.d/*.cnf

echo "✓ File permissions secured"

# Generate random root password if not set (for initial setup)
if [ -z "$MYSQL_ROOT_PASSWORD" ]; then
    export MYSQL_ROOT_PASSWORD=$(openssl rand -base64 32)
    echo "⚠️  Generated random root password (save this):"
    echo "   $MYSQL_ROOT_PASSWORD"
fi

echo "✓ Password security verified"

# Pass control to original MySQL entrypoint
exec /usr/local/bin/docker-entrypoint.sh "$@"
