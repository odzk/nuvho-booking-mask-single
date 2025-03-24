<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://herobe.com
 * @since      2.0.0
 *
 * @package    Nuvho_Booking_Mask
 * @subpackage Nuvho_Booking_Mask/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two hooks for
 * enqueuing the admin-specific stylesheet and JavaScript.
 *
 * @package    Nuvho_Booking_Mask
 * @subpackage Nuvho_Booking_Mask/admin
 * @author     Odysseus Ambut / Herobe <contact@herobe.com>
 */
class Nuvho_Booking_Mask_Admin {

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
     * @param      string    $plugin_name       The name of this plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct($plugin_name, $version) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @since    2.0.0
     */
    public function enqueue_styles($hook) {
        // Only load on plugin pages
        if (strpos($hook, 'nuvho-booking-mask') === false) {
            return;
        }

        wp_enqueue_style('wp-color-picker');
        wp_enqueue_style($this->plugin_name, NUVHO_BOOKING_MASK_PLUGIN_URL . 'admin/css/nuvho-booking-mask-admin.css', array(), $this->version, 'all');
    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    2.0.0
     */
    public function enqueue_scripts($hook) {
        // Only load on plugin pages
        if (strpos($hook, 'nuvho-booking-mask') === false) {
            return;
        }

        wp_enqueue_script('wp-color-picker');
        wp_enqueue_script($this->plugin_name, NUVHO_BOOKING_MASK_PLUGIN_URL . 'admin/js/nuvho-booking-mask-admin.js', array('jquery', 'wp-color-picker'), $this->version, false);

        // Add Chart.js for reports page
        if (strpos($hook, 'nuvho-booking-mask-reports') !== false) {
            wp_enqueue_script('chart-js', 'https://cdn.jsdelivr.net/npm/chart.js', array(), '3.7.0', true);
            wp_enqueue_script('nuvho-booking-mask-reports', NUVHO_BOOKING_MASK_PLUGIN_URL . 'admin/js/nuvho-booking-mask-reports.js', array('jquery', 'chart-js'), $this->version, true);
        }
    }

    /**
     * Add plugin admin menu
     */
    public function add_plugin_admin_menu() {
        add_menu_page(
            'Nuvho Booking Mask',
            'Nuvho Booking Mask (Single)',
            'manage_options',
            'nuvho-booking-mask',
            array($this, 'display_settings_page'),
            'dashicons-calendar-alt',
            30
        );
        
        add_submenu_page(
            'nuvho-booking-mask',
            'Settings',
            'Settings',
            'manage_options',
            'nuvho-booking-mask',
            array($this, 'display_settings_page')
        );
        
        add_submenu_page(
            'nuvho-booking-mask',
            'Reports',
            'Reports',
            'manage_options',
            'nuvho-booking-mask-reports',
            array($this, 'display_reports_page')
        );
    }

    /**
     * Register settings
     */
    public function register_settings() {
        register_setting('nuvho_booking_mask_settings_group', 'nuvho_booking_mask_settings');
    }

    /**
     * Display the settings page
     */
    public function display_settings_page() {
        require_once NUVHO_BOOKING_MASK_PLUGIN_DIR . 'admin/partials/nuvho-booking-mask-admin-settings.php';
    }

    /**
     * Display the reports page
     */
    public function display_reports_page() {
        require_once NUVHO_BOOKING_MASK_PLUGIN_DIR . 'admin/partials/nuvho-booking-mask-admin-reports.php';
    }
    
    /**
     * Register REST API endpoints for AJAX operations
     */
    public function register_rest_routes() {
        register_rest_route('nuvho-booking-mask/v1', '/track-conversion', array(
            'methods' => 'POST',
            'callback' => array($this, 'track_conversion'),
            'permission_callback' => '__return_true'
        ));
    }
    
    /**
     * Track conversion callback for REST API
     */
    public function track_conversion($request) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'nuvho_booking_mask_reports';
        
        $json = $request->get_json_params();
        $booking_engine = sanitize_text_field($json['booking_engine']);
        
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
                    'conversions' => $record->conversions + 1,
                    'conversion_rate' => round((($record->conversions + 1) / $record->clicks) * 100, 2)
                ),
                array(
                    'id' => $record->id
                )
            );
        }
        
        return rest_ensure_response(array(
            'success' => true,
            'message' => 'Conversion tracked successfully'
        ));
    }
    
    /**
     * Export reports as CSV
     */
    public function export_reports() {
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.', 'nuvho-booking-mask'));
        }
        
        if (!isset($_GET['action']) || $_GET['action'] !== 'nuvho_export_report') {
            return;
        }
        
        // Verify nonce
        check_admin_referer('nuvho_export_report', 'nonce');
        
        $date_range = isset($_GET['date_range']) ? sanitize_text_field($_GET['date_range']) : '7days';
        $report_type = isset($_GET['report_type']) ? sanitize_text_field($_GET['report_type']) : 'daily';
        
        switch ($date_range) {
            case '30days':
                $days = 30;
                break;
            case '90days':
                $days = 90;
                break;
            case '1year':
                $days = 365;
                break;
            case '7days':
            default:
                $days = 7;
                break;
        }
        
        global $wpdb;
        $table_name = $wpdb->prefix . 'nuvho_booking_mask_reports';
        
        if ($report_type === 'daily') {
            $results = $wpdb->get_results(
                $wpdb->prepare(
                    "SELECT DATE(date) as report_date, 
                            SUM(clicks) as total_clicks, 
                            SUM(conversions) as total_conversions,
                            ROUND(AVG(conversion_rate), 2) as avg_conversion_rate
                    FROM $table_name 
                    WHERE date >= DATE_SUB(NOW(), INTERVAL %d DAY) 
                    GROUP BY DATE(date) 
                    ORDER BY DATE(date) ASC",
                    $days
                )
            );
        } else {
            $results = $wpdb->get_results(
                $wpdb->prepare(
                    "SELECT booking_engine, 
                            SUM(clicks) as total_clicks, 
                            SUM(conversions) as total_conversions,
                            ROUND(AVG(conversion_rate), 2) as avg_conversion_rate
                    FROM $table_name 
                    WHERE date >= DATE_SUB(NOW(), INTERVAL %d DAY) 
                    GROUP BY booking_engine 
                    ORDER BY SUM(clicks) DESC",
                    $days
                )
            );
        }
        
        // Set headers for CSV download
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="nuvho-booking-mask-report-' . date('Y-m-d') . '.csv"');
        
        // Create CSV
        $output = fopen('php://output', 'w');
        
        if ($report_type === 'daily') {
            // Headers for daily report
            fputcsv($output, array('Date', 'Clicks', 'Conversions', 'Conversion Rate (%)'));
            
            // Data rows
            foreach ($results as $row) {
                fputcsv($output, array(
                    $row->report_date,
                    $row->total_clicks,
                    $row->total_conversions,
                    $row->avg_conversion_rate
                ));
            }
        } else {
            // Headers for engine report
            fputcsv($output, array('Booking Engine', 'Clicks', 'Conversions', 'Conversion Rate (%)'));
            
            // Data rows
            foreach ($results as $row) {
                fputcsv($output, array(
                    $row->booking_engine,
                    $row->total_clicks,
                    $row->total_conversions,
                    $row->avg_conversion_rate
                ));
            }
        }
        
        fclose($output);
        exit;
    }
}