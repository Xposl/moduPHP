<?php

global $base_url;
$base_url = $_SERVER['REQUEST_URI'];
if (isset($_SERVER['PATH_INFO'])) {
    $base_url = substr($base_url, 0, strpos($base_url, $_SERVER['PATH_INFO']));
}

/**
 * get path arguments by index
 */
function path_args($index = 0) {
    if (!isset($_SERVER['PATH_INFO']))
        return '';
    $path = $_SERVER['PATH_INFO'];
    $args = explode('/', $path);
    $args = array_slice($args, 1);
    return isset($args[$index]) ? $args[$index] : null;
}

/**
 * get current path
 */
function path_current_path() {
    if (!isset($_SERVER['PATH_INFO']))
        return '';
    return $_SERVER['PATH_INFO'];
}

/**
 * get url
 */
function path_url($path) {
    $base_url = $_SERVER['REQUEST_URI'];
    if (isset($_SERVER['PATH_INFO'])) {
        $base_url = substr($base_url, 0, strpos($base_url, $_SERVER['PATH_INFO']));
        return $base_url . '/' . $path;
    }
    $base_url = preg_replace('/\/+/', '/', $base_url);
    return $base_url . $path;
}

/**
 * match path in route
 */
function path_route($path, $route) {
    $args = explode('/', $path);
    $args = array_slice($args, 1);
    foreach ($route as $route_path => $value) {
        $path_regex = str_replace('%', '([a-zA-Z0-9_-]*)', $route_path);
        $path_regex = '/^\/?' . str_replace('/', '\/', $path_regex) . '\/?$/';
        if (preg_match($path_regex, $path, $match)) {
            $view_args = array_slice($match, 1);
            $value['args'] = $view_args;
            return $value;
        }
    }
    return array();
}

function path_route_callback($path, $route) {
    global $modules;
    if ($path == '')
        $path = '/';
    $sys_name = $modules->get_sys_name();
    $result = path_route($path, $route);
    //TODO: add cache
    if (isset($result['callback']) && function_exists($result['callback'])) {
        call_user_func_array($result['callback'], $result['args']);
    } else{
        header("HTTP/1.0 404 Not Found");
        $noFound = path_route('404', $route);
        if (isset($noFound['callback']) && function_exists($noFound['callback'])) {
            call_user_func_array($noFound['callback'], $noFound['args']);
        }else{
            echo "Page No Found";
        }
    }
}
