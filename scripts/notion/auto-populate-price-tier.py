#!/usr/bin/env python3
"""
Automatically populate Price Tier based on Session Fee.

Tiers:
$ = Under $120
$$ = $120-180
$$$ = $180-250
$$$$ = Over $250
Bulk Billing = Bulk Billing checkbox is checked
Sliding Scale = If "sliding scale" mentioned in rebates
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

def get_property_value(prop):
    """Extract value from a Notion property"""
    if not prop:
        return ""
    
    prop_type = prop.get('type')
    
    if prop_type == 'title':
        return prop.get('title', [{}])[0].get('plain_text', '') if prop.get('title') else ''
    elif prop_type == 'rich_text':
        return prop.get('rich_text', [{}])[0].get('plain_text', '') if prop.get('rich_text') else ''
    elif prop_type == 'number':
        return prop.get('number')
    elif prop_type == 'checkbox':
        return prop.get('checkbox', False)
    elif prop_type == 'select':
        return prop.get('select', {}).get('name', '') if prop.get('select') else ''
    
    return ""

def determine_price_tier(session_fee, bulk_billing, rebates):
    """Determine price tier from session fee"""
    
    # Check bulk billing first
    if bulk_billing:
        return "Bulk Billing"
    
    # Check for sliding scale
    if rebates and 'sliding scale' in rebates.lower():
        return "Sliding Scale"
    
    # No fee data
    if not session_fee:
        return None
    
    # Categorize by fee
    if session_fee < 120:
        return "$"
    elif session_fee < 180:
        return "$$"
    elif session_fee < 250:
        return "$$$"
    else:
        return "$$$$"

def main():
    print("═" * 80)
    print("  Auto-Populate Price Tier")
    print("  Based on Session Fee and Bulk Billing status")
    print("═" * 80)
    print()
    
    # Query the database
    url = f"https://api.notion.com/v1/databases/{THERAPISTS_DB_ID}/query"
    all_results = []
    has_more = True
    start_cursor = None
    
    while has_more:
        payload = {"page_size": 100}
        if start_cursor:
            payload["start_cursor"] = start_cursor
        
        response = requests.post(url, headers=HEADERS, json=payload)
        
        if response.status_code != 200:
            print(f"❌ Error: {response.json()}")
            exit(1)
        
        data = response.json()
        all_results.extend(data.get('results', []))
        has_more = data.get('has_more', False)
        start_cursor = data.get('next_cursor')
    
    print(f"✅ Found {len(all_results)} total entries\n")
    print("═" * 80)
    print()
    
    updated_count = 0
    skipped_count = 0
    
    for entry in all_results:
        props = entry.get('properties', {})
        page_id = entry.get('id')
        
        # Extract values
        first_name = get_property_value(props.get('First Name'))
        last_name = get_property_value(props.get('Last Name'))
        name = f"{first_name} {last_name}".strip() if first_name or last_name else "Unknown"
        
        session_fee = get_property_value(props.get('Session Fee'))
        bulk_billing = get_property_value(props.get('Bulk Billing'))
        rebates = get_property_value(props.get('Do you offer rebates or other funding models?'))
        existing_price_tier = get_property_value(props.get('Price Tier'))
        
        # Determine price tier
        price_tier = determine_price_tier(session_fee, bulk_billing, rebates)
        
        # Skip if already has price tier or no data to determine it
        if existing_price_tier or not price_tier:
            skipped_count += 1
            continue
        
        # Update
        print(f"📝 {name}")
        print(f"   Session Fee: ${session_fee}" if session_fee else "   Session Fee: (not set)")
        print(f"   Bulk Billing: {'Yes' if bulk_billing else 'No'}")
        if rebates:
            print(f"   Rebates: {rebates[:50]}...")
        print(f"   → Price Tier: {price_tier}")
        
        update_url = f"https://api.notion.com/v1/pages/{page_id}"
        update_payload = {
            "properties": {
                "Price Tier": {"select": {"name": price_tier}}
            }
        }
        
        update_response = requests.patch(update_url, headers=HEADERS, json=update_payload)
        
        if update_response.status_code == 200:
            print(f"   ✅ Updated")
            updated_count += 1
        else:
            print(f"   ❌ Error: {update_response.json().get('message', 'Unknown')}")
        
        print("   " + "─" * 76)
        print()
        
        time.sleep(0.334)
    
    print()
    print("═" * 80)
    print()
    print("📊 Summary:")
    print(f"   Entries updated: {updated_count}")
    print(f"   Entries skipped: {skipped_count}")
    print()
    
    print("💡 Next: Add pronouns and mini bios manually for top therapists!")
    print("   Guide: docs/onboarding/THERAPIST-PROFILE-GUIDE.md")
    print()
    print("✅ Price tiers populated!")

if __name__ == "__main__":
    main()

