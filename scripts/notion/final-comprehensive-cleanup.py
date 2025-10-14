#!/usr/bin/env python3
"""
Final comprehensive cleanup - handles all remaining edge cases:
- Instagram handles with parentheses: "@handle (instagram)"
- Multiple URLs in one line
- Incomplete phone numbers (missing leading 0)
- LinkedIn/Facebook URLs without https://
- Handle-only entries like "@handle"
- Business names for website column
"""

import os
import requests
import re
import time
from dotenv import load_dotenv

load_dotenv()

NOTION_TOKEN = os.getenv('NOTION_TOKEN')
THERAPISTS_DB_ID = os.getenv('THERAPISTS_DATABASE_ID')

HEADERS = {
    'Authorization': f'Bearer {NOTION_TOKEN}',
    'Notion-Version': '2022-06-28',
    'Content-Type': 'application/json'
}

def get_property_value(prop):
    if not prop:
        return ""
    prop_type = prop.get('type')
    if prop_type == 'title':
        return prop.get('title', [{}])[0].get('plain_text', '') if prop.get('title') else ''
    elif prop_type == 'rich_text':
        return prop.get('rich_text', [{}])[0].get('plain_text', '') if prop.get('rich_text') else ''
    elif prop_type == 'url':
        return prop.get('url', '')
    return ""

