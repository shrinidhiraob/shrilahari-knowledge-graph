<?php
/**
 * Nearby Intelligence Engine
 * Finds posts within a radius using GPS coordinates
 */
if (!defined('ABSPATH')) exit;

class SH_KG_Nearby_Engine {

    public function __construct() {
        add_filter('the_content', [$this, 'append_nearby_content']);
    }

    /**
     * Append Nearby Section Below Content
     */
    public function append_nearby_content($content) {
        if (!is_singular()) return $content;

        global $post;
        $lat = get_post_meta($post->ID, '_sh_kg_lat', true);
        $lng = get_post_meta($post->ID, '_sh_kg_lng', true);

        if (!$lat || !$lng) return $content;

        $nearby = $this->fetch_nearby_items($post->ID, $lat, $lng);

        if (empty($nearby)) return $content;

        ob_start();
        ?>
        <div class="sh-kg-nearby-wrapper">
            <h2>Nearby Places</h2>
            <ul>
                <?php foreach ($nearby as $item): ?>
                    <li>
                        <a href="<?php echo get_permalink($item['id']); ?>">
                            <?php echo esc_html($item['title']); ?>
                        </a>
                        <span><?php echo $item['distance']; ?> km</span>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php

        return $content . ob_get_clean();
    }


    /**
     * Query + Distance Filter
     */
    private function fetch_nearby_items($current_id, $lat, $lng) {

        $post_types = [
            'sh_temple',
            'sh_place',
            'sh_food',
            'sh_event',
            'sh_restaurant'
        ];

        $query = new WP_Query([
            'post_type' => $post_types,
            'posts_per_page' => -1,
            'post__not_in' => [$current_id],
        ]);

        $results = [];

        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $id = get_the_ID();

                $lat2 = get_post_meta($id, '_sh_kg_lat', true);
                $lng2 = get_post_meta($id, '_sh_kg_lng', true);

                if (!$lat2 || !$lng2) continue;

                $dist = $this->calculate_distance($lat, $lng, $lat2, $lng2);

                if ($dist <= 50) { // Auto radius can be improved later
                    $results[] = [
                        'id' => $id,
                        'title' => get_the_title($id),
                        'distance' => round($dist, 1)
                    ];
                }
            }
            wp_reset_postdata();
        }

        usort($results, fn($a, $b) => $a['distance'] <=> $b['distance']);
        return array_slice($results, 0, 10);
    }


    /**
     * Haversine Formula (KM)
     */
    private function calculate_distance($lat1, $lng1, $lat2, $lng2) {
        $earth_radius = 6371;
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);

        $a = sin($dLat/2) * sin($dLat/2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLng/2) * sin($dLng/2);

        return $earth_radius * 2 * asin(sqrt($a));
    }
}

new SH_KG_Nearby_Engine();
