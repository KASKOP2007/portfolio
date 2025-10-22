<?php

namespace Growww;

use Exception;
use ZipArchive;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;
use function Growww\get_growww_config;

class BitBucket
{
    //Setup private info
    public $plugin_folder;
    public $repo;
    public $organisation;
    public $destination;
    public $zip_location;

    //Construct all the things
    public function __construct()
    {

        //Get config details
        $bitbucket_config = get_growww_config('bitbucket');

        //Get private info from config file
        $this->repo = $bitbucket_config['repo'];
        $this->organisation = $bitbucket_config['organisation'];
        $this->plugin_folder = explode('/', substr(dirname(__FILE__), (strlen(WP_PLUGIN_DIR) + 1)))[0];
        $this->destination = WP_PLUGIN_DIR . '/' . $this->plugin_folder . '/package';
        $this->zip_location = wp_upload_dir()['basedir'];
        
        //Download the zip 
        if(get_transient('growww_bitbucket_package_created')) $this->download_repo();
    }

    /**
     * Download the repo as a zip from bitbucket
     *
     * @return void
     */
    private function download_repo()
    {

        //Check dir before entering...
        if(!is_dir($this->destination)) mkdir($this->destination);

        //Do some terminal/ssh commands to navigate to right folder
        $dir = getcwd();
        chdir($this->destination);
        $rootpath = getcwd();

        //Try to clone it from bitbucket
        try {

            //Pull the git from bit
            exec("git clone https://Mart_Vredeveld:KdD4cuEqy2LF2r8HP8Uk@bitbucket.org/" . $this->organisation . "/" . $this->repo . ".git", $output_lines, $return_value);

            //After downloading we can zip it
            $this->create_zip();

        } catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }
    }
    
    /**
     * Get the URL where the package is placed
     *
     * @return void
     */
    public function get_package_url()
    {
        //Return full path
        return $this->zip_location . '/' . $this->plugin_folder . '.zip';
    }

    /**
     * Create a zip file with all the plugin files
     *
     * @param [type] $rootpath
     * @return void
     */
    private function create_zip()
    {

        //Check if we have faulty version zip
        if(file_exists($this->zip_location . '/' . $this->plugin_folder . '.zip')) unlink($this->zip_location . '/' . $this->plugin_folder . '.zip');

        //Create zip
        $zip = new ZipArchive();
        $zip->open($this->zip_location . '/' . $this->plugin_folder . '.zip', ZipArchive::CREATE | ZipArchive::OVERWRITE);

        //Get the bitbucket created folder
        $directories = glob($this->destination . '/*' , GLOB_ONLYDIR);

        //Check for folder to do a recurse
        if (is_dir($directories[0])) {

            //Get all the files in the folder
            $directory_iterator = new RecursiveDirectoryIterator($directories[0]);
            foreach (new RecursiveIteratorIterator($directory_iterator, RecursiveIteratorIterator::LEAVES_ONLY) as $filename => $file) {

                // //Get the real and relative path for zip purposes
                $file_path = substr($filename, strlen($directories[0]) + 1);
                $relative_path = $this->plugin_folder . substr($filename, strlen($directories[0]));

                //Cut off the git and empty
                if(substr($file_path, 0, 1) == '.' || strlen($file_path) < 1 || is_dir($file)) continue;

                //Add to the zip
                $zip->addFile($file, $relative_path);

            }

            // Zip archive will be created only after closing object
            $zip->close();

            //At the end we can remove the original folder
            $this->remove_package_folder();
            
            //Set a transient so we won't be doing this EVERY load
            set_transient('growww_bitbucket_package_created', true, 60 * 60 * 24 * 7);

        }
    }
    
    /**
     * Remove the entire package folder
     *
     * @param [type] $rootpath
     * @return void
     */
    private function remove_package_folder()
    {
        //Empty the package folder
        $dir = $this->destination;

        //Check if it's a dir if so remove
        if (is_dir($dir)) {
            $it = new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS);
            $files = new RecursiveIteratorIterator(
                $it,
                RecursiveIteratorIterator::CHILD_FIRST
            );
            foreach ($files as $file) {
                if ($file->isDir()) {
                    rmdir($file->getRealPath());
                } else {
                    unlink($file->getRealPath());
                }
            }
            rmdir($dir);
        }
    }
}