def comprehensive_interpret(text, context):
    """Final comprehensive interpretation with all edge cases"""
    result = {
        'instagram': None,
        'facebook': None,
        'twitter': None,
        'linkedin': None,
        'website': None,
        'notes': None,
        'should_clear': False,
        'reasoning': []
    }
    
    if not text or not text.strip():
        return result
    
    text_clean = text.strip()
    text_lower = text_clean.lower()
    
    # === MULTIPLE URLs IN ONE LINE ===
    url_pattern = r'https?://[^\s]+'
    urls = re.findall(url_pattern, text_clean)
    
    for url in urls:
        url_lower = url.lower()
        
        if 'instagram.com' in url_lower and not context.get('existing_instagram'):
            # Extract handle
            handle_match = re.search(r'instagram\.com/([^/?]+)', url)
            if handle_match:
                handle = handle_match.group(1)
                result['instagram'] = f"@{handle}"
                result['reasoning'].append(f"Extracted Instagram: @{handle}")
        
        elif 'facebook.com' in url_lower and not context.get('existing_facebook'):
            result['facebook'] = url
            result['reasoning'].append(f"Extracted Facebook URL")
        
        elif 'linkedin.com' in url_lower and not context.get('existing_linkedin'):
            result['linkedin'] = url
            result['reasoning'].append(f"Extracted LinkedIn URL")
        
        elif 'twitter.com' in url_lower and not context.get('existing_twitter'):
            handle_match = re.search(r'twitter\.com/([^/?]+)', url)
            if handle_match:
                result['twitter'] = f"https://twitter.com/{handle_match.group(1)}"
                result['reasoning'].append(f"Extracted Twitter")
        
        elif not urls[0] == url:  # Not the first URL
            continue
        elif not context.get('existing_website'):
            result['website'] = url
            result['reasoning'].append(f"Extracted website")
    
    # === INSTAGRAM WITH PARENTHESES ===
    # "@handle (instagram)" or "handle (Instagram)"
    ig_paren_match = re.search(r'@?([a-z0-9_.]+)\s*\(instagram\)', text_lower)
    if ig_paren_match and not context.get('existing_instagram'):
        handle = ig_paren_match.group(1)
        result['instagram'] = f"@{handle}"
        result['reasoning'].append(f"Extracted Instagram from parentheses: @{handle}")
    
    # "Instagram: @handle" or "Instagram @handle"
    ig_colon_match = re.search(r'instagram[\s:]+@?([a-z0-9_.]+)', text_lower)
    if ig_colon_match and not context.get('existing_instagram') and not result['instagram']:
        handle = ig_colon_match.group(1)
        if handle not in ['sound', 'and']:  # Filter out common words
            result['instagram'] = f"@{handle}"
            result['reasoning'].append(f"Extracted Instagram: @{handle}")
    
    # Just "@handle" alone
    lone_handle_match = re.match(r'^@([a-z0-9_.]+)$', text_lower)
    if lone_handle_match and not context.get('existing_instagram'):
        handle = lone_handle_match.group(1)
        result['instagram'] = f"@{handle}"
        result['reasoning'].append(f"Extracted Instagram handle: @{handle}")
    
    # "handle_name" alone (username without @)
    if not result['instagram'] and not context.get('existing_instagram'):
        if re.match(r'^[a-z0-9_.]+$', text_lower) and len(text_clean) > 3 and '_' in text_clean:
            result['instagram'] = f"@{text_clean}"
            result['reasoning'].append(f"Inferred Instagram handle: @{text_clean}")
    
    # === FACEBOOK WITHOUT HTTPS ===
    if 'facebook.com' in text_lower and not result['facebook'] and not context.get('existing_facebook'):
        fb_match = re.search(r'(?:www\.)?facebook\.com/([^\s]+)', text_lower)
        if fb_match:
            result['facebook'] = f"https://facebook.com/{fb_match.group(1)}"
            result['reasoning'].append(f"Added https:// to Facebook URL")
    
    # "Facebook: pagename" or "Facebook pagename"
    if not result['facebook'] and not context.get('existing_facebook'):
        fb_page_match = re.search(r'facebook[\s:]+@?([a-z0-9_.]+)', text_lower)
        if fb_page_match:
            pagename = fb_page_match.group(1)
            if pagename not in ['page', 'the', 'and']:
                result['facebook'] = f"https://facebook.com/{pagename}"
                result['reasoning'].append(f"Constructed Facebook URL: {pagename}")
    
    # === LINKEDIN WITHOUT HTTPS ===
    if 'linkedin.com' in text_lower and not result['linkedin'] and not context.get('existing_linkedin'):
        li_match = re.search(r'linkedin\.com/in/([^\s]+)', text_lower)
        if li_match:
            result['linkedin'] = f"https://linkedin.com/in/{li_match.group(1)}"
            result['reasoning'].append(f"Added https:// to LinkedIn URL")
    
    # === TWITTER/X HANDLES ===
    if not result['twitter'] and not context.get('existing_twitter'):
        tw_match = re.search(r'(?:twitter|x)[\s:]+@?([a-z0-9_]+)', text_lower)
        if tw_match:
            result['twitter'] = f"https://twitter.com/{tw_match.group(1)}"
            result['reasoning'].append(f"Extracted Twitter handle")
    
    # === INCOMPLETE PHONE NUMBERS ===
    # Numbers like "412930789" (9 digits, missing leading 0)
    if re.match(r'^\d{9}$', text_clean):
        result['notes'] = f"Possible phone (missing leading 0): {text_clean}"
        result['reasoning'].append("Incomplete phone - added to notes")
    
    # === BUSINESS NAMES AS WEBSITES ===
    # If it looks like a business name with no other data
    if not any([result['instagram'], result['facebook'], result['twitter'], result['linkedin'], result['website']]):
        # Check if it's a simple name (2-4 words, no special chars except spaces)
        if re.match(r'^[A-Za-z\s]{3,50}$', text_clean) and text_clean.count(' ') <= 3:
            # It's likely just a business name - check against context
            if context.get('business_name') and context['business_name'].lower() in text_lower:
                result['should_clear'] = True
                result['reasoning'].append("Business name reference - clearing")
            else:
                result['notes'] = f"Business/practice name: {text_clean}"
                result['reasoning'].append("Added business name to notes")
        # Check for directory listing
        elif 'psychology.com.au' in text_lower or 'directory' in text_lower:
            result['notes'] = text_clean
            result['reasoning'].append("Directory reference - added to notes")
    
    # === WEBSITES WITHOUT HTTP ===
    # www.domain.com or domain.com
    if not result['website'] and not context.get('existing_website'):
        www_match = re.search(r'(?:www\.)?([a-z0-9\-]+\.[a-z]{2,}(?:/[^\s]*)?)', text_lower)
        if www_match and not any(x in text_lower for x in ['instagram', 'facebook', 'linkedin', 'twitter']):
            domain = www_match.group(0)
            result['website'] = f"https://{domain}" if not domain.startswith('http') else domain
            result['reasoning'].append(f"Constructed website URL")
    
    # === DETERMINE IF SHOULD CLEAR ===
    if any([result['instagram'], result['facebook'], result['twitter'], 
            result['linkedin'], result['website'], result['notes']]):
        result['should_clear'] = True
    
    return result

