#!/bin/bash

php artisan serve &
npm run dev &
php artisan reverb:start &
php artisan queue:listen &
