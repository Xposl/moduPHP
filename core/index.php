<?php

function dprint_r($var){
  echo "<pre>";
  print_r($var);
  echo "</pre>";
}

require CANROOTPATH.'/core/path.php';
require CANROOTPATH.'/core/config.php';
require CANROOTPATH.'/core/export.php';
require CANROOTPATH.'/core/database/mysql.php';
require CANROOTPATH.'/core/modules/module.php';


