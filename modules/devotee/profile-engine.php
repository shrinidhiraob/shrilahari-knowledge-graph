<?php
/**
 * Devotee Profile Engine
 * Public profile pages for verified devotees
 */
if (!defined('ABSPATH')) exit;

class SH_KG_Profile_Engine {

    public function __construct() {
        add_action('init', [$this, 'register_endpoint']);
        add_filter('query_vars', [$this, 'add_query_var']);
        add_action('template_redirect', [$this, 'profile_template']);
        add_filter('author_link', [$this, 'override_author_links'], 10, 3);
    }

    /**
     * Create custom URL endpoint: /devotee/{name}
     */
    public function register_endpoint() {
        add_rewrite_rule(
            '^devotee/([^/]*)/?',
            'index.php?devotee_profile=$matches[1]',
            'top'
        );
    }

    public function add_query_var($vars) {
        $vars[] = 'devotee_profile';
        return $vars;
    }

    /**
     * Intercept and render profile page
     */
    public function profile_template() {
        $username = get_query_var('devotee_profile');

        if (!$username) return;

        $user = get_user_by('slug', sanitize_title($username));
        if (!$user) wp_die('<h2>Devotee not found</h2>');

        $uid    = $user->ID;
        $visits = get_user_meta($uid, '_sh_passport', true) ?: [];
        $count  = count($visits);

        $level = $this->badge_level($count);

        get_header(); ?>
        <div class="sh-profile-wrap">
            <h1>🛕 <?= esc_html($user->display_name) ?></h1>
            <p><strong>Verified Temples Visited:</strong> <?= $count ?></p>
            <p><strong>Devotee Level:</strong> <?= $level ?></p>

            <?php if ($count > 0): ?>
                <h3>Visited Temples</h3>
                <ul class="sh-profile-visited">
                    <?php foreach ($visits as $pid): ?>
                        <li><a href="<?= get_permalink($pid) ?>">
                            <?= get_the_title($pid) ?>
                        </a></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
        <?php
        get_footer();
        exit;
    }

    /**
     * Badge system — consistent with Passport
     */
    private function badge_level($count) {
        if ($count >= 100) return '💎 Diamond Devotee';
        if ($count >= 50) return '🏅 Gold Devotee';
        if ($count >= 20) return '⚪ Silver Devotee';
        if ($count >= 5) return '🟤 Bronze Devotee';
        return '🙏 New Devotee';
    }

    /**
     * Replace WP's author link with our devotee URL
     */
    public function override_author_links($link, $author_id, $author_nicename) {
        return home_url('/devotee/' . $author_nicename);
    }

}

new SH_KG_Profile_Engine();
