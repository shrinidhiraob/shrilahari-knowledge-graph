<?php
/*
Plugin Name: Shrilahari Knowledge Graph
Description: Universal Knowledge Graph for Temples, Food, Travel, Places & Festivals with automated Schema, Location & Nearby Intelligence.
Version: 1.0.0
Author: Shrilahari
Text Domain: shrilahari-kg
Requires PHP: 7.4
Requires at least: 6.0
*/

if (!defined('ABSPATH')) exit;

// Define plugin path
define('SH_KG_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('SH_KG_PLUGIN_URL', plugin_dir_url(__FILE__));

// Load modules
function sh_kg_load_modules() {

    // Core
    require_once SH_KG_PLUGIN_DIR . 'modules/core/cpt-register.php';
    require_once SH_KG_PLUGIN_DIR . 'modules/core/taxonomy-location.php';
    require_once SH_KG_PLUGIN_DIR . 'modules/core/metadata.php';
    require_once SH_KG_PLUGIN_DIR . 'modules/temple/metadata-temple.php';
    require_once SH_KG_PLUGIN_DIR . 'modules/temple/temple-ui.php';
    require_once SH_KG_PLUGIN_DIR . 'modules/frontend/breadcrumbs.php';
    require_once SH_KG_PLUGIN_DIR . 'modules/frontend/faq-schema.php';
    require_once SH_KG_PLUGIN_DIR . 'modules/core/map-picker.php';
    require_once SH_KG_PLUGIN_DIR . 'modules/core/performance.php';\
    require_once SH_KG_PLUGIN_DIR . 'modules/language/multilingual.php';
    require_once SH_KG_PLUGIN_DIR . 'modules/travel/route-engine.php';
    require_once SH_KG_PLUGIN_DIR . 'modules/temple/devotion-engine.php';
    require_once SH_KG_PLUGIN_DIR . 'modules/commercial/affiliate-engine.php';
    require_once SH_KG_PLUGIN_DIR . 'modules/graph/relationship-engine.php';
    require_once SH_KG_PLUGIN_DIR . 'modules/frontend/map-cluster.php';
    require_once SH_KG_PLUGIN_DIR . 'modules/reputation/rating-engine.php';
    require_once SH_KG_PLUGIN_DIR . 'modules/reputation/review-engine.php';
    require_once SH_KG_PLUGIN_DIR . 'modules/reputation/visit-engine.php';




    // Schema
    require_once SH_KG_PLUGIN_DIR . 'modules/schema/schema-engine.php';

    // Nearby Intelligence
    require_once SH_KG_PLUGIN_DIR . 'modules/nearby/nearby-engine.php';

    // Frontend UI
    require_once SH_KG_PLUGIN_DIR . 'modules/frontend/ui-blocks.php';

    // CSS
    add_action('wp_enqueue_scripts', function() {
        wp_enqueue_style('sh-kg-style', SH_KG_PLUGIN_URL . 'assets/css/style.css', [], '1.0.0');
    });
}
add_action('plugins_loaded', 'sh_kg_load_modules');

// Activation: flush URLs
function sh_kg_activate() {
    sh_kg_load_modules();
    flush_rewrite_rules();
}
register_activation_hook(__FILE__, 'sh_kg_activate');

// Deactivation: flush URLs
function sh_kg_deactivate() {
    flush_rewrite_rules();
}
register_deactivation_hook(__FILE__, 'sh_kg_deactivate');
?>

