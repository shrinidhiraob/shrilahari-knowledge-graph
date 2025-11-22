<?php
/**
 * Temple Rating Engine
 * 1–5 Star Ratings + AggregateRating Schema
 */
if (!defined('ABSPATH')) exit;

class SH_KG_Rating_Engine {

    public function __construct() {
        add_action('wp_enqueue_scripts', [$this, 'scripts']);
        add_filter('the_content', [$this, 'rating_box'], 60);
        add_action('wp_ajax_sh_rate', [$this, 'save_rating']);
        add_action('wp_ajax_nopriv_sh_rate', [$this, 'save_rating']);
        add_filter('sh_kg_schema_temple', [$this, 'extend_schema'], 10, 2);
    }

    public function scripts() {
        wp_enqueue_script(
            'sh-rating-js',
            SH_KG_PLUGIN_URL . 'assets/js/rating.js',
            ['jquery'],
            null,
            true
        );
        wp_localize_script('sh-rating-js','sh_rating_vars',[
            'ajax_url'=>admin_url('admin-ajax.php'),
            'nonce'=>wp_create_nonce('sh_rate_nonce')
        ]);
        wp_enqueue_style('sh-rating-css', SH_KG_PLUGIN_URL . 'assets/css/style.css');
    }

    /**
     * UI Output
     */
    public function rating_box($content) {
        if (!is_singular('sh_temple')) return $content;

        global $post;

        $avg = get_post_meta($post->ID, '_sh_rating_avg', true) ?: 0;
        $count = get_post_meta($post->ID, '_sh_rating_count', true) ?: 0;

        ob_start(); ?>
        <div class="sh-rating-box">
            <h3>Rate This Temple</h3>
            <div class="sh-stars" data-post="<?= $post->ID ?>">
                <?php for($i=1;$i<=5;$i++): ?>
                    <span class="sh-star" data-value="<?= $i ?>">⭐</span>
                <?php endfor; ?>
            </div>
            <p class="sh-rating-status">
                Current Rating: <strong><?= $avg ?> / 5</strong>
                (<?= $count ?> Votes)
            </p>

            <input type="email" id="sh_rating_email" placeholder="Enter Email to Verify" required>
            <button id="sh_rating_submit">Submit Rating</button>
        </div>
        <?php
        return $content . ob_get_clean();
    }

    /**
     * Store Rating
     */
    public function save_rating() {
        check_ajax_referer('sh_rate_nonce','nonce');

        $post_id = intval($_POST['post_id']);
        $rating  = intval($_POST['rating']);
        $email   = sanitize_email($_POST['email']);

        if (!$post_id || !$rating || !$email) wp_send_json_error('Invalid');

        // Prevent duplicate email ratings
        $voters = get_post_meta($post_id, '_sh_rating_voters', true) ?: [];
        if (in_array($email, $voters)) {
            wp_send_json_error('You have already rated');
        }

        $count = get_post_meta($post_id,'_sh_rating_count',true) ?: 0;
        $total = get_post_meta($post_id,'_sh_rating_total',true) ?: 0;

        $count++;
        $total += $rating;
        $avg = round($total / $count, 2);

        // Save
        update_post_meta($post_id,'_sh_rating_count',$count);
        update_post_meta($post_id,'_sh_rating_total',$total);
        update_post_meta($post_id,'_sh_rating_avg',$avg);

        $voters[] = $email;
        update_post_meta($post_id,'_sh_rating_voters',$voters);

        wp_send_json_success([
            'avg'=>$avg,
            'count'=>$count
        ]);
    }

    /**
     * Schema Aggregation
     */
    public function extend_schema($schema, $post_id) {
        $avg = get_post_meta($post_id,'_sh_rating_avg',true);
        $count = get_post_meta($post_id,'_sh_rating_count',true);

        if ($count && $avg) {
            $schema['aggregateRating'] = [
                '@type' => 'AggregateRating',
                'ratingValue' => $avg,
                'ratingCount' => $count
            ];
        }
        return $schema;
    }
}

new SH_KG_Rating_Engine();
