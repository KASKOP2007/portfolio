<?php

namespace Growww;

use Error;
use stdClass;
use Growww\Plugin;

use function wpml_active_languages;
use function get_plugins;
use function wp_enqueue_script;
use function wp_register_script;
use function wp_enqueue_style;
use function wp_register_style;

class Translations
{
    protected bool $active = false;
    protected string $language;
    protected array $languages;
    protected ?array $_settings = null;
    protected ?array $_strings = null;
    protected array $folder_exceptions = ['.', '..', '.history', '.git', 'node_modules', 'vendor'];
    protected array $translate_functions = ['translate', '__', '_e', 'esc_attr__', 'esc_attr_e', 'esc_attr_x', 'esc_html__', 'esc_html_e', 'esc_html_x', '_x', '_ex', '_n', '_nx'];
    /** @var string different content to show on the translations page, depending on current action */
    protected ?string $_translation_page = null;    
    
    protected static Translations $instance;

    public const DEFAULT_LANGUAGE = 'nl';

    /**
     * initialize common values early (when all plugins are loaded)
     *
     * @return void
     */
    public function init(): void
    {
        $this->language = self::DEFAULT_LANGUAGE;
        $this->languages = $this->get_languages() ?? [$this->language, 'en', 'de'];
    }

    /**
     * Setup menu item for growww/translations
     *
     * @return void
     */
    public function add_menu(): void
    {
        add_action('init', [$this, 'handle_menu_actions'], 50, 0);
        add_action('admin_enqueue_scripts', [$this, 'load_admin_resources'], 10, 0);
        add_action('admin_menu', function () {
            add_submenu_page(
                'options-general.php',
                'Growww teksten',
                'Tekst / vertaling',
                'manage_options',
                'growww-string-translations',
                [$this, 'create_translations_page']
            );
        }, 1000);
        //If we need this we want a page for the translate settings by hook
        add_action('admin_bar_menu', function ($wp_admin_bar) {
            //Main menu-item is Growww
            $growww = [
                'id'        => 'adminbar_growww_main',
                'title'     => '<svg xmlns="http://www.w3.org/2000/svg" width="18px" viewBox="0 0 387.535 221.41">
                    <g id="Group_1052" data-name="Group 1052" transform="translate(-1237.291 -310.399)">
                    <g id="Group_1051" data-name="Group 1051">
                        <path id="Path_223" data-name="Path 223" d="M1459.213,310.451v-.028H1291.979v.028a55.3,55.3,0,0,0-54.688,55.288V524.263a7.444,7.444,0,0,0,7.442,7.448h40.262a7.453,7.453,0,0,0,7.448-7.448l.067-140.751a18.809,18.809,0,0,1,18.383-17.977l37.042,0-.005,158.731a7.452,7.452,0,0,0,7.448,7.448h40.437a7.453,7.453,0,0,0,7.448-7.448l-.006-158.731,37.042,0a18.809,18.809,0,0,1,18.384,17.977l.066,140.751a7.453,7.453,0,0,0,7.449,7.448h40.261a7.445,7.445,0,0,0,7.443-7.448V365.739A55.3,55.3,0,0,0,1459.213,310.451Z" fill="#c3c4c7"/>
                        <path id="Path_224" data-name="Path 224" d="M1597.144,476.435a27.687,27.687,0,1,0,27.682,27.687A27.684,27.684,0,0,0,1597.144,476.435Z" fill="#c3c4c7"/>
                    </g>
                    <path id="Path_225" data-name="Path 225" d="M1444.494,310.4" fill="#c3c4c7"/>
                    </g>
                </svg> Instellingen',
                'href'      => admin_url('options-general.php?page=growww'),
            ];
            //Sub page - Translations
            $translations = [
                'id'        => 'adminbar_growww_translations',
                'parent'    => 'adminbar_growww_main',
                'title'     => 'Tekst / vertaling',
                'href'      => admin_url('options-general.php?page=growww-string-translations'),
            ];
            //Sub page - General_settings
            $general_settings = [
                'id'        => 'adminbar_growww_general',
                'parent'    => 'adminbar_growww_main',
                'title'     => 'Algemene instellingen',
                'href'      => admin_url('admin.php?page=growww-algemeen'),
            ];
            //Sub page - Theme_settings
            $theme_settings = [
                'id'        => 'adminbar_growww_theme',
                'parent'    => 'adminbar_growww_main',
                'title'     => 'Thema instellingen',
                'href'      => admin_url('admin.php?page=growww-thema'),
            ];

            //Add them to the args
            $args[] = $growww;
            $args[] = $translations;
            $args[] = $general_settings;
            $args[] = $theme_settings;

            //Add nodes to admin bar
            if (!empty($args)) foreach ($args as $arg) {
                $wp_admin_bar->add_node($arg);
            }
        }, 40);

    }

