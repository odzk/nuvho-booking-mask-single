# Nuvho Booking Mask (Single) WordPress Plugin Structure

## Main Plugin File
- `nuvho-booking-mask.php` - Main plugin file with plugin metadata and initialization

## Includes Directory
- `includes/class-nuvho-booking-mask.php` - Core plugin class that defines all hooks
- `includes/class-nuvho-booking-mask-loader.php` - Loader class for registering hooks
- `includes/class-nuvho-booking-mask-i18n.php` - Internationalization functionality
- `includes/class-nuvho-booking-mask-activator.php` - Activation functions
- `includes/class-nuvho-booking-mask-deactivator.php` - Deactivation functions
- `includes/index.php` - Empty file to prevent directory listing

## Admin Directory
- `admin/class-nuvho-booking-mask-admin.php` - Admin-specific functionality
- `admin/partials/nuvho-booking-mask-admin-settings.php` - Settings page template
- `admin/partials/nuvho-booking-mask-admin-reports.php` - Reports page template
- `admin/partials/index.php` - Empty file to prevent directory listing
- `admin/js/nuvho-booking-mask-admin.js` - Admin JavaScript
- `admin/js/nuvho-booking-mask-reports.js` - Reports page JavaScript
- `admin/js/index.php` - Empty file to prevent directory listing
- `admin/css/nuvho-booking-mask-admin.css` - Admin styles
- `admin/css/index.php` - Empty file to prevent directory listing
- `admin/index.php` - Empty file to prevent directory listing

## Public Directory
- `public/class-nuvho-booking-mask-public.php` - Public-facing functionality
- `public/partials/nuvho-booking-mask-public-display.php` - Booking mask template
- `public/partials/index.php` - Empty file to prevent directory listing
- `public/js/nuvho-booking-mask-public.js` - Public JavaScript
- `public/js/index.php` - Empty file to prevent directory listing
- `public/css/nuvho-booking-mask-public.css` - Public styles
- `public/css/index.php` - Empty file to prevent directory listing
- `public/index.php` - Empty file to prevent directory listing

## Languages Directory
- `languages/nuvho-booking-mask.pot` - Translation template file
- `languages/nuvho-booking-mask-fr_FR.po` - Sample French translation
- `languages/README.md` - Instructions for translation
- `languages/index.php` - Empty file to prevent directory listing

## Key Features

### Main Plugin Structure
The plugin follows the WordPress best practices for plugin development, with a clear separation of concerns:
- The main plugin file defines the plugin and registers activation/deactivation hooks
- The includes directory contains core functionality
- The admin directory handles admin-specific functionality
- The public directory manages front-end display
- The languages directory enables internationalization

### Booking Engine Support
The plugin supports multiple booking engines:
- Simple Booking v1
- Simple Booking v2
- Accor
- Cloudbeds
- Staah
- SiteMinder
- RMS
- Protel
- MEWS
- TravelClick
- Frome

### Settings Management
The settings page allows configuration of:
- Booking engine selection
- URL and property ID
- Language and currency
- Visual customization (colors, fonts, borders)
- Engine-specific settings (e.g., Accor parameters)
- Display options (e.g., show promo code field)

### Style Customization
- Customizable background color and opacity
- Customizable button appearance
- Font selection
- Border radius options
- Live preview of changes

### Date Range Selection
- Modern date range picker using DateRangePicker library
- Multi-language support
- Responsive design
- Clear visual feedback

### Analytics & Reporting
- Tracks clicks on the booking button
- Records conversion rates
- Displays visual charts and statistics
- Allows filtering by date range

### Internationalization
- Full translation support
- Multi-language interface for both admin and public areas
- Date format adaptation based on language
- Sample translation included

### Responsiveness
- Mobile-friendly design
- Adaptive layout for various screen sizes
- Touch-friendly interface

### Integration
- Shortcode `[nuvho_booking_mask_single]` for easy embedding
- Compatible with all WordPress themes
- GDPR compliant

## Database Structure

### Options
- `nuvho_booking_mask_settings` - Plugin settings stored as a serialized array

### Custom Tables
- `{prefix}_nuvho_booking_mask_reports` - Stores analytics data with columns:
  - id
  - date
  - booking_engine
  - clicks
  - conversions
  - conversion_rate

## Implementation Details

### JavaScript Libraries
- MomentJS - For date handling
- DateRangePicker - For date range selection
- ChartJS - For analytics visualization

### External Dependencies
- CDN for DateRangePicker
- CDN for MomentJS
- CDN for ChartJS (admin only)

### WordPress Integration
- Uses WordPress Settings API
- Custom admin pages
- Custom shortcode
- WordPress database functions