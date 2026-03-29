linter:
	@echo "делаем линтер (найдёт всё и поправит)"
	@sleep 3
	 ./vendor/bin/pint

linter-show:
	@echo "смотрим что линтер видит (проверяет показывает косяки, без правок)"
	@sleep 3
	 ./vendor/bin/pint -v

swagger-generate:
	@echo "генерируем swagger документацию в контейнере site_api"
	@sleep 1
	php artisan l5-swagger:generate


