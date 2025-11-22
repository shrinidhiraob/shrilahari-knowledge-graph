<?php
/**
 * Knowledge Graph Breadcrumb Navigation
 */
if (!defined('ABSPATH')) exit;

class SH_KG_Breadcrumbs {

    public function __construct() {
        add_action('the_content', [$this, 'render_breadcrumbs'], 1);
    }

    public function render_breadcrumbs($content) {

        if (!is_singular()) return $content;

        global $post;
        $type = get_post_type($post);

        $cpt_labels = [
            'sh_temple'     => __('Temple', 'shrilahari-kg'),
            'sh_place'      => __('Place', 'shrilahari-kg'),
            'sh_food'       => __('Food', 'shrilahari-kg'),
            'sh_event'      => __('Festival/Event', 'shrilahari-kg'),
            'sh_restaurant' => __('Restaurant/Stay', 'shrilahari-kg'),
            'sh_transport'  => __('Transport', 'shrilahari-kg'),
        ];

        if (!isset($cpt_labels[$type])) return $content;

        $trail = [];
        $trail[] = '<a href="'.home_url().'">Home</a>';

        // Location taxonomy path
        $locations = wp_get_post_terms($post->ID, 'sh_location');

        if (!empty($locations)) {
            $loc = $locations[0];

            while ($loc) {
                $trail[] = '<a href="'.get_term_link($loc).'">'.$loc->name.'</a>';
                $loc = ($loc->parent) ? get_term($loc->parent) : null;
            }
        }

        // Entity Type
        $trail[] = $cpt_labels[$type];

        // Current Title
        $trail[] = get_the_title($post->ID);

        $html = '<div class="sh-kg-breadcrumb">'.implode(' &raquo; ', $trail).'</div>';

        return $html . $content;
    }
}

new SH_KG_Breadcrumbs();
