up: clean
	docker-compose up -d
dbssh:
	docker-compose exec SwiftCurrencyDb /bin/bash
db:
	docker-compose exec SwiftCurrencyDb /usr/bin/mysql -u currencyuser -ppassword -h 127.0.0.1 currencydb
down:
	docker-compose stop
clean:
	docker system prune --force
rm:
	docker-compose stop; docker-compose rm -f
list:
	docker-compose ps
reload: down up
logs:
	docker-compose logs -f
