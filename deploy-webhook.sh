#!/bin/bash

# Deploy Resend Webhook Fix
# This script deploys the resend-webhook.php file to fix the 404 error

# Configuration
HOST="45.87.81.159"
USER="u549396201"
PORT="65002"
REMOTE_PATH="domains/therapair.com.au/public_html/api/research"
LOCAL_FILE="api/research/resend-webhook.php"

echo "🚀 Deploying Resend Webhook Fix..."
echo ""

# Check if file exists locally
if [ ! -f "$LOCAL_FILE" ]; then
    echo "❌ Error: $LOCAL_FILE not found!"
    echo "   Make sure you're running this from the products/landing-page directory"
    exit 1
fi

echo "📤 Uploading resend-webhook.php..."
scp -P $PORT "$LOCAL_FILE" "$USER@$HOST:$REMOTE_PATH/"

if [ $? -eq 0 ]; then
    echo ""
    echo "✅ Webhook file deployed successfully!"
    echo ""
    echo "🔍 Next steps:"
    echo "   1. Verify RESEND_WEBHOOK_SECRET is in config.php"
    echo "   2. Test webhook in Resend dashboard (click 'Replay' on failed events)"
    echo "   3. Check that events now return 200 OK instead of 404"
    echo ""
    echo "🧪 Test URL: https://therapair.com.au/api/research/resend-webhook.php"
else
    echo ""
    echo "❌ Deployment failed. Please check:"
    echo "   - SSH connection"
    echo "   - File permissions"
    echo "   - Remote directory exists"
    exit 1
fi

