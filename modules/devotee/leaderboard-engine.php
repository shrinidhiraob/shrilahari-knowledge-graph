<?php
/**
 * Devotee Leaderboard Engine
 * Shows top devotees per temple & platform-wide
 */
if (!defined('ABSPATH')) exit;

class SH_KG_Leaderboard_Engine {

    public function __construct() {
        add_filter('the_content', [$this, 'render_leaderboards'], 90);
        add_shortcode('devotee_leaderboard', [$this, 'global_leaderboard_shortcode']);
    }

    /**
     * Show temple-specific leaderboard below Passport Box
     */
    public function render_leaderboards($content) {
        if (!is_singular('sh_temple')) return $content;

        global $post;

        $leaders = $this->get_temple_leaders($post->ID);

        if (empty($leaders)) return $content;

        ob_start(); ?>
        <div class="sh-leaderboard-box">
            <h3>🏆 Top Devotees of This Temple</h3>
            <ol class="sh-leaderboard-list">
                <?php foreach($leaders as $uid => $count): ?>
                    <li>
                        <?php echo $this->devotee_name($uid); ?>
                        <span class="count">(<?= $count ?> visits)</span>
                        <?= $this->badge_icon($count) ?>
                    </li>
                <?php endforeach; ?>
            </ol>
        </div>
        <?php

        return $content . ob_get_clean();
    }


    /**
     * Get top devotees who verified this temple via QR
     */
    private function get_temple_leaders($post_id) {
        $users = get_users([
            'meta_key' => '_sh_passport',
            'meta_compare' => 'EXISTS'
        ]);

        $ranking = [];
        foreach ($users as $u) {
            $visits = get_user_meta($u->ID, '_sh_passport', true) ?: [];
            if (in_array($post_id, $visits)) {
                $ranking[$u->ID] = count($visits);
            }
        }

        arsort($ranking);

        return array_slice($ranking, 0, 5, true); // Top 5
    }


    /**
     * Global Leaderboard Shortcode
     * [devotee_leaderboard limit="20"]
     */
    public function global_leaderboard_shortcode($atts) {
        $atts = shortcode_atts([
            'limit' => 20,
        ], $atts);

        $users = get_users([
            'meta_key' => '_sh_passport',
            'meta_compare' => 'EXISTS',
            'fields' => 'all'
        ]);

        $rank = [];
        foreach ($users as $u) {
            $count = count(get_user_meta($u->ID, '_sh_passport', true) ?: []);
            if ($count > 0) {
                $rank[$u->ID] = $count;
            }
        }

        arsort($rank);

        $leaders = array_slice($rank, 0, intval($atts['limit']), true);

        if (empty($leaders)) return '<p>No verified devotees yet.</p>';

        ob_start(); ?>
        <div class="sh-leaderboard-full">
            <h2>🏆 Devotee Leaderboard</h2>
            <ol>
                <?php foreach($leaders as $uid => $count): ?>
                    <li>
                        <?= $this->devotee_name($uid); ?>
                        <strong><?= $count ?></strong> temples
                        <?= $this->badge_icon($count) ?>
                    </li>
                <?php endforeach; ?>
            </ol>
        </div>
        <?php

        return ob_get_clean();
    }


    /** Utility functions */

    private function devotee_name($uid) {
        $user = get_user_by('id', $uid);
        return $user ? $user->display_name : 'Devotee';
    }

    private function badge_icon($count) {
        if ($count >= 100) return ' 💎';
        if ($count >= 50) return ' 🏅';
        if ($count >= 20) return ' ⚪';
        if ($count >= 5) return ' 🟤';
        return ' 🙏';
    }
}

new SH_KG_Leaderboard_Engine();
