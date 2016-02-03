<?php

/**
 * include the all msystem functions to your page.
 */
function _module_include_refer_sys($use_modules = array()) {
    global $modules, $database;
    require_once(ROOTPATH . '/core/modules/_modules_.php');
    $modules = new Modules( SYSROOTPATH, $use_modules);
    if ($modules->db) {
        $database = $modules->db;
    }
    return $modules;
}

/**
 * invoke the function of sys modules
 */
function module_invoke_functions($function_name, $args = array(), $use_modules = null) {
    global $modules;
    $use_modules = $modules && $modules->get_sys_name() ? $modules : $use_modules;
    if ($use_modules && $use_modules->get_sys_name()) {
        $use_modules->module_invoke_functions($function_name, $args);
    }
}

/**
 * get file path of your sys module
 */
function module_get_sys_root_path($use_modules = null) {
    global $modules;
    $use_modules = $modules && $modules->get_sys_name() ? $modules : $use_modules;
    if ($use_modules && $use_modules->get_sys_root_path()) {
        return $file_path = $use_modules->get_sys_root_path();
    }
    return null;
}

/**
 * get file path of your sys module
 */
function module_get_file_path($module_name, $use_modules = null) {
    global $modules;
    $use_modules = $modules && $modules->get_sys_name() ? $modules : $use_modules;
    if ($use_modules && $use_modules->get_sys_name()) {
        return $file_path = $use_modules->get_sys_module_file_path($module_name);
    }
    return null;
}

/**
 * get the folder path of your sys module
 */
function module_get_path($module_name, $use_modules = null) {
    $path = module_get_file_path($module_name, $use_modules);
    return $path ? dirname($path) : null;
}

/**
 * get the system loading order
 */
function module_get_system_loading_order($use_modules = null) {
    global $modules;
    $use_modules = $modules && $modules->get_sys_name() ? $modules : $use_modules;
    if ($use_modules && $use_modules->get_sys_name()) {
        return $use_modules->get_sys_module_loading_order();
    }
}
