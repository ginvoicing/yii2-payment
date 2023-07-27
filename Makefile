up: clean
	docker-compose up -d
dbssh:
	docker-compose exec PaymentDb /bin/bash
db:
	docker-compose exec PaymentDb /usr/bin/mysql -u paymentuser -ppassword -h 127.0.0.1 paymentdb
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
