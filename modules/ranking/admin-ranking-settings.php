<?php
/**
 * Admin Panel — Popularity Score Controls
 */
if (!defined('ABSPATH')) exit;

class SH_KG_Ranking_Admin {

    public function __construct() {
        add_action('admin_menu', [$this, 'add_menu']);
        add_action('admin_post_sh_recalculate_scores', [$this, 'handle_recalculate']);
    }

    public function add_menu() {
        add_submenu_page(
            'edit.php?post_type=sh_temple',
            'Temple Ranking Settings',
            'Ranking Settings',
            'manage_options',
            'sh-ranking-settings',
            [$this, 'settings_page']
        );
    }

    public function settings_page() { ?>
        <div class="wrap">
            <h2>Temple Ranking Settings</h2>

            <p>Click below to update popularity scores immediately.</p>

            <form method="POST" action="<?php echo admin_url('admin-post.php'); ?>">
                <input type="hidden" name="action" value="sh_recalculate_scores">
                <?php submit_button('Recalculate Scores Now 🔄'); ?>
            </form>
        </div>

        <style>
            .wrap h2 { margin-bottom: 14px; }
        </style>
    <?php }

    /**
     * Manual trigger
     */
    public function handle_recalculate() {

        if (!current_user_can('manage_options')) wp_die('Not allowed');

        // Trigger cron function manually
        do_action('sh_update_popularity_scores');

        wp_redirect(add_query_arg([
            'page' => 'sh-ranking-settings',
            'updated' => '1'
        ], admin_url('edit.php?post_type=sh_temple')));
        exit;
    }

}

new SH_KG_Ranking_Admin();
