import { Client } from '@notionhq/client';
import * as dotenv from 'dotenv';

dotenv.config();

const NOTION_TOKEN = process.env.NOTION_TOKEN;
const THERAPISTS_DB_ID = process.env.THERAPISTS_DATABASE_ID;

const notion = new Client({ auth: NOTION_TOKEN });

// ============================================
// HELPER FUNCTIONS
// ============================================

function parseFullName(fullName) {
    if (!fullName) return { firstName: '', lastName: '' };
    
    const parts = fullName.trim().split(/\s+/);
    
    if (parts.length === 1) {
        return { firstName: parts[0], lastName: '' };
    } else if (parts.length === 2) {
        return { firstName: parts[0], lastName: parts[1] };
    } else if (parts.length === 3) {
        // First two are first/middle name, last is surname
        return { 
            firstName: parts.slice(0, 2).join(' '), 
            lastName: parts[2] 
        };
    } else {
        // More than 3: first two are first names, rest are last name
        return { 
            firstName: parts.slice(0, 2).join(' '), 
            lastName: parts.slice(2).join(' ') 
        };
    }
}

function extractPhoneFromContact(contactText) {
    if (!contactText) return '';
    
    // Match Australian phone numbers
    const phoneRegex = /(\+?61\s?)?(\(0\d\))?\s?\d{3,4}\s?\d{3}\s?\d{3}|\d{4}\s?\d{3}\s?\d{3}/;
    const match = contactText.match(phoneRegex);
    return match ? match[0].trim() : contactText.trim();
}

function extractSocialMedia(contactText) {
    if (!contactText) return '';
    
    // Extract @handles or social media info
    const socialRegex = /@[\w.]+|instagram|facebook|linkedin/i;
    if (socialRegex.test(contactText)) {
        return contactText.trim();
    }
    return '';
}

function standardizeSuburbs(suburbText) {
    if (!suburbText) return [];
    
    const suburbs = suburbText.split(/,|and/).map(s => s.trim()).filter(Boolean);
    
    // Standardize common variations
    return suburbs.map(suburb => {
        // Convert "ONLINE" variations
        if (/online/i.test(suburb)) return 'Online';
        if (/phone/i.test(suburb)) return 'Phone Only';
        
        // Capitalize properly
        return suburb
            .split(' ')
            .map(word => word.charAt(0).toUpperCase() + word.slice(1).toLowerCase())
            .join(' ');
    });
}

function extractRegion(areaText) {
    if (!areaText) return '';
    
    const area = areaText.trim();
    
    // Map to standard regions
    const regionMap = {
        'cbd': 'Melbourne CBD',
        'melbourne cbd': 'Melbourne CBD',
        'inner north': 'Inner North',
        'north': 'North',
        'inner south': 'Inner South',
        'south': 'South',
        'east': 'East',
        'west': 'West',
        'online': 'Online',
        'online/phone': 'Online',
        'online only': 'Online',
        'phone only': 'Online'
    };
    
    const normalized = area.toLowerCase();
    return regionMap[normalized] || area;
}

function generateProfileURL(fullName) {
    if (!fullName) return '';
    
    return fullName
        .toLowerCase()
        .replace(/[^a-z0-9\s-]/g, '')
        .replace(/\s+/g, '-')
        .replace(/-+/g, '-')
        .trim();
}

function generateSecureToken() {
    const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    let token = 'therapist_';
    for (let i = 0; i < 32; i++) {
        token += chars.charAt(Math.floor(Math.random() * chars.length));
    }
    return token;
}

function getTokenExpiry() {
    const date = new Date();
    date.setDate(date.getDate() + 30); // 30 days from now
    return date.toISOString().split('T')[0]; // YYYY-MM-DD format
}

// ============================================
// MAIN CLEANUP FUNCTION
// ============================================

