# WooCommerce Victoriabank Payments

Contributors: Dan Oistric
Tags: WooCommerce, Moldova, Victoriabank, VB, bank, payment, gateway, visa, mastercard, credit card
Requires at least: 6.0
Tested up to: 6.3.1
Stable tag: 1.0.0
Requires PHP: 7.2
License: GPLv3 or later
License URI: <https://www.gnu.org/licenses/gpl-3.0.html>

Plugin for Victoriabank payments on the WooCommerce shops.

== Description ==
The objective is to provide merchants with a template solution that together with a set of instructions will allow the merchant to easily connect to BC Victoriabank's payment system with 3 payment methods:

1. **Visa/Mastercard Card (accepting all Visa or Mastercard Cards)**
2. **Star Card Rate (accepting only Star Card issued by BC Victoriabank)**
3. **Star Points (accepting only Star Card issued by BC Victoriabank)**

The aim is to ease the process of integration into the payment system of BC Victoriabank.

= Features =

* `Authorization` and `Charge` card transaction types
* `Reversal` transactions – partial or complete refunds
* `Sales completion` transactions – complete authorized transactions
* Order confirmation email with card transaction details
* Free to use – [Open-source GPL-3.0 license on GitHub](https://github.com/cyberink-co/vb-payment-plugin)

= Getting Started =

* [Installation Instructions](./installation/)
* [Frequently Asked Questions](./faq/)

== Installation ==

1. Upload the `vb-payment-plugin` folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in the WordPress admin panel.
3. Configure the plugin settings under the 'Settings' section and WooCommerce Payments settings.
4. After setup settings for payment methods will be needed to make one more step. Is needed to turn off default email templates from WooCommerce, for using email template from plugin. To make this will be need to go to tab `Emails` on WooCommerce settings and turn off templates - `New order`, `Processing order`, `Completed order`.

== Usage ==
How to use the plugin and its main features.

== Frequently Asked Questions ==

= How can I configure the plugin settings? =

Configure the plugin settings under the 'VB payments settings' section in the *Settings* on the admin panel menu.
After use the *WooCommerce > Settings > Payments > Victoriabank...* screens to configure the plugin for each payment method.

= Where can I get the Merchant Data and Connection Settings? =

The merchant data and connection settings are provided by Victoriabank. This data is used by the plugin to connect to the Victoriabank payment gateway and process the card transactions.

= What store settings are supported? =

Victoriabank currently supports transactions in MDL (Moldovan Leu), EUR (Euro) and USD (United States Dollar).

= What is the difference between transaction types? =

* **Charge** submits all transactions for settlement.
* **Authorization** simply authorizes the order total for capture later. Change order status to *Completed* to settle the previously authorized transaction.

= How can I manually process a bank transaction response callback data message received by email from the bank? =

As part of the backup procedure Victoriabank payment gateway sends a duplicate copy of the transaction responses to a specially designated merchant email address specified during initial setup.
If the automated response payment notification callback failed the shop administrator can manually process the transaction response message received from the bank.
Go to the 'VB payments settings' section in the *Settings* on the admin panel menu, go to *Notification settings* section and paste the bank transaction response data as received in the email and click *Process*.

== Screenshots ==

1. Payment gateways
2. Payment gateway settings
3. Merchant data
4. Connection settings
5. Notification settings
6. Payment settings
