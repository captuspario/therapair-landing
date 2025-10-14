#!/usr/bin/env python3
"""
Reorganize name columns in Notion database:
1. Current 'First Name' (title column) → Becomes 'Full Name' (first + last name combined)
2. Current 'Fullname' (rich text) → Becomes 'First Name' (just first name)
3. 'Last Name' stays as is

This will make the card view show the full name instead of just first name.
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
    
    return ""

def main():
    print("═" * 80)
    print("  Reorganize Name Columns")
    print("  Making 'Full Name' the title column for better card display")
    print("═" * 80)
    print()
    
    print("📋 Current structure:")
    print("   • First Name (title column) - shows on cards")
    print("   • Last Name (rich text)")
    print("   • Fullname (rich text)")
    print()
    print("🎯 New structure:")
    print("   • Full Name (title column) - will show 'First Last' on cards")
    print("   • First Name (rich text) - individual first name")
    print("   • Last Name (rich text) - stays the same")
    print()
    print("⚠️  NOTE: You'll need to manually rename columns in Notion:")
    print("   1. Rename 'First Name' → 'Full Name'")
    print("   2. Rename 'Fullname' → 'First Name'")
    print()
    print("🔍 Reading database...\n")
    
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
    
    for entry in all_results:
        props = entry.get('properties', {})
        page_id = entry.get('id')
        
        # Get current values
        current_first_name_title = get_property_value(props.get('First Name'))  # This is the title column
        current_last_name = get_property_value(props.get('Last Name'))
        current_fullname_text = get_property_value(props.get('Fullname'))  # This has the actual first name
        
        # Determine what to use for each field
        # New Full Name (title) = First + Last
        if current_fullname_text and current_last_name:
            new_full_name = f"{current_fullname_text} {current_last_name}"
        elif current_first_name_title and current_last_name:
            new_full_name = f"{current_first_name_title} {current_last_name}"
        elif current_fullname_text:
            new_full_name = current_fullname_text
        elif current_first_name_title:
            new_full_name = current_first_name_title
        else:
            new_full_name = "Unknown"
        
        # New First Name (from Fullname field)
        new_first_name = current_fullname_text if current_fullname_text else current_first_name_title
        
        # Build the update
        updates = {
            'First Name': {'title': [{'text': {'content': new_full_name}}]},  # Title column becomes Full Name
            'Fullname': {'rich_text': [{'text': {'content': new_first_name}}]}  # Fullname becomes First Name
        }
        
        print(f"📝 {new_full_name}")
        print(f"   Current title: \"{current_first_name_title}\"")
        print(f"   Current Fullname field: \"{current_fullname_text}\"")
        print(f"   Current Last Name: \"{current_last_name}\"")
        print()
        print(f"   ✨ New values:")
        print(f"      Title column: \"{new_full_name}\" (shows on cards)")
        print(f"      Fullname field: \"{new_first_name}\" (will become 'First Name')")
        
        # Update the page
        update_url = f"https://api.notion.com/v1/pages/{page_id}"
        update_payload = {"properties": updates}
        
        update_response = requests.patch(update_url, headers=HEADERS, json=update_payload)
        
        if update_response.status_code == 200:
            print(f"   ✅ Updated successfully")
            updated_count += 1
        else:
            error_data = update_response.json()
            print(f"   ❌ Error: {error_data.get('message', 'Unknown error')}")
        
        print("   " + "─" * 76)
        print()
        
        # Rate limiting
        time.sleep(0.334)
    
    print()
    print("═" * 80)
    print()
    print("📊 Summary:")
    print(f"   Entries updated: {updated_count}")
    print()
    print("⚠️  IMPORTANT: Manual steps required in Notion:")
    print()
    print("   1️⃣  Open your Notion database")
    print("   2️⃣  Click on the 'First Name' column header")
    print("   3️⃣  Rename it to 'Full Name'")
    print("   4️⃣  Click on the 'Fullname' column header")
    print("   5️⃣  Rename it to 'First Name'")
    print()
    print("   After renaming, your cards will show full names! 🎉")
    print()
    print("✅ Data update complete!")

if __name__ == "__main__":
    main()

