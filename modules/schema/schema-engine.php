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

    // Temple metadata
    $deity        = get_post_meta($post_id, '_sh_temple_deity', true);
    $sampradaya   = get_post_meta($post_id, '_sh_temple_sampradaya', true);
    $type         = get_post_meta($post_id, '_sh_temple_type', true);
    $architecture = get_post_meta($post_id, '_sh_temple_architecture', true);
    $year         = get_post_meta($post_id, '_sh_temple_year', true);
    $timings      = get_post_meta($post_id, '_sh_temple_timings', true);
    $phone        = get_post_meta($post_id, '_sh_temple_phone', true);
    $website      = get_post_meta($post_id, '_sh_temple_website', true);
    $rating       = get_post_meta($post_id, '_sh_temple_rating', true);

    $schema = [
        '@context' => 'https://schema.org',
        '@type' => 'HinduTemple',
        'name' => get_the_title($post_id),
        'description' => wp_strip_all_tags(get_the_excerpt($post_id)),
        'image' => $image ?: '',
        'url' => get_permalink($post_id),
        'geo' => [
            '@type' => 'GeoCoordinates',
            'latitude' => $lat,
            'longitude' => $lng
        ],
    ];

    if ($deity)        $schema['religion'] = 'Hindu'; // implicit
    if ($deity)        $schema['additionalProperty'][] = ['name' => 'Main Deity', 'value' => $deity];
    if ($sampradaya)   $schema['additionalProperty'][] = ['name' => 'Sampradaya', 'value' => $sampradaya];
    if ($type)         $schema['additionalProperty'][] = ['name' => 'Temple Type', 'value' => $type];
    if ($architecture) $schema['additionalProperty'][] = ['name' => 'Architecture Style', 'value' => $architecture];
    if ($year)         $schema['foundingDate'] = $year;
    if ($timings)      $schema['openingHours'] = $timings;
    if ($phone)        $schema['telephone'] = $phone;
    if ($website)      $schema['sameAs'] = $website;
    if ($rating)       $schema['aggregateRating'] = [
                            '@type' => 'AggregateRating',
                            'ratingValue' => $rating,
                            'ratingCount' => 100,
                        ];

    return $schema;
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
