# OpenCart plugin for Paylike

This plugin is *not* developed or maintained by Paylike but kindly made
available by the community.

Released under the MIT license: https://opensource.org/licenses/MIT

You can also find information about the plugin here: https://paylike.io/plugins/opencart-3

## Supported OpenCart versions

[![Last succesfull test](https://log.derikon.ro/api/v1/log/read?tag=opencart3&view=svg&label=Opencart&key=ecommerce&background=01afe8)](https://log.derikon.ro/api/v1/log/read?tag=opencart3&view=html)

*The plugin has been tested with most versions of Opencart at every iteration. We recommend using the latest version of Opencart, but if that is not possible for some reason, test the plugin with your OpenCart version and it would probably function properly.* 

## Prerequisites

- The plugin works with vQmod, but also with OCMOD, no need to install vQmod if you don't already need it. 

## Installation

1. Upload the paylike.ocmod.zip file in the extensions uploader. 
2. Log in as administrator and click  "Extensions" from the top menu then "extension" then "payments" and install the Paylike plugin by clicking the `Install` link listed there.
3. Click the Edit Paylike button 
4. Add the Public and App key that you can find in your Paylike account and enable the plugin

## Updating settings

Under the extension settings, you can:
 * Update the payment method text in the payment gateways list
 * Update the payment method description in the payment gateways list
 * Update the title that shows up in the payment popup 
 * Add test/live keys
 * Set payment mode (test/live)
 * Change the capture type (Instant/Manual via Paylike Tool)
 * Change the order statuses that the orders will get after a certain payment action is done (void/refund/capture/authorization)
 
 ## How to capture / managing transactions
  
  The transactions will show up under paylike payments (admin/index.php?route=extension/payment/paylike/payments) and they can be reached by clicking the green button at the top of the extension settings page. Here you can see refund/void and capture transactions depending on their status. 
