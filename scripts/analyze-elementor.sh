#!/bin/bash

# Ekstraktovanje Elementor module JSON i analiza
MYSQL_CMD="/Applications/Local.app/Contents/Resources/extraResources/lightning-services/mysql-8.0.35+4/bin/darwin/bin/mysql"
SOCKET="/Users/user/Library/Application Support/Local/run/skejlzMMw/mysql/mysqld.sock"
DB_USER="root"
DB_PASS="root"
DB_NAME="local"

OUTPUT_FILE="elementor-analysis.txt"

echo "🔍 Analiza Elementor modula i CSS-a..." > "$OUTPUT_FILE"
echo "" >> "$OUTPUT_FILE"

# Izvuci sve stranice sa Elementor meta podacima
$MYSQL_CMD -u $DB_USER -p$DB_PASS --socket="$SOCKET" -D $DB_NAME -e "
SELECT 
    p.ID as page_id,
    p.post_title as page_title,
    p.post_name as page_slug,
    LENGTH(pm.meta_value) as data_size
FROM wp_posts p
INNER JOIN wp_postmeta pm ON p.ID = pm.post_id
WHERE pm.meta_key = '_elementor_data'
AND p.post_type = 'page'
AND p.post_status = 'publish'
ORDER BY p.post_title;
" 2>&1 | grep -v Warning >> "$OUTPUT_FILE"

echo "" >> "$OUTPUT_FILE"
echo "✅ Analiza dostupna u: $OUTPUT_FILE" >> "$OUTPUT_FILE"

cat "$OUTPUT_FILE"
