<?php

spl_autoload_register(function ($class) {
  $file = dirname(__FILE__).'/' . str_replace('\\', '/', str_replace('_', '/', $class)) . '.php';
  if (file_exists($file)) {
    include_once $file;
  }
});
