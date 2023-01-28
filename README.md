## Laravel Forge Monitor
Monitors global disk usage, site disk usage and database disk usage on servers managed by Laravel Forge. Might also be compatible with other servers.

> I always wondered how much disk space each site on the server used, and so I created the monitor to keep an eye on it and notify me so that I can keep an eye on it.

By default, Laravel Forge creates servers with a 20GB drive, but there is no way to know how much of this space is actually being used (by the operating system, services and applications, the actual sites/projects hosted on the website, the databases of these sites and anything else). 

Laravel forge is a simple command in Laravel thus can be run on a schedule, or any time you need to know the status of your server. I have it set to run once every day.

Laravel Forge Monitor output to command line and optionally sends an email with the result every time it runs, and you control the recipient of this email. 



### Installation

You can install the package via composer:
```bash
composer require jacotheron/forge-monitor
```

You can publish the config and views file:
```bash
php artisan vendor:publish --tag="forge-monitor-config"
php artisan vendor:publish --tag="forge-monitor-views"
```

### Usage

To run the monitor, simply run the following command (on demand):
```bash
php artisan monitor-forge-sites
```

To run the monitor on a schedule, add the following to your `app/Console/Kernel.php` file, in the 'schedule' method (and change the frequency options):
```php
$schedule->command('monitor-forge-sites')->daily();
```
The above schedule option requires that your site is set up with scheduling (cron jobs).

### How it works:

Laravel Forge Monitor makes use of the Symfony Process component to run a couple of commands directly. 

There is no privilege escalation (sudo) in these commands, it simply runs them as the current user.

#### Global Disk Usage
Command that is run:
```bash
df -h /
```
This command generates a very simple table that indicates the size of the disk, the total space used, the amount of available space and percentage used. 

The idea of this monitor is to determine when it may be time to either expand the disk size, or create an extra server.

#### Site Disk Usage
When set to monitor all sites (inside the configurable sites root, which is default `/home/forge`), will start with an entry for the sites root, followed by all its direct subdirectories (in alphabetical order).

When in single site mode, will only run once for the current site.

Command that is run (looped for all sites):
```bash
du -hs {location}
```

#### Database Usage
Database Usage is based on MySQL/MariaDB databases, and requires a new Database Connection (to the `information_schema` database).

In `config\database.php` copy the "mysql" connection, and only update the `database` option inside the new one to `information_schema`.
For example:
```php
'information_schema' => [
    'driver' => 'mysql',
    'url' => env('DATABASE_URL'),
    'host' => env('DB_HOST', '127.0.0.1'),
    'port' => env('DB_PORT', '3306'),
    'database' => 'information_schema',
    'username' => env('DB_USERNAME', 'forge'),
    'password' => env('DB_PASSWORD', ''),
    'unix_socket' => env('DB_SOCKET', ''),
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'prefix' => '',
    'prefix_indexes' => true,
    'strict' => true,
    'engine' => null,
    'options' => extension_loaded('pdo_mysql') ? array_filter([
        PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
    ]) : [],
],
```

Database query being run:
```php
DB::connection('information_schema')
    ->table('TABLES')
    ->select(DB::raw('TABLE_SCHEMA as `database`, SUM(DATA_LENGTH + INDEX_LENGTH) as `size`'))
    ->groupBy('TABLE_SCHEMA')
    ->orderBy('size', 'desc')
    ->get();
```