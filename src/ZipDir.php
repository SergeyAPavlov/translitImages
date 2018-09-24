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

    public function addDirectory($dir)
    { // adds directory
        $this->addGlob($dir . '/*.*', 0, ['remove_path' => $dir]);
    }
}

