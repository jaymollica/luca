<?php

require_once  $_SERVER['DOCUMENT_ROOT'] . '/php/class/apize.php';

$dirs = glob('php/configs/*', GLOB_ONLYDIR);

$config = $dirs[0];

$path_to_config = $_SERVER['DOCUMENT_ROOT'] . '/' . $config . '/config.php';
$path_to_whitelist = $_SERVER['DOCUMENT_ROOT'] . '/' . $config . '/whitelist.json';

if(file_exists($path_to_config)) {
  include $path_to_config;
}
else {
  print '<p>There is no config file for this collection.</p>';
  exit;
}

if(file_exists($path_to_whitelist)) {
  $whitelist = json_decode(file_get_contents($path_to_whitelist));
}
else{
  print '<p>There is no whitelist for this collection.</p>';
}

$apize = new apize($pdo,$whitelist);

$tables = $apize->tables();

print '<pre>'; print_r($tables); print '</pre>';

foreach($tables AS $t) {
  $columns = $apize->getColumnNames($t);
  print '<pre>'; print_r($columns); print '</pre>';
}

?>
