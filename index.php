<?php

//header start

session_start();
require_once  $_SERVER['DOCUMENT_ROOT'] . '/php/class/apize.php';
require_once($_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php');
$loader = new Twig_Loader_Filesystem($_SERVER['DOCUMENT_ROOT'] . '/tpls');
$twig = new Twig_Environment($loader);

//end header

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
  print '<p>There is no whitelist for this collection!</p>';
}

$apize = new apize($pdo,$whitelist);

$tables = $apize->tables();

foreach($tables AS $t) {
  $columns[] = $apize->getColumnNames($t);
}

$columns = call_user_func_array('array_merge', $columns);

if(!empty($columns)) {
  $arguments = $apize->getArguments($columns);

  print '<pre>'; print_r($arguments); print '</pre>';

  echo $twig->render('base_header.html',array());
  foreach($arguments AS $args) {
    echo $twig->render('documentation.html', array('arguments' => $args));
  }
  echo $twig->render('base_footer.html',array());
}

?>
