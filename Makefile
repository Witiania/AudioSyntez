lint: stan cs-fixer

stan:
	./vendor/bin/phpstan analyse

cs-fixer:
	./vendor/bin/php-cs-fixer fix
