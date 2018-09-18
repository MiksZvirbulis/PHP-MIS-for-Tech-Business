<?php
ob_start();

# Errors
error_reporting(E_ALL | E_STRICT);
ini_set("display_errors", 1);

# Timezone
date_default_timezone_set("Europe/London");

# Counting load time
$time = microtime();
$time = explode(" ", $time);
$time = $time[1] + $time[0];
$start = $time;

# Configuration
$config = array(
	# General
	"title" => "Tech-House MIS", // Title
	"vat" => "20", // Current TAX percentage
	"default_page" => "main", // Default Page upon login
	"text2reach_api_key" => "", // Text2Reach API Key
	
	# Do not touch
	"url" => "http" . ((!empty($_SERVER['HTTPS'])) ? "s" : "") . "://" . $_SERVER['SERVER_NAME'], // SET AUTOMATICALLY
	"dir" => $_SERVER['DOCUMENT_ROOT'] . "/", // SET AUTOMATICALLY

	# Database authorisation
	"sql_host" => "", // SQL Host
	"sql_username" => "", // SQL Username
	"sql_password" => "", // SQL Password
	"sql_database" => "" // SQL Database
	);

# Defining the settings
foreach($config as $var => $value){
	define($var, $value);
}

require dir . "/system/classes/db.class.php";
require dir . "/system/classes/page.class.php";

# Text2Reach

require dir . "/system/classes/text2reach/api.php";
require dir . "/system/classes/text2reach/exception.php";
require dir . "/system/classes/text2reach/sms.php";
require dir . "/system/classes/text2reach/sms/bulk.php";
require dir . "/system/classes/text2reach/sms/status.php";