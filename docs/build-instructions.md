# Therapair – Landing Page Build Instructions

## Overview
This document outlines the steps to create, export, and refine the Therapair landing page using **Figma Make**, **VS Code**, and **Claude**.  
The goal is to produce a fully responsive, accessible, and brand‑consistent page that explains the product, builds credibility, and collects interest via Mailchimp.

---

## 1️⃣ Design Phase – Figma Make

### Step 1: Generate Layout
1. Open **Figma Make** (Diagram plugin).
2. Paste the provided **Therapair Landing Page prompt**.
3. Confirm structure:
   - Hero section with sign‑up CTA  
   - How It Works (3 steps)  
   - Who It’s For grid  
   - Purpose/Story section  
   - Therapist profile preview grid  
   - Interest/Signup + Footer  

### Step 2: Apply Design System
- Use the `DESIGN‑SYSTEM.md` palette and typography.
- Create reusable components:
  - Buttons (`Primary`, `Secondary`)
  - Cards (`TherapistCard`, `AudienceCard`)
  - Form elements (`Input`, `Textarea`)
  - Section layout (`Container`, `Grid`)

### Step 3: Prototype & Export
- Link CTA buttons to Mailchimp placeholder URLs.
- Check responsive behaviour (Desktop 1440px / Tablet 768px / Mobile 375px).
- Export design specs via **Figma → Export Code** or **Anima / Figma to HTML** plugin.

---

## 2️⃣ Development Phase – VS Code

### Step 1: Import Design
- Open a clean folder in VS Code.
- Paste exported HTML + CSS (or Next.js page).
- Create `/assets/therapists/` for profile images.

### Step 2: Structure