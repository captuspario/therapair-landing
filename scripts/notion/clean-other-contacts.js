import { Client } from '@notionhq/client';
import * as dotenv from 'dotenv';

// Load environment variables
dotenv.config();

const NOTION_TOKEN = process.env.NOTION_TOKEN || process.env.NOTION_API_KEY;
const THERAPISTS_DB_ID = process.env.THERAPISTS_DATABASE_ID;

if (!NOTION_TOKEN || !THERAPISTS_DB_ID) {
    console.error('❌ Error: NOTION_TOKEN and THERAPISTS_DATABASE_ID required');
    process.exit(1);
}

const notion = new Client({ auth: NOTION_TOKEN });

// Helper function to extract property value
function extractPropertyValue(property) {
    if (!property) return '';
    try {
        switch (property.type) {
            case 'title':
                return property.title?.[0]?.plain_text || '';
            case 'rich_text':
                return property.rich_text?.[0]?.plain_text || '';
            case 'email':
                return property.email || '';
            case 'phone_number':
                return property.phone_number || '';
            case 'url':
                return property.url || '';
            default:
                return '';
        }
    } catch {
        return '';
    }
}

// Parse "Other Contacts" field intelligently
function parseOtherContacts(otherContactsText, existingPhone) {
    const result = {
        phone: null,
        facebook: null,
        instagram: null,
        twitter: null,
        linkedin: null,
        shouldClear: false
    };

    if (!otherContactsText || otherContactsText.trim() === '') {
        return result;
    }

    const text = otherContactsText.toLowerCase().trim();
    
    // Extract phone numbers (Australian format)
    // Patterns: 0412 345 678, 0412345678, 04 1234 5678, +61 412 345 678
    const phonePatterns = [
        /(?:\+61|0)\s?[2-478]\s?\d{4}\s?\d{4}/g,
        /\b0[2-478]\d{8}\b/g,
        /\b04\d{2}\s?\d{3}\s?\d{3}\b/g
    ];
    
    for (const pattern of phonePatterns) {
        const matches = otherContactsText.match(pattern);
        if (matches && matches.length > 0) {
            // If there's already a phone number in the Phone column, skip duplicates
            const foundPhone = matches[0].replace(/\s/g, '');
            const normalizedExisting = existingPhone ? existingPhone.replace(/\s/g, '') : '';
            
            if (!normalizedExisting || foundPhone !== normalizedExisting) {
                result.phone = matches[0];
            }
        }
    }
    
    // Parse social media hints
    // "facebook & instagram is [name]" or "facebook: [name]"
    const fbPatterns = [
        /facebook\s*(?:&|and)?\s*instagram\s*(?:is|:|\-)\s*([a-z0-9_.]+)/i,
        /facebook\s*(?:is|:|\-)\s*([a-z0-9_.]+)/i,
        /fb\s*(?:is|:|\-)\s*([a-z0-9_.]+)/i
    ];
    
    const igPatterns = [
        /instagram\s*(?:&|and)?\s*facebook\s*(?:is|:|\-)\s*([a-z0-9_.]+)/i,
        /instagram\s*(?:is|:|\-)\s*([a-z0-9_.]+)/i,
        /ig\s*(?:is|:|\-)\s*([a-z0-9_.]+)/i,
        /insta\s*(?:is|:|\-)\s*([a-z0-9_.]+)/i
    ];
    
    // Check for "facebook & instagram is [name]" pattern first
    const combinedPattern = /(?:facebook|fb)\s*(?:&|and)\s*(?:instagram|ig|insta)\s*(?:is|:|\-)\s*([a-z0-9_.]+)/i;
    const combinedMatch = otherContactsText.match(combinedPattern);
    
    if (combinedMatch) {
        const username = combinedMatch[1];
        result.facebook = `https://facebook.com/${username}`;
        result.instagram = `https://instagram.com/${username}`;
    } else {
        // Check individual patterns
        for (const pattern of fbPatterns) {
            const match = otherContactsText.match(pattern);
            if (match) {
                result.facebook = `https://facebook.com/${match[1]}`;
                break;
            }
        }
        
        for (const pattern of igPatterns) {
            const match = otherContactsText.match(pattern);
            if (match) {
                result.instagram = `https://instagram.com/${match[1]}`;
                break;
            }
        }
    }
    
    // Twitter/X patterns
    const twitterPatterns = [
        /(?:twitter|x)\s*(?:is|:|\-)\s*@?([a-z0-9_]+)/i
    ];
    
    for (const pattern of twitterPatterns) {
        const match = otherContactsText.match(pattern);
        if (match) {
            result.twitter = `https://twitter.com/${match[1]}`;
            break;
        }
    }
    
    // LinkedIn patterns
    const linkedinPatterns = [
        /linkedin\s*(?:is|:|\-)\s*([a-z0-9\-]+)/i,
        /linkedin\.com\/in\/([a-z0-9\-]+)/i
    ];
    
    for (const pattern of linkedinPatterns) {
        const match = otherContactsText.match(pattern);
        if (match) {
            result.linkedin = `https://linkedin.com/in/${match[1]}`;
            break;
        }
    }
    
    // Determine if we should clear the field
    // Clear if we extracted meaningful data OR if it only contains duplicated phone
    if (result.phone || result.facebook || result.instagram || result.twitter || result.linkedin) {
        result.shouldClear = true;
    } else if (existingPhone && text.includes(existingPhone.toLowerCase().replace(/\s/g, ''))) {
        result.shouldClear = true; // It's just a duplicate phone number
    }
    
    return result;
}

