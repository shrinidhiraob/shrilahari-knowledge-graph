<?php
/**
 * Smart Auto FAQ Generator + Schema
 * Creates FAQ blocks from existing temple metadata
 */
if (!defined('ABSPATH')) exit;

class SH_KG_FAQ_Schema {

    public function __construct() {
        add_filter('the_content', [$this, 'render_faq'], 50);
        add_action('wp_head', [$this, 'add_faq_schema'], 30);
    }


    /**
     * Display FAQs under content
     */
    public function render_faq($content) {

        if (!is_singular('sh_temple')) return $content;

        global $post;

        $faq = $this->get_faq_data($post->ID);
        if (empty($faq)) return $content;

        $html = '<div class="sh-kg-faq"><h3>Frequently Asked Questions</h3><ul>';

        foreach ($faq as $q => $a) {
            if (!$a) continue;
            $html .= "<li><strong>$q</strong><br>$a</li>";
        }

        $html .= '</ul></div>';

        return $content . $html;
    }


    /**
     * Schema Markup
     */
    public function add_faq_schema() {

        if (!is_singular('sh_temple')) return;

        global $post;

        $faq = $this->get_faq_data($post->ID);
        if (empty($faq)) return;

        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => []
        ];

        foreach ($faq as $q => $a) {
            if (!$a) continue;
            $schema['mainEntity'][] = [
                '@type' => 'Question',
                'name' => $q,
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text' => wp_strip_all_tags($a)
                ]
            ];
        }

        echo "<script type='application/ld+json'>" .
            wp_json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) .
        "</script>";
    }


    /**
     * Extract FAQ Based on Meta Values
     */
    private function get_faq_data($post_id) {

        return [
            'Who is the main deity of this temple?' =>
                get_post_meta($post_id, '_sh_temple_deity', true),

            'What are the opening timings?' =>
                get_post_meta($post_id, '_sh_temple_timings', true),

            'Is prasadam available?' =>
                get_post_meta($post_id, '_sh_temple_prasadam', true),

            'What is the architecture style of this temple?' =>
                get_post_meta($post_id, '_sh_temple_architecture', true),
        ];
    }
}

new SH_KG_FAQ_Schema();
