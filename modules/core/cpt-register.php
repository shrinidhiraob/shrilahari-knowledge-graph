<?php
/**
 * Register Custom Post Types for Knowledge Graph
 */
if (!defined('ABSPATH')) exit;

class SH_KG_CPT_Register {

    public function __construct() {
        add_action('init', [$this, 'register_cpts']);
    }

    public function register_cpts() {

        // Common settings for all CPTs
        $supports = ['title', 'editor', 'thumbnail', 'excerpt'];
        $show_rest = true; // Enable Gutenberg & API

        // Temples
        register_post_type('sh_temple', [
            'label' => __('Temples', 'shrilahari-kg'),
            'public' => true,
            'has_archive' => true,
            'rewrite' => ['slug' => 'temple'],
            'menu_icon' => 'dashicons-location-alt',
            'supports' => $supports,
            'show_in_rest' => $show_rest,
        ]);

        // Places: Beaches, Waterfalls, Viewpoints, etc.
        register_post_type('sh_place', [
            'label' => __('Places', 'shrilahari-kg'),
            'public' => true,
            'has_archive' => true,
            'rewrite' => ['slug' => 'place'],
            'menu_icon' => 'dashicons-palmtree',
            'supports' => $supports,
            'show_in_rest' => $show_rest,
        ]);

        // Food & Recipes
        register_post_type('sh_food', [
            'label' => __('Food & Recipes', 'shrilahari-kg'),
            'public' => true,
            'has_archive' => true,
            'rewrite' => ['slug' => 'food'],
            'menu_icon' => 'dashicons-carrot',
            'supports' => $supports,
            'show_in_rest' => $show_rest,
        ]);

        // Festivals & Events
        register_post_type('sh_event', [
            'label' => __('Festivals & Events', 'shrilahari-kg'),
            'public' => true,
            'has_archive' => true,
            'rewrite' => ['slug' => 'event'],
            'menu_icon' => 'dashicons-calendar-alt',
            'supports' => $supports,
            'show_in_rest' => $show_rest,
        ]);

        // Restaurants & Stays
        register_post_type('sh_restaurant', [
            'label' => __('Restaurants & Hotels', 'shrilahari-kg'),
            'public' => true,
            'has_archive' => true,
            'rewrite' => ['slug' => 'stay'],
            'menu_icon' => 'dashicons-store',
            'supports' => $supports,
            'show_in_rest' => $show_rest,
        ]);

        // Transport: Airport / Bus / Rail
        register_post_type('sh_transport', [
            'label' => __('Transportation', 'shrilahari-kg'),
            'public' => true,
            'has_archive' => true,
            'rewrite' => ['slug' => 'transport'],
            'menu_icon' => 'dashicons-admin-site-alt3',
            'supports' => $supports,
            'show_in_rest' => $show_rest,
        ]);
    }
}

new SH_KG_CPT_Register();
