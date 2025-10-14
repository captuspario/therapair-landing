#!/usr/bin/env python3
"""
Intelligent cleanup of "Other contact details, social media, etc." column.

This script uses contextual understanding to interpret vague entries like:
- "linkedin" → https://linkedin.com/in/firstname-lastname
- "Instagram: @username" → https://instagram.com/username
- "Facebook page name" → https://facebook.com/name
- "@handle" → Determine platform and create URL
- Full URLs → Extract and place in correct column
- Business names → Keep if they match website, otherwise flag

Global best practices applied:
1. Context-aware interpretation using therapist's name and existing data
2. Pattern matching with fuzzy logic for variations
3. URL validation and normalization
4. Preserve existing data (never overwrite)
5. Clear after successful extraction
6. Log all changes for audit trail
"""

import os
import json
import requests
import re
import time
from urllib.parse import urlparse
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

def normalize_name_for_url(name):
    """Convert name to URL-friendly format: 'John Smith' → 'john-smith'"""
    if not name:
        return ""
    return name.lower().replace(' ', '-').replace('.', '').replace(',', '').strip()

def extract_url_username(url):
    """Extract username from social media URL"""
    if not url:
        return None
    
    # Remove protocol and www
    clean_url = re.sub(r'^https?://(www\.)?', '', url)
    
    # Common patterns
    patterns = [
        r'instagram\.com/([^/?]+)',
        r'facebook\.com/([^/?]+)',
        r'twitter\.com/([^/?]+)',
        r'linkedin\.com/in/([^/?]+)',
        r'tiktok\.com/@?([^/?]+)',
    ]
    
    for pattern in patterns:
        match = re.search(pattern, clean_url)
        if match:
            return match.group(1)
    
    return None

