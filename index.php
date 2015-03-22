<?php
ini_set('display_errors', 1);
error_reporting(~0);
define('CANROOTPATH', __DIR__);
define('SYSROOTPATH', __DIR__."/system");
define('APPPATH', __DIR__."/application");
define('PUBLICPATH', __DIR__."/public");
define('TEMPPATH', __DIR__."/temp");


require CANROOTPATH."/core/index.php";
require APPPATH."/home.php";