    /**
     * Register after theme setup the translations hooks
     *
     * @return void
     */
    public function register_translation_hook(): void
    {
        $this->active = true;
        $this->language = defined('ICL_LANGUAGE_CODE') ? \ICL_LANGUAGE_CODE : self::DEFAULT_LANGUAGE;
        add_filter('gettext', function($text, $strid, $domain) {
            if (!$this->active) {
                return $text;
            }
            if (!isset($this->_strings)) {
                if (!did_action('after_setup_theme')) {
                    return $text;
                }
                $this->_strings = [];
            }
            if (!isset($this->_strings[$domain])) {
                $this->_strings[$domain] = false;
                $all = $this->get_strings($domain);
                if (!empty($all)) {
                    $this->_strings[$domain] = array_reduce(
                        $all,
                        function($carry, $row) {
                            $carry[$row->msgid] = $row->msgstr;
                            return $carry;
                        },
                        []
                    );
                }
            }
            if (isset($strid, $this->_strings[$domain][$strid])) {
                $text = $this->_strings[$domain][$strid];
            }
            return $text;
        }, 10, 3);        
    }

    /**
     * Get the language
     *
     * @return string|null
     */
    public function get_language(): ?string
    {
        return $this->language;
    }

    /**
     * Get the languages
     */
    public function get_languages(): ?array
    {
        if (function_exists('wpml_active_languages')) {
            $languages = wpml_active_languages();
            return $languages;
        }
        return null;
    }

    /**
     * Get translation domains
     *
     * @return array
     */
    public function get_translation_domains(): array
    {
        return $this->get_setting('domains') ?? ['growww'];
    }

    public function is_active(): bool
    {
        return $this->active;
    }

    public function set_active(bool $active): bool
    {
        $was = $this->active;
        $this->active = $active;
        return $was;
    }

    /**
     * early handling of translation page actions (when redirection is still possible)
     * this happens in init, but init:10 is too early - dependencies will not be loaded then.
     * running this is init:50 currently.
     *
     * @return void
     */
    public function handle_menu_actions(): void
    {
        // https://algemeen.test/wp-admin/options-general.php?page=growww-string-translations&action=sync
        if (!isset($_GET['page'], $_GET['action']) || $_GET['page'] !== 'growww-string-translations') {
            return;
        }
        $is_post = $_SERVER['REQUEST_METHOD'] === 'POST';
        if ($_GET['action'] === 'save') {
            $strings = stripslashes_deep($_POST['strings']);
            if ($is_post) {
                $this->save_strings($strings);
            }
            return;
        }
        if ($_GET['action'] === 'sync') {
            $folders = stripslashes_deep($_POST['folders'] ?? null);
            $strings = stripslashes_deep($_POST['strings'] ?? null);
            if (isset($strings)) {
                if ($is_post) {
                    $this->add_scan_results($strings);
                }
                return;
            }
            if (!isset($folders)) {
                $this->_translation_page = 'pick_scan_folders';
                return;
            }
            $this->_translation_page = 'generate_translations';
        }
    }

