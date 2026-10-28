<?php
error_reporting(E_ALL);			// Logging
ini_set('display_errors',1);
ini_set('log_errors',1);

require_once('tcpdf/tcpdf.php'); // TCPDF Library laden
require_once __DIR__ . '/Card.php';
require_once __DIR__ . '/CustomerMk.php';
require_once __DIR__ . '/CustomerTafel.php';
require_once __DIR__ . '/CustomerMkWhite.php';
require_once __DIR__ . '/CustomerTafelWhite.php';
require_once __DIR__ . '/Ausweis.php';

spl_autoload_register(function ($class) {	// Autoload
    require_once $class . '.php';
});

// ###########################################

$config = __DIR__ . '/../config.php';
Logger::debug("bootstrap: Load Config $config ...");
require_once $config;

Logger::debug("bootstrap: " . $message);

