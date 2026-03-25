<?php
/**
 * Provide a admin area view for the plugin settings
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://herobe.com
 * @since      2.0.0
 *
 * @package    Nuvho_Booking_Mask
 * @subpackage Nuvho_Booking_Mask/admin/partials
 */

// Get saved settings
$settings = get_option('nuvho_booking_mask_settings');

// Theme presets
$theme_presets = array(
    'default' => array(
        'name' => 'Default Theme',
        'background_color' => '#ffffff',
        'background_opacity' => '100%',
        'button_color' => '#0073aa',
        'button_text_color' => '#ffffff',
        'datepicker_color' => '#0073aa',
        'datepicker_text_color' => '#ffffff',
        'font_color' => '#333333',
        'button_text' => 'Check Availability',
        'button_border_radius' => 'Rounded',
        'booking_mask_border_radius' => 'Rounded',
        'font' => 'Default'
    ),
    'ocean_breeze' => array(
        'name' => 'Ocean Breeze',
        'background_color' => '#f8fafb',
        'background_opacity' => '100%',
        'button_color' => '#5a9fd4',
        'button_text_color' => '#ffffff',
        'datepicker_color' => '#5a9fd4',
        'datepicker_text_color' => '#ffffff',
        'font_color' => '#2d3748',
        'button_text' => 'Check Availability',
        'button_border_radius' => 'Rounded',
        'booking_mask_border_radius' => 'Rounded',
        'font' => 'Arial'
    ),
    'sunset_coral' => array(
        'name' => 'Sunset Coral',
        'background_color' => '#fff8f6',
        'background_opacity' => '100%',
        'button_color' => '#ff6b47',
        'button_text_color' => '#ffffff',
        'datepicker_color' => '#ff6b47',
        'datepicker_text_color' => '#ffffff',
        'font_color' => '#2d1810',
        'button_text' => 'Book Now',
        'button_border_radius' => 'Rounded',
        'booking_mask_border_radius' => 'Rounded',
        'font' => 'Helvetica'
    ),
    'forest_green' => array(
        'name' => 'Forest Green',
        'background_color' => '#f0fdf4',
        'background_opacity' => '100%',
        'button_color' => '#10b981',
        'button_text_color' => '#ffffff',
        'datepicker_color' => '#10b981',
        'datepicker_text_color' => '#ffffff',
        'font_color' => '#1f2937',
        'button_text' => 'Reserve Room',
        'button_border_radius' => 'Rounded',
        'booking_mask_border_radius' => 'Rounded',
        'font' => 'Verdana'
    ),
    'royal_purple' => array(
        'name' => 'Royal Purple',
        'background_color' => '#faf5ff',
        'background_opacity' => '100%',
        'button_color' => '#8b5cf6',
        'button_text_color' => '#ffffff',
        'datepicker_color' => '#8b5cf6',
        'datepicker_text_color' => '#ffffff',
        'font_color' => '#2d1b69',
        'button_text' => 'Book Stay',
        'button_border_radius' => 'Rounded',
        'booking_mask_border_radius' => 'Rounded',
        'font' => 'Georgia'
    ),
    'midnight_dark' => array(
        'name' => 'Midnight Dark',
        'background_color' => '#1f2937',
        'background_opacity' => '100%',
        'button_color' => '#60a5fa',
        'button_text_color' => '#ffffff',
        'datepicker_color' => '#60a5fa',
        'datepicker_text_color' => '#ffffff',
        'font_color' => '#f9fafb',
        'button_text' => 'Check Rates',
        'button_border_radius' => 'Rounded',
        'booking_mask_border_radius' => 'Rounded',
        'font' => 'Arial'
    )
);

// Booking providers
$booking_providers = array(
    'Simple Booking v2',
    'Accor',
    'Cloudbeds',
    'Staah',
    'SiteMinder',
    'RMS',
    'Protel',
    'MEWS',
    'TravelClick',
    'Custom'
);

// Languages
$languages = array(
    'English (US)',
    'English (UK)',
    'Italian',
    'French',
    'German',
    'Spanish',
    'Portuguese',
    'Dutch',
    'Russian',
    'Chinese',
    'Japanese'
);

// Currencies
$currencies = array(
    'USD',
    'EUR',
    'GBP',
    'AUD',
    'CAD',
    'CHF',
    'JPY',
    'CNY',
    'RUB'
);

// Border radius options
$border_radius_options = array(
    'Square',
    'Rounded',
    'Pill'
);

// Fonts
$fonts = array(
    'Default',
    'Arial',
    'Helvetica',
    'Times New Roman',
    'Georgia',
    'Verdana',
    'Courier'
);

// Opacity options
$opacity_options = array(
    '100%',
    '90%',
    '80%',
    '70%',
    '60%',
    '50%'
);

?>
<div class="wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
    
   <!-- Live Preview - completely separate section at the top -->
