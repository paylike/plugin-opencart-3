# OpenCart plugin for Paylike [![Build Status](https://travis-ci.org/paylike/plugin-opencart-3.svg?branch=master)](https://travis-ci.org/paylike/plugin-opencart-3)

This plugin is *not* developed or maintained by Paylike but kindly made
available by the community.

Released under the MIT license: https://opensource.org/licenses/MIT

You can also find information about the plugin here: https://paylike.io/plugins/opencart-3

## Supported OpenCart versions

[![Last succesfull test](https://log.derikon.ro/api/v1/log/read?tag=opencart3&view=svg&label=Opencart&key=ecommerce&background=01afe8)](https://log.derikon.ro/api/v1/log/read?tag=opencart3&view=html)

*The plugin has been tested with most versions of Opencart at every iteration. We recommend using the latest version of Opencart, but if that is not possible for some reason, test the plugin with your OpenCart version and it would probably function properly.*

Last tested version: Opencart 3.0.3.8

## Prerequisites

- The plugin works with vQmod, but also with OCMOD, no need to install vQmod if you don't already need it.

## Installation

Once you have installed OpenCart, follow these simple steps:
1. Signup at [paylike.io](https://paylike.io) (it’s free)
1. Create a live account
1. Create an app key for your OpenCart website
1. Upload the paylike.ocmod.zip file in the extensions uploader.
1. Log in as administrator and click  "Extensions" from the top menu then "extension" then "payments" and install the Paylike plugin by clicking the `Install` link listed there.
1. Click the Edit Paylike button
1. Select a store for your configuration
1. Add the Public and App key that you can find in your Paylike account and enable the plugin
1. Save the settings

## Updating settings

Under the extension settings, you can:
 * Choose the OpenCart store to make settings for
 * Update the payment method text in the payment gateways list
 * Update the payment method description in the payment gateways list
 * Update the title that shows up in the payment popup
 * Add test/live keys
 * Set payment mode (test/live)
 * Change the capture type (Instant/Delayed)
 * Change the order statuses that the orders will get after a certain payment action is done (authorization/capture/refund/void)

 ## How to capture / manage transactions

  The transactions will show up under **`Sales -> Paylike Payments`** side menu. Here you can see capture/refund/void transactions depending on their status. Alternatively Paylike payments can be accessed from SITE_URL/admin/index.php?route=extension/payment/paylike/payments and they can be reached by clicking the green button at the top right of the extension settings page

  In Delayed mode you can do transactions (full capture, refund, void) from admin panel, for each order info page, adding a history to the order. The `Order Status` that is wanted to be set for specific transaction must  be identical with that set in Paylike extension page (Advanced section/tab). By default it is `Completed` for capture, `Refunded` for refund and `Voided` for void an order.

1. Capture
    * In Instant mode, the orders are captured automatically
    * In Delayed mode you can do this in admin panel, order info page, adding **`Completed`** order status history to the order.
    * OR
    * In Delayed mode you can do this in admin panel Paylike Payments in Action section in the table.
2. Refund
    * In Delayed mode you can do this in admin panel, order info page, adding **`Refunded`** order status history to the order.
    * OR
    * To Refund an order you can do this in admin panel Paylike Payments in Action section in the table.
3. Void
    * In Delayed mode you can do this in admin panel, order info page, adding **`Voided`** order status history to the order.
    * OR
    * To Void an order you can do this in admin panel Paylike Payments in Action section in the table.

## Available features

### Multistore support
    * The Paylike multi-store functionality allows the merchant to have different sets of keys for each store.
    * You need to have a separate merchant account for a single store to keep Paylike transactions for each store independently.

### Transactions
    1. Capture
        * Opencart admin panel: full capture
        * Paylike admin panel: full/partial capture
    2. Refund
        * Opencart admin panel: full/partial refund (only full refund from order view page)
        * Paylike admin panel: full/partial refund
    3. Void
        * Opencart admin panel: full void
        * Paylike admin panel: full/partial void

## Changelog

#### 1.4.0:
* Added multistore support

#### 1.3.0:
* Added logic to make a transaction on order status change (admin panel)

#### 1.2.0:
* Updated js SDK version to 10.js
* Updated logic to work with SDK v10 version

#### 1.1.1:
* Added logic to convert currency code to uppercase

#### 1.1.0:
* Updated js sdk version to 6.js

#### 1.0.9:
* Fix infinite loading on the popup close

#### 1.0.8:
* Added backend key validation

#### 1.0.7:
* Update description text

#### 1.0.6:
* Version bump

#### 1.0.5:
* This release fixes a minor bug, showing up when using a quick checkout extension.

#### 1.0.4:
* Initial stable release
* This is a stable release, that works for opencart 2.3.* and for opencart 3.*+ .
* It can also be used as an update for https://github.com/paylike/plugin-opencart-2.3, all transactions will be ported, as the plugin is backward compatible.

#### 1.0.0:
* Initial version
