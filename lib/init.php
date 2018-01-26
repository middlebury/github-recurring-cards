<?php

require_once(dirname(__FILE__)."/autoload.php");
require_once(dirname(__FILE__)."/../vendor/autoload.php");
require_once(dirname(__FILE__)."/../config.php");

if (empty($config['GITHUB']['CLIENT_ID'])) {
  throw new Exception("\$config['GITHUB']['CLIENT_ID'] must be defined in config.php");
}
if (empty($config['GITHUB']['CLIENT_SECRET'])) {
  throw new Exception("\$config['GITHUB']['CLIENT_SECRET'] must be defined in config.php");
}
$github = new \Github\Client();
$github->authenticate($config['GITHUB']['CLIENT_ID'], $config['GITHUB']['CLIENT_SECRET'], \Github\Client::AUTH_HTTP_PASSWORD);

if (empty($config['TIMEZONE'])) {
  date_default_timezone_set('UTC');
} else {
  date_default_timezone_set($config['TIMEZONE']);
}
