(function($) {
    'use strict';

    $(document).ready(function() {

        let lastValidParsed = {};
        let cachedPublicCSS = null;
        let suppressCSSSync = false;

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
         *  3. Guest selector – exact copy of public JS
         * ------------------------------------------------------------------ */
        let tempAdults = 2, tempKids = 0;
        let originalAdults, originalKids;

        function getActivePreviewModal() {
            var type = $('#nuvho-guest-selection-type').val();
            return type === 'dropdown' ? $('#nuvho-preview-guest-modal-dropdown') : $('#nuvho-preview-guest-modal');
        }

        $(document).on('click', '.nuvho-guest-trigger', function(e) {
            e.preventDefault();
            e.stopPropagation();
            originalAdults = tempAdults;
            originalKids   = tempKids;
            var $modal = getActivePreviewModal();
            // Sync dropdown selects if in dropdown mode
            $modal.find('.nuvho-guest-dropdown[data-target="adults"]').val(tempAdults);
            $modal.find('.nuvho-guest-dropdown[data-target="kids"]').val(tempKids);
            $modal.fadeIn(200);
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
            var $modal = getActivePreviewModal();
            // If dropdown mode, read values from select elements
            if ($modal.hasClass('nuvho-guest-modal-dropdown')) {
                tempAdults = parseInt($modal.find('.nuvho-guest-dropdown[data-target="adults"]').val()) || 2;
                tempKids = parseInt($modal.find('.nuvho-guest-dropdown[data-target="kids"]').val()) || 0;
            }
            $('#nuvho-adults-input').val(tempAdults);
            $('#nuvho-kids-input').val(tempKids);
            $('.nuvho-adults-count, .nuvho-adults-number').text(tempAdults);
            $('.nuvho-kids-count, .nuvho-kids-number').text(tempKids);
            $modal.fadeOut(200);
        });

        $('.nuvho-cancel-btn').on('click', function() {
            tempAdults = originalAdults;
            tempKids   = originalKids;
            updateGuestDisplay();
            $('.nuvho-guest-modal').fadeOut(200);
        });

        $(document).on('click', function(e) {
            var $visibleModal = $('.nuvho-guest-modal:visible');
            if ($visibleModal.length && !$(e.target).closest('.nuvho-guest-selector-container').length) {
                tempAdults = originalAdults;
                tempKids   = originalKids;
                updateGuestDisplay();
                $visibleModal.fadeOut(200);
            }
        });

        function updateGuestDisplay() {
            $('.nuvho-adults-number').text(tempAdults);
            $('.nuvho-kids-number').text(tempKids);
            $('.nuvho-adults-count').text(tempAdults);
            $('.nuvho-kids-count').text(tempKids);
        }

        /* ------------------------------------------------------------------
         *  4. Helpers
         * ------------------------------------------------------------------ */
        function radius(v) { return v === 'Rounded' ? '8px' : (v === 'Pill' ? '20px' : '0'); }

        function hexToRgba(hex, a) {
            const m = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
            if (!m) return hex;
            return `rgba(${parseInt(m[1],16)}, ${parseInt(m[2],16)}, ${parseInt(m[3],16)}, ${a})`;
        }

        function rgbaToHex(color) {
            const m = color.match(/rgba?\((\d+),\s*(\d+),\s*(\d+)/i);
            if (!m) return color;
            return '#' + [m[1], m[2], m[3]].map(function(n) {
                return ('0' + parseInt(n).toString(16)).slice(-2);
            }).join('');
        }

        function getColorVal(settingName) {
            const $el = $('input[name="nuvho_booking_mask_settings[' + settingName + ']"]');
            try { return $el.wpColorPicker('color') || $el.val(); }
            catch(e) { return $el.val(); }
        }

        /* ------------------------------------------------------------------
         *  5. CSS find-and-replace engine
         * ------------------------------------------------------------------ */

        // Find selector block in CSS text, replace (or insert) a property value.
        // Only modifies the FIRST match — top-level rules, not those inside @media.
        function updateCSSProperty(cssText, selector, property, newValue) {
            var esc = selector.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
            var blockRegex = new RegExp('(' + esc + '\\s*\\{)([^}]*)(\\})', 'g');
            var found = false;
            return cssText.replace(blockRegex, function(match, open, body, close) {
                if (found) return match;
                found = true;
                var propRegex = new RegExp('(\\s*' + property + '\\s*:\\s*)([^;]+)(;)');
                if (propRegex.test(body)) {
                    body = body.replace(propRegex, '$1' + newValue + '$3');
                } else {
                    body = body.trimEnd() + '\n    ' + property + ': ' + newValue + ';\n';
                }
                return open + body + close;
            });
        }

        // Push current form settings into the CSS textarea via in-place find-and-replace
        function syncSettingsToCSS() {
            if (suppressCSSSync) return;
            if (!$('#nuvho-show-css-toggle').is(':checked')) return;
            var ta = document.getElementById('nuvho-custom-css');
            if (!ta || !ta.value) return;

            var css = ta.value;
            var bgColor  = getColorVal('background_color');
            var opacity  = parseInt($('select[name="nuvho_booking_mask_settings[background_opacity]"]').val()) / 100;
            var fontColor = getColorVal('font_color');
            var font     = $('select[name="nuvho_booking_mask_settings[font]"]').val();
            var maskR    = radius($('select[name="nuvho_booking_mask_settings[booking_mask_border_radius]"]').val());
            var btnColor = getColorVal('button_color');
            var btnTxt   = getColorVal('button_text_color');
            var btnR     = radius($('select[name="nuvho_booking_mask_settings[button_border_radius]"]').val());
            var dpColor  = getColorVal('datepicker_color') || '#4c7380';
            var dpTxt    = getColorVal('datepicker_text_color') || '#ffffff';

            css = updateCSSProperty(css, '.nuvho-booking-mask-container', 'background-color', hexToRgba(bgColor, opacity));
            css = updateCSSProperty(css, '.nuvho-booking-mask-container', 'border-radius', maskR);
            css = updateCSSProperty(css, '.nuvho-booking-mask-container', 'opacity', String(opacity));
            css = updateCSSProperty(css, '.nuvho-booking-form', 'color', fontColor);
            css = updateCSSProperty(css, '.nuvho-booking-form', 'font-family', font === 'Default' ? 'inherit' : font);
            css = updateCSSProperty(css, '.nuvho-submit-field button[type="submit"]', 'background-color', btnColor);
            css = updateCSSProperty(css, '.nuvho-submit-field button[type="submit"]', 'color', btnTxt);
            css = updateCSSProperty(css, '.nuvho-submit-field button[type="submit"]', 'border-radius', btnR);
            css = updateCSSProperty(css, '.daterangepicker td.active', 'background-color', dpColor);
            css = updateCSSProperty(css, '.daterangepicker td.active', 'border-color', dpColor);
            css = updateCSSProperty(css, '.daterangepicker td.active', 'color', dpTxt);
            css = updateCSSProperty(css, '.daterangepicker td.in-range', 'background-color', dpColor);
            css = updateCSSProperty(css, '.daterangepicker .drp-buttons .btn.btn-primary', 'background-color', dpColor);
            css = updateCSSProperty(css, '.daterangepicker .drp-buttons .btn.btn-primary', 'border-color', dpColor);

            ta.value = css;

            // Re-parse so lastValidParsed reflects the updated CSS
            var parsed = parseCSSToProps(css);
            if (Object.keys(parsed).length > 0) lastValidParsed = parsed;
        }

        /* ------------------------------------------------------------------
         *  6. CSS parsing and preview application
         * ------------------------------------------------------------------ */

        // Parse CSS text into { selector: { property: value } } map
        function parseCSSToProps(cssText) {
            var result = {};
            var tempStyle = document.createElement('style');
            tempStyle.textContent = cssText;
            document.head.appendChild(tempStyle);
            try {
                Array.from(tempStyle.sheet.cssRules || []).forEach(function(rule) {
                    if (!rule.selectorText || !rule.style) return;
                    var sel = rule.selectorText.trim();
                    result[sel] = result[sel] || {};
                    for (var i = 0; i < rule.style.length; i++) {
                        var prop = rule.style[i];
                        result[sel][prop] = rule.style.getPropertyValue(prop);
                    }
                });
            } catch(e) {}
            document.head.removeChild(tempStyle);
            return result;
        }

        var cssPreviewMap = {
            '.nuvho-booking-mask-container':             '#nuvho-preview-container',
            '.nuvho-booking-form':                       '#nuvho-preview-container .nuvho-booking-form',
            '.nuvho-submit-field button':                '#nuvho-preview-button',
            '.nuvho-submit-field button[type="submit"]': '#nuvho-preview-button',
            '.nuvho-submit-field':                       '#nuvho-preview-container .nuvho-submit-field',
            '.daterangepicker td.active':                '.daterangepicker td.active',
            '.daterangepicker td.in-range':              '.daterangepicker td.in-range',
            '.daterangepicker .drp-buttons .btn.btn-primary': '.daterangepicker .drp-buttons .btn.btn-primary',
        };

        function applyParsedCSS(parsed) {
            Object.keys(parsed).forEach(function(sel) {
                var previewSel = cssPreviewMap[sel];
                if (!previewSel) return;
                var $el = $(previewSel);
                if (!$el.length) return;
                var props = parsed[sel];
                Object.keys(props).forEach(function(prop) {
                    $el.css(prop, props[prop]);
                });
            });
        }

        function syncFormFromCSS(parsed) {
            function setColor(selector, hex) {
                var $el = $(selector);
                if (!$el.length) return;
                $el.val(hex);
                try { $el.wpColorPicker('color', hex); } catch(e) { $el.trigger('change'); }
            }
            function setSelect(selector, val) {
                var $el = $(selector);
                if (!$el.length || !$el.find('option[value="' + val + '"]').length) return;
                $el.val(val).trigger('change');
            }
            function radiusToOption(v) {
                if (v === '8px')  return 'Rounded';
                if (v === '20px') return 'Pill';
                if (v === '0px' || v === '0') return 'Square';
                return null;
            }

            Object.keys(parsed).forEach(function(sel) {
                var props = parsed[sel];
                if (sel === '.nuvho-booking-mask-container') {
                    if (props['background-color']) setColor('#nuvho-bg-color', rgbaToHex(props['background-color']));
                    if (props['border-radius']) {
                        var opt = radiusToOption(props['border-radius']);
                        if (opt) setSelect('select[name="nuvho_booking_mask_settings[booking_mask_border_radius]"]', opt);
                    }
                }
                if (sel === '.nuvho-booking-form') {
                    if (props['color']) setColor('#nuvho-font-color', rgbaToHex(props['color']));
                    if (props['font-family']) {
                        var fontName = props['font-family'].split(',')[0].trim().replace(/['"]/g, '');
                        var displayName = fontName === 'inherit' ? 'Default' : fontName;
                        setSelect('select[name="nuvho_booking_mask_settings[font]"]', displayName);
                    }
                }
                if (sel === '.nuvho-submit-field button[type="submit"]' || sel === '.nuvho-submit-field button') {
                    if (props['background-color']) setColor('#nuvho-button-color', rgbaToHex(props['background-color']));
                    if (props['color'])            setColor('#nuvho-button-text-color', rgbaToHex(props['color']));
                    if (props['border-radius']) {
                        var btnOpt = radiusToOption(props['border-radius']);
                        if (btnOpt) setSelect('select[name="nuvho_booking_mask_settings[button_border_radius]"]', btnOpt);
                    }
                }
                if (sel === '.daterangepicker td.active') {
                    if (props['background-color']) setColor('#nuvho-datepicker-color', rgbaToHex(props['background-color']));
                    if (props['color'])            setColor('#nuvho-datepicker-text-color', rgbaToHex(props['color']));
                }
            });
        }

        /* ------------------------------------------------------------------
         *  7. Live preview of all visual settings
         * ------------------------------------------------------------------ */
        $('.nuvho-color-picker').not('#nuvho-datepicker-color, #nuvho-datepicker-text-color').wpColorPicker({ change: updatePreview });
        $('select[name^="nuvho_booking_mask_settings"], input[name^="nuvho_booking_mask_settings"]').on('change input', updatePreview);
        $('input[name="nuvho_booking_mask_settings[show_promo_code]"]').on('change', function() {
            $('#nuvho-preview-promo-field').toggle($(this).is(':checked'));
            updatePreview();
        });

        function updatePreview() {
            var s = {};
            s.bg         = getColorVal('background_color');
            s.opacity    = parseInt($('select[name="nuvho_booking_mask_settings[background_opacity]"]').val()) / 100;
            s.btnColor   = getColorVal('button_color');
            s.btnTextCol = getColorVal('button_text_color');
            s.fontColor  = getColorVal('font_color');
            s.btnText    = $('input[name="nuvho_booking_mask_settings[button_text]"]').val();
            s.font       = $('select[name="nuvho_booking_mask_settings[font]"]').val();
            s.maskRadius = radius($('select[name="nuvho_booking_mask_settings[booking_mask_border_radius]"]').val());
            s.btnRadius  = radius($('select[name="nuvho_booking_mask_settings[button_border_radius]"]').val());
            s.dpColor    = getColorVal('datepicker_color') || '#4c7380';
            s.dpTextCol  = getColorVal('datepicker_text_color') || '#ffffff';

            var rgba = hexToRgba(s.bg, s.opacity);
            $('#nuvho-preview-container').css({ 'background-color': rgba, 'opacity': s.opacity, 'border-radius': s.maskRadius });

            var family = s.font === 'Default' ? 'inherit' : s.font;
            $('.nuvho-booking-form, #nuvho-preview-promo-field label').css({ 'color': s.fontColor, 'font-family': family });
            $('.nuvho-guest-trigger, .nuvho-guest-count, .nuvho-guest-count span').css('color', s.fontColor);

            $('#nuvho-preview-button').css({ 'background-color': s.btnColor, 'color': s.btnTextCol, 'border-radius': s.btnRadius }).text(s.btnText);

            $('.nuvho-decrease:not(.disabled), .nuvho-increase').css('background-color', rgba);
            $('.nuvho-done-btn').css('background-color', s.btnColor);

            // Datepicker colors via <style> tag (survives calendar re-renders)
            var dpCSS = '.daterangepicker td.active, .daterangepicker td.start-date, .daterangepicker td.end-date,'
                + ' .daterangepicker td.active:hover, .daterangepicker td.start-date:hover, .daterangepicker td.end-date:hover,'
                + ' .daterangepicker td.start-date.in-range, .daterangepicker td.start-date.in-range:hover,'
                + ' .daterangepicker td.end-date.in-range, .daterangepicker td.end-date.in-range:hover'
                + ' { background-color: ' + s.dpColor + ' !important; border-color: ' + s.dpColor + ' !important; color: ' + s.dpTextCol + ' !important; opacity: 1 !important; }'
                + ' .daterangepicker td.in-range { background-color: ' + s.dpColor + ' !important; opacity: 0.4; }'
                + ' .daterangepicker .drp-buttons .btn-primary { background-color: ' + s.dpColor + ' !important; border-color: ' + s.dpColor + ' !important; }';
            var $dpStyle = $('#nuvho-admin-dp-preview');
            if (!$dpStyle.length) {
                $dpStyle = $('<style id="nuvho-admin-dp-preview">').appendTo('head');
            }
            $dpStyle.text(dpCSS);

            // Sync settings into CSS textarea (in-place find-and-replace)
            syncSettingsToCSS();

            // Re-apply custom CSS on top of inline styles for preview
            if ($('#nuvho-show-css-toggle').is(':checked')) {
                applyParsedCSS(lastValidParsed);
            }
        }

        /* ------------------------------------------------------------------
         *  8. Engine selector – show correct panels & default URL
         * ------------------------------------------------------------------ */
        $('#nuvho-booking-option').on('change', function() {
            var e = $(this).val();
            var idLabel = 'Hotel ID:';
            if (e === 'SiteMinder') idLabel = 'Property:';
            else if (e === 'GuestCentric') idLabel = 'API Key:';
            else if (e === 'Clock PMS') idLabel = 'Hotel ID / Property ID:';
            $('#hotel-id-label').text(idLabel);
            $('#accor-specific-settings, #simple-booking-specific-settings, #custom-engine-settings').hide();
            if (e === 'Accor') $('#accor-specific-settings').show();
            if (e.includes('Simple Booking') || e === 'Little Hotelier') $('#simple-booking-specific-settings').show();
            if (e === 'Custom') $('#custom-engine-settings').show();

            var defaults = {
                'Accor':'https://all.accor.com/ssr/app/accor/rates',
                'Simple Booking v2':'https://www.simplebooking.it/ibe2/hotel',
                'Cloudbeds':'https://hotels.cloudbeds.com/reservation/',
                'Staah':'https://secure.staah.com/common-cgi/package/packagebooking.pl',
                'SiteMinder':'https://book-directonline.com/properties',
                'RMS':'https://rms.rezexchange.com/bookings',
                'Protel':'https://booking.protel.net/booking',
                'MEWS':'https://www.mewssystems.com/booking',
                'TravelClick':'https://gc.synxis.com/rez.aspx',
                'GuestCentric':'https://secure.guestcentric.net/api/bg/book.php',
                'Beds24':'https://www.beds24.com/booking2.php',
                'Little Hotelier':'https://app.littlehotelier.com/properties/',
                'Bookassist':'https://booking.bookassist.com/',
                'Cubilis':'https://booking.cubilis.eu/',
                'Clock PMS':'https://sky-eu1.clock-software.com/'
            };
            if (defaults[e]) {
                $('input[name="nuvho_booking_mask_settings[url]"]').val(defaults[e]).attr('placeholder', '');
            } else if (e === 'Custom') {
                $('input[name="nuvho_booking_mask_settings[url]"]').val('').attr('placeholder', 'Enter your custom booking URL');
            }
        }).trigger('change');

        /* ------------------------------------------------------------------
         *  8a. Region filter – filter booking engine dropdown by region
         * ------------------------------------------------------------------ */
        var regionMap = (typeof nuvhoAdminData !== 'undefined' && nuvhoAdminData.region_engine_map) ? nuvhoAdminData.region_engine_map : {};

        $('#nuvho-region-filter').on('change', function() {
            var region = $(this).val();
            var $engineSelect = $('#nuvho-booking-option');

            $engineSelect.find('option').each(function() {
                var engine = $(this).val();
                // Custom is always visible
                if (engine === 'Custom') {
                    $(this).show();
                    return;
                }
                if (region === 'all' || !regionMap[region]) {
                    $(this).show();
                } else {
                    var visible = regionMap[region].indexOf(engine) !== -1;
                    if (visible) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                }
            });

            // If currently selected engine is now hidden, reset to first visible
            var $selected = $engineSelect.find('option:selected');
            if ($selected.css('display') === 'none' || $selected.is(':hidden')) {
                $engineSelect.find('option').each(function() {
                    if ($(this).css('display') !== 'none') {
                        $(this).prop('selected', true);
                        $engineSelect.trigger('change');
                        return false;
                    }
                });
            }
        });

        /* ------------------------------------------------------------------
         *  8b. Guest Selection Type toggle (stepper ↔ dropdown preview)
         * ------------------------------------------------------------------ */
        $('#nuvho-guest-selection-type').on('change', function() {
            var type = $(this).val();
            if (type === 'dropdown') {
                $('#nuvho-preview-guest-modal').hide();
                $('#nuvho-preview-guest-modal-dropdown').hide(); // will show on trigger click
            } else {
                $('#nuvho-preview-guest-modal-dropdown').hide();
                $('#nuvho-preview-guest-modal').hide(); // will show on trigger click
            }
        });

        /* ------------------------------------------------------------------
         *  9. Advanced: Show advanced options
         * ------------------------------------------------------------------ */
        $('#nuvho-show-css-toggle').on('change', function() {
            var cssRow = document.getElementById('nuvho-css-editor-row');
            $('#nuvho-datepicker-color-row, #nuvho-datepicker-text-color-row').toggle(this.checked);
            if (this.checked) {
                // Re-initialize WP Color Picker now that fields are visible
                $('#nuvho-datepicker-color, #nuvho-datepicker-text-color').wpColorPicker({ change: updatePreview });
                cssRow.style.display = '';
                var ta = document.getElementById('nuvho-custom-css');
                var isStale = ta.value.includes('/* Auto-generated') ||
                              ta.value.includes('/* == Appearance Settings Override') ||
                              (ta.value.trim().length > 0 && ta.value.trim().length < 1000);
                if (!ta.value.trim() || isStale) {
                    // Empty or stale — fetch full public CSS and apply current settings
                    function populateTextarea() {
                        ta.value = cachedPublicCSS || '';
                        // Apply current settings values into the fetched CSS
                        suppressCSSSync = false;
                        syncSettingsToCSS();
                        var parsed = parseCSSToProps(ta.value);
                        if (Object.keys(parsed).length > 0) {
                            lastValidParsed = parsed;
                            updatePreview();
                        }
                    }
                    if (cachedPublicCSS !== null) {
                        populateTextarea();
                    } else {
                        $.get(nuvhoAdminData.public_css_url).done(function(css) {
                            cachedPublicCSS = css;
                            populateTextarea();
                        }).fail(function() {
                            cachedPublicCSS = '';
                            populateTextarea();
                        });
                    }
                } else {
                    // User's saved CSS — just parse and apply
                    var parsed = parseCSSToProps(ta.value.trim());
                    if (Object.keys(parsed).length > 0) {
                        lastValidParsed = parsed;
                        updatePreview();
                    }
                }
            } else {
                cssRow.style.display = 'none';
                lastValidParsed = {};
                updatePreview();
            }
        });

        /* ------------------------------------------------------------------
         *  10. CSS textarea input handler (CSS → settings + preview)
         * ------------------------------------------------------------------ */
        $('#nuvho-custom-css').on('input', function() {
            var cssText = this.value.trim();
            if (!cssText) {
                lastValidParsed = {};
                updatePreview();
                return;
            }
            var parsed = parseCSSToProps(cssText);
            if (Object.keys(parsed).length === 0) return;
            lastValidParsed = parsed;
            suppressCSSSync = true;
            syncFormFromCSS(parsed);
            updatePreview();
            suppressCSSSync = false;
        });

        /* ------------------------------------------------------------------
         *  11. Custom Engine – Fetch & Parse Parameters via Claude API
         * ------------------------------------------------------------------ */
        var valueSources = [
            'checkin', 'checkout', 'adults', 'children', 'rooms',
            'promo', 'hotel_id', 'language', 'currency', 'custom', 'static'
        ];

        function escHtml(str) {
            return $('<div>').text(str || '').html();
        }

        function populateParamEditor(data) {
            var $editor = $('#nuvho-param-editor');
            var $tbody  = $('#nuvho-param-table tbody');
            $tbody.empty();

            if (data.engine_name) {
                $('#nuvho-detected-engine-name').text('Detected engine: ' + data.engine_name);
            } else {
                $('#nuvho-detected-engine-name').text('');
            }

            if (data.parameters && data.parameters.length) {
                data.parameters.forEach(function(param) {
                    var sourceOptions = valueSources.map(function(src) {
                        var selected = (src === param.value_source) ? ' selected' : '';
                        return '<option value="' + src + '"' + selected + '>' + src + '</option>';
                    }).join('');

                    var row = '<tr>' +
                        '<td><input type="text" class="param-name" value="' + escHtml(param.name) + '" style="width:100%;" /></td>' +
                        '<td><select class="param-source">' + sourceOptions + '</select></td>' +
                        '<td><input type="text" class="param-format" value="' + escHtml(param.format || '') + '" placeholder="e.g. YYYY-MM-DD" style="width:100%;" /></td>' +
                        '<td><input type="text" class="param-desc" value="' + escHtml(param.description || '') + '" style="width:100%;" /></td>' +
                        '</tr>';
                    $tbody.append(row);
                });
            }

            if (data.has_promo) {
                $('#nuvho-custom-has-promo').prop('checked', true);
            }

            updateCustomEngineConfig(data);
            $editor.show();
        }

        function updateCustomEngineConfig(data) {
            var config = {
                engine_name: data.engine_name || 'Custom',
                base_url_pattern: data.base_url_pattern || '',
                method: data.method || 'GET',
                hotel_id_in_path: data.hotel_id_in_path || false,
                hotel_id_path_position: data.hotel_id_path_position || '',
                has_promo: $('#nuvho-custom-has-promo').is(':checked'),
                parameters: []
            };

            $('#nuvho-param-table tbody tr').each(function() {
                config.parameters.push({
                    name: $(this).find('.param-name').val(),
                    value_source: $(this).find('.param-source').val(),
                    format: $(this).find('.param-format').val(),
                    description: $(this).find('.param-desc').val()
                });
            });

            $('#nuvho-custom-engine-config').val(JSON.stringify(config));
        }

        // Update hidden config whenever editor fields change
        $(document).on('change input', '#nuvho-param-table input, #nuvho-param-table select, #nuvho-custom-has-promo', function() {
            var existingConfig = {};
            try {
                existingConfig = JSON.parse($('#nuvho-custom-engine-config').val() || '{}');
            } catch(e) { /* ignore */ }
            updateCustomEngineConfig(existingConfig);
        });

        // Fetch Parameters button
        $('#nuvho-fetch-params-btn').on('click', function() {
            var url = $('input[name="nuvho_booking_mask_settings[url]"]').val();
            if (!url) {
                alert('Please enter a booking engine URL first.');
                return;
            }

            var $btn    = $(this);
            var $status = $('#nuvho-fetch-status');
            $btn.prop('disabled', true);
            $status.text('Analyzing URL...').css('color', '#666');

            $.ajax({
                url: nuvhoAdminData.ajax_url,
                method: 'POST',
                data: {
                    action: 'nuvho_fetch_engine_params',
                    nonce:  nuvhoAdminData.nonce,
                    url:    url,
                    mode:   'identify'
                },
                success: function(response) {
                    $btn.prop('disabled', false);
                    if (response.success) {
                        if (response.data.identified) {
                            $status.text('Engine identified: ' + response.data.engine_name).css('color', 'green');
                            populateParamEditor(response.data);
                        } else {
                            $status.text('Engine not recognized. Please provide a sample booking URL below.').css('color', 'orange');
                            $('#nuvho-sample-url-row').show();
                        }
                    } else {
                        $status.text('Error: ' + response.data.message).css('color', 'red');
                    }
                },
                error: function() {
                    $btn.prop('disabled', false);
                    $status.text('Request failed. Check your API key and try again.').css('color', 'red');
                }
            });
        });

        // Parse Sample URL button
        $('#nuvho-parse-sample-btn').on('click', function() {
            var baseUrl   = $('input[name="nuvho_booking_mask_settings[url]"]').val();
            var sampleUrl = $('#nuvho-sample-url').val();
            if (!sampleUrl) {
                alert('Please paste a full sample booking URL with parameters.');
                return;
            }

            var $status = $('#nuvho-fetch-status');
            $status.text('Parsing sample URL...').css('color', '#666');
            $('#nuvho-parse-sample-btn').prop('disabled', true);

            $.ajax({
                url: nuvhoAdminData.ajax_url,
                method: 'POST',
                data: {
                    action:     'nuvho_fetch_engine_params',
                    nonce:      nuvhoAdminData.nonce,
                    url:        baseUrl,
                    sample_url: sampleUrl,
                    mode:       'parse'
                },
                success: function(response) {
                    $('#nuvho-parse-sample-btn').prop('disabled', false);
                    if (response.success) {
                        $status.text('Parameters parsed successfully.').css('color', 'green');
                        populateParamEditor(response.data);
                    } else {
                        $status.text('Error: ' + response.data.message).css('color', 'red');
                    }
                },
                error: function() {
                    $('#nuvho-parse-sample-btn').prop('disabled', false);
                    $status.text('Request failed. Check your API key and try again.').css('color', 'red');
                }
            });
        });

        // On page load, if Custom is selected and config exists, restore the editor
        (function() {
            var configVal = $('#nuvho-custom-engine-config').val();
            if ($('#nuvho-booking-option').val() === 'Custom' && configVal) {
                try {
                    var config = JSON.parse(configVal);
                    if (config.parameters && config.parameters.length) {
                        populateParamEditor(config);
                    }
                } catch(e) { /* ignore */ }
            }
        })();

        /* ------------------------------------------------------------------
         *  12. Page-load initialisation
         * ------------------------------------------------------------------ */
        if ($('#nuvho-show-css-toggle').is(':checked')) {
            var ta = document.getElementById('nuvho-custom-css');
            if (ta && ta.value.trim()) {
                var parsed = parseCSSToProps(ta.value.trim());
                if (Object.keys(parsed).length > 0) lastValidParsed = parsed;
            }
        }

        setTimeout(updatePreview, 150); // after WPColorPicker init
    });

})(jQuery);
