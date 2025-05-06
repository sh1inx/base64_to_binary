<?php
require 'vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

DB::$dsn = $_ENV['DB_DSN'];
DB::$user = $_ENV['DB_USER'];
DB::$password = $_ENV['DB_PASSWORD'];
?>