async function cleanOtherContactsColumn() {
    try {
        console.log('🔍 Reading Victorian Therapists database...\n');
        
        // Get all entries
        let allResults = [];
        let hasMore = true;
        let startCursor = undefined;
        
        while (hasMore) {
            const response = await notion.databases.query({
                database_id: THERAPISTS_DB_ID,
                start_cursor: startCursor,
                page_size: 100
            });
            
            allResults = allResults.concat(response.results);
            hasMore = response.has_more;
            startCursor = response.next_cursor;
        }
        
        console.log(`✅ Found ${allResults.length} total entries\n`);
        console.log('═'.repeat(80));
        
        let processedCount = 0;
        let updatedCount = 0;
        let skippedCount = 0;
        
        for (const page of allResults) {
            processedCount++;
            const props = page.properties;
            
            // Get current values
            const name = extractPropertyValue(props['Fullname'] || props['Name']);
            const otherContacts = extractPropertyValue(props['Other contacts (specify if it is mobile, phone, facebook page, business name, etc)']);
            const existingPhone = extractPropertyValue(props['Phone']);
            const existingFacebook = extractPropertyValue(props['Facebook']);
            const existingInstagram = extractPropertyValue(props['Instagram']);
            
            if (!otherContacts || otherContacts.trim() === '') {
                skippedCount++;
                continue;
            }
            
            // Parse the other contacts field
            const parsed = parseOtherContacts(otherContacts, existingPhone);
            
            // Build update payload
            const updates = {};
            let hasUpdates = false;
            
            // Only update if we found new data
            if (parsed.phone && !existingPhone) {
                updates['Phone'] = { phone_number: parsed.phone };
                hasUpdates = true;
            }
            
            if (parsed.facebook && !existingFacebook) {
                updates['Facebook'] = { url: parsed.facebook };
                hasUpdates = true;
            }
            
            if (parsed.instagram && !existingInstagram) {
                updates['Instagram'] = { url: parsed.instagram };
                hasUpdates = true;
            }
            
            if (parsed.twitter && (!props['Twitter/X'] || !extractPropertyValue(props['Twitter/X']))) {
                updates['Twitter/X'] = { url: parsed.twitter };
                hasUpdates = true;
            }
            
            if (parsed.linkedin && (!props['LinkedIn'] || !extractPropertyValue(props['LinkedIn']))) {
                updates['LinkedIn'] = { url: parsed.linkedin };
                hasUpdates = true;
            }
            
            // Clear the "Other Contacts" field if we extracted data or it's a duplicate
            if (parsed.shouldClear) {
                updates['Other contacts (specify if it is mobile, phone, facebook page, business name, etc)'] = { rich_text: [] };
                hasUpdates = true;
            }
            
            if (hasUpdates) {
                console.log(`\n📝 Updating: ${name || 'Unknown'}`);
                console.log(`   Original: "${otherContacts}"`);
                
                if (parsed.phone) console.log(`   ✓ Phone: ${parsed.phone}`);
                if (parsed.facebook) console.log(`   ✓ Facebook: ${parsed.facebook}`);
                if (parsed.instagram) console.log(`   ✓ Instagram: ${parsed.instagram}`);
                if (parsed.twitter) console.log(`   ✓ Twitter: ${parsed.twitter}`);
                if (parsed.linkedin) console.log(`   ✓ LinkedIn: ${parsed.linkedin}`);
                if (parsed.shouldClear) console.log(`   ✓ Clearing "Other Contacts" field`);
                
                // Update the page
                await notion.pages.update({
                    page_id: page.id,
                    properties: updates
                });
                
                updatedCount++;
                console.log(`   ✅ Updated successfully`);
                
                // Rate limiting - be nice to Notion API
                await new Promise(resolve => setTimeout(resolve, 334)); // ~3 requests/second
            } else {
                console.log(`\n⏭️  Skipping: ${name || 'Unknown'} - no updates needed`);
                console.log(`   Content: "${otherContacts}"`);
                skippedCount++;
            }
        }
        
        console.log('\n' + '═'.repeat(80));
        console.log('\n📊 Summary:');
        console.log(`   Total entries processed: ${processedCount}`);
        console.log(`   Entries updated: ${updatedCount}`);
        console.log(`   Entries skipped: ${skippedCount}`);
        console.log('\n✅ Done!');
        
    } catch (error) {
        console.error('\n❌ Error:', error.message);
        if (error.body) {
            console.error('Details:', JSON.stringify(error.body, null, 2));
        }
        throw error;
    }
}

// Run the script
console.log('═'.repeat(80));
console.log('  Clean "Other Contacts" Column');
console.log('  Parse phone numbers and social media into proper columns');
console.log('═'.repeat(80));
console.log();

cleanOtherContactsColumn()
    .then(() => {
        console.log('\n🎉 All done!');
    })
    .catch((error) => {
        console.error('\n❌ Failed:', error.message);
        process.exit(1);
    });

