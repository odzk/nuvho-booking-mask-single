(function($) {
    'use strict';

    /**
     * Admin JavaScript functionality for the Nuvho Booking Mask plugin
     */
    $(document).ready(function() {
        // Initialize color pickers
        $('.nuvho-color-picker').wpColorPicker({
            change: function(event, ui) {
                // Update preview on color change
                updatePreview();
            }
        });

        // Handle changes to settings fields for live preview
        $('select[name^="nuvho_booking_mask_settings"], input[name^="nuvho_booking_mask_settings"]').on('change', function() {
            updatePreview();
        });

        // Update preview when typing in text fields
        $('input[type="text"][name^="nuvho_booking_mask_settings"]').on('input', function() {
            updatePreview();
        });

        // Special handling for checkbox changes
        $('input[type="checkbox"][name="nuvho_booking_mask_settings[show_promo_code]"]').on('change', function() {
            // Direct handling for immediate visual feedback
            const showPromoCode = $(this).is(':checked');
            
            if (showPromoCode) {
                if ($('#nuvho-preview-promo-field').length === 0) {
                    // Create the promo code field if it doesn't exist
                    const promoField = $('<div id="nuvho-preview-promo-field" class="nuvho-preview-row"><label>Promo Code</label><input type="text" placeholder="Enter promo code" readonly></div>');
                    $('#nuvho-preview-container .nuvho-booking-form-preview .nuvho-preview-row').eq(1).after(promoField);
                }
            } else {
                // Remove the promo code field if it exists
                $('#nuvho-preview-promo-field').remove();
            }
            
            // Also run the full update
            updatePreview();
        });

        // Function to update preview
        function updatePreview() {
            // Get current values
            const backgroundColorField = $('input[name="nuvho_booking_mask_settings[background_color]"]');
            const backgroundColor = backgroundColorField.wpColorPicker('color') || backgroundColorField.val();
            
            const opacityField = $('select[name="nuvho_booking_mask_settings[background_opacity]"]');
            const opacity = parseInt(opacityField.val()) / 100;
            
            const buttonColorField = $('input[name="nuvho_booking_mask_settings[button_color]"]');
            const buttonColor = buttonColorField.wpColorPicker('color') || buttonColorField.val();
            
            const fontColorField = $('input[name="nuvho_booking_mask_settings[font_color]"]');
            const fontColor = fontColorField.wpColorPicker('color') || fontColorField.val();
            
            const buttonTextColorField = $('input[name="nuvho_booking_mask_settings[button_text_color]"]');
            const buttonTextColor = buttonTextColorField.wpColorPicker('color') || buttonTextColorField.val();
            
            const buttonText = $('input[name="nuvho_booking_mask_settings[button_text]"]').val();
            
            const buttonRadius = $('select[name="nuvho_booking_mask_settings[button_border_radius]"]').val();
            let buttonRadiusValue = '0';
            if (buttonRadius === 'Rounded') {
                buttonRadiusValue = '8px';
            } else if (buttonRadius === 'Pill') {
                buttonRadiusValue = '20px';
            }
            
            const maskRadius = $('select[name="nuvho_booking_mask_settings[booking_mask_border_radius]"]').val();
            let maskRadiusValue = '0';
            if (maskRadius === 'Rounded') {
                maskRadiusValue = '8px';
            } else if (maskRadius === 'Pill') {
                maskRadiusValue = '20px';
            }
            
            const font = $('select[name="nuvho_booking_mask_settings[font]"]').val();
            const fontFamily = font === 'Default' ? 'inherit' : font;
            
            // Check if promo code field should be shown
            const showPromoCode = $('input[name="nuvho_booking_mask_settings[show_promo_code]"]').is(':checked');
            
            // Update preview
            $('#nuvho-preview-container').css({
                'background-color': backgroundColor,
                'opacity': opacity,
                'border-radius': maskRadiusValue
            });
            
            $('#nuvho-preview-container div').css({
                'color': fontColor,
                'font-family': fontFamily
            });
            
            $('#nuvho-preview-container button').css({
                'background-color': buttonColor,
                'color': buttonTextColor,
                'border-radius': buttonRadiusValue
            }).text(buttonText);
            
            // Show/hide promo code field in preview
            if (showPromoCode) {
                if ($('#nuvho-preview-promo-field').length === 0) {
                    // Create the promo code field if it doesn't exist
                    const promoField = $('<div id="nuvho-preview-promo-field" class="nuvho-preview-row"><label>Promo Code</label><input type="text" placeholder="Enter promo code" readonly></div>');
                    $('#nuvho-preview-container .nuvho-booking-form-preview').prepend(promoField);
                }
            } else {
                // Remove the promo code field if it exists
                $('#nuvho-preview-promo-field').remove();
            }
        }

        // Handle booking engine selection
        $('#nuvho-booking-option').on('change', function() {
            const selectedEngine = $(this).val();
            const urlField = $('input[name="nuvho_booking_mask_settings[url]"]');
            const currentUrl = urlField.val();
            let defaultUrl = '';
            
            // Toggle specific settings sections
            if (selectedEngine === 'Accor') {
                $('#accor-specific-settings').show();
                $('#simple-booking-specific-settings').hide();
                defaultUrl = 'https://all.accor.com/ssr/app/accor/rates';
            } else if (selectedEngine === 'Simple Booking v1' || selectedEngine === 'Simple Booking v2') {
                $('#simple-booking-specific-settings').show();
                $('#accor-specific-settings').hide();
                
                if (selectedEngine === 'Simple Booking v1') {
                    defaultUrl = 'https://www.simplebooking.it/ibe/search';
                } else { // Simple Booking v2
                    defaultUrl = 'https://www.simplebooking.it/ibe2/hotel';
                }
            } else if (selectedEngine === 'Cloudbeds') {
                $('#accor-specific-settings').hide();
                $('#simple-booking-specific-settings').hide();
                defaultUrl = 'https://hotels.cloudbeds.com/reservation/';
            } else {
                $('#accor-specific-settings').hide();
                $('#simple-booking-specific-settings').hide();
                
                // Set default URL based on selected booking engine
                switch(selectedEngine) {
                    case 'Staah':
                        defaultUrl = 'https://secure.staah.com/common-cgi/package/packagebooking.pl';
                        break;
                    case 'SiteMinder':
                        defaultUrl = 'https://book-directonline.com/properties';
                        break;
                    case 'RMS':
                        defaultUrl = 'https://rms.rezexchange.com/bookings';
                        break;
                    case 'Protel':
                        defaultUrl = 'https://booking.protel.net/booking';
                        break;
                    case 'MEWS':
                        defaultUrl = 'https://www.mewssystems.com/booking';
                        break;
                    case 'TravelClick':
                        defaultUrl = 'https://gc.synxis.com/rez.aspx';
                        break;
                    case 'Frome':
                        defaultUrl = 'https://frome.bookings.com/reservation';
                        break;
                }
            }
            
            // Always update the URL field to the appropriate default when changing booking engines
            if (defaultUrl) {
                urlField.val(defaultUrl);
            }
        });
    });

})(jQuery);