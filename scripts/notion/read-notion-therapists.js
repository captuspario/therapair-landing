import { Client } from '@notionhq/client';
import * as dotenv from 'dotenv';

// Load environment variables
dotenv.config();

const NOTION_TOKEN = process.env.NOTION_TOKEN || process.env.NOTION_API_KEY;
const THERAPISTS_DB_ID = process.env.THERAPISTS_DATABASE_ID; // You'll need to add this

if (!NOTION_TOKEN) {
    console.error('❌ Error: NOTION_TOKEN not found in environment variables');
    console.log('Please set NOTION_TOKEN in your .env file or environment');
    process.exit(1);
}

if (!THERAPISTS_DB_ID) {
    console.error('❌ Error: THERAPISTS_DATABASE_ID not found');
    console.log('Please add THERAPISTS_DATABASE_ID to your .env file');
    console.log('\nTo find your database ID:');
    console.log('1. Open your Notion database');
    console.log('2. Copy the URL (looks like: notion.so/workspace/DATABASE_ID?v=...)');
    console.log('3. The DATABASE_ID is the 32-character string before the ?');
    process.exit(1);
}

// Initialize Notion client
const notion = new Client({ auth: NOTION_TOKEN });

async function readTherapistsDatabase() {
    try {
        console.log('🔍 Reading Victorian Therapists database...\n');
        
        // Get database structure first
        const database = await notion.databases.retrieve({
            database_id: THERAPISTS_DB_ID
        });
        
        console.log('📊 Database Name:', database.title?.[0]?.plain_text || 'Unknown');
        console.log('🆔 Database ID:', database.id);
        console.log('\n📋 Properties (Columns):');
        console.log('─'.repeat(60));
        
        const properties = database.properties || {};
        const propertyKeys = Object.keys(properties);
        
        if (propertyKeys.length === 0) {
            console.log('⚠️  No properties found in database');
        } else {
            propertyKeys.forEach((key, index) => {
                const prop = properties[key];
                console.log(`${index + 1}. ${key} (${prop?.type || 'unknown'})`);
            });
        }
        
        console.log('\n📄 Fetching therapist entries...\n');
        
        // Query the database
        const response = await notion.databases.query({
            database_id: THERAPISTS_DB_ID,
            page_size: 10 // Start with first 10 entries
        });
        
        console.log(`✅ Found ${response.results.length} entries (showing first 10)`);
        console.log('─'.repeat(60));
        
        // Display each therapist
        response.results.forEach((page, index) => {
            console.log(`\n${index + 1}. Therapist Entry:`);
            
            // Extract properties
            const props = page.properties;
            
            // Common fields to display
            const displayFields = [
                'Name', 'Full Name', 'Fullname', 
                'Email', 'Email Address',
                'Profession', 'Professional Title',
                'Suburb', 'Suburbs', 'Suburb/s of practice',
                'Area', 'Gender', 'Business Name'
            ];
            
            displayFields.forEach(fieldName => {
                if (props[fieldName]) {
                    const value = extractPropertyValue(props[fieldName]);
                    if (value) {
                        console.log(`   ${fieldName}: ${value}`);
                    }
                }
            });
        });
        
        console.log('\n' + '─'.repeat(60));
        console.log(`\n📊 Total entries in database: ${response.results.length}`);
        
        if (response.has_more) {
            console.log('⚠️  Note: There are more entries. This shows first 10 only.');
        }
        
        return {
            database,
            entries: response.results,
            hasMore: response.has_more
        };
        
    } catch (error) {
        console.error('\n❌ Error reading database:', error.message);
        
        if (error.code === 'object_not_found') {
            console.log('\n🔧 Troubleshooting:');
            console.log('1. Make sure the database is shared with your integration');
            console.log('2. Check the database ID is correct');
            console.log('3. Verify the integration has access to the workspace');
        } else if (error.code === 'unauthorized') {
            console.log('\n🔧 The integration token is invalid or expired');
            console.log('Create a new integration at: https://www.notion.so/my-integrations');
        }
        
        throw error;
    }
}

function extractPropertyValue(property) {
    try {
        switch (property.type) {
            case 'title':
                return property.title[0]?.plain_text || '';
            case 'rich_text':
                return property.rich_text[0]?.plain_text || '';
            case 'email':
                return property.email || '';
            case 'phone_number':
                return property.phone_number || '';
            case 'select':
                return property.select?.name || '';
            case 'multi_select':
                return property.multi_select.map(s => s.name).join(', ') || '';
            case 'date':
                return property.date?.start || '';
            case 'checkbox':
                return property.checkbox ? '✓' : '✗';
            case 'url':
                return property.url || '';
            case 'number':
                return property.number || '';
            default:
                return '';
        }
    } catch (error) {
        return '';
    }
}

// Run the script
console.log('═'.repeat(60));
console.log('  Victorian Therapists Database Reader');
console.log('═'.repeat(60));
console.log();

readTherapistsDatabase()
    .then(() => {
        console.log('\n✅ Done!');
    })
    .catch((error) => {
        console.error('\n❌ Failed:', error.message);
        process.exit(1);
    });