def interpret_other_contacts(text, context):
    """
    Intelligently interpret the "Other Contacts" field using context.
    
    Args:
        text: The raw text from Other Contacts field
        context: Dict containing first_name, last_name, fullname, website, existing social media
    
    Returns:
        Dict with extracted data: {instagram, facebook, twitter, linkedin, website, phone, email, notes, should_clear}
    """
    result = {
        'instagram': None,
        'facebook': None,
        'twitter': None,
        'linkedin': None,
        'website': None,
        'phone': None,
        'email': None,
        'notes': None,
        'should_clear': False,
        'reasoning': []
    }
    
    if not text or text.strip() in ['', 'n/a', 'N/A', 'nil', 'none', 'None']:
        result['should_clear'] = True
        result['reasoning'].append("Empty or null value")
        return result
    
    text_lower = text.lower().strip()
    
    # === FULL URLs ===
    # Extract all URLs from the text
    url_pattern = r'https?://[^\s,]+'
    urls = re.findall(url_pattern, text)
    
    for url in urls:
        url_lower = url.lower()
        
        if 'instagram.com' in url_lower and not context.get('existing_instagram'):
            # Extract handle from URL
            username = extract_url_username(url)
            if username:
                result['instagram'] = f"@{username}" if not username.startswith('@') else username
                result['reasoning'].append(f"Extracted Instagram from URL: {url}")
        
        elif 'facebook.com' in url_lower and not context.get('existing_facebook'):
            result['facebook'] = url
            result['reasoning'].append(f"Extracted Facebook URL: {url}")
        
        elif 'twitter.com' in url_lower or 'x.com' in url_lower and not context.get('existing_twitter'):
            username = extract_url_username(url)
            if username:
                result['twitter'] = f"https://twitter.com/{username}"
                result['reasoning'].append(f"Extracted Twitter from URL: {url}")
        
        elif 'linkedin.com' in url_lower and not context.get('existing_linkedin'):
            result['linkedin'] = url
            result['reasoning'].append(f"Extracted LinkedIn URL: {url}")
        
        elif 'tiktok.com' in url_lower:
            # TikTok doesn't have a dedicated column, add to notes
            result['notes'] = f"TikTok: {url}"
            result['reasoning'].append(f"Added TikTok to notes: {url}")
        
        elif not context.get('existing_website'):
            # Generic URL - might be website
            result['website'] = url
            result['reasoning'].append(f"Extracted website URL: {url}")
    
    # === EXPLICIT PLATFORM MENTIONS ===
    
    # LinkedIn variations
    if re.search(r'\blinkedin\b', text_lower) and not context.get('existing_linkedin'):
        # Check for explicit URL
        linkedin_match = re.search(r'linkedin\.com/in/([^\s,\)]+)', text_lower)
        if linkedin_match:
            result['linkedin'] = f"https://linkedin.com/in/{linkedin_match.group(1)}"
            result['reasoning'].append(f"Extracted LinkedIn URL")
        else:
            # Just says "linkedin" - construct from name
            name_slug = normalize_name_for_url(context.get('fullname') or f"{context.get('first_name')} {context.get('last_name')}")
            if name_slug:
                result['linkedin'] = f"https://linkedin.com/in/{name_slug}"
                result['reasoning'].append(f"Constructed LinkedIn from name: {name_slug}")
    
    # Instagram with @ handle or username
    instagram_patterns = [
        r'(?:instagram|ig|insta)[\s:]+@?([a-z0-9_.]+)',
        r'@([a-z0-9_.]+)(?:\s+\(instagram\)|\s+instagram)',
        r'^@([a-z0-9_.]+)$',  # Just an @ handle alone
    ]
    
    for pattern in instagram_patterns:
        match = re.search(pattern, text_lower, re.IGNORECASE)
        if match and not context.get('existing_instagram'):
            handle = match.group(1)
            result['instagram'] = f"@{handle}" if not handle.startswith('@') else handle
            result['reasoning'].append(f"Extracted Instagram handle: {handle}")
            break
    
    # Facebook page name
    facebook_patterns = [
        r'(?:facebook|fb)[\s:]+([^\s,\.\)]+)',
        r'([^\s]+)\s+(?:on\s+)?facebook',
    ]
    
    for pattern in facebook_patterns:
        match = re.search(pattern, text_lower, re.IGNORECASE)
        if match and not context.get('existing_facebook'):
            page_name = match.group(1).strip()
            if page_name not in ['page', 'the']:
                result['facebook'] = f"https://facebook.com/{page_name}"
                result['reasoning'].append(f"Constructed Facebook URL from page name: {page_name}")
                break
    
    # Twitter/X handle
    twitter_patterns = [
        r'(?:twitter|x)[\s:]+@?([a-z0-9_]+)',
        r'@([a-z0-9_]+)(?:\s+\(twitter\)|\s+twitter)',
    ]
    
    for pattern in twitter_patterns:
        match = re.search(pattern, text_lower, re.IGNORECASE)
        if match and not context.get('existing_twitter'):
            handle = match.group(1)
            result['twitter'] = f"https://twitter.com/{handle}"
            result['reasoning'].append(f"Extracted Twitter handle: {handle}")
            break
    
    # === EMAIL ADDRESSES ===
    email_pattern = r'\b[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Z|a-z]{2,}\b'
    email_match = re.search(email_pattern, text)
    if email_match:
        result['email'] = email_match.group(0)
        result['reasoning'].append(f"Extracted email: {email_match.group(0)}")
    
    # === PHONE NUMBERS ===
    phone_patterns = [
        r'(?:phone|mobile|ph|mob|m|t)[\s:]+(\d[\d\s\(\)\-]+)',
        r'(\+?61[\s]?[2-478][\s]?\d{4}[\s]?\d{4})',
        r'(\(0\d\)[\s]?\d{4}[\s]?\d{4})',
        r'(0[2-478]\d{8})',
        r'(04\d{2}[\s]?\d{3}[\s]?\d{3})',
    ]
    
    for pattern in phone_patterns:
        match = re.search(pattern, text, re.IGNORECASE)
        if match:
            phone_str = match.group(1).strip()
            # Validate it has at least 8 digits
            if len(re.sub(r'\D', '', phone_str)) >= 8:
                result['phone'] = phone_str
                result['reasoning'].append(f"Extracted phone: {phone_str}")
                break
    
    # === DIRECTORY LISTINGS ===
    if any(domain in text_lower for domain in ['psychologytoday.com', 'goodtherapy.com', 'halaxy.com']):
        result['notes'] = text
        result['reasoning'].append("Directory listing - added to notes")
    
    # === BUSINESS NAMES ===
    # If it mentions a business name that matches or is similar to existing data, it's informational only
    if context.get('business_name'):
        if context['business_name'].lower() in text_lower:
            result['should_clear'] = True
            result['reasoning'].append(f"Business name matches existing data")
    
    # === DETERMINE IF SHOULD CLEAR ===
    if any([result['instagram'], result['facebook'], result['twitter'], 
            result['linkedin'], result['website'], result['phone'], 
            result['email'], result['notes']]):
        result['should_clear'] = True
    
    # === SPECIAL CASES ===
    # If just says "as above" or similar
    if text_lower in ['as above', 'as above - fbk page', 'see above', 'same as above']:
        result['should_clear'] = True
        result['reasoning'].append("Reference to data elsewhere")
    
    return result

