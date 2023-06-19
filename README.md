# Anomaly detection core
There is an anomaly detection core


# Installation for developer
- edit .env and set credential 
- composer install
- (When installed via git clone or download, run `php artisan key:generate` once)
- (you need docker installed to use sail)
- run `sail up` (`sail` like alias for `./vendor/bin/sail`)
- run `sail artisan migrate`

# Installation for prod
- run migrations `php artisan migrate`
- add loading logs command into cron `php artisan app:get-client-logs`
- add checking logs command into cron `php artisan app:check-client-logs`
