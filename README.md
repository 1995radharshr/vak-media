# Vāk Media — Official Website

> **Clearer. Not louder.**
> Content agency for YouTube & social media creators.

Live site: [vakmedia.in](https://vakmedia.in)
Contact: [hello@vakmedia.in](mailto:hello@vakmedia.in)

---

## Overview

Single-file production website (`index.html`) for Vāk Media — a full-service content agency handling strategy, production, distribution, and advertising for YouTube creators and social media content creators.

---

## Features

### Design & UX
- **Dark / Light theme toggle** — CSS custom properties, persisted via `localStorage`
- **SVG sketch animation** — Logo paths draw themselves on load using `stroke-dashoffset` keyframes, with a re-sketch on hover
- **Scroll-painted blueprint grid** — CSS mask `--grid-reveal` variable updated on scroll so grid lines appear to be drawn as you scroll down
- **Cinematic section reveals** — Per-section `@keyframes`: `cinSlideLeft`, `cinZoomIn`, `cinFlipUp`, `cinBlurUp`, `cinSlideRight`, `cinSplitLeft`, `cinSplitRight`
- **Custom cursor** — Lagging ring + dot with state classes: `.expand` on links/cards, `.hide` on form inputs, `.active` on click targets
- **Magnetic buttons** — `mousemove` offset transform on `.magnetic` elements (desktop only)
- **3D card tilt** — `perspective(600px) rotateX/Y` on who-cards (desktop only)
- **Card glow follow** — Radial gradient follows mouse inside service cards
- **Parallax orbs** — `data-speed` attribute drives `translateY` on scroll
- **Particle system** — Capped at 20 particles, pauses on `visibilitychange`
- **Loading screen** — Progress bar + skip button, logo sketch animates during load
- **Scroll progress bar** — Fixed top bar fills as page scrolls
- **Blueprint coordinate display** — Live X/Y and scroll% readout (desktop)
- **Mobile hamburger menu** — Slide-in panel with overlay, closes on link click

### SEO
- Semantic HTML5 with proper heading hierarchy (`h1` → `h2` → `h3`)
- `<title>` and `<meta description>` keyword-optimised for: Vāk, Vak, Vakmedia, YouTube content agency, social media content creator, creator economy
- `<meta keywords>` with full keyword set
- Open Graph tags (`og:title`, `og:description`, `og:image`, `og:type`, `og:url`)
- Twitter Card tags (`twitter:card`, `twitter:site` @thevakmedia, `twitter:creator` @vakmediaofficial)
- `<link rel="canonical">` pointing to `https://vakmedia.in`
- JSON-LD structured data (`Organization` schema) with `makesOffer`, `contactPoint`, `sameAs`
- `<meta name="referrer">` strict origin policy
- `<meta name="theme-color">` updates dynamically with theme toggle
- `<noscript>` fallback — full content visible without JavaScript
- `rel="noopener noreferrer"` on all `target="_blank"` links

### Contact Form
- Powered by **[FormSubmit.co](https://formsubmit.co)** — no backend required
- Submissions delivered to `hello@vakmedia.in`
- Honeypot spam protection (`_honey` hidden field)
- CAPTCHA enabled (`_captcha: true`)
- Custom subject line and table template
- `#thankyou` redirect hash — shows success state after submission
- Client-side validation: required fields, RFC-compliant email regex, select validation
- Error messages animate in/out per field

---

## Social Links

| Platform  | Handle | URL |
|-----------|--------|-----|
| Instagram | @vakmediaofficial | https://www.instagram.com/vakmediaofficial/ |
| YouTube   | @vakmediaofficial | https://www.youtube.com/@vakmediaofficial |
| X         | @thevakmedia      | https://x.com/thevakmedia |

---

## SEO Keywords

Primary: `Vāk`, `Vak`, `Vakmedia`, `Vākmedia`, `Vāk Media`, `Vak Media`
Secondary: `content agency`, `YouTube content agency`, `social media content creator`, `content creator agency`, `YouTube creator`, `content strategy`, `video production`, `content distribution`, `YouTube growth`, `Instagram growth`, `TikTok creator`, `creator economy`, `content marketing agency India`

---

## Fonts

All fonts loaded from Google Fonts:

| Font | Use |
|------|-----|
| Fraunces | Display headings (serif, italic) |
| Barlow | Body text, buttons |
| Barlow Condensed | Labels, uppercase elements |
| IBM Plex Mono | Mono labels, coordinates |

---

## Tech Stack

| Layer | Choice |
|-------|--------|
| Format | Single HTML file (inline CSS + JS) |
| Fonts | Google Fonts |
| Form backend | FormSubmit.co |
| Hosting | Static (any CDN / GitHub Pages / Vercel / Netlify) |
| Dependencies | None (zero npm, zero build step) |

---

## Deployment

The entire site is `index.html` — deploy anywhere that serves static files:

**GitHub Pages**
```
Settings → Pages → Source: main branch / root
```

**Netlify / Vercel**
Drag and drop the `index.html` file or connect this repo.

**Custom domain**
Point your domain's DNS A record to your host. Update the canonical URL in `<head>`:
```html
<link rel="canonical" href="https://vakmedia.in">
```
And in the JSON-LD block:
```json
"url": "https://vakmedia.in"
```

---

## Changelog

### 2026-03-23
- Updated social links: Instagram → @vakmediaofficial, YouTube → @vakmediaofficial, X → @thevakmedia
- Replaced LinkedIn with YouTube across all social link sections
- Fixed broken `</p>` HTML tag in contact section
- Updated Twitter meta: `twitter:site` → @thevakmedia, `twitter:creator` → @vakmediaofficial
- Updated JSON-LD `sameAs` array with correct social profile URLs
- Full SEO audit: keyword-enriched title, description, OG tags, Twitter Card, JSON-LD

### 2026-03-22
- Added SVG sketch animation to logo (stroke-dashoffset draw, then fill)
- Added scroll-painted blueprint grid (CSS mask driven by JS scroll position)
- Light/dark theme toggle with localStorage persistence
- Custom cursor with per-element interaction states
- FormSubmit.co form integration → hello@vakmedia.in
- Removed "For who" nav link
- Mobile responsive: hamburger menu, 100dvh, pointer:coarse guards
- Cinematic per-section scroll reveal animations
- Particle system, parallax orbs, scan line, vignette, noise overlay
- Initial production build

---

## License

&copy; 2026 Vāk Media. All rights reserved.
