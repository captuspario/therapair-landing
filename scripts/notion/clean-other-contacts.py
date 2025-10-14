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

def parse_other_contacts(text, existing_phone):
    """Parse the Other Contacts field intelligently"""
    result = {
        'phone': None,
        'facebook': None,
        'instagram': None,
        'twitter': None,
        'linkedin': None,
        'should_clear': False
    }
    
    if not text or not text.strip():
        return result
    
    # Extract phone numbers (Australian format)
    phone_patterns = [
        r'(?:\+61|0)\s?[2-478]\s?\d{4}\s?\d{4}',
        r'\b0[2-478]\d{8}\b',
        r'\b04\d{2}\s?\d{3}\s?\d{3}\b'
    ]
    
    for pattern in phone_patterns:
        match = re.search(pattern, text)
        if match:
            found_phone = match.group(0)
            normalized_found = found_phone.replace(' ', '')
            normalized_existing = existing_phone.replace(' ', '') if existing_phone else ''
            
            if not normalized_existing or normalized_found != normalized_existing:
                result['phone'] = found_phone
                break
    
    # Parse social media
    # Combined pattern: "facebook & instagram is [name]"
    combined_match = re.search(r'(?:facebook|fb)\s*(?:&|and)\s*(?:instagram|ig|insta)\s*(?:is|:|-)\s*([a-z0-9_.]+)', text, re.IGNORECASE)
    
    if combined_match:
        username = combined_match.group(1)
        result['facebook'] = f"https://facebook.com/{username}"
        result['instagram'] = f"https://instagram.com/{username}"
    else:
        # Individual patterns
        fb_match = re.search(r'(?:facebook|fb)\s*(?:is|:|-)\s*([a-z0-9_.]+)', text, re.IGNORECASE)
        if fb_match:
            result['facebook'] = f"https://facebook.com/{fb_match.group(1)}"
        
        ig_match = re.search(r'(?:instagram|ig|insta)\s*(?:is|:|-)\s*([a-z0-9_.]+)', text, re.IGNORECASE)
        if ig_match:
            result['instagram'] = f"https://instagram.com/{ig_match.group(1)}"
    
    # Twitter
    twitter_match = re.search(r'(?:twitter|x)\s*(?:is|:|-)\s*@?([a-z0-9_]+)', text, re.IGNORECASE)
    if twitter_match:
        result['twitter'] = f"https://twitter.com/{twitter_match.group(1)}"
    
    # LinkedIn
    linkedin_match = re.search(r'linkedin\s*(?:is|:|-)\s*([a-z0-9\-]+)', text, re.IGNORECASE)
    if linkedin_match:
        result['linkedin'] = f"https://linkedin.com/in/{linkedin_match.group(1)}"
    
    # Determine if we should clear the field
    if any([result['phone'], result['facebook'], result['instagram'], result['twitter'], result['linkedin']]):
        result['should_clear'] = True
    elif existing_phone and existing_phone.lower().replace(' ', '') in text.lower().replace(' ', ''):
        result['should_clear'] = True  # It's just a duplicate phone number
    
    return result

def main():
    print("═" * 80)
    print("  Clean \"Other Contacts\" Column")
    print("  Applying changes to Notion database")
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
    
    processed_count = 0
    updated_count = 0
    skipped_count = 0
    
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
        
        other_contacts = get_property_value(props.get('Other contact details, social media, etc.'))
        existing_phone = get_property_value(props.get('Phone'))
        existing_facebook = get_property_value(props.get('Facebook'))
        existing_instagram = get_property_value(props.get('Instagram'))
        existing_twitter = get_property_value(props.get('Twitter/X'))
        existing_linkedin = get_property_value(props.get('LinkedIn'))
        
        processed_count += 1
        
        if not other_contacts or not other_contacts.strip():
            continue
        
        # Parse the other contacts field
        parsed = parse_other_contacts(other_contacts, existing_phone)
        
        # Build update payload
        updates = {}
        has_updates = False
        
        # Only update if we found new data
        if parsed['phone'] and not existing_phone:
            updates['Phone'] = {'rich_text': [{'text': {'content': parsed['phone']}}]}
            has_updates = True
        
        if parsed['facebook'] and not existing_facebook:
            updates['Facebook'] = {'url': parsed['facebook']}
            has_updates = True
        
        if parsed['instagram'] and not existing_instagram:
            updates['Instagram'] = {'rich_text': [{'text': {'content': parsed['instagram']}}]}
            has_updates = True
        
        if parsed['twitter'] and not existing_twitter:
            updates['Twitter/X'] = {'rich_text': [{'text': {'content': parsed['twitter']}}]}
            has_updates = True
        
        if parsed['linkedin'] and not existing_linkedin:
            updates['LinkedIn'] = {'url': parsed['linkedin']}
            has_updates = True
        
        # Clear the "Other Contacts" field if we extracted data or it's a duplicate
        if parsed['should_clear']:
            updates['Other contact details, social media, etc.'] = {'rich_text': []}
            has_updates = True
        
        if has_updates:
            print(f"📝 Updating: {name}")
            print(f"   Original: \"{other_contacts}\"")
            
            if parsed['phone']:
                print(f"   ✓ Phone: {parsed['phone']}")
            if parsed['facebook']:
                print(f"   ✓ Facebook: {parsed['facebook']}")
            if parsed['instagram']:
                print(f"   ✓ Instagram: {parsed['instagram']}")
            if parsed['twitter']:
                print(f"   ✓ Twitter: {parsed['twitter']}")
            if parsed['linkedin']:
                print(f"   ✓ LinkedIn: {parsed['linkedin']}")
            if parsed['should_clear']:
                print(f"   ✓ Clearing \"Other Contacts\" field")
            
            # Update the page
            update_url = f"https://api.notion.com/v1/pages/{page_id}"
            update_payload = {"properties": updates}
            
            update_response = requests.patch(update_url, headers=HEADERS, json=update_payload)
            
            if update_response.status_code == 200:
                print(f"   ✅ Updated successfully")
                updated_count += 1
            else:
                print(f"   ❌ Error: {update_response.json()}")
            
            print()
            
            # Rate limiting - be nice to Notion API (~3 requests/second)
            time.sleep(0.334)
        else:
            skipped_count += 1
    
    print()
    print("═" * 80)
    print()
    print("📊 Summary:")
    print(f"   Total entries processed: {processed_count}")
    print(f"   Entries updated: {updated_count}")
    print(f"   Entries skipped: {skipped_count}")
    print()
    print("✅ Done!")

if __name__ == "__main__":
    main()

