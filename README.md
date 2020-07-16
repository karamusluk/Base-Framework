# Backbone Framework

This mini framework is to support my PHP projects as a backbone.

## Installation

There is no explicit installation process for this project.

You can always chance models and migrate to the DB with your credentials with the help of Eloquent ORM.

You will need to install [Composer](http://getcomposer.org/) following the instructions on their site.

Then, simply run the following command to install dependencies:


```bash
composer install
```

## Initial Setup

Open 
```php
src/models/Database.php
```
and edit database credentials with yours.

```php
$capsule->addConnection([
    'driver'    => 'mysql',
    'host'      => 'HOSTNAME',
    'database'  => 'DATABASE_NAME',
    'username'  => 'USERNAME',
    'password'  => 'PASSWORD',
    'charset'   => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix'    => '',
]);
```

After adding new Models or Controllers do not forget to run following command to generate autoload files.
```bash
composer dump-autoload -o
```


## Contributing
Pull requests are welcome. For major changes, please open an issue first to discuss what you would like to change.

## License
[GNU GPLv3](https://choosealicense.com/licenses/gpl-3.0/)