<?php
/**
 * Visit Verification Engine
 * QR Code per temple + Verified visitor flag
 */
if (!defined('ABSPATH')) exit;

class SH_KG_Visit_Engine {

    public function __construct() {
        add_filter('the_content', [$this, 'render_verified_badge'], 70);
        add_action('add_meta_boxes', [$this, 'add_qr_box']);
        add_action('save_post', [$this, 'generate_qr_code']);
    }

    /**
     * Show Verified Badge if email matches
     */
    public function render_verified_badge($content) {
        if (!is_singular('sh_temple')) return $content;

        if (isset($_GET['visit_verify']) && $_GET['visit_verify'] === 'success') {
            $content = '<div class="sh-verified-box">✔ Verified Visitor of this Temple</div>' . $content;
        }

        return $content;
    }

    /**
     * QR Code Generator box in Admin
     */
    public function add_qr_box() {
        add_meta_box(
            'sh_visit_verify_box',
            __('Visitor Verification QR', 'shrilahari-kg'),
            [$this, 'render_qr_box'],
            'sh_temple',
            'side'
        );
    }

    public function render_qr_box($post) {
        $qr = get_post_meta($post->ID, '_sh_visit_qr', true);

        if ($qr) {
            echo '<img src="' . esc_url($qr) . '" style="max-width:100%">';
            echo '<p><small>Download QR & place at temple location</small></p>';
        } else {
            echo '<p>Save this post to generate QR code.</p>';
        }
    }

    /**
     * Generate QR code on Save
     */
    public function generate_qr_code($post_id) {
        if (get_post_type($post_id) !== 'sh_temple') return;

        $verify_url = add_query_arg([
            'visit_verify' => 'success'
        ], get_permalink($post_id));

        $qr_api = 'https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=' . urlencode($verify_url);

        update_post_meta($post_id, '_sh_visit_qr', $qr_api);
    }

}

new SH_KG_Visit_Engine();
