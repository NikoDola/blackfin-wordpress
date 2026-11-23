# Blackfin UI and UX review for Sol

The three journeys to protect are choosing a service, judging the work from photos, and requesting a quote. The local build already has a useful foundation for all three. The Remodeling page is a good visual reference for the next pass. It names the service immediately, shows a recognizable finished kitchen, and gives the visitor one clear next action.

I reviewed all ten published WordPress pages in Chrome at 390, 768 and 1,440 px wide. I also checked the overview pages, Contact and the light-background templates on a small phone or in landscape, and exercised the menu with a keyboard. The findings below are from the local build. The viewport measurements describe this build, and the suggested page order is a design recommendation.

## Fix first

### 1. Carousel controls cover copy and intercept a service link

At 390 × 844, the previous and next arrows sit over the paragraph on every slide I checked across Home, Residential and Commercial. At 320 × 568 on Residential, the slide dots and pause button sit directly on **Our Services**. Hit-testing the left, center and right portions of that link returned carousel controls. At 768 px the home arrows also overlap the first headline. [Home on a phone](mobile-home.png) · [Residential on a small phone](small-phone-carousel.png)

**Change** — Give the copy and controls separate space. My preference is a single project photo, a plain service-and-location headline, and a quote button on phones. If the carousel stays, put its controls in a row below the actions and let the hero grow with the copy.

**Verify** — At 320, 390 and 768 px, cycle through every slide. Tap the center and both sides of each visible action. The intended link must open every time. Preserve pause and reduced-motion behavior if the carousel remains.

### 2. Keyboard users move behind the open mobile menu

On Remodeling at 390 px, focus the hamburger and press Enter. The menu opens. The next Tab focuses **Request a free quote** on the page behind it. The following Tab reaches **Discuss your project** farther down the page. Escape at that point leaves the menu open. The header pattern places the toggle after the navigation in DOM order, and the Escape listener runs on the header. [Open mobile menu](mobile-menu.png)

**Change** — Put the toggle before its controlled panel in reading order. Give the disclosure a consistent focus sequence. If focus leaves a nonmodal panel, close it. Escape from an open panel should close it and return focus to the toggle.

**Verify** — Using only Enter, Tab, Shift+Tab and Escape, open the menu, reach all four service links, follow one, then reopen and dismiss it. Focus must remain visible throughout.

### 3. The header disappears on light pages

Open Privacy Policy or a missing URL at the top of the page. The desktop navigation is white text over white, with a transparent header. On a phone, the white hamburger disappears into the same background. Scrolling adds a dark header and reveals controls that visitors needed on arrival. [Desktop privacy header](desktop-privacy.png) · [Mobile privacy header](mobile-privacy-policy.png)

**Change and verify** — Use the solid navy header on light templates from first paint. Check Privacy Policy and the 404 at the top, after scrolling and after returning to the top. Normal-size navigation text should reach 4.5:1 contrast. [W3C contrast guidance](https://www.w3.org/WAI/WCAG22/Understanding/contrast-minimum.html)

### 4. A quote click should land at the form

From Remodeling, **Request a free quote** opens the top of Contact. At 390 × 844 the visitor sees a Contact Us introduction, another quote heading and three contact cards. The first input begins **925 px** down. The Subject field is blank even though the visitor just chose Remodeling. On an empty submission, focus stayed on Send Message while the first field error was above the visible viewport. [Arrival after requesting a quote](mobile-quote-arrival.png)

**Change** — Give the form a stable anchor, put the first field beside or directly under the quote heading on mobile, and keep tap-to-call near it. Pass the originating service into an editable project-type or subject field. Show which fields are required before submission. On validation failure, focus the first invalid field or an accessible error summary with links to the fields.

**Verify** — From each service page, a single quote click puts the first usable field in view below the sticky header. A visitor can change the suggested service and correct an empty submission using only the keyboard. In the same pass, darken the placeholder examples and contact-card labels. They currently measure about 1.8:1 and 2.8:1 respectively against white.

## Improve the visual hierarchy

### 5. Bring finished work earlier on the homepage

At 390 px wide, the hero occupies the first 844 px. The four stacked service cards take another 1,909 px. Three large feature sections follow, so the project gallery starts **5,549 px** down the page. Visitors see project photos earlier, but several sections repeat service descriptions before they can browse results.

**Change** — Keep the four service names easy to scan. Follow them with a small selection of finished projects, with one factual line per image explaining the work. Give Residential and Commercial compact entry points. While the Instagram section contains only a profile link, put that link alongside the gallery or in the footer. Restore a full feed section once it has posts. As a design target, let a phone visitor reach an identifiable finished project within the first two screenfuls.

### 6. Make the service tiles behave like their appearance

On the homepage I clicked the first service photo and its heading. Both left me on Home. Only **Explore new construction & additions** opened the page. That repeats a long title directly under the card and gives the smallest element the entire navigation job. [Desktop service directory](desktop-services.png)

**Change and verify** — Make the service name the main link and extend its hit area across the photo and card. Use one keyboard focus stop per service with a visible focus outline. Clicking the name, image or empty card space should open the same service.

### 7. Edit the portfolio as evidence of workmanship

The Remodeling hero shows a finished kitchen clearly. Interior Painting leads with a plastic-covered room, and Exterior Painting leads with scaffolding in front of a storefront sign. Those images explain preparation, but a prospective customer also needs to see the finish. The Residential gallery mixes finished and in-progress photos without captions. One 225-pixel-wide image is displayed roughly 405 px wide on desktop and looks visibly soft. That site-work photo is labeled “Cabinet painting” for screen readers, and the finished kitchen is labeled “Exterior painting.” [Residential gallery](residential-gallery.png)

**Change and verify** — Choose a sharp, representative finished photo for each service where one is available. Use preparation photos farther down with a short explanation. Correct descriptions in the existing WordPress blocks and the seed data. Check the image against its alt text and rendered size at desktop and mobile widths. Ask Amanda only for project details or images the existing material cannot establish.

## Implementation notes

- **Header and carousel** — Start in [header.php](../wordpress/wp-content/themes/blackfin/patterns/header.php), [site.js](../wordpress/wp-content/themes/blackfin/assets/site.js), and [site.css](../wordpress/wp-content/themes/blackfin/assets/site.css). The affected CSS selectors include `.bf-nav-header`, `.bf-hero-prevBtn`, `.bf-hero-nextBtn` and `.bf-hero-dots`.
- **Quote flow** — The shared section is built by `bf_quote_section()` in [blocks.php](../blackfin-content/blocks.php). The form fields live in [quote-form.html](../blackfin-content/quote-form.html); the local Contact Form 7 record supplies the current rendered form.
- **Service cards and photography** — Start with `bf_service_directory()` in [service-blocks.php](../blackfin-content/service-blocks.php), [services.json](../blackfin-content/services.json), and the existing gallery descriptions in [pages.json](../blackfin-content/pages.json). The seed preserves previous editor changes, so verify both the current local pages and the source used for fresh installs.

The four services and the single service-area page fit Amanda’s brief. Keep those destinations, the existing navy and teal palette, and the editable WordPress content through the UI pass.
