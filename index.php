<?php
/**
 * Used for development with localhost
 * regardless of whatever port the server will use
 * example: localhost/website_folder
 * 			localhost_base: website_folder/
 * 
 * NOTE: remember to delete the content of this constant before
 * deploying to production!!!
 */
define("localhost_base", "mywebsite/");
define("is_localhost", (defined("localhost_base") && !empty(localhost_base)));

# Functions and objects
require_once "sys/session.php";
require_once "sys/router.php";
require_once "sys/utils.php";
require_once "sys/mysqldb.php";
require_once "sys/views.php";
require_once "sys/services.php";

use Session\SessionManager;
use Router\Router;
use Database\MySQL;

# check for session availability and start a new session if possible
SessionManager::check_session();

# Use localhost credentials
MySQL::is_localhost(is_localhost);

# Get routes
Router::set_routes();

$path = preg_replace("/.*\.php/", "", $_SERVER['REQUEST_URI']);
$path = preg_replace("/\/$/", "", $path);
$urlc = explode('/', ltrim($path, "/"));
$router = new Router($urlc);
?>