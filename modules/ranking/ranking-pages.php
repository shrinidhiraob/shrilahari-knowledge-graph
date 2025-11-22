<?php
/**
 * Auto Generate Temple Ranking SEO Pages
 * /top-temples/
 */
if (!defined('ABSPATH')) exit;

class SH_KG_Ranking_Pages {

    private $pages = [
        [
            'title' => 'Top Temples in India',
            'slug'  => 'top-temples',
            'shortcode' => '[top_temples limit="50"]'
        ]
    ];

    public function __construct() {
        add_action('admin_init', [$this, 'ensure_pages_exist']);
        add_filter('theme_page_templates', [$this, 'register_template']);
        add_filter('page_template', [$this, 'apply_template']);
    }

    /**
     * Create pages if missing
     */
    public function ensure_pages_exist() {
        foreach ($this->pages as $page) {

            $existing = get_page_by_path($page['slug']);

            if (!$existing) {
                wp_insert_post([
                    'post_title'   => $page['title'],
                    'post_name'    => $page['slug'],
                    'post_status'  => 'publish',
                    'post_type'    => 'page',
                    'post_content' => $page['shortcode']
                ]);
            }
        }
    }

    /**
     * Custom template (optional)
     */
    public function register_template($templates) {
        $templates['ranking-template.php'] = 'Temple Ranking Template';
        return $templates;
    }

    public function apply_template($template) {
        if (is_page('top-temples')) {
            $custom = SH_KG_PLUGIN_DIR . 'modules/ranking/ranking-template.php';
            if (file_exists($custom)) return $custom;
        }
        return $template;
    }

}

new SH_KG_Ranking_Pages();
