# Multilanguage Support for Nuvho Booking Mask

This directory contains language files for the Nuvho Booking Mask plugin.

## Available Translations

- `nuvho-booking-mask.pot` - The template file containing all translatable strings
- `nuvho-booking-mask-fr_FR.po/mo` - French translation (example, not included by default)
- `nuvho-booking-mask-de_DE.po/mo` - German translation (example, not included by default)
- `nuvho-booking-mask-es_ES.po/mo` - Spanish translation (example, not included by default)
- `nuvho-booking-mask-it_IT.po/mo` - Italian translation (example, not included by default)

## How to Add a New Translation

1. Copy the `nuvho-booking-mask.pot` file to `nuvho-booking-mask-{locale}.po` where `{locale}` is the WordPress locale code (e.g., `fr_FR` for French, `de_DE` for German).

2. Use a translation tool like Poedit (https://poedit.net/) to translate the strings in your `.po` file.

3. Save your translations. Poedit will automatically generate the `.mo` file.

4. Place both `.po` and `.mo` files in this directory.

## How to Update Translations

When new versions of the plugin are released with new strings:

1. Update the POT file using WordPress' internationalization tools:
   ```
   wp i18n make-pot /path/to/plugin languages/nuvho-booking-mask.pot
   ```

2. Update your PO files by opening them in Poedit and using the "Update from POT file" option.

3. Translate the new strings and save.

## Testing Translations

To test a translation, set your WordPress installation to use that language and check if all strings are correctly translated in both admin and public areas of the plugin.