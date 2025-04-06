<?php
// Allow cross-origin requests
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Define base path
define('BASE_PATH', realpath(dirname(__FILE__) . '/../'));

// Include database configuration
require_once 'db_config.php';