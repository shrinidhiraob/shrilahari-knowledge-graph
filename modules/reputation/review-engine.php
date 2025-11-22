<?php
/**
 * Review Engine - User Text + Photo Reviews
 * Anti-spam email validation (same as Rating Engine)
 */
if (!defined('ABSPATH')) exit;

class SH_KG_Review_Engine {

    public function __construct() {
        add_filter('the_content', [$this, 'review_box'], 65);
        add_action('wp_enqueue_scripts', [$this, 'scripts']);
        add_action('wp_ajax_sh_add_review', [$this, 'save_review']);
        add_action('wp_ajax_nopriv_sh_add_review', [$this, 'save_review']);
    }

    public function scripts() {
        wp_enqueue_script(
            'sh-review-js',
            SH_KG_PLUGIN_URL . 'assets/js/review.js',
            ['jquery'],
            null,
            true
        );
        wp_localize_script('sh-review-js','sh_review_vars',[
            'ajax_url'=>admin_url('admin-ajax.php'),
            'nonce'=>wp_create_nonce('sh_review_nonce')
        ]);
        wp_enqueue_style('sh-review-css', SH_KG_PLUGIN_URL . 'assets/css/style.css');
    }

    /**
     * Display review form + existing reviews
     */
    public function review_box($content) {
        if (!is_singular('sh_temple')) return $content;
        global $post;

        $reviews = get_post_meta($post->ID, '_sh_reviews', true) ?: [];

        ob_start(); ?>
        <div class="sh-review-box">
            <h3>Share Your Experience at This Temple</h3>

            <textarea id="sh_review_text" placeholder="Write your review…" rows="3"></textarea><br>
            <input type="email" id="sh_review_email" placeholder="Enter Email to verify"><br>
            <input type="file" id="sh_review_photo" accept="image/*"><br>

            <button id="sh_review_submit">Submit Review</button>
            <p id="sh_review_msg"></p>

            <?php if (!empty($reviews)): ?>
                <h4>Recent Reviews</h4>
                <ul class="sh-review-list">
                    <?php foreach (array_reverse($reviews) as $rev): ?>
                        <li>
                            <?php if(!empty($rev['photo'])): ?>
                                <img src="<?= esc_url($rev['photo']); ?>" class="sh-review-img">
                            <?php endif; ?>
                            <p><?= esc_html($rev['text']); ?></p>
                            <small>— Verified Visitor</small>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
        <?php

        return $content . ob_get_clean();
    }

    /**
     * Save review via AJAX
     */
    public function save_review() {
        check_ajax_referer('sh_review_nonce','nonce');

        $post_id = intval($_POST['post_id']);
        $email   = sanitize_email($_POST['email']);
        $text    = sanitize_text_field($_POST['text']);

        if (!$post_id || !$email || !$text) wp_send_json_error('Incomplete');

        $reviews = get_post_meta($post_id, '_sh_reviews', true) ?: [];
        $voters = get_post_meta($post_id, '_sh_review_voters', true) ?: [];

        if (in_array($email, $voters)) wp_send_json_error("Already submitted review");

        // Handle optional photo upload
        $photo_url = '';
        if (!empty($_FILES['photo'])) {
            $upload = wp_handle_upload($_FILES['photo'], ['test_form'=>false]);
            if (!isset($upload['error'])) {
                $photo_url = $upload['url'];
            }
        }

        $reviews[] = [
            'email'=>$email,
            'text'=>$text,
            'photo'=>$photo_url
        ];
        $voters[] = $email;

        update_post_meta($post_id, '_sh_reviews', $reviews);
        update_post_meta($post_id, '_sh_review_voters', $voters);

        wp_send_json_success('Thanks for sharing your experience!');
    }
}

new SH_KG_Review_Engine();
