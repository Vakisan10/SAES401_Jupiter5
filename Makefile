r:
	cd Backend/src && php -S localhost:8000
i:
	cd Backend/src && composer install
linux:
	sudo apt install php8.3-cli && php -v && sudo apt install composer && sudo apt install php8.3-xml && sudo apt install php8.3-mysql
