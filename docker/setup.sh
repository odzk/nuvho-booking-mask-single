#!/bin/sh
# WP-CLI auto-setup script
# Runs once inside the wpcli container on first launch

set -e

WP_URL="http://localhost:8080"
WP_TITLE="Nuvho Staging"
WP_ADMIN_USER="nuvho"
WP_ADMIN_PASS="Hoteliers.200677!"
WP_ADMIN_EMAIL="admin@nuvho.test"
PLUGIN_SLUG="nuvho-booking-mask-single"

# ---- Wait for WordPress container to write wp-config.php ----
echo "[setup] Waiting for wp-config.php..."
WAIT=0
until [ -f /var/www/html/wp-config.php ]; do
  sleep 2
  WAIT=$((WAIT+2))
  if [ $WAIT -ge 120 ]; then
    echo "[setup] ERROR: wp-config.php not found after 120s. Is the wordpress container running?"
    exit 1
  fi
done

# ---- Wait for DB to accept connections ----
echo "[setup] Waiting for database..."
WAIT=0
until wp db check --quiet 2>/dev/null; do
  sleep 3
  WAIT=$((WAIT+3))
  if [ $WAIT -ge 120 ]; then
    echo "[setup] ERROR: Database not reachable after 120s."
    exit 1
  fi
done

# ---- Skip if already installed ----
if wp core is-installed 2>/dev/null; then
  echo "[setup] WordPress already installed — skipping."
  exit 0
fi

# ---- Install WordPress core ----
echo "[setup] Installing WordPress..."
wp core install \
  --url="$WP_URL" \
  --title="$WP_TITLE" \
  --admin_user="$WP_ADMIN_USER" \
  --admin_password="$WP_ADMIN_PASS" \
  --admin_email="$WP_ADMIN_EMAIL" \
  --skip-email

# ---- Clone plugin from GitHub ----
PLUGIN_DIR="/var/www/html/wp-content/plugins/$PLUGIN_SLUG"
if [ ! -d "$PLUGIN_DIR/.git" ]; then
  echo "[setup] Installing git..."
  apk add --no-cache git --quiet
  echo "[setup] Cloning plugin from GitHub..."
  git clone https://github.com/odzk/nuvho-booking-mask-single.git "$PLUGIN_DIR"
else
  echo "[setup] Plugin already cloned, pulling latest..."
  git -C "$PLUGIN_DIR" pull
fi

# ---- Activate the plugin ----
echo "[setup] Activating plugin: $PLUGIN_SLUG"
wp plugin activate "$PLUGIN_SLUG"

# ---- Create a test page with the booking shortcode ----
echo "[setup] Creating test page..."
wp post create \
  --post_type=page \
  --post_title="Booking Widget Test" \
  --post_content='[nuvho_booking_mask_single]' \
  --post_status=publish \
  --porcelain

# ---- Disable comments on the test page (cleaner UI) ----
TEST_PAGE_ID=$(wp post list --post_type=page --post_status=publish --name="booking-widget-test" --field=ID --quiet 2>/dev/null || echo "")
if [ -n "$TEST_PAGE_ID" ]; then
  wp post update "$TEST_PAGE_ID" --comment_status=closed --ping_status=closed --quiet
fi

# ---- Set permalink structure ----
wp rewrite structure '/%postname%/' --hard --quiet

echo ""
echo "============================================"
echo "  Setup complete!"
echo "--------------------------------------------"
echo "  Site:      $WP_URL"
echo "  Admin:     $WP_URL/wp-admin"
echo "  User:      $WP_ADMIN_USER"
echo "  Password:  $WP_ADMIN_PASS"
echo "--------------------------------------------"
echo "  Test page: $WP_URL/booking-widget-test"
echo "============================================"
