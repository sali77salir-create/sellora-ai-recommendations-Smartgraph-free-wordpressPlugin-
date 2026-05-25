<?php
/**
 * Plugin Name:       Sellora AI Recommendations
 * Plugin URI:        https://smartgraph.online/sellora-ai
 * Description:       Turn your WooCommerce store into a revenue engine with intelligent AI recommendations and cross-sells.
 * Version:           1.0.0
 * Author:            Salman Rafiei
 * Author URI:        https://smartgraph.online
 * License:           GPLv2 or later
 * Text Domain:       sellora-ai-recommendations
 * Tested up to:      7.0
 */

if ( ! defined( 'ABSPATH' ) ) exit;

define( 'SELLORA_VERSION', '1.0.4' );
define( 'SELLORA_API_URL', 'https://sgr-backend-demo.onrender.com/api/v1' );

add_action( 'admin_notices', function() {
    if ( ! in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins', array() ) ) ) ) {
        echo wp_kses_post('<div class="notice notice-error"><p>' . sprintf( __( '<strong>Sellora AI</strong> requires WooCommerce to be active.', 'sellora-ai-recommendations' ) ) . '</p></div>');
    }
});

register_activation_hook( __FILE__, function() {
    add_option( 'sellora_store_id', '' );
    add_option( 'sellora_api_key', '' );
});

add_action( 'admin_enqueue_scripts', function( $hook ) {
    if ( $hook !== 'toplevel_page_sellora-recommender' ) return;
    wp_enqueue_style( 'sellora-admin-style', plugin_dir_url( __FILE__ ) . 'assets/admin-style.css', [], SELLORA_VERSION );
});

add_action( 'admin_menu', function() {
    add_menu_page( 'Sellora AI', 'Sellora AI', 'manage_options', 'sellora-recommender', 'sellora_render_admin_page', 'dashicons-networking', 56 );
});

