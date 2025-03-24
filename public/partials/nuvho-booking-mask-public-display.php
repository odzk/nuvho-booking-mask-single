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

<div class="nuvho-booking-mask-container" 
     style="background-color: <?php echo esc_attr($settings['background_color']); ?>; 
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
                <?php if (!empty($settings['coupon'])) : ?>
                    <input type="hidden" name="coupon" value="<?php echo esc_attr($settings['coupon']); ?>">
                <?php endif; ?>
            <?php elseif ($settings['option'] === 'Simple Booking v2') : ?>
                <input type="hidden" name="lang" value="<?php echo esc_attr(strtoupper(substr(explode(' ', $settings['language'])[0], 0, 2))); ?>">
                <input type="hidden" name="cur" value="<?php echo esc_attr($settings['currency']); ?>">
                <?php if (!empty($settings['coupon'])) : ?>
                    <input type="hidden" name="coupon" value="<?php echo esc_attr($settings['coupon']); ?>">
                <?php endif; ?>
            <?php else : ?>
                <input type="hidden" name="hotel_id" value="<?php echo esc_attr($settings['hotel_id']); ?>">
                <input type="hidden" name="lang" value="<?php echo esc_attr(strtolower(explode(' ', $settings['language'])[0])); ?>">
                <input type="hidden" name="currency" value="<?php echo esc_attr($settings['currency']); ?>">
            <?php endif; ?>>
            
            <div class="nuvho-form-row">
                <div class="nuvho-form-field nuvho-date-range">
                    <label for="nuvho-date-picker"><?php esc_html_e('Check-in / Check-out', 'nuvho-booking-mask'); ?></label>
                    <div class="nuvho-date-inputs">
                        <input type="text" id="nuvho-date-picker" class="nuvho-date-picker" readonly>
                        <input type="hidden" id="nuvho-checkin" name="arrival">
                        <input type="hidden" id="nuvho-checkout" name="departure">
                    </div>
                </div>
                
                <div class="nuvho-form-field">
                    <label for="nuvho-adults"><?php esc_html_e('Adults', 'nuvho-booking-mask'); ?></label>
                    <select id="nuvho-adults" name="adults">
                        <?php for ($i = 1; $i <= 10; $i++) : ?>
                            <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                
                <div class="nuvho-form-field">
                    <label for="nuvho-children"><?php esc_html_e('Children', 'nuvho-booking-mask'); ?></label>
                    <select id="nuvho-children" name="children">
                        <?php for ($i = 0; $i <= 10; $i++) : ?>
                            <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                
                <div class="nuvho-form-field nuvho-submit-field">
                    <?php if (isset($settings['show_promo_code']) && $settings['show_promo_code'] && (strpos($settings['option'], 'Simple Booking') !== false)) : ?>
                    <div class="nuvho-promo-field">
                        <label for="nuvho-promo"><?php esc_html_e('Promo Code', 'nuvho-booking-mask'); ?></label>
                        <input type="text" id="nuvho-promo" name="coupon" placeholder="<?php esc_attr_e('Enter promo code', 'nuvho-booking-mask'); ?>">
                    </div>
                    <?php endif; ?>
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