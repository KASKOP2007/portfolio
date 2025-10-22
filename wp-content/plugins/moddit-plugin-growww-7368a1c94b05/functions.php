<?php

namespace Growww;

//Get a config file
function get_growww_config($name) {
    $fn = GROWWW_ALGEMEEN_DIR.'/config/'.$name.'.php';
    if (is_file($fn)) return include($fn);
}

//Get or make dir
function get_or_growww_mkdir($path) {
    if (is_dir($path)) return true;
    if (substr($path, 0, strlen(ABSPATH)) !== ABSPATH) return false;
    $parts = explode('/', substr($path, strlen(ABSPATH)));
    $p = rtrim(ABSPATH, '/');
    foreach ($parts as $part) {
        $p .= '/'.$part;
        if (!is_dir($p) && !mkdir($p)) return false;
    }
    return true;
}
