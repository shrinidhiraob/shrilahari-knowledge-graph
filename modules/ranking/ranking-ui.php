<?php
/**
 * Ranking UI: Top Temples Lists + ItemList Schema
 */
if (!defined('ABSPATH')) exit;

class SH_KG_Ranking_UI {

    public function __construct() {
        add_shortcode('top_temples', [$this, 'shortcode_top_temples']);
        add_filter('the_content', [$this, 'schema_after_content'], 99);
    }

    /**
     * Shortcode: [top_temples] (default top 10)
     */
    public function shortcode_top_temples($atts) {

        $atts = shortcode_atts([
            'limit' => 10,
            'deity' => '',
            'location' => ''
        ], $atts);

        $args = [
            'post_type' => 'sh_temple',
            'posts_per_page' => intval($atts['limit']),
            'meta_key' => '_sh_popularity_score',
            'orderby' => 'meta_value_num',
            'order' => 'DESC'
        ];

        // Filter by deity
        if (!empty($atts['deity'])) {
            $args['meta_query'][] = [
                'key' => '_sh_temple_deity',
                'value' => sanitize_text_field($atts['deity']),
                'compare' => '='
            ];
        }

        // Filter by location taxonomy
        if (!empty($atts['location'])) {
            $args['tax_query'][] = [
                'taxonomy' => 'sh_location',
                'field' => 'slug',
                'terms' => sanitize_title($atts['location'])
            ];
        }

        $posts = get_posts($args);

        if (empty($posts)) return '<p>No temples found.</p>';

        ob_start(); ?>

        <div class="sh-rank-box">
            <h2>Top Temples</h2>

            <!-- Mobile/Card UI -->
            <div class="sh-rank-cards">
                <?php $i=1; foreach($posts as $p): ?>
                    <div class="sh-rank-card">
                        <span class="rank-num"><?= $i++ ?></span>
                        <a href="<?= get_permalink($p->ID); ?>">
                            <h4><?= get_the_title($p->ID); ?></h4>
                        </a>
                        <div class="score">
                            Score: <?= get_post_meta($p->ID,'_sh_popularity_score',true) ?> / 100
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Desktop Table UI -->
            <table class="sh-rank-table">
                <thead><tr><th>#</th><th>Temple</th><th>Score</th></tr></thead>
                <tbody>
                    <?php $i=1; foreach($posts as $p): ?>
                        <tr>
                            <td><?= $i ?></td>
                            <td>
                                <a href="<?= get_permalink($p->ID); ?>">
                                    <?= get_the_title($p->ID); ?>
                                </a>
                            </td>
                            <td><?= get_post_meta($p->ID,'_sh_popularity_score',true) ?></td>
                        </tr>
                    <?php $i++; endforeach; ?>
                </tbody>
            </table>
        </div>

        <?php
        $html = ob_get_clean();

        // Save for schema usage
        $GLOBALS['sh_ranked_temples'] = $posts;

        return $html;
    }

    /**
     * Schema (ItemList) for lists
     */
    public function schema_after_content($content) {

        if (!isset($GLOBALS['sh_ranked_temples'])) return $content;

        $list = [];
        foreach ($GLOBALS['sh_ranked_temples'] as $p) {
            $list[] = [
                '@type' => 'ListItem',
                'position' => count($list) + 1,
                'url' => get_permalink($p->ID),
                'name' => get_the_title($p->ID)
            ];
        }

        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'ItemList',
            'itemListElement' => $list
        ];

        return $content . '<script type="application/ld+json">'.json_encode($schema).'</script>';
    }
}

new SH_KG_Ranking_UI();
