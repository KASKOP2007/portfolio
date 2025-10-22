<?php

namespace Growww;

use WP_REST_Controller;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ZipArchive;

/**
 * Core class to access plugin details via the REST API.
 *
 * @since 4.7.0
 *
 * @see WP_REST_Controller
 */
class Plugin_Controller extends WP_REST_Controller
{
    protected $namespace;
    protected $topic;

    /**
     * Constructor.
     *
     * @since 4.7.0
     *
     * @param string $post_type Post type.
     */
    public function __construct()
    {

        $this->namespace = 'growww/v1';
        $this->topic = 'plugin';

        add_action('rest_api_init', [$this, 'register_routes']);
    }

    /**
     * Registers the routes for the objects of the controller.
     *
     * @since 4.7.0
     *
     * @see register_rest_route()
     */
    public function register_routes()
    {

        //Get new version
        register_rest_route($this->namespace . '/' . $this->topic, '/get_newest_version', [
            'method' => 'GET',
            'callback' => [$this, 'get_newest_version'],
        ]);
    }

    public function create_package($slug)
    {
        //Get ziplocation
        $plugin_folder = explode('/', substr(dirname(__FILE__), (strlen(WP_PLUGIN_DIR) + 1)))[0];
        $zip_location = WP_PLUGIN_DIR . '/' . $plugin_folder;

        //Check if we have faulty version zip
        if (file_exists($zip_location . '/latest.zip')) return false;

        //Create zip
        $zip = new ZipArchive();
        $zip->open($zip_location . '/latest.zip', ZipArchive::CREATE | ZipArchive::OVERWRITE);

        //Get the plugin folder
        $directories = glob(WP_PLUGIN_DIR . '/*', GLOB_ONLYDIR);

        //We need te check what index matches the plugin slug
        $index = -1;
        foreach ($directories as $key => $directory) {
            if (explode('/', $directory)[count(explode('/', $directory)) - 1] == explode('/', substr(dirname(__FILE__), (strlen(WP_PLUGIN_DIR) + 1)))[0]) $index = $key;
        }

        //Escape if we didn't find the plugin
        if ($index == -1) return false;

        //Check for folder to do a recurse
        if (is_dir($directories[$index])) {

            //Get all the files in the folder
            $directory_iterator = new RecursiveDirectoryIterator($directories[$index]);
            foreach (new RecursiveIteratorIterator($directory_iterator, RecursiveIteratorIterator::LEAVES_ONLY) as $filename => $file) {

                //Set data
                $relative_path = $plugin_folder . substr($filename, strlen($directories[$index]));
                $file_path = substr($filename, strlen($directories[$index]) + 1);

                //Cut off the git and empty
                if (substr($file_path, 0, 1) == '.' || strlen($file_path) < 1 || is_dir($file)) continue;

                //Add to the zip
                $zip->addFile($file, $relative_path);
            }

            //Zip archive will be created only after closing object
            $zip->close();
        };
    }

    public function get_newest_version($request): string
    {
        //Get the data
        $plugin_data = $GLOBALS['growww_plugin_data'];

        //Check if we need to build up the zip first...
        if ($_SERVER['HTTP_HOST'] === 'pluginalgemeen.moddit.dev') $this->create_package(sanitize_title_with_dashes($plugin_data['Name']));

        //Create new data
        $plugin_info = [];
        $plugin_info['version'] = $plugin_data['Version'];
        $plugin_info['package'] = 'https://pluginalgemeen.moddit.dev/wp-content/plugins/' . sanitize_title_with_dashes($plugin_data['Name']) . '/latest.zip';
        $plugin_info['author'] = $plugin_data['Author'];
        $plugin_info['external'] = $plugin_data['PluginURI'];
        $plugin_info['requires'] = $plugin_data['RequiresWP'];
        $plugin_info['tested'] = $plugin_data['RequiresWP'];
        $plugin_info['php'] = $plugin_data['RequiresPHP'];
        $plugin_info['slug'] = sanitize_title_with_dashes($plugin_data['Name']);

        return json_encode($plugin_info);
    }
}

new Plugin_Controller();
