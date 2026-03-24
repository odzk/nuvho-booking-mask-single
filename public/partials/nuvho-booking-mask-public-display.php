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
?>
<div class="nuvho-booking-mask-container">

    <div class="nuvho-booking-form">

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
                    
                    <!-- Modal that appears when clicked -->
<?php $guest_type = isset($settings['guest_selection_type']) ? $settings['guest_selection_type'] : 'stepper'; ?>
<?php if ($guest_type === 'dropdown') : ?>
<!-- Dropdown variant -->
<div class="nuvho-guest-modal nuvho-guest-modal-dropdown" id="nuvho-guest-modal">
    <div class="nuvho-modal-content">
        <div class="nuvho-room-title"><?php esc_html_e('Guests', 'nuvho-booking-mask'); ?></div>
        <div class="nuvho-dropdown-row">
            <div class="nuvho-dropdown-group">
                <label><?php esc_html_e('Adults', 'nuvho-booking-mask'); ?></label>
                <select class="nuvho-guest-dropdown" id="nuvho-adults-dropdown" data-target="adults">
                    <?php for ($i = 1; $i <= 10; $i++) : ?>
                        <option value="<?php echo $i; ?>" <?php selected($adults, $i); ?>><?php echo $i; ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="nuvho-dropdown-group">
                <label><?php esc_html_e('Kids', 'nuvho-booking-mask'); ?></label>
                <select class="nuvho-guest-dropdown" id="nuvho-kids-dropdown" data-target="kids">
                    <?php for ($i = 0; $i <= 10; $i++) : ?>
                        <option value="<?php echo $i; ?>" <?php selected($children, $i); ?>><?php echo $i; ?></option>
                    <?php endfor; ?>
                </select>
            </div>
        </div>
        <div class="nuvho-modal-buttons">
            <button type="button" class="nuvho-cancel-btn"><?php esc_html_e('Cancel', 'nuvho-booking-mask'); ?></button>
            <button type="button" class="nuvho-done-btn"><?php esc_html_e('Done', 'nuvho-booking-mask'); ?></button>
        </div>
    </div>
</div>
<?php else : ?>
<!-- Stepper variant (default) -->
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
<?php endif; ?>
                    
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
                    <button type="submit" onclick="trackBookingClick()"><?php echo esc_html($settings['button_text']); ?></button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    // Update guest number displays in modal (stepper mode only)
    document.addEventListener('DOMContentLoaded', function() {
        // Skip if in dropdown mode
        if (document.querySelector('.nuvho-guest-modal-dropdown')) return;

        var adultsInput = document.getElementById('nuvho-adults-input');
        var kidsInput = document.getElementById('nuvho-kids-input');
        var adultsDisplay = document.querySelector('.nuvho-adults-number');
        var kidsDisplay = document.querySelector('.nuvho-kids-number');

        document.querySelectorAll('.nuvho-increase').forEach(function(button) {
            button.addEventListener('click', function() {
                var target = this.getAttribute('data-target');
                if (target === 'adults') {
                    var val = parseInt(adultsInput.value) || 1;
                    if (val < 10) { adultsInput.value = ++val; adultsDisplay.textContent = val; }
                } else if (target === 'kids') {
                    var val = parseInt(kidsInput.value) || 0;
                    if (val < 10) { kidsInput.value = ++val; kidsDisplay.textContent = val; }
                }
            });
        });

        document.querySelectorAll('.nuvho-decrease').forEach(function(button) {
            button.addEventListener('click', function() {
                var target = this.getAttribute('data-target');
                if (target === 'adults') {
                    var val = parseInt(adultsInput.value) || 1;
                    if (val > 1) { adultsInput.value = --val; adultsDisplay.textContent = val; }
                } else if (target === 'kids') {
                    var val = parseInt(kidsInput.value) || 0;
                    if (val > 0) { kidsInput.value = --val; kidsDisplay.textContent = val; }
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