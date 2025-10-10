# Therapair Design System

**Version**: 1.0  
**Last Updated**: 2025-10-10  
**Applies To**: All Therapair Projects

---

## 📐 Design Principles

1. **Inclusive by Design**: Built specifically for diverse communities
2. **Human-Centred**: Warm, approachable, never cold or clinical
3. **Accessible**: WCAG 2.1 AA minimum, keyboard navigation
4. **Mobile-First**: Most users on mobile devices
5. **Performance**: Fast loading, minimal dependencies
6. **Consistent**: Same look and feel across all touchpoints

---

## 🎨 Color System

### Primary Palette

```css
/* Therapair Purple - Main Brand Color */
--therapair-purple-base: #9B74B7;
--therapair-purple-dark: #4F064F;
--therapair-purple-light: #D4B5D8;
--therapair-purple-bg: #F8F4FF;

/* Usage */
.primary-button { background: var(--therapair-purple-base); }
.primary-button:hover { background: var(--therapair-purple-dark); }
.accent-bg { background: var(--therapair-purple-light); }
.subtle-bg { background: var(--therapair-purple-bg); }
```

### Secondary Colors

```css
/* Blue - Professional, Trustworthy */
--color-blue-600: #2563eb;
--color-blue-500: #3b82f6;

/* Purple-Blue - Gradient accent */
--color-purple: #8b5cf6;

/* Green - Success, Affirming */
--color-green-600: #10b981;
--color-green-700: #059669;
```

### Neutral Palette

```css
/* Text Colors */
--color-gray-900: #1e293b;  /* Primary text */
--color-gray-700: #374151;  /* Secondary text */
--color-gray-600: #64748b;  /* Tertiary text */
--color-gray-500: #9ca3af;  /* Disabled text */

/* Backgrounds */
--color-gray-50: #f8fafc;   /* Page background */
--color-gray-100: #f1f5f9;  /* Card background */
--color-gray-200: #e2e8f0;  /* Border */
--color-gray-300: #cbd5e1;  /* Border hover */
```

### Semantic Colors

```css
/* States */
--color-error: #ef4444;     /* Errors, destructive */
--color-warning: #f59e0b;   /* Warnings */
--color-info: #3b82f6;      /* Information */
--color-success: #10b981;   /* Success states */
```

### Gradients

```css
/* Header gradients */
.gradient-primary {
  background: linear-gradient(135deg, #9B74B7, #4F064F);
}

.gradient-subtle {
  background: linear-gradient(135deg, 
    rgba(139, 92, 246, 0.15), 
    rgba(16, 185, 129, 0.15)
  );
}
```

---

## ✍️ Typography

### Font Families

```css
/* Primary Font: Open Sans */
@import url('https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;500;600;700&display=swap');

--font-primary: 'Open Sans', ui-sans-serif, system-ui, sans-serif;

/* System Font Stack (fallback/performance) */
--font-system: ui-sans-serif, system-ui, -apple-system, 
               BlinkMacSystemFont, 'Segoe UI', sans-serif;
```

### Font Sizes

```css
/* Headings */
--text-5xl: 3rem;      /* 48px - Hero headlines */
--text-4xl: 2.5rem;    /* 40px - Page titles */
--text-3xl: 2rem;      /* 32px - Section headers */
--text-2xl: 1.5rem;    /* 24px - Card headers */
--text-xl: 1.25rem;    /* 20px - Subheadings */
--text-lg: 1.125rem;   /* 18px - Large body */

/* Body */
--text-base: 1rem;     /* 16px - Default */
--text-sm: 0.875rem;   /* 14px - Small text */
--text-xs: 0.75rem;    /* 12px - Captions */
```

### Font Weights

```css
--font-normal: 400;
--font-medium: 500;
--font-semibold: 600;
--font-bold: 700;
```

### Line Heights

```css
--leading-tight: 1.25;   /* Headings */
--leading-snug: 1.375;   /* Tight paragraphs */
--leading-normal: 1.5;   /* Default body */
--leading-relaxed: 1.625; /* Comfortable reading */
--leading-loose: 2;      /* Spacious */
```

### Type Scale Examples

```html
<h1 class="text-4xl font-bold leading-tight">
  Main Page Heading
</h1>

<h2 class="text-3xl font-semibold leading-tight">
  Section Heading
</h2>

<p class="text-base leading-relaxed text-gray-700">
  Body paragraph text with comfortable reading line-height.
</p>

<p class="text-sm text-gray-600">
  Secondary text or captions
</p>
```

---

## 📏 Spacing System

### Scale (Base unit: 4px)

