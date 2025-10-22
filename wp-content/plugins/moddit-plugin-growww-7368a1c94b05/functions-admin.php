<?php

// Register the menu.
add_action( "admin_menu", "growww_plugin_menu" );
if (isset($_POST['action']) && $_POST['action'] === 'update_growww_settings') {
    add_action("init", "update_growww_settings");
}

/**
 * Setup menu item for the plugin
 *
 * @return void
 */
function growww_plugin_menu(): void 
{
    add_submenu_page( "options-general.php",    // Which menu parent
        "Growww",                               // Page title
        "Growww",                               // Menu title
        "manage_options",                       // Minimum capability (manage_options is an easy way to target administrators)
        "growww",                               // Menu slug
        "growww_plugin_options"                 // Callback that prints the markup
    );
}

/**
 * Plugin option page generator, makes the HTML for the admin settings page
 *
 * @return void
 */
function growww_plugin_options(): void
{
    if ( !current_user_can( "manage_options" ) )  {
        wp_die( __( "You do not have sufficient permissions to access this page." ) );
    }
   
    $translation = get_option('growww-i18n') ?: ['domains' => ['growww-plugin']];
    $domains = !empty($translation['domains']) ? implode(',', $translation['domains']) : '';
    ?>
    <form method="post" action="<?php echo admin_url('options-general.php?page=growww'); ?>">

        <input type="hidden" name="action" value="update_growww_settings" />

        <h3><?php _e("Growww licentiecode", "growww-plugin"); ?></h3>
        <p>
            <label><?php _e("licentiecode:", ""); ?></label>
            <input class="" type="text" name="growww-license" value="<?php echo get_option('growww-license'); ?>" />
        </p>

        <h3><?php _e("Vertalingen", "growww-plugin"); ?></h3>
        <p>
            <label>domeinen:</label>
            <input class="" type="text" name="translation[domains]" value="<?php echo esc_attr($domains); ?>" />
        </p>

        <input class="button button-primary" type="submit" value="<?php _e("Save", "growww-plugin"); ?>" />

    </form>
    <?php
}

/**
 * Update growww settings
 *
 * @return void
 */
function update_growww_settings(): void  
{

    // //Validate in the future if licence matches from dashboard

    // // Update the values
    if (isset($_POST['translation'])) {
        $translation = (array) $_POST['translation'];
        $translation['domains'] = !empty($translation['domains']) ? preg_split('/\s*,\s*/', $translation['domains']) : [];
        update_option("growww-i18n", $translation, true);
    }
    wp_redirect(admin_url('options-general.php?page=growww&status=success'));
    exit;
    
 }