    /**
     * load js/css on admin pages
     *
     * @return void
     */
    public function load_admin_resources(): void
    {
        if (!isset($_GET['page']) || $_GET['page'] !== 'growww-string-translations') {
            return;
        }
        $action = $_GET['action'] ?? null;
        $plugin = Plugin::get_instance();
        wp_register_script('growww-i18n-include', $plugin->get_plugin_dir_url('library/js/include.js'), ['jquery'], '0.'.time());//'1.0.0');
        wp_register_script('growww-i18n-admin-strings', $plugin->get_plugin_dir_url('library/js/admin-strings.js'), ['growww-i18n-include'], '0.'.time());//'1.0.0');
        wp_register_script('growww-i18n-admin-sync', $plugin->get_plugin_dir_url('library/js/admin-sync.js'), ['growww-i18n-include'], '0.'.time());//'1.0.0');
        wp_register_style('growww-i18n-admin', $plugin->get_plugin_dir_url('library/css/admin.css'), [], '0.'.time());//'1.0.0');
        
        wp_enqueue_style('growww-i18n-admin');
        if ($action === 'sync') {
            wp_enqueue_script('growww-i18n-admin-sync');
        } else {
            wp_enqueue_script('growww-i18n-admin-strings');
        }
    }

    /**
     * Create a page in the backend for the texts and translations
     *
     * @return void
     */
    public function create_translations_page(): void
    {
        if (isset($_GET['language'])) {
            $this->language = $_GET['language'];
        }
        if (isset($this->_translation_page)) {
            switch ($this->_translation_page) {
                case 'pick_scan_folders':
                    $this->pick_scan_folders();
                    return;
                case 'generate_translations':
                    $folders = stripslashes_deep($_POST['folders'] ?? null);
                    $this->generate_translations($folders);
                    return;
            }
        }
        //Get all text domains and set to first
        $tabs = $this->get_translation_domains();
        $current = $_GET['domain'] ?? $tabs[0];
        $language = $_GET['language'] ?? $this->language;
        $strings = $this->get_strings($current);
        $active = $this->set_active(false);
        ?>
        <div class="wrap">
            <h2>
                Vertalingen & Teksten
                <form method="POST" style="display:inline" action="<?php echo admin_url("options-general.php?page=growww-string-translations&action=sync"); ?>">
                    <button class="page-title-action">Scan thema/plugins</button>
                </form>
            </h2>

            <div style="display:block;width:100%;float:left;margin-bottom:24px;">
                <ul class="subsubsub">
                    <?php foreach ($this->languages as $index => $language_code): ?>
                    <li>
                        <?php
                        echo 
                            ($index === 0 ? '' : ' | ')
                            .'<a href="'
                            ."?page=growww-string-translations&domain=$current&language=$language_code\""
                            .($language_code === $language ? ' class="current"' : '').'>'
                            .strtoupper($language_code)
                            .'</a>';
                        ?>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <?php

            //Set html for tabs
            echo '<div id="icon-themes" class="icon32" style="display:block;width:100%;"><br></div>';
            echo '<h2 class="nav-tab-wrapper">';
            foreach ($tabs as $domain) {
                $class = ($domain == $current) ? ' nav-tab-active' : '';
                echo "<a class='nav-tab$class' href='?page=growww-string-translations&domain=$domain&language=$language'>$domain</a>";
            }
            echo '</h2>';

            if (empty($strings)) {
                ?>
                <p class="information">Scan thema's/plugins om vertaalbare teksten toe te voegen, of kies een ander domein.</p>
                <?php
                return;
            }

            ?>

            <form method="post" action="<?php echo admin_url("options-general.php?page=growww-string-translations&action=save&domain=$current&language=$language"); ?>">

                <table class="form-table js-translations-table">

                    <tr>
                        <td><strong>String ID</strong></td>
                        <td><strong><?=strtoupper($language)?></strong></td>
                    </tr>
                    <?php
                    foreach ($strings as $string_row) {
                        $default_value = __($string_row->msgid, $current);
                        $value = $string_row->msgstr;
                        $backup = isset($value) && $value !== $default_value ? $value : '';
                        ?>
                        <tr valign="top">
                            <td class="ptr js-msgid" title="Dubbelklik om de standaard waarde in te vullen">
                                <?=esc_html($string_row->msgid)?><br>
                                <small></small>
                            </td>
                            <td>
                                <input type="text" placeholder="<?=esc_attr($default_value)?>" data-backup="<?=esc_attr($backup)?>" name="strings[<?=esc_attr($string_row->msgid)?>]" value="<?=esc_attr($value)?>" />
                            </td>
                        </tr>
                        <?php
                    }
                    ?>

                </table>

                <?php submit_button(); ?>
                <fieldset>
                    <legend>Zet alle bovenstaande waarden op ..</legend>
                    <button class="button js-all-default">standaard waarde</button>
                    &nbsp;
                    <button class="button js-all-empty">leeg</button>
                </fieldset>

            </form>
            <?php // @todo hier zat het script ?>
        </div>
        <?php
        $this->set_active($active);
    }

