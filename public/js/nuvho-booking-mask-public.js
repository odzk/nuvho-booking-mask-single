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
                    format: nuvhoBookingSettings.date_format || 'MM/DD/YYYY',
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
                autoUpdateInput: true
            }, function(start, end, label) {
                // Update hidden inputs with the selected dates in YYYY-MM-DD format
                checkinInput.val(formatDateForInput(start));
                checkoutInput.val(formatDateForInput(end));
            });
            
            // Set language if specified
            if (nuvhoBookingSettings.locale && nuvhoBookingSettings.locale !== 'en') {
                moment.locale(nuvhoBookingSettings.locale);
            }
            
            // Apply custom styling to match the rest of the form
            setTimeout(function() {
                $('.daterangepicker').addClass('nuvho-daterangepicker');
            }, 100);
        });

        // Inject datepicker active-state styles using the admin background color + opacity
        (function injectDatepickerStyles() {
            var hex     = (nuvhoBookingSettings.background_color || '#4c7380').replace('#', '');
            var opacity = parseFloat(
                (nuvhoBookingSettings.background_opacity || '100').toString().replace('%', '')
            ) / 100;

            // Parse hex → r, g, b
            var r    = parseInt(hex.substring(0, 2), 16);
            var g    = parseInt(hex.substring(2, 4), 16);
            var b    = parseInt(hex.substring(4, 6), 16);
            var rgba = 'rgba(' + r + ', ' + g + ', ' + b + ', ' + opacity + ')';

            var css = [
                '.daterangepicker td.active,',
                '.daterangepicker td.active:hover,',
                '.daterangepicker td.start-date,',
                '.daterangepicker td.start-date:hover,',
                '.daterangepicker td.end-date,',
                '.daterangepicker td.end-date:hover,',
                '.daterangepicker td.start-date.in-range,',
                '.daterangepicker td.start-date.in-range:hover,',
                '.daterangepicker td.end-date.in-range,',
                '.daterangepicker td.end-date.in-range:hover {',
                '    background-color: ' + rgba + ' !important;',
                '    border-color: '     + rgba + ' !important;',
                '    color: #fff !important;',
                '    opacity: 1 !important;',
                '}',
                '.daterangepicker td.in-range {',
                '    background-color: ' + rgba + ' !important;',
                '    opacity: 0.4;',
                '}',
                '.daterangepicker .drp-buttons .btn-primary {',
                '    background-color: ' + rgba + ' !important;',
                '    border-color: '     + rgba + ' !important;',
                '}'
            ].join('\n');

            // Create our style tag
            var style = document.createElement('style');
            style.id   = 'nuvho-daterangepicker-colors';
            style.type = 'text/css';
            style.appendChild(document.createTextNode(css));
            document.head.appendChild(style);

            // Watch for daterangepicker injecting its own styles after ours,
            // and move ours back to the end of <head> so we always win
            var observer = new MutationObserver(function(mutations) {
                mutations.forEach(function(mutation) {
                    mutation.addedNodes.forEach(function(node) {
                        if (node !== style && (node.tagName === 'STYLE' || node.tagName === 'LINK')) {
                            // Another stylesheet was added — move ours to the end
                            document.head.appendChild(style);
                        }
                    });
                });
            });

            observer.observe(document.head, { childList: true });
        })();

        // Initialize original stepper controls if present (backward compatibility)
        $(document).on('click', '.nuvho-stepper-btn', function() {
            var input = $(this).data('input');
            var inputField = $('#' + input);
            var currentValue = parseInt(inputField.val());
            var minValue = parseInt(inputField.attr('min'));
            var maxValue = parseInt(inputField.attr('max'));
            
            if ($(this).hasClass('nuvho-stepper-plus')) {
                // Handle plus button
                if (currentValue < maxValue) {
                    inputField.val(currentValue + 1);
                }
            } else if ($(this).hasClass('nuvho-stepper-minus')) {
                // Handle minus button
                if (currentValue > minValue) {
                    inputField.val(currentValue - 1);
                }
            }
        });
        
        // *** NEW GUEST SELECTOR FUNCTIONALITY ***
        
        // Elements
        const guestTrigger = $('.nuvho-guest-trigger');
        const guestModal = $('.nuvho-guest-modal');
        const cancelBtn = $('.nuvho-cancel-btn');
        const doneBtn = $('.nuvho-done-btn');
        
        // Counters and displays - FIXED CLASS NAMES
        const adultCountDisplay = $('.nuvho-adults-number');
        const kidsCountDisplay = $('.nuvho-kids-number');
        const adultCountSummary = $('.nuvho-adults-count');
        const kidsCountSummary = $('.nuvho-kids-count');
        
        // Hidden inputs
        const adultInput = $('#nuvho-adults-input');
        const kidsInput = $('#nuvho-kids-input');
        
        // State variables
        let tempAdults = parseInt(adultInput.val()) || 2;
        let tempKids = parseInt(kidsInput.val()) || 0;
        let originalAdults, originalKids;
        
        // Open modal
        guestTrigger.on('click', function(e) {
            e.preventDefault();
            e.stopPropagation(); // Prevent the click from propagating to document
            
            // Store original values in case of cancel
            originalAdults = tempAdults;
            originalKids = tempKids;
            
            // Show the modal
            guestModal.fadeIn(200);
            
            // Update displays in modal
            updateDisplays();
            checkButtonStates();
        });
        
        // Close modal on Cancel button click
        cancelBtn.on('click', function(e) {
            e.preventDefault();
            closeModalWithoutSaving();
        });
        
        // Save values on Done button click
        doneBtn.on('click', function(e) {
            e.preventDefault();
            
            // Update hidden inputs
            adultInput.val(tempAdults);
            kidsInput.val(tempKids);
            
            // Update summary display
            adultCountSummary.text(tempAdults);
            kidsCountSummary.text(tempKids);
            
            // Close the modal
            guestModal.fadeOut(200);
        });
        
        // Close modal when clicking outside
        $(document).on('click', function(e) {
            if (guestModal.is(':visible') && 
                !guestModal.is(e.target) && 
                guestModal.has(e.target).length === 0 && 
                !guestTrigger.is(e.target) && 
                guestTrigger.has(e.target).length === 0) {
                
                closeModalWithoutSaving();
            }
        });
        
        // Stop propagation for clicks inside the modal
        guestModal.on('click', function(e) {
            e.stopPropagation();
        });
        
        // Increment/Decrement buttons
        $('.nuvho-circle-btn').on('click', function() {
            const target = $(this).data('target');
            const isIncrease = $(this).hasClass('nuvho-increase');
            
            if (target === 'adults') {
                if (isIncrease && tempAdults < 10) {
                    tempAdults++;
                } else if (!isIncrease && tempAdults > 1) {
                    tempAdults--;
                }
            } else if (target === 'kids') {
                if (isIncrease && tempKids < 10) {
                    tempKids++;
                } else if (!isIncrease && tempKids > 0) {
                    tempKids--;
                }
            }
            
            // Update displays both in modal and in summary (real-time update)
            updateDisplays();
            updateSummaryDisplay();
            checkButtonStates();
        });
        
        // Helper functions
        function updateDisplays() {
            adultCountDisplay.text(tempAdults);
            kidsCountDisplay.text(tempKids);
        }
        
        function updateSummaryDisplay() {
            // Update the summary display in real-time
            adultCountSummary.text(tempAdults);
            kidsCountSummary.text(tempKids);
        }
        
        function checkButtonStates() {
            // Adults buttons
            $('.nuvho-decrease[data-target="adults"]')
                .toggleClass('disabled', tempAdults <= 1);
                
            $('.nuvho-increase[data-target="adults"]')
                .toggleClass('disabled', tempAdults >= 10);
                
            // Kids buttons
            $('.nuvho-decrease[data-target="kids"]')
                .toggleClass('disabled', tempKids <= 0);
                
            $('.nuvho-increase[data-target="kids"]')
                .toggleClass('disabled', tempKids >= 10);
        }
        
        function closeModalWithoutSaving() {
            // Reset to original values
            tempAdults = originalAdults;
            tempKids = originalKids;
            
            // Reset the summary display to original values
            adultCountSummary.text(originalAdults);
            kidsCountSummary.text(originalKids);
            
            // Close the modal
            guestModal.fadeOut(200);
        }
        
        // Initialize displays
        adultCountSummary.text(adultInput.val() || '2');
        kidsCountSummary.text(kidsInput.val() || '0');
        
        // END OF GUEST SELECTOR FUNCTIONALITY
        
        // Track booking form submissions
        $(document).on('submit', '#nuvho-booking-form', function() {
            // Track the click
            trackBookingClick();
            
            // Only allow the form submission if dates are selected
            const checkin = $(this).find('#nuvho-checkin').val();
            const checkout = $(this).find('#nuvho-checkout').val();
            
            if (!checkin || !checkout) {
                alert('Please select both check-in and check-out dates.');
                return false;
            }
            
            return true;
        });
    });

    // Function to track booking clicks (can be called from other scripts)
    window.trackBookingClick = function() {
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
    };

})(jQuery);