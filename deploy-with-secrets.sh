#!/bin/bash

# Therapair Landing Page - Deploy with Secrets
# Usage: ./deploy-with-secrets.sh

# Configuration
HOST="45.87.81.159"
USER="u549396201"
PORT="65002"
REMOTE_PATH="domains/therapair.com.au/public_html"
LOCAL_CONFIG="config.php"

echo "🚀 Deploying Therapair Landing Page..."

# 1. Upload config.php
if [ -f "$LOCAL_CONFIG" ]; then
    echo "🔑 Uploading config.php..."
    scp -P $PORT "$LOCAL_CONFIG" "$USER@$HOST:$REMOTE_PATH/"
else
    echo "⚠️  Warning: config.php not found locally. Skipping upload."
fi

# 2. Pull latest code via Git
echo "📥 Pulling latest code from Git..."
ssh -p $PORT "$USER@$HOST" "cd $REMOTE_PATH && git pull origin main"

if [ $? -eq 0 ]; then
    echo ""
    echo "✅ Deployment successful!"
    echo "🌐 Your site is now live at: https://therapair.com.au"
else
    echo ""
    echo "❌ Deployment failed. Please check the error message above."
    exit 1
fi
