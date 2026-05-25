=== Sellora AI Recommendations ===
Contributors: salmanrafiei20
Donate link: https://smartgraph.online
Tags: woocommerce, recommendations, ai, related products, upsell
Requires at least: 5.8
Tested up to: 7.0
Stable tag: 1.0.0
Requires PHP: 7.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Turn your WooCommerce store into a revenue engine with intelligent AI recommendations and cross-sells.

== Description ==

Sellora AI Recommendations is a recommendation engine built specifically for WooCommerce. Sellora processes recommendation algorithms on cloud infrastructure to display highly relevant products to your customers based on their shopping behavior.

### Features
* **Zero-Config FBT:** Automatically generate and display "Frequently Bought Together" bundles.
* **Grid Recommendations:** Show personalized product suggestions below the main product to increase engagement.
* **Performance Optimized:** Cloud-based processing ensures zero-latency and keeps your local server fast and light.

== External services ==

This plugin relies on an external cloud API to generate advanced product recommendations.

* **What the service is:** The plugin connects to our recommendation engine API (https://sgr-backend-demo.onrender.com/api/v1) to calculate and fetch relevant products using collaborative filtering.
* **What data is sent:** To provide accurate recommendations, the plugin safely transmits anonymized purchase events (Product ID and encrypted User ID) and view events.
* **Terms of Service:** https://smartgraph.online/terms
* **Privacy Policy:** https://smartgraph.online/privacy

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/sellora-ai-recommendations` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress.
3. Navigate to the Sellora AI settings panel to configure your preferences and connect the API.

== Frequently Asked Questions ==

= Does this slow down my website? =
No. All algorithmic calculations are processed off-site on our cloud servers, keeping your local server incredibly fast.

== Changelog ==

= 1.0.0 =
* Initial release for WordPress repository.