<?php
/**
 * Temple Cluster Map Engine
 * Auto display nearby temples & places on map with clustering
 */
if (!defined('ABSPATH')) exit;

class SH_KG_Map_Cluster {

    public function __construct() {
        add_filter('the_content', [$this, 'render_cluster_map'], 35);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_map_assets']);
    }

    /**
     * Load Leaflet & marker cluster
     */
    public function enqueue_map_assets() {

        if (!is_singular('sh_temple')) return;

        wp_enqueue_style('leaflet-css', 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css');
        wp_enqueue_script('leaflet-js', 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js', [], null, true);

        wp_enqueue_style('leaflet-cluster-css', 'https://unpkg.com/leaflet.markercluster/dist/MarkerCluster.css');
        wp_enqueue_style('leaflet-cluster-default-css', 'https://unpkg.com/leaflet.markercluster/dist/MarkerCluster.Default.css');
        wp_enqueue_script('leaflet-marker-cluster-js', 'https://unpkg.com/leaflet.markercluster/dist/leaflet.markercluster.js', [], null, true);
    }


    /**
     * Generate UI
     */
    public function render_cluster_map($content) {

        if (!is_singular('sh_temple')) return $content;

        global $post;
        $lat = get_post_meta($post->ID, '_sh_kg_lat', true);
        $lng = get_post_meta($post->ID, '_sh_kg_lng', true);

        if (!$lat || !$lng) return $content;

        $nearby = $this->query_nearby($lat, $lng, $post->ID);

        if (empty($nearby)) return $content;

        ob_start(); ?>
        <div class="sh-map-cluster-box">
            <h3>Temples & Places Near This Location</h3>
            <div id="sh_cluster_map" style="height: 380px;"></div>
        </div>

        <script>
        document.addEventListener("DOMContentLoaded", function() {
            var map = L.map('sh_cluster_map').setView([<?= $lat ?>, <?= $lng ?>], 12);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
            }).addTo(map);

            var markers = L.markerClusterGroup();

            <?php foreach ($nearby as $item):
                $ilat = get_post_meta($item->ID, '_sh_kg_lat', true);
                $ilng = get_post_meta($item->ID, '_sh_kg_lng', true);
                $title = addslashes(get_the_title($item->ID));
                $link = get_permalink($item->ID);
            ?>
                var m = L.marker([<?= $ilat ?>, <?= $ilng ?>])
                    .bindPopup('<strong><?= $title ?></strong><br><a href="<?= $link ?>" target="_blank">View Details</a>');
                markers.addLayer(m);
            <?php endforeach; ?>

            map.addLayer(markers);
        });
        </script>
        <?php

        return $content . ob_get_clean();
    }

    /**
     * Fetch nearby temples & attractions using GPS metadata
     */
    private function query_nearby($lat, $lng, $exclude) {
        global $wpdb;

        $range = 0.25; // Approx 25km radius

        return $wpdb->get_results($wpdb->prepare("
            SELECT p.ID FROM $wpdb->posts p
            JOIN $wpdb->postmeta lat ON lat.post_id = p.ID AND lat.meta_key = '_sh_kg_lat'
            JOIN $wpdb->postmeta lng ON lng.post_id = p.ID AND lng.meta_key = '_sh_kg_lng'
            WHERE p.post_status = 'publish'
              AND p.ID != %d
              AND lat.meta_value BETWEEN %f AND %f
              AND lng.meta_value BETWEEN %f AND %f
        ", $exclude, $lat-$range, $lat+$range, $lng-$range, $lng+$range));
    }
}

new SH_KG_Map_Cluster();