function sellora_render_admin_page() {
    if ( isset($_POST['sellora_save']) ) {
        if ( isset($_POST['sellora_settings_nonce']) && wp_verify_nonce( sanitize_key( $_POST['sellora_settings_nonce'] ), 'sellora_save_action' ) ) {
            
            update_option('sellora_store_id', isset($_POST['sid']) ? sanitize_text_field(wp_unslash($_POST['sid'])) : '');
            update_option('sellora_api_key', isset($_POST['skey']) ? sanitize_text_field(wp_unslash($_POST['skey'])) : '');
            update_option('sellora_txt_personal', isset($_POST['txt_personal']) ? sanitize_text_field(wp_unslash($_POST['txt_personal'])) : '');
            update_option('sellora_txt_fbt', isset($_POST['txt_fbt']) ? sanitize_text_field(wp_unslash($_POST['txt_fbt'])) : '');
            update_option('sellora_txt_add_all', isset($_POST['txt_add_all']) ? sanitize_text_field(wp_unslash($_POST['txt_add_all'])) : '');
            
            echo '<div class="updated notice is-dismissible"><p>' . esc_html__( 'Settings saved successfully.', 'sellora-ai-recommendations' ) . '</p></div>';
        } else {
            echo '<div class="error notice is-dismissible"><p>' . esc_html__( 'Security check failed. Please refresh and try again.', 'sellora-ai-recommendations' ) . '</p></div>';
        }
    }

    $sid = get_option('sellora_store_id');
    $key = get_option('sellora_api_key');
    $is_active = !empty($sid) && !empty($key);
    ?>
    <div class="wrap sellora-admin-wrap">
        <div class="sellora-header">
            <h1><?php esc_html_e( 'Sellora', 'sellora-ai-recommendations' ); ?> <span style="color:#4f46e5"><?php esc_html_e( 'AI', 'sellora-ai-recommendations' ); ?></span> <?php esc_html_e( 'Recommendations', 'sellora-ai-recommendations' ); ?></h1>
            <div class="status-badge <?php echo $is_active ? 'online' : 'offline'; ?>">
                <?php echo $is_active ? esc_html__( '● Cloud AI Active', 'sellora-ai-recommendations' ) : esc_html__( '○ Local Mode (Waiting for API)', 'sellora-ai-recommendations' ); ?>
            </div>
        </div>

        <div class="sellora-main-grid">
            <div class="sellora-content-col">
                <form method="post">
                    <?php wp_nonce_field('sellora_save_action', 'sellora_settings_nonce'); ?>
                    
                    <div class="sellora-card">
                        <h2>🤖 <?php esc_html_e( 'Connection Settings', 'sellora-ai-recommendations' ); ?></h2>
                        <p style="color:#666; margin-bottom: 15px;"><?php esc_html_e( 'Enter your API key to activate the cloud AI engine. Leave blank to use standard local fallback recommendations.', 'sellora-ai-recommendations' ); ?></p>
                        <p><strong><?php esc_html_e( 'Store ID:', 'sellora-ai-recommendations' ); ?></strong><br><input type="text" name="sid" value="<?php echo esc_attr($sid); ?>" placeholder="e.g. mystore_123" class="regular-text"></p>
                        <p><strong><?php esc_html_e( 'API Key:', 'sellora-ai-recommendations' ); ?></strong><br><input type="password" name="skey" value="<?php echo esc_attr($key); ?>" placeholder="••••••••" class="regular-text"></p>
                    </div>

                    <div class="sellora-card">
                        <h2>🎨 <?php esc_html_e( 'Display Settings', 'sellora-ai-recommendations' ); ?></h2>
                        <p style="color:#666; margin-bottom: 15px;"><?php esc_html_e( 'Customize the text displayed to your customers.', 'sellora-ai-recommendations' ); ?></p>

                        <div style="margin-bottom: 15px;">
                            <strong><?php esc_html_e( 'Recommendations Section Title:', 'sellora-ai-recommendations' ); ?></strong><br>
                            <input type="text" name="txt_personal" value="<?php echo esc_attr(get_option('sellora_txt_personal', 'Recommended for You')); ?>" class="regular-text">
                        </div>
                        
                        <div style="margin-bottom: 15px;">
                            <strong><?php esc_html_e( 'FBT Box Title:', 'sellora-ai-recommendations' ); ?></strong><br>
                            <input type="text" name="txt_fbt" value="<?php echo esc_attr(get_option('sellora_txt_fbt', 'Frequently Bought Together')); ?>" class="regular-text">
                        </div>
                        
                        <div style="margin-bottom: 15px;">
                            <strong><?php esc_html_e( 'Add to Cart Button Text:', 'sellora-ai-recommendations' ); ?></strong><br>
                            <input type="text" name="txt_add_all" value="<?php echo esc_attr(get_option('sellora_txt_add_all', 'Add Bundle to Cart')); ?>" class="regular-text">
                        </div>
                    </div>

                    <p><input type="submit" name="sellora_save" class="button button-primary button-large" value="<?php esc_attr_e( 'Save All Settings', 'sellora-ai-recommendations' ); ?>"></p>
                </form>

                <div class="sellora-card">
                    <h2 style="display:flex; justify-content:space-between"><?php esc_html_e( 'Revenue Analytics', 'sellora-ai-recommendations' ); ?> <span class="badge">PRO</span></h2>
                    <p style="color:#666"><?php esc_html_e( 'Track ROI and AI performance in real-time.', 'sellora-ai-recommendations' ); ?></p>
                    <div class="blur-teaser">
                        <p><?php esc_html_e( 'Upgrade to Pro to unlock advanced analytics and visual charts.', 'sellora-ai-recommendations' ); ?></p>
                        <a href="https://sali77.gumroad.com/l/smartgraph-ltd" class="button" target="_blank"><?php esc_html_e( 'Unlock Now', 'sellora-ai-recommendations' ); ?></a>
                    </div>
                </div>
            </div>

            <div class="sellora-sidebar-col">
                <div class="sellora-card sidebar-pro">
                    <h3>🚀 <?php esc_html_e( 'Upgrade to Pro', 'sellora-ai-recommendations' ); ?></h3>
                    <p><?php esc_html_e( 'Unlock the full power of Artificial Intelligence to boost your sales by up to 30%.', 'sellora-ai-recommendations' ); ?></p>
                    <ul class="feat-list">
                        <li>✅ <?php esc_html_e( 'Advanced Cloud AI Algorithms', 'sellora-ai-recommendations' ); ?></li>
                        <li>✅ <?php esc_html_e( 'Multi-Strategy FBT Bundles', 'sellora-ai-recommendations' ); ?></li>
                        <li>✅ <?php esc_html_e( 'Smart FOMO Toasts (Live Popups)', 'sellora-ai-recommendations' ); ?></li>
                        <li>✅ <?php esc_html_e( 'Advanced Revenue Analytics', 'sellora-ai-recommendations' ); ?></li>
                        <li>✅ <?php esc_html_e( 'Priority VIP Support', 'sellora-ai-recommendations' ); ?></li>
                    </ul>
                    <a href="https://sali77.gumroad.com/l/smartgraph-ltd" target="_blank" class="pro-btn"><?php esc_html_e( 'Get Lifetime Deal', 'sellora-ai-recommendations' ); ?></a>
                </div>

                <div class="sellora-card" style="margin-top: 20px;">
                    <h3><?php esc_html_e( 'Need Help?', 'sellora-ai-recommendations' ); ?></h3>
                    <p><?php esc_html_e( 'Check our documentation or contact support.', 'sellora-ai-recommendations' ); ?></p>
                    <ul style="margin: 0; padding-left: 15px; font-size: 13px;">
                        <li><a href="https://smartgraph.online/docs" target="_blank"><?php esc_html_e( 'Documentation', 'sellora-ai-recommendations' ); ?></a></li>
                        <li><a href="https://smartgraph.online/support" target="_blank"><?php esc_html_e( 'Open a Ticket', 'sellora-ai-recommendations' ); ?></a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <?php
}

add_action( 'wp_ajax_sellora_get_recs', 'sellora_handle_ajax_recs' );
add_action( 'wp_ajax_nopriv_sellora_get_recs', 'sellora_handle_ajax_recs' );