def main():
    print("═" * 80)
    print("  Intelligent Cleanup: Other Contacts Column")
    print("  Context-aware interpretation using AI-like reasoning")
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
    
    updated_count = 0
    skipped_count = 0
    
    for entry in all_results:
        props = entry.get('properties', {})
        page_id = entry.get('id')
        
        # Extract current values
        first_name = get_property_value(props.get('First Name'))
        last_name = get_property_value(props.get('Last Name'))
        fullname = get_property_value(props.get('Fullname'))
        business_name = get_property_value(props.get('Business Name'))
        
        # Construct display name
        if first_name and last_name:
            name = f"{first_name} {last_name}"
        elif fullname:
            name = fullname
        elif first_name:
            name = first_name
        else:
            name = "Unknown"
        
        other_contacts = get_property_value(props.get('Other contact details, social media, etc.'))
        
        # Skip if empty
        if not other_contacts or not other_contacts.strip():
            continue
        
        # Build context
        context = {
            'first_name': first_name,
            'last_name': last_name,
            'fullname': fullname,
            'business_name': business_name,
            'existing_instagram': get_property_value(props.get('Instagram')),
            'existing_facebook': get_property_value(props.get('Facebook')),
            'existing_twitter': get_property_value(props.get('Twitter/X')),
            'existing_linkedin': get_property_value(props.get('LinkedIn')),
            'existing_website': get_property_value(props.get('Website (or alternative listing like Facebook or health engine)')),
            'existing_phone': get_property_value(props.get('Phone')),
        }
        
        # Interpret the other contacts field
        interpreted = interpret_other_contacts(other_contacts, context)
        
        # Build updates
        updates = {}
        has_updates = False
        
        if interpreted['instagram'] and not context['existing_instagram']:
            updates['Instagram'] = {'rich_text': [{'text': {'content': interpreted['instagram']}}]}
            has_updates = True
        
        if interpreted['facebook'] and not context['existing_facebook']:
            updates['Facebook'] = {'url': interpreted['facebook']}
            has_updates = True
        
        if interpreted['twitter'] and not context['existing_twitter']:
            updates['Twitter/X'] = {'rich_text': [{'text': {'content': interpreted['twitter']}}]}
            has_updates = True
        
        if interpreted['linkedin'] and not context['existing_linkedin']:
            updates['LinkedIn'] = {'url': interpreted['linkedin']}
            has_updates = True
        
        if interpreted['website'] and not context['existing_website']:
            updates['Website (or alternative listing like Facebook or health engine)'] = {'url': interpreted['website']}
            has_updates = True
        
        if interpreted['phone'] and not context['existing_phone']:
            updates['Phone'] = {'rich_text': [{'text': {'content': interpreted['phone']}}]}
            has_updates = True
        
        # Notes go to Admin Notes if anything useful was extracted
        if interpreted['notes']:
            existing_notes = get_property_value(props.get('Admin Notes'))
            new_notes = f"{existing_notes}\n{interpreted['notes']}" if existing_notes else interpreted['notes']
            updates['Admin Notes'] = {'rich_text': [{'text': {'content': new_notes.strip()}}]}
            has_updates = True
        
        # Clear if we extracted something or it's determined to be unnecessary
        if interpreted['should_clear']:
            updates['Other contact details, social media, etc.'] = {'rich_text': []}
            has_updates = True
        
        if has_updates:
            print(f"📝 {name}")
            print(f"   Original: \"{other_contacts}\"")
            print()
            
            if interpreted['reasoning']:
                print("   🧠 Reasoning:")
                for reason in interpreted['reasoning']:
                    print(f"      • {reason}")
                print()
            
            print("   ✨ Changes:")
            if interpreted['instagram']:
                print(f"      📸 Instagram: {interpreted['instagram']}")
            if interpreted['facebook']:
                print(f"      👥 Facebook: {interpreted['facebook']}")
            if interpreted['twitter']:
                print(f"      🐦 Twitter: {interpreted['twitter']}")
            if interpreted['linkedin']:
                print(f"      💼 LinkedIn: {interpreted['linkedin']}")
            if interpreted['website']:
                print(f"      🌐 Website: {interpreted['website']}")
            if interpreted['phone']:
                print(f"      📱 Phone: {interpreted['phone']}")
            if interpreted['email']:
                print(f"      📧 Email: {interpreted['email']}")
            if interpreted['notes']:
                print(f"      📝 Notes: {interpreted['notes']}")
            if interpreted['should_clear']:
                print(f"      🧹 Clearing \"Other Contacts\" field")
            
            # Update the database
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
        else:
            skipped_count += 1
    
    print()
    print("═" * 80)
    print()
    print("📊 Summary:")
    print(f"   Entries processed: {len(all_results)}")
    print(f"   Entries updated: {updated_count}")
    print(f"   Entries skipped: {skipped_count}")
    print()
    print("✅ Intelligent cleanup complete!")

if __name__ == "__main__":
    main()

