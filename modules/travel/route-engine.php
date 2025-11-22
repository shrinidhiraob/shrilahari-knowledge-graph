<?php
/**
 * Route Intelligence Engine
 * Auto "How to Reach" with Hybrid UI (Cards + Table)
 */
if (!defined('ABSPATH')) exit;

class SH_KG_Route_Engine {

    public function __construct() {
        add_filter('the_content', [$this, 'render_route_info'], 15);
    }

    /**
     * Insert Route Section below Temple Knowledge Panel
     */
    public function render_route_info($content) {

        if (!is_singular('sh_temple')) return $content;

        global $post;
        $lat = get_post_meta($post->ID, '_sh_kg_lat', true);
        $lng = get_post_meta($post->ID, '_sh_kg_lng', true);

        if (!$lat || !$lng) return $content;

        // Static initial placeholders – will auto-improve later 🚀
        $routes = [
            'car' => [
                'icon' => '🚘',
                'title' => 'By Road',
                'details' => 'Good road access from nearby cities.',
            ],
            'bus' => [
                'icon' => '🚌',
                'title' => 'By Bus',
                'details' => 'Frequent bus service available.',
            ],
            'train' => [
                'icon' => '🚆',
                'title' => 'By Rail',
                'details' => 'Nearest railway station within reachable distance.',
            ],
            'air' => [
                'icon' => '✈️',
                'title' => 'By Air',
                'details' => 'Nearest airport with taxi or bus connections.',
            ],
        ];

        ob_start();
        ?>
        <div class="sh-route-box">
            <h3>How to Reach</h3>

            <div class="sh-route-card-wrap">
                <?php foreach ($routes as $r): ?>
                    <div class="sh-route-card">
                        <div class="icon"><?= $r['icon'] ?></div>
                        <div class="rt-title"><?= $r['title'] ?></div>
                        <div class="rt-text"><?= $r['details'] ?></div>
                    </div>
                <?php endforeach; ?>
            </div>

            <table class="sh-route-table">
                <tr><th>Mode</th><th>Details</th></tr>
                <?php foreach ($routes as $r): ?>
                    <tr>
                        <td><?= $r['icon'].' '.$r['title'] ?></td>
                        <td><?= $r['details'] ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>
        <?php

        return $content . ob_get_clean();
    }
}

new SH_KG_Route_Engine();