```css
--space-0: 0;
--space-1: 0.25rem;  /* 4px */
--space-2: 0.5rem;   /* 8px */
--space-3: 0.75rem;  /* 12px */
--space-4: 1rem;     /* 16px */
--space-5: 1.25rem;  /* 20px */
--space-6: 1.5rem;   /* 24px */
--space-8: 2rem;     /* 32px */
--space-10: 2.5rem;  /* 40px */
--space-12: 3rem;    /* 48px */
--space-16: 4rem;    /* 64px */
--space-20: 5rem;    /* 80px */
--space-24: 6rem;    /* 96px */
```

### Spacing Usage

```css
/* Component padding */
.card-padding-sm { padding: var(--space-4); }
.card-padding-md { padding: var(--space-6); }
.card-padding-lg { padding: var(--space-8); }

/* Section spacing */
.section-spacing { padding: var(--space-16) 0; }

/* Element gaps */
.gap-sm { gap: var(--space-2); }
.gap-md { gap: var(--space-4); }
.gap-lg { gap: var(--space-6); }
```

---

## 🔲 Components

### Buttons

```css
/* Primary Button */
.btn-primary {
  background: var(--therapair-purple-base);
  color: white;
  padding: 0.875rem 2rem;
  border-radius: 25px;
  font-weight: 600;
  font-size: 1rem;
  border: none;
  cursor: pointer;
  transition: all 0.2s ease;
}

.btn-primary:hover {
  background: var(--therapair-purple-dark);
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(155, 116, 183, 0.25);
}

/* Secondary Button */
.btn-secondary {
  background: white;
  color: var(--therapair-purple-base);
  padding: 0.875rem 2rem;
  border: 2px solid var(--therapair-purple-base);
  border-radius: 25px;
  font-weight: 600;
  font-size: 1rem;
  cursor: pointer;
  transition: all 0.2s ease;
}

.btn-secondary:hover {
  background: var(--therapair-purple-bg);
  border-color: var(--therapair-purple-dark);
}

/* Button Sizes */
.btn-sm { padding: 0.5rem 1rem; font-size: 0.875rem; }
.btn-md { padding: 0.75rem 1.5rem; font-size: 1rem; }
.btn-lg { padding: 1rem 2rem; font-size: 1.125rem; }
```

### Form Inputs

```css
.input-field {
  width: 100%;
  padding: 0.875rem 1rem;
  border: 2px solid var(--color-gray-200);
  border-radius: 12px;
  font-size: 1rem;
  font-family: var(--font-primary);
  color: var(--color-gray-900);
  transition: all 0.2s ease;
}

.input-field:focus {
  outline: none;
  border-color: var(--therapair-purple-base);
  box-shadow: 0 0 0 3px rgba(155, 116, 183, 0.1);
}

.input-field::placeholder {
  color: var(--color-gray-500);
}

.input-field:disabled {
  background: var(--color-gray-100);
  cursor: not-allowed;
  opacity: 0.6;
}
```

### Cards

```css
.card {
  background: white;
  border-radius: 16px;
  padding: 1.5rem;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  transition: all 0.2s ease;
}

.card-hover:hover {
  transform: translateY(-4px);
  box-shadow: 0 8px 24px rgba(155, 116, 183, 0.15);
}

.card-feature {
  background: white;
  border-radius: 20px;
  padding: 2rem;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
}
```

### Checkboxes & Radio Buttons

```css
/* Custom styled checkbox */
.checkbox-container {
  display: flex;
  align-items: center;
  padding: 1rem;
  border: 2px solid var(--color-gray-200);
  border-radius: 12px;
  cursor: pointer;
  transition: all 0.2s ease;
}

.checkbox-container:hover {
  border-color: var(--therapair-purple-base);
  background: var(--therapair-purple-bg);
}

.checkbox-container input[type="checkbox"] {
  width: 1.25rem;
  height: 1.25rem;
  margin-right: 0.75rem;
  cursor: pointer;
}
```

---

## 📐 Layout

### Container Widths

```css
.container-sm { max-width: 640px; }
.container-md { max-width: 768px; }
.container-lg { max-width: 1024px; }
.container-xl { max-width: 1280px; }
.container-2xl { max-width: 1536px; }
```

### Grid System

```css
.grid-cols-1 { grid-template-columns: repeat(1, 1fr); }
.grid-cols-2 { grid-template-columns: repeat(2, 1fr); }
.grid-cols-3 { grid-template-columns: repeat(3, 1fr); }
.grid-cols-4 { grid-template-columns: repeat(4, 1fr); }

/* Responsive */
@media (max-width: 768px) {
  .md\:grid-cols-2 { grid-template-columns: repeat(2, 1fr); }
}

@media (max-width: 640px) {
  .sm\:grid-cols-1 { grid-template-columns: repeat(1, 1fr); }
}
```

---

