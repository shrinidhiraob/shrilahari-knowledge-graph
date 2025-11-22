<?php
/**
 * Schema Engine for Knowledge Graph
 */
if (!defined('ABSPATH')) exit;

class SH_KG_Schema_Engine {

    public function __construct() {
        add_action('wp_head', [$this, 'output_schema'], 20);
    }

    /**
     * Output JSON-LD Schema in Head
     */
    public function output_schema() {
        if (!is_singular()) return;

        global $post;
        $type = $post->post_type;

        switch ($type) {
            case 'sh_temple':
                $data = $this->schema_temple($post->ID);
                break;

            case 'sh_place':
                $data = $this->schema_generic($post->ID, 'TouristAttraction');
                break;

            case 'sh_food':
                $data = $this->schema_generic($post->ID, 'Recipe');
                break;

            case 'sh_event':
                $data = $this->schema_generic($post->ID, 'Festival');
                break;

            case 'sh_restaurant':
                $data = $this->schema_generic($post->ID, 'Restaurant');
                break;

            case 'sh_transport':
                $data = $this->schema_generic($post->ID, 'CivicStructure');
                break;

            default:
                return;
        }

        if (!empty($data)) {
            echo "<script type='application/ld+json'>" . wp_json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . "</script>";
        }
    }

    /**
     * Temple Schema (Enhanced Knowledge Graph)
     */
    private function schema_temple($post_id) {

        $lat = get_post_meta($post_id, '_sh_kg_lat', true);
        $lng = get_post_meta($post_id, '_sh_kg_lng', true);
        $image = get_the_post_thumbnail_url($post_id);

        return [
            '@context' => 'https://schema.org',
            '@type' => 'HinduTemple',
            'name' => get_the_title($post_id),
            'description' => wp_strip_all_tags(get_the_excerpt($post_id)),
            'url' => get_permalink($post_id),
            'image' => $image ?: '',
            'geo' => [
                '@type' => 'GeoCoordinates',
                'latitude' => $lat,
                'longitude' => $lng
            ],
        ];
    }

    /**
     * Generic Schema Template
     */
    private function schema_generic($post_id, $schema_type) {

        $image = get_the_post_thumbnail_url($post_id);

        return [
            '@context' => 'https://schema.org',
            '@type' => $schema_type,
            'name' => get_the_title($post_id),
            'description' => wp_strip_all_tags(get_the_excerpt($post_id)),
            'url' => get_permalink($post_id),
            'image' => $image ?: '',
        ];
    }
}

new SH_KG_Schema_Engine();
