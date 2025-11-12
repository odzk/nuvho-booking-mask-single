<?php
/**
 * Provide a public-facing view for the booking mask
 *
 * @link       https://herobe.com
 * @since      2.0.0
 *
 * @package    Nuvho_Booking_Mask
 * @subpackage Nuvho_Booking_Mask/public/partials
 */

// Variables available from the shortcode function:
// $settings - Plugin settings from options table
// $border_radius - Calculated booking mask border radius
// $button_radius - Calculated button border radius
// $opacity - Calculated opacity value
?>

<?php
// Set default values for guest counts
$adults = 2;
$children = 0;

$opacity = str_replace('%', '', $settings['background_opacity']) / 100;
// Calculate background color with opacity
$bg_color = $settings['background_color'];
$background_color_with_opacity = $bg_color; // Default

// If it's a hex color, convert to rgba
if (preg_match('/^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i', $bg_color, $matches)) {
    $r = hexdec($matches[1]);
    $g = hexdec($matches[2]);
    $b = hexdec($matches[3]);
    $background_color_with_opacity = "rgba($r, $g, $b, $opacity)";
} 
// If it's already rgba, just update the alpha
elseif (preg_match('/^rgba\((\d+),\s*(\d+),\s*(\d+),\s*[\d\.]+\)$/i', $bg_color, $matches)) {
    $r = $matches[1];
    $g = $matches[2];
    $b = $matches[3];
    $background_color_with_opacity = "rgba($r, $g, $b, $opacity)";
}
// If it's rgb, convert to rgba
elseif (preg_match('/^rgb\((\d+),\s*(\d+),\s*(\d+)\)$/i', $bg_color, $matches)) {
    $r = $matches[1];
    $g = $matches[2];
    $b = $matches[3];
    $background_color_with_opacity = "rgba($r, $g, $b, $opacity)";
}

?>

<div class="nuvho-booking-mask-container" 
     style="background-color: <?php echo esc_attr($background_color_with_opacity) ?>; 
            opacity: <?php echo esc_attr($opacity); ?>; 
            border-radius: <?php echo esc_attr($border_radius); ?>;">
    
    <div class="nuvho-booking-form" 
         style="color: <?php echo esc_attr($settings['font_color']); ?>;
                font-family: <?php echo $settings['font'] === 'Default' ? 'inherit' : esc_attr($settings['font']); ?>;">
        
        <form id="nuvho-booking-form" action="<?php echo esc_url($settings['url']); ?>" method="get" target="_blank">
            <!-- Hidden fields for the booking engine configuration -->
            <?php if ($settings['option'] === 'Accor') : ?>
                <?php if (!empty($settings['code_hotel'])) : ?>
                    <input type="hidden" name="code_hotel" value="<?php echo esc_attr($settings['code_hotel']); ?>">
                <?php endif; ?>
                
                <?php if (!empty($settings['goto'])) : ?>
                    <input type="hidden" name="goto" value="<?php echo esc_attr($settings['goto']); ?>">
                <?php endif; ?>
                
                <?php if (!empty($settings['merchantid'])) : ?>
                    <input type="hidden" name="merchantid" value="<?php echo esc_attr($settings['merchantid']); ?>">
                <?php endif; ?>
                
                <?php if (!empty($settings['sourceid'])) : ?>
                    <input type="hidden" name="sourceid" value="<?php echo esc_attr($settings['sourceid']); ?>">
                <?php endif; ?>
                
                <?php if (!empty($settings['utm_campaign'])) : ?>
                    <input type="hidden" name="utm_campaign" value="<?php echo esc_attr($settings['utm_campaign']); ?>">
                <?php endif; ?>
                
                <?php if (!empty($settings['utm_medium'])) : ?>
                    <input type="hidden" name="utm_medium" value="<?php echo esc_attr($settings['utm_medium']); ?>">
                <?php endif; ?>
                
                <?php if (!empty($settings['utm_source'])) : ?>
                    <input type="hidden" name="utm_source" value="<?php echo esc_attr($settings['utm_source']); ?>">
                <?php endif; ?>
                
                <?php if (!empty($settings['contextparam'])) : ?>
                    <input type="hidden" name="contextparam" value="<?php echo esc_attr($settings['contextparam']); ?>">
                <?php endif; ?>
            <?php elseif ($settings['option'] === 'Simple Booking v1') : ?>
                <input type="hidden" name="hid" value="<?php echo esc_attr($settings['hotel_id']); ?>">
                <input type="hidden" name="lang" value="<?php echo esc_attr(strtoupper(substr(explode(' ', $settings['language'])[0], 0, 2))); ?>">
                <input type="hidden" name="cur" value="<?php echo esc_attr($settings['currency']); ?>">
                <?php if (!empty($settings['coupon']) && empty($settings['show_promo_code'])) : ?>
                    <input type="hidden" name="coupon" value="<?php echo esc_attr($settings['coupon']); ?>">
                <?php endif; ?>
            <?php elseif ($settings['option'] === 'Simple Booking v2') : ?>
                <input type="hidden" name="lang" value="<?php echo esc_attr(strtoupper(substr(explode(' ', $settings['language'])[0], 0, 2))); ?>">
                <input type="hidden" name="cur" value="<?php echo esc_attr($settings['currency']); ?>">
                <?php if (!empty($settings['coupon']) && empty($settings['show_promo_code'])) : ?>
                    <input type="hidden" name="coupon" value="<?php echo esc_attr($settings['coupon']); ?>">
                <?php endif; ?>
            <?php else : ?>
                <input type="hidden" name="hotel_id" value="<?php echo esc_attr($settings['hotel_id']); ?>">
                <input type="hidden" name="lang" value="<?php echo esc_attr(strtolower(explode(' ', $settings['language'])[0])); ?>">
                <input type="hidden" name="currency" value="<?php echo esc_attr($settings['currency']); ?>">
            <?php endif; ?>
            
            <!-- Single row with all elements in consistent order -->
            <div class="nuvho-form-row">
                <!-- 1. Date picker -->
                <div class="nuvho-form-field nuvho-date-range">
                    <label for="nuvho-date-picker"><?php esc_html_e('Check-in / Check-out', 'nuvho-booking-mask'); ?></label>
                    <div class="nuvho-date-inputs">
                        <input type="text" id="nuvho-date-picker" class="nuvho-date-picker" readonly>
                        <input type="hidden" id="nuvho-checkin" name="arrival">
                        <input type="hidden" id="nuvho-checkout" name="departure">
                    </div>
                </div>
                
                <!-- 2. Guest selection modal -->
                <div class="nuvho-form-field nuvho-guest-selector-container">
                    <label><?php esc_html_e('Persons:', 'nuvho-booking-mask'); ?></label>
                    <!-- Trigger button that shows current selection -->
                    <div class="nuvho-guest-summary">
                    <button type="button" class="nuvho-guest-trigger" style="color: #333;">
                    <span class="nuvho-guest-count" style="color: #333;">
                        <strong class="nuvho-adults-count">2</strong> 
                        <span style="color: #333;"><?php esc_html_e('ADULTS', 'nuvho-booking-mask'); ?></span>
                        + <strong class="nuvho-kids-count">0</strong> 
                        <span style="color: #333;"><?php esc_html_e('KIDS', 'nuvho-booking-mask'); ?></span>
                    </span>
                    <span class="nuvho-selector-toggle">&#9660;</span>
                </button>
                    </div>
                    
                    <!-- Modal that appears when clicked -->
