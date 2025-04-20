/**
 * Handle Simple Booking v2 URL format - Direct override approach
 */
jQuery(document).ready(function($) {
    // Check if we're using Simple Booking v2
    if (nuvhoBookingSettings && nuvhoBookingSettings.option === 'Simple Booking v2') {
        var bookingForm = $('#nuvho-booking-form');
        
        // Store original form submission
        bookingForm.on('submit', function(e) {
            e.preventDefault();
            
            // Get values
            var hotelId = nuvhoBookingSettings.hotel_id;
            var baseUrl = nuvhoBookingSettings.url;
            var language = nuvhoBookingSettings.lang || 'EN';
            var currency = nuvhoBookingSettings.currency || 'USD';
            
            // Get form values
            var checkin = $('#nuvho-checkin').val();
            var checkout = $('#nuvho-checkout').val();
            var adults = $('#nuvho-adults-input').val(); // Changed to use the new hidden input
            var children = $('#nuvho-kids-input').val(); // Changed to use the new hidden input
            var coupon = $('#nuvho-promo').length ? $('#nuvho-promo').val() : '';
            
            // Build guests parameter
            var guests = '';
            // Add adults
            for (var i = 0; i < parseInt(adults); i++) {
                guests += 'A,';
            }
            
            // Add children - using "5" for each child as specified
            if (parseInt(children) > 0) {
                for (var j = 0; j < parseInt(children); j++) {
                    guests += '5,';
                }
            }
            
            // Remove trailing comma
            if (guests.endsWith(',')) {
                guests = guests.substring(0, guests.length - 1);
            }
            
            // Ensure base URL ends with hotel ID
            if (baseUrl.indexOf(hotelId) === -1) {
                if (baseUrl.endsWith('/')) {
                    baseUrl += hotelId;
                } else {
                    baseUrl += '/' + hotelId;
                }
            }
            
            // Build the URL with correct parameters
            var redirectUrl = baseUrl;
            redirectUrl += '?lang=' + language;
            redirectUrl += '&cur=' + currency;
            redirectUrl += '&guests=' + encodeURIComponent(guests);
            redirectUrl += '&in=' + checkin;
            redirectUrl += '&out=' + checkout;
            
            if (coupon) {
                redirectUrl += '&coupon=' + encodeURIComponent(coupon);
            }
            
            // Track click if tracking function exists
            if (typeof trackBookingClick === 'function') {
                trackBookingClick();
            }
            
            // Redirect to the booking engine
            window.open(redirectUrl, '_blank');
            return false;
        });
    }
});