    /**
     * show interface to pick theme and plugin folders for scanning
     *
     * @return void
     */
    private function pick_scan_folders(): void
    {
        $theme_folders = [];
        $plugin_folders = [];
        /** @var WP_Theme[] $themes */
        $active_theme = wp_get_theme();
        $themes = wp_get_themes();
        foreach ($themes as $slug => $theme) {
            $pick = (object) [
                'folder' => $theme->get_template_directory(),
                'name' => $theme->name,
                'author' => $theme->author,
                'checked' => false,
            ];
            if ($pick->folder === $active_theme->get_template_directory()) {
                $pick->checked = true;
            }
            $theme_folders[] = $pick;
        }
        $plugins = get_plugins();
        foreach ($plugins as $slug => $plugin_array) {
            $pick = (object) [
                'folder' => WP_PLUGIN_DIR.'/'.dirname($slug),
                'name' => $plugin_array['Name'],
                'author' => $plugin_array['AuthorName'],
                'checked' => false,
            ];
            $plugin_folders[] = $pick;
        }
        ?>
        <form class="js-scan-locations" method="POST" style="display:inline" action="<?php echo admin_url("options-general.php?page=growww-string-translations&action=sync"); ?>">
            <h3>Thema's</h3>
            <?php foreach ($theme_folders as $index => $pick): ?>
                <label title="<?=esc_attr(basename($pick->folder))?>" for="theme_<?=$index?>">
                    <input type="checkbox" id="theme_<?=$index?>" name="folders[]" value="<?=esc_attr($pick->folder)?>"<?=$pick->checked ? ' checked' : ''?> />
                    <?php echo $pick->name.( $pick->author ? " [$pick->author]" : '' ); ?>
                </label><br/>
            <?php endforeach; ?>
            <hr/>
            <h3>Plugins</h3>
            <?php foreach ($plugin_folders as $index => $pick): ?>
                <label title="<?=esc_attr(basename($pick->folder))?>" for="plugin_<?=$index?>">
                    <input type="checkbox" id="plugin_<?=$index?>" name="folders[]" value="<?=esc_attr($pick->folder)?>"<?=$pick->checked ? ' checked' : ''?> />
                    <?php echo $pick->name.( $pick->author ? " [$pick->author]" : '' ); ?>
                </label><br/>
            <?php endforeach; ?>
            <hr/><br/>
            <button class="page-title-action button button-primary">Scan deze folders</button>
        </form>
        <a href="<?php echo admin_url("options-general.php?page=growww-string-translations"); ?>" class="page-title-action button">terug</a>
        <?php
    }

