#!/bin/bash
# Deploy config.php to Hostinger (without committing to git)
# Usage: ./deploy-config.php

echo "🔐 Deploying config.php to Hostinger..."
echo ""

# Check if config.php exists
if [ ! -f "config.php" ]; then
    echo "❌ Error: config.php not found in current directory"
    echo "   Please run this script from the landing-page directory"
    exit 1
fi

# Upload config.php via SCP
echo "📤 Uploading config.php..."
scp -P 65002 config.php u549396201@45.87.81.159:domains/therapair.com.au/public_html/

if [ $? -eq 0 ]; then
    echo ""
    echo "✅ config.php deployed successfully!"
    echo "🌐 Updated configuration is now live on the server"
    echo ""
    echo "💡 Next steps:"
    echo "   1. Test survey submission"
    echo "   2. Test feedback widget"
    echo "   3. Verify entries appear in Notion databases"
else
    echo ""
    echo "❌ Deployment failed. Please check:"
    echo "   - SSH key is configured"
    echo "   - Server credentials are correct"
    echo "   - Network connection is available"
    exit 1
fi

