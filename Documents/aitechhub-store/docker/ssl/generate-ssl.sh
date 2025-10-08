#!/bin/bash
# Automatic SSL Certificate Generation with Let's Encrypt
# Supports staging and production certificates

set -e

# Configuration
DOMAIN="${DOMAIN:-aitechhub.store}"
EMAIL="${SSL_EMAIL:-admin@aitechhub.store}"
STAGING="${STAGING:-0}"
WEBROOT="/var/www/certbot"

echo "======================================"
echo "  SSL Certificate Generation"
echo "======================================"
echo "Domain: $DOMAIN"
echo "Email: $EMAIL"
echo "Staging: $STAGING"
echo ""

# Function to check if certificates exist
check_certificates() {
    if [ -f "/etc/letsencrypt/live/$DOMAIN/fullchain.pem" ]; then
        return 0
    else
        return 1
    fi
}

# Function to generate self-signed certificate (fallback)
generate_self_signed() {
    echo "Generating self-signed certificate..."

    mkdir -p /etc/nginx/ssl

    openssl req -x509 -nodes -days 365 -newkey rsa:2048 \
        -keyout /etc/nginx/ssl/privkey.pem \
        -out /etc/nginx/ssl/fullchain.pem \
        -subj "/C=US/ST=State/L=City/O=Organization/CN=$DOMAIN"

    echo "✓ Self-signed certificate generated"
    echo "⚠️  WARNING: Self-signed certificates are not trusted by browsers"
    echo "   Use Let's Encrypt in production"
}

# Function to generate Let's Encrypt certificate
generate_letsencrypt() {
    echo "Generating Let's Encrypt certificate..."

    # Prepare certbot arguments
    CERTBOT_ARGS="certonly --webroot -w $WEBROOT -d $DOMAIN --email $EMAIL --agree-tos --non-interactive"

    # Add staging flag if enabled
    if [ "$STAGING" = "1" ]; then
        CERTBOT_ARGS="$CERTBOT_ARGS --staging"
        echo "Using Let's Encrypt STAGING environment"
    fi

    # Add force renewal if requested
    if [ "$FORCE_RENEWAL" = "1" ]; then
        CERTBOT_ARGS="$CERTBOT_ARGS --force-renewal"
    fi

    # Run certbot
    if certbot $CERTBOT_ARGS; then
        echo "✓ Let's Encrypt certificate generated successfully"

        # Create symlinks for nginx
        ln -sf /etc/letsencrypt/live/$DOMAIN/fullchain.pem /etc/nginx/ssl/fullchain.pem
        ln -sf /etc/letsencrypt/live/$DOMAIN/privkey.pem /etc/nginx/ssl/privkey.pem

        # Test nginx configuration
        nginx -t

        # Reload nginx
        nginx -s reload || true

        return 0
    else
        echo "✗ Failed to generate Let's Encrypt certificate"
        return 1
    fi
}

# Function to generate DH parameters
generate_dhparams() {
    if [ ! -f "/etc/nginx/ssl/dhparams.pem" ]; then
        echo "Generating DH parameters (this may take a while)..."
        openssl dhparam -out /etc/nginx/ssl/dhparams.pem 2048
        echo "✓ DH parameters generated"
    fi
}

# Main execution
main() {
    # Check if running in development mode
    if [ "$APP_ENV" = "local" ] || [ "$SELF_SIGNED" = "1" ]; then
        echo "Running in development mode - generating self-signed certificate"
        generate_self_signed
        generate_dhparams
        return 0
    fi

    # Check if certificates already exist
    if check_certificates && [ "$FORCE_RENEWAL" != "1" ]; then
        echo "✓ SSL certificates already exist"

        # Verify expiration
        expiry_date=$(openssl x509 -enddate -noout -in /etc/letsencrypt/live/$DOMAIN/fullchain.pem | cut -d= -f2)
        echo "   Certificate expires: $expiry_date"

        # Create symlinks
        ln -sf /etc/letsencrypt/live/$DOMAIN/fullchain.pem /etc/nginx/ssl/fullchain.pem
        ln -sf /etc/letsencrypt/live/$DOMAIN/privkey.pem /etc/nginx/ssl/privkey.pem

        return 0
    fi

    # Ensure webroot exists
    mkdir -p $WEBROOT

    # Try Let's Encrypt first
    if generate_letsencrypt; then
        generate_dhparams
        echo "✓ SSL setup complete"
        return 0
    else
        echo "⚠️  Let's Encrypt failed, falling back to self-signed"
        generate_self_signed
        generate_dhparams
        return 0
    fi
}

# Run main function
main

# Certificate information
echo ""
echo "======================================"
echo "  Certificate Information"
echo "======================================"

if [ -f "/etc/nginx/ssl/fullchain.pem" ]; then
    openssl x509 -in /etc/nginx/ssl/fullchain.pem -noout -subject -issuer -dates
fi

echo ""
echo "✓ SSL generation complete"
