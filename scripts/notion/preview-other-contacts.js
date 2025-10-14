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

async function previewOtherContactsChanges() {
    try {
        console.log('🔍 Reading Victorian Therapists database...\n');
        
        // Get all entries using the correct API
        let allResults = [];
        let hasMore = true;
        let startCursor = undefined;
        
        while (hasMore) {
            const queryParams = {
                database_id: THERAPISTS_DB_ID,
                page_size: 100
            };
            
            if (startCursor) {
                queryParams.start_cursor = startCursor;
            }
            
            const response = await notion.request({
                path: `databases/${THERAPISTS_DB_ID}/query`,
                method: 'POST',
                body: queryParams
            });
            
            allResults = allResults.concat(response.results);
            hasMore = response.has_more;
            startCursor = response.next_cursor;
        }
        
        console.log(`✅ Found ${allResults.length} total entries\n`);
        console.log('═'.repeat(80));
        
        let entriesWithOtherContacts = 0;
        let entriesWillBeUpdated = 0;
        let entriesWillBeSkipped = 0;
        
        for (const page of allResults) {
            const props = page.properties;
            
            // Get current values
            const name = extractPropertyValue(props['Fullname'] || props['Name']);
            const otherContacts = extractPropertyValue(props['Other contacts (specify if it is mobile, phone, facebook page, business name, etc)']);
            const existingPhone = extractPropertyValue(props['Phone']);
            const existingFacebook = extractPropertyValue(props['Facebook']);
            const existingInstagram = extractPropertyValue(props['Instagram']);
            const existingTwitter = extractPropertyValue(props['Twitter/X']);
            const existingLinkedIn = extractPropertyValue(props['LinkedIn']);
            
            if (!otherContacts || otherContacts.trim() === '') {
                continue;
            }
            
            entriesWithOtherContacts++;
            
            // Parse the other contacts field
            const parsed = parseOtherContacts(otherContacts, existingPhone);
            
            // Check if we'll make updates
            let willUpdate = false;
            const changes = [];
            
            if (parsed.phone && !existingPhone) {
                changes.push(`   📱 Phone: "${parsed.phone}"`);
                willUpdate = true;
            }
            
            if (parsed.facebook && !existingFacebook) {
                changes.push(`   👥 Facebook: "${parsed.facebook}"`);
                willUpdate = true;
            }
            
            if (parsed.instagram && !existingInstagram) {
                changes.push(`   📸 Instagram: "${parsed.instagram}"`);
                willUpdate = true;
            }
            
            if (parsed.twitter && !existingTwitter) {
                changes.push(`   🐦 Twitter/X: "${parsed.twitter}"`);
                willUpdate = true;
            }
            
            if (parsed.linkedin && !existingLinkedIn) {
                changes.push(`   💼 LinkedIn: "${parsed.linkedin}"`);
                willUpdate = true;
            }
            
            if (parsed.shouldClear) {
                changes.push(`   🧹 Will clear "Other Contacts" field`);
                willUpdate = true;
            }
            
            if (willUpdate) {
                console.log(`\n📝 ${name || 'Unknown'}`);
                console.log(`   Current "Other Contacts": "${otherContacts}"`);
                console.log(`\n   📊 Existing data:`);
                if (existingPhone) console.log(`      Phone: ${existingPhone}`);
                if (existingFacebook) console.log(`      Facebook: ${existingFacebook}`);
                if (existingInstagram) console.log(`      Instagram: ${existingInstagram}`);
                if (existingTwitter) console.log(`      Twitter: ${existingTwitter}`);
                if (existingLinkedIn) console.log(`      LinkedIn: ${existingLinkedIn}`);
                if (!existingPhone && !existingFacebook && !existingInstagram && !existingTwitter && !existingLinkedIn) {
                    console.log(`      (No existing social media data)`);
                }
                console.log(`\n   ✨ Proposed changes:`);
                changes.forEach(change => console.log(change));
                console.log(`   ${'─'.repeat(76)}`);
                
                entriesWillBeUpdated++;
            } else {
                console.log(`\n⏭️  ${name || 'Unknown'}`);
                console.log(`   "Other Contacts": "${otherContacts}"`);
                console.log(`   ℹ️  No changes needed (data already exists in proper columns or can't parse)`);
                console.log(`   ${'─'.repeat(76)}`);
                
                entriesWillBeSkipped++;
            }
        }
        
        console.log('\n' + '═'.repeat(80));
        console.log('\n📊 Preview Summary:');
        console.log(`   Total entries in database: ${allResults.length}`);
        console.log(`   Entries with "Other Contacts" data: ${entriesWithOtherContacts}`);
        console.log(`   Entries that will be updated: ${entriesWillBeUpdated}`);
        console.log(`   Entries that will be skipped: ${entriesWillBeSkipped}`);
        console.log('\n💡 To apply these changes, run:');
        console.log('   node scripts/notion/clean-other-contacts.js');
        console.log('\n✅ Preview complete!');
        
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
console.log('  Preview "Other Contacts" Cleanup');
console.log('  Shows what will be changed WITHOUT making any updates');
console.log('═'.repeat(80));
console.log();

previewOtherContactsChanges()
    .then(() => {
        console.log('\n🎉 Preview complete!');
    })
    .catch((error) => {
        console.error('\n❌ Failed:', error.message);
        process.exit(1);
    });

