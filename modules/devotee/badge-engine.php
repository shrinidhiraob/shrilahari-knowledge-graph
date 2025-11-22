<?php
/**
 * Badge System Engine
 * Awards visual achievements based on verified temple visits
 */
if (!defined('ABSPATH')) exit;

class SH_KG_Badge_Engine {

    public function __construct() {
        add_action('sh_user_verified_visit', [$this, 'assign_badge'], 15, 2);
        add_filter('sh_kg_devotee_info', [$this, 'add_badge_to_profile'], 10, 2);
    }

    /**
     * Execute when new temple is verified
     */
    public function assign_badge($user_id, $post_id) {

        $visits = count(get_user_meta($user_id, '_sh_passport', true) ?: []);
        $prev_badge = get_user_meta($user_id, '_sh_badge_level', true);

        $badge = $this->get_badge($visits);

        if ($badge !== $prev_badge) {
            update_user_meta($user_id, '_sh_badge_level', $badge);
        }
    }

    /**
     * Decide badge level by visit count
     */
    private function get_badge($visits) {
        if ($visits >= 100) return 'diamond';
        if ($visits >= 50) return 'gold';
        if ($visits >= 20) return 'silver';
        if ($visits >= 5) return 'bronze';
        return 'new';
    }

    /**
     * Render badge icon for display
     */
    public static function badge_icon($uid) {
        $badge = get_user_meta($uid, '_sh_badge_level', true);

        $icons = [
            'diamond' => '💎 Diamond Devotee',
            'gold'    => '🏅 Gold Devotee',
            'silver'  => '⚪ Silver Devotee',
            'bronze'  => '🟤 Bronze Devotee',
            'new'     => '🙏 New Devotee'
        ];

        return $icons[$badge] ?? '🙏 New Devotee';
    }

    /**
     * Add badge to devotee profile info
     */
    public function add_badge_to_profile($info, $uid) {
        $info['badge'] = self::badge_icon($uid);
        return $info;
    }
}

new SH_KG_Badge_Engine();
