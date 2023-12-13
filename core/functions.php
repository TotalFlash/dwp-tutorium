<?php

define("DEBUG", (bool) getenv('LOG_DEBUG') ?? false);

function debug_to_logFile($message, $class = null): void {
  if (is_array($message)) {
    $message = json_encode($message, 128);
  }

  if (DEBUG) {
    $class = ($class != null) ? $class : '';
    $message = '[' . (new DateTime())->format('Y-m-d H:i:s ') . $class . ']' . $message . "\n";
    file_put_contents(__DIR__ . '/../logs/logs.txt', $message, FILE_APPEND);
  }
}

function sendHeaderByControllerAndAction($controller, $action): void {
  header('Location: ?c=' . $controller . '&a=' . $action);
}

function DUMP($value): void {
  echo '<pre>DEBUG: ';
  print_r($value);
  echo "</pre>\r\n";

  flush();
}