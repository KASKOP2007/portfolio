<?php
/*
Plugin Name: Plugin Growww
Plugin URI: https://growww.nl/
Description: Growww plugin
Version: 0.1.25
Author: Growww
Author URI: https://growww.nl/
License: GPLv2 or later
Text Domain: growww
Update URI: https://pluginalgemeen.moddit.dev/wp-json/growww/v1/plugin/get_newest_version
Requires PHP: 7.4
Requires at least: 5.2
*/

/**
 * @package Growww
 */

use Growww\Acf;
use Growww\Plugin;
use Growww\Translations;
use function Growww\get_growww_config;
use Growww\Services\Wordpress_Migrations_Service;

require_once(ABSPATH . 'wp-admin/includes/plugin.php');
$GLOBALS['growww_plugin_data'] = get_plugin_data(__FILE__);


if (!function_exists('wp_get_current_user')) {
    include(ABSPATH . "wp-includes/pluggable.php");
}

//Fallback on direct call
if (!function_exists('add_action')) {
    echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
    exit;
}

//Define all
define('GROWWW_ALGEMEEN_PLUGINFILE', realpath(__FILE__));
define('GROWWW_ALGEMEEN_DIR', dirname(GROWWW_ALGEMEEN_PLUGINFILE));
define('GROWWW_REQUEST_TIME', time());
define('GROWWW_TIMEZONE_OFFSET', get_option('gmt_offset') * HOUR_IN_SECONDS);

//Visitor IP
if (isset($_SERVER['HTTP_CF_CONNECTING_IP'])) $remote_addr = 'HTTP_CF_CONNECTING_IP';
elseif (isset($_SERVER['HTTP_X_CLIENT_IP'])) $remote_addr = 'HTTP_X_CLIENT_IP'; // nginx/fastcgi? sulfur/tc
else $remote_addr = 'REMOTE_ADDR';
define('GROWWW_VISITOR_IP', $_SERVER[$remote_addr] ?? false);

$is_activating = isset($_GET['action'], $_GET['plugin']) && in_array($_GET['action'], ['activate', 'error_scrape']) && $_GET['plugin'] === plugin_basename(__FILE__);

if ($is_activating) {
    if (version_compare(PHP_VERSION, '7.4', '<')) {
        die('Je moet minimaal php 7.4 (liefst 8.0 of hoger) draaien om gebruik te maken van onze plugin.');
    }
}

//Setup autoload for classes
spl_autoload_register(function ($cls) {

    //Check for base GROWWW in cls
    $base = 'Growww';
    if (strpos($cls, $base . '\\') === 0) {

        //Create an array with class names exploded on //
        $plugin_parts = explode('\\', substr($cls, strlen($base) + 1));

        //If it's for local look in template directory
        if ($plugin_parts[0] === 'Local') $dir = get_template_directory() . '/classes';

        //If the dir exists shift the array by 1 to look for name
        if (isset($dir)) array_shift($plugin_parts);
        else $dir = GROWWW_ALGEMEEN_DIR . '/classes';

        //Find kebabcase version of path
        while (count($plugin_parts) > 1) {

            //Get right text markup //Former camel_to_kebabcase
            $plugin_part = lcfirst(preg_replace_callback('/([a-z0-9])([A-Z])/', function ($m) {
                return $m[1] . '-' . strtolower($m[2]);
            }, array_shift($plugin_parts)));
            $dir .= '/' . $plugin_part;
        }

        //Check if the file exists and if so include it
        if (is_file($dir . '/' . $plugin_parts[0] . '.php')) {
            include($dir . '/' . $plugin_parts[0] . '.php');
            return;
        }
    }
});

//Include the functions
include GROWWW_ALGEMEEN_DIR . '/functions.php';
include GROWWW_ALGEMEEN_DIR . '/functions-admin.php';
include GROWWW_ALGEMEEN_DIR . '/functions-front.php';
include GROWWW_ALGEMEEN_DIR . '/controller/Plugin_Controller.php';

// //Update version code
// $current_version = get_growww_config('growww-plugin')['version'];
// define('GROWWW_ALGEMEEN_VERSION', $current_version);

//Hooks & actions
// register_activation_hook(__FILE__, 'plugin_activate_growww_algemeen');
// register_deactivation_hook(__FILE__, 'plugin_deactivate_growww_algemeen');

add_action('admin_init', function () {
    (new Wordpress_Migrations_Service(get_growww_config('migrations')))->run_new_migrations();
});

new Acf();
Plugin::get_instance();

add_action('plugins_loaded', function () {
    Translations::get_instance()->init();
    Translations::get_instance()->add_menu();
    if (get_option('growww_migrations_version')) {
        Translations::get_instance()->register_translation_hook();
    }
});

add_action('wp_enqueue_scripts', function () {
    if (get_field('twitter', 'options')) {
        wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.0/css/fontawesome.min.css', [], '6.7.0');
        wp_enqueue_style('font-awesome-brands', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.0/css/brands.min.css', [], '6.7.0');
    }
});
