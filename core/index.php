<?php
define('DS', DIRECTORY_SEPARATOR);
define('SYSROOTPATH', ROOTPATH.'/system');
define('APPPATH', ROOTPATH.'/application');
define('PUBLICPATH', ROOTPATH.'/public');
define('TEMPPATH', ROOTPATH.'/temp');

function dprint_r($var){
  echo "<pre>";
  print_r($var);
  echo "</pre>";
}

require ROOTPATH.'/core/path.php';
require ROOTPATH.'/core/config.php';
require ROOTPATH.'/core/export.php';
require ROOTPATH.'/core/database/mysql.php';
require ROOTPATH.'/core/modules/module.php';


$file = SYSROOTPATH.'/route.php';
if(is_file($file)){
  //reset module
  _module_include_refer_sys();
  include_once($file);
  path_route_callback(path_current_path(),sys_route());
}
  


