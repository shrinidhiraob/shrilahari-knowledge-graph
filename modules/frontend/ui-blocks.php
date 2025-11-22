<?php
/**
 * Frontend UI Blocks for Nearby Sections
 */
if (!defined('ABSPATH')) exit;

class SH_KG_UI_Blocks {

    public function __construct() {
        add_filter('the_content', [$this, 'render_nearby_ui'], 30);
    }

    public function render_nearby_ui($content) {
        if (!is_singular()) return $content;

        global $post;

        $lat = get_post_meta($post->ID, '_sh_kg_lat', true);
        $lng = get_post_meta($post->ID, '_sh_kg_lng', true);

        if (!$lat || !$lng) return $content;

        $post_types = [
            'sh_temple'     => '🛕 Nearby Temples',
            'sh_place'      => '📸 Nearby Places',
            'sh_food'       => '🍽 Nearby Food',
            'sh_event'      => '🎉 Nearby Events',
            'sh_restaurant' => '🏨 Stay & Restaurants',
            'sh_transport'  => '🚌 Nearby Transport',
        ];

        $menu_html = '';
        $sections_html = '';
        $index = 0;

        foreach ($post_types as $type => $label) {
            $items = $this->fetch_items($post->ID, $lat, $lng, $type);
            if (empty($items)) continue;

            $section_id = 'sh-kg-section-' . $index;
            $menu_html .= "<a href='#{$section_id}'>{$label}</a> | ";

            $sections_html .= "<div id='{$section_id}' class='sh-kg-section'>
                <h3>{$label}</h3>" .
                $this->render_items($items, $type) .
            "</div>";

            $index++;
        }

        if ($sections_html === '') return $content;

        $menu_html = "<div class='sh-kg-top-menu'><strong>Explore Nearby: </strong>" 
                    . rtrim($menu_html, " | ") . "</div>";

        return $content . $menu_html . $sections_html;
    }


    private function fetch_items($current_id, $lat, $lng, $type) {

        $query = new WP_Query([
            'post_type' => $type,
            'post__not_in' => [$current_id],
            'posts_per_page' => -1
        ]);

        $results = [];

        while ($query->have_posts()) {
            $query->the_post();
            $id = get_the_ID();

            $lat2 = get_post_meta($id, '_sh_kg_lat', true);
            $lng2 = get_post_meta($id, '_sh_kg_lng', true);

            if (!$lat2 || !$lng2) continue;

            $dist = $this->distance($lat, $lng, $lat2, $lng2);
            if ($dist <= 50) {
                $results[] = [
                    'id' => $id,
                    'title' => get_the_title($id),
                    'image' => get_the_post_thumbnail_url($id, 'medium'),
                    'distance' => round($dist, 1)
                ];
            }
        }
        wp_reset_postdata();

        usort($results, fn($a, $b) => $a['distance'] <=> $b['distance']);
        return array_slice($results, 0, 6);
    }


    private function distance($lat1, $lng1, $lat2, $lng2) {
        $earth = 6371;
        $lat = deg2rad($lat2 - $lat1);
        $lng = deg2rad($lng2 - $lng1);

        $a = sin($lat/2)**2 + cos(deg2rad($lat1)) *
            cos(deg2rad($lat2)) * sin($lng/2)**2;

        return $earth * 2 * asin(sqrt($a));
    }


    private function render_items($items, $type) {

        if (in_array($type, ['sh_temple','sh_place','sh_food'])) {
            $html = "<div class='sh-kg-card-grid'>";
            foreach ($items as $i) {
                $img = $i['image'] ? "<img src='{$i['image']}' alt=''>" : '';
                $html .= "<div class='sh-kg-card'>{$img}
                            <h4><a href='".get_permalink($i['id'])."'>{$i['title']}</a></h4>
                            <p>{$i['distance']} km</p>
                          </div>";
            }
            return $html . "</div>";
        }

        $html = "<ul class='sh-kg-list'>";
        foreach ($items as $i) {
            $html .= "<li><a href='".get_permalink($i['id'])."'>{$i['title']}</a> - {$i['distance']} km</li>";
        }
        return $html . "</ul>";
    }
}

new SH_KG_UI_Blocks();
