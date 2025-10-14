#!/usr/bin/env python3
import os
import json
import requests
import re
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
    
    # Pattern for phone numbers in text
    patterns = [
        r'(?:phone|mobile|ph|mob|m|t)[\s:]*(\+?61[\s\d]+)',  # With prefix
        r'(?:phone|mobile|ph|mob|m|t)[\s:]*([\d\s\(\)]+)',    # Without prefix
        r'(\+?61[\s]?[2-478][\s]?\d{4}[\s]?\d{4})',           # International format
        r'(\(0\d\)[\s]?\d{4}[\s]?\d{4})',                      # (0X) XXXX XXXX
        r'(0[2-478]\d{8})',                                     # 0XXXXXXXXX
        r'(04\d{2}[\s]?\d{3}[\s]?\d{3})',                      # Mobile
        r'(\d{4}[\s]?\d{3}[\s]?\d{3})',                        # XXXX XXX XXX (missing area code)
    ]
    
    for pattern in patterns:
        match = re.search(pattern, text, re.IGNORECASE)
        if match:
            return match.group(1).strip()
    
    return None

def main():
    print("═" * 80)
    print("  Find Duplicate Phone Numbers")
    print("  Compare Phone column with Other Contacts column")
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
    
    duplicates_found = 0
    other_phones_found = 0
    
    for entry in all_results:
        props = entry.get('properties', {})
        
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
        
        if other_phone:
            other_phones_found += 1
            
            # Normalize both for comparison
            normalized_phone = normalize_phone(phone)
            normalized_other = normalize_phone(other_phone)
            
            # Check if they're the same number
            # Handle case where one has area code and other doesn't
            is_duplicate = False
            
            if normalized_phone == normalized_other:
                is_duplicate = True
            elif normalized_other and normalized_phone:
                # Check if one is a substring of the other (e.g., "90878379" in "0390878379")
                if normalized_other in normalized_phone or normalized_phone in normalized_other:
                    # Further check - they should match when we add area code
                    if len(normalized_other) < len(normalized_phone):
                        # Other might be missing area code
                        if normalized_phone.endswith(normalized_other):
                            is_duplicate = True
            
            if is_duplicate:
                duplicates_found += 1
                print(f"📝 {name}")
                print(f"   Phone column:         \"{phone}\" → normalized: {normalized_phone}")
                print(f"   Other Contacts:       \"{other_contacts}\"")
                print(f"   Extracted phone:      \"{other_phone}\" → normalized: {normalized_other}")
                print(f"   ✅ DUPLICATE DETECTED")
                
                # Suggest best format
                best_format = format_australian_phone(phone if phone else other_phone)
                print(f"   💡 Suggested format:  \"{best_format}\"")
                print(f"   🧹 Action: Keep in Phone column, clear from Other Contacts")
                print("   " + "─" * 76)
                print()
            else:
                # Different numbers
                print(f"⚠️  {name}")
                print(f"   Phone column:         \"{phone}\"")
                print(f"   Other Contacts:       \"{other_contacts}\"")
                print(f"   Extracted:            \"{other_phone}\"")
                print(f"   ⚠️  DIFFERENT NUMBERS - Manual review needed")
                print("   " + "─" * 76)
                print()
    
    print()
    print("═" * 80)
    print()
    print("📊 Summary:")
    print(f"   Phone numbers found in Other Contacts: {other_phones_found}")
    print(f"   Duplicates detected: {duplicates_found}")
    print(f"   Different numbers needing review: {other_phones_found - duplicates_found}")
    print()
    print("💡 Next step: Run the cleanup script to fix duplicates")
    print("   python3 scripts/notion/fix-duplicate-phones.py")
    print()
    print("✅ Analysis complete!")

if __name__ == "__main__":
    main()

