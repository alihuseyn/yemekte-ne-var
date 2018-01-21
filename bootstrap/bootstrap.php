<?php
/**
* ---------------------------------------
* Initialize 3rd Party libraries
* ---------------------------------------
*/
// Load Enviroment 
$dotenv = new Dotenv\Dotenv(dirname(__DIR__));
$dotenv->load();

// Set Default Timezone & Locale
setlocale(LC_TIME, 'tr_TR.utf8');
date_default_timezone_set('Europe/Istanbul');

// Initialize Router
$app = new \Klein\Klein();
// Require router file for routes
require(dirname(__DIR__).DIRECTORY_SEPARATOR.'route'.DIRECTORY_SEPARATOR.'routes.php');
// Dispatch Router
$app->dispatch();
