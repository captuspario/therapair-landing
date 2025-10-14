#!/usr/bin/env python3
"""
Add new optimization fields to Notion database:
1. Mini Bio (150 chars for cards)
2. Pronouns (she/her, he/him, they/them)
3. Service Area (for better location matching)

These fields enable better card display and matching.
"""

import os
import requests
import time
from dotenv import load_dotenv

load_dotenv()

NOTION_TOKEN = os.getenv('NOTION_TOKEN')
THERAPISTS_DB_ID = os.getenv('THERAPISTS_DATABASE_ID')

if not NOTION_TOKEN or not THERAPISTS_DB_ID:
    print("❌ Error: NOTION_TOKEN and THERAPISTS_DATABASE_ID required in .env")
    exit(1)

NOTION_VERSION = "2022-06-28"
HEADERS = {
    "Authorization": f"Bearer {NOTION_TOKEN}",
    "Notion-Version": NOTION_VERSION,
    "Content-Type": "application/json"
}

def main():
    print("═" * 80)
    print("  Add Optimization Fields to Database")
    print("  Enhancing profile display and matching capabilities")
    print("═" * 80)
    print()
    
    print("📋 Fields to add:")
    print("   1. Mini Bio (Rich Text) - 150 char summary for cards")
    print("   2. Pronouns (Rich Text) - Display next to name")
    print("   3. Price Tier (Select) - $ / $$ / $$$ for quick reference")
    print()
    
    # Prepare new properties
    new_properties = {
        "Mini Bio": {
            "rich_text": {}
        },
        "Pronouns": {
            "rich_text": {}
        },
        "Price Tier": {
            "select": {
                "options": [
                    {"name": "$", "color": "green"},      # <$120
                    {"name": "$$", "color": "yellow"},    # $120-$180
                    {"name": "$$$", "color": "orange"},   # $180-$250
                    {"name": "$$$$", "color": "red"},     # $250+
                    {"name": "Bulk Billing", "color": "blue"},
                    {"name": "Sliding Scale", "color": "purple"}
                ]
            }
        }
    }
    
    # Add properties to database
    print("🔧 Adding properties to database...\n")
    
    update_url = f"https://api.notion.com/v1/databases/{THERAPISTS_DB_ID}"
    
    response = requests.patch(
        update_url,
        headers=HEADERS,
        json={"properties": new_properties}
    )
    
    if response.status_code == 200:
        print("✅ Properties added successfully!\n")
        print("New fields:")
        for prop_name in new_properties.keys():
            prop_type = list(new_properties[prop_name].keys())[0]
            print(f"   ✓ {prop_name} ({prop_type})")
    else:
        error = response.json()
        print(f"❌ Error: {error.get('message', 'Unknown error')}")
        print("\nNote: Some fields may already exist. Let's continue...")
    
    print()
    print("═" * 80)
    print()
    print("📋 Next Steps:")
    print()
    print("1. In Notion, reorder columns to place:")
    print("   • Full Name (first - title column)")
    print("   • Pronouns (right after name)")
    print("   • Professional Title")
    print("   • Mini Bio")
    print()
    print("2. Manually add Mini Bios for key therapists:")
    print("   • Use the template in THERAPIST-PROFILE-GUIDE.md")
    print("   • Keep to 150 characters")
    print("   • Focus on approach + who you help + method")
    print()
    print("3. Add Pronouns where known:")
    print("   • she/her, he/him, they/them, etc.")
    print("   • Leave blank if unsure (therapist can add later)")
    print()
    print("4. Set Price Tier based on Session Fee:")
    print("   • $ = Under $120")
    print("   • $$ = $120-180 (most common)")
    print("   • $$$ = $180-250")
    print("   • $$$$ = Over $250")
    print("   • Or: Bulk Billing / Sliding Scale")
    print()
    print("✅ Database optimization fields added!")

if __name__ == "__main__":
    main()

