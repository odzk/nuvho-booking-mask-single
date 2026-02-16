/**
 * Handle Cloudbeds URL format override
 */
jQuery(document).ready(function($) {
    // Check if we're using Cloudbeds
    if (nuvhoBookingSettings && nuvhoBookingSettings.option === 'Cloudbeds') {
        var bookingForm = $('#nuvho-booking-form');
        
        // Store original form submission
        bookingForm.on('submit', function(e) {
            e.preventDefault();
            
            // Get values
            var hotelId = nuvhoBookingSettings.hotel_id;
            var language = nuvhoBookingSettings.lang || 'en';
            var currency = (nuvhoBookingSettings.currency || 'USD').toLowerCase();
            
            // Get form values
            var checkin = $('#nuvho-checkin').val();
            var checkout = $('#nuvho-checkout').val();
            var adults = $('#nuvho-adults-input').val();
            var children = $('#nuvho-kids-input').val();
            
            // Create the correctly formatted Cloudbeds URL
            var baseUrl = 'https://hotels.cloudbeds.com/en/reservation';
            
            // Clean the hotel ID (remove any slashes)
            hotelId = hotelId.replace(/\//g, '');
            
            // Build the URL with correct format for Cloudbeds
            var redirectUrl = baseUrl + '/' + hotelId;
            redirectUrl += '?checkin=' + checkin;
            redirectUrl += '&checkout=' + checkout;
            redirectUrl += '&adults=' + adults;
            redirectUrl += '&children=' + children;
            redirectUrl += '&currency=' + currency;
            
            console.log("Debug - Redirecting to: " + redirectUrl);
            
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