    /**
     * save strings in current language, and redirect to the translations page
     *
     * @param  array $strings
     * @return void
     */
    private function save_strings(array $strings): void
    {
        if (isset($_GET['language'])) {
            $this->language = $_GET['language'];
        }
        $domain = $_GET['domain'] ?? $this->get_translation_domains()[0];
        $existing = $this->get_strings($domain);
        foreach ($existing as $row) {
            if (isset($strings[$row->msgid]) && $strings[$row->msgid] === $row->msgstr) {
                continue;
            }
            $this->save_string($row->msgid, $strings[$row->msgid], $domain);
        }
        wp_redirect(admin_url("options-general.php?page=growww-string-translations&domain=$domain&language=$this->language"));
        exit;
    }

    /**
     * add picked strings/domains to all languages, and redirect to the translations page
     *
     * @param  array $results 
     * @return void
     */
    private function add_scan_results(array $results): void
    {
        $add_domains = [];
        $known_domains = $this->get_translation_domains();
        foreach ($results as $domain => $strings) {
            $this->add_strings_in_all_languages($domain, $strings);
            if (!in_array($domain, $known_domains, true)) {
                $add_domains[] = $domain;
            }
        }
        if (!empty($add_domains)) {
            $known_domains = array_merge($known_domains, $add_domains);
            $settings = get_option('growww-i18n');
            $settings['domains'] = $known_domains;
            update_option('growww-i18n', $settings, true);
        }
        wp_redirect(admin_url("options-general.php?page=growww-string-translations"));
        exit;
    }

    /**
     * show interface so the user can pick which strings/domains to add
     *
     * @param  array $domaindriven_array 
     * @return void
     */
    private function pick_scan_results(array $domaindriven_array): void
    {
        ?>
        <p class="information">Dubbelklik op een domein om de teksten daarin aan/uit te zetten.</p>
        <form method="POST" class="js-scan-results" style="display:inline" action="<?php echo admin_url("options-general.php?page=growww-string-translations&action=sync"); ?>">
            <?php foreach ($domaindriven_array as $domain => $results):?>
                <h3 class="js-domain-title ptr"><?=esc_html($domain)?></h3>
                <div class="js-domain-strings">
                    <?php foreach ($results as $index => $result): ?>
                        <label for="<?=$domain.'_'.$index?>">
                            <input type="checkbox" id="<?=$domain.'_'.$index?>" name="strings[<?=esc_attr($domain)?>][]" value="<?=esc_attr($result['string'])?>" />
                            <?php echo esc_html(sprintf('%s [%s : %s]', $result['string'], $result['file'], $result['line'])); ?>
                        </label><br/>
                    <?php endforeach; ?>
                </div>
                <hr/>
            <?php endforeach; ?>
            <br/>
            <button class="page-title-action button button-primary">Toevoegen</button>
            <br/><br/>
            <button class="button js-all">Selecteer alle/geen teksten</button>
        </form>
        <br/><br/>
        <a href="<?php echo admin_url("options-general.php?page=growww-string-translations"); ?>" class="page-title-action button">terug</a>
        <?php
    }

    /**
     * Generate a list with all translateble strings from all domains within certain folders or active theme
     *
     * @return void
     */
    public function generate_translations(?array $folders = null): void
    {
        if (!isset($folders)) {
            $folders = [get_template_directory()];
        }
        //Setup some vars
        $translate_functions = $this->translate_functions;
        $list_of_translatables = [];

        foreach ($folders as $folder) {
            //Do function get all php, on result do function inside
            $this->get_all_php($folder, function ($finder) use ($translate_functions, &$list_of_translatables) {

                //Get all calls to functions
                $calls = $this->get_calls_to_function($finder, $translate_functions, true);

                //If no strings, we don;'t need to find matches
                if (count($calls) < 1) return false;

                //Get all calls
                foreach ($calls as $line => $functions) {

                    //Foreach function ook checken
                    foreach ($functions as $function_type => $call_values) {

                        //Check for entrire results from all rows and types
                        if (!ctype_alnum(substr($call_values[2], 0, 1))) $call_values[2] = '\\' .  $call_values[2];

                        try {
                            preg_match_all('/' . $function_type . '\\(' . $call_values[2] . '(.*?)\'\\)/', $call_values[1], $matches);

                            //Break out when empty
                            if (empty($matches[0])) continue;

                            //else add up
                            $list_of_translatables[$line][$function_type]['result'] = $matches[0];
                            $list_of_translatables[$line][$function_type]['values'] = $call_values;
                        } catch (Error $e) {
                        }
                    }
                }
            });
        }

        $domaindriven_array = $this->group_translatables_by_domain($list_of_translatables);
        $this->pick_scan_results($domaindriven_array);
    }

