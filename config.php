<?php

/*
 * PHP-Chat Config
 */

// CREATE TABLE phpchat ( id INT NOT NULL AUTO_INCREMENT PRIMARY KEY, username VARCHAR(32), message VARCHAR(128), timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP);
// GRANT INSERT, UPDATE, DELETE ON phpchat.* TO 'phpchat'@'localhost';
define('DB_DSN', 'mysql:host=localhost;dbname=phpchat');
define('DB_USERNAME', 'phpchat');
define('DB_PASSWORD', 'lol');

?>

