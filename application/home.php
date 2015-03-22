<?php

 //get the system you want
  $system = path_args(0);
  
  if($system){
    $file = APPPATH.DS.$system.DS.'route.php';
    if(is_file($file)){
      //reset module
      _module_include_refer_sys($system);
      //set system root path
      define('SYSVIEWPATH', APPPATH.DS.$system);
      include_once(SYSVIEWPATH.DS.'route.php');
      path_route_callback(substr(path_current_path(),strpos(path_current_path(),$system)+strlen($system)),sys_route());
      exit(1);
    }
  }
?>
<!--TODO-->
<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8" />
    <title>Kissit Can</title>
  </head>
  <body>
    <a href="<?php print path_url('handmaker'); ?>">Handmaker</a>
  </body>
</html>
