/**
 * Shared Booking Engine URL Override
 *
 * Handles URL construction for: GuestCentric, Beds24, Little Hotelier,
 * Bookassist, Cubilis, Clock PMS.
 *
 * @since 2.1.4
 */
(function($) {
    'use strict';

    $(document).ready(function() {
        if (typeof nuvhoBookingSettings === 'undefined') return;

        var engine = nuvhoBookingSettings.option;

        // Only handle the 6 new engines
        var supportedEngines = [
            'GuestCentric', 'Beds24', 'Little Hotelier',
            'Bookassist', 'Cubilis', 'Clock PMS'
        ];
        if (supportedEngines.indexOf(engine) === -1) return;

        $(document).on('submit', '#nuvho-booking-form', function(e) {
            e.preventDefault();

            var hotelId  = nuvhoBookingSettings.hotel_id || '';
            var lang     = (nuvhoBookingSettings.lang || 'EN').substring(0, 2);
            var currency = nuvhoBookingSettings.currency || 'USD';

            var checkin  = $('#nuvho-checkin').val();
            var checkout = $('#nuvho-checkout').val();
            var adults   = parseInt($('#nuvho-adults-input').val()) || 2;
            var children = parseInt($('#nuvho-kids-input').val()) || 0;
            var promo    = $('#nuvho-promo').length ? $('#nuvho-promo').val() : '';

            var url = '';

            switch (engine) {
                case 'GuestCentric':
                    url = buildGuestCentric(hotelId, checkin, checkout, adults, children, lang);
                    break;
                case 'Beds24':
                    url = buildBeds24(hotelId, checkin, checkout, adults, children, lang, currency);
                    break;
                case 'Little Hotelier':
                    url = buildLittleHotelier(hotelId, promo);
                    break;
                case 'Bookassist':
                    url = buildBookassist(hotelId, checkin, checkout, lang);
                    break;
                case 'Cubilis':
                    url = buildCubilis(hotelId, lang);
                    break;
                case 'Clock PMS':
                    url = buildClockPMS(hotelId, checkin, checkout, adults, children);
                    break;
            }

            if (url) {
                if (typeof trackBookingClick === 'function') {
                    trackBookingClick();
                }
                window.open(url, '_blank');
            }

            return false;
        });

        /**
         * Compute number of nights between two YYYY-MM-DD dates
         */
        function computeNights(checkin, checkout) {
            if (!checkin || !checkout) return 1;
            var a = moment(checkin, 'YYYY-MM-DD');
            var b = moment(checkout, 'YYYY-MM-DD');
            var diff = b.diff(a, 'days');
            return diff > 0 ? diff : 1;
        }

        /**
         * GuestCentric
         * https://secure.guestcentric.net/api/bg/book.php?apikey=X&startDay=YYYY-MM-DD&nrNights=N&nrAdults=N&nrChildren=N&l=en&amount=1
         */
        function buildGuestCentric(hotelId, checkin, checkout, adults, children, lang) {
            var baseUrl = nuvhoBookingSettings.url || 'https://secure.guestcentric.net/api/bg/book.php';
            var params = new URLSearchParams();
            params.append('apikey', hotelId);
            params.append('startDay', checkin);
            params.append('nrNights', computeNights(checkin, checkout));
            params.append('nrAdults', adults);
            params.append('nrChildren', children);
            params.append('l', lang.toLowerCase());
            params.append('amount', '1');
            return baseUrl + '?' + params.toString();
        }

        /**
         * Beds24
         * https://www.beds24.com/booking2.php?propid=X&checkin=YYYY-MM-DD&checkout=YYYY-MM-DD&numadult=N&numchild=N&lang=en&cur=USD
         */
        function buildBeds24(hotelId, checkin, checkout, adults, children, lang, currency) {
            var baseUrl = nuvhoBookingSettings.url || 'https://www.beds24.com/booking2.php';
            var params = new URLSearchParams();
            params.append('propid', hotelId);
            params.append('checkin', checkin);
            params.append('checkout', checkout);
            params.append('numadult', adults);
            params.append('numchild', children);
            params.append('lang', lang.toLowerCase());
            params.append('cur', currency.toUpperCase());
            return baseUrl + '?' + params.toString();
        }

        /**
         * Little Hotelier
         * https://app.littlehotelier.com/properties/{id}direct?promocode=X
         * Hotel ID is already appended to URL path by PHP (with 'direct' suffix)
         */
        function buildLittleHotelier(hotelId, promo) {
            var baseUrl = nuvhoBookingSettings.url || 'https://app.littlehotelier.com/properties/';
            // PHP already appends hotelId + 'direct' to the URL
            if (promo) {
                return baseUrl + '?promocode=' + encodeURIComponent(promo);
            }
            return baseUrl;
        }

        /**
         * Bookassist
         * https://booking.bookassist.com/?hotel_id=X&date_in=YYYY-MM-DD&date_out=YYYY-MM-DD&user_language=en&action=c_1&service_model=2
         */
        function buildBookassist(hotelId, checkin, checkout, lang) {
            var baseUrl = nuvhoBookingSettings.url || 'https://booking.bookassist.com/';
            var params = new URLSearchParams();
            params.append('hotel_id', hotelId);
            params.append('date_in', checkin);
            params.append('date_out', checkout);
            params.append('user_language', lang.toLowerCase());
            params.append('action', 'c_1');
            params.append('service_model', '2');
            return baseUrl + '?' + params.toString();
        }

        /**
         * Cubilis
         * https://booking.cubilis.eu/?logisid=X&taal=en
         */
        function buildCubilis(hotelId, lang) {
            var baseUrl = nuvhoBookingSettings.url || 'https://booking.cubilis.eu/';
            var params = new URLSearchParams();
            params.append('logisid', hotelId);
            params.append('taal', lang.toLowerCase());
            return baseUrl + '?' + params.toString();
        }

        /**
         * Clock PMS
         * https://sky-eu1.clock-software.com/{hotelId}/{propertyId}/wbe/products/new?wbe_product[arrival]=YYYY-MM-DD&wbe_product[nights]=N&wbe_product[adult_count]=N&wbe_product[children_count]=N
         * Hotel ID field expects format: "hotelId/propertyId" (e.g. "39390/8311")
         * PHP already appends hotelId + '/wbe/products/new' to the URL
         */
        function buildClockPMS(hotelId, checkin, checkout, adults, children) {
            var baseUrl = nuvhoBookingSettings.url || 'https://sky-eu1.clock-software.com/';
            // PHP already appends hotelId/wbe/products/new to the URL path
            var params = [];
            params.push('wbe_product[arrival]=' + encodeURIComponent(checkin));
            params.push('wbe_product[nights]=' + computeNights(checkin, checkout));
            params.push('wbe_product[adult_count]=' + adults);
            params.push('wbe_product[children_count]=' + children);
            return baseUrl + '?' + params.join('&');
        }
    });

})(jQuery);
