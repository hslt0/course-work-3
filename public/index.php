<?php

ob_start();

require_once '../app/init.php';

use App\Core\App;

$app = new App();

ob_end_flush();