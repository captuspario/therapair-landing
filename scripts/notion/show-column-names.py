#!/usr/bin/env python3
import os
import json
import requests
from dotenv import load_dotenv

# Load environment variables
load_dotenv()

NOTION_TOKEN = os.getenv('NOTION_TOKEN')
THERAPISTS_DB_ID = os.getenv('THERAPISTS_DATABASE_ID')

NOTION_VERSION = "2022-06-28"
HEADERS = {
    "Authorization": f"Bearer {NOTION_TOKEN}",
    "Notion-Version": NOTION_VERSION,
    "Content-Type": "application/json"
}

def main():
    print("🔍 Fetching database schema...\n")
    
    # Get database schema
    url = f"https://api.notion.com/v1/databases/{THERAPISTS_DB_ID}"
    response = requests.get(url, headers=HEADERS)
    
    if response.status_code != 200:
        print(f"❌ Error: {response.json()}")
        exit(1)
    
    data = response.json()
    properties = data.get('properties', {})
    
    print(f"📊 Database: {data.get('title', [{}])[0].get('plain_text', 'Unknown')}\n")
    print(f"Found {len(properties)} columns:\n")
    print("═" * 80)
    
    for i, (name, prop) in enumerate(properties.items(), 1):
        prop_type = prop.get('type', 'unknown')
        print(f"{i:3d}. {name}")
        print(f"      Type: {prop_type}")
        print()
    
    print("═" * 80)
    
    # Now fetch one entry to see what columns have data
    print("\n🔍 Fetching sample entry...\n")
    
    url = f"https://api.notion.com/v1/databases/{THERAPISTS_DB_ID}/query"
    payload = {"page_size": 1}
    response = requests.post(url, headers=HEADERS, json=payload)
    
    if response.status_code != 200:
        print(f"❌ Error: {response.json()}")
        exit(1)
    
    data = response.json()
    results = data.get('results', [])
    
    if not results:
        print("No entries found in database")
        return
    
    entry_props = results[0].get('properties', {})
    
    print("Sample entry property values:")
    print("═" * 80)
    
    for name, prop in entry_props.items():
        prop_type = prop.get('type')
        value = ""
        
        if prop_type == 'title':
            value = prop.get('title', [{}])[0].get('plain_text', '') if prop.get('title') else ''
        elif prop_type == 'rich_text':
            value = prop.get('rich_text', [{}])[0].get('plain_text', '') if prop.get('rich_text') else ''
        elif prop_type == 'phone_number':
            value = prop.get('phone_number', '')
        elif prop_type == 'url':
            value = prop.get('url', '')
        elif prop_type == 'email':
            value = prop.get('email', '')
        elif prop_type == 'select':
            value = prop.get('select', {}).get('name', '') if prop.get('select') else ''
        elif prop_type == 'multi_select':
            value = ', '.join([s.get('name', '') for s in prop.get('multi_select', [])])
        elif prop_type == 'checkbox':
            value = '✓' if prop.get('checkbox') else '✗'
        elif prop_type == 'date':
            value = prop.get('date', {}).get('start', '') if prop.get('date') else ''
        
        if value:
            # Truncate long values
            display_value = value[:100] + '...' if len(str(value)) > 100 else value
            print(f"• {name}")
            print(f"  Value: {display_value}")
            print()

if __name__ == "__main__":
    main()

