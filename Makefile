lint: cs-fixer stan twig-fixer

cs-fixer:
	./vendor/bin/php-cs-fixer fix

stan:
	./vendor/bin/phpstan analyse

twig-fixer:
	./vendor/bin/twig-cs-fixer lint --fix

test:
	./vendor/bin/phpunit

doc:
	php bin/console nelmio:apidoc:dump --format=html

setup:
	touch ./.git/hooks/pre-commit
	chmod +x ./.git/hooks/pre-commit
	ln -sf ./pre-commit ./.git/hooks/pre-commit
