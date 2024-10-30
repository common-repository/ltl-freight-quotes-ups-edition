=== LTL Freight Quotes - TForce Edition ===
Contributors: enituretechnology
 Tags: eniture,TForce,LTL freight rates,LTL freight quotes, shipping estimates
Requires at least: 6.5
Tested up to: 6.6.2
Stable tag: 3.6.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html


Real-time LTL freight quotes from UPS. Fifteen day free trial.

== Description ==

UPS (NYSE: UPS) is a leading global provider of transportation and logistics solutions. Its freight division, UPS Freight, provides freight related services, including less-than-truckload (LTL) freight. This application retrieves your negotiated UPS LTL freight rates, takes action on them according to the application settings, and displays the result as shipping charges in the WooCommerce checkout process. If you don’t have an UPS Freight account, call 800-333-7400, or register online [register online](https://www.ups.com/one-to-one/login).

**Key Features**

* Displays negotiated LTL shipping rates in the shopping cart.
* Provide quotes for shipments within the United States and to Canada.
* Custom label results displayed in the shopping cart.
* Display transit times with returned quotes.
* Product specific freight classes.
* Support for variable products.
* Define multiple warehouses.
* Identify which products drop ship from vendors.
* Product specific shipping parameters: weight, dimensions, freight class.
* Option to determine a product's class by using the built in density calculator.
* Option to include residential delivery fees.
* Option to include fees for lift gate service at the destination address.
* Option to mark up quoted rates by a set dollar amount or percentage.
* Works seamlessly with other quoting apps published by Eniture Technology.

**Requirements**

* WooCommerce 6.4 or newer.
* A UPS account number.
* Your username and password to ups.com.
* Your UPS web services authentication key.
* A API key from Eniture Technology.

== Installation ==

**Installation Overview**

Before installing this plugin you should have the following information handy:

* Your UPS account number.
* Your username and password to ups.com.
* Your UPS web services authentication key.

If you need assistance obtaining any of the above information, contact your local UPS Freight office
or call the [UPS Freight](http://ups.com) corporate headquarters at 1-800-333-7400.

A more comprehensive and graphically illustrated set of instructions can be found on the *Documentation* tab at
[eniture.com](https://eniture.com/woocommerce-ups-ltl-freight/).

**1. Install and activate the plugin**
In your WordPress dashboard, go to Plugins => Add New. Search for "LTL Freight Quotes - UPS Edition", and click Install Now.
After the installation process completes, click the Activate Plugin link to activate the plugin.

**2. Get a API key from Eniture Technology**
Go to [Eniture Technology](https://eniture.com/woocommerce-UPS-ltl-freight/) and pick a
subscription package. When you complete the registration process you will receive an email containing your API key and
your login to eniture.com. Save your login information in a safe place. You will need it to access your customer dashboard
where you can manage your API keys and subscriptions. A credit card is not required for the free trial. If you opt for the free
trial you will need to login to your [Eniture Technology](http://eniture.com) dashboard before the trial period expires to purchase
a subscription to the API key. Without a paid subscription, the plugin will stop working once the trial period expires.

**3. Establish the connection**
Go to WooCommerce => Settings => UPS Freight. Use the *Connection* link to create a connection to your UPS account.

**5. Select the plugin settings**
Go to WooCommerce => Settings => UPS Freight. Use the *Quote Settings* link to enter the required information and choose
the optional settings.

**6. Enable the plugin**
Go to WooCommerce => Settings => Shipping. Click on the link for UPS Freight and enable the plugin.

**7. Configure your products**
Assign each of your products and product variations a weight, Shipping Class and freight classification. Products shipping LTL freight should have the Shipping Class set to “LTL Freight”. The Freight Classification should be chosen based upon how the product would be classified in the NMFC Freight Classification Directory. If you are unfamiliar with freight classes, contact the carrier and ask for assistance with properly identifying the freight classes for your  products. 

== Frequently Asked Questions ==

= What happens when my shopping cart contains products that ship LTL and products that would normally ship FedEx or UPS? =

If the shopping cart contains one or more products tagged to ship LTL freight, all of the products in the shopping cart 
are assumed to ship LTL freight. To ensure the most accurate quote possible, make sure that every product has a weight, dimensions and a freight classification recorded.

= What happens if I forget to identify a freight classification for a product? =

In the absence of a freight class, the plugin will determine the freight classification using the density calculation method. To do so the products weight and dimensions must be recorded. This is accurate in most cases, however identifying the proper freight class will be the most reliable method for ensuring accurate rate estimates.

= Why was the invoice I received from UPS Freight more than what was quoted by the plugin? =

One of the shipment parameters (weight, dimensions, freight class) is different, or additional services (such as residential 
delivery, lift gate, delivery by appointment and others) were required. Compare the details of the invoice to the shipping 
settings on the products included in the shipment. Consider making changes as needed. Remember that the weight of the packaging 
materials, such as a pallet, is included by the carrier in the billable weight for the shipment.

= How do I find out what freight classification to use for my products? =

Contact your local UPS Freight office for assistance. You might also consider getting a subscription to ClassIT offered 
by the National Motor Freight Traffic Association (NMFTA). Visit them online at classit.nmfta.org.

= How do I get a UPS Freight account? =

Check your phone book for local listings or call  1-800-333-7400.

= Where do I find my UPS Freight username and password? =

Usernames and passwords to UPS Freight’s online shipping system are issued by UPS Freight. If you have a UPS Freight account number, go to [UPS.com](http://ups.com) and click the login link at the top right of the page. You will be redirected to a page where you can register as a new user. If you don’t have a UPS Freight account, contact the UPS Freight at 1-800-333-7400.

= How do I get a API key for my plugin? =

You must register your installation of the plugin, regardless of whether you are taking advantage of the trial period or 
purchased a API key outright. At the conclusion of the registration process an email will be sent to you that will include the 
API key. You can also login to eniture.com using the username and password you created during the registration process 
and retrieve the API key from the My API keys tab.

= How do I change my plugin API key from the trail version to one of the paid subscriptions? =

Login to eniture.com and navigate to the My API keys tab. There you will be able to manage the licensing of all of your 
Eniture Technology plugins.

= How do I install the plugin on another website? =

The plugin has a single site API key. To use it on another website you will need to purchase an additional API key. 
If you want to change the website with which the plugin is registered, login to eniture.com and navigate to the My API keys tab. 
There you will be able to change the domain name that is associated with the API key.

= Do I have to purchase a second API key for my staging or development site? =

No. Each API key allows you to identify one domain for your production environment and one domain for your staging or 
development environment. The rate estimates returned in the staging environment will have the word “Sandbox” appended to them.

= Why isn’t the plugin working on my other website? =

If you can successfully test your credentials from the Connection page (WooCommerce > Settings > UPS Freight > Connections) 
then you have one or more of the following licensing issues:

1) You are using the API key on more than one domain. The API keys are for single sites. You will need to purchase an additional API key.
2) Your trial period has expired.
3) Your current API key has expired and we have been unable to process your form of payment to renew it. Login to eniture.com and go to the My API keys tab to resolve any of these issues.

== Screenshots ==

1. Quote settings page
2. Warehouses and Drop Ships page
3. Quotes displayed in cart

== Changelog ==

= 3.6.3 =
* Fix: Resolved the dropship selection issue for variable products

= 3.6.2 =
* Update: Updated connection tab according to WordPress requirements 

= 3.6.1 =
* Update: Introduced capability to suppress parcel rates once the weight threshold has been reached.
* Update: Compatibility with WordPress version 6.5.2
* Update: Compatibility with PHP version 8.2.0

= 3.6.0 =
* Update: Introduced TForce API in the plugin. 

= 3.5.1 =
* Update: Changed required plan from standard to basic for delivery estimate options.

= 3.5.0 =
* Update: Introduced the Origin Level Markup feature
* Update: Introduced the Product Level Markup Feature
* Update: Display "Free Shipping" at checkout when handling fee in the quote settings is  -100% .
* Update: Introduce the Shipping Logs feature.
* Update: Fixed issue in receiver city name

= 3.4.5 =
* Update: Compatibility with WooCommerce HPOS(High-Performance Order Storage)

= 3.4.4 =
* Update: Removed new API connections. 

= 3.4.3 =
* Update: Introduced username and password fields on connection settings field. 
* Fix: Inherent Freight Class value of parent to variations.  

= 3.4.2 =
* Update: Modified expected delivery message at front-end from "Estimated number of days until delivery" to "Expected delivery by".
* Fix: Allow space characters in the city field in the warehouse tab.
* Fix: Tab navigation in the warehouse form.
 
= 3.4.1 =
* Fix: Right selection of the API for quotes.

= 3.4.0 =
* Update: Introduced TForce new API AOuth process.

= 3.3.2 =
* Update: Added compatibility with "Address Type Disclosure" in Residential address detection

= 3.3.1 =
* Update: Compatibility with WordPress version 6.1
* Update: Compatibility with WooCommerce version 7.0.1

= 3.3.0 =
* Update: Added feature, Add Handling Fee/Markup for each product

= 3.2.0 =
* Update: Added feature, Add Handling Fee/Markup for each ship-from origin

= 3.1.10 =
* Fix: Fixed issue in saving product shipping parameters

= 3.1.9 =
* Update: Introduced connectivity from the plugin to FreightDesk.Online using Company ID

= 3.1.8 =
* Fix: Conflict resolved with "WooCommerce Payments" plugin

= 3.1.7 =
* Update: Changed UPS label and links to TForce 

= 3.1.6 =
* Fix: Fixed freight class field conflict Stripe Payments. 

= 3.1.5 =
* Update: Compatibility with WordPress version 6.0.
* Update: Included tabs for freightdesk.online and validate-addresses.com

= 3.1.4 =
* Fix: Fixed Cron scheduling.

= 3.1.3 =
* Update: Compatibility with Crowlernation custom work Flat Shipping. 
* Update: Compatibility with WordPress multisite network
* Fix: Fixed support link. 

= 3.1.2 =
* Update: Compatibility with PHP version 8.1.
* Update: Compatibility with WordPress version 5.9.

= 3.1.1 =
* Update: Relocation of NMFC Number field along with freight class.

= 3.1.0 =
* Fix: Issue fixed in release 3.1.0

= 3.1.0 =
* Update: Added features, Multiple Pallet Packaging and data analysis.

= 3.0.0 =
* Update: Compatibility with PHP version 8.0.
* Update: Compatibility with WordPress version 5.8.
* Fix: Corrected product page URL in connection settings tab.

= 2.4.1 =
* Update: Cuttoff Time.
* Update: Added images URL for freightdesk.online portal.
* Update: CSV columns updated.
* Update: Compatibility with Micro-warehouse addon.

= 2.3.1 =
* Update: Added compatibility with WP 5.7, compatibility with shippable ad-don, compatibility with account number ad-don fields showing on the checkout page.

= 2.3.0 =
* Update: Compatibility with WordPress 5.6

= 2.2.7 =
* Fix: Fixed In Store and Local delivery as an default selection.

= 2.2.6 =
* Fix: Restrict plugin js and css from cart and checkout pages.  

= 2.2.5 =
* Update: Introduced a dimensional freight with pallet package solution. <a href = "http://eniture.com/doc/pallet-packaging.zip">Pallet packaging addon </a> is required for this feature.

= 2.2.4 =
* Update: Compatibility with WordPress 5.4,Product nesting feature, Update meta data for freightdesk.online and a link in admin to get updated plans from eniture.com. 

= 2.2.3 =
* Update: Compatibility with WordPress 5.4

= 2.2.2 =
* Update: Introduced weight of handing unit and maximum weight per handling unit

= 2.2.1 =
* Update: Changed UI of quote settings tab.

= 2.2.0 =
* Update: This update introduces: 1) New options of “Cart Weight Threshold” and "Weight of Handling Unit" on quote settings tab. 2) Don’t sort by price. 3) Customizable error message in the event the plugin is unable to retrieve rates. 4) Fixed UI of Quote Settings tab.

= 2.1.2 =
* Update:  Introduced quote using Third Party account number.

= 2.1.1 =
* Fix: Appearance of pallet weight field.

= 2.1.0 =
* Update:  Introduced new feature pallet weight.

= 2.0.2 =
* Update: Compatibility with WordPress 5.1

= 2.0.1 =
* Fix: Identify one warehouse and multiple drop ship locations in basic plan.

= 2.0.0 =
* Update: Introduced new features and Basic, Standard and Advanced plans.

= 1.2.2 =
* Update: Compatibility with WordPress 5.0

= 1.2.1 =
* Fix: Corrected quotes labels on checkout page

= 1.2.0 =
* Update: Introduced compatibility with the Residential Address Detection plugin.

= 1.1.1 =
* Fix: Fixed issue with new reserved word in PHP 7.1.

= 1.1.0 =
* Update: Compatibility with WordPress 4.9

= 1.0 =
* Initial release.

== Upgrade Notice ==
