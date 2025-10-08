# SSL Certificate Manager with Let's Encrypt
# Automatic certificate generation and renewal

FROM nginx:alpine

# Install certbot and dependencies
RUN apk add --no-cache \
    certbot \
    certbot-nginx \
    openssl \
    bash \
    curl

# Create directories
RUN mkdir -p /etc/letsencrypt \
    && mkdir -p /var/www/certbot \
    && mkdir -p /etc/nginx/ssl

# Copy SSL automation scripts
COPY docker/ssl/generate-ssl.sh /usr/local/bin/generate-ssl.sh
COPY docker/ssl/renew-ssl.sh /usr/local/bin/renew-ssl.sh
COPY docker/ssl/nginx-ssl.conf /etc/nginx/conf.d/default.conf

# Make scripts executable
RUN chmod +x /usr/local/bin/generate-ssl.sh \
    && chmod +x /usr/local/bin/renew-ssl.sh

# Healthcheck
HEALTHCHECK --interval=30s --timeout=3s --start-period=10s \
    CMD curl -f http://localhost/health || exit 1

EXPOSE 80 443

CMD ["nginx", "-g", "daemon off;"]