    /**
     * Generate all the files needed for translation
     *
     * @param [array] $translatables
     * @return void
     */
    public function group_translatables_by_domain($translatables): array
    {

        //WE're gonna translate the strings array on domainname:
        $domaindriven_array = [];
        foreach ($translatables as $line => $translate_data) {

            //Loop over translate functions
            foreach ($translate_data as $function => $results_data) {

                //File where found = 
                $file_found = explode("/", $results_data['values'][0])[count(explode("/", $results_data['values'][0])) - 1];

                //Preg match to get all 'test' between 'this'
                preg_match_all("/\((?:[^()]|(?R))+\)|'[^']*'|[^(),\s]+/", substr($results_data['result'][0], strlen($function) + 1), $matches);

                //Text domain
                $text_domain = $matches[0][1] ?? 'no domain?';
                $text_domain = str_replace('"', '', $text_domain);
                $text_domain = str_replace("'", "", $text_domain);
                // remove ' and " from beginning and maybe end of string
                $matches[0][0] = preg_replace('/^(\'|")(.+?)(\\1)?$/', '$2', $matches[0][0]);

                //Get array index
                $array_index = isset($domaindriven_array[$text_domain]) ? count($domaindriven_array[$text_domain]) : 0;
                $domaindriven_array[$text_domain][$array_index]['string']  = $matches[0][0];
                $domaindriven_array[$text_domain][$array_index]['file']    = $file_found;
                $domaindriven_array[$text_domain][$array_index]['line']    = $line;
            }
        }
        return $domaindriven_array;
    }

    /**
     * Search for all calls to translate functions
     *
     * @param string $file
     * @param array  $translate_functions
     * @return array
     */
    public function get_calls_to_function(string $file, array $translate_functions): array
    {
        $lines_with_calls = [];

        //Loop over all possible calls
        foreach ($translate_functions as $index => $func) {

            //Loop over the lines of file
            $handle = fopen($file, "r");
            if ($handle) {
                $line_number = 0;
                while (($line = fgets($handle)) !== false) {

                    //Setup vars
                    $line_number++;
                    $offset = 0;

                    while ($strpos = strpos($line, $func . '(', $offset)) {

                        $offset = $strpos + 1;

                        //Save in results 
                        $lines_with_calls[$line_number][$func][] = $file;
                        $lines_with_calls[$line_number][$func][] = substr($line, 0, strlen($line));
                        $lines_with_calls[$line_number][$func][] = substr($line, $offset + strlen($func), 1);
                    }
                }

                fclose($handle);
            }
        }

        return $lines_with_calls;
    }


    /**
     * Get all the PHP files that might contain the translate functions
     *
     * @param string   $folder
     * @param callable $callback_function
     * @return void
     */
    public function get_all_php(string $folder, callable $callback_function): void
    {

        //Get the real folder
        $folder = realpath($folder);

        //Create a place to save our resurve
        $array_with_files = [];
        $folder_exceptions = $this->folder_exceptions;

        //Make a callback to our callback
        $callback = null;
        $callback = function ($folder) use (&$callback, &$array_with_files, &$folder_exceptions) {

            //Scan all dirs and sub dirs
            foreach (scandir($folder) as $folder_item) {

                //Stop it if we hit a big no-no
                if (in_array($folder_item, $folder_exceptions)) continue;

                //Set full path
                $full = $folder . '/' . $folder_item;

                //If a dir recurse else check for .php ext
                if (is_dir($full)) $callback($full);
                elseif (is_file($full) && substr($folder_item, -4) === '.php') $array_with_files[] = $full;
            }
        };
        $callback($folder);

        //For every file we find, let's throw the path into the callback function
        foreach ($array_with_files as $path) {
            $callback_function($path);
        }
    }

