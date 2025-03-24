(function($) {
    'use strict';

    /**
     * Reports functionality for the Nuvho Booking Mask plugin
     */
    $(document).ready(function() {
        // Handle date range selection
        $('.nuvho-date-filter select').on('change', function() {
            $(this).closest('form').submit();
        });
        
        // Export functionality (if needed in the future)
        $('.nuvho-export-btn').on('click', function(e) {
            e.preventDefault();
            
            const dateRange = $('.nuvho-date-filter select').val();
            const reportType = $(this).data('type');
            
            // Construct export URL
            let exportUrl = ajaxurl + '?action=nuvho_export_report';
            exportUrl += '&date_range=' + dateRange;
            exportUrl += '&report_type=' + reportType;
            exportUrl += '&nonce=' + nuvhoReports.nonce;
            
            // Redirect to export URL
            window.location.href = exportUrl;
        });
    });

})(jQuery);