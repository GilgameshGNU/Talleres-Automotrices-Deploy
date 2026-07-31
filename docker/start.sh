#!/bin/bash
set -e

# Railway inyecta PORT dinámicamente; Apache por defecto escucha en 80
PORT="${PORT:-8080}"
sed -i "s/80/${PORT}/g" /etc/apache2/ports.conf
sed -i "s/:80/:${PORT}/g" /etc/apache2/sites-available/000-default.conf

php artisan migrate --force

apache2-foreground
