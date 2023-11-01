drop-db:
	@echo "Suppression des tables, séquences et de la base de données..."
	docker-compose exec db psql -U user -d shop -c "DROP SCHEMA public CASCADE; CREATE SCHEMA public;"
	docker-compose exec api php bin/console doctrine:database:drop --force
	@echo "Base de données supprimée."



init-db:
	docker-compose exec api php bin/console doctrine:database:create
	docker-compose exec api php bin/console make:migration
	docker-compose exec api php bin/console doctrine:migration:migrate
	docker-compose exec api php bin/console doctrine:fixtures:load

