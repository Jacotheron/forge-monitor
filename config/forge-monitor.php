<?php

return [

    /*
     * Select the checks that should be run
     */
    'checks_to_run' => [
        /*
         * Global Disk Usage:
         * Shows the disk size, used disk space, available disk space and usage percentage
         */
        'global_disk_usage' => true,

        /*
         * Per Project Disk Usage:
         * Scans the directory and get usage for each subdirectory
         */
        'per_project_disk_usage' => true,

        /*
         * Self Disk Usage:
         * Only get this site's usage
         */
        'self_disk_usage' => false,

        /*
         * Database Disk Usage
         * Get the disk usage for each database from the 'information_schema' database (MySQL/MariaDB)
         */
        'database_disk_usage' => false,
    ],

    /*
     * Sites Locations
     * Root for the sites (each site/project is a subdirectory from this path)
     * More can be added (for isolated sites), however the user needs read access in these locations
     */
    'sites_locations' => [
        '/home/forge'
    ],

    /*
     * Database Connection
     * A database connection that can access the 'information_schema' database.
     */
    'database_connection' => env('FMONITOR_DB_CONNECTION', 'information_schema'),

    /*
     * Email settings
     * Enable the sending of the email; configure the email address to receive
     */
    'email' => [
        'enabled' => env('FMONITOR_MAIL_ENABLED', false),
        'address' => env('FMONITOR_MAIL_ADDRESS'),
        'subject' => 'Forge Monitor Sites Disk Usage Breakdown'
    ]
];