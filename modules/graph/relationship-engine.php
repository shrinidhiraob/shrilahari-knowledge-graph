<?php
/**
 * Relationship Graph Engine
 * Smart Internal Linking based on deity, location & entity type
 */
if (!defined('ABSPATH')) exit;

class SH_KG_Relationship_Engine {

    public function __construct() {
        add_filter('the_content', [$this, 'render_relationship_blocks'], 45);
    }

    public function render_relationship_blocks($content) {

        if (!is_singular()) return $content;

        global $post;
        $type = get_post_type($post);
        $pid  = $post->ID;

        $blocks = [];

        if ($type === 'sh_temple') {
            $blocks[] = $this->related_by_deity($pid);
        }

        $blocks[] = $this->related_by_location($pid);

        return $content . implode('', array_filter($blocks));
    }


    /**
     * 1️⃣ Related Temples -> Same Deity
     */
    private function related_by_deity($pid) {

        $deity = get_post_meta($pid, '_sh_temple_deity', true);
        if (!$deity) return '';

        $posts = $this->query_related([
            [
                'key'     => '_sh_temple_deity',
                'value'   => $deity,
                'compare' => '='
            ]
        ], $pid);

        return $this->render_section('More Temples of ' . $deity, $posts);
    }


    /**
     * 2️⃣ Related by same Location Taxonomy
     */
    private function related_by_location($pid) {

        $terms = wp_get_post_terms($pid, 'sh_location');
        if (empty($terms)) return '';

        $posts = [];

        foreach ($terms as $term) {
            $posts = array_merge($posts, $this->query_term($term->term_id, $pid));
        }

        return $this->render_section('Nearby Places of Interest', $posts);
    }


    /**
     * Query helpers
     */
    private function query_related($meta_query, $exclude) {
        return get_posts([
            'post_type' => ['sh_temple','sh_place','sh_food','sh_event'],
            'posts_per_page' => 6,
            'post__not_in' => [$exclude],
            'meta_query' => $meta_query
        ]);
    }

    private function query_term($term_id, $exclude) {
        return get_posts([
            'post_type' => ['sh_temple','sh_place','sh_food','sh_event'],
            'posts_per_page' => 6,
            'post__not_in' => [$exclude],
            'tax_query' => [[
                'taxonomy'=>'sh_location',
                'terms'=>$term_id
            ]]
        ]);
    }


    /**
     * Render card section
     */
    private function render_section($title, $posts) {
        if (empty($posts)) return '';

        ob_start(); ?>
        <div class="sh-kg-section">
            <h3><?php echo esc_html($title); ?></h3>
            <div class="sh-kg-card-grid">
                <?php foreach ($posts as $p): ?>
                    <div class="sh-kg-card">
                        <a href="<?php echo get_permalink($p); ?>">
                            <?php if (has_post_thumbnail($p)) {
                                echo get_the_post_thumbnail($p, 'medium');
                            } ?>
                            <h4><?php echo get_the_title($p); ?></h4>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
}

new SH_KG_Relationship_Engine();
