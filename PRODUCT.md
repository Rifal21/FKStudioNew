# Product

<!-- impeccable:product-schema 1 -->

## Platform

web

## Users

Primary: owners of small and medium businesses (UMKM) in Indonesia who need a website or app and are not technical. They compare packages on the landing page, then order and pay through the site's own checkout.

Secondary: startups and larger companies with a bigger or more specific need, who ask for a quote through the "Enterprise Custom" package instead of buying off the shelf.

Also in the product: the FKStudio team (super admin) who manage landing content, orders, invoices, vouchers, tickets and users from the dashboard. Visitors and customers see the Indonesian and English versions of the site (ID/EN).

## Product Purpose

FKStudio sells custom website and app development to businesses, and the site is both the storefront and the delivery channel. A visitor reads the offer, picks a package, pays a down payment (DP) and the remaining balance (pelunasan) online, then follows the build from their account. Success is a visitor who reaches checkout or a consultation request, and a customer who can see where their project stands without chasing the team.

## Positioning

Custom-coded websites and apps, not templates or page-builder output. A neighbouring agency that resells themes could not truthfully say this.

## Operating Context

- Content (hero, about, services, portfolio, packages, testimonials, clients, owners, blog) is managed from a CMS dashboard and stored per language (`_id` / `_en` fields); the landing page renders whatever is in the database.
- Packages are published with a price and feature list; checkout supports domain availability checks, vouchers, DP and pelunasan payments, and invoices (including PDF).
- Payments go through Duitku; domain lookup and registration go through IDCloudHost; media can live on Nextcloud storage.
- Customers have an account area for orders, websites, tickets and profile.
- Contact is by email, phone and WhatsApp (the process section links to a WhatsApp chat built from the site's phone setting).

## Capabilities and Constraints

- Stack already exists: Laravel 13 (PHP 8.3+), Blade + Alpine.js, Tailwind CSS 3 via Vite, Swiper, AOS, SweetAlert2. Headings use Bricolage Grotesque and body text Hanken Grotesk (variable fonts via Fontsource).
- Packages today: Starter Spark (Rp 7.500.000), Business Growth (Rp 18.500.000, featured), Enterprise Custom (by quote). These are seeded values; the real prices are managed in the CMS.
- Services today: custom website development, mobile app development, web systems and SaaS platforms, UI/UX design, API and payment integration, IT consulting and maintenance.
- Product copy exists in both Indonesian and English; any new copy needs both.
- Open: whether the seeded prices, package features and contact details are final.

## Brand Commitments

- The name is FKStudio.
- The user rejected the first redesign (violet and pink gradients, uniform rounded cards) as too "template". Binding visual constraints they stated: bright yellow plus light blue, no purple or violet, and a landing page that does not look like a generic SaaS template. (Recorded as stated; the visual world itself belongs to design work, not this file.)

## Evidence on Hand

All proof on the site is currently placeholder seed data. Future work must not present any of it as fact:

- Client logos (Google, Microsoft, Apple, Meta) are not real FKStudio clients.
- Testimonials (Budi Santoso, Sarah Wijaya, Michael Chen, Linda Permata) are not real customers.
- Stats ("5+ years", "100+ projects", "50+ happy clients") are not verified.
- Package prices, the contact email (hello@fkstudio.com), phone number and address are seed values.
- Portfolio projects and hero/about images come from seeders or uploads and are unconfirmed as real work.

No real case studies, press, or verified customer numbers are on hand yet. Do not invent any; use the CMS fields as the source and mark anything unconfirmed.

## Product Principles

1. Sell on what is true: custom-coded work, clear packages, and an account where progress is visible. Do not borrow credibility from placeholder clients or numbers.
2. Serve the small-business owner first. Plain language, prices and next steps stated openly, no agency jargon.
3. One continuous experience from landing page to checkout to customer account to dashboard, not separate products.
4. Everything works in Indonesian and English, and on a phone.
5. The CMS owns the content. Design must hold up when the team swaps in real text, images and numbers.

## Accessibility & Inclusion

No product-specific standard was confirmed with the user. The repo's redesign plan targets WCAG AA contrast (4.5:1) and flags missing image `alt` text and icon-button labels; treat that as the working bar, not a stated requirement.
