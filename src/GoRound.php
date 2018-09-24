<?php
/**
 * Created by PhpStorm.
 * Date: 22.09.2018
 * Time: 17:00
 */

namespace sergey_pavlov\panorama_test;


/**
 * Подготовка списка файлов в директории
 * Class GoRound
 * @package sergey_pavlov\panorama_test
 */
class GoRound
{


    /**
     * @param string $pattern
     * @return string[]
     */
    public static function globAllFiles($pattern)
    {
        $files = self::globRecursive($pattern, GLOB_MARK);
        $res = [];
        foreach ($files as $file) {
            if ($file[strlen($file) - 1] != DIRECTORY_SEPARATOR) {
                $res[] = $file;
            }
        }
        return $res;
    }


    /**
     * @param string $pattern
     * @param int $flags
     * @return string[]
     */
    public static function globRecursive($pattern, $flags = 0)
    {
        $files = glob($pattern, $flags);

        foreach (glob(dirname($pattern) . '/*',
            GLOB_ONLYDIR | GLOB_NOSORT) as $dir) {
            $files = array_merge($files, self::globRecursive
            ($dir . '/' . basename($pattern), $flags));
        }

        return $files;
    }

}