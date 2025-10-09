#!/bin/bash

# Therapair Landing Page - Deploy to Hostinger
# Usage: ./deploy-to-hostinger.sh

echo "🚀 Deploying Therapair Landing Page to Hostinger..."
echo ""

# Pull latest changes on Hostinger
ssh u549396201@45.87.81.159 -p 65002 'cd domains/therapair.com.au/public_html && git pull origin main'

if [ $? -eq 0 ]; then
    echo ""
    echo "✅ Deployment successful!"
    echo "🌐 Your site is now live at: https://therapair.com.au"
else
    echo ""
    echo "❌ Deployment failed. Please check the error message above."
    exit 1
fi