async function cleanupDatabase() {
    console.log('🧹 Starting database cleanup...\n');
    
    let allPages = [];
    let hasMore = true;
    let startCursor = undefined;
    
    // Fetch ALL pages
    while (hasMore) {
        const response = await notion.databases.query({
            database_id: THERAPISTS_DB_ID,
            start_cursor: startCursor,
            page_size: 100
        });
        
        allPages = allPages.concat(response.results);
        hasMore = response.has_more;
        startCursor = response.next_cursor;
        
        console.log(`📄 Fetched ${allPages.length} entries...`);
    }
    
    console.log(`\n✅ Total therapists to process: ${allPages.length}\n`);
    console.log('─'.repeat(60));
    
    const cleanupReport = {
        total: allPages.length,
        processed: 0,
        errors: [],
        changes: []
    };
    
    for (let i = 0; i < allPages.length; i++) {
        const page = allPages[i];
        const props = page.properties;
        
        try {
            console.log(`\n${i + 1}. Processing: ${extractPropertyValue(props.Name) || 'Unknown'}`);
            
            // Extract current data
            const fullName = extractPropertyValue(props.Name) || '';
            const firstName = extractPropertyValue(props['First Name']) || '';
            const surname = extractPropertyValue(props.Surname) || '';
            const contactDetails = extractPropertyValue(props['Other contact details, social media, etc.']) || '';
            const suburb = extractPropertyValue(props['Suburb/s of practice (if online only, write ONLINE)']) || '';
            const area = extractPropertyValue(props['Area predominantly work in']) || '';
            
            // Parse names
            const { firstName: parsedFirst, lastName: parsedLast } = parseFullName(fullName);
            const finalFirstName = firstName || parsedFirst;
            const finalLastName = surname || parsedLast;
            
            // Extract phone and social
            const phone = extractPhoneFromContact(contactDetails);
            const social = extractSocialMedia(contactDetails);
            
            // Standardize locations
            const suburbs = standardizeSuburbs(suburb);
            const region = extractRegion(area);
            
            // Generate system fields
            const profileURL = generateProfileURL(fullName);
            const onboardingToken = generateSecureToken();
            const tokenExpiry = getTokenExpiry();
            
            console.log(`   ✓ Name: ${finalFirstName} ${finalLastName}`);
            console.log(`   ✓ Profile URL: /therapist/${profileURL}`);
            console.log(`   ✓ Region: ${region}`);
            console.log(`   ✓ Suburbs: ${suburbs.join(', ')}`);
            
            // Prepare updates object
            const updates = {
                'First Name': { rich_text: [{ text: { content: finalFirstName } }] },
                'Last Name': { rich_text: [{ text: { content: finalLastName } }] }
            };
            
            // Add optional fields if they have data
            if (phone) {
                updates['Phone'] = { rich_text: [{ text: { content: phone } }] };
            }
            if (social) {
                updates['Social Media'] = { rich_text: [{ text: { content: social } }] };
            }
            if (profileURL) {
                updates['Profile URL'] = { rich_text: [{ text: { content: `/therapist/${profileURL}` } }] };
            }
            if (onboardingToken) {
                updates['Onboarding Token'] = { rich_text: [{ text: { content: onboardingToken } }] };
            }
            if (tokenExpiry) {
                updates['Token Expiry'] = { date: { start: tokenExpiry } };
            }
            if (region) {
                updates['Region'] = { select: { name: region } };
            }
            
            // Add system fields
            updates['Status'] = { select: { name: 'Pending Review' } };
            updates['Published'] = { checkbox: false };
            updates['Import Date'] = { date: { start: new Date().toISOString().split('T')[0] } };
            
            // Update the page
            await notion.pages.update({
                page_id: page.id,
                properties: updates
            });
            
            cleanupReport.processed++;
            cleanupReport.changes.push({
                name: fullName,
                firstName: finalFirstName,
                lastName: finalLastName,
                profileURL: profileURL,
                region: region,
                suburbs: suburbs
            });
            
            console.log(`   ✅ Updated successfully`);
            
            // Rate limiting
            await new Promise(resolve => setTimeout(resolve, 100));
            
        } catch (error) {
            console.log(`   ❌ Error: ${error.message}`);
            cleanupReport.errors.push({
                name: extractPropertyValue(props.Name),
                error: error.message
            });
        }
    }
    
    return cleanupReport;
}

function extractPropertyValue(property) {
    if (!property) return '';
    
    try {
        switch (property.type) {
            case 'title':
                return property.title[0]?.plain_text || '';
            case 'rich_text':
                return property.rich_text[0]?.plain_text || '';
            case 'email':
                return property.email || '';
            case 'select':
                return property.select?.name || '';
            case 'multi_select':
                return property.multi_select.map(s => s.name).join(', ');
            default:
                return '';
        }
    } catch {
        return '';
    }
}

