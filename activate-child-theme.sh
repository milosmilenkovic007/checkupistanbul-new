#!/bin/bash

# Aktivacija Hello Elementor Child teme
# Ovaj script automatski aktivira child temu kroz MySQL

MYSQL_CMD="/Applications/Local.app/Contents/Resources/extraResources/lightning-services/mysql-8.0.35+4/bin/darwin/bin/mysql"
SOCKET="/Users/user/Library/Application Support/Local/run/skejlzMMw/mysql/mysqld.sock"
DB_USER="root"
DB_PASS="root"
DB_NAME="local"

echo "🚀 Aktivacija Hello Elementor Child teme..."

# Aktivacija child teme
$MYSQL_CMD -u $DB_USER -p$DB_PASS --socket="$SOCKET" -D $DB_NAME <<EOF
UPDATE wp_options SET option_value = 'hello-elementor-child' WHERE option_name = 'stylesheet';
UPDATE wp_options SET option_value = 'hello-elementor' WHERE option_name = 'template';
EOF

if [ $? -eq 0 ]; then
    echo "✅ Child tema uspešno aktivirana!"
    echo ""
    echo "Sledeći koraci:"
    echo "1. Poseti medicorner.local da proveriš sajt"
    echo "2. Idi na Pages > Our Packages"
    echo "3. Omogući 'Use ACF Template' switch"
    echo "4. Popuni ACF polja sa sadržajem"
else
    echo "❌ Greška pri aktivaciji teme"
    exit 1
fi
