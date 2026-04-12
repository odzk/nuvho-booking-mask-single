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

        // Migrate deprecated booking engine selections
        $settings = get_option('nuvho_booking_mask_settings');
        if (!empty($settings)) {
            $updated = false;
            if ($settings['option'] === 'Simple Booking v1') {
                $settings['option'] = 'Simple Booking v2';
                $settings['url'] = 'https://www.simplebooking.it/ibe2/hotel';
                $updated = true;
            }
            if ($settings['option'] === 'Frome') {
                $settings['option'] = 'Simple Booking v2';
                $settings['url'] = 'https://www.simplebooking.it/ibe2/hotel';
                $updated = true;
            }
            if ($updated) {
                update_option('nuvho_booking_mask_settings', $settings);
            }
        }
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
        wp_localize_script($this->plugin_name, 'nuvhoAdminData', array(
            'public_css_url' => NUVHO_BOOKING_MASK_PLUGIN_URL . 'public/css/nuvho-booking-mask-public.css',
            'ajax_url'       => admin_url('admin-ajax.php'),
            'nonce'          => wp_create_nonce('nuvho_custom_engine_nonce'),
            'region_engine_map' => array(
                'europe'      => array('Simple Booking v2', 'Accor', 'Protel', 'MEWS', 'GuestCentric', 'Beds24', 'Bookassist', 'Cubilis', 'Clock PMS'),
                'asia_pacific' => array('Staah', 'SiteMinder', 'RMS', 'Little Hotelier'),
                'americas'    => array('Cloudbeds', 'TravelClick'),
            ),
        ));

        // Load the exact same assets the public mask uses
        wp_enqueue_style('daterangepicker', 'https://cdn.jsdelivr.net/npm/daterangepicker@3.1.0/daterangepicker.css', [], '3.1.0');
        wp_enqueue_script('moment', 'https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js', [], '2.29.4', false);
        wp_enqueue_script('daterangepicker', 'https://cdn.jsdelivr.net/npm/daterangepicker@3.1.0/daterangepicker.min.js', ['jquery', 'moment'], '3.1.0', false);

        // Load public CSS for accurate live preview
        wp_enqueue_style(
            $this->plugin_name . '-public-preview',
            NUVHO_BOOKING_MASK_PLUGIN_URL . 'public/css/nuvho-booking-mask-public.css',
            array(),
            $this->version
        );
        // Force public styles to win in preview
        $inline_css = '
            #nuvho-admin-preview * { all: revert !important; }
            #nuvho-admin-preview .nuvho-form-row,
            #nuvho-admin-preview .nuvho-form-field,
            #nuvho-admin-preview .nuvho-date-range,
            #nuvho-admin-preview .nuvho-submit-field,
            #nuvho-admin-preview .nuvho-guest-selector-container,
            #nuvho-admin-preview .nuvho-promo-field {
                /* Let public CSS handle layout */
            }
        ';
        wp_add_inline_style('nuvho-booking-mask-public-preview', $inline_css);

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
            'Nuvho Booking Mask',
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
        
        // REPORTS MENU - TEMPORARILY HIDDEN
        /*
        add_submenu_page(
            'nuvho-booking-mask',
            'Reports',
            'Reports',
            'manage_options',
            'nuvho-booking-mask-reports',
            array($this, 'display_reports_page')
        );
        */
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
     * AJAX handler: notify support when someone requests custom booking engine updates.
     */
    public function ajax_custom_notify() {
        check_ajax_referer('nuvho_custom_notify', 'nonce');

        $email = isset($_POST['email']) ? sanitize_email($_POST['email']) : '';
        if (!is_email($email)) {
            wp_send_json_error(array('message' => __('Invalid email address.', 'nuvho-booking-mask')));
        }

        $site_url   = get_site_url();
        $site_name  = get_bloginfo('name');
        $admin_mail = get_bloginfo('admin_email');
        $wp_version = get_bloginfo('version');
        $php_version = PHP_VERSION;
        $locale     = get_locale();
        $plugins    = array();

        if (function_exists('get_plugins')) {
            foreach (get_plugins() as $file => $data) {
                $plugins[] = $data['Name'] . ' ' . $data['Version']
                    . (is_plugin_active($file) ? ' (active)' : ' (inactive)');
            }
        }

        $subject = sprintf('[Nuvho] Custom Booking Engine Interest — %s', $site_name);

        $body  = "A user has expressed interest in the Custom Booking Engine feature.\n\n";
        $body .= "Notification Email : {$email}\n\n";
        $body .= "--- Site Information ---\n";
        $body .= "Site Name   : {$site_name}\n";
        $body .= "Site URL    : {$site_url}\n";
        $body .= "Admin Email : {$admin_mail}\n";
        $body .= "Locale      : {$locale}\n\n";
        $body .= "--- Environment ---\n";
        $body .= "WordPress   : {$wp_version}\n";
        $body .= "PHP         : {$php_version}\n\n";
        $body .= "--- Active & Inactive Plugins ---\n";
        $body .= implode("\n", $plugins) . "\n";

        $sent = wp_mail(
            'support@nuvho.com',
            $subject,
            $body,
            array('Content-Type: text/plain; charset=UTF-8')
        );

        if ($sent) {
            wp_send_json_success(array(
                'message' => __("Thanks! We'll notify you at {$email} when this feature launches.", 'nuvho-booking-mask')
            ));
        } else {
            wp_send_json_error(array(
                'message' => __('Could not send your request. Please try again later.', 'nuvho-booking-mask')
            ));
        }
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

    /**
     * AJAX handler: Fetch engine parameters via Claude API
     */
    public function ajax_fetch_engine_params() {
        check_ajax_referer('nuvho_custom_engine_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => 'Insufficient permissions.'));
        }

        $settings = get_option('nuvho_booking_mask_settings');
        $api_key = isset($settings['anthropic_api_key']) ? $settings['anthropic_api_key'] : '';

        if (empty($api_key)) {
            wp_send_json_error(array('message' => 'Anthropic API key not configured. Please save your API key first.'));
        }

        $url = sanitize_url(wp_unslash($_POST['url']));
        $mode = sanitize_text_field(wp_unslash($_POST['mode']));
        $sample_url = isset($_POST['sample_url']) ? sanitize_url(wp_unslash($_POST['sample_url'])) : '';

        if (empty($url)) {
            wp_send_json_error(array('message' => 'URL is required.'));
        }

        $system_prompt = $this->get_claude_system_prompt();

        if ($mode === 'identify') {
            $user_message = 'Identify the hotel booking engine from this base URL and return its parameter structure: ' . $url;
        } else {
            $user_message = "Base URL: " . $url . "\nSample booking URL with parameters: " . $sample_url . "\nParse the sample URL and extract all query parameters, mapping them to their booking function.";
        }

        $result = $this->call_claude_api($api_key, $system_prompt, $user_message);

        if (is_wp_error($result)) {
            wp_send_json_error(array('message' => $result->get_error_message()));
        }

        wp_send_json_success($result);
    }

    /**
     * Call the Anthropic Claude API
     */
    private function call_claude_api($api_key, $system_prompt, $user_message) {
        $response = wp_remote_post('https://api.anthropic.com/v1/messages', array(
            'timeout' => 30,
            'headers' => array(
                'Content-Type'      => 'application/json',
                'x-api-key'         => $api_key,
                'anthropic-version' => '2023-06-01',
            ),
            'body' => wp_json_encode(array(
                'model'      => 'claude-sonnet-4-20250514',
                'max_tokens' => 2048,
                'system'     => $system_prompt,
                'messages'   => array(
                    array('role' => 'user', 'content' => $user_message)
                ),
            )),
        ));

        if (is_wp_error($response)) {
            return $response;
        }

        $status_code = wp_remote_retrieve_response_code($response);
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        if ($status_code !== 200) {
            $error_msg = isset($data['error']['message']) ? $data['error']['message'] : 'API request failed (HTTP ' . $status_code . ')';
            return new WP_Error('api_error', $error_msg);
        }

        // Extract text content from Claude response
        $text = '';
        if (isset($data['content'])) {
            foreach ($data['content'] as $block) {
                if ($block['type'] === 'text') {
                    $text .= $block['text'];
                }
            }
        }

        // Parse JSON from Claude's response (strip code fences if present)
        $text = trim($text);
        if (preg_match('/```(?:json)?\s*([\s\S]*?)```/', $text, $matches)) {
            $text = trim($matches[1]);
        }

        $parsed = json_decode($text, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return new WP_Error('parse_error', 'Failed to parse API response as JSON. Raw response: ' . substr($text, 0, 500));
        }

        return $parsed;
    }

    /**
     * Get the system prompt for Claude API
     */
    private function get_claude_system_prompt() {
        return 'You are a hotel booking engine URL parameter expert. Your task is to identify hotel booking engines from URLs and return their complete URL parameter structure as JSON.

IMPORTANT: Return ONLY valid JSON. No markdown formatting, no explanation text, no code fences. Output raw JSON only.

When given a base URL (identify mode):
1. Analyze the domain and URL path to identify the booking engine provider
2. If recognized, set "identified": true and provide the complete parameter structure from your knowledge
3. If not recognized, set "identified": false with an empty parameters array

When given a base URL + sample booking URL (parse mode):
1. Parse all query string parameters from the sample URL
2. Analyze parameter names and their values to determine what each represents
3. Map each parameter to its booking function (check-in date, adults count, etc.)
4. Always set "identified": true since you have concrete data to work with

Required JSON output structure:
{
    "identified": true or false,
    "engine_name": "Name of the booking engine, or \'Unknown\'",
    "base_url_pattern": "The base URL without query parameters or dynamic path segments",
    "method": "GET or POST",
    "hotel_id_in_path": true or false,
    "hotel_id_path_position": "Description of where hotel ID appears in URL path, e.g. \'appended as last path segment\' or \'\' if not in path",
    "has_promo": true or false,
    "parameters": [
        {
            "name": "the exact query parameter name as it appears in the URL",
            "value_source": "one of the allowed source values (see below)",
            "format": "precise format description (see examples below)",
            "description": "human-readable explanation of what this parameter does",
            "default_value": "default or static value if applicable, otherwise empty string",
            "required": true or false
        }
    ]
}

Allowed value_source values:
- "checkin" — Maps to the user\'s selected check-in date
- "checkout" — Maps to the user\'s selected check-out date
- "adults" — Maps to the number of adults selected by the user
- "children" — Maps to the number of children selected by the user
- "rooms" — Maps to the number of rooms (typically defaults to 1)
- "promo" — Maps to the promotional/coupon code input field
- "hotel_id" — Maps to the configured hotel or property identifier
- "language" — Maps to the configured display language
- "currency" — Maps to the configured currency
- "static" — A fixed value that never changes (provide the value in default_value)
- "custom" — A value the hotel administrator needs to configure manually

Format description examples (be this precise):
- Dates: "YYYY-MM-DD", "DD/MM/YYYY", "MM/DD/YYYY", "YYYYMMDD", "DD-MM-YYYY"
- Guest counts: "integer" (just the number, e.g. "3"), "repeated-letter:A" (e.g. 3 adults = "A,A,A")
- Language: "ISO 639-1 lowercase" (en, fr, de), "ISO 639-1 uppercase" (EN, FR, DE), "full lowercase" (english, french)
- Currency: "ISO 4217 uppercase" (USD, EUR), "ISO 4217 lowercase" (usd, eur)
- Boolean: "1 or 0", "true or false", "yes or no"
- String: "freetext"

Guidelines:
- Include ALL known parameters for the identified engine, including optional ones
- Mark each parameter as required or optional
- If a parameter has a known default or static value, include it
- For engines you recognize, draw on your complete knowledge of their booking URL structure
- For parsed sample URLs, infer the value_source from the parameter name and its value
- Be comprehensive — it is better to include an extra parameter than to miss one';
    }
}