// ============================================
// ADD NEW PROPERTIES TO DATABASE
// ============================================

async function addNewProperties() {
    console.log('\n📋 Adding new properties to database...\n');
    
    const newProperties = {
        'Last Name': {
            rich_text: {}
        },
        'Phone': {
            rich_text: {}
        },
        'Social Media': {
            rich_text: {}
        },
        'Region': {
            select: {
                options: [
                    { name: 'Melbourne CBD', color: 'blue' },
                    { name: 'Inner North', color: 'green' },
                    { name: 'North', color: 'yellow' },
                    { name: 'Inner South', color: 'orange' },
                    { name: 'South', color: 'red' },
                    { name: 'East', color: 'purple' },
                    { name: 'West', color: 'pink' },
                    { name: 'Online', color: 'gray' }
                ]
            }
        },
        'Status': {
            select: {
                options: [
                    { name: 'Pending Review', color: 'yellow' },
                    { name: 'Verified', color: 'green' },
                    { name: 'Published', color: 'blue' },
                    { name: 'Archived', color: 'gray' }
                ]
            }
        },
        'Published': {
            checkbox: {}
        },
        'Profile URL': {
            rich_text: {}
        },
        'Onboarding Token': {
            rich_text: {}
        },
        'Token Expiry': {
            date: {}
        },
        'Last Contacted': {
            date: {}
        },
        'Admin Notes': {
            rich_text: {}
        },
        'Import Date': {
            date: {}
        }
    };
    
    try {
        await notion.databases.update({
            database_id: THERAPISTS_DB_ID,
            properties: newProperties
        });
        
        console.log('✅ New properties added successfully!\n');
        console.log('Added:');
        Object.keys(newProperties).forEach(prop => {
            console.log(`   ✓ ${prop}`);
        });
        
        return true;
    } catch (error) {
        console.error('❌ Error adding properties:', error.message);
        console.log('\nNote: Some properties may already exist, which is fine.');
        console.log('Continuing with cleanup...\n');
        return false;
    }
}

// ============================================
// GENERATE REPORT
// ============================================

function generateReport(report) {
    console.log('\n\n' + '═'.repeat(60));
    console.log('  CLEANUP REPORT');
    console.log('═'.repeat(60));
    
    console.log(`\n📊 Summary:`);
    console.log(`   Total therapists: ${report.total}`);
    console.log(`   Successfully processed: ${report.processed}`);
    console.log(`   Errors: ${report.errors.length}`);
    
    if (report.errors.length > 0) {
        console.log(`\n❌ Errors:`);
        report.errors.forEach((error, i) => {
            console.log(`   ${i + 1}. ${error.name}: ${error.error}`);
        });
    }
    
    console.log(`\n✅ Sample Changes (first 5):`);
    report.changes.slice(0, 5).forEach((change, i) => {
        console.log(`\n   ${i + 1}. ${change.name}`);
        console.log(`      First: ${change.firstName} | Last: ${change.lastName}`);
        console.log(`      URL: /therapist/${change.profileURL}`);
        console.log(`      Region: ${change.region}`);
        console.log(`      Suburbs: ${change.suburbs.join(', ')}`);
    });
    
    if (report.changes.length > 5) {
        console.log(`\n   ... and ${report.changes.length - 5} more`);
    }
    
    console.log('\n' + '═'.repeat(60));
    console.log('✅ Database cleanup complete!');
    console.log('═'.repeat(60));
}

// ============================================
// RUN SCRIPT
// ============================================

console.log('═'.repeat(60));
console.log('  Victorian Therapists Database Cleanup');
console.log('═'.repeat(60));
console.log();

(async () => {
    try {
        // Step 1: Add new properties
        await addNewProperties();
        
        // Wait a bit for Notion to process
        await new Promise(resolve => setTimeout(resolve, 2000));
        
        // Step 2: Clean up all entries
        const report = await cleanupDatabase();
        
        // Step 3: Generate report
        generateReport(report);
        
        console.log('\n🎉 All done! Your database is now clean and organized.');
        console.log('\nNext steps:');
        console.log('1. Review the changes in Notion');
        console.log('2. Manually reorder columns in Notion UI');
        console.log('3. Ready to send onboarding invitations!');
        
    } catch (error) {
        console.error('\n❌ Fatal error:', error);
        process.exit(1);
    }
})();

