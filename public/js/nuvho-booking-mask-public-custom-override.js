/**
 * Custom Booking Engine URL Builder
 *
 * Reads the custom_engine_config JSON from nuvhoBookingSettings
 * and dynamically constructs the booking URL based on saved parameter mappings.
 *
 * @since 2.1.4
 */
(function($) {
    'use strict';

    $(document).ready(function() {
        if (typeof nuvhoBookingSettings === 'undefined' ||
            nuvhoBookingSettings.option !== 'Custom' ||
            !nuvhoBookingSettings.custom_engine_config) {
            return;
        }

        var config;
        try {
            config = typeof nuvhoBookingSettings.custom_engine_config === 'string'
                ? JSON.parse(nuvhoBookingSettings.custom_engine_config)
                : nuvhoBookingSettings.custom_engine_config;
        } catch(e) {
            console.error('Nuvho: Invalid custom engine config');
            return;
        }

        if (!config.parameters || !config.parameters.length) {
            return;
        }

        $(document).on('submit', '#nuvho-booking-form', function(e) {
            e.preventDefault();

            var hotelId  = nuvhoBookingSettings.hotel_id || '';
            var language = nuvhoBookingSettings.lang || 'EN';
            var currency = nuvhoBookingSettings.currency || 'USD';

            var checkin  = $('#nuvho-checkin').val();
            var checkout = $('#nuvho-checkout').val();
            var adults   = parseInt($('#nuvho-adults-input').val()) || 2;
            var children = parseInt($('#nuvho-kids-input').val()) || 0;
            var promo    = $('#nuvho-promo').length ? $('#nuvho-promo').val() : '';

            // Start with base URL
            var baseUrl = config.base_url_pattern || nuvhoBookingSettings.url;

            // Append hotel_id to path if configured
            if (config.hotel_id_in_path && hotelId) {
                if (baseUrl.charAt(baseUrl.length - 1) !== '/') {
                    baseUrl += '/';
                }
                baseUrl += hotelId;
            }

            // Build query parameters
            var params = [];

            config.parameters.forEach(function(param) {
                var value = resolveParamValue(param, {
                    checkin:  checkin,
                    checkout: checkout,
                    adults:   adults,
                    children: children,
                    promo:    promo,
                    hotel_id: hotelId,
                    language: language,
                    currency: currency
                });

                if (value !== null && value !== '') {
                    params.push(encodeURIComponent(param.name) + '=' + encodeURIComponent(value));
                }
            });

            var fullUrl = baseUrl;
            if (params.length) {
                fullUrl += (fullUrl.indexOf('?') === -1 ? '?' : '&') + params.join('&');
            }

            if (typeof trackBookingClick === 'function') {
                trackBookingClick();
            }

            window.open(fullUrl, '_blank');
            return false;
        });

        /**
         * Resolve a parameter value based on its source and format
         */
        function resolveParamValue(param, data) {
            var source = param.value_source;
            var format = param.format || '';

            switch (source) {
                case 'checkin':
                    return formatDate(data.checkin, format);
                case 'checkout':
                    return formatDate(data.checkout, format);
                case 'adults':
                    return formatGuests(data.adults, format);
                case 'children':
                    return formatGuests(data.children, format);
                case 'rooms':
                    return '1';
                case 'promo':
                    return data.promo || '';
                case 'hotel_id':
                    return data.hotel_id;
                case 'language':
                    return formatLanguage(data.language, format);
                case 'currency':
                    return formatCurrency(data.currency, format);
                case 'static':
                    return param.default_value || '';
                case 'custom':
                    return param.default_value || '';
                default:
                    return '';
            }
        }

        function formatDate(dateStr, format) {
            if (!dateStr) return '';
            // dateStr is always YYYY-MM-DD from the hidden input
            var m = moment(dateStr, 'YYYY-MM-DD');
            if (!m.isValid()) return dateStr;

            var fmt = (format || '').toUpperCase();
            if (fmt === 'YYYYMMDD' || fmt === 'COMPACT') {
                return m.format('YYYYMMDD');
            } else if (fmt === 'DD/MM/YYYY') {
                return m.format('DD/MM/YYYY');
            } else if (fmt === 'MM/DD/YYYY') {
                return m.format('MM/DD/YYYY');
            } else if (fmt === 'DD-MM-YYYY') {
                return m.format('DD-MM-YYYY');
            } else {
                return m.format('YYYY-MM-DD');
            }
        }

        function formatGuests(count, format) {
            count = parseInt(count) || 0;
            var fmt = (format || '').toLowerCase();

            // Handle "repeated-letter:X" format (e.g. 3 adults => "A,A,A")
            var repeatMatch = fmt.match(/repeated[- ]?letter[: ]?(\w)/i);
            if (repeatMatch) {
                var letter = repeatMatch[1];
                var parts = [];
                for (var i = 0; i < count; i++) {
                    parts.push(letter.toUpperCase());
                }
                return parts.join(',');
            }

            return String(count);
        }

        function formatLanguage(lang, format) {
            if (!lang) return '';
            var fmt = (format || '').toLowerCase();
            // Extract just the 2-letter code
            var code = lang.substring(0, 2);
            if (fmt.indexOf('lowercase') !== -1) {
                return code.toLowerCase();
            }
            return code.toUpperCase();
        }

        function formatCurrency(currency, format) {
            if (!currency) return '';
            var fmt = (format || '').toLowerCase();
            if (fmt.indexOf('lowercase') !== -1) {
                return currency.toLowerCase();
            }
            return currency.toUpperCase();
        }
    });

})(jQuery);
