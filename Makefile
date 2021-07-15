make.PHONY:
start:
	docker-compose up -d

.PHONY:
stop:
	docker-compose stop

.PHONY:
init:
	docker-compose build
	docker-compose up -d

.PHONY:
rebuild:
	docker-compose up --build --force-recreate -d

.PHONY:
nginx-shell:
	docker exec -it reports-nginx /bin/bash

.PHONY:
php-shell:
	docker exec -it reports-php /bin/bash

.PHONY:
db-shell:
	docker exec -it reports-db /bin/bash
