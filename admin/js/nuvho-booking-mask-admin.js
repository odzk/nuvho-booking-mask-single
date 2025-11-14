(function($) {
    'use strict';

    $(document).ready(function() {

        /* ------------------------------------------------------------------
         *  1. Load the same assets the public mask uses
         * ------------------------------------------------------------------ */
        // Moment + daterangepicker (already enqueued on admin via class)
        // Public CSS is enqueued in the admin class (see step 3)

        /* ------------------------------------------------------------------
         *  2. Initialise date picker (exact copy of public logic)
         * ------------------------------------------------------------------ */
        const localeMap = {
            'English (US)': 'en',    'English (UK)': 'en-gb', 'Italian': 'it',
            'French': 'fr',          'German': 'de',          'Spanish': 'es',
            'Portuguese': 'pt',      'Dutch': 'nl',           'Russian': 'ru',
            'Chinese': 'zh-cn',      'Japanese': 'ja'
        };
        const formatMap = {
            'English (US)': 'MM/DD/YYYY', 'English (UK)': 'DD/MM/YYYY',
            'Italian': 'DD/MM/YYYY',      'French': 'DD/MM/YYYY',
            'German': 'DD.MM.YYYY',       'Spanish': 'DD/MM/YYYY',
            'Portuguese': 'DD/MM/YYYY',   'Dutch': 'DD-MM-YYYY',
            'Russian': 'DD.MM.YYYY',      'Chinese': 'YYYY-MM-DD',
            'Japanese': 'YYYY/MM/DD'
        };

        const lang = $('select[name="nuvho_booking_mask_settings[language]"]').val() || 'English (US)';
        const dateFormat = formatMap[lang] || 'MM/DD/YYYY';

        $('#nuvho-preview-date-picker').daterangepicker({
            startDate: moment(),
            endDate: moment().add(1, 'day'),
            minDate: moment(),
            opens: 'center',
            autoApply: true,
            locale: { format: dateFormat }
        }, function(start, end) {
            $('#nuvho-preview-checkin').val(start.format('YYYY-MM-DD'));
            $('#nuvho-preview-checkout').val(end.format('YYYY-MM-DD'));
        });

        /* ------------------------------------------------------------------
         *  3. Guest selector – **exact copy of public JS**
         * ------------------------------------------------------------------ */
        let tempAdults = 2, tempKids = 0;
        let originalAdults, originalKids;

        $(document).on('click', '.nuvho-guest-trigger', function(e) {
            e.preventDefault();
            e.stopPropagation();
            originalAdults = tempAdults;
            originalKids   = tempKids;
            $('#nuvho-preview-guest-modal').fadeIn(200);
            updateGuestDisplay();
        });

        $('.nuvho-circle-btn').on('click', function() {
            const target = $(this).data('target');
            const inc    = $(this).hasClass('nuvho-increase');
            if (target === 'adults') {
                tempAdults = Math.max(1, Math.min(10, tempAdults + (inc ? 1 : -1)));
            } else {
                tempKids = Math.max(0, Math.min(10, tempKids + (inc ? 1 : -1)));
            }
            updateGuestDisplay();
        });

        $('.nuvho-done-btn').on('click', function() {
            $('#nuvho-adults-input').val(tempAdults);
            $('#nuvho-kids-input').val(tempKids);
            $('.nuvho-adults-count, .nuvho-adults-number').text(tempAdults);
            $('.nuvho-kids-count, .nuvho-kids-number').text(tempKids);
            $('#nuvho-preview-guest-modal').fadeOut(200);
        });

        $('.nuvho-cancel-btn').on('click', function() {
            tempAdults = originalAdults;
            tempKids   = originalKids;
            updateGuestDisplay();
            $('#nuvho-preview-guest-modal').fadeOut(200);
        });

        $(document).on('click', function(e) {
            const $modal = $('#nuvho-preview-guest-modal');
            if ($modal.is(':visible') && !$(e.target).closest('.nuvho-guest-selector-container').length) {
                $modal.fadeOut(200);
            }
        });

        function updateGuestDisplay() {
            $('.nuvho-adults-number').text(tempAdults);
            $('.nuvho-kids-number').text(tempKids);
            $('.nuvho-adults-count').text(tempAdults);
            $('.nuvho-kids-count').text(tempKids);
        }

        /* ------------------------------------------------------------------
         *  4. Live preview of all visual settings
         * ------------------------------------------------------------------ */
        $('.nuvho-color-picker').wpColorPicker({ change: updatePreview });
        $('select[name^="nuvho_booking_mask_settings"], input[name^="nuvho_booking_mask_settings"]').on('change input', updatePreview);
        $('input[name="nuvho_booking_mask_settings[show_promo_code]"]').on('change', function() {
            $('#nuvho-preview-promo-field').toggle($(this).is(':checked'));
            updatePreview();
        });

        function updatePreview() {
            const s = {};
            s.bg         = $('input[name="nuvho_booking_mask_settings[background_color]"]').wpColorPicker('color') || $('input[name="nuvho_booking_mask_settings[background_color]"]').val();
            s.opacity    = parseInt($('select[name="nuvho_booking_mask_settings[background_opacity]"]').val()) / 100;
            s.btnColor   = $('input[name="nuvho_booking_mask_settings[button_color]"]').wpColorPicker('color') || $('input[name="nuvho_booking_mask_settings[button_color]"]').val();
            s.btnTextCol = $('input[name="nuvho_booking_mask_settings[button_text_color]"]').wpColorPicker('color') || $('input[name="nuvho_booking_mask_settings[button_text_color]"]').val();
            s.fontColor  = $('input[name="nuvho_booking_mask_settings[font_color]"]').wpColorPicker('color') || $('input[name="nuvho_booking_mask_settings[font_color]"]').val();
            s.btnText    = $('input[name="nuvho_booking_mask_settings[button_text]"]').val();
            s.font       = $('select[name="nuvho_booking_mask_settings[font]"]').val();
            s.maskRadius = radius($('select[name="nuvho_booking_mask_settings[booking_mask_border_radius]"]').val());
            s.btnRadius  = radius($('select[name="nuvho_booking_mask_settings[button_border_radius]"]').val());

            const rgba = hexToRgba(s.bg, s.opacity);
            $('#nuvho-preview-container').css({ 'background-color': rgba, 'opacity': s.opacity, 'border-radius': s.maskRadius });

            const family = s.font === 'Default' ? 'inherit' : s.font;
            $('.nuvho-booking-form, #nuvho-preview-promo-field label').css({ 'color': s.fontColor, 'font-family': family });
            $('.nuvho-guest-trigger, .nuvho-guest-count, .nuvho-guest-count span').css('color', s.fontColor);

            $('#nuvho-preview-button').css({ 'background-color': s.btnColor, 'color': s.btnTextCol, 'border-radius': s.btnRadius }).text(s.btnText);
        }

        function radius(v) { return v === 'Rounded' ? '8px' : (v === 'Pill' ? '20px' : '0'); }
        function hexToRgba(hex, a) {
            const m = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
            if (!m) return hex;
            return `rgba(${parseInt(m[1],16)},${parseInt(m[2],16)},${parseInt(m[3],16)},${a})`;
        }

        /* ------------------------------------------------------------------
         *  5. Engine selector – show correct panels & default URL
         * ------------------------------------------------------------------ */
        $('#nuvho-booking-option').on('change', function() {
            const e = $(this).val();
            $('#hotel-id-label').text(e === 'SiteMinder' ? 'Property:' : 'Hotel ID:');
            $('#accor-specific-settings, #simple-booking-specific-settings').hide();
            if (e === 'Accor') $('#accor-specific-settings').show();
            if (e.includes('Simple Booking')) $('#simple-booking-specific-settings').show();

            const defaults = {
                'Accor':'https://all.accor.com/ssr/app/accor/rates',
                'Simple Booking v1':'https://www.simplebooking.it/ibe/search',
                'Simple Booking v2':'https://www.simplebooking.it/ibe2/hotel',
                'Cloudbeds':'https://hotels.cloudbeds.com/reservation/',
                'Staah':'https://secure.staah.com/common-cgi/package/packagebooking.pl',
                'SiteMinder':'https://book-directonline.com/properties',
                'RMS':'https://rms.rezexchange.com/bookings',
                'Protel':'https://booking.protel.net/booking',
                'MEWS':'https://www.mewssystems.com/booking',
                'TravelClick':'https://gc.synxis.com/rez.aspx',
                'Frome':'https://frome.bookings.com/reservation'
            };
            if (defaults[e]) $('input[name="nuvho_booking_mask_settings[url]"]').val(defaults[e]);
        }).trigger('change');

        /* ------------------------------------------------------------------
         *  6. Initial render
         * ------------------------------------------------------------------ */
        setTimeout(updatePreview, 150); // after WPColorPicker init
    });

})(jQuery);