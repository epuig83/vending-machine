# Vending Machine

The goal of this program is to model a vending machine and the state it must maintain during its operation.

The machine works like all vending machines: it takes money then gives you items. The vending machine accepts money in the form of 0.05, 0.10, 0.25 and 1

You must have at least have 3 primary items that cost 0.65, 1.00, and 1.50. Also user may hit the button “return coin” to get back the money they’ve entered so far, If you put more money in than the item price, you get the item and change back.

## Specification

### Valid set of actions on the vending machine are:

* 0.05, 0.10, 0.25, 1 - insert money
* Return Coin - returns all inserted money
* GET Water, GET Juice, GET Soda - select item (Water = 0.65, Juice = 1.00, Soda = 1.50)
* SERVICE - a service person opens the machine and set the available change and how many items we have.

### Valid set of responses on the vending machine are:

* 0.05, 0.10, 0.25 - return coin
* Water,  Juice, Soda - vend item

### Vending machine must track the following state:

* Available items - each item has a count, a price and selector
* Available change - Number os coins available
* Currently inserted money

## Examples
```
Example 1: Buy Soda with exact change
1, 0.25, 0.25, GET-SODA
-> SODA
Example 2: Start adding money, but user ask for return coin
0.10, 0.10, RETURN-COIN
-> 0.10, 0.10
Example 3: Buy Water without exact change
1, GET-WATER
-> WATER, 0.25, 0.10
```

Prerequisites
===================

- PHP 7.4
- Symfony 5.3.*
- Docker + Docker Compose

Install
===================

### Clone

```sh
$ git clone https://github.com/epuig83/vending-machine.git
$ cd vending-machine
```

### Build up all containers

```sh
$ docker-compose build
$ docker-compose up -d
```

### Access to bash container

```sh
$ docker exec -it [container-name] bash
```

### Composer dependencies

```sh
$ docker-compose run --rm webserver php composer.phar install --no-interaction
```

### Run Doctrine migrations

```sh
$ docker-compose run --rm webserver php bin/console doctrine:migrations:migrate
```

### Load default coins and items (Doctrine fixtures)

```sh
$ docker-compose run --rm webserver php bin/console doctrine:fixtures:load --no-interaction
```

### Create new test database

```sh
$ docker-compose run --rm webserver php bin/console doctrine:database:create --env=test --no-interaction
```

### Run Doctrine migrations for test database

```sh
$ docker-compose run --rm webserver php bin/console doctrine:migrations:migrate --env=test --no-interaction
```

### Load default coins and items for test database (Doctrine fixtures)

```sh
$ docker-compose run --rm webserver php bin/console doctrine:fixtures:load --no-interaction --env=test
```


Testing
===================
Run tests

```sh
./vendor/bin/phpunit
```

API Docs
===================

HTML version

```console
http://localhost:8009/api/doc
```

JSON version

```console
http://localhost:8009/api/doc.json
```