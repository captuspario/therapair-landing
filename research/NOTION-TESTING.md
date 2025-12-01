# Notion Research Smoke Testing

This guide explains how to verify that survey submissions reach the Therapist Research database in Notion.

---

## 1. Prerequisites

1. **Share the database with the integration**  
   In Notion, open the therapist research database → `Share` → invite the integration that owns your `NOTION_TOKEN`.

2. **Create `config.php`**  
   Copy `products/landing-page/config.sample.php` to `config.php` (same directory) and populate:
   - `NOTION_TOKEN` – Internal integration token (`secret_...`).
   - `THERAPIST_RESEARCH_DATABASE_ID` – 32-character ID from the research database URL.
   - `RESEARCH_TOKEN_SECRET` – Long random string that matches the system minting invite links.
   - Optional: directory database IDs if you want submissions to upsert therapist pages.

3. **Deploy updated files**  
   Push `config.php`, the latest `/research` assets, and `/api/research` PHP endpoints to Hostinger. Clear the cache or bump the stylesheet query string if the UI looks stale.

---

## 2. Environment variables (optional)

You can store defaults for the smoke test script in `.env.research.local`, `.env.local`, or `.env` inside `products/landing-page/`:

```
RESEARCH_TEST_BASE_URL=https://therapair.com.au
RESEARCH_TEST_TOKEN=preview
RESEARCH_TEST_EMAIL=preview+smoke@therapair.com.au
```

These values can still be overridden via CLI flags.

---

## 3. Run the automated smoke test

From `products/landing-page/`:

```
npm run research:smoke
```

### Supported flags

- `--base https://staging.therapair.com.au` – Override base URL.
- `--token eyJhbGci...` – Use a specific invite token.
- `--email qa@example.com` – Force a specific email address.
- `--dry-run` – Print payload without submitting (useful for review).

### What the script does

1. Calls `api/research/session.php` to make sure the token is valid.
2. Builds a complete survey payload (all required fields populated).
3. POSTs the payload to `api/research/response.php`.
4. Prints HTTP statuses and JSON responses for both calls.
5. On success, prints the Notion record ID returned by the API.

---

## 4. Manual verification in Notion

1. Open the research database URL you shared earlier.
2. Filter by the `Respondent ID` or `Consent Timestamp` shown in the smoke test output.
3. Confirm that:
   - All multi-select and select fields are populated.
   - Consent properties contain the correct version and timestamp.
   - Email and metadata fields display the smoke test values.
4. (Optional) open the therapist directory database and confirm the linked page was updated if a `directory_page_id` was present in the token.

---

## 5. Troubleshooting

| Symptom | Likely Cause | Fix |
| --- | --- | --- |
| `Session lookup failed` | Invalid token or missing `RESEARCH_TOKEN_SECRET` | Re-issue token or set the secret in `config.php`/environment. |
| `Research database ID not configured` | Forgot to set `THERAPIST_RESEARCH_DATABASE_ID` | Add the value to `config.php` and redeploy. |
| `Notion create failed` | Integration lacks database access or ID is wrong | Re-share database with integration or copy the correct ID. |
| `Please choose at least one option...` | Script fields edited incorrectly | Ensure required arrays (client types, modalities, etc.) have at least one value. |
| `Saving to research database failed` | Temporary Notion outage | Retry the smoke test; inspect server logs for full error text. |

---

## 6. Next steps

- Once the smoke test passes, send yourself a real invite link and complete the survey end-to-end to confirm production data flows.  
- Add the smoke test to your deployment checklist so every release validates the Notion pipeline before emailing therapists.
