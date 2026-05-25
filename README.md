# Sellora AI Recommendations for WooCommerce 🚀🤖

A high-performance, cloud-decoupled recommendation engine client built for WooCommerce. This plugin offloads heavy database queries and complex AI recommendation logic from the local WordPress MySQL database (`wp_postmeta`) to a specialized cloud infrastructure, dropping local server recommendation load to 0%.

## 📌 The Challenge & Solution
Standard WooCommerce setups handle related products and cross-sells using heavy SQL queries that scan the messy `wp_postmeta` table. During high-traffic events (like Black Friday), these queries cause database locks and crash checkouts. 

**Sellora AI** solves this by acting as an asynchronous API client. It syncs essential product contexts to a cloud-based GraphRAG engine (powered by Python, .NET, and Graph Databases), computes highly accurate personalized recommendations off-site, and injects them back into the frontend seamlessly via secure AJAX endpoints.

## ⚙️ Technical Architecture & Stack
* **Frontend Logic:** Native JavaScript (jQuery/AJAX) optimizing DOM injection for "Frequently Bought Together" (FBT) bundles and dynamic product grids.
* **Backend Bridge:** Object-Oriented PHP / WordPress Plugin API.
* **Security Layer:** WordPress Nonce verification (`wp_verify_nonce`) preventing CSRF attacks on dynamic cart operations.
* **Asynchronous Fallback Architecture:** If the cloud API is unreachable or the user lacks an API key, the plugin intelligently falls back to native WooCommerce relations (`wc_get_related_products`), guaranteeing 100% uptime and zero UI breakage.

## 🚀 Key Features
* **Frequently Bought Together (FBT) Bundles:** Automatically displays Amazon-style multi-product bundles with real-time price accumulation via client-side JavaScript.
* **Smart AJAX Recommendations:** Fetches personalized item grids asynchronously (`wp_remote_post`) to bypass page caching mechanisms.
* **Decoupled Server Load:** Migrates heavy collaborative filtering operations entirely to a cloud infrastructure.
* **Admin Monetization Hook (Freemium Boilerplate):** Includes a beautifully designed admin dashboard featuring secure settings validation, connection status indicators, and an embedded blur-teaser layout designed to drive premium PRO conversions.

## 📂 Repository Structure
```text
├── sellora-ai-recommendations.php  # Main plugin bootstrap, API router, and AJAX handlers
├── readme.txt                      # Official WordPress.org Repository metadata specification
└── assets/
    ├── recommender.js              # Asynchronous fetch logic and dynamic bundle price calculator
    ├── style.css                   # Responsive frontend CSS for FBT galleries and checkboxes
    └── admin-style.css             # Modern Admin Dashboard UI styling with premium upsell teasers
```

## 💻 Installation & Setup

1. **Clone the repository into your WordPress local development plugins directory:**
```bash
cd wp-content/plugins/
git clone [https://github.com/yourusername/sellora-ai-recommendations.git](https://github.com/yourusername/sellora-ai-recommendations.git)
```

2. **Activate the plugin:**
Go to the WordPress Admin Dashboard -> **Plugins** -> **Installed Plugins** and click **Activate** under **Sellora AI Recommendations**.

3. **Configure API Credentials:**
Navigate to the newly created **Sellora AI** sidebar menu, enter your `Store ID` and `API Key` connected to the cloud engine, and save changes.

## 🔒 Security & Optimization Best Practices Implemented
* **CSRF Mitigation:** Strict token validation on the custom multi-cart insertion endpoint (`sellora_add_all`).
* **Data Sanitization:** Strict usage of `sanitize_text_field()`, `absint()`, and `esc_html()` across all configuration forms to prevent XSS.
* **Zero Latency UI:** Employs CSS-driven skeleton preparation (`display:none` wrappers) that only transition into view once AJAX responses successfully complete, eliminating page layout shifts (CLS).
