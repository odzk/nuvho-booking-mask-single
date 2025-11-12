(function($) {
    'use strict';

    /**
     * SiteMinder booking engine handler
     * Converts form data to SiteMinder URL format
     */
    
    $(document).ready(function() {
        // Only run if SiteMinder is selected
        if (typeof nuvhoBookingSettings === 'undefined' || nuvhoBookingSettings.option !== 'SiteMinder') {
            return;
        }
        
        // Intercept form submission for SiteMinder
        $(document).on('submit', '#nuvho-booking-form', function(e) {
            e.preventDefault();
            
            const form = $(this);
            
            // Get form values
            const hotelId = form.find('input[name="hotel_id"]').val();
            const checkIn = form.find('#nuvho-checkin').val();
            const checkOut = form.find('#nuvho-checkout').val();
            const adults = form.find('#nuvho-adults-input').val() || '2';
            const children = form.find('#nuvho-kids-input').val() || '0';
            const lang = (nuvhoBookingSettings.lang || 'EN').toLowerCase();
            const currency = nuvhoBookingSettings.currency || 'AUD';
            
            // Build SiteMinder URL
            const baseUrl = 'https://book-directonline.com/properties';
            let siteMinderUrl = baseUrl + '/' + hotelId;
            
            // Build query parameters
            const params = new URLSearchParams();
            params.append('locale', lang);
            params.append('items[0][adults]', adults);
            params.append('items[0][children]', children);
            params.append('items[0][infants]', '0');
            params.append('currency', currency);
            params.append('checkInDate', checkIn);
            params.append('checkOutDate', checkOut);
            params.append('trackPage', 'yes');
            
            // Add tracking parameter if available
            if (nuvhoBookingSettings._gads_gcid) {
                params.append('_gads_gcid', nuvhoBookingSettings._gads_gcid);
            }
            
            const fullUrl = siteMinderUrl + '?' + params.toString();
            
            // Track booking click
            if (typeof trackBookingClick === 'function') {
                trackBookingClick();
            }
            
            // Open booking page
            window.open(fullUrl, '_blank');
            
            return false;
        });
    });

})(jQuery);