<div class="nuvho-preview-section">
    <div class="nuvho-preview-card">
        <h2>Live Preview</h2>
        <div class="nuvho-booking-preview" id="nuvho-booking-mask-public-preview">
            <?php 
            // ---- same calculations as public ----
            $border_radius = $settings['booking_mask_border_radius'] === 'Rounded' ? '8px' : ($settings['booking_mask_border_radius'] === 'Pill' ? '20px' : '0');
            $button_radius = $settings['button_border_radius'] === 'Rounded' ? '8px' : ($settings['button_border_radius'] === 'Pill' ? '20px' : '0');
            $opacity = str_replace('%', '', $settings['background_opacity']) / 100;

            // convert hex → rgba
            $bg = $settings['background_color'];
            $rgba = $bg;
            if (preg_match('/^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i', $bg, $m)) {
                $r = hexdec($m[1]); $g = hexdec($m[2]); $b = hexdec($m[3]);
                $rgba = "rgba($r,$g,$b,$opacity)";
            }
            $accent_color = esc_attr($rgba);
            ?>
            <div id="nuvho-preview-container" class="nuvho-booking-mask-container nuvho-public-mask"
                 style="background-color:<?php echo esc_attr($rgba); ?>;opacity:<?php echo esc_attr($opacity); ?>;border-radius:<?php echo esc_attr($border_radius); ?>;">
                
                <div class="nuvho-booking-form nuvho-public-form"
                     style="color:<?php echo esc_attr($settings['font_color']); ?>;
                            font-family:<?php echo $settings['font'] === 'Default' ? 'inherit' : esc_attr($settings['font']); ?>;">
                    
                    <form id="nuvho-preview-form" class="nuvho-public-booking-form" onsubmit="return false;">
                        <div class="nuvho-form-row">

                            <!-- 1. DATE PICKER -->
                            <div class="nuvho-form-field nuvho-date-range">
                                <label for="nuvho-preview-date-picker"><?php esc_html_e('Check-in / Check-out', 'nuvho-booking-mask'); ?></label>
                                <div class="nuvho-date-inputs">
                                    <input type="text" id="nuvho-preview-date-picker" class="nuvho-date-picker" readonly>
                                    <input type="hidden" id="nuvho-preview-checkin" name="arrival">
                                    <input type="hidden" id="nuvho-preview-checkout" name="departure">
                                </div>
                            </div>

                            <!-- 2. GUEST SELECTOR (exact copy of public) -->
                            <div class="nuvho-form-field nuvho-guest-selector-container">
                                <label><?php esc_html_e('Persons:', 'nuvho-booking-mask'); ?></label>
                                <div class="nuvho-guest-summary">
                                    <button type="button" class="nuvho-guest-trigger">
                                        <span class="nuvho-guest-count">
                                            <strong class="nuvho-adults-count">2</strong> 
                                            <span><?php esc_html_e('ADULTS', 'nuvho-booking-mask'); ?></span>
                                            + <strong class="nuvho-kids-count">0</strong> 
                                            <span><?php esc_html_e('KIDS', 'nuvho-booking-mask'); ?></span>
                                        </span>
                                        <span class="nuvho-selector-toggle">&#9660;</span>
                                    </button>
                                </div>

                                <!-- Stepper modal (default) -->
                                <div class="nuvho-guest-modal nuvho-guest-modal-stepper" id="nuvho-preview-guest-modal">
                                    <div class="nuvho-modal-content">
                                        <div class="nuvho-room-title"><?php esc_html_e('Guests', 'nuvho-booking-mask'); ?></div>

                                        <!-- Adults -->
                                        <div class="nuvho-guest-row">
                                            <div class="nuvho-guest-label">
                                                <span class="nuvho-adults-number">2</span>
                                                <span class="nuvho-label-suffix"><?php esc_html_e('Adults', 'nuvho-booking-mask'); ?></span>
                                            </div>
                                            <div class="nuvho-stepper-controls">
                                                <button type="button" class="nuvho-circle-btn nuvho-decrease" data-target="adults" style="background-color: <?php echo $accent_color; ?>;"><span class="nuvho-btn-icon">-</span></button>
                                                <button type="button" class="nuvho-circle-btn nuvho-increase" data-target="adults" style="background-color: <?php echo $accent_color; ?>;"><span class="nuvho-btn-icon">+</span></button>
                                            </div>
                                        </div>

                                        <!-- Kids -->
                                        <div class="nuvho-guest-row">
                                            <div class="nuvho-guest-label">
                                                <span class="nuvho-kids-number">0</span>
                                                <span class="nuvho-label-suffix"><?php esc_html_e('Kids', 'nuvho-booking-mask'); ?></span>
                                            </div>
                                            <div class="nuvho-stepper-controls">
                                                <button type="button" class="nuvho-circle-btn nuvho-decrease" data-target="kids" style="background-color: <?php echo $accent_color; ?>;"><span class="nuvho-btn-icon">-</span></button>
                                                <button type="button" class="nuvho-circle-btn nuvho-increase" data-target="kids" style="background-color: <?php echo $accent_color; ?>;"><span class="nuvho-btn-icon">+</span></button>
                                            </div>
                                        </div>

                                        <div class="nuvho-modal-buttons">
                                            <button type="button" class="nuvho-cancel-btn"><?php esc_html_e('Cancel', 'nuvho-booking-mask'); ?></button>
                                            <button type="button" class="nuvho-done-btn" style="background-color: <?php echo $accent_color; ?>;"><?php esc_html_e('Done', 'nuvho-booking-mask'); ?></button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Dropdown modal (shown when guest_selection_type is dropdown) -->
                                <div class="nuvho-guest-modal nuvho-guest-modal-dropdown" id="nuvho-preview-guest-modal-dropdown" style="display:none;">
                                    <div class="nuvho-modal-content">
                                        <div class="nuvho-room-title"><?php esc_html_e('Guests', 'nuvho-booking-mask'); ?></div>
                                        <div class="nuvho-dropdown-row">
                                            <div class="nuvho-dropdown-group">
                                                <label><?php esc_html_e('Adults', 'nuvho-booking-mask'); ?></label>
                                                <select class="nuvho-guest-dropdown" data-target="adults">
                                                    <?php for ($i = 1; $i <= 10; $i++) : ?>
                                                        <option value="<?php echo $i; ?>" <?php selected(2, $i); ?>><?php echo $i; ?></option>
                                                    <?php endfor; ?>
                                                </select>
                                            </div>
                                            <div class="nuvho-dropdown-group">
                                                <label><?php esc_html_e('Kids', 'nuvho-booking-mask'); ?></label>
                                                <select class="nuvho-guest-dropdown" data-target="kids">
                                                    <?php for ($i = 0; $i <= 10; $i++) : ?>
                                                        <option value="<?php echo $i; ?>" <?php selected(0, $i); ?>><?php echo $i; ?></option>
                                                    <?php endfor; ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="nuvho-modal-buttons">
                                            <button type="button" class="nuvho-cancel-btn"><?php esc_html_e('Cancel', 'nuvho-booking-mask'); ?></button>
                                            <button type="button" class="nuvho-done-btn" style="background-color: <?php echo $accent_color; ?>;"><?php esc_html_e('Done', 'nuvho-booking-mask'); ?></button>
                                        </div>
                                    </div>
                                </div>

                                <input type="hidden" id="nuvho-adults-input" name="adults" value="2">
                                <input type="hidden" id="nuvho-kids-input" name="children" value="0">
                            </div>

                            <!-- 3. PROMO CODE (always in DOM, hidden by default) -->
                            <div class="nuvho-form-field nuvho-promo-field" id="nuvho-preview-promo-field" style="display:none;">
                                <label for="nuvho-preview-promo"><?php esc_html_e('Promo Code', 'nuvho-booking-mask'); ?></label>
                                <input type="text" id="nuvho-preview-promo" name="coupon" placeholder="<?php esc_attr_e('Enter promo code', 'nuvho-booking-mask'); ?>">
                            </div>

                            <!-- 4. SUBMIT BUTTON -->
                            <div class="nuvho-form-field nuvho-submit-field">
                                <button type="button" id="nuvho-preview-button"
                                        style="background-color:<?php echo esc_attr($settings['button_color']); ?>;
                                               color:<?php echo esc_attr($settings['button_text_color']); ?>;
                                               border-radius:<?php echo esc_attr($button_radius); ?>;">
                                    <?php echo esc_html($settings['button_text']); ?>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
    </div>
