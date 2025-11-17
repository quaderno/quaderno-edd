=== Quaderno for EDD ===
Contributors: polimorfico
Tags: sales tax, vat, gst, verifactu, ticketbai
Tested up to: 6.8
Requires PHP: 7.4
Stable tag: 1.38.1
License: GPLv3

Automate global tax calculations and compliant invoicing for Easy Digital Downloads. Handle sales tax, VAT, GST worldwide with instant reports.

== Description ==

Transform tax complexity into seamless growth with automated global tax compliance. The Quaderno plugin eliminates manual tax calculations and invoice generation, ensuring your Easy Digital Download store stays compliant across all markets while you focus on scaling your business.

Selling globally shouldn't mean drowning in tax paperwork. Quaderno handles the intricate web of international sales tax, VAT, and GST regulations automatically, so you can concentrate on what matters most: growing your business.

https://www.youtube.com/watch?v=mGs6SVOr7fU

= How it works =

Quaderno works behind the scenes to automatically calculate precise taxes for every sale and generate compliant invoices and credit notes, regardless of your customer's location. Our intelligent system continuously updates with the latest tax regulations, seamlessly handling complex requirements including US sales tax, EU VAT, Canadian GST, and mandatory e-invoicing systems like Verifactu and TicketBAI.

= Key Benefits & Features = 

* **Effortless Global Tax Compliance**: Effortlessly comply with ever-changing local tax laws in countries around the world. Rest easy knowing your business is always in line with regulations.
* **Proactive Tax Notifications**: Get instant alerts when you become liable for taxes by surpassing a tax registration threshold worldwide. We'll also notify you when a tax rate changes in any region where you sell.
* **Real-Time, Accurate Tax Calculation**: Our intelligent database instantly identifies the precise tax rate and amount based on your product and your customer's exact location, ensuring accuracy every time.
* **Instant, Comprehensive Tax Reports**: Generate all the vital information you need for your tax returns at a glance. Quaderno's intuitive dashboard provides immediate, actionable insights into your tax obligations.
* **Automated E-invoicing & Credit Notes**: Ditch the manual work. Professionally crafted invoices and credit notes are automatically generated and sent for every order, available in multiple languages and currencies. We also provide support for legally compliant e-invoicing and e-reporting systems, including Verifactu/VERI*FACTU and TicketBAI.
* **Worldwide Registration & Filing Service**: Simplify your tax obligations from end to end. We offer a comprehensive service to handle your tax registration and filing, wherever you do business.
* **Customer Self-Service**: Empower your customers to download their invoices and credit notes directly from their Easy Digital Download orders page, reducing customer service inquiries and improving their experience.

== Frequently Asked Questions ==