## 🎭 Effects

### Shadows

```css
--shadow-xs: 0 1px 2px rgba(0, 0, 0, 0.05);
--shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.1);
--shadow-md: 0 4px 12px rgba(155, 116, 183, 0.15);
--shadow-lg: 0 8px 24px rgba(155, 116, 183, 0.2);
--shadow-xl: 0 20px 40px rgba(155, 116, 183, 0.25);
```

### Border Radius

```css
--radius-sm: 8px;
--radius-md: 12px;
--radius-lg: 16px;
--radius-xl: 20px;
--radius-2xl: 24px;
--radius-pill: 25px;
--radius-full: 9999px;
```

### Transitions

```css
--transition-fast: 0.15s ease;
--transition-base: 0.2s ease;
--transition-slow: 0.3s ease;
```

---

## 📱 Responsive Breakpoints

```css
/* Mobile first approach */
@media (min-width: 640px) { /* sm */ }
@media (min-width: 768px) { /* md */ }
@media (min-width: 1024px) { /* lg */ }
@media (min-width: 1280px) { /* xl */ }
@media (min-width: 1536px) { /* 2xl */ }
```

---

## ♿ Accessibility

### Focus States

```css
/* Keyboard navigation focus */
*:focus-visible {
  outline: 3px solid var(--therapair-purple-base);
  outline-offset: 2px;
}

button:focus-visible,
a:focus-visible {
  outline: 3px solid var(--therapair-purple-base);
  outline-offset: 3px;
}
```

### ARIA Labels

```html
<button aria-label="Close modal">×</button>
<input aria-label="Email address" type="email" />
<div role="dialog" aria-labelledby="modal-title">
  <h2 id="modal-title">Booking Form</h2>
</div>
```

### Color Contrast

All text meets WCAG 2.1 AA standards:
- Normal text: 4.5:1 minimum
- Large text: 3:1 minimum
- UI components: 3:1 minimum

---

## 📊 Animation

### Subtle Animations

```css
@keyframes slideIn {
  from {
    opacity: 0;
    transform: translateY(10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.animate-slide-in {
  animation: slideIn 0.3s ease-out;
}
```

### Hover Effects

```css
.hover-lift {
  transition: transform 0.2s ease;
}

.hover-lift:hover {
  transform: translateY(-2px);
}
```

---

## 🖼️ Icons

### Icon Library

**Lucide Icons** (preferred):
```html
<script src="https://unpkg.com/lucide@latest"></script>
<script>lucide.createIcons();</script>

<i data-lucide="heart"></i>
<i data-lucide="users"></i>
<i data-lucide="check-circle"></i>
```

### Icon Sizes

```css
.icon-sm { width: 16px; height: 16px; }
.icon-md { width: 20px; height: 20px; }
.icon-lg { width: 24px; height: 24px; }
.icon-xl { width: 32px; height: 32px; }
```

---

## 🎨 Usage Examples

### Landing Page Hero

```html
<section class="hero">
  <div class="container-xl mx-auto px-4 py-16">
    <h1 class="text-5xl font-bold leading-tight mb-6">
      Match with a therapist who truly understands you
    </h1>
    <p class="text-xl text-gray-700 leading-relaxed mb-8">
      Finding the right therapist shouldn't be overwhelming
    </p>
    <button class="btn-primary btn-lg">
      Request Early Access
    </button>
  </div>
</section>
```

### Form Card

```html
<div class="card card-hover p-8">
  <h3 class="text-2xl font-semibold mb-4">Tell us about yourself</h3>
  <form class="space-y-6">
    <div>
      <label class="block text-sm font-medium mb-2">Email</label>
      <input type="email" class="input-field" placeholder="your@email.com" />
    </div>
    <button type="submit" class="btn-primary w-full">
      Submit
    </button>
  </form>
</div>
```

---

## 📦 Component Library (Future)

### Planned Components

- [ ] Modal/Dialog
- [ ] Toast Notifications
- [ ] Progress Bar
- [ ] Tabs
- [ ] Accordion
- [ ] Dropdown Menu
- [ ] Date Picker
- [ ] File Upload
- [ ] Avatar
- [ ] Badge

---

## 🔄 Version Control

### Updating the Design System

1. Document changes in this file
2. Update version number
3. Notify all projects
4. Create migration guide if breaking changes
5. Test across all projects

### Breaking Changes

Always provide:
- Migration guide
- Before/after examples
- Timeline for deprecation
- Support during transition

---

## Version History

### v1.0 (2025-10-10)
- Initial design system established
- Unified color palette across projects
- Typography and spacing defined
- Component patterns documented

---

**Maintained By**: Therapair Design Team  
**Last Review**: 2025-10-10

---

**End of Design System**

