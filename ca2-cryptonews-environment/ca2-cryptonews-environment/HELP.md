# Summary

This is a base template to develop the assignment. It contains the following:

* `docker-compose.yaml` file with a
  * php-fpm image
  * mysql-image
  * nginx image
  * phpmyadmin image
* `docker-compose/mysql/schema.sql` so when you run `docker compose up` a database is created following such
  schema
* `.env` file
* `www` directory with the PHP code to start your application, including
  * `composer.json` file
  * `templates` folder
  * `src` folder
  * `config` folder
  * `public` folder
* `cypress` folder for testing

## How to run tests

We are going to use `cypress` to execute end-to-end tests.

You may have noticed that the `docker-compose.yaml` file does not specify any cypress service. That is because you are
going to use a separate container to run `cypress`. Every time you want to run a test suite / spec (you will find them
inside `cypress/e2e`) you will need to execute a command such as:

```bash
# inside the project root directory, assuming that the environment is running:

docker run --rm --env CYPRESS_baseUrl=http://nginx:80 -v ./www/cypress:/cypress --env-file .env --network "ca2-cryptonews-environment_pw2_network" -it -w /cypress vcaballerosalle/cypress-mysql:3.0 --browser electron --spec "e2e/sign-up.cy.js"
```

Notice that we're specifying the spec at the end of the command. In this case, `e2e/sign-up.cy.js` is the 
chosen test suite, but you can remove the `--spec` argument to run all specs found inside the `e2e` folder.

## Important notice

Know that you will face data loss if you run the previous command and you signal to cancel its execution (Ctrl+C) before all tests are done.
You can find a backup of the database's information before running the tests at `www/cypress/tmp/dump.sql`, which you can
restore by running the following command from the project root: `docker compose exec -T mysql sh -c 'mysql -uroot -p$MYSQL_ROOT_PASSWORD $MYSQL_DATABASE' < www/cypress/tmp/dump.sql`