= How does Quaderno automatically handle taxes for my Easy Digital Download sales? =
After [connecting to your Easy Digital Download store](https://support.quaderno.io/article/608-connecting-easy-digital-downloads/?utm_source=wordpress&utm_campaign=edd), Quaderno runs automatically in the background. It instantly identifies the correct tax rate for each transaction based on your products and customer location, seamlessly integrating into your checkout process. It's truly set-and-forget tax automation.

= Does Quaderno support e-invoicing and e-reporting formats? =
Absolutely. Quaderno ensures compliance with global e-invoicing regulations, automatically supporting legally mandated systems including Spain's **Verifactu/VERI*FACTU** and **TicketBAI**. Your invoices are automatically formatted and submitted to tax authorities in the required format, eliminating manual compliance work while keeping your business fully compliant.

= Can I use Quaderno with other e-commerce platforms besides Easy Digital Download? =
Yes! Quaderno centralizes tax and invoicing across multiple sales channels. Beyond Easy Digital Download, you can integrate with platforms like WooCommerce and Shopify, managing all your sales data, tax calculations, and compliance reporting from a single unified dashboard.

= What are the pricing options for Quaderno? =
Quaderno offers transparent, transaction-based pricing that scales with your business. Plans start at just $29 per month with no hidden fees. View all available [pricing plans here](https://www.quaderno.io/pricing/?utm_source=wordpress&utm_campaign=edd).

== Installation ==

= Minimum Requirements =
* PHP 7.4 or greater is required (PHP 8.0 or greater is recommended)
* Easy Digital Download 3.2 or greater
* A [Quaderno account](https://quadernoapp.com/signup/?utm_source=wordpress&utm_campaign=edd).

= Automatic installation =
Automatic installation is the easiest option -- WordPress will handle the file transfer, and you won’t need to leave your web browser. To do an automatic install of Easy Digital Download Quaderno, log in to your WordPress dashboard, navigate to the Plugins menu, and click “Add New.”

In the search field type “Quaderno,” then click “Search Plugins.” Once you’ve found us, you can view details about it such as the point release, rating, and description. Most importantly of course, you can install it by! Click “Install Now,” and WordPress will take it from there.

= Manual installation =
Manual installation method requires downloading the Easy Digital Download Quaderno plugin and uploading it to your web server via your favorite FTP application. The WordPress codex contains [instructions on how to do this here](https://wordpress.org/support/article/managing-plugins/#manual-plugin-installation).


== Screenshots ==

1. Copy your API token and API URL from your Quaderno account
2. Paste it on the Quaderno settings page
3. Checkout screen
4. Example of an invoice

== Changelog ==

= 1.38.1 – November 17, 2025 =
* Update: remove plugin requirement to support EDD Pro

= 1.38.0 – September 19, 2025 =
* New: autosend invoices if the autosend preference is active in Quaderno
* New: support for WordPress 6.8

= 1.37.7 – November 14, 2024 =
* New: support for WordPress 6.7

= 1.37.6 – November 8, 2024 =
* Fix: address is not copying in recurring invoices

= 1.37.5 – July 18, 2024 =
* Fix: round total amounts on line items

= 1.37.4 – July 17, 2024 =
* New: support for WordPress 6.6

= 1.37.3 – July 9, 2024 =
* Fix: error when a renewed subscription has no customer

= 1.37.2 – April 2, 2024 =
* New: support for WordPress 6.5

= 1.37.1 – February 28, 2023 =
* Fix: recurring invoices don't show the customer's name

= 1.37.0 – February 20, 2023 =
* New: use orders data to generate invoices

= 1.36.0 – December 19, 2023 =
* New: use the customer's current data to invoice renewed payments
* Fix: some credit notes are not being issued
* Fix: remove warning when accesing nonexistent IP addresses

= 1.35.1 – October 18, 2023 =
* Fix: the email tag must return the URL instead of an HTML link

= 1.35.0 – October 16, 2023 =
* New: add a email tag to show a link to the final invoice
* New: hooks to the purchase form business fields

= 1.34.0 – September 21, 2023 =
* New: show permalinks to credit notes in new refunds
* Fix: error when getting past orders during the invoice creation

= 1.33.4 – August 9, 2023 =
* New: support for WordPress 6.3

= 1.33.3 – Jun 12, 2023 =
* Fix: error in getting the IP address from the parent order

= 1.33.2 – May 26, 2023 =
* Fix: fallback to payment metadata searching in order metadata

= 1.33.1 – May 1, 2023 =
* Fix: warning message "Undefined index: clear_trasients"

= 1.33.0 – March 30, 2023 =
* New: support for partial credits
* Fix: review requests are not showing up
* Fix: customers meta are duplicated

= 1.32.1 – March 22, 2023 =
* New: Added support for paypal_commerce gateway

= 1.32.0 – February 7, 2023 =
* New: users from any country can enter their tax ID

= 1.31.0 – December 21, 2022 =
* IMPORTANT: This release needs EDD 3.x or higher, and should be tested in your staging environments prior to running on your live site.
* Fix: business name and tax ID is not set successfully
* New: refactoring the code to process recurring orders

= 1.30.3 – November 19, 2022 =
* Fix: fallback if function edd_get_customer_by does not exist

= 1.30.2 – November 14, 2022 =
* Fix: error when customer does not exist

= 1.30.1 – November 11, 2022 =
* Fix: notice on invoice manager
* Fix: a wrong tax rate is applied until the tax id is changed if the customer has it already set

= 1.30.0 – November 2, 2022 =
* New: send transaction id to Quaderno
* New: support for WordPress 6.1

= 1.29.9 – October 24, 2022 =
* Fix: shopping cart is hidden when a free product is purchased

= 1.29.8 – September 21, 2022 =
* Update: Spanish translation

= 1.29.7 – May 25, 2022 =
* New: support for WordPress 6.0

= 1.29.6 – April 22, 2022 =
* Fix: tax ID validation issues when validation service is down

= 1.29.5 – January 27, 2022 =
* New: support for WordPress 5.9

= 1.29.4 – December 27, 2021 =
* Fix: business names do not support single-quotes

= 1.29.3 – November 25, 2021 =
* Improvement: use the blog ID in the invoice's processor ID to avoid conflicts in multisite installations

= 1.29.0 – July 23, 2021 =
* New: validate tax IDs in Switzerland, Quebec, Australia, and New Zealand
* New: always show the field business name
* New: allow users to clear the tax cache
* New: support for WordPress 5.8

= 1.28.0 – July 5, 2021 =
* New: introducing new tax codes for SaaS and standard-rated products

= 1.27.3 – April 25, 2021 =
* New: move business fields to a new section in checkout form

= 1.27.2 – March 28, 2021 =
* Fix: error in total amounts in invoices

= 1.27.1 – March 26, 2021 =
* New: Base the transaction item amount on the subtotal minus discount.
* New: Expose the invoice transaction via filter before the invoice is created.
* New: Expose the credit transaction via filter before the credit is created.
* Fix: Wrong variable name in invoice creation.  

= 1.27.0 – March 23, 2021 =
* New: Use new Quaderno Transactions API
* New: Show renewal and upgrades notes if using EDD Recurring with the Software Licensing extension
* New: Support for EDD 3.0

= 1.26.5 – March 11, 2021 =
* New: Support for WordPress 5.7
* New: Support for EDD 2.10.1

= 1.26.4 – February 11, 2021 =
* New: hooks to overwrite tax calculations

= 1.26.3 – February 5, 2021 =
* Fix: location evidence are not stored in Quaderno

= 1.26.2 – January 20, 2021 =
* New: Support for installment payments

= 1.26.1 – December 9, 2020 =
* New: Support for WordPress 5.6

= 1.26.0 – November 23, 2020 =
* New: show link to Quaderno invoices in payment history page
* New: store customer's business name and tax ID as meta for future payments
* Improvement: javascript code

= 1.25.4 – August 27, 2020 =
* New: send tax region to Quaderno

= 1.25.3 – August 11, 2020 =
* New: Support for WordPress 5.5

= 1.25.2 - April 2, 2020 =
* New: Support for WordPress 5.4

= 1.25.1 - February 4, 2020 =
* Improvement: show reverse charge note when needed

= 1.25.0 - February 2, 2020 =
* Improvement: merge tax id and vat number fields
* Improvement: use region full names on invoices & credits

= 1.24.5 - December 10, 2019 =
* Improvement: move company name field to billing addres details area

= 1.24.4 - November 8, 2019 =
* Improvement: create new contact in Quaderno is customers udpate their names

= 1.24.3 - October 2, 2019 =
* Improvement: show tax IDs in some countries

= 1.24.2 - October 2, 2019 =
* Improvement: use VAT number if exists

= 1.24.1 - July 25, 2019 =
* Fix: syntax error in PHP 7.2+

= 1.24.0 - July 25, 2019 =
* New: send US tax codes to Quaderno
* New: use cities to calculate US sales tax
* New: require tax ID only in some EU countries

= 1.23.2 - June 10, 2019 =
* Change the processor ID for credit notes

= 1.23.1 - May 27, 2019 =
* Fix: Billing state/province field reset when plugin is active

= 1.23.0 - April 18, 2019 =
* New: Create products on Quaderno
* New: Generate invoices from admin dashboard

= 1.22.0 - March 10, 2019 =
* New: Send product code to Quaderno
* New: Store payment URL on Quaderno

= 1.21.1 - February 26, 2019 =
* Tested with WordPress 5.1

= 1.21.0 - February 19, 2019 =
* New: Send download tags to Quaderno
* New: Show error message when VIES service is down

= 1.20.2 - January 22, 2019 =
* New: Translation in German

= 1.20.1 - January 14, 2019 =
* New: Link credit notes to original invoices in Quaderno

= 1.20.0 - January 9, 2019 =
* New: new hooks

= 1.19.1 - December 6, 2018 =
* Fix: Tax ID should not be required for foreign customers
* New: support for WordPress 5.0

= 1.19.0 - December 3, 2018 =
* New: notes filter and invoice/credit action

= 1.18.2 - November 15, 2018 =
* Improvement: delete transients when plugin is deactivated

= 1.18.1 - September 19, 2018 =
* Fix: nonce warning

= 1.18.0 - September 10, 2018 =
* Improvement: send tax type to Quaderno

= 1.17.7 - August 30, 2018 =
* Fix: tax rates are not override for valid VAT numbers

= 1.17.6 - August 23, 2018 =
* Fix: Non admin users can see the review request

= 1.17.5 - August 9, 2018 =
* New: Demo video 

= 1.17.4 - August 8, 2018 =
* New: Show empty cell in History when a Quaderno invoice is not available 

= 1.17.3 - August 7, 2018 =
* Fix: Links to Quaderno invoices are show when they don't exist
* Fix: Error with customers on multisites

= 1.17.2 - July 27, 2018 =
* Fix: Error in tax calculation call in checkout page

= 1.17.1 - July 25, 2018 =
* Fix: Error when parent metadata are copied in recurring payments

= 1.17.0 - July 16, 2018 =
* New: Show a link to invoice in purchase history
* New: Ask for plugin review

= 1.16.12 - July 16, 2018 =
* Enhacement: Use EDD tax fallback when Quaderno does not return any tax rate
* Enhacement: Reduce transient time for VAT numbers
* Enhacement: Use .json extension in all API calls

= 1.16.11 - July 2, 2018 =
* Fix: Use timestamps as processor ID

= 1.16.10 - June 20, 2018 =
* Fix: VAT numbers are not copied on subscriptions
* Fix: Warning in payment details when VAT number does not exist

= 1.16.9 - June 9, 2018 =
* Fix: Syntax error in settings 

= 1.16.8 - May 23, 2018 =
* Fix: VAT numbers are not showing up in the payment details
* Fix: VAT numbers are not updated 

= 1.16.7 - May 18, 2018 =
* Fix: Recurring payment do not remember original VAT numbers and IP address

= 1.16 =
* New: Use translations from wordpress.org
* New: Update customer's data in recurring purchases
* Fix: Refunds are not created for edd_subscriptions
* New: Users can add new payment methods
* New: Discounts are showed on invoices and credits
* Fix: Company name disappears when you change an order status
* Fix: Some payment processor repeat their transaction ID
* New: Store invoices URL on payment meta
* New: Show Quaderno URL on payment details
* Fix: Refactoring evidence collection

= 1.15 =
* New: Support for gateway fees
* Fix: Error when you apply a 100% off discount

= 1.14 =
* New: Add an item per cart detail
* New: Update VAT number if customer exists

= 1.13 =
* New: Tax ID field 
* New: Billing address is required
* New: Compatibility with EDD 2.8
* New: Update Quaderno API version
* Fix: Error at delivering invoices
* Fix: Error in free purchases

= 1.12 =
* Always show business name field
* Move placeholders to descriptions
* Fix: Use payment date on invoices
* Fix: Problem with edd_get_payment
* Fix: Credit notes for recurring payments are not issued
* New: Edit VAT numbers
* Fix: VAT number is not stored after a PayPal payment
* Fix: Credits are not issued for recurring payments
* New: WordPress 4.8 compatibility
* Fix: http_build_query() warnings

= 1.11 =
* New: Compatibility with EDD 2.7

= 1.10 =
* New: Users can enter their business name during checkout
* Update copys
* Fix: Invoices are not being sent if VAT number is empty
* Fix: Credit customers are wrong

= 1.9 =
* New: Merge cart items in invoices and credit notes
* New: WordPress 4.7 compatibility
* Fix: Use last name for people with a valid VAT number

= 1.8 =
* New: Send invoices (simplified invoices)
* New: Add just one item to the invoice
* New: Do not invoice if total amount is zero
* Fix: error with customers on multisites
* Fix: negative payments always generate invoices
* Fix: sometimes transaction id does not exist
* Fix: sanitize vat numbers and translations

= 1.7 =
* New: Send gateway info to Quaderno
* Fix: error when customer has not name

= 1.6 =
* New: Compatibility with EDD 2.5
* New: Validate EU VAT Numbers
* New: Request Tax ID to Spanish customers
* New: Hide VAT Number field when customer is based in the store country
* New: Track different payment methods on Quaderno
* Fix: syntax bug for certain PHP versions
* Fix: error with Tax ID when the store is not Spanish

= 1.5.3 =
* New: Mark recurring payments

= 1.5.2 =
* Fix: error in payment date

= 1.5.1 =
* Fix: autosend option cannot be deactivated

= 1.5.0 =
* New: Process recurring payments

= 1.4.1 =
* Minor fixes

= 1.4.0 =
* New: Create credit notes for refunds
* New: Refactoring code
* New: Update descriptions

= 1.3.1 =
* Fix: error when purchases include more than one item

= 1.3.0 =
* New: Remove contacts when a customer is deleted
* New: Create invoices faster

= 1.2.4 =
* TNew: ag invoices from EDD

= 1.2.3 =
* New: Tested on Wordpress 4.3
* Fix: total amount is wrong when taxes are included
* New: Update translations

= 1.2.2 =
* Fix: error at calculating taxes when cart is empty

= 1.2.1 =
* Fix: discount does not appear on final invoices

= 1.2.0 =
* New: Cache tax calculations
* New: Mark e-books on download detail page

= 1.1.0 =
* New: Calculate taxes for e-books
* New: New icons

= 1.0.0 =
* First version
