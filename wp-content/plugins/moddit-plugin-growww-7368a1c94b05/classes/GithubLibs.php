<?php
namespace Growww;
use function Growww\{get_or_growww_mkdir,get_growww_config};
use Exception,ZipArchive;

class GithubLibs
{
    protected const
        INSTALL_PATH = 'custom/includes';

    private static
        $_instance,
        $_loadedLibs = [];

    public function require($name, $dev = true) 
    {
        if (!isset(self::$_loadedLibs[$name])) {
            //Get the readable configs from github-libs.php
            $config = get_growww_config('github-libs');
            
            //Check for conf name
            if (!isset($config[$name])) throw new Exception(__CLASS__.': onbekende Github library!');
            $conf = $config[$name];
            $className = $conf['class'];

            //Setup settings
            if (!class_exists($className)) {
                $path = \ABSPATH.($conf['installPath'] ?? self::INSTALL_PATH).'/'.$name;

                //Check if path exists
                if (!is_dir($path)) {
                    //Install repo?
                    $this->install($conf['repo'], $path, $conf['zipName'] ?? 'master', $dev);
                }

                //When done require autoload
                if (is_file($path.'/vendor/autoload.php')) require_once $path.'/vendor/autoload.php';

                if (!class_exists($className)) {
                    throw new Exception("Class '$className' niet gevonden!");
                }

                //Add to stored libs
                self::$_loadedLibs[$name] = $className;
            }
        }
        return self::$_loadedLibs[$name];
    }

    public function install($repo, $destinationFolder, $zipName = 'master', $dev = false) 
    {
        //Download item in right place
        $this->download($repo, $destinationFolder, $zipName);

        //Do some terminal/ssh commands to navigate to right folder
        $dir = getcwd();
        chdir($destinationFolder);

        //Check if there's a composer.json available
        if (is_file($destinationFolder.'/composer.json')) {

            //Check if retval exists (not 127)
            exec('composer about --no-interaction', $output_lines, $return_value);

            if($return_value !== 127) {
                //Here we have composer
                exec('composer install 2>&1 --ignore-platform-reqs --no-interaction'.($dev ? '' : ' --no-dev'), $output, $retval);
            } else {
                //Here we don't
                exec('php '.\plugin_dir_path(__FILE__).'composer.phar install 2>&1 --ignore-platform-reqs --no-interaction'.($dev ? '' : ' --no-dev'), $output, $retval);
            }

            //Check if failed OR command doesn't exist (127)
            if (!is_file($destinationFolder.'/vendor/autoload.php') || $retval == 127) throw new Exception('Composer mislukt!');
        }

        //Reset dir
        chdir($dir);
        return true;
    }

    /**
     * Download function
     *
     * @param string $repo
     * @param string $destinationFolder
     * @param string $zipName
     * @return bool result
     */
    public function download($repo, $destinationFolder, $zipName = 'master') 
    {
        //Set the unzip folder of de destination & check for access
        $unzipFolder = dirname($destinationFolder);
        if (!get_or_growww_mkdir($unzipFolder) || !is_writable($unzipFolder)) throw new Exception('De map is niet schrijfbaar('.$unzipFolder.')');
        
        //Download zip
        $url = 'https://github.com/'.$repo.'/archive/'.$zipName.'.zip';
        $zipFilename = $unzipFolder.'/'.$zipName.'.zip';
        if (!copy($url, $zipFilename)) throw new Exception('Kon de ZIP niet downloaden ('.$url.')');

        //Open the archive and extract it 
        $zip = new ZipArchive();
        $zip->open($zipFilename);
        $ok = $zip->extractTo($unzipFolder);
        $zip->close();

        //Remove file
        unlink($zipFilename);

        if (!$ok) throw new Exception('Kon de ZIP niet uitpakken ('.$zipFilename.')');

        //Get zipped folder
        $zippedFolder = basename($repo).'-'.$zipName;
        if (!is_dir($unzipFolder.'/'.$zippedFolder)) throw new Exception("Verwachte map $zippedFolder niet gevonden in de ZIP");
        if (!rename($unzipFolder.'/'.$zippedFolder, $destinationFolder)) throw new Exception("Kon de map niet hernoemen naar $destinationFolder");
        return true;
    }

    public static function get_instance() 
    {
        if (!isset(self::$_instance)) self::$_instance = new self();
        return self::$_instance;
    }
}
