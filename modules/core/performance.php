<?php
/**
 * Performance Optimization + Nearby Caching
 */
if (!defined('ABSPATH')) exit;

class SH_KG_Performance {

    private $cache_key_prefix = 'sh_kg_cache_';

    public function __construct() {
        add_filter('sh_kg_nearby_query', [$this, 'cache_nearby'], 10, 4);
        add_action('save_post', [$this, 'clear_cache_on_save']);
    }

    /**
     * Caching Nearby Items
     */
    public function cache_nearby($callback, $post_id, $lat, $lng) {

        $key = $this->cache_key_prefix . $post_id;
        $cached = get_transient($key);

        if ($cached) {
            return $cached;
        }

        // Run the original callback
        $result = call_user_func($callback, $post_id, $lat, $lng);

        // Cache for 12 hours
        set_transient($key, $result, 12 * HOUR_IN_SECONDS);

        return $result;
    }


    /**
     * Clear cache when post is updated
     */
    public function clear_cache_on_save($post_id) {

        $key = $this->cache_key_prefix . $post_id;
        delete_transient($key);
    }
}

new SH_KG_Performance();
