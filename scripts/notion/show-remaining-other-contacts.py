#!/usr/bin/env python3
import os
import requests
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
    return ""

url = f'https://api.notion.com/v1/databases/{THERAPISTS_DB_ID}/query'
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

print(f"═" * 80)
print(f"  Remaining Entries in 'Other Contacts' Column")
print(f"═" * 80)
print()

remaining = []
for entry in all_results:
    props = entry.get('properties', {})
    first_name = get_property_value(props.get('First Name'))
    last_name = get_property_value(props.get('Last Name'))
    name = f"{first_name} {last_name}".strip() if first_name or last_name else "Unknown"
    
    other = get_property_value(props.get('Other contact details, social media, etc.'))
    if other and other.strip():
        remaining.append((name, other))

for i, (name, content) in enumerate(remaining, 1):
    print(f"{i}. {name}")
    print(f"   \"{content}\"")
    print()

print(f"═" * 80)
print(f"Total remaining: {len(remaining)}")

