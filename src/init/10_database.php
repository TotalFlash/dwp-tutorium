<?php
$dbname = getenv('DB_NAME') ?? '';
$host = getenv('DB_HOST') ?? '';
$db_user = getenv('DB_USER') ?? '';
$db_password = getenv('DB_PASSWORD') ?? '';

$dns = "mysql:dbname=$dbname;host=$host";

$options = [
  PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
  PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
  PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
];

$db = null;
try {
  $db = new PDO($dns, $db_user, $db_password, $options);
}
catch(PDOException $e){
  $message = 'Database connection failed: ' . $e->getMessage();
  die($message);
}