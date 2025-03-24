<?php
/**
 * Provide a admin area view for the plugin reports
 *
 * This file is used to markup the admin-facing reports of the plugin.
 *
 * @link       https://herobe.com
 * @since      2.0.0
 *
 * @package    Nuvho_Booking_Mask
 * @subpackage Nuvho_Booking_Mask/admin/partials
 */

// Get report data from database
global $wpdb;
$table_name = $wpdb->prefix . 'nuvho_booking_mask_reports';

// Get date range
$date_range = isset($_GET['date_range']) ? sanitize_text_field($_GET['date_range']) : '7days';

switch ($date_range) {
    case '30days':
        $days = 30;
        break;
    case '90days':
        $days = 90;
        break;
    case '1year':
        $days = 365;
        break;
    case '7days':
    default:
        $days = 7;
        break;
}

// Get report data
$report_data = $wpdb->get_results(
    $wpdb->prepare(
        "SELECT DATE(date) as report_date, 
                SUM(clicks) as total_clicks, 
                SUM(conversions) as total_conversions,
                ROUND(AVG(conversion_rate), 2) as avg_conversion_rate
         FROM $table_name 
         WHERE date >= DATE_SUB(NOW(), INTERVAL %d DAY) 
         GROUP BY DATE(date) 
         ORDER BY DATE(date) ASC",
        $days
    )
);

// Get booking engine breakdown
$engine_data = $wpdb->get_results(
    $wpdb->prepare(
        "SELECT booking_engine, 
                SUM(clicks) as total_clicks, 
                SUM(conversions) as total_conversions,
                ROUND(AVG(conversion_rate), 2) as avg_conversion_rate
         FROM $table_name 
         WHERE date >= DATE_SUB(NOW(), INTERVAL %d DAY) 
         GROUP BY booking_engine 
         ORDER BY SUM(clicks) DESC",
        $days
    )
);

// Calculate totals
$total_clicks = 0;
$total_conversions = 0;
$dates = [];
$clicks_data = [];
$conversion_data = [];
$rate_data = [];

foreach ($report_data as $data) {
    $total_clicks += $data->total_clicks;
    $total_conversions += $data->total_conversions;
    
    $dates[] = date('M d', strtotime($data->report_date));
    $clicks_data[] = $data->total_clicks;
    $conversion_data[] = $data->total_conversions;
    $rate_data[] = $data->avg_conversion_rate;
}

$overall_conversion_rate = $total_clicks > 0 ? round(($total_conversions / $total_clicks) * 100, 2) : 0;

// Prepare chart data
$chart_data = [
    'labels' => $dates,
    'clicks' => $clicks_data,
    'conversions' => $conversion_data,
    'rates' => $rate_data
];

?>
<div class="wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
    
    <div class="nuvho-reports-header">
        <div class="nuvho-date-filter">
            <form method="get">
                <input type="hidden" name="page" value="nuvho-booking-mask-reports">
                <select name="date_range" onchange="this.form.submit()">
                    <option value="7days" <?php selected($date_range, '7days'); ?>>Last 7 Days</option>
                    <option value="30days" <?php selected($date_range, '30days'); ?>>Last 30 Days</option>
                    <option value="90days" <?php selected($date_range, '90days'); ?>>Last 90 Days</option>
                    <option value="1year" <?php selected($date_range, '1year'); ?>>Last Year</option>
                </select>
            </form>
        </div>
    </div>
    
    <div class="nuvho-metrics-container">
        <div class="nuvho-metric-card">
            <h3>Total Clicks</h3>
            <div class="nuvho-metric-value"><?php echo number_format($total_clicks); ?></div>
        </div>
        
        <div class="nuvho-metric-card">
            <h3>Total Conversions</h3>
            <div class="nuvho-metric-value"><?php echo number_format($total_conversions); ?></div>
        </div>
        
        <div class="nuvho-metric-card">
            <h3>Conversion Rate</h3>
            <div class="nuvho-metric-value"><?php echo $overall_conversion_rate; ?>%</div>
        </div>
    </div>
    
    <div class="nuvho-charts-container">
        <div class="nuvho-chart-card">
            <h2>Performance Overview</h2>
            <div class="nuvho-chart-container">
                <canvas id="nuvho-performance-chart"></canvas>
            </div>
        </div>
        
        <div class="nuvho-chart-card">
            <h2>Conversion Rate Trend</h2>
            <div class="nuvho-chart-container">
                <canvas id="nuvho-conversion-chart"></canvas>
            </div>
        </div>
    </div>
    
    <div class="nuvho-table-card">
        <h2>Booking Engine Performance</h2>
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th>Booking Engine</th>
                    <th>Clicks</th>
                    <th>Conversions</th>
                    <th>Conversion Rate</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($engine_data)) : ?>
                    <tr>
                        <td colspan="4">No data available for the selected period.</td>
                    </tr>
                <?php else : ?>
                    <?php foreach ($engine_data as $engine) : ?>
                        <tr>
                            <td><?php echo esc_html($engine->booking_engine); ?></td>
                            <td><?php echo number_format($engine->total_clicks); ?></td>
                            <td><?php echo number_format($engine->total_conversions); ?></td>
                            <td><?php echo $engine->avg_conversion_rate; ?>%</td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script type="text/javascript">
    document.addEventListener('DOMContentLoaded', function() {
        // Chart data
        var chartData = <?php echo json_encode($chart_data); ?>;
        
        // Performance Chart
        var performanceCtx = document.getElementById('nuvho-performance-chart').getContext('2d');
        var performanceChart = new Chart(performanceCtx, {
            type: 'bar',
            data: {
                labels: chartData.labels,
                datasets: [
                    {
                        label: 'Clicks',
                        data: chartData.clicks,
                        backgroundColor: 'rgba(54, 162, 235, 0.5)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'Conversions',
                        data: chartData.conversions,
                        backgroundColor: 'rgba(75, 192, 192, 0.5)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1
                    }
                ]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
        
        // Conversion Rate Chart
        var conversionCtx = document.getElementById('nuvho-conversion-chart').getContext('2d');
        var conversionChart = new Chart(conversionCtx, {
            type: 'line',
            data: {
                labels: chartData.labels,
                datasets: [
                    {
                        label: 'Conversion Rate (%)',
                        data: chartData.rates,
                        backgroundColor: 'rgba(255, 99, 132, 0.2)',
                        borderColor: 'rgba(255, 99, 132, 1)',
                        borderWidth: 2,
                        fill: false,
                        tension: 0.4
                    }
                ]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Conversion Rate (%)'
                        }
                    }
                }
            }
        });
    });
</script>