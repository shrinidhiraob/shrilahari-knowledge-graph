<?php
/**
 * GPS Map Picker for Admin Screen
 */
if (!defined('ABSPATH')) exit;

class SH_KG_Map_Picker {

    public function __construct() {
        add_action('admin_enqueue_scripts', [$this, 'enqueue_map_assets']);
        add_action('edit_form_after_editor', [$this, 'render_map_picker']);
    }

    /**
     * Load Leaflet Map CSS + JS
     */
    public function enqueue_map_assets($hook) {

        global $post;

        if (!isset($post)) return;

        $post_types = ['sh_temple','sh_place','sh_food','sh_event','sh_restaurant','sh_transport'];

        if (!in_array($post->post_type, $post_types)) return;

        wp_enqueue_style('leaflet-css', 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css', [], null);
        wp_enqueue_script('leaflet-js', 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js', [], null, true);
    }


    /**
     * Admin map below editor
     */
    public function render_map_picker($post) {

        if (!in_array($post->post_type, ['sh_temple','sh_place','sh_food','sh_event','sh_restaurant','sh_transport'])) return;

        $lat = get_post_meta($post->ID, '_sh_kg_lat', true) ?: 13.3379;
        $lng = get_post_meta($post->ID, '_sh_kg_lng', true) ?: 74.7421;

        ?>
        <h3>📍 Set Location on Map</h3>

        <p>Drag marker or click location to update GPS automatically.</p>

        <div id="sh_kg_map" style="height: 350px; margin-bottom: 10px; border:1px solid #ccc;"></div>

        <script>
        document.addEventListener('DOMContentLoaded', function() {

            var map = L.map('sh_kg_map').setView([<?php echo $lat; ?>, <?php echo $lng; ?>], 12);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
            }).addTo(map);

            var marker = L.marker([<?php echo $lat; ?>, <?php echo $lng; ?>], {draggable:true}).addTo(map);

            function updatePosition(lat, lng) {
                document.querySelector('input[name="sh_kg_lat"]').value = lat;
                document.querySelector('input[name="sh_kg_lng"]').value = lng;
            }

            map.on('click', function(e) {
                marker.setLatLng(e.latlng);
                updatePosition(e.latlng.lat, e.latlng.lng);
            });

            marker.on('dragend', function() {
                var pos = marker.getLatLng();
                updatePosition(pos.lat, pos.lng);
            });

        });
        </script>
        <?php
    }
}

new SH_KG_Map_Picker();
