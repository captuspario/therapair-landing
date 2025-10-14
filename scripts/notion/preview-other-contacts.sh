#!/bin/bash

# Load environment variables
source .env 2>/dev/null || {
    echo "❌ Error: .env file not found"
    exit 1
}

if [ -z "$NOTION_TOKEN" ] || [ -z "$THERAPISTS_DATABASE_ID" ]; then
    echo "❌ Error: NOTION_TOKEN and THERAPISTS_DATABASE_ID required in .env"
    exit 1
fi

echo "═══════════════════════════════════════════════════════════════════════════════="
echo "  Preview \"Other Contacts\" Cleanup"
echo "  Shows what will be changed WITHOUT making any updates"
echo "═══════════════════════════════════════════════════════════════════════════════="
echo

echo "🔍 Reading Victorian Therapists database..."
echo

# Query the database
RESPONSE=$(curl -s -X POST "https://api.notion.com/v1/databases/${THERAPISTS_DATABASE_ID}/query" \
  -H "Authorization: Bearer ${NOTION_TOKEN}" \
  -H "Notion-Version: 2022-06-28" \
  -H "Content-Type: application/json" \
  -d '{"page_size": 100}')

# Check for errors
if echo "$RESPONSE" | jq -e '.object == "error"' > /dev/null 2>&1; then
    echo "❌ Error querying database:"
    echo "$RESPONSE" | jq '.message'
    exit 1
fi

# Get total count
TOTAL=$(echo "$RESPONSE" | jq '.results | length')
echo "✅ Found $TOTAL entries in database"
echo
echo "═══════════════════════════════════════════════════════════════════════════════="
echo

ENTRIES_WITH_OTHER_CONTACTS=0
WILL_UPDATE=0
WILL_SKIP=0

# Process each entry
echo "$RESPONSE" | jq -r '.results[] | @json' | while IFS= read -r entry; do
    # Extract properties
    NAME=$(echo "$entry" | jq -r '.properties.Fullname.title[0].plain_text // "Unknown"')
    OTHER_CONTACTS=$(echo "$entry" | jq -r '.properties["Other contacts (specify if it is mobile, phone, facebook page, business name, etc)"].rich_text[0].plain_text // ""')
    EXISTING_PHONE=$(echo "$entry" | jq -r '.properties.Phone.phone_number // ""')
    EXISTING_FACEBOOK=$(echo "$entry" | jq -r '.properties.Facebook.url // ""')
    EXISTING_INSTAGRAM=$(echo "$entry" | jq -r '.properties.Instagram.url // ""')
    EXISTING_TWITTER=$(echo "$entry" | jq -r '.properties["Twitter/X"].url // ""')
    EXISTING_LINKEDIN=$(echo "$entry" | jq -r '.properties.LinkedIn.url // ""')
    
    # Skip if no other contacts data
    if [ -z "$OTHER_CONTACTS" ] || [ "$OTHER_CONTACTS" = "null" ]; then
        continue
    fi
    
    ((ENTRIES_WITH_OTHER_CONTACTS++))
    
    echo "📝 $NAME"
    echo "   Current \"Other Contacts\": \"$OTHER_CONTACTS\""
    echo
    echo "   📊 Existing data:"
    
    HAS_EXISTING=false
    [ -n "$EXISTING_PHONE" ] && [ "$EXISTING_PHONE" != "null" ] && echo "      Phone: $EXISTING_PHONE" && HAS_EXISTING=true
    [ -n "$EXISTING_FACEBOOK" ] && [ "$EXISTING_FACEBOOK" != "null" ] && echo "      Facebook: $EXISTING_FACEBOOK" && HAS_EXISTING=true
    [ -n "$EXISTING_INSTAGRAM" ] && [ "$EXISTING_INSTAGRAM" != "null" ] && echo "      Instagram: $EXISTING_INSTAGRAM" && HAS_EXISTING=true
    [ -n "$EXISTING_TWITTER" ] && [ "$EXISTING_TWITTER" != "null" ] && echo "      Twitter: $EXISTING_TWITTER" && HAS_EXISTING=true
    [ -n "$EXISTING_LINKEDIN" ] && [ "$EXISTING_LINKEDIN" != "null" ] && echo "      LinkedIn: $EXISTING_LINKEDIN" && HAS_EXISTING=true
    
    if [ "$HAS_EXISTING" = false ]; then
        echo "      (No existing social media data)"
    fi
    
    echo
    
    # Parse the other contacts field
    WILL_CHANGE=false
    echo "   ✨ Proposed changes:"
    
    # Check for phone numbers
    if echo "$OTHER_CONTACTS" | grep -qE '(\+?61|0)\s?[2-478]\s?\d{4}\s?\d{4}'; then
        PHONE=$(echo "$OTHER_CONTACTS" | grep -oE '(\+?61|0)\s?[2-478]\s?\d{4}\s?\d{4}' | head -1)
        if [ -z "$EXISTING_PHONE" ] || [ "$EXISTING_PHONE" = "null" ]; then
            echo "      📱 Phone: \"$PHONE\""
            WILL_CHANGE=true
        fi
    fi
    
    # Check for social media patterns
    # Facebook & Instagram combined
    if echo "$OTHER_CONTACTS" | grep -qiE '(facebook|fb)\s*(& |and )\s*(instagram|ig)'; then
        USERNAME=$(echo "$OTHER_CONTACTS" | grep -oiE '(facebook|fb)\s*(& |and )\s*(instagram|ig)\s*(is|:|-)\s*([a-z0-9_.]+)' | sed -E 's/.*(is|:|-)\s*//i')
        if [ -n "$USERNAME" ]; then
            if [ -z "$EXISTING_FACEBOOK" ] || [ "$EXISTING_FACEBOOK" = "null" ]; then
                echo "      👥 Facebook: \"https://facebook.com/$USERNAME\""
                WILL_CHANGE=true
            fi
            if [ -z "$EXISTING_INSTAGRAM" ] || [ "$EXISTING_INSTAGRAM" = "null" ]; then
                echo "      📸 Instagram: \"https://instagram.com/$USERNAME\""
                WILL_CHANGE=true
            fi
        fi
    fi
    
    if [ "$WILL_CHANGE" = true ]; then
        echo "      🧹 Will clear \"Other Contacts\" field"
        echo "   ────────────────────────────────────────────────────────────────────────────"
        echo
        ((WILL_UPDATE++))
    else
        echo "      ℹ️  No changes needed (data already exists or can't parse)"
        echo "   ────────────────────────────────────────────────────────────────────────────"
        echo
        ((WILL_SKIP++))
    fi
    
done

echo
echo "═══════════════════════════════════════════════════════════════════════════════="
echo
echo "📊 Preview Summary:"
echo "   Total entries in database: $TOTAL"
echo "   Entries with \"Other Contacts\" data: (processing...)"
echo
echo "💡 To apply these changes, run:"
echo "   bash scripts/notion/clean-other-contacts.sh"
echo
echo "✅ Preview complete!"