function sellora_handle_ajax_recs() {
    check_ajax_referer( 'sellora_ajax_action', 'nonce' );

    $pid = isset($_POST['product_id']) ? absint(wp_unslash($_POST['product_id'])) : 0;
    $sid = get_option('sellora_store_id');
    $key = get_option('sellora_api_key');
    
    $response = null;
    if ( !empty($sid) && !empty($key) ) {
        $response = wp_remote_post( SELLORA_API_URL . '/recommend', [
            'method' => 'POST', 'timeout' => 10,
            'headers' => ['Content-Type' => 'application/json', 'X-API-Key' => $key],
            'body' => wp_json_encode(['store_id' => $sid, 'target_product_id' => (string)$pid, 'limit' => 4])
        ]);
    }
    
    $data = (!is_wp_error($response) && !empty($response)) ? json_decode(wp_remote_retrieve_body($response), true) : [];
    $ids = [];
    if(isset($data['recommendations'])) {
        foreach($data['recommendations'] as $r) { if((int)$r['product_id'] !== $pid) $ids[] = (int)$r['product_id']; }
    }
    
    if(count($ids) < 4) {
        $related = wc_get_related_products($pid, 10);
        foreach($related as $rid) { if(!in_array($rid, $ids) && count($ids) < 4) $ids[] = $rid; }
    }

    $fbt_data = [];
    $main = wc_get_product($pid);
    if($main) {
        $fbt_data[] = [
            'id' => $main->get_id(), 
            'title' => $main->get_name(), 
            'price' => (float)wc_get_price_to_display($main), 
            'price_html' => $main->get_price_html(), 
            'image' => get_the_post_thumbnail_url($main->get_id(), 'thumbnail'), 
            'permalink' => $main->get_permalink(), 
            'is_main' => true
        ];
        foreach(array_slice($ids, 0, 2) as $fid) {
            $p = wc_get_product($fid);
            if($p && $p->is_in_stock()) {
                $fbt_data[] = [
                    'id' => $p->get_id(), 
                    'title' => $p->get_name(), 
                    'price' => (float)wc_get_price_to_display($p), 
                    'price_html' => $p->get_price_html(), 
                    'image' => get_the_post_thumbnail_url($p->get_id(), 'thumbnail'), 
                    'permalink' => $p->get_permalink(), 
                    'is_main' => false
                ];
            }
        }
    }

    ob_start();
    foreach($ids as $id) {
        $post_obj = get_post($id);
        if($post_obj) { 
            $GLOBALS['post'] =& $post_obj; 
            setup_postdata($GLOBALS['post']); 
            wc_get_template_part('content', 'product'); 
        }
    }
    wp_reset_postdata();
    
    wp_send_json_success(['html' => ob_get_clean(), 'fbt_data' => $fbt_data]);
}

add_action( 'wp_enqueue_scripts', function() {
    wp_enqueue_style( 'sellora-style', plugin_dir_url(__FILE__) . 'assets/style.css', [], SELLORA_VERSION );
    wp_enqueue_script( 'sellora-js', plugin_dir_url(__FILE__) . 'assets/recommender.js', ['jquery'], SELLORA_VERSION, true );
    
    global $post;
    
    wp_localize_script( 'sellora-js', 'sellora_obj', [
        'ajax_url'   => admin_url('admin-ajax.php'),
        'nonce'      => wp_create_nonce('sellora_ajax_action'),
        'product_id' => is_product() ? $post->ID : 0,
        'currency'   => get_woocommerce_currency_symbol(),
        'i18n'       => [
            't_fbt' => get_option('sellora_txt_fbt', 'Frequently Bought Together'), 
            'b_add' => get_option('sellora_txt_add_all', 'Add Bundle to Cart')
        ]
    ]);
});

add_action( 'woocommerce_after_add_to_cart_form', function() {
    echo '<div id="sellora-fbt-wrapper" class="sellora-fbt-container" style="display:none;"></div>';
}, 20 );

add_action( 'wp', function() {
    remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );
}, 999 );

add_action( 'woocommerce_after_single_product_summary', function() {
    $grid_title = get_option('sellora_txt_personal', 'Recommended for You');
    echo '<section id="sellora-grid-container" class="related products sellora-recommendation-wrapper" style="display:none; margin-top:40px; border-top:1px solid #eee; padding-top:20px;">';
    echo '<h2 id="sellora-grid-title">' . esc_html($grid_title) . '</h2>';
    echo '<div id="sellora-grid-wrapper"></div>';
    echo '</section>';
}, 25 );
add_action( 'wp_ajax_sellora_add_all', 'sellora_add_all_cb' );
add_action( 'wp_ajax_nopriv_sellora_add_all', 'sellora_add_all_cb' );
function sellora_add_all_cb() {
    check_ajax_referer( 'sellora_ajax_action', 'nonce' );

    if ( isset($_POST['product_ids']) && is_array($_POST['product_ids']) ) {
        $product_ids = array_map( 'absint', wp_unslash( $_POST['product_ids'] ) );
        
        foreach( $product_ids as $pid ) { 
            if( $pid > 0 ) {
                WC()->cart->add_to_cart( $pid ); 
            }
        }
    }
    wp_send_json_success();
}