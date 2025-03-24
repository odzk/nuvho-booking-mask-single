(function($) {
    'use strict';

    /**
     * Public JavaScript functionality for the Nuvho Booking Mask plugin
     */
    $(document).ready(function() {
        // Format date to YYYY-MM-DD for form submission
        const formatDateForInput = function(date) {
            return moment(date).format('YYYY-MM-DD');
        };
        
        // Initialize daterangepickers
        $('.nuvho-booking-mask-container').each(function() {
            const container = $(this);
            const datePicker = container.find('.nuvho-date-picker');
            const checkinInput = container.find('#nuvho-checkin');
            const checkoutInput = container.find('#nuvho-checkout');
            
            // Get today and tomorrow dates
            const today = moment();
            const tomorrow = moment().add(1, 'days');
            
            // Set default values for hidden inputs
            checkinInput.val(formatDateForInput(today));
            checkoutInput.val(formatDateForInput(tomorrow));
            
            // Initialize daterangepicker
            datePicker.daterangepicker({
                startDate: today,
                endDate: tomorrow,
                minDate: today,
                opens: 'center',
                autoApply: true,
                locale: {
                    format: nuvhoBookingMaskSettings.date_format,
                    applyLabel: 'Apply',
                    cancelLabel: 'Cancel',
                    fromLabel: 'From',
                    toLabel: 'To',
                    customRangeLabel: 'Custom',
                    weekLabel: 'W',
                    daysOfWeek: ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'],
                    monthNames: [
                        'January', 'February', 'March', 'April', 'May', 'June',
                        'July', 'August', 'September', 'October', 'November', 'December'
                    ],
                    firstDay: 1
                },
                // Apply custom colors to match the plugin's theme
                autoUpdateInput: true
            }, function(start, end, label) {
                // Update hidden inputs with the selected dates in YYYY-MM-DD format
                checkinInput.val(formatDateForInput(start));
                checkoutInput.val(formatDateForInput(end));
            });
            
            // Set language if specified
            if (nuvhoBookingMaskSettings.locale && nuvhoBookingMaskSettings.locale !== 'en') {
                moment.locale(nuvhoBookingMaskSettings.locale);
            }
            
            // Apply custom styling to match the rest of the form
            setTimeout(function() {
                $('.daterangepicker').addClass('nuvho-daterangepicker');
            }, 100);
        });
        
        // Track booking form submissions
        $(document).on('submit', '#nuvho-booking-form', function() {
            // Get current page URL
            const currentUrl = window.location.href;
            const separator = currentUrl.indexOf('?') > -1 ? '&' : '?';
            const trackingUrl = currentUrl + separator + 'nuvho_track=click';
            
            // Use fetch API to track the click (async, don't delay the form submission)
            fetch(trackingUrl, {
                method: 'GET',
                credentials: 'same-origin'
            }).catch(function(error) {
                console.error('Tracking error:', error);
            });
            
            // Check if the form has the required fields filled
            const checkin = $(this).find('#nuvho-checkin').val();
            const checkout = $(this).find('#nuvho-checkout').val();
            
            if (!checkin || !checkout) {
                alert('Please select both check-in and check-out dates.');
                return false;
            }
            
            // Continue with form submission
            return true;
        });
    });

})(jQuery);