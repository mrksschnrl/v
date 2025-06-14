#!/bin/bash
cd /var/www/html/V || exit 1

# Eigentümer auf Webserver setzen (anpassen falls nötig)
chown -R www-data:www-data .

# Ordner: 755 (rwxr-xr-x)
find . -type d -exec chmod 755 {} \;

# Dateien: 644 (rw-r--r--)
find . -type f -exec chmod 644 {} \;

echo "✅ Rechte & Eigentümer korrekt gesetzt."
