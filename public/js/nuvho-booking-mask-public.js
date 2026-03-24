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
        
        // *** GUEST SELECTOR FUNCTIONALITY ***

        // Common elements
        const guestTrigger = $('.nuvho-guest-trigger');
        const guestModal = $('.nuvho-guest-modal');
        const cancelBtn = $('.nuvho-cancel-btn');
        const doneBtn = $('.nuvho-done-btn');
        const adultCountSummary = $('.nuvho-adults-count');
        const kidsCountSummary = $('.nuvho-kids-count');
        const adultInput = $('#nuvho-adults-input');
        const kidsInput = $('#nuvho-kids-input');

        // Detect which variant is active
        const isDropdownMode = $('.nuvho-guest-modal-dropdown').length > 0;

        if (isDropdownMode) {
            // *** DROPDOWN MODE ***
            const adultsDropdown = $('#nuvho-adults-dropdown');
            const kidsDropdown = $('#nuvho-kids-dropdown');
            let savedAdults, savedKids;

            // Open modal
            guestTrigger.on('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                // Store current values for cancel
                savedAdults = adultInput.val();
                savedKids = kidsInput.val();
                // Sync dropdowns with hidden inputs
                adultsDropdown.val(savedAdults);
                kidsDropdown.val(savedKids);
                guestModal.fadeIn(200);
            });

            // Done — save dropdown values
            doneBtn.on('click', function(e) {
                e.preventDefault();
                var adults = parseInt(adultsDropdown.val());
                var kids = parseInt(kidsDropdown.val());
                adultInput.val(adults);
                kidsInput.val(kids);
                adultCountSummary.text(adults);
                kidsCountSummary.text(kids);
                guestModal.fadeOut(200);
            });

            // Cancel — restore original values
            cancelBtn.on('click', function(e) {
                e.preventDefault();
                adultsDropdown.val(savedAdults);
                kidsDropdown.val(savedKids);
                guestModal.fadeOut(200);
            });

            // Close on outside click
            $(document).on('click', function(e) {
                if (guestModal.is(':visible') &&
                    !guestModal.is(e.target) &&
                    guestModal.has(e.target).length === 0 &&
                    !guestTrigger.is(e.target) &&
                    guestTrigger.has(e.target).length === 0) {
                    adultsDropdown.val(savedAdults);
                    kidsDropdown.val(savedKids);
                    guestModal.fadeOut(200);
                }
            });

            guestModal.on('click', function(e) {
                e.stopPropagation();
            });
        } else {
            // *** STEPPER MODE (original) ***
            const adultCountDisplay = $('.nuvho-adults-number');
            const kidsCountDisplay = $('.nuvho-kids-number');
            let tempAdults = parseInt(adultInput.val()) || 2;
            let tempKids = parseInt(kidsInput.val()) || 0;
            let originalAdults, originalKids;

            guestTrigger.on('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                originalAdults = tempAdults;
                originalKids = tempKids;
                guestModal.fadeIn(200);
                updateDisplays();
                checkButtonStates();
            });

            cancelBtn.on('click', function(e) {
                e.preventDefault();
                closeModalWithoutSaving();
            });

            doneBtn.on('click', function(e) {
                e.preventDefault();
                adultInput.val(tempAdults);
                kidsInput.val(tempKids);
                adultCountSummary.text(tempAdults);
                kidsCountSummary.text(tempKids);
                guestModal.fadeOut(200);
            });

            $(document).on('click', function(e) {
                if (guestModal.is(':visible') &&
                    !guestModal.is(e.target) &&
                    guestModal.has(e.target).length === 0 &&
                    !guestTrigger.is(e.target) &&
                    guestTrigger.has(e.target).length === 0) {
                    closeModalWithoutSaving();
                }
            });

            guestModal.on('click', function(e) {
                e.stopPropagation();
            });

            $('.nuvho-circle-btn').on('click', function() {
                const target = $(this).data('target');
                const isIncrease = $(this).hasClass('nuvho-increase');
                if (target === 'adults') {
                    if (isIncrease && tempAdults < 10) tempAdults++;
                    else if (!isIncrease && tempAdults > 1) tempAdults--;
                } else if (target === 'kids') {
                    if (isIncrease && tempKids < 10) tempKids++;
                    else if (!isIncrease && tempKids > 0) tempKids--;
                }
                updateDisplays();
                updateSummaryDisplay();
                checkButtonStates();
            });

            function updateDisplays() {
                adultCountDisplay.text(tempAdults);
                kidsCountDisplay.text(tempKids);
            }
            function updateSummaryDisplay() {
                adultCountSummary.text(tempAdults);
                kidsCountSummary.text(tempKids);
            }
            function checkButtonStates() {
                $('.nuvho-decrease[data-target="adults"]').toggleClass('disabled', tempAdults <= 1);
                $('.nuvho-increase[data-target="adults"]').toggleClass('disabled', tempAdults >= 10);
                $('.nuvho-decrease[data-target="kids"]').toggleClass('disabled', tempKids <= 0);
                $('.nuvho-increase[data-target="kids"]').toggleClass('disabled', tempKids >= 10);
            }
            function closeModalWithoutSaving() {
                tempAdults = originalAdults;
                tempKids = originalKids;
                adultCountSummary.text(originalAdults);
                kidsCountSummary.text(originalKids);
                guestModal.fadeOut(200);
            }
        }

        // Initialize displays (both modes)
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