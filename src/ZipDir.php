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
    public function addDirectory($files, $dir)
    { // adds directory

        foreach ($files as $file) {
            $patternLength = strlen($dir . '/');
            $localName = substr($file, $patternLength);
            if (is_dir($file)) {
                $this->addDirectory($localName);
            } else {
                $this->addFile($file, $localName);
            }
        }
    }
}

