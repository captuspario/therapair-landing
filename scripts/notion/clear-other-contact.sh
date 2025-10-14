#!/bin/bash

# Clear "Other contact" column after extracting data

# Load from environment or .env file
source .env 2>/dev/null || true
NOTION_TOKEN="${NOTION_TOKEN}"
DATABASE_ID="${THERAPISTS_DATABASE_ID}"
API_VERSION="2022-06-28"

echo "════════════════════════════════════════════════════════════"
echo "  Clear 'Other Contact' Column"
echo "════════════════════════════════════════════════════════════"
echo ""
echo "This will clear the 'Other contact details, social media, etc.'"
echo "column since we've extracted all useful data to specific columns."
echo ""
read -p "Continue? (y/n): " -n 1 -r
echo ""

if [[ ! $REPLY =~ ^[Yy]$ ]]; then
    echo "Cancelled."
    exit 0
fi

echo ""
echo "🔍 Fetching all entries..."
echo ""

rm -f /tmp/therapists_clear.jsonl
HAS_MORE=true
START_CURSOR=""
TOTAL=0

while [ "$HAS_MORE" = "true" ]; do
  if [ -z "$START_CURSOR" ]; then
    RESPONSE=$(curl -s -X POST "https://api.notion.com/v1/databases/${DATABASE_ID}/query" \
      -H "Authorization: Bearer ${NOTION_TOKEN}" \
      -H "Notion-Version: ${API_VERSION}" \
      -H "Content-Type: application/json" \
      --data '{"page_size": 100}')
  else
    RESPONSE=$(curl -s -X POST "https://api.notion.com/v1/databases/${DATABASE_ID}/query" \
      -H "Authorization: Bearer ${NOTION_TOKEN}" \
      -H "Notion-Version: ${API_VERSION}" \
      -H "Content-Type: application/json" \
      --data "{\"page_size\": 100, \"start_cursor\": \"$START_CURSOR\"}")
  fi
  
  PAGE_COUNT=$(echo "$RESPONSE" | jq -r '.results | length')
  TOTAL=$((TOTAL + PAGE_COUNT))
  echo "$RESPONSE" | jq -c '.results[]' >> /tmp/therapists_clear.jsonl
  
  HAS_MORE=$(echo "$RESPONSE" | jq -r '.has_more')
  START_CURSOR=$(echo "$RESPONSE" | jq -r '.next_cursor')
done

echo "✅ Found: $TOTAL therapists"
echo ""
echo "────────────────────────────────────────────────────────────"
echo ""
echo "🧹 Clearing 'Other contact' column..."
echo ""

COUNTER=0
CLEARED=0

while read -r page; do
  COUNTER=$((COUNTER + 1))
  
  PAGE_ID=$(echo "$page" | jq -r '.id')
  FULL_NAME=$(echo "$page" | jq -r '.properties.Name.title[0].text.content // ""')
  CONTACT=$(echo "$page" | jq -r '.properties["Other contact details, social media, etc."].rich_text[0].text.content // ""')
  PHONE=$(echo "$page" | jq -r '.properties.Phone.rich_text[0].text.content // ""')
  INSTAGRAM=$(echo "$page" | jq -r '.properties.Instagram.rich_text[0].text.content // ""')
  FACEBOOK=$(echo "$page" | jq -r '.properties.Facebook.url // ""')
  
  # Only clear if we have extracted data OR if it's just a phone/social
  if [ ! -z "$CONTACT" ]; then
    # Check if we extracted anything useful
    if [ ! -z "$PHONE" ] || [ ! -z "$INSTAGRAM" ] || [ ! -z "$FACEBOOK" ]; then
      echo "$COUNTER/$TOTAL. $FULL_NAME - Clearing (data extracted)"
      
      curl -s -X PATCH "https://api.notion.com/v1/pages/${PAGE_ID}" \
        -H "Authorization: Bearer ${NOTION_TOKEN}" \
        -H "Notion-Version: ${API_VERSION}" \
        -H "Content-Type: application/json" \
        --data '{"properties": {"Other contact details, social media, etc.": {"rich_text": []}}}' > /dev/null
      
      CLEARED=$((CLEARED + 1))
      sleep 0.3
    else
      echo "$COUNTER/$TOTAL. $FULL_NAME - Keeping (no extraction done, may need manual review)"
    fi
  fi
  
done < /tmp/therapists_clear.jsonl

rm /tmp/therapists_clear.jsonl

echo ""
echo "════════════════════════════════════════════════════════════"
echo "  CLEANUP COMPLETE!"
echo "════════════════════════════════════════════════════════════"
echo ""
echo "📊 Summary:"
echo "   Total processed: $TOTAL"
echo "   Entries cleared: $CLEARED"
echo "   Entries kept: $((TOTAL - CLEARED)) (may need manual review)"
echo ""
echo "✅ 'Other contact' column is now clean!"
echo ""
echo "════════════════════════════════════════════════════════════"

