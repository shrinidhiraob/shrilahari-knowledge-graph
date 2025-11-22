<?php
/**
 * Temple Specific Metadata Fields
 */
if (!defined('ABSPATH')) exit;

class SH_KG_Temple_Metadata {

    public function __construct() {
        add_action('add_meta_boxes', [$this, 'add_box']);
        add_action('save_post', [$this, 'save_data']);
    }

    public function add_box() {

        add_meta_box(
            'sh_temple_info_box',
            __('Temple Information', 'shrilahari-kg'),
            [$this, 'render_box'],
            'sh_temple',
            'normal',
            'high'
        );
    }

    public function render_box($post) {

        wp_nonce_field('sh_temple_meta_save', 'sh_temple_meta_nonce');

        $fields = [
            'deity'         => __('Main Deity', 'shrilahari-kg'),
            'sampradaya'    => __('Sampradaya / Matha', 'shrilahari-kg'),
            'type'          => __('Temple Type', 'shrilahari-kg'),
            'architecture'  => __('Architecture Style', 'shrilahari-kg'),
            'year'          => __('Year Established', 'shrilahari-kg'),
            'timings'       => __('Opening Hours', 'shrilahari-kg'),
            'phone'         => __('Contact Phone', 'shrilahari-kg'),
            'website'       => __('Official Website', 'shrilahari-kg'),
            'dress_code'    => __('Dress Code', 'shrilahari-kg'),
            'prasadam'      => __('Prasadam Availability', 'shrilahari-kg'),
            'rating'        => __('Temple Rating (1-5)', 'shrilahari-kg')
        ];

        echo '<table class="form-table">';

        foreach ($fields as $key => $label) {
            $value = get_post_meta($post->ID, '_sh_temple_' . $key, true);

            echo '<tr><th><label>' . $label . '</label></th><td>';
            echo '<input type="text" name="sh_temple_' . $key . '" value="' . esc_attr($value) . '" style="width:300px;">';
            echo '</td></tr>';
        }

        echo '</table>';
    }

    public function save_data($post_id) {

        if (!isset($_POST['sh_temple_meta_nonce']) ||
            !wp_verify_nonce($_POST['sh_temple_meta_nonce'], 'sh_temple_meta_save')) {
            return;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;

        $fields = ['deity','sampradaya','type','architecture','year',
                   'timings','phone','website','dress_code','prasadam','rating'];

        foreach ($fields as $field) {
            if (isset($_POST['sh_temple_' . $field])) {
                update_post_meta(
                    $post_id,
                    '_sh_temple_' . $field,
                    sanitize_text_field($_POST['sh_temple_' . $field])
                );
            }
        }
    }
}

new SH_KG_Temple_Metadata();
