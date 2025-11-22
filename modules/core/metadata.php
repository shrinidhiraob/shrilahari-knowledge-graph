<?php
/**
 * Universal Metadata System
 * Stores GPS + future entity details
 */
if (!defined('ABSPATH')) exit;

class SH_KG_Metadata {

    public function __construct() {
        add_action('add_meta_boxes', [$this, 'add_meta_box']);
        add_action('save_post', [$this, 'save_meta']);
    }

    /**
     * Add Unified Meta Panel for All Entity Types
     */
    public function add_meta_box() {

        $screens = [
            'sh_temple',
            'sh_place',
            'sh_food',
            'sh_event',
            'sh_restaurant',
            'sh_transport'
        ];

        foreach ($screens as $screen) {
            add_meta_box(
                'sh_kg_meta_panel',
                __('Shrilahari Knowledge Graph Details', 'shrilahari-kg'),
                [$this, 'render_meta_box'],
                $screen,
                'normal',
                'high'
            );
        }
    }

    /**
     * Admin UI for Metadata
     */
    public function render_meta_box($post) {

        wp_nonce_field('sh_kg_save_meta', 'sh_kg_meta_nonce');

        $lat = get_post_meta($post->ID, '_sh_kg_lat', true);
        $lng = get_post_meta($post->ID, '_sh_kg_lng', true);
        ?>

        <style>
            .sh-kg-meta input {
                width: 200px;
                margin-bottom: 5px;
            }
            .sh-kg-group {
                margin-bottom: 15px;
                padding: 10px;
                border: 1px solid #ddd;
                background: #fafafa;
            }
        </style>

        <div class="sh-kg-meta">

            <div class="sh-kg-group">
                <h4><?php _e('Location Details', 'shrilahari-kg'); ?></h4>

                <label><strong>Latitude:</strong></label><br>
                <input type="text" name="sh_kg_lat" value="<?php echo esc_attr($lat); ?>"><br>

                <label><strong>Longitude:</strong></label><br>
                <input type="text" name="sh_kg_lng" value="<?php echo esc_attr($lng); ?>">
                <p><em>Use Google Maps or Map Picker (coming soon) to get coordinates</em></p>
            </div>

        </div>

        <?php
    }

    /**
     * Save metadata securely
     */
    public function save_meta($post_id) {

        if (!isset($_POST['sh_kg_meta_nonce']) ||
            !wp_verify_nonce($_POST['sh_kg_meta_nonce'], 'sh_kg_save_meta')) {
            return;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;

        if (isset($_POST['sh_kg_lat']))
            update_post_meta($post_id, '_sh_kg_lat', sanitize_text_field($_POST['sh_kg_lat']));

        if (isset($_POST['sh_kg_lng']))
            update_post_meta($post_id, '_sh_kg_lng', sanitize_text_field($_POST['sh_kg_lng']));
    }
}

new SH_KG_Metadata();
