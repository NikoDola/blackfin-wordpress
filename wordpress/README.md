# Blackfin local WordPress

Open [the site](http://localhost:8881/) or [local wp-admin](http://localhost:8881/wp-admin/). The URLs below belong to the WordPress Studio instance for this workspace.

The [Blackfin block theme](wp-content/themes/blackfin/) contains the templates, styles and frontend interactions. The [seed](../blackfin-content/seed.php) creates the pages, Media Library attachments, shared quote pattern, navigation and Contact Form 7 record. Initial page copy and image order come from [pages.json](../blackfin-content/pages.json) and the [reference checkout](../blackfin-reference/). After import, edit page text and images in WordPress. Use Appearance → Editor to edit the header, footer, and shared navigation. Edit the form in Contact → Contact Forms and its surrounding section in the synced Blackfin Quote Section pattern.

## Amanda’s review copy

The local homepage lists four services, followed by three captioned project photos. Each service has an editable WordPress page, its own project image, a scope overview, a customer question and a link that opens the quote form with the project type filled in. The About page lists the 12 communities from the original site. All ten pages are published to localhost for review. The source copy for a fresh install is in [services.json](../blackfin-content/services.json) and [service-blocks.php](../blackfin-content/service-blocks.php).

| Page | What to confirm |
| --- | --- |
| [New Construction & Additions](http://localhost:8881/new-construction-and-additions/) | Specify which additions, ADUs, buildouts and ground-up projects the team accepts. The current copy invites inquiries about ground-up work. |
| [Remodeling](http://localhost:8881/remodeling/) | Confirm the residential scope and choose a representative kitchen or bathroom project. |
| [Interior Painting](http://localhost:8881/interior-painting/) | Confirm the surfaces and property types the team wants to highlight. |
| [Exterior Painting](http://localhost:8881/exterior-painting/) | Confirm the prep and coating services for homes and commercial properties. |
| [About Us and service areas](http://localhost:8881/about-us/#service-areas) | Review the communities, company background and any team details to add. |

### Handpicked Google reviews

In Appearance → Editor → Patterns, open **Blackfin Selected Google Reviews**. Enter Amanda’s first selected quote into the empty Quote block. In the citation field, enter the reviewer’s public display name and link it to the original Google review. Duplicate the Quote block for each additional review, then save. Home and About both display the same selection automatically. While the pattern is empty, both pages omit the section.

### Recent Instagram posts

The local install includes Smash Balloon Social Photo Feed. The footer links to Blackfin’s Instagram profile, and the homepage feed stays hidden until it has a configured ID. To turn on the timeline, the account owner connects the business account under **Instagram Feed → Settings → Manage Sources**. Under **All Feeds**, create a User Timeline, select the source and order it newest first. Copy the feed ID from the Embed dialog. Edit the homepage Shortcode block from `[blackfin_instagram]` to `[blackfin_instagram feed="ID"]`, replacing ID with that number. Check that recent posts load on mobile and that a later post appears after the provider’s cache refreshes. [Provider instructions](https://smashballoon.com/doc/display-multiple-instagram-feeds/)

### Handoff to Blackfin’s maintenance team

After content approval, move the theme, pages, media, navigation, synced patterns and form through the existing host’s staging workflow. Preserve existing production records. When migrating WordPress content, remap the local media and page URLs, attachment IDs, navigation references and the three theme options for navigation, quote form and review pattern to the destination records. The seed is for this local workspace.

On staging, connect or reauthorize the intended social account, review privacy copy for the enabled integrations and verify a quote reaches the business inbox. Keep staging private. At the approved launch, verify the public canonical URLs, search visibility, sitemap, existing contact redirect and quote delivery. Submit the production sitemap through the existing Google Search Console property and inspect the new page URLs.

## Local commands

Run these from the workspace root in PowerShell. The workspace-local WordPress Studio CLI is pinned to 2.3.1. The package requires Node 22 or later.

~~~powershell
$studio = '.\.blackfin-runtime\node_modules\.bin\studio.cmd'
& $studio start --path .\wordpress --skip-browser
& $studio status --path .\wordpress
& $studio wp core version --path .\wordpress
& $studio wp post list --post_type=page --fields=ID,post_title,post_name,post_status --path .\wordpress
& $studio wp post list --post_type=attachment --post_status=inherit --fields=ID,post_title --path .\wordpress
& $studio stop --path .\wordpress
~~~

Studio status shows the local administrator login. For a CLI content or title edit, use `studio wp post update` with a page ID from the list and `--path .\wordpress`. For example, `& $studio wp post update 42 '--post_title=Contact Us' --path .\wordpress` updates page 42 when that ID belongs to the intended page.

### Bootstrap a fresh local instance

Keep a copy of the theme source before setting up an empty `wordpress/` directory. Use the [Studio CLI](https://wordpress.studio/docs/cli/) to create the instance, then place the theme source at `wordpress/wp-content/themes/blackfin/`. The existing workspace already has WordPress, the theme and seeded content.

~~~powershell
npm.cmd ci --prefix .blackfin-runtime
$studio = '.\.blackfin-runtime\node_modules\.bin\studio.cmd'
& $studio create --path .\wordpress --name 'Blackfin Local' --skip-browser
& $studio wp option get home --path .\wordpress
# After placing the theme source and verifying the local URL:
& $studio wp theme activate blackfin --path .\wordpress
& $studio wp plugin install contact-form-7 --version=6.2.6 --activate --path .\wordpress
& $studio wp plugin install instagram-feed --version=6.17.0 --activate --path .\wordpress
$seed = (Get-Item -LiteralPath '.\blackfin-content\seed.php').FullName
& $studio wp eval-file $seed --user=admin --path .\wordpress
~~~

The seed checks that the database belongs to the workspace’s localhost WordPress instance. It recognizes existing records by slug or source filename and preserves editor content on subsequent runs. Update the seed inputs as well when an edit needs to be reproducible on another fresh instance.

## Local quote delivery

The form sends to a reserved `example.test` inbox. WordPress Studio captures local mail as `.eml` files under the Email path shown by `studio status`. The on-screen success panel responds to Contact Form 7’s delivery event. The business email displayed in the contact card is the address from the design reference. The Privacy Policy copy comes from the earlier site capture and is ready for owner review. [Contact Form 7 events](https://contactform7.com/dom-events/) · [Archived privacy policy](https://web.archive.org/web/20260517083055/https://blackfincon.com/privacy-policy/)

## Original rebuild verification

The local site runs WordPress 7.1.5 in Studio. Headless Chrome loaded all four reference routes and compared them with Vercel at widths of 390, 768 and 1,440 px. The main sections differed in height by up to 10 px at 390 px and up to 2 px at the two wider sizes. The footer agreed within 1 px. All displayed images loaded and the pages fit their viewports. The carousel adds a pause control and respects reduced-motion preferences. [Measurements](../.blackfin-runtime/preview/comparison.json) · [Vercel reference](https://blackfin-taupe.vercel.app/)

All five pages opened in the block editor with valid blocks. A temporary WP-CLI edit and a temporary Gutenberg edit survived reseeding and a Studio restart. The original headings were restored. The mobile menu, slide controls, legacy contact redirect, privacy link, form validation, and local quote delivery passed.

## Expanded site verification

The expanded site opened in Chrome at 390, 768 and 1,440 px. The four homepage cards, Services submenu, quote links and service-area anchors worked. All ten local pages passed Gutenberg block validation. The homepage emits business structured data based on the name, telephone and service areas shown on the site. Local search indexing is disabled.

One cold-load Chrome run at 390 × 844 px, 2× device pixel ratio, 80 ms latency, 8 Mbps download and 4× CPU slowdown recorded largest contentful paint between 0.89 and 1.03 seconds on the five new pages. The homepage measured 1.32 seconds. Each recorded zero cumulative layout shift during the test window. Repeat the measurements on the intended host after connecting the social feed. [Local measurements](../.blackfin-runtime/preview/service-speed.json)

