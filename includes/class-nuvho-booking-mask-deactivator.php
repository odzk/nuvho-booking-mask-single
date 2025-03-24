<?php
/**
 * Fired during plugin deactivation
 *
 * @link       https://herobe.com
 * @since      2.0.0
 *
 * @package    Nuvho_Booking_Mask
 * @subpackage Nuvho_Booking_Mask/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      2.0.0
 * @package    Nuvho_Booking_Mask
 * @subpackage Nuvho_Booking_Mask/includes
 * @author     Odysseus Ambut / Herobe <contact@herobe.com>
 */
class Nuvho_Booking_Mask_Deactivator {

    /**
     * Plugin deactivation tasks
     *
     * @since    2.0.0
     */
    public static function deactivate() {
        // Deactivation tasks can be added here if needed
        // We're not removing database tables or options on deactivation
        // to preserve user data
    }
}