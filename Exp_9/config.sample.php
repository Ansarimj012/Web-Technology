<?php
/**
 * config.php  ←  COPY THIS FILE and rename to config.php
 *
 * ⚠  Never commit config.php to version control.
 *    Add it to .gitignore:  echo "config.php" >> .gitignore
 *
 * This file must RETURN an array.
 */
return [
    'host'   => '127.0.0.1',   // MySQL host
    'port'   => '3306',         // MySQL port
    'dbname' => 'online_store', // Database name (run schema.sql first)
    'charset'=> 'utf8mb4',
    'user'   => 'root',         // ← change to your MySQL username
    'pass'   => '',             // ← change to your MySQL password
];
