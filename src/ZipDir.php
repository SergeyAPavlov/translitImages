<?php
/**
 * Created by PhpStorm.
 * Date: 22.09.2018
 * Time: 19:50
 */

namespace sergey_pavlov\panorama_test;


/**
 * Class zipDir
 * @package sergey_pavlov\panorama_test
 */
class zipDir extends \ZipArchive
{

    /**
     * @param $dir
     */
    public function addDirectory($dir)
    { // adds directory
        foreach (glob($dir . '/*') as $file) {
            if (is_dir($file)) {
                $this->addDirectory($file);
            } else {
                $this->addFile($file);
            }
        }
    }
}

