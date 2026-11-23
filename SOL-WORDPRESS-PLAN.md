h# Sol handoff · Blackfin WordPress build

Recreate the supplied Blackfin UI as a native WordPress site in a new local `wordpress/` directory inside this workspace. Use the Vercel deployment for appearance and the Git checkout for copy, layout, interaction timing and media. Deliver a site the owner can edit in WordPress and maintain from a terminal. **Production is strictly read-only. Run every install, activation, content write and form test against the local site.** Keep the reference checkout and both earlier export folders for the owner’s later review.

## Inputs and inspection

| Source | What was verified |
| --- | --- |
| [Local Git checkout](blackfin-reference/) | Branch `main`, commit `deecdc2ac3a35aa831c93a90afb1057899508b56`. The four Next.js routes delegate to the page compositions in [src/featured](blackfin-reference/src/featured/). [Pinned source](https://github.com/NikoDola/blackfin/tree/deecdc2ac3a35aa831c93a90afb1057899508b56) |
| [Vercel reference](https://blackfin-taupe.vercel.app/) | Home, Residential, Commercial and Contact returned HTTP 200. The logo and homepage hero also returned image files. The footer links to `/privacy-policy`, which returned 404. |
| [Original images](blackfin-reference/public/images/) | All 25 files decoded successfully. There are 24 JPEGs and one WebP, including the logo, hero images and project galleries. All 21 distinct filenames referenced by the components are present. Keep the four additional gallery files available in Media Library. |
| Live WordPress admin, inspected November 23, 2026 | The footer displayed WordPress 7.1.5. The visible menu included Portfolio, UX Blocks, SeedProd and Login Security. Portfolio showed zero items. Appearance and Plugins were absent from the visible menu. The exact current active theme, child-theme status and full installed/active plugin inventory remain unverified. |
| Historical theme evidence | The June 16 homepage identified `wp-theme-flatsome` and loaded Flatsome assets tagged 3.20.6, Instagram Feed and Custom Facebook Feed styles, plus the theme’s Ninja Forms integration stylesheet. The April 11 contact capture rendered Contact Form 7. These dates matter when choosing a form integration. [June homepage](https://web.archive.org/web/20260616024243/https://blackfincon.com/) · [April contact page](https://web.archive.org/web/20260411143121/https://blackfincon.com/home-page/contact/) |

The [package lock](blackfin-reference/package-lock.json) pins Next 16.1.6, React and React DOM 19.2.3, Framer Motion 12.36.0, Lucide React 0.577.0 and Swiper 12.1.2. The hero actually uses a React timer and Framer Motion. Swiper has no import in the page components. The public WordPress frontend can reproduce these behaviors with PHP-rendered markup, the existing CSS rules and small vanilla JS modules.

## 1. Establish the local WordPress project

Use a standalone custom block theme named `blackfin`. This gives Sol control of the markup and existing CSS while letting the owner edit content with the block editor. The theme can run independently of a licensed Flatsome installation.

Use WordPress Studio on Windows for a local runtime with WP-CLI. At inspection, Node and npm were available. Studio, standalone WP-CLI, PHP and Docker were absent from the shell’s PATH. Make the official Studio CLI available during implementation and record the version used. From the workspace, create the local site once, then use its working directory for subsequent commands. [Studio CLI documentation](https://wordpress.studio/docs/cli/)

~~~powershell
studio create --path ./wordpress --skip-browser
Set-Location ./wordpress
studio status
studio wp option get home
studio wp core version
~~~

Confirm the reported URL is local before any write. Build the theme in `wordpress/wp-content/themes/blackfin/` with `style.css`, `theme.json`, `functions.php`, `templates/`, `parts/`, `patterns/` and `assets/`. Keep a repeatable seed command and its inputs in the workspace. Track custom source and seed inputs in version control. Exclude generated WordPress core, the local database, uploads created by the seed and credentials. [WordPress template hierarchy](https://developer.wordpress.org/themes/templates/template-hierarchy/)

## 2. Port the four page compositions

| Local route | Source and required section order |
| --- | --- |
| `/` | [Home](blackfin-reference/src/featured/home/index.tsx) · Three hero slides, three service cards, residential feature, reversed dark exterior feature, commercial feature, nine gallery images, quote section. |
| `/residential/` | [Residential](blackfin-reference/src/featured/residential/index.tsx) · Two hero slides, remodeling and painting service lists at `#services`, approach feature, nine gallery images, quote section. |
| `/commercial/` | [Commercial](blackfin-reference/src/featured/commercial/index.tsx) · Three hero slides, three service groups, dark specialty feature, eight gallery images, quote section. |
| `/contact/` | [Contact](blackfin-reference/src/featured/contact/index.tsx) · Dedicated contact hero and telephone link, then the shared quote section. |

Use header and footer template parts and a page template that renders the editable page content. Seed core blocks and section patterns for headings, services, image/text features and galleries. A hero pattern can group editable slide content and attachment images inside a wrapper that the frontend carousel script enhances. Reuse a synced pattern for the quote section. Populate navigation, contact links and the twelve service areas from [the reference constants](blackfin-reference/src/lib/utils.ts). Preserve the original route-specific titles, descriptions, image order, alt text and CTA destinations.

Set Home as the static front page. Retain the existing WordPress contact address `/home-page/contact/` as a local redirect to `/contact/`. Connect the footer to a real local Privacy Policy page. Use the [captured policy](blackfin-static/privacy-policy/index.html) as a clearly identified content source for owner review. Keep the old WordPress sample post and author archives out of the new navigation and page seed.

## 3. Match the UI and interactions

Port the [global styles](blackfin-reference/src/app/globals.css) and the CSS module beside each [layout](blackfin-reference/src/components/layout/) and [section](blackfin-reference/src/components/sections/) component. Give the module selectors stable, component-specific class names so generic selectors such as `.section`, `.title` and `.grid` keep their original scope.

- Match the Arial/Helvetica body stack, 1,280 px container and 16 px side gutters. The Next layout registers a Geist variable, while the active body rule selects Arial. Copy the original navy, pale blue, teal, warm accent and text colors into `theme.json`. Preserve breakpoints at 640, 768 and 1,024 px.
- Reproduce the fixed transparent header and the darker, compact state after 20 px of scroll. Match the mobile menu and current-page styling. Support keyboard use, Escape and an accurate expanded state.
- Preserve the 100vh hero with its 600 px minimum, centered image crops, gradient, controls, active dot and 10-second slide interval. Manual navigation resets the timer. Match the text entrance and parallax from [HeroSection](blackfin-reference/src/components/sections/HeroSection.tsx). Keep the first slide readable before JS loads and offer reduced-motion behavior.
- Match gallery source order, two CSS columns below 768 px, three above, 16 px gaps and the hover treatment. Port the alternating image/text layouts and original button and icon treatments. Use local icon assets for the Lucide glyphs that appear in the reference.

## 4. Make the content and images maintainable

Import the 25 repository images into the local Media Library. Assign a stable source-filename key to each attachment and reuse it on subsequent seed runs. Resolve attachment IDs at seed time and render WordPress image sizes and responsive sources. Keep page-specific alt text on each block where a reused image has a different description. [WP-CLI media import](https://developer.wordpress.org/cli/commands/media/import/)

Seed pages by slug and reuse existing menu and pattern records. A second run must preserve editor changes and avoid duplicate pages, slides or attachments. Include the exact commands for start, stop, seed, theme activation and inspecting page/media IDs in the local project README. Verify a CLI content edit and a block-editor content edit both appear on the frontend and survive a restart.

## 5. Implement and test the quote form locally

The [reference form](blackfin-reference/src/components/sections/ContactSection.tsx) waits 1.2 seconds and shows success without sending anything. Preserve its layout, labels, required Name/Phone/Email fields, optional Subject/Message fields, busy state and “Send Another” action. Use a locally installed form integration, with Contact Form 7 as the proposed default based on the captured form. Keep styles in the theme and configure a single reusable form. Validate submissions on the server. Route test delivery to a local mail catcher or a local-only capture and confirm receipt before showing success. Keep the visible contact address distinct from the delivery recipient, which the owner can confirm before any future launch.

## Acceptance and handoff

1. Compare the four reference routes side by side with local WordPress at 390, 768 and 1,440 px. Use the same viewport, first slide and scroll position. Check hero height, type wrapping, photo crops, alternating sections, gallery order, header states and footer. Record any visible deviations.
2. Verify every displayed image loads locally, all four navigation destinations work, both contact paths resolve and the privacy footer link opens. Exercise the carousel, mobile menu, telephone links, validation errors and one local form submission.
3. Re-run the seed and restart the site. Confirm stable content and media counts, persisted edits and a working local URL. Deliver the theme source, seed, setup commands and comparison results. The implementation ends with local owner review.

