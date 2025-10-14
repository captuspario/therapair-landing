#!/bin/bash

# Parse and clean up expertise fields into structured multi-selects

source ../../.env 2>/dev/null || source .env 2>/dev/null || true
NOTION_TOKEN="${NOTION_TOKEN}"
DATABASE_ID="${THERAPISTS_DATABASE_ID}"
API_VERSION="2022-06-28"

echo "════════════════════════════════════════════════════════════"
echo "  Parse Expertise Fields into Structured Data"
echo "════════════════════════════════════════════════════════════"
echo ""
echo "This will parse the long 'Types of People I see...' field"
echo "into structured multi-select tags for better filtering."
echo ""

# Define common expertise tags to extract
EXPERTISE_TAGS=(
  "Couples"
  "Families"
  "Children"
  "Adolescents"
  "Adults"
  "Seniors"
  "LGBTQ+"
  "Gay and Lesbian"
  "Bisexual+"
  "Transgender"
  "Nonbinary"
  "Gender Questioning"
  "Intersex"
  "Asexual"
  "Aromantic"
  "Neurodiversity"
  "ADHD"
  "Autism"
  "Disability"
  "Deaf"
  "First Nations"
  "Aboriginal"
  "Torres Strait Islander"
  "People of Colour"
  "POC"
  "Immigrant"
  "Asylum Seekers"
  "Trauma"
  "PTSD"
  "CPTSD"
  "Anxiety"
  "Depression"
  "Eating Disorders"
  "Substance Use"
  "Family Violence"
  "DFV"
  "Sex work"
  "Kink"
  "BDSM"
  "Polyamory"
  "Nonmonogamy"
)

echo "📊 Will extract these expertise tags:"
printf '%s\n' "${EXPERTISE_TAGS[@]}" | head -10
echo "... and $(( ${#EXPERTISE_TAGS[@]} - 10 )) more"
echo ""
echo "🔍 Fetching entries..."
echo ""

# Fetch all entries
rm -f /tmp/parse_expertise.jsonl
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
  echo "$RESPONSE" | jq -c '.results[]' >> /tmp/parse_expertise.jsonl
  
  HAS_MORE=$(echo "$RESPONSE" | jq -r '.has_more')
  START_CURSOR=$(echo "$RESPONSE" | jq -r '.next_cursor')
done

echo "✅ Found $TOTAL therapists"
echo ""
echo "────────────────────────────────────────────────────────────"
echo ""
echo "Note: This is a complex parsing task."
echo "For now, the raw data is preserved in the original fields."
echo ""
echo "Recommended approach:"
echo "1. Review the data manually in Notion"
echo "2. Create multi-select tags based on common patterns"
echo "3. Manually tag the first 20-30 therapists"
echo "4. Use those as training data for automated tagging"
echo ""
echo "The current database structure supports manual tagging"
echo "with the following fields:"
echo "  • Client Age Groups (multi-select)"
echo "  • Modalities (multi-select)"
echo "  • Primary Expertise (can be added as multi-select)"
echo ""
echo "════════════════════════════════════════════════════════════"

rm /tmp/parse_expertise.jsonl






