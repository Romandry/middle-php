test:
	/usr/local/opt/php@8.3/bin/php artisan test

pint:
	/usr/local/opt/php@8.3/bin/php ./vendor/bin/pint

stan:
	/usr/local/opt/php@8.3/bin/php ./vendor/bin/phpstan analyse -c phpstan.neon app tests
