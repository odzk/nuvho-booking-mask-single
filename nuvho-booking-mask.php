<?php
/**
 * @wordpress-plugin
 * Plugin Name:       Nuvho Booking Mask (Single)
 * Plugin URI:        https://support.herobe.com/nuvho-booking-mask/
 * Description:       Nuvho Single Booking Mask. Universal booking mask for single property. Easy to use, no coding required. Customizable, responsive, multilingual, GDPR compliant.
 * Version:           2.1.2
 * Author:            Odysseus Ambut / Herobe
 * Author URI:        https://herobe.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       nuvho-booking-mask
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

// Define plugin constants
define('NUVHO_BOOKING_MASK_VERSION', '2.1.2');
define('NUVHO_BOOKING_MASK_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('NUVHO_BOOKING_MASK_PLUGIN_URL', plugin_dir_url(__FILE__));

/**
 * The code that runs during plugin activation.
 */
function activate_nuvho_booking_mask() {
    require_once NUVHO_BOOKING_MASK_PLUGIN_DIR . 'includes/class-nuvho-booking-mask-activator.php';
    Nuvho_Booking_Mask_Activator::activate();
}
register_activation_hook(__FILE__, 'activate_nuvho_booking_mask');

/**
 * The code that runs during plugin deactivation.
 */
function deactivate_nuvho_booking_mask() {
    require_once NUVHO_BOOKING_MASK_PLUGIN_DIR . 'includes/class-nuvho-booking-mask-deactivator.php';
    Nuvho_Booking_Mask_Deactivator::deactivate();
}
register_deactivation_hook(__FILE__, 'deactivate_nuvho_booking_mask');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require_once NUVHO_BOOKING_MASK_PLUGIN_DIR . 'includes/class-nuvho-booking-mask.php';

/**
 * Begins execution of the plugin.
 */
function run_nuvho_booking_mask() {
    $plugin = new Nuvho_Booking_Mask();
    $plugin->run();
}

/**
 * Plugin update checker
 */

require_once NUVHO_BOOKING_MASK_PLUGIN_DIR . 'vendor/plugin-update-checker/plugin-update-checker.php';
use YahnisElsts\PluginUpdateChecker\v5\PucFactory;

$myUpdateChecker = PucFactory::buildUpdateChecker(
    'https://updates.herobe.com/nuvho-booking-mask-single.json',
    __FILE__, 'nuvho-booking-mask-single');
run_nuvho_booking_mask();
