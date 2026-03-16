# A simple package to monitor storage used by sites on a Forge provisioned server. Also monitors database sizes. Sends email with the details after each run.

[![Latest Version on Packagist](https://img.shields.io/packagist/v/jacotheron/forge-monitor.svg?style=flat-square)](https://packagist.org/packages/jacotheron/forge-monitor)
[![Total Downloads](https://img.shields.io/packagist/dt/jacotheron/forge-monitor.svg?style=flat-square)](https://packagist.org/packages/jacotheron/forge-monitor)

Easily get notifications of the storage used by sites on a Forge provisioned server including database sizes. Sends email with the details after each run.

## Installation

You can install the package via composer:

```bash
composer require jacotheron/forge-monitor
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="forge-monitor-config"
```

This is the contents of the published config file:

```php
return [

    /**
     * Commands to run to get disk usage (File system, Disk Size, Used, Available, Use%, Mounted on)
     * This is provided directly to the shell using Symfony/Process component
     * Example: ['df', '-h', '/'] results in "df -h /"
     * Your current user needs to be able to run this command successfully, otherwise to skip this, set to null
     * accepts: array|null
     */
    'disc_commands' => [
        'df',
        '-h',
        '/'
    ],

    /**
     * Disk Command Timeout
     * The command may take a while to complete, depending on the number of files it needs to scan, if you get timeouts, increase this value
     * accepts: int
     */
    'disk_command_timeout' => 1200,

    /**
     * Directory containing all sites/projects to monitor
     * Set to null to skip
     * accepts: string|null
     */
    'projects_location' => '/home/forge',

    /**
     * Commands to run on each directory inside the 'projects_location'
     * This is also provided directly into the shell using Symfony/Process component
     * Example: ['du', '-hs'] runs "du -hs /home/forge/{dir}"
     * Your current user needs to be able to run this command successfully, otherwise to skip this, set to null
     * Directory is automatically added to the command
     * accepts: array|null
     */
    'project_commands' => [
        'du',
        '-hs',
    ],

    /**
     * Project Command Timeout
     * The command may take a while to complete, depending on the number of files it needs to scan, if you get timeouts, increase this value
     * accepts: int
     */
    'project_command_timeout' => 1200,

    /**
     * Also run 'project_commands' on the 'projects_location' directory (results in a "All Projects" entry)
     * accepts: bool
     */
    'project_commands_on_projects_location' => true,

    /**
     * Only run 'project_commands' on the 'projects_location' directory (results in a "All Projects" entry)
     * accepts: bool
     */
    'project_commands_only_on_projects_location' => false,

    /**
     * Enable DB Monitoring (MySQL / MariaDB)
     * Note: Your db user needs to be able to query the 'information_schema' root database.
     * A quick way to handle this, is to duplicate the 'mysql' database driver in the config/database.php file, and setup with a dedicated user,
     * and the database option set to 'information_schema';
     *
     * Value here should be the name of the database driver to use
     *
     * accepts: string|null
     */
    'db_driver' => null,

    /**
     * String outputs for the email/command outputs
     * Use this if you need to handle translations
     * accepts: array
     */
    'strings' => [
        'disk_results' => 'Disk Results:',
        'all_projects' => '(All Projects)',
        'scan_results' => 'Scan Results:',
        'db_results' => 'DB Results:',
        'total' => 'Total',
        'database' => 'Database',
        'size' => 'Size',
        'done' => 'Done',
        'email' => [
            'disk_details' => 'Disk Details:',
            'disk_usage' => 'Disk Usage Results:',
            'db_usage' => 'Database Usage Results:',
            'db' => 'Database',
            'size' => 'Size',
        ],
    ],

    /**
     * Email Recipient
     * Set to_address to null to disable email sending
     * Subject have {date} placeholder
     * accepts array|null
     */
    'email' => [
        'to_address' => null,
        'subject' => 'Forge Monitor Report - {date}',
        'subject_date_format' => 'Y-m-d H:i:s',
    ],

];
```

Optionally, you can publish the views using

```bash
php artisan vendor:publish --tag="forge-monitor-views"
```


In order for the command to read database sizes, we need a db connection to the 'information_schema' database on the server. This can be done by copying the mysql driver in your `config/database.php` file.
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
Note the `database` set to 'information_schema'; if you want to be more secure, you can create a new user for this purpose, and change the env keys to use the dedicated details.

You can then set the `db_driver` in the config to the name of the new driver you created.

## Usage
Run the command using:

```bash
php artisan forge-monitor
```
Schedule the command to run at regular intervals by editing your app/Console/Kernel.php file or in newer Laravel versions, routes/console.php file

```php
use Illuminate\Support\Facades\Schedule;

Schedule::command('forge-monitor')->dailyAt('00:00');
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Jacotheron](https://github.com/Jacotheron)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

## Important details

Global disk usage is default fetched using the command `df -h /` - your current user needs to be able to run this command successfully, otherwise to skip this, set to null in config.

Default project folder is `/home/forge/`.

Project disk usage is fetched using the command `du -hs {project_folder}/{dir}` - your current user needs to be able to run this command successfully, otherwise to skip this, set to null in config.

Currently only 1 project folder is supported; when using isolated sites, this package will be needed on each one you want to monitor, since they are run in different user accounts.

Database size includes both the Data and Index lengths, as stored in the information_schema database's TABLES table.
This package is currently only compatible with MySQL / MariaDB.

This package does not make any changes to the server (no files and databases), it is only a monitoring tool. While I do take security very seriously, and the commands used are quite safe to use, I am not responsible for any damage that may occur due to the use of this package. The commands that are used can be changed in the config file, and a malicious command here can cause damage to the server.