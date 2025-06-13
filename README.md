# Symfony shop

Here is the exercice: https://github.com/MathiasGilles/eval_iim_symfony

## Installation
1. Clone the repository
````shell
$ https://github.com/AznTufu/symfony-shop.git
````
2. Go to the project directory
````shell
$ cd symfony-shop
````
3. Install composer packages
````shell
$ composer install
````
4. Duplicate the `.env` file and name it `.env.local`
5. Update the `DATABASE_URL` variable in the `.env.local` file with your database credentials. It should look like this : <br>
```php
DATABASE_URL="mysql://db_user:db_password@127.0.0.1:3306/db_name?serverVersion=5.7"
```
6. Create the database
````shell
$ php bin/console doctrine:database:create
````
7. Run the migrations
````shell
$ php bin/console doctrine:migrations:migrate
````
8. Load the fixtures
```shell
$ php bin/console doctrine:fixtures:load
```
9. Run the server
````shell 
$ symfony serve
````
10. Exec messenger async
```
php bin/console messenger:consume async -vv
```
11. Go to http://127.0.0.1:8000

12. Enjoy!

## Accéder a l'admin : http://127.0.0.1:8000/admin <br>

  Mail : admin@admin.fr <br>
  Mot de passe : admin <br>

## Accéder a l'api (admin only) : http://127.0.0.1:8000/api <br>

  Mail : admin@admin.fr <br>
  Mot de passe : admin <br>
