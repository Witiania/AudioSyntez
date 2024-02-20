lint: cs-fixer stan

cs-fixer:
	./vendor/bin/php-cs-fixer fix

stan:
	./vendor/bin/phpstan analyse

test:
	./vendor/bin/phpunit