<!-- Modal that appears when clicked -->
<div class="nuvho-guest-modal" id="nuvho-guest-modal">
    <div class="nuvho-modal-content">
        <div class="nuvho-room-title"><?php esc_html_e('Guests', 'nuvho-booking-mask'); ?></div>
        
        <!-- Adults selection -->
        <div class="nuvho-guest-row">
            <div class="nuvho-guest-label">
                <span class="nuvho-adults-number">2</span>
                <span class="nuvho-label-suffix"><?php echo esc_html(((int)$adults === 1) ? __('Adult', 'nuvho-booking-mask') : __('Adults', 'nuvho-booking-mask')); ?></span>
            </div>
            <div class="nuvho-stepper-controls">
                <button type="button" class="nuvho-circle-btn nuvho-decrease" data-target="adults">
                    <span class="nuvho-btn-icon">−</span>
                </button>
                <button type="button" class="nuvho-circle-btn nuvho-increase" data-target="adults">
                    <span class="nuvho-btn-icon">+</span>
                </button>
            </div>
        </div>
        
        <!-- Kids selection -->
        <div class="nuvho-guest-row">
            <div class="nuvho-guest-label">
                <span class="nuvho-kids-number">0</span>
                <span class="nuvho-label-suffix"><?php echo esc_html(((int)$children === 1) ? __('Kid', 'nuvho-booking-mask') : __('Kids', 'nuvho-booking-mask')); ?></span>
            </div>
            <div class="nuvho-stepper-controls">
                <button type="button" class="nuvho-circle-btn nuvho-decrease" data-target="kids">
                    <span class="nuvho-btn-icon">−</span>
                </button>
                <button type="button" class="nuvho-circle-btn nuvho-increase" data-target="kids">
                    <span class="nuvho-btn-icon">+</span>
                </button>
            </div>
        </div>
        
        <!-- Modal buttons -->
        <div class="nuvho-modal-buttons">
            <button type="button" class="nuvho-cancel-btn"><?php esc_html_e('Cancel', 'nuvho-booking-mask'); ?></button>
            <button type="button" class="nuvho-done-btn"><?php esc_html_e('Done', 'nuvho-booking-mask'); ?></button>
        </div>
    </div>
