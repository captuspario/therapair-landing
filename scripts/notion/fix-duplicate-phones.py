#!/usr/bin/env python3
import os
import json
import requests
import re
import time
from dotenv import load_dotenv

# Load environment variables
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
    elif prop_type == 'phone_number':
        return prop.get('phone_number', '')
    elif prop_type == 'url':
        return prop.get('url', '')
    elif prop_type == 'email':
        return prop.get('email', '')
    
    return ""

def normalize_phone(phone):
    """Normalize phone number to just digits for comparison"""
    if not phone:
        return ""
    # Remove all spaces, parentheses, dashes, plus signs
    normalized = re.sub(r'[\s\(\)\-\+]', '', str(phone))
    return normalized

def format_australian_phone(phone):
    """Format Australian phone number consistently"""
    if not phone:
        return ""
    
    # Normalize to digits only
    digits = normalize_phone(phone)
    
    # Must be at least 8 digits
    if len(digits) < 8:
        return phone
    
    # Australian mobile: 04XX XXX XXX
    if digits.startswith('04') and len(digits) == 10:
        return f"{digits[:4]} {digits[4:7]} {digits[7:]}"
    
    # Australian landline with area code: (0X) XXXX XXXX
    if digits.startswith('0') and len(digits) == 10 and digits[1] in '23578':
        return f"({digits[:2]}) {digits[2:6]} {digits[6:]}"
    
    # 1300/1800 numbers: 1XXX XXX XXX
    if digits.startswith('1') and len(digits) == 10:
        return f"{digits[:4]} {digits[4:7]} {digits[7:]}"
    
    # International format starting with 61
    if digits.startswith('61') and len(digits) >= 11:
        # Convert to Australian format
        aus_digits = '0' + digits[2:]
        return format_australian_phone(aus_digits)
    
    # Return original if we can't determine format
    return phone

def extract_phone_from_text(text):
    """Extract phone number from text like 'Phone: 9087 8379' or 'Mobile: 0412345678'"""
    if not text:
        return None
    
    # More specific patterns that require actual digits
    patterns = [
        r'(?:phone|mobile|ph|mob|m|t)[\s:]+(\d[\d\s\(\)\-]+\d)',  # Phone: XXXXX
        r'(\+?61[\s]?[2-478][\s]?\d{4}[\s]?\d{4})',                # International
        r'(\(0\d\)[\s]?\d{4}[\s]?\d{4})',                          # (0X) XXXX XXXX
        r'(0[2-478]\d{8})',                                         # 0XXXXXXXXX
        r'(04\d{2}[\s]?\d{3}[\s]?\d{3})',                          # Mobile
        r'(1[38]00[\s]?\d{3}[\s]?\d{3})',                          # 1300/1800
    ]
    
    for pattern in patterns:
        match = re.search(pattern, text, re.IGNORECASE)
        if match:
            phone_str = match.group(1).strip()
            # Validate it's actually a phone number (has at least 8 digits)
            if len(normalize_phone(phone_str)) >= 8:
                return phone_str
    
    return None

def main():
    print("═" * 80)
    print("  Fix Duplicate Phone Numbers")
    print("  Clean up duplicates and format consistently")
    print("═" * 80)
    print()
    print("🔍 Reading Victorian Therapists database...\n")
    
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
    
    fixed_count = 0
    
    for entry in all_results:
        props = entry.get('properties', {})
        page_id = entry.get('id')
        
        # Extract current values
        first_name = get_property_value(props.get('First Name'))
        last_name = get_property_value(props.get('Last Name'))
        fullname = get_property_value(props.get('Fullname'))
        
        # Construct the display name
        if first_name and last_name:
            name = f"{first_name} {last_name}"
        elif fullname:
            name = fullname
        elif first_name:
            name = first_name
        else:
            name = "Unknown"
        
        phone = get_property_value(props.get('Phone'))
        other_contacts = get_property_value(props.get('Other contact details, social media, etc.'))
        
        if not other_contacts or not other_contacts.strip():
            continue
        
        # Check if there's a phone number in other contacts
        other_phone = extract_phone_from_text(other_contacts)
        
        if not other_phone:
            continue
        
        # Normalize both for comparison
        normalized_phone = normalize_phone(phone)
        normalized_other = normalize_phone(other_phone)
        
        # Check if they're the same number
        is_duplicate = False
        
        if normalized_phone == normalized_other:
            is_duplicate = True
        elif normalized_other and normalized_phone:
            # Check if one is a substring of the other (e.g., "90878379" in "0390878379")
            if len(normalized_other) < len(normalized_phone):
                # Other might be missing area code
                if normalized_phone.endswith(normalized_other):
                    is_duplicate = True
        
        if is_duplicate:
            print(f"📝 Fixing: {name}")
            print(f"   Phone column:     \"{phone}\"")
            print(f"   Other Contacts:   \"{other_contacts}\"")
            print(f"   Extracted phone:  \"{other_phone}\"")
            
            # Determine best format (prefer the one with area code)
            best_phone = phone if phone and len(normalized_phone) >= len(normalized_other) else other_phone
            formatted_phone = format_australian_phone(best_phone)
            
            print(f"   ✓ Best format:    \"{formatted_phone}\"")
            
            # Update the database
            updates = {
                'Phone': {'rich_text': [{'text': {'content': formatted_phone}}]},
                'Other contact details, social media, etc.': {'rich_text': []}
            }
            
            update_url = f"https://api.notion.com/v1/pages/{page_id}"
            update_payload = {"properties": updates}
            
            update_response = requests.patch(update_url, headers=HEADERS, json=update_payload)
            
            if update_response.status_code == 200:
                print(f"   ✅ Updated successfully")
                fixed_count += 1
            else:
                print(f"   ❌ Error: {update_response.json()}")
            
            print("   " + "─" * 76)
            print()
            
            # Rate limiting
            time.sleep(0.334)
    
    print()
    print("═" * 80)
    print()
    print("📊 Summary:")
    print(f"   Duplicates fixed: {fixed_count}")
    print()
    print("✅ Done!")

if __name__ == "__main__":
    main()

