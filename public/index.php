<?php

use Dotenv\Dotenv;
use Pimple\Container;
use Rakit\Validation\Validator;
use Pecee\SimpleRouter\SimpleRouter;


include "../vendor/autoload.php";
include "../src/helpers.php";

$dotenv = Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();


// services setup //

$container = new Container();
$container['db'] = function() {
    $host = $_ENV["DB_HOST"];
    $db   = $_ENV["DB_NAME"];
    $user = $_ENV["DB_USER"];
    $pass = $_ENV["DB_PASS"];
    $charset = 'utf8mb4';

    $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => true,
    ];
    try {
        $pdo = new PDO($dsn, $user, $pass, $options);
    } catch (\PDOException $e) {
        throw new \PDOException($e->getMessage(), (int)$e->getCode());
    }
    return $pdo;
};
$container['validator'] = function() {
return new Validator();
};

/* Load external routes file */
require_once '../src/routes.php';

/**
 * The default namespace for route-callbacks, so we don't have to specify it each time.
 * Can be overwritten by using the namespace config option on your routes.
 */

// SimpleRouter::setDefaultNamespace('\Demo\Controllers');

// Start the routing
SimpleRouter::start();