</div>
                    
                    <!-- Hidden input fields for form submission -->
                    <input type="hidden" id="nuvho-adults-input" name="adults" value="2">
                    <input type="hidden" id="nuvho-kids-input" name="children" value="0">
                </div>
                
                <!-- 3. Promo code field - always in third position if enabled -->
                <?php if (isset($settings['show_promo_code']) && $settings['show_promo_code'] && (strpos($settings['option'], 'Simple Booking') !== false)) : ?>
                <div class="nuvho-form-field nuvho-promo-field">
                    <label for="nuvho-promo"><?php esc_html_e('Promo Code', 'nuvho-booking-mask'); ?></label>
                    <input type="text" id="nuvho-promo" name="coupon" placeholder="<?php esc_attr_e('Enter promo code', 'nuvho-booking-mask'); ?>">
                </div>
                <?php endif; ?>
                
                <!-- 4. Submit button - always last -->
                <div class="nuvho-form-field nuvho-submit-field">
                    <button type="submit" 
                            style="background-color: <?php echo esc_attr($settings['button_color']); ?>; 
                                   color: <?php echo esc_attr($settings['button_text_color']); ?>; 
                                   border-radius: <?php echo esc_attr($button_radius); ?>;"
                            onclick="trackBookingClick()"><?php echo esc_html($settings['button_text']); ?></button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    // Update guest number displays in modal
    document.addEventListener('DOMContentLoaded', function() {
        // Get modal elements
        const modal = document.getElementById('nuvho-guest-modal');
        const adultsInput = document.getElementById('nuvho-adults-input');
        const kidsInput = document.getElementById('nuvho-kids-input');
        const adultsDisplay = document.querySelector('.nuvho-adults-number');
        const kidsDisplay = document.querySelector('.nuvho-kids-number');
        
        // Handle increase/decrease buttons
        const increaseButtons = document.querySelectorAll('.nuvho-increase');
        const decreaseButtons = document.querySelectorAll('.nuvho-decrease');
        
        increaseButtons.forEach(button => {
            button.addEventListener('click', function() {
                const target = this.getAttribute('data-target');
                if (target === 'adults') {
                    let currentValue = parseInt(adultsInput.value) || 1;
                    if (currentValue < 10) { // Set a reasonable max
                        currentValue++;
                        adultsInput.value = currentValue;
                        adultsDisplay.textContent = currentValue;
                    }
                } else if (target === 'kids') {
                    let currentValue = parseInt(kidsInput.value) || 0;
                    if (currentValue < 10) { // Set a reasonable max
                        currentValue++;
                        kidsInput.value = currentValue;
                        kidsDisplay.textContent = currentValue;
                    }
                }
            });
        });
        
        decreaseButtons.forEach(button => {
            button.addEventListener('click', function() {
                const target = this.getAttribute('data-target');
                if (target === 'adults') {
                    let currentValue = parseInt(adultsInput.value) || 1;
                    if (currentValue > 1) { // Minimum 1 adult
                        currentValue--;
                        adultsInput.value = currentValue;
                        adultsDisplay.textContent = currentValue;
                    }
                } else if (target === 'kids') {
                    let currentValue = parseInt(kidsInput.value) || 0;
                    if (currentValue > 0) { // Minimum 0 kids
                        currentValue--;
                        kidsInput.value = currentValue;
                        kidsDisplay.textContent = currentValue;
                    }
                }
            });
        });
    });
    
    // Set minimum dates
    document.addEventListener('DOMContentLoaded', function() {
        const today = new Date();
        const tomorrow = new Date(today);
        tomorrow.setDate(tomorrow.getDate() + 1);
        
        const checkin = document.getElementById('nuvho-checkin');
        const checkout = document.getElementById('nuvho-checkout');
        
        // Format dates as YYYY-MM-DD
        const formatDate = (date) => {
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');
            return `${year}-${month}-${day}`;
        };
        
        checkin.min = formatDate(today);
        checkin.value = formatDate(today);
        
        checkout.min = formatDate(tomorrow);
        checkout.value = formatDate(tomorrow);
        
        // Update checkout min date when checkin changes
        checkin.addEventListener('change', function() {
            const newMinDate = new Date(this.value);
            newMinDate.setDate(newMinDate.getDate() + 1);
            
            checkout.min = formatDate(newMinDate);
            
            // If current checkout date is before new min date, update it
            if (new Date(checkout.value) <= new Date(this.value)) {
                checkout.value = formatDate(newMinDate);
            }
        });
    });
    
    // Track booking clicks for analytics
    function trackBookingClick() {
        // Send tracking data
        const trackingUrl = window.location.href + (window.location.href.indexOf('?') > -1 ? '&' : '?') + 'nuvho_track=click';
        
        // Use fetch API to track the click
        fetch(trackingUrl, {
            method: 'GET',
            credentials: 'same-origin'
        }).then(function(response) {
            // No need to handle the response
        }).catch(function(error) {
            console.error('Tracking error:', error);
        });
    }
</script>