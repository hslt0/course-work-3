<?php

use App\Core\DotEnv;

require_once dirname(__FILE__, 2) . '/Core/DotEnv.php';
DotEnv::load(dirname(__FILE__, 3) . '/.env');

define('DB_HOST', $_ENV['DB_HOST'] ?? '127.0.0.1');
define('DB_USER', $_ENV['DB_USER'] ?? 'root');
define('DB_PASS', $_ENV['DB_PASS'] ?? '');
define('DB_NAME', $_ENV['DB_NAME'] ?? 'coursework3');

define('APPROOT', dirname(__FILE__, 2));

define('URLROOT', $_ENV['URLROOT'] ?? 'http://course-work');

define('SITENAME', $_ENV['SITENAME'] ?? 'LanguageCourses');