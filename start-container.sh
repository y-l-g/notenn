#!/bin/sh
set -e

mv .env.production .env

for secret_file in /run/secrets/*; do
    name=$(basename "$secret_file" | sed "s/^thetune_//")
    value=$(cat "$secret_file")
    if grep -q "^${name}=" /app/.env; then
        sed -i "s|^${name}=.*|${name}=${value}|" /app/.env
    else
        echo "${name}=${value}" >>/app/.env
    fi
done

php /app/artisan storage:link
php /app/artisan optimize

exec "$@"
