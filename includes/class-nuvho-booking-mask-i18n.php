<?php
/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://herobe.com
 * @since      2.0.0
 *
 * @package    Nuvho_Booking_Mask
 * @subpackage Nuvho_Booking_Mask/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      2.0.0
 * @package    Nuvho_Booking_Mask
 * @subpackage Nuvho_Booking_Mask/includes
 * @author     Odysseus Ambut / Herobe <contact@herobe.com>
 */
class Nuvho_Booking_Mask_i18n {

    /**
     * Load the plugin text domain for translation.
     *
     * @since    2.0.0
     */
    public function load_plugin_textdomain() {
        load_plugin_textdomain(
            'nuvho-booking-mask',
            false,
            dirname(dirname(plugin_basename(__FILE__))) . '/languages/'
        );
    }
}