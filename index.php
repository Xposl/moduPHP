<?php
ini_set('display_errors', 1);
error_reporting(1); 
ini_set('display_errors', 1);
error_reporting(E_ALL & ~E_NOTICE);

date_default_timezone_set("Hongkong");
define('ROOTPATH', __DIR__);

require ROOTPATH."/core/index.php";


