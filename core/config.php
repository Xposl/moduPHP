<?php

/**
 * The base core function about config
 * in path core/config.php
 * author: windrainsky@gmail.com
 * */

/**
 * Read the config file from of you system file as SYSTEMNAME.info
 */
function config_get_config($filePath, $readSize = 4096) {
    $configs = array();
    if (file_exists($filePath)) {
        $file_handle = fopen($filePath, 'r');
        $data = fread($file_handle, $readSize);

        if (preg_match_all('/^([a-zA-Z][_a-zA-Z0-9]*)[ ]*=[ ]*([\._a-zA-Z0-9 \/-]*)/m', $data, $matches)) {
            for ($i = 0; $i < sizeof($matches[0]); $i++) {
                $value = $matches[2][$i];
                if ($value == 'true') {
                    $value = true;
                } else if ($value == 'false') {
                    $value = false;
                }
                $configs[strtolower($matches[1][$i])] = $value;
            }
        }
        if (preg_match_all('/^([a-zA-Z][_a-zA-Z0-9]*)((\[[_a-zA-Z0-9]*\]){1,})[ ]*=[ ]*([\._a-zA-Z0-9 \/-]*)/m', $data, $matches)) {
            for ($i = 0; $i < sizeof($matches[0]); $i++) {
                $attribute1 = strtolower($matches[1][$i]);
                if (!isset($configs[$attribute1])) {
                    $configs[$attribute1] = array();
                }
                $attributes = (string) $matches[2][$i];
                if (preg_match_all('/\[([_a-zA-Z0-9]*)\]/m', $attributes, $attr_matches)) {
                    $temp_array = &$configs[$matches[1][$i]];
                    $value = $matches[4][$i];
                    if ($value == 'true') {
                        $value = true;
                    } else if ($value == 'false') {
                        $value = false;
                    }
                    $is_end = false;
                    for ($j = 0; $j < sizeof($attr_matches[0]); $j++) {
                        if ($attr_matches[1][$j] == '') {
                            $temp_array[] = $value;
                            $is_end = true;
                            break;
                        } else {
                            $temp_array[$attr_matches[1][$j]] = array();
                            $temp_array = &$temp_array[$attr_matches[1][$j]];
                        }
                    }
                    if (!$is_end) {
                        $temp_array = $value;
                    }
                }
            }
        }
    }
    return $configs;
}
