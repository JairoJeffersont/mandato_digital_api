<?php

/**
 * Application Configuration File
 * 
 * This file contains all the configuration settings for the application,
 * including database connection parameters and application-specific settings.
 * 
 * Configuration Structure:
 * - db: Database connection settings
 *   - host: Database server hostname
 *   - database: Database name
 *   - username: Database user
 *   - password: Database password
 *   - charset: Database character set
 * 
 * - app: Application settings
 *   - development: Boolean flag for development mode
 * 
 * @package App\Config
 * @version 1.0.0
 */

return [
    'db' => [
        'host' => 'localhost',
        'database' => 'api',
        'username' => 'root',
        'password' => 'root',
        'charset' => 'utf8mb4'
    ],
    'app' => [
        'development' => true,
        'jwt' => [
            'secret' => 'd76abcc5-6b70-43a8-8b22-b523ebcfaf8f',
            'expiration' => 36000
        ]
    ]
]; 