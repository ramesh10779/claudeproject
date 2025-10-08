# Hardened MySQL Database Container
# Security-focused with performance optimization

FROM mysql:8.0

# Metadata
LABEL maintainer="AITechHub <dev@aitechhub.store>"
LABEL description="Hardened MySQL Database"
LABEL version="8.0"

# Install security tools
RUN apt-get update && apt-get install -y \
    gnupg \
    && rm -rf /var/lib/apt/lists/*

# Copy custom MySQL configuration
COPY docker/database/mysql-hardened.cnf /etc/mysql/conf.d/mysql-hardened.cnf

# Copy initialization scripts
COPY docker/database/init/ /docker-entrypoint-initdb.d/

# Security hardening scripts
COPY docker/database/harden.sh /usr/local/bin/harden.sh
RUN chmod +x /usr/local/bin/harden.sh

# Set secure permissions
RUN chmod 644 /etc/mysql/conf.d/mysql-hardened.cnf \
    && chmod 755 /docker-entrypoint-initdb.d

# Health check
HEALTHCHECK --interval=30s --timeout=3s --start-period=60s --retries=5 \
    CMD mysqladmin ping -h localhost -u root -p${MYSQL_ROOT_PASSWORD} || exit 1

# Expose port
EXPOSE 3306

# Run hardening script on startup
ENTRYPOINT ["/usr/local/bin/harden.sh"]
CMD ["mysqld"]
