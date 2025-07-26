# 🏦 WooCommerce Victoriabank Payments Plugin

**A comprehensive payment gateway plugin for WooCommerce that integrates with Victoriabank's payment system.**

[![WordPress Plugin](https://img.shields.io/badge/WordPress-Plugin-blue)](https://wordpress.org/)
[![WooCommerce](https://img.shields.io/badge/WooCommerce-Compatible-purple)](https://woocommerce.com/)
[![License: GPL v3](https://img.shields.io/badge/License-GPLv3-orange.svg)](https://www.gnu.org/licenses/gpl-3.0)
[![Version](https://img.shields.io/badge/Version-1.0.0-green)](https://github.com/danoistric/vb-payment-plugin)

---

## 📋 Plugin Information

- **Contributors:** Dan Oistric
- **Tags:** WooCommerce, Moldova, Victoriabank, VB, bank, payment, gateway, visa, mastercard, credit card
- **Requires WordPress:** 6.0+
- **Tested up to:** 6.3.1
- **Stable tag:** 1.0.0
- **Requires PHP:** 7.2+
- **License:** GPLv3 or later

---

## 🎯 Description

This plugin provides a seamless integration between WooCommerce stores and Victoriabank's payment processing system. It offers merchants in Moldova a robust, secure, and user-friendly solution for accepting online card payments.

### 💳 Supported Payment Methods

1. **🌍 Visa/Mastercard** - Universal acceptance for all Visa and Mastercard cards
2. **⭐ Star Card Rate** - Installment payments (2, 3, 6, 9, 12 months) for Victoriabank Star Card holders
3. **🎁 Star Points** - Points-based payments for Victoriabank Star Card customers

---

## ✨ Key Features

### 🔒 **Secure Transactions**
- RSA encryption with MD5/SHA-256 support
- Digital signatures for transaction validation
- Secure key management system

### 💰 **Transaction Types**
- **Authorization** - Reserve funds for later capture
- **Charge** - Immediate payment processing
- **Reversal** - Partial or complete refunds
- **Sales Completion** - Complete authorized transactions

### 📧 **Email Customization**
- Custom email subjects and content
- Multilingual support (Romanian, Russian, English)
- Option to hide WooCommerce promotional content
- Transaction details in confirmation emails

### 🔧 **Advanced Features**
- Comprehensive admin settings panel
- Activity logging and debugging
- Manual transaction processing
- Automatic callback handling
- Multi-currency support (MDL, EUR, USD)

---

## 🚀 Installation

### Automatic Installation
1. Log in to your WordPress admin panel
2. Navigate to **Plugins** → **Add New**
3. Search for "WooCommerce Victoriabank Payments"
4. Click **Install Now** and then **Activate**

### Manual Installation
1. Download the plugin ZIP file
2. Upload the `vb-payment-plugin` folder to `/wp-content/plugins/`
3. Activate the plugin through the **Plugins** menu in WordPress
4. Configure the plugin settings

---

## ⚙️ Configuration

### 1. **Basic Setup**
Navigate to **Settings** → **VB payments settings** in your WordPress admin:

#### 📊 Merchant Data
- Merchant name and URL
- Business address
- Return/refund policy URL
- Customer service contact

#### 🔐 Connection Settings
- Upload RSA public/private keys
- Set private key password
- Configure encryption method

#### 📩 Notification Settings
- Set callback URLs
- Configure manual processing options

#### 🎨 Payment Settings
- Upload custom logos for each payment method
- Customize email subjects and content
- Configure promotional text settings

### 2. **WooCommerce Integration**
Go to **WooCommerce** → **Settings** → **Payments**:

- Configure each Victoriabank payment method
- Set transaction types (Authorization/Charge)
- Enable test/live modes
- Configure debugging options

### 3. **Email Templates**
For custom email templates, disable default WooCommerce emails:
- Go to **WooCommerce** → **Settings** → **Emails**
- Disable: "New order", "Processing order", "Completed order"

---

## 🎨 Customization Options

### Email Personalization
- **Custom Subjects:** Use placeholders like `{merchant_name}`, `{order_number}`, `{country}`
- **Additional Content:** Add personalized messages with HTML support
- **Branding:** Hide WooCommerce promotional text
- **Multilingual:** Full support for Romanian, Russian, and English

### Logo Management
- Upload custom logos for each payment method
- Enable/disable logos per payment type
- Automatic fallback to default icons

---

## 🛠️ Technical Requirements

- **WordPress:** 6.0 or higher
- **WooCommerce:** Latest version recommended
- **PHP:** 7.2 or higher
- **OpenSSL:** For RSA encryption
- **cURL:** For API communications

### Supported Currencies
- **MDL** (Moldovan Leu) - Primary
- **EUR** (Euro)
- **USD** (US Dollar)

---

## 📚 Usage Guide

### For Customers
1. Select products and proceed to checkout
2. Choose preferred Victoriabank payment method
3. Complete payment on secure Victoriabank gateway
4. Receive confirmation email with transaction details

### For Merchants
1. Monitor transactions in WooCommerce orders
2. Process refunds directly from order page
3. View activity logs for debugging
4. Manually process failed callbacks if needed

---

## 🔧 Troubleshooting

### Common Issues

**Payment callbacks not working?**
- Check callback URL configuration
- Verify firewall settings
- Use manual processing feature

**Email customization not applying?**
- Ensure WooCommerce default emails are disabled
- Check placeholder syntax
- Verify translation files

**Keys not uploading?**
- Check file permissions on uploads directory
- Verify key file format (.pem)
- Ensure private key password is correct

---

## 📞 Support

For technical support and questions:

- **Developer:** Dan Oistric
- **Website:** [https://victoriabank.md/](https://victoriabank.md/)
- **Documentation:** Available in plugin admin area

### Merchant Support
For merchant account setup and banking questions, contact Victoriabank directly.

---

## 📄 License

This plugin is licensed under the [GPL v3](https://www.gnu.org/licenses/gpl-3.0.html) license.

```
WooCommerce Victoriabank Payments Plugin
Copyright (C) 2025 Dan Oistric

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.
```

---

## 🌟 Contributing

Contributions are welcome! Please feel free to submit issues and pull requests.

---

**Made with ❤️ for the Moldovan e-commerce community**
