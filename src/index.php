<?php

// Session start
session_save_path('data/');
session_start();

require_once 'init/10_database.php';
require_once 'init/20_imports.php';

$loggedIn = (bool) ($_SESSION['loggedIn'] ?? false);
$controller = 'User';
$action = 'login';

if(isset($_GET['c'], $_GET['a']) && $loggedIn) {
	$controller = $_GET['c'];
	$action = $_GET['a'];
}

$controllerPath = __DIR__ . "/controller/{$controller}Controller.php";

if(file_exists($controllerPath)) {
  require_once $controllerPath;

  $controllerClass = "\\GWP\\{$controller}Controller";

  if(class_exists($controllerClass)) {
    $controllerInstance = new $controllerClass($action, $controller);

    if(method_exists($controllerInstance, $action)) {
      $controllerInstance->$action();
      $controllerInstance->renderHTML();
    } else {
      die("Action not found! $controller / $action");
    }
  } else {
    die("ControllerClass not found! $controller / $action");
  }
} else {
  die("Controller not found! $controller / $action");
}

