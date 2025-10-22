<?php

namespace Growww;

class Acf
{
    protected static Acf $_instance;

    /**
     * 
     * Import ACF file
     * 
     * $param string $fn (FileName)
     * @return boolean / array $notices
     * 
     */
    public function __construct()
    {
        //Add the hooks
        $this->add_option_pages();
        $this->import_option_fields();
    }

    /**
     * Include the ACF fields we need
     *
     * @return void
     */
    public function import_option_fields(): void
    {
        include GROWWW_ALGEMEEN_DIR . '/config/acf-fields.php';
    }

    /**
     * Add option pages
     *
     * @return void
     */
    public function add_option_pages(): void
    {

        if(!function_exists('acf_add_options_page')) return;        
        //Parent
        $parent = acf_add_options_page(array(
            'page_title' => 'Growww',
            'menu_title' => 'Growww',
            'menu_slug'  => 'growww',
            'icon_url'   => 'dashicons-hammer',
            'redirect'   => true,
        ));

        //Algemeen
        acf_add_options_sub_page(array(
            'page_title'  => 'Algemene thema opties',
            'menu_title'  => 'Algemeen',
            'parent_slug' => $parent['menu_slug'],
            'menu_slug'   => 'growww-algemeen',
        ));

        //Thema
        acf_add_options_sub_page(array(
            'page_title'  => 'Thema velden',
            'menu_title'  => 'Thema velden',
            'parent_slug' => $parent['menu_slug'],
            'menu_slug'   => 'growww-thema',
        ));
    }
}
