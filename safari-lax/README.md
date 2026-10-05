# Safari LAX theme: install and plug-in guide

## Install (about 5 minutes)
1. WordPress admin > Appearance > Themes > Add New > Upload Theme > safari-lax.zip > Activate.
2. On activation the theme automatically creates: Home (set as homepage), About, Journeys, Services, Gallery, Contact,
   a Primary menu, and three SAMPLE journeys. It never overwrites pages that already exist.
3. Settings > Safari LAX: set the enquiry recipient, the "from" address, the thank-you message, phone, Instagram, privacy link.
4. Appearance > Customize > Safari LAX Home: headline, intro, button, hero image, footer email. Site Identity > Logo replaces the text wordmark.
5. Replace ALL sample text and the three sample journeys before launch.

## Built in
Self-hosted fonts (no Google requests, so no cookie banner is needed while the site uses no analytics or third-party embeds), mobile menu, skip link, schema markup, hardened login/author/XML-RPC, enquiry inbox. If analytics or embeds are added later, a cookie notice becomes necessary.

## Where things are edited
- Pages: normal WordPress page editor. Gallery page: add a Gallery block (wide alignment).
- Journeys: "Journeys" menu. Title, excerpt, featured image, and "Location line" box.
- Enquiries: "Enquiries" menu. Every form submission is saved here BEFORE any email is sent, so nothing is lost if email fails.
  Each entry shows whether the team email was sent.
- Brand: top of style.css (colour and font tokens) and the Google Fonts link in functions.php.

## Email: pick ONE
A. No plugin: add to wp-config.php (above the "stop editing" line), using the real bookings@ mailbox details from her email host:
   define('LAX_SMTP_HOST','mail.example.com'); define('LAX_SMTP_PORT',587); define('LAX_SMTP_SECURE','tls');
   define('LAX_SMTP_USER','bookings@safarilax.world'); define('LAX_SMTP_PASS','...');
   (use 'ssl' with port 465 if the host requires it)
B. Use an SMTP plugin such as WP Mail SMTP with the same mailbox.
Then Settings > Safari LAX > "Send a test email". If an enquiry ever fails to email, a warning appears in the admin and the enquiry is still in Enquiries.

## Launch checklist
- [ ] SMTP set up and test email received
- [ ] All sample text and the 3 sample journeys replaced; hero image and logo added; site icon set (Customize > Site Identity)
- [ ] Privacy Policy page (created as a DRAFT) completed, reviewed by a lawyer, and published; the footer and form link appear automatically
- [ ] Settings > Reading: untick "Discourage search engines" ONLY at go-live
- [ ] Settings > Permalinks set to "Post name"; Settings > General: correct site title and tagline
- [ ] Send a test enquiry from phone and desktop: appears in Enquiries, reaches bookings@, thank-you reaches guest
- [ ] Backup plugin installed and scheduled; security plugin or host firewall on; strong admin password; admin username is not "admin"
- [ ] SSL active (https) before DNS cutover; add `define('DISALLOW_FILE_EDIT', true);` to wp-config.php
- [ ] DNS cutover per the section below; send and receive test mail on info@ and bookings@
- [ ] Submit the sitemap (yoursite/wp-sitemap.xml) to Google Search Console
- Send a test enquiry from phone and desktop. Check: it appears under Enquiries, arrives at bookings@, Reply-To is the guest, the guest gets the thank-you.
- Set a privacy policy page and add its link in Settings > Safari LAX.
- Install a backup plugin and a security plugin (these are the "premium licences" in the quote).

## Domain and email (do not break her mail)
1. Export or screenshot ALL current DNS records at Namecheap first.
2. Keep every MX, SPF (TXT), DKIM and DMARC record exactly as is.
3. Only change the A record (and www CNAME) to the new host.
4. Add the host's SPF include to the EXISTING SPF record; never create a second SPF record.
5. Lower TTL to 300 the day before; restore after. Test sending and receiving on info@ and bookings@.

## Content intake checklist (ask the client)
Brand document, logo files (SVG/PNG), fonts, final copy per page, 8-15 hero-quality photos with credits, Journeys list,
services wording, privacy policy, decision on languages, Namecheap access, current email host, social links, the correct travel@ address.

## Handover
Recorded walkthrough: edit pages, add a Journey, swap images, change menu, read and reply to Enquiries, updates and backups.
