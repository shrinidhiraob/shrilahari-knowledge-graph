<?php
/**
 * Multilingual UI — Kannada + English Auto Switch
 */
if (!defined('ABSPATH')) exit;

class SH_KG_Multilingual {

    public function __construct() {
        add_filter('sh_kg_label', [$this, 'translate_label'], 10, 2);
    }

    public function translate_label($label, $key) {

        $locale = get_locale();
        // Kannada detected
        if (strpos($locale, 'kn') !== false) {
            return $this->kannada_labels($key) ?: $label;
        }

        // Default English
        return $label;
    }

    private function kannada_labels($key) {

        $labels = [
            // Temple Info Fields
            'deity'        => 'ಮುಖ್ಯ ದೇವತೆ',
            'sampradaya'   => 'ಸಂಪ್ರದಾಯ / ಮಠ',
            'type'         => 'ದೇವಾಲಯ ದರ್ಜೆ',
            'architecture' => 'ವಾಸ್ತುಶೈಲಿ',
            'year'         => 'ನಿರ್ಮಾಣ ವರ್ಷ',
            'timings'      => 'ತೆರೆಯುವ ಸಮಯ',
            'phone'        => 'ಸಂಪರ್ಕ',
            'website'      => 'ವೆಬ್‌ಸೈಟ್',
            'dress_code'   => 'ಉಡುಪಿನ ನಿಯಮ',
            'prasadam'     => 'ಪ್ರಸಾದ ಲಭ್ಯತೆ',
            'rating'       => 'ರೇಟಿಂಗ್',

            // Frontend Sections
            'temple_info'  => 'ದೇವಾಲಯ ಮಾಹಿತಿ',
            'nearby_temples' => 'ಹತ್ತಿರದ ದೇವಾಲಯಗಳು',
            'nearby_places'  => 'ಹತ್ತಿರದ ಸ್ಥಳಗಳು',
            'nearby_food'    => 'ಹತ್ತಿರದ ಆಹಾರ',
            'nearby_stay'    => 'ಹತ್ತಿರದ ವಾಸ್ತವ್ಯ',
            'nearby_events'  => 'ಹತ್ತಿರದ ಉತ್ಸವಗಳು',
            'nearby_transport' => 'ಹತ್ತಿರದ ಸಾರಿಗೆ',

            // Breadcrumb
            'Temple'       => 'ದೇವಾಲಯ',
            'Place'        => 'ಸ್ಥಳ',
            'Food'         => 'ಆಹಾರ',
            'Festival/Event' => 'ಉತ್ಸವ',
            'Restaurant/Stay' => 'ಹೋಟೆಲ್ / ವಾಸ್ತವ್ಯ',
            'Transport'    => 'ಸಾರಿಗೆ',

            // FAQ
            'faq_title'    => 'ಪದೇ ಪದೇ ಕೇಳುವ ಪ್ರಶ್ನೆಗಳು',
        ];

        return $labels[$key] ?? null;
    }
}

new SH_KG_Multilingual();
