<?php
/**
 * Temple Knowledge Panel - Frontend Display
 */
if (!defined('ABSPATH')) exit;

class SH_KG_Temple_UI {

    public function __construct() {
        add_filter('the_content', [$this, 'add_temple_info_panel'], 5);
    }

    public function add_temple_info_panel($content) {

        if (!is_singular('sh_temple')) return $content;

        global $post;
        $fields = [
            'deity'        => '🛕 Main Deity',
            'sampradaya'   => '🕉️ Sampradaya / Matha',
            'type'         => '🏛 Temple Type',
            'architecture' => '🏗 Architecture',
            'year'         => '📜 Year Established',
            'timings'      => '⏰ Opening Hours',
            'phone'        => '📞 Contact',
            'website'      => '🌐 Website',
            'dress_code'   => '👕 Dress Code',
            'prasadam'     => '🍛 Prasadam',
            'rating'       => '⭐ Rating'
        ];

        $html = '<div class="sh-temple-info-box"><h3>Temple Information</h3><ul>';

        $hasData = false;

        foreach ($fields as $key => $label) {
            $value = get_post_meta($post->ID, '_sh_temple_' . $key, true);
            if (!$value) continue;
            
            $hasData = true;

            if ($key === 'website') {
                $value = '<a href="' . esc_url($value) . '" target="_blank">' . esc_html($value) . '</a>';
            }

            $html .= '<li><span class="icon">' . $label . ':</span> ' . $value . '</li>';
        }

        $html .= '</ul></div>';

        if (!$hasData) return $content;

        return $html . $content;
    }
}

new SH_KG_Temple_UI();
