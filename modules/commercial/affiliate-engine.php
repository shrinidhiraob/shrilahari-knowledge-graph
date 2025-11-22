<?php
/**
 * Affiliate Monetization Engine
 * Hotels & Stays near temples (Booking.com deep links)
 */
if (!defined('ABSPATH')) exit;

class SH_KG_Affiliate_Engine {

    private $affiliate_id = 'YOUR_BOOKING_AFFILIATE_ID'; // change later

    public function __construct() {
        add_filter('the_content', [$this, 'render_affiliate_hotels'], 30);
        add_action('admin_menu', [$this, 'settings_menu']);
        add_action('admin_init', [$this, 'register_settings']);
    }

    /**
     * Admin settings for adding affiliate ID
     */
    public function settings_menu() {
        add_options_page(
            'Affiliate Settings',
            'Affiliate Settings',
            'manage_options',
            'sh-affiliate-settings',
            [$this, 'settings_page']
        );
    }

    public function register_settings() {
        register_setting('sh_affiliate', 'sh_affiliate_booking_id');
    }

    public function settings_page() {
        ?>
        <div class="wrap">
            <h2>Affiliate Settings</h2>
            <form method="post" action="options.php">
                <?php settings_fields('sh_affiliate'); ?>
                <?php do_settings_sections('sh_affiliate'); ?>
                
                <label>Booking.com Affiliate ID</label><br>
                <input type="text" name="sh_affiliate_booking_id"
                       value="<?php echo esc_attr(get_option('sh_affiliate_booking_id')); ?>"
                       style="width:300px;"><br><br>

                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }

    /**
     * Frontend Affiliate hotel cards
     */
    public function render_affiliate_hotels($content) {
        if (!is_singular(['sh_temple','sh_place'])) return $content;

        global $post;

        $lat = get_post_meta($post->ID, '_sh_kg_lat', true);
        $lng = get_post_meta($post->ID, '_sh_kg_lng', true);

        if (!$lat || !$lng) return $content;

        $aff_id = get_option('sh_affiliate_booking_id');

        // Booking.com deep link builder
        $base_url = "https://www.booking.com/search.html?";
        $params = http_build_query([
            'aid' => $aff_id,
            'latitude' => $lat,
            'longitude' => $lng,
            'label' => sanitize_title(get_the_title($post->ID))
        ]);
        $url = $base_url . $params;

        ob_start();
        ?>
        <div class="sh-affiliate-box">
            <h3>Hotels & Stays Nearby</h3>
            <p>Find comfortable stays near <?php echo get_the_title($post->ID); ?>.</p>

            <a class="sh-aff-btn" href="<?php echo esc_url($url); ?>" target="_blank">
                🔍 View Hotels Near This Temple
            </a>

            <p style="font-size:12px; opacity:0.7;">
                *Affiliate link — supports our website 🙏
            </p>
        </div>
        <?php

        return $content . ob_get_clean();
    }
}

new SH_KG_Affiliate_Engine();
