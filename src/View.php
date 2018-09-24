<?php


namespace sergey_pavlov\panorama_test;


/**
 * Class View
 * @package sergey_pavlov\panorama_test
 */
class View
{

    public static $templatePath = '../src';


    /**
     * @param string $filename
     * @param array $data
     */
    public static function display($filename, $data = [])
    {
        $templateFile = self::$templatePath . DIRECTORY_SEPARATOR . $filename;

        include $templateFile;
    }

}