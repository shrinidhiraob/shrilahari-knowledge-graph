<?php
/**
 * Devotee Passport Engine
 * Tracks verified visits + auto badge level
 */
if (!defined('ABSPATH')) exit;

class SH_KG_Passport_Engine {

    public function __construct() {
        add_filter('the_content', [$this, 'passport_box'], 80);
        add_action('sh_user_verified_visit', [$this, 'add_passport_stamp'], 10, 2);
    }

    /**
     * Add a Devotee Passport box on Temple page
     */
    public function passport_box($content) {
        if (!is_singular('sh_temple')) return $content;

        // Only for logged-in users (guest sees login CTA)
        if (!is_user_logged_in()) {
            return $content . $this->show_login_cta();
        }

        $user_id = get_current_user_id();
        $visited = $this->get_user_visits($user_id);
        $count = count($visited);

        $level = $this->get_badge_level($count);

        ob_start(); ?>
        <div class="sh-passport-box">
            <h3>🛕 Your Devotee Passport</h3>
            <p><strong>Temples Visited:</strong> <?= $count ?></p>
            <p><strong>Devotee Level:</strong> <?= $level ?></p>

            <?php if($count > 0): ?>
            <div class="sh-passport-stamps">
                <?php foreach($visited as $pid): ?>
                    <span class="stamp">💮</span>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
        <?php
        return $content . ob_get_clean();
    }


    /**
     * Login box for new devotees
     */
    private function show_login_cta() {
        return '
        <div class="sh-passport-box">
            <p><strong>Get your Devotee Passport!</strong></p>
            <button class="sh-login-btn" onclick="sh_open_login()">Login / Join Now</button>
            <script>
                function sh_open_login(){
                    alert(\"Login popup coming soon: Google + Email + Mobile OTP\");
                }
            </script>
        </div>';
    }


    /**
     * Called when QR visit verified successfully
     */
    public function add_passport_stamp($user_id, $post_id) {

        $visits = $this->get_user_visits($user_id);

        if (!in_array($post_id, $visits)) {
            $visits[] = $post_id;
            update_user_meta($user_id, '_sh_passport', $visits);
        }
    }


    /**
     * Fetch user passport visits
     */
    private function get_user_visits($uid) {
        return get_user_meta($uid, '_sh_passport', true) ?: [];
    }


    /**
     * Badge Level System
     */
    private function get_badge_level($count) {
        if ($count >= 100) return '💎 Diamond Devotee';
        if ($count >= 50) return '🏅 Gold Devotee';
        if ($count >= 20) return '⚪ Silver Devotee';
        if ($count >= 5) return '🟤 Bronze Devotee';
        return '🙏 New Devotee';
    }

}

new SH_KG_Passport_Engine();
