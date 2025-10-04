# 🎬 Cinema Website Setup Guide

## Create your own `constants.php`

Each contributor must use their own local credentials.

Create a new file inside `/include/constants.php`:

```php
<?php
$db_name   = "mysql:dbname=cinema";
$db_host   = "host=localhost";
$db_charset = "charset=utf8";

define("DSN", "$db_name;$db_host;$db_charset");
define("DB_USER", "root");    // your MySQL username
define("DB_PASS", "");        // your MySQL password (blank by default)


Initialize or reset the database

Visit the following URL in your browser to run the SQL code:

http://localhost/cinema-website/setup_database.php


You should see:
"Database setup completed successfully."

There will be admin user created with password 123456 and login admin@admin.com