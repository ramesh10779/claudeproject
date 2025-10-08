#!/bin/bash
# Automatic SSL Certificate Renewal
# Run this via cron: 0 0 * * * /usr/local/bin/renew-ssl.sh

set -e

echo "======================================"
echo "  SSL Certificate Renewal Check"
echo "======================================"
echo "Date: $(date)"
echo ""

# Configuration
DOMAIN="${DOMAIN:-aitechhub.store}"

# Check if using Let's Encrypt
if [ ! -d "/etc/letsencrypt/live/$DOMAIN" ]; then
    echo "⚠️  No Let's Encrypt certificates found"
    echo "   Run generate-ssl.sh first"
    exit 0
fi

# Check certificate expiration
cert_file="/etc/letsencrypt/live/$DOMAIN/fullchain.pem"
expiry_date=$(openssl x509 -enddate -noout -in "$cert_file" | cut -d= -f2)
expiry_epoch=$(date -d "$expiry_date" +%s)
current_epoch=$(date +%s)
days_left=$(( ($expiry_epoch - $current_epoch) / 86400 ))

echo "Certificate expires: $expiry_date"
echo "Days until expiration: $days_left"
echo ""

# Renew if less than 30 days remaining
if [ $days_left -lt 30 ]; then
    echo "Certificate expires in less than 30 days - renewing..."

    # Renew certificate
    if certbot renew --quiet --nginx; then
        echo "✓ Certificate renewed successfully"

        # Reload nginx
        nginx -s reload

        # Log renewal
        echo "$(date): Certificate renewed for $DOMAIN" >> /var/log/ssl-renewal.log

        # Send notification (optional)
        if [ -n "$RENEWAL_WEBHOOK" ]; then
            curl -X POST "$RENEWAL_WEBHOOK" \
                -H "Content-Type: application/json" \
                -d "{\"domain\":\"$DOMAIN\",\"renewed\":true,\"days_left\":$days_left}"
        fi
    else
        echo "✗ Certificate renewal failed"

        # Send alert (optional)
        if [ -n "$RENEWAL_WEBHOOK" ]; then
            curl -X POST "$RENEWAL_WEBHOOK" \
                -H "Content-Type: application/json" \
                -d "{\"domain\":\"$DOMAIN\",\"renewed\":false,\"error\":true}"
        fi

        exit 1
    fi
else
    echo "✓ Certificate is still valid ($days_left days remaining)"
    echo "   No renewal needed"
fi

echo ""
echo "✓ Renewal check complete"
