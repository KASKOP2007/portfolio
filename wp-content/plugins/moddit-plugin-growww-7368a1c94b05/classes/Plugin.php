<?php

namespace Growww;

use stdClass;

class Plugin
{

    public static $plugin_data;

    private static $plugin_instance;

    /**
     * Plugin constructor.
     */
    public function __construct()
    {
        self::$plugin_data = $GLOBALS['growww_plugin_data'];
        self::$plugin_instance = $this;

        //Add filter & action
        add_filter('pre_set_site_transient_update_plugins', [$this, 'add_plugin_to_update']);
        add_action('init', [$this, 'allowMarketingGravityForms'], 100);
    }

    /**
     * Allow Gravity Forms for the Growww marketing team
     */
    public function allowMarketingGravityForms(): void
    {
        $editorRole = get_role('editor');
        if ($editorRole) $editorRole->add_cap('gform_full_access');
    }

    public static function get_plugin_dir_url(): string
    {
        return explode('/', substr(dirname(__FILE__), (strlen(WP_PLUGIN_DIR) + 1)))[0];
    }

    public static function get_instance(): Plugin
    {
        if (!isset(self::$plugin_instance)) {
            self::$plugin_instance = new self;
        }
        return self::$plugin_instance;
    }

    public static function add_plugin_to_update($transient)
    {
        //If we are dev we don't need to check
        if (isset($_SERVER['HTTP_HOST']) && $_SERVER['HTTP_HOST'] === 'pluginalgemeen.moddit.dev') return $transient;

        //Get the old and new version...
        $current_version = self::$plugin_data['Version'];
        $new_data = json_decode(self::get_newest_version());
        $new_version = $new_data->version;

        //Chech the versions 
        if (version_compare($current_version, $new_version, '>=')) return $transient;

        //Get the plugin folder data
        $plugin_folder = explode('/', substr(dirname(__FILE__), (strlen(WP_PLUGIN_DIR) + 1)))[0];

        //Add the new version to the transient
        $plugin_transient = new stdClass;
        $plugin_transient->id = 'https://pluginalgemeen.moddit.dev/';
        $plugin_transient->slug = $plugin_folder;
        $plugin_transient->plugin = $plugin_folder . '/' . sanitize_title_with_dashes(self::$plugin_data['Name']) . '.php';
        $plugin_transient->new_version = $new_version;
        $plugin_transient->url = self::$plugin_data['PluginURI'];
        $plugin_transient->package = 'https://pluginalgemeen.moddit.dev/wp-content/plugins/' . sanitize_title_with_dashes(self::$plugin_data['Name']) . '/latest.zip';
        $plugin_transient->requires = self::$plugin_data['RequiresWP'];
        $plugin_transient->tested = self::$plugin_data['RequiresWP'];
        $plugin_transient->requires_php = self::$plugin_data['RequiresPHP'];

        //Compare the versions, add slug key to array
        $transient->response[$plugin_folder . '/' . sanitize_title_with_dashes(self::$plugin_data['Name']) . '.php'] = $plugin_transient;

        return $transient;
    }

    public static function get_newest_version(): string
    {
        //Call to base URL
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, self::$plugin_data['UpdateURI']);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
        ));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        $result = curl_exec($curl);

        //Get response
        $response = json_decode($result, true);

        return $response;
    }
}