    /**
     * get an array of msgid/msgstr objects in some domain and the current language
     *
     * @param  string $domain 
     * @return array  object[]: {string $msgid, string $msgstr}
     */
    private function get_strings(string $domain): array
    {
        global $wpdb;
        return $wpdb->get_results($wpdb->prepare(
            "SELECT `msgid`,`msgstr` FROM `growww_i18n_strings` WHERE `lang`=%s AND `domain`=%s ORDER BY `msgid` ASC",
            $this->language,
            $domain
        ));
    }

    /**
     * add array of strings to some domain in all languages
     *
     * @param  string $domain  
     * @param  array  $strings 
     * @return bool
     */
    private function add_strings_in_all_languages(string $domain, array $strings): bool
    {
        $some_added = false;
        foreach ($this->languages as $language) {
            if ($this->add_strings($domain, $strings, $language)) {
                $some_added = true;
            }
        }
        return $some_added;
    }

    /**
     * add array of strings to some domain in specific|current language. filters out already known strings.
     *
     * @param  string      $domain   
     * @param  array       $strings  
     * @param  string|null $language 
     * @return bool
     */
    private function add_strings(string $domain, array $strings, ?string $language = null): bool
    {
        global $wpdb;
        // remove existing msgids
        $strings = array_diff($strings, $this->get_msgids($domain, $language));
        if (empty($strings)) {
            return false;
        }
        $_domain = esc_sql($domain);
        $_language = esc_sql($language ?? $this->language);
        $values = array_map(fn($msgid) => "('$_language','$_domain','".esc_sql($msgid)."')", $strings);
        $query = "INSERT INTO `growww_i18n_strings` (`lang`,`domain`,`msgid`) VALUES ".implode(',', $values);
        return (bool) $wpdb->query($query);
    }

    private function save_string(string $msgid, string $msgstr, string $domain): bool
    {
        global $wpdb;
        if ($msgstr === '') {
            $query = $wpdb->prepare("UPDATE `growww_i18n_strings` SET `msgstr`=NULL WHERE `msgid`=%s AND `lang`=%s AND `domain`=%s",
                $msgid,
                $this->language,
                $domain
            );
            return (bool) $wpdb->query($query);
        } else {
            $query = $wpdb->prepare("UPDATE `growww_i18n_strings` SET `msgstr`=%s WHERE `msgid`=%s AND `lang`=%s AND `domain`=%s",
                $msgstr,
                $msgid,
                $this->language,
                $domain
            );
            return (bool) $wpdb->query($query);
        }
    }

    /**
     * get an array of msgids in this language and some domain
     *
     * @param  string $domain 
     * @return array
     */
    private function get_msgids(string $domain, ?string $language = null): array
    {
        global $wpdb;
        $language = $language ?? $this->language;
        return $wpdb->get_col($wpdb->prepare(
            "SELECT `msgid` FROM `growww_i18n_strings` WHERE `lang`=%s AND `domain`=%s ORDER BY `msgid` ASC",
            $language,
            $domain
        ));
    }

    /**
     * shortcut for getting some growww-i18n option
     *
     * @param  string $key 
     * @return mixed
     */
    public function get_setting(string $key)
    {
        if (!isset($this->_settings)) {
            $this->_settings = get_option('growww-i18n') ?: [];
        }
        return $this->_settings[$key] ?? null;
    }

    /**
     * singleton static getter
     *
     * @return Translations
     */
    public static function get_instance(): Translations
    {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }
}

