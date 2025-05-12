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

// Booking providers
$booking_providers = array(
    'Simple Booking v1',
    'Simple Booking v2',
    'Accor',
    'Cloudbeds',
    'Staah',
    'SiteMinder',
    'RMS',
    'Protel',
    'MEWS',
    'TravelClick',
    'Frome'
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
        <div class="nuvho-booking-preview">
            <?php 
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

            // Convert background color to rgba with opacity
            $bg_color = $settings['background_color'];
            $rgba_color = $bg_color;

            // If it's a hex color, convert to rgba
            if (preg_match('/^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i', $bg_color, $matches)) {
                $r = hexdec($matches[1]);
                $g = hexdec($matches[2]);
                $b = hexdec($matches[3]);
                $rgba_color = "rgba($r, $g, $b, $opacity)";
            } 
            // Handle other color formats if needed...

            $background_color_with_opacity = $rgba_color;
            ?>
            <div id="nuvho-preview-container" style="
                background-color: <?php echo esc_attr($background_color_with_opacity); ?>;
                border-radius: <?php echo esc_attr($border_radius); ?>;
                padding: 20px;
                max-width: 100%;
            ">
                <div style="
                    color: <?php echo esc_attr($settings['font_color']); ?>;
                    font-family: <?php echo $settings['font'] === 'Default' ? 'inherit' : esc_attr($settings['font']); ?>;
                ">
                    <div class="nuvho-booking-form-preview">
                        <!-- 1. Check-in / Check-out -->
                        <div class="nuvho-preview-row">
                            <label>Check-in / Check-out</label>
                            <input type="text" placeholder="Select dates" readonly>
                        </div>
                        
                        <!-- 2. Guests -->
                        <div class="nuvho-preview-row">
                            <label>Guests</label>
                            <select>
                                <option>1 Adult</option>
                                <option>2 Adults</option>
                            </select>
                        </div>
                        
                        <!-- 3. Promo Code (always in third position if enabled) -->
                        <?php if (isset($settings['show_promo_code']) && $settings['show_promo_code']) : ?>
                        <div id="nuvho-preview-promo-field" class="nuvho-preview-row">
                            <label>Promo Code</label>
                            <input type="text" placeholder="Enter promo code" readonly>
                        </div>
                        <?php endif; ?>
                        
                        <!-- 4. Button (always last) -->
                        <div class="nuvho-preview-row">
                            <label>&nbsp;</label>
                            <button style="
                                background-color: <?php echo esc_attr($settings['button_color']); ?>;
                                color: <?php echo esc_attr($settings['button_text_color']); ?>;
                                border-radius: <?php echo esc_attr($button_radius); ?>;
                                border: none;
                                padding: 8px 16px;
                                cursor: pointer;
                            "><?php echo esc_html($settings['button_text']); ?></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="nuvho-shortcode-info">
            <h3>Shortcode</h3>
            <p>Use this shortcode to display the booking mask on any page or post:</p>
            <code>[nuvho_booking_mask_single]</code>
        </div>
    </div>
</div>

<?php
/**
 * Partial update for the admin settings - enable promo code 
 * Place this within the Simple Booking specific settings section
 */
?>

<div id="simple-booking-specific-settings" class="nuvho-settings-card" style="<?php echo (strpos($settings['option'], 'Simple Booking') !== false) ? 'display: block;' : 'display: none;'; ?>">
    <h2>Simple Booking Settings</h2>
    <table class="form-table">
        <tr>
            <th scope="row">Show Promo Code Field:</th>
            <td>
                <label>
                    <input type="checkbox" name="nuvho_booking_mask_settings[show_promo_code]" value="1" <?php checked(isset($settings['show_promo_code']) && $settings['show_promo_code']); ?> />
                    Display a promo code field in the booking form
                </label>
                <p class="description">This allows users to enter their own promotional codes. The promo code field will always appear as the third element in the booking form.</p>
            </td>
        </tr>
    </table>
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
                            <th scope="row">Option:</th>
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
                            <th scope="row">Hotel ID:</th>
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
                                    <input type="checkbox" name="nuvho_booking_mask_settings[show_promo_code]" value="1" <?php checked(isset($settings['show_promo_code']) && $settings['show_promo_code']); ?> />
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
            </div>
            
            <div class="nuvho-admin-column">
                <div class="nuvho-settings-card">
                    <h2>Appearance Settings</h2>
                    <table class="form-table">
                        <tr>
                            <th scope="row">Background Color:</th>
                            <td>
                                <input type="text" name="nuvho_booking_mask_settings[background_color]" value="<?php echo esc_attr($settings['background_color']); ?>" class="nuvho-color-picker" />
                            </td>
                        </tr>
                        
                        <tr>
                            <th scope="row">Background Opacity:</th>
                            <td>
                                <select name="nuvho_booking_mask_settings[background_opacity]">
                                    <?php foreach ($opacity_options as $opacity) : ?>
                                        <option value="<?php echo esc_attr($opacity); ?>" <?php selected($settings['background_opacity'], $opacity); ?>><?php echo esc_html($opacity); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                        </tr>
                        
                        <tr>
                            <th scope="row">Button Color:</th>
                            <td>
                                <input type="text" name="nuvho_booking_mask_settings[button_color]" value="<?php echo esc_attr($settings['button_color']); ?>" class="nuvho-color-picker" />
                            </td>
                        </tr>
                        
                        <tr>
                            <th scope="row">Font Color:</th>
                            <td>
                                <input type="text" name="nuvho_booking_mask_settings[font_color]" value="<?php echo esc_attr($settings['font_color']); ?>" class="nuvho-color-picker" />
                            </td>
                        </tr>
                        
                        <tr>
                            <th scope="row">Button Text:</th>
                            <td>
                                <input type="text" name="nuvho_booking_mask_settings[button_text]" value="<?php echo esc_attr($settings['button_text']); ?>" class="regular-text" />
                            </td>
                        </tr>
                        
                        <tr>
                            <th scope="row">Button Text Color:</th>
                            <td>
                                <input type="text" name="nuvho_booking_mask_settings[button_text_color]" value="<?php echo esc_attr($settings['button_text_color']); ?>" class="nuvho-color-picker" />
                            </td>
                        </tr>
                        
                        <tr>
                            <th scope="row">Button Border Radius:</th>
                            <td>
                                <select name="nuvho_booking_mask_settings[button_border_radius]">
                                    <?php foreach ($border_radius_options as $radius) : ?>
                                        <option value="<?php echo esc_attr($radius); ?>" <?php selected($settings['button_border_radius'], $radius); ?>><?php echo esc_html($radius); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                        </tr>
                        
                        <tr>
                            <th scope="row">Booking Mask Border Radius:</th>
                            <td>
                                <select name="nuvho_booking_mask_settings[booking_mask_border_radius]">
                                    <?php foreach ($border_radius_options as $radius) : ?>
                                        <option value="<?php echo esc_attr($radius); ?>" <?php selected($settings['booking_mask_border_radius'], $radius); ?>><?php echo esc_html($radius); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                        </tr>
                        
                        <tr>
                            <th scope="row">Guest Selection Type:</th>
                            <td>
                                <select name="nuvho_booking_mask_settings[guest_selection_type]">
                                    <option value="dropdown" <?php selected(isset($settings['guest_selection_type']) ? $settings['guest_selection_type'] : 'dropdown', 'dropdown'); ?>>Dropdown</option>
                                    <option value="stepper" <?php selected(isset($settings['guest_selection_type']) ? $settings['guest_selection_type'] : 'dropdown', 'stepper'); ?>>Stepper</option>
                                </select>
                                <p class="description">Choose how guests can select the number of adults and children. Dropdown uses standard select menus. Stepper uses +/- buttons to increment or decrement values.</p>
                            </td>
                        </tr>
                        
                        <tr>
                            <th scope="row">Font:</th>
                            <td>
                                <select name="nuvho_booking_mask_settings[font]">
                                    <?php foreach ($fonts as $font) : ?>
                                        <option value="<?php echo esc_attr($font); ?>" <?php selected($settings['font'], $font); ?>><?php echo esc_html($font); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                        </tr>
                    </table>
                </div>
                
                <?php submit_button('Save Changes', 'primary', 'submit', true); ?>
            </div>
        </div>
    </form>
</div>