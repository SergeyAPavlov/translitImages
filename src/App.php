<?php
/**
 * Created by PhpStorm.
 * Date: 23.09.2018
 * Time: 23:10
 */

namespace sergey_pavlov\panorama_test;


class App
{

    public static $log = [];


    /**
     * Пишет файл, если директория не существует - создает директорию
     * @param string $fileName
     * @param string $data
     * @return bool|int|null
     */
    public static function putContents($fileName, $data)
    {
        $return = false;
        try {
            $dir = dirname($fileName);
            if (!file_exists($dir)) {
                mkdir($dir, 0777, true);
            }
            if (is_dir($fileName) AND !file_exists($fileName)) {
                mkdir($fileName, 0777, true);
            }
            if (
                !is_dir($fileName)
                AND substr($fileName, -1) != DIRECTORY_SEPARATOR
                AND substr($fileName, -1) != '/'
            ) {
                $return = file_put_contents($fileName, $data, LOCK_EX);
                if ($return === false) {
                    self::$log[] = 'File ' . $fileName . ' was not written';
                    $return = null;
                }
            }

        } catch (\Throwable $t) {
            $return = null;
            self::$log[] = 'File ' . $fileName . ' was not written';
        }
        return $return;
    }


}