def main():
    print("═" * 80)
    print("  Final Comprehensive Cleanup")
    print("  Handling all remaining edge cases")
    print("═" * 80)
    print()
    
    url = f"https://api.notion.com/v1/databases/{THERAPISTS_DB_ID}/query"
    all_results = []
    has_more = True
    start_cursor = None
    
    while has_more:
        payload = {"page_size": 100}
        if start_cursor:
            payload["start_cursor"] = start_cursor
        
        response = requests.post(url, headers=HEADERS, json=payload)
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
        
        first_name = get_property_value(props.get('First Name'))
        last_name = get_property_value(props.get('Last Name'))
        name = f"{first_name} {last_name}".strip() if first_name or last_name else "Unknown"
        
        other_contacts = get_property_value(props.get('Other contact details, social media, etc.'))
        
        if not other_contacts or not other_contacts.strip():
            continue
        
        context = {
            'business_name': get_property_value(props.get('Business Name')),
            'existing_instagram': get_property_value(props.get('Instagram')),
            'existing_facebook': get_property_value(props.get('Facebook')),
            'existing_twitter': get_property_value(props.get('Twitter/X')),
            'existing_linkedin': get_property_value(props.get('LinkedIn')),
            'existing_website': get_property_value(props.get('Website (or alternative listing like Facebook or health engine)')),
        }
        
        interpreted = comprehensive_interpret(other_contacts, context)
        
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
        
        if interpreted['notes']:
            existing_notes = get_property_value(props.get('Admin Notes'))
            new_notes = f"{existing_notes}\n{interpreted['notes']}" if existing_notes else interpreted['notes']
            updates['Admin Notes'] = {'rich_text': [{'text': {'content': new_notes.strip()}}]}
            has_updates = True
        
        if interpreted['should_clear']:
            updates['Other contact details, social media, etc.'] = {'rich_text': []}
            has_updates = True
        
        if has_updates:
            print(f"📝 {name}")
            print(f"   Original: \"{other_contacts}\"")
            if interpreted['reasoning']:
                print(f"   🧠 Reasoning: {', '.join(interpreted['reasoning'])}")
            
            if interpreted['instagram']:
                print(f"   📸 Instagram: {interpreted['instagram']}")
            if interpreted['facebook']:
                print(f"   👥 Facebook: {interpreted['facebook']}")
            if interpreted['twitter']:
                print(f"   🐦 Twitter: {interpreted['twitter']}")
            if interpreted['linkedin']:
                print(f"   💼 LinkedIn: {interpreted['linkedin']}")
            if interpreted['website']:
                print(f"   🌐 Website: {interpreted['website']}")
            if interpreted['notes']:
                print(f"   📝 Notes: {interpreted['notes']}")
            
            update_url = f"https://api.notion.com/v1/pages/{page_id}"
            update_response = requests.patch(update_url, headers=HEADERS, json={"properties": updates})
            
            if update_response.status_code == 200:
                print(f"   ✅ Updated successfully")
                updated_count += 1
            else:
                print(f"   ❌ Error: {update_response.json().get('message', 'Unknown')}")
            
            print("   " + "─" * 76)
            print()
            time.sleep(0.334)
    
    print()
    print("═" * 80)
    print(f"📊 Updated: {updated_count} entries")
    print("✅ Final cleanup complete!")

if __name__ == "__main__":
    main()

