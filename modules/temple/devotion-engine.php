<?php
/**
 * Devotion Engine: Pooja, Darshan & Spiritual Experience
 */
if (!defined('ABSPATH')) exit;

class SH_KG_Devotion_Engine {

    public function __construct() {
        add_action('add_meta_boxes', [$this, 'meta_box']);
        add_action('save_post', [$this, 'save_meta']);
        add_filter('the_content', [$this, 'render_devotion_block'], 25);
        add_filter('sh_kg_schema_temple', [$this, 'extend_schema'], 10, 2);
    }

    /**
     * Admin: Add Devotion fields for Temples
     */
    public function meta_box() {
        add_meta_box(
            'sh_devotion_box',
            __('Devotion Details', 'shrilahari-kg'),
            [$this, 'render_box'],
            'sh_temple', 'normal', 'high'
        );
    }

    public function render_box($post) {

        wp_nonce_field('sh_devotion_save', 'sh_devotion_nonce');

        $pooja  = get_post_meta($post->ID, '_sh_temple_pooja', true);
        $queue  = get_post_meta($post->ID, '_sh_temple_queue', true);
        $fest   = get_post_meta($post->ID, '_sh_temple_festivals', true);

        ?>
        <style>.sh-field input, .sh-field textarea { width: 100%; }</style>

        <div class="sh-field">
            <label><strong>Pooja / Seva Details</strong></label>
            <textarea name="sh_temple_pooja" rows="4"><?php echo esc_textarea($pooja); ?></textarea>
        </div>

        <div class="sh-field">
            <label><strong>Darshan / Queue Info</strong></label>
            <input type="text" name="sh_temple_queue" value="<?php echo esc_attr($queue); ?>">
            <small>(Example: "Peak on Weekends", "Quick Darshan in Morning")</small>
        </div>

        <div class="sh-field">
            <label><strong>Associated Festivals</strong></label>
            <textarea name="sh_temple_festivals" rows="3"><?php echo esc_textarea($fest); ?></textarea>
        </div>
        <?php
    }

    public function save_meta($post_id) {

        if (!isset($_POST['sh_devotion_nonce']) ||
            !wp_verify_nonce($_POST['sh_devotion_nonce'], 'sh_devotion_save')) return;

        $fields = ['pooja','queue','festivals'];

        foreach ($fields as $key) {
            if (isset($_POST['sh_temple_' . $key])) {
                update_post_meta(
                    $post_id,
                    '_sh_temple_' . $key,
                    sanitize_textarea_field($_POST['sh_temple_' . $key])
                );
            }
        }
    }

    /**
     * Frontend Output — Devotion Block Section
     */
    public function render_devotion_block($content) {

        if (!is_singular('sh_temple')) return $content;
        global $post;

        $pooja = get_post_meta($post->ID, '_sh_temple_pooja', true);
        $queue = get_post_meta($post->ID, '_sh_temple_queue', true);
        $fest  = get_post_meta($post->ID, '_sh_temple_festivals', true);

        if (!$pooja && !$queue && !$fest) return $content;

        ob_start(); ?>
        <div class="sh-devotion-box">
            <h3>Devotion Experience</h3>
            <ul>
                <?php if($pooja): ?><li><strong>Seva / Pooja:</strong><br><?php echo nl2br($pooja); ?></li><?php endif; ?>
                <?php if($queue): ?><li><strong>Darshan Queue:</strong><br><?php echo esc_html($queue); ?></li><?php endif; ?>
                <?php if($fest): ?><li><strong>Main Festivals:</strong><br><?php echo nl2br($fest); ?></li><?php endif; ?>
            </ul>
        </div>
        <?php
        return $content . ob_get_clean();
    }

    /**
     * Schema enrichment
     */
    public function extend_schema($schema, $post_id) {

        $pooja = get_post_meta($post_id, '_sh_temple_pooja', true);
        $queue = get_post_meta($post_id, '_sh_temple_queue', true);
        $fest  = get_post_meta($post_id, '_sh_temple_festivals', true);

        if ($pooja) $schema['additionalProperty'][] = ['name'=>'Pooja Details','value'=>wp_strip_all_tags($pooja)];
        if ($queue) $schema['additionalProperty'][] = ['name'=>'Darshan Queue','value'=>wp_strip_all_tags($queue)];
        if ($fest)  $schema['event'] = [
            '@type' => 'Event',
            'name' => wp_strip_all_tags($fest)
        ];

        return $schema;
    }

}

new SH_KG_Devotion_Engine();
