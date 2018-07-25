=== EDD Quaderno ===
Author URI: http://quaderno.io
Contributors: polimorfico
Tags: edd, easydigitaldownloads, easy digital downloads, quaderno, vat, gst, sales tax, vatmoss, vat moss, tax, billing, invoices, receipts, credit notes
Requires at least: 4.6
Tested up to: 4.9
Stable tag: 1.17.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Quaderno replaces and improves the default Easy Digital Downloads receipts. Setup in less than 5 minutes.

== Description ==

Quaderno for EDD takes taxes off your plate by automatically calculating tax on every sale and following up with beautiful invoices or receipts, no matter where your customer is located. Transactions and invoices processed through Quaderno always comply with ever-changing local tax rules, including in the US, Canada, Australia, New Zealand, Singapore, and the European Union.

= What you get =

* **Automatic invoices, receipts, and credit notes** with every order in your store, in **multiple languages and currencies**.
* **Comply with local tax laws** in countries around the world, including the EU VAT rules for digital goods & services.
* **100% customizable invoices** to reflect your brand or add a personal touch.
* **Manage all your invoices and revenue sources** in one easy-to-use dashboard.
* **Real-time reports of your revenue**. Track profits, losses, and patterns in the data to identify where your business is going strong and where it may need some TLC.

**Setup in less than 5 minutes**. Fast and easy!

1. Download & activate this plugin
2. [Sign up](https://quaderno.io/integrations/easy-digital-downloads/) for a Quaderno account
3. Paste your API key in your EDD site
4. Brand your Quaderno invoices with custom design

**Please Note:** this plugin requires both an [Easy Digital Downloads](http://wordpress.org/extend/plugins/easy-digital-downloads/) and [Quaderno](https://quaderno.io/integrations/easy-digital-downloads/) account.

== Installation ==

Following are the steps to install the EDD Quaderno

1. Unpack the entire contents of this plugin zip file into your wp-content/plugins/ folder locally.
2. Upload to your site.
3. Navigate to wp-admin/plugins.php on your site (your WP Admin plugin page).
4. Activate this plugin.
5. Configure the options from Downloads > Settings > Quaderno.

OR you can just install it with WordPress by going to Plugins > Add New > and type this plugin's name

That's it! You can now customize your EDD receipts and be EU VAT compliant.

== Frequently Asked Questions ==

= Do I need to modify any code? =
Nope - we take care of everything you. Just install the plugin, add your API token and you’ll be good to go!

= Does Quaderno work with any themes? =
Yes, Quaderno works with any theme - whether free, commercial or custom. You just need EDD activated for Quaderno to work.

If you have any questions please get in touch with us at hello@quaderno.io.

== Screenshots ==

1. Copy your API token and API URL from your Quaderno account
2. Paste it on the Quaderno settings page
3. Checkout screen
4. Example of a receipt

== Changelog ==

= 1.16.12 - July 16, 2018 =
* New: Show a link to invoice in purchase history
* New: Ask for plugin review
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
* New: Store receipts URL on payment meta
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
* Fix: Error at delivering receipts
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
* Fix: Receipts are not being sent if VAT number is empty
* Fix: Credit customers are wrong

= 1.9 =
* New: Merge cart items in invoices and credit notes
* New: WordPress 4.7 compatibility
* Fix: Use last name for people with a valid VAT number

= 1.8 =
* New: Send receipts (simplified invoices)
* New: Add just one item to the invoice
* New: Do not invoice if total amount is zero
* Fix: error with customers on multisites
* Fix: negative payments always generate receipts
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
