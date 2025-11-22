<?php
/**
 * Universal Location Hierarchy Taxonomy
 * Country → State → District → Taluk → City/Village
 */
if (!defined('ABSPATH')) exit;

class SH_KG_Location_Taxonomy {

    public function __construct() {
        add_action('init', [$this, 'register_location_taxonomy']);
    }

    public function register_location_taxonomy() {

        $labels = [
            'name'          => __('Locations', 'shrilahari-kg'),
            'singular_name' => __('Location', 'shrilahari-kg'),
            'search_items'  => __('Search Locations', 'shrilahari-kg'),
            'all_items'     => __('All Locations', 'shrilahari-kg'),
            'parent_item'   => __('Parent Location', 'shrilahari-kg'),
            'edit_item'     => __('Edit Location', 'shrilahari-kg'),
            'add_new_item'  => __('Add New Location', 'shrilahari-kg'),
        ];

        register_taxonomy('sh_location', [
            'sh_temple', 'sh_place', 'sh_food', 'sh_event', 'sh_restaurant', 'sh_transport'
        ], [
            'labels' => $labels,
            'hierarchical' => true,
            'public' => true,
            'show_admin_column' => true,
            'rewrite' => [
                'slug' => 'location',
                'hierarchical' => true,
            ],
            'show_in_rest' => true,
        ]);
    }
}

new SH_KG_Location_Taxonomy();
