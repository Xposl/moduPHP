<?php

function sys_route() {
  sys_example_init();
  $route = array();

  $route['/'] = array(
    'callback' => 'sys_example_page_base'
  );

  return $route;
}


function sys_example_page_base() {
  //Do somthing
  phpinfo();
}

