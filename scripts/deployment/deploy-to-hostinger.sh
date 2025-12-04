#!/bin/bash

# Therapair Landing Page - Deploy to Hostinger
# Usage: ./deploy-to-hostinger.sh

echo "🚀 Deploying Therapair Landing Page to Hostinger..."
echo ""

# Push local commits to GitHub first
echo "📤 Pushing commits to GitHub..."
git push origin main

if [ $? -ne 0 ]; then
    echo ""
    echo "❌ Failed to push to GitHub. Please check the error message above."
    exit 1
fi

echo ""
echo "📥 Pulling latest changes on Hostinger..."

# Pull latest changes on Hostinger (stash any local changes first, then pull)
ssh u549396201@45.87.81.159 -p 65002 'cd domains/therapair.com.au/public_html && git stash && git pull origin main && git stash pop || true'

if [ $? -eq 0 ]; then
    echo ""
    echo "✅ Deployment successful!"
    echo "🌐 Your site is now live at: https://therapair.com.au"
else
    echo ""
    echo "❌ Deployment failed. Please check the error message above."
    exit 1
fi


