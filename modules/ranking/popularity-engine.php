<?php
/**
 * Temple Popularity Score Engine
 * Mixed Authority Score (C)
 */
if (!defined('ABSPATH')) exit;

class SH_KG_Popularity_Engine {

    private $meta_key = '_sh_popularity_score';

    public function __construct() {
        add_action('sh_update_popularity_scores', [$this, 'update_all_scores']);
        add_action('init', [$this, 'schedule_cron']);
    }

    /**
     * Setup weekly auto-score refresh (cron)
     */
    public function schedule_cron() {
        if (!wp_next_scheduled('sh_update_popularity_scores')) {
            wp_schedule_event(time(), 'daily', 'sh_update_popularity_scores');
        }
    }

    /**
     * Score calculation for all temples
     */
    public function update_all_scores() {

        $temples = get_posts([
            'post_type' => 'sh_temple',
            'posts_per_page' => -1
        ]);

        foreach ($temples as $post) {
            $this->update_score($post->ID);
        }
    }

    /**
     * Calculate popularity score
     */
    public function update_score($post_id) {

        $rating = get_post_meta($post_id, '_sh_rating_avg', true) ?: 0;
        $visits = count(get_post_meta($post_id, '_sh_review_voters', true) ?: []);
        $reviews = count(get_post_meta($post_id, '_sh_reviews', true) ?: []);
        $relations = $this->relationship_count($post_id);

        $score = 
            ($rating * 30) +
            ($visits * 0.5) +
            ($reviews * 2) +
            ($relations * 1);

        $score = round(min($score, 100), 2);

        update_post_meta($post_id, $this->meta_key, $score);

        return $score;
    }

    /**
     * Count related items (signals popularity reach)
     */
    private function relationship_count($post_id) {
        $location_terms = wp_get_post_terms($post_id, 'sh_location');
        return !empty($location_terms) ? count($location_terms) : 0;
    }

}

new SH_KG_Popularity_Engine();
