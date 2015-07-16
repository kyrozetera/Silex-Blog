# Silex-Blog

A simple one page blog created as a code sample using the Silex framework.

To set up, install the dependencies using composer:

```bash
$ composer install
# or
$ php composer.phar install
```

Then set up the database schema:

```bash
$ mysql -u user -e "CREATE DATABASE blog"
$ mysql -u user blog < blog_dump.sql
```

Then copy the `config.yml.dist` file to `config.yml` and fill in the database credentials.
