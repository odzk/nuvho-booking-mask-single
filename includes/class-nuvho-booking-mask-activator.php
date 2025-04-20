<?php
/**
 * Fired during plugin activation
 *
 * @link       https://herobe.com
 * @since      2.0.0
 *
 * @package    Nuvho_Booking_Mask
 * @subpackage Nuvho_Booking_Mask/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      2.0.0
 * @package    Nuvho_Booking_Mask
 * @subpackage Nuvho_Booking_Mask/includes
 * @author     Odysseus Ambut / Herobe <contact@herobe.com>
 */
class Nuvho_Booking_Mask_Activator {

    /**
     * Initialize default settings and create necessary database tables
     *
     * @since    2.0.0
     */
    public static function activate() {
        // Initialize default settings
        $default_settings = array(
            'option' => 'Simple Booking v1',
            'url' => 'https://www.simplebooking.it/ibe/search',
            'language' => 'English (US)',
            'currency' => 'AUD',
            'hotel_id' => '6886',
            'background_color' => '#4c7380',
            'background_opacity' => '100%',
            'button_color' => '#cccccc',
            'font_color' => '#ffffff',
            'button_text' => 'Book Now',
            'button_text_color' => '#000000',
            'button_border_radius' => 'Square',
            'booking_mask_border_radius' => 'Square',
            'font' => 'Default',
            'guest_selection_type' => 'dropdown',
            // Accor-specific settings
            'code_hotel' => '',
            'goto' => 'fiche_hotel',
            'merchantid' => '',
            'sourceid' => '',
            'utm_campaign' => '',
            'utm_medium' => '',
            'utm_source' => '',
            'contextparam' => '',
            // Display options
            'show_promo_code' => false
        );
        
        if (!get_option('nuvho_booking_mask_settings')) {
            add_option('nuvho_booking_mask_settings', $default_settings);
        }
        
        // Create reports table
        global $wpdb;
        $table_name = $wpdb->prefix . 'nuvho_booking_mask_reports';
        
        $charset_collate = $wpdb->get_charset_collate();
        
        $sql = "CREATE TABLE $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            date datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
            booking_engine varchar(50) NOT NULL,
            clicks mediumint(9) NOT NULL,
            conversions mediumint(9) NOT NULL,
            conversion_rate decimal(5,2) NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
        
        // Insert sample data
        $sample_data = array(
            array(
                'date' => current_time('mysql'),
                'booking_engine' => 'Simple Booking',
                'clicks' => 125,
                'conversions' => 28,
                'conversion_rate' => 22.40
            ),
            array(
                'date' => date('Y-m-d H:i:s', strtotime('-1 day')),
                'booking_engine' => 'Simple Booking',
                'clicks' => 98,
                'conversions' => 19,
                'conversion_rate' => 19.39
            )
        );
        
        foreach ($sample_data as $data) {
            $wpdb->insert($table_name, $data);
        }
    }
}