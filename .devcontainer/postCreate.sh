#!/bin/bash

DATABASE=database/database.sqlite

chmod a+x /root && sudo rm -rf /var/www/html && sudo ln -s "$(pwd)/public" /var/www/html
cp .env.example .env && echo "APP_TRUSTED_PROXIES=*" >> .env
sed -i "s%DB_CONNECTION=.*%DB_CONNECTION=sqlite%" .env
sed -i "s%DB_DATABASE=.*%DB_DATABASE=$(pwd)/$DATABASE%" .env
touch $DATABASE && chgrp www-data database $DATABASE && chmod g+w database $DATABASE
chgrp -R www-data storage && chmod -R g+w storage

composer install
php artisan key:generate --no-interaction
php artisan monica:setup -vvv
yarn install
yarn run build

a2enmod rewrite
apache2ctl restart
