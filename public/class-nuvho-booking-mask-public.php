<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://herobe.com
 * @since      2.0.0
 *
 * @package    Nuvho_Booking_Mask
 * @subpackage Nuvho_Booking_Mask/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and hooks for
 * enqueuing the public-facing stylesheet and JavaScript.
 *
 * @package    Nuvho_Booking_Mask
 * @subpackage Nuvho_Booking_Mask/public
 * @author     Odysseus Ambut / Herobe <contact@herobe.com>
 */
class Nuvho_Booking_Mask_Public {

    /**
     * The ID of this plugin.
     *
     * @since    2.0.0
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    2.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @since    2.0.0
     * @param      string    $plugin_name       The name of the plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct($plugin_name, $version) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    /**
     * Register the stylesheets for the public-facing side of the site.
     *
     * @since    2.0.0
     */
    public function enqueue_styles() {
        // DateRangePicker styles
        wp_enqueue_style('daterangepicker', 'https://cdn.jsdelivr.net/npm/daterangepicker@3.1.0/daterangepicker.css', array(), '3.1.0');
        wp_enqueue_style($this->plugin_name, NUVHO_BOOKING_MASK_PLUGIN_URL . 'public/css/nuvho-booking-mask-public.css', array('daterangepicker'), $this->version, 'all');
    }

    /**
     * Register the JavaScript for the public-facing side of the site.
     *
     * @since    2.0.0
     */
    public function enqueue_scripts() {
        // Moment.js is required for DateRangePicker
        wp_enqueue_script('moment', 'https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js', array(), '2.29.4', false);
        wp_enqueue_script('daterangepicker', 'https://cdn.jsdelivr.net/npm/daterangepicker@3.1.0/daterangepicker.min.js', array('jquery', 'moment'), '3.1.0', false);
        wp_enqueue_script($this->plugin_name, NUVHO_BOOKING_MASK_PLUGIN_URL . 'public/js/nuvho-booking-mask-public.js', array('jquery', 'moment', 'daterangepicker'), $this->version, false);
        
        // Add the direct URL override for Simple Booking v2
        wp_enqueue_script($this->plugin_name . '-direct-override', NUVHO_BOOKING_MASK_PLUGIN_URL . 'public/js/nuvho-booking-mask-public-direct-override.js', array('jquery', $this->plugin_name), $this->version, false);
        
        // Localize script with settings data
        $settings = get_option('nuvho_booking_mask_settings');
        
        // Add date formatting and locale settings for daterangepicker
        $locale_map = array(
            'English (US)' => 'en',
            'English (UK)' => 'en-gb',
            'Italian' => 'it',
            'French' => 'fr',
            'German' => 'de',
            'Spanish' => 'es',
            'Portuguese' => 'pt',
            'Dutch' => 'nl',
            'Russian' => 'ru',
            'Chinese' => 'zh-cn',
            'Japanese' => 'ja'
        );
        
        $date_format_map = array(
            'English (US)' => 'MM/DD/YYYY',
            'English (UK)' => 'DD/MM/YYYY',
            'Italian' => 'DD/MM/YYYY',
            'French' => 'DD/MM/YYYY',
            'German' => 'DD.MM.YYYY',
            'Spanish' => 'DD/MM/YYYY',
            'Portuguese' => 'DD/MM/YYYY',
            'Dutch' => 'DD-MM-YYYY',
            'Russian' => 'DD.MM.YYYY',
            'Chinese' => 'YYYY-MM-DD',
            'Japanese' => 'YYYY/MM/DD'
        );
        
        $locale = isset($locale_map[$settings['language']]) ? $locale_map[$settings['language']] : 'en';
        $date_format = isset($date_format_map[$settings['language']]) ? $date_format_map[$settings['language']] : 'MM/DD/YYYY';
        
        $localized_data = array_merge($settings, array(
            'locale' => $locale,
            'date_format' => $date_format,
            'lang' => strtoupper(substr(explode(' ', $settings['language'])[0], 0, 2)),
            'currency' => $settings['currency']
        ));
        
        wp_localize_script($this->plugin_name, 'nuvhoBookingSettings', $localized_data);
    }

    /**
     * Register tracking endpoint for analytics
     */
    private function track_booking_click() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'nuvho_booking_mask_reports';
        
        $settings = get_option('nuvho_booking_mask_settings');
        $booking_engine = $settings['option'];
        
        // Get today's record if exists
        $today = current_time('Y-m-d');
        $record = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM $table_name WHERE DATE(date) = %s AND booking_engine = %s",
                $today,
                $booking_engine
            )
        );
        
        if ($record) {
            // Update existing record
            $wpdb->update(
                $table_name,
                array(
                    'clicks' => $record->clicks + 1,
                    'conversion_rate' => round(($record->conversions / ($record->clicks + 1)) * 100, 2)
                ),
                array(
                    'id' => $record->id
                )
            );
        } else {
            // Insert new record
            $wpdb->insert(
                $table_name,
                array(
                    'date' => current_time('mysql'),
                    'booking_engine' => $booking_engine,
                    'clicks' => 1,
                    'conversions' => 0,
                    'conversion_rate' => 0
                )
            );
        }
    }

    /**
     * Booking mask shortcode callback
     */
    public function display_booking_mask($atts) {
        // Get settings
        $settings = get_option('nuvho_booking_mask_settings');
        
        // Track click (AJAX would be better for production)
        if (isset($_GET['nuvho_track']) && $_GET['nuvho_track'] === 'click') {
            $this->track_booking_click();
        }
        
        // Calculate border radius based on settings
        $border_radius = '0';
        if ($settings['booking_mask_border_radius'] === 'Rounded') {
            $border_radius = '8px';
        } elseif ($settings['booking_mask_border_radius'] === 'Pill') {
            $border_radius = '20px';
        }
        
        // Calculate button border radius
        $button_radius = '0';
        if ($settings['button_border_radius'] === 'Rounded') {
            $button_radius = '8px';
        } elseif ($settings['button_border_radius'] === 'Pill') {
            $button_radius = '20px';
        }
        
        // Calculate opacity
        $opacity = str_replace('%', '', $settings['background_opacity']) / 100;
        
        // For Simple Booking v2, we need to append the hotel_id to the URL
        if ($settings['option'] === 'Simple Booking v2') {
            $settings['url'] = trailingslashit($settings['url']) . $settings['hotel_id'];
        }
        
        // For Cloudbeds, append the hotel_id to the URL
        if ($settings['option'] === 'Cloudbeds') {
            $settings['url'] = trailingslashit($settings['url']) . $settings['hotel_id'];
        }
        
        // Start output buffering
        ob_start();
        
        // Include the template
        include NUVHO_BOOKING_MASK_PLUGIN_DIR . 'public/partials/nuvho-booking-mask-public-display.php';
        
        return ob_get_clean();
    }
}