</div>

<!-- Settings form - PROPERLY STRUCTURED -->
    <form method="post" action="options.php">
        <?php settings_fields('nuvho_booking_mask_settings_group'); ?>
        
        <div class="nuvho-admin-columns">
            <div class="nuvho-admin-column">
                <div class="nuvho-settings-card">
                    <h2>Basic Settings</h2>
                    <table class="form-table">
                        <tr>
                            <th scope="row">Region:</th>
                            <td>
                                <select id="nuvho-region-filter">
                                    <option value="all">All Regions</option>
                                    <option value="europe">Europe</option>
                                    <option value="asia_pacific">Asia-Pacific</option>
                                    <option value="americas">Americas</option>
                                </select>
                                <p class="description">Filter booking engines by region.</p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">Booking Engine:</th>
                            <td>
                                <select name="nuvho_booking_mask_settings[option]" id="nuvho-booking-option">
                                    <?php foreach ($booking_providers as $provider) : ?>
                                        <option value="<?php echo esc_attr($provider); ?>" <?php selected($settings['option'], $provider); ?>><?php echo esc_html($provider); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                        </tr>
                        
                        <tr>
                            <th scope="row">Enter URL:</th>
                            <td>
                                <input type="text" name="nuvho_booking_mask_settings[url]" value="<?php echo esc_attr($settings['url']); ?>" class="regular-text" />
                            </td>
                        </tr>
                        
                        <tr>
                            <th scope="row">Choose language:</th>
                            <td>
                                <select name="nuvho_booking_mask_settings[language]">
                                    <?php foreach ($languages as $language) : ?>
                                        <option value="<?php echo esc_attr($language); ?>" <?php selected($settings['language'], $language); ?>><?php echo esc_html($language); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                        </tr>
                        
                        <tr>
                            <th scope="row">Choose currency:</th>
                            <td>
                                <select name="nuvho_booking_mask_settings[currency]">
                                    <?php foreach ($currencies as $currency) : ?>
                                        <option value="<?php echo esc_attr($currency); ?>" <?php selected($settings['currency'], $currency); ?>><?php echo esc_html($currency); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                        </tr>
                        
                        <tr>
                            <th scope="row"><span id="hotel-id-label">Hotel ID:</span></th>
                            <td>
                                <input type="text" name="nuvho_booking_mask_settings[hotel_id]" value="<?php echo esc_attr($settings['hotel_id']); ?>" class="regular-text" />
                            </td>
                        </tr>
                    </table>
                </div>
                
                <!-- Simple Booking specific settings -->
                <div id="simple-booking-specific-settings" class="nuvho-settings-card" style="<?php echo (strpos($settings['option'], 'Simple Booking') !== false) ? 'display: block;' : 'display: none;'; ?>">
                    <h2>Simple Booking Settings</h2>
                    <table class="form-table">
                        <tr>
                            <th scope="row">Show Promo Code Field:</th>
                            <td>
                                <label>
                                    <input type="checkbox" name="nuvho_booking_mask_settings[show_promo_code]" value="1" id="nuvho-show-promo-code" <?php checked(isset($settings['show_promo_code']) && $settings['show_promo_code']); ?> />
                                    Display a promo code field in the booking form
                                </label>
                                <p class="description">This allows users to enter their own promotional codes.</p>
                            </td>
                        </tr>
                    </table>
                </div>
                
                <!-- Accor-specific settings -->
                <div id="accor-specific-settings" class="nuvho-settings-card" style="<?php echo ($settings['option'] === 'Accor') ? 'display: block;' : 'display: none;'; ?>">
                    <h2>Accor Specific Settings</h2>
                    <table class="form-table">
                        <tr>
                            <th scope="row">Code Hotel:</th>
                            <td>
                                <input type="text" name="nuvho_booking_mask_settings[code_hotel]" value="<?php echo isset($settings['code_hotel']) ? esc_attr($settings['code_hotel']) : ''; ?>" class="regular-text" />
                                <p class="description">Specifies the unique identifier for a hotel.</p>
                            </td>
                        </tr>
                        
                        <tr>
                            <th scope="row">Goto:</th>
                            <td>
                                <input type="text" name="nuvho_booking_mask_settings[goto]" value="<?php echo isset($settings['goto']) ? esc_attr($settings['goto']) : 'fiche_hotel'; ?>" class="regular-text" />
                                <p class="description">Determines the destination page or action, such as fiche_hotel to view the hotel's information page.</p>
                            </td>
                        </tr>
                        
                        <tr>
                            <th scope="row">Merchant ID:</th>
                            <td>
                                <input type="text" name="nuvho_booking_mask_settings[merchantid]" value="<?php echo isset($settings['merchantid']) ? esc_attr($settings['merchantid']) : ''; ?>" class="regular-text" />
                                <p class="description">Identifies the merchant or affiliate source.</p>
                            </td>
                        </tr>
                        
                        <tr>
                            <th scope="row">Source ID:</th>
                            <td>
                                <input type="text" name="nuvho_booking_mask_settings[sourceid]" value="<?php echo isset($settings['sourceid']) ? esc_attr($settings['sourceid']) : ''; ?>" class="regular-text" />
                                <p class="description">Indicates the source of the traffic or referral.</p>
                            </td>
                        </tr>
                        
                        <tr>
                            <th scope="row">UTM Campaign:</th>
                            <td>
                                <input type="text" name="nuvho_booking_mask_settings[utm_campaign]" value="<?php echo isset($settings['utm_campaign']) ? esc_attr($settings['utm_campaign']) : ''; ?>" class="regular-text" />
                                <p class="description">UTM parameter used for tracking marketing campaigns.</p>
                            </td>
                        </tr>
                        
                        <tr>
                            <th scope="row">UTM Medium:</th>
                            <td>
                                <input type="text" name="nuvho_booking_mask_settings[utm_medium]" value="<?php echo isset($settings['utm_medium']) ? esc_attr($settings['utm_medium']) : ''; ?>" class="regular-text" />
                                <p class="description">UTM parameter used for tracking marketing medium.</p>
                            </td>
                        </tr>
                        
                        <tr>
                            <th scope="row">UTM Source:</th>
                            <td>
                                <input type="text" name="nuvho_booking_mask_settings[utm_source]" value="<?php echo isset($settings['utm_source']) ? esc_attr($settings['utm_source']) : ''; ?>" class="regular-text" />
                                <p class="description">UTM parameter used for tracking marketing source.</p>
                            </td>
                        </tr>
                        
                        <tr>
                            <th scope="row">Context Param:</th>
                            <td>
                                <input type="text" name="nuvho_booking_mask_settings[contextparam]" value="<?php echo isset($settings['contextparam']) ? esc_attr($settings['contextparam']) : ''; ?>" class="regular-text" />
                                <p class="description">Provides additional context or parameters for the action.</p>
                            </td>
                        </tr>
                    </table>
                </div>

                <!-- Custom Engine Settings -->
                <div id="custom-engine-settings" class="nuvho-settings-card" style="<?php echo ($settings['option'] === 'Custom') ? 'display: block;' : 'display: none;'; ?>">
                    <h2>Custom Booking Engine Settings</h2>
                    <table class="form-table">
                        <tr>
                            <th scope="row">Anthropic API Key:</th>
                            <td>
                                <input type="password" name="nuvho_booking_mask_settings[anthropic_api_key]" value="<?php echo esc_attr(isset($settings['anthropic_api_key']) ? $settings['anthropic_api_key'] : ''); ?>" class="regular-text" autocomplete="off" />
                                <p class="description">Required for auto-detecting booking engine parameters. <a href="https://console.anthropic.com/" target="_blank">Get API key</a></p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">Fetch Parameters:</th>
                            <td>
                                <button type="button" id="nuvho-fetch-params-btn" class="button button-secondary">Fetch Parameters</button>
                                <span id="nuvho-fetch-status" style="margin-left:10px;"></span>
                                <p class="description">Analyzes the URL above to detect booking engine parameters automatically.</p>
                            </td>
                        </tr>
                        <tr id="nuvho-sample-url-row" style="display:none;">
                            <th scope="row">Sample Booking URL:</th>
                            <td>
                                <input type="text" id="nuvho-sample-url" class="regular-text" placeholder="Paste a full booking URL with parameters" />
                                <button type="button" id="nuvho-parse-sample-btn" class="button button-secondary">Parse URL</button>
                                <p class="description">If the engine was not recognized, paste a complete booking URL with all query parameters filled in.</p>
                            </td>
                        </tr>
                    </table>

                    <!-- Parameter Editor (populated by AJAX response) -->
                    <div id="nuvho-param-editor" style="display:none;">
                        <h3>Detected Parameters</h3>
                        <p id="nuvho-detected-engine-name" style="font-style:italic;"></p>
                        <table class="widefat" id="nuvho-param-table">
                            <thead>
                                <tr>
                                    <th>Parameter Name</th>
                                    <th>Maps To</th>
                                    <th>Format</th>
                                    <th>Description</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                        <p style="margin-top:10px;">
                            <label>
                                <input type="checkbox" id="nuvho-custom-has-promo" name="nuvho_booking_mask_settings[custom_has_promo]" value="1" <?php checked(isset($settings['custom_has_promo']) && $settings['custom_has_promo']); ?> />
                                Engine supports promo/coupon codes
                            </label>
                        </p>
                    </div>

                    <!-- Hidden field storing serialized custom config as JSON -->
                    <input type="hidden" name="nuvho_booking_mask_settings[custom_engine_config]" id="nuvho-custom-engine-config" value="<?php echo esc_attr(isset($settings['custom_engine_config']) ? $settings['custom_engine_config'] : ''); ?>" />
                </div>
            </div>

            <div class="nuvho-admin-column">
                <div class="nuvho-settings-card">
                    <h2>Appearance Settings</h2>
                    <table class="form-table">
                        <tr>
                            <th scope="row">Theme Preset:</th>
                            <td>
                                <select name="nuvho_booking_mask_settings[theme_preset]" id="nuvho-theme-selector">
                                    <option value="">Select a theme preset...</option>
                                    <?php foreach ($theme_presets as $theme_key => $theme_data) : ?>
                                        <option value="<?php echo esc_attr($theme_key); ?>" <?php selected(isset($settings['theme_preset']) ? $settings['theme_preset'] : '', $theme_key); ?>><?php echo esc_html($theme_data['name']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <p class="description">Select a preset theme to automatically configure appearance settings, or customize manually below.</p>
                            </td>
                        </tr>
                        
                        <tr>
                            <th scope="row">Background Color:</th>
                            <td>
                                <input type="text" name="nuvho_booking_mask_settings[background_color]" value="<?php echo esc_attr($settings['background_color']); ?>" class="nuvho-color-picker" id="nuvho-bg-color" />
                            </td>
                        </tr>
                        
                        <tr>
                            <th scope="row">Background Opacity:</th>
                            <td>
                                <select name="nuvho_booking_mask_settings[background_opacity]" id="nuvho-bg-opacity">
                                    <?php foreach ($opacity_options as $opacity) : ?>
                                        <option value="<?php echo esc_attr($opacity); ?>" <?php selected($settings['background_opacity'], $opacity); ?>><?php echo esc_html($opacity); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                        </tr>
                        
                        <tr>
                            <th scope="row">Button Color:</th>
                            <td>
                                <input type="text" name="nuvho_booking_mask_settings[button_color]" value="<?php echo esc_attr($settings['button_color']); ?>" class="nuvho-color-picker" id="nuvho-button-color" />
                            </td>
                        </tr>
                        
                        <tr>
                            <th scope="row">Font Color:</th>
                            <td>
                                <input type="text" name="nuvho_booking_mask_settings[font_color]" value="<?php echo esc_attr($settings['font_color']); ?>" class="nuvho-color-picker" id="nuvho-font-color" />
                            </td>
                        </tr>
                        
                        <tr>
                            <th scope="row">Button Text:</th>
                            <td>
                                <input type="text" name="nuvho_booking_mask_settings[button_text]" value="<?php echo esc_attr($settings['button_text']); ?>" class="regular-text" id="nuvho-button-text" />
                            </td>
                        </tr>
                        
                        <tr>
                            <th scope="row">Button Text Color:</th>
                            <td>
                                <input type="text" name="nuvho_booking_mask_settings[button_text_color]" value="<?php echo esc_attr($settings['button_text_color']); ?>" class="nuvho-color-picker" id="nuvho-button-text-color" />
                            </td>
                        </tr>
                        
                        <tr>
                            <th scope="row">Button Border Radius:</th>
                            <td>
                                <select name="nuvho_booking_mask_settings[button_border_radius]" id="nuvho-button-radius">
                                    <?php foreach ($border_radius_options as $radius) : ?>
                                        <option value="<?php echo esc_attr($radius); ?>" <?php selected($settings['button_border_radius'], $radius); ?>><?php echo esc_html($radius); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                        </tr>
                        
                        <tr>
                            <th scope="row">Booking Mask Border Radius:</th>
                            <td>
                                <select name="nuvho_booking_mask_settings[booking_mask_border_radius]" id="nuvho-mask-radius">
                                    <?php foreach ($border_radius_options as $radius) : ?>
                                        <option value="<?php echo esc_attr($radius); ?>" <?php selected($settings['booking_mask_border_radius'], $radius); ?>><?php echo esc_html($radius); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                        </tr>
                        
                        <tr>
                            <th scope="row">Guest Selection Type:</th>
                            <td>
                                <select name="nuvho_booking_mask_settings[guest_selection_type]" id="nuvho-guest-selection-type">
                                    <option value="stepper" <?php selected(isset($settings['guest_selection_type']) ? $settings['guest_selection_type'] : 'stepper', 'stepper'); ?>>Stepper</option>
                                    <option value="dropdown" <?php selected(isset($settings['guest_selection_type']) ? $settings['guest_selection_type'] : 'stepper', 'dropdown'); ?>>Dropdown</option>
                                </select>
                                <p class="description">Choose how guests can select the number of adults and children. Dropdown uses standard select menus. Stepper uses +/- buttons to increment or decrement values.</p>
                            </td>
                        </tr>
                        
                        <tr>
                            <th scope="row">Font:</th>
                            <td>
                                <select name="nuvho_booking_mask_settings[font]" id="nuvho-font">
                                    <?php foreach ($fonts as $font) : ?>
                                        <option value="<?php echo esc_attr($font); ?>" <?php selected($settings['font'], $font); ?>><?php echo esc_html($font); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                        </tr>

                        <tr>
                            <th scope="row">Advanced:</th>
                            <td>
                                <label>
                                    <input type="checkbox" id="nuvho-show-css-toggle">
                                    Show advanced settings
                                </label>
                            </td>
                        </tr>
                        <tr id="nuvho-datepicker-color-row" style="display:none;">
                            <th scope="row">Date Picker Color:</th>
                            <td>
                                <input type="text" name="nuvho_booking_mask_settings[datepicker_color]" value="<?php echo esc_attr(isset($settings['datepicker_color']) ? $settings['datepicker_color'] : '#4c7380'); ?>" class="nuvho-color-picker" id="nuvho-datepicker-color" />
                            </td>
                        </tr>
                        <tr id="nuvho-datepicker-text-color-row" style="display:none;">
                            <th scope="row">Date Picker Text Color:</th>
                            <td>
                                <input type="text" name="nuvho_booking_mask_settings[datepicker_text_color]" value="<?php echo esc_attr(isset($settings['datepicker_text_color']) ? $settings['datepicker_text_color'] : '#ffffff'); ?>" class="nuvho-color-picker" id="nuvho-datepicker-text-color" />
                            </td>
                        </tr>
                        <tr id="nuvho-css-editor-row" style="display:none;">
                            <th scope="row"><label for="nuvho-custom-css">Booking Mask CSS:</label></th>
                            <td>
                                <textarea name="nuvho_booking_mask_settings[custom_css]" id="nuvho-custom-css"
                                    rows="15" style="width:100%; font-family:monospace; font-size:13px;"><?php echo esc_textarea(isset($settings['custom_css']) ? $settings['custom_css'] : ''); ?></textarea>
                                <p class="description">
                                    This textbox is editable. Any changes here will update the booking mask in real-time.
                                </p>
                            </td>
                        </tr>
                    </table>
                </div>

                <?php submit_button('Save Changes', 'primary', 'submit', true); ?>
            </div>
        </div>
        
        <div class="nuvho-shortcode-info">
            <h3>Shortcode</h3>
            <p>Use this shortcode to display the booking mask on any page or post:</p>
            <code>[nuvho_booking_mask_single]</code>
        </div>
    </form>
</div>

<script>
jQuery(document).ready(function($) {
    // Add theme selection styling
    $('<style>')
        .text(`
            #nuvho-theme-selector {
                min-width: 200px;
                padding: 8px 12px;
                border: 1px solid #ddd;
                border-radius: 4px;
                background: white;
                font-size: 14px;
            }
            
            .nuvho-theme-preview {
                margin-top: 10px;
                padding: 15px;
                border: 2px solid #e1e1e1;
                border-radius: 8px;
                background: #f9f9f9;
                display: none;
            }
            
            .nuvho-theme-preview.active {
                display: block;
                border-color: #5a9fd4;
                background: #f0f8ff;
            }
            
            .nuvho-theme-info {
                font-size: 13px;
                color: #666;
                font-style: italic;
            }
        `)
        .appendTo('head');
    
    // Theme presets data
    var themePresets = <?php echo json_encode($theme_presets); ?>;
    
    // Add theme description after the select
    $('#nuvho-theme-selector').after('<div class="nuvho-theme-info" id="theme-info"></div>');
    
    // Handle booking option changes to show/hide specific settings
    $('#nuvho-booking-option').on('change', function() {
        var selectedOption = $(this).val();
        
        // Hide all specific settings
        $('#simple-booking-specific-settings, #accor-specific-settings').hide();
        
        // Show relevant settings based on selection
        if (selectedOption.indexOf('Simple Booking') !== -1) {
            $('#simple-booking-specific-settings').show();
        } else if (selectedOption === 'Accor') {
            $('#accor-specific-settings').show();
        }
    });
    
    // Initialize booking option settings on page load
    $('#nuvho-booking-option').trigger('change');
    
    // Handle promo code checkbox toggle
    $('#nuvho-show-promo-code').on('change', function() {
        var promoField = $('#nuvho-preview-promo-field');
        if ($(this).is(':checked')) {
            promoField.slideDown(200);
        } else {
            promoField.slideUp(200);
        }
    });
    
    // Initialize promo code field visibility based on current setting
    var promoCheckbox = $('#nuvho-show-promo-code');
    if (promoCheckbox.length && promoCheckbox.is(':checked')) {
        $('#nuvho-preview-promo-field').show();
    } else {
        $('#nuvho-preview-promo-field').hide();
    }
    
    // Handle theme selection
    $('#nuvho-theme-selector').on('change', function() {
        var selectedTheme = $(this).val();
        var infoDiv = $('#theme-info');
        
        if (selectedTheme && themePresets[selectedTheme]) {
            var preset = themePresets[selectedTheme];
            
            // Show theme info
            if (selectedTheme === 'ocean_breeze') {
                infoDiv.html('<strong>Ocean Breeze:</strong> Clean, modern design with professional teal color scheme and optimized user experience.').show();
            } else if (selectedTheme === 'default') {
                infoDiv.html('<strong>Default Theme:</strong> Classic WordPress-style appearance with blue accents.').show();
            } else if (selectedTheme === 'sunset_coral') {
                infoDiv.html('<strong>Sunset Coral:</strong> Warm and inviting coral theme perfect for tropical or beach resorts.').show();
            } else if (selectedTheme === 'forest_green') {
                infoDiv.html('<strong>Forest Green:</strong> Nature-inspired emerald green theme ideal for eco-friendly or outdoor accommodations.').show();
            } else if (selectedTheme === 'royal_purple') {
                infoDiv.html('<strong>Royal Purple:</strong> Elegant and sophisticated purple theme for luxury hotels and premium properties.').show();
            } else if (selectedTheme === 'midnight_dark') {
                infoDiv.html('<strong>Midnight Dark:</strong> Sleek dark theme with light accents, perfect for modern boutique hotels.').show();
            }
            
            // Update form fields
            $('#nuvho-bg-color').val(preset.background_color).trigger('change');
            $('#nuvho-bg-opacity').val(preset.background_opacity).trigger('change');
            $('#nuvho-button-color').val(preset.button_color).trigger('change');
            $('#nuvho-button-text-color').val(preset.button_text_color).trigger('change');
            $('#nuvho-font-color').val(preset.font_color).trigger('change');
            $('#nuvho-button-text').val(preset.button_text).trigger('change');
            $('#nuvho-button-radius').val(preset.button_border_radius).trigger('change');
            $('#nuvho-mask-radius').val(preset.booking_mask_border_radius).trigger('change');
            $('#nuvho-font').val(preset.font).trigger('change');
            $('#nuvho-datepicker-color').val(preset.datepicker_color || '#4c7380').trigger('change');
            $('#nuvho-datepicker-text-color').val(preset.datepicker_text_color || '#ffffff').trigger('change');

            // Update color pickers
            $('.nuvho-color-picker').each(function() {
                if ($(this).hasClass('wp-color-picker')) {
                    $(this).wpColorPicker('color', $(this).val());
                }
            });
            
            // Trigger preview update
            setTimeout(updateLivePreview, 100);
            
            // Show success message
            $('<div class="notice notice-success is-dismissible"><p><strong>Theme Applied!</strong> ' + preset.name + ' settings have been loaded. Click "Save Changes" to make them permanent.</p></div>')
                .insertAfter('.wrap h1')
                .delay(4000)
                .fadeOut();
        } else {
            infoDiv.hide();
        }
    });
    
    // Update live preview function
    function updateLivePreview() {
        var bgColor = $('#nuvho-bg-color').val();
        var bgOpacity = $('#nuvho-bg-opacity').val().replace('%', '') / 100;
        var buttonColor = $('#nuvho-button-color').val();
        var buttonTextColor = $('#nuvho-button-text-color').val();
        var fontColor = $('#nuvho-font-color').val();
        var buttonText = $('#nuvho-button-text').val();
        var buttonRadius = $('#nuvho-button-radius').val();
        var maskRadius = $('#nuvho-mask-radius').val();
        var font = $('#nuvho-font').val();
        var datepickerColor = $('#nuvho-datepicker-color').val() || '#4c7380';
        var datepickerTextColor = $('#nuvho-datepicker-text-color').val() || '#ffffff';

        // Convert hex to rgba for background
        var rgba = hexToRgba(bgColor, bgOpacity);
        
        // Apply styles to preview
        var previewContainer = $('#nuvho-preview-container');
        var previewForm = previewContainer.find('.nuvho-booking-form');
        var previewButton = $('#nuvho-preview-button');

        // Reset theme-specific styles that accumulate across switches
        previewContainer.css({
            'box-shadow': '',
            'border': ''
        });
        previewContainer.find('input, select, button').css({
            'border-radius': '',
            'border': '',
            'background-color': '',
            'color': ''
        });
        previewContainer.find('.nuvho-date-picker').css({
            'background': '',
            'padding': '',
            'font-size': ''
        });
        previewContainer.find('label').css({
            'color': ''
        });

        // Update container styles
        previewContainer.css({
            'background-color': rgba,
            'border-radius': maskRadius === 'Rounded' ? '8px' : (maskRadius === 'Pill' ? '20px' : '0')
        });

        // Update form styles
        previewForm.css({
            'color': fontColor,
            'font-family': font === 'Default' ? 'inherit' : font
        });

        // Update button styles
        previewButton.css({
            'background-color': buttonColor,
            'color': buttonTextColor,
            'border-radius': buttonRadius === 'Rounded' ? '8px' : (buttonRadius === 'Pill' ? '20px' : '0')
        }).text(buttonText);

        // Apply datepicker colors via <style> tag (survives calendar re-renders)
        var dpCSS = '.daterangepicker td.active, .daterangepicker td.start-date, .daterangepicker td.end-date,'
            + ' .daterangepicker td.active:hover, .daterangepicker td.start-date:hover, .daterangepicker td.end-date:hover,'
            + ' .daterangepicker td.start-date.in-range, .daterangepicker td.start-date.in-range:hover,'
            + ' .daterangepicker td.end-date.in-range, .daterangepicker td.end-date.in-range:hover'
            + ' { background-color: ' + datepickerColor + ' !important; border-color: ' + datepickerColor + ' !important; color: ' + datepickerTextColor + ' !important; opacity: 1 !important; }'
            + ' .daterangepicker td.in-range { background-color: ' + datepickerColor + ' !important; opacity: 0.4; }'
            + ' .daterangepicker .drp-buttons .btn-primary { background-color: ' + datepickerColor + ' !important; border-color: ' + datepickerColor + ' !important; }';
        var $dpStyle = $('#nuvho-admin-dp-preview');
        if (!$dpStyle.length) {
            $dpStyle = $('<style id="nuvho-admin-dp-preview">').appendTo('head');
        }
        $dpStyle.text(dpCSS);

        // Apply theme-specific styling
        var selectedTheme = $('#nuvho-theme-selector').val();
        if (selectedTheme === 'ocean_breeze') {
            previewContainer.css({
                'box-shadow': '0 2px 8px rgba(0,0,0,0.1)',
                'border': '1px solid #e2e8f0'
            });
            previewContainer.find('input, select, button').css({
                'border-radius': '6px',
                'border': '1px solid #d1d5db'
            });
            previewContainer.find('.nuvho-date-picker').css({
                'background': 'white',
                'padding': '12px',
                'font-size': '14px'
            });
        } else if (selectedTheme === 'midnight_dark') {
            previewContainer.css({
                'box-shadow': '0 4px 12px rgba(0,0,0,0.3)',
                'border': '1px solid #374151'
            });
            previewContainer.find('input, select').css({
                'background-color': '#374151',
                'border': '1px solid #4b5563',
                'color': '#f9fafb'
            });
            previewContainer.find('.nuvho-guest-trigger').css({
                'background-color': '#374151',
                'border': '1px solid #4b5563',
                'color': '#f9fafb'
            });
            previewContainer.find('label').css({
                'color': '#f9fafb'
            });
            // Fix modal-interior elements (white background popup)
            previewContainer.find('.nuvho-dropdown-group label, .nuvho-guest-label, .nuvho-room-title').css({
                'color': '#333'
            });
            previewContainer.find('.nuvho-guest-dropdown').css({
                'background-color': '#fff',
                'border': '1px solid #ddd',
                'color': '#333'
            });
            previewContainer.find('.nuvho-done-btn').css({
                'background-color': buttonColor
            });
        } else if (selectedTheme === 'sunset_coral') {
            previewContainer.css({
                'box-shadow': '0 2px 8px rgba(255,107,71,0.1)',
                'border': '1px solid #fed7d7'
            });
        } else if (selectedTheme === 'forest_green') {
            previewContainer.css({
                'box-shadow': '0 2px 8px rgba(16,185,129,0.1)',
                'border': '1px solid #d1fae5'
            });
        } else if (selectedTheme === 'royal_purple') {
            previewContainer.css({
                'box-shadow': '0 2px 8px rgba(139,92,246,0.1)',
                'border': '1px solid #e9d5ff'
            });
        }
    }
    
    // Convert hex to rgba
    function hexToRgba(hex, alpha) {
        var result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
        if (result) {
            var r = parseInt(result[1], 16);
            var g = parseInt(result[2], 16);
            var b = parseInt(result[3], 16);
            return 'rgba(' + r + ',' + g + ',' + b + ',' + alpha + ')';
        }
        return hex;
    }
    
    // Trigger preview update when any appearance setting changes
    $('#nuvho-bg-color, #nuvho-bg-opacity, #nuvho-button-color, #nuvho-button-text-color, #nuvho-font-color, #nuvho-button-text, #nuvho-button-radius, #nuvho-mask-radius, #nuvho-font, #nuvho-datepicker-color, #nuvho-datepicker-text-color').on('change input', function() {
        setTimeout(updateLivePreview, 50);
    });
    
    // Initial preview update
    setTimeout(updateLivePreview, 200);
});
</script>