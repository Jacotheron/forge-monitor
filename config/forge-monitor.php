<?php

// config for Jacotheron/ForgeMonitor
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
        'all_projects' => 'All Projects',
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
