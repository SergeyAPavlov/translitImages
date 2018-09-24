<?php
/**
 * Created by PhpStorm.
 * Date: 22.09.2018
 * Time: 15:52
 */

namespace sergey_pavlov\panorama_test;

use ZipArchive;


/**
 * Class Rename
 * @package sergey_pavlov\panorama_test
 */
class Rename
{
    public $namesTable = [];

    /**
     * Переименовывает файл в транслит
     * @param $name
     * @return bool|null // true - переименовали; false - переименование не требуется; null - ошибка
     */
    public function file($name)
    {
        $convert = Translit::convert($name);
        if (is_null($convert)) {
            return null;
        }
        $newName = strtolower($convert);
        if ($newName == $name) {
            return false;
        }
        if (file_exists($newName)) {
            $newName = self::addPreSuffix($newName, uniqid());
        }

        if (rename($name, $newName)) {
            $namesTable[$name] = $newName;
            return true;
        } else {
            return null;
        }

    }

    /**
     * Извлекает файл из архива с переименованием в транслит
     * @param int $zipIndex
     * @param ZipArchive $zip
     * @param string $unzipDir
     * @return null|string
     */
    public function unzip($zipIndex, ZipArchive $zip, $unzipDir)
    {
        $name = $unzipDir . DIRECTORY_SEPARATOR . $zip->getNameIndex($zipIndex);
        $convert = Translit::convert($name);
        if (is_null($convert)) {
            return null;
        }

        $newName = strtolower($convert);

        if (file_exists($newName)) {
            $newName = self::addPreSuffix($newName, uniqid());
        }

        $text = $zip->getFromIndex($zipIndex);
        $put = App::putContents($newName, $text);

        if (!($newName == $name) AND $put) {
            $this->namesTable[$name] = $newName;
        }

        if ($put) {
            return $newName;
        } else {
            return null;
        }

    }


    /**
     * Добавляет уникальный суффикс к имени файла
     * @param string $filename
     * @param string $preSuffix
     * @return string
     */
    public static function addPreSuffix($filename, $preSuffix)
    {
        $info = pathinfo($filename);
        $extension = $info['extension'];
        if (empty($extension)) {
            return $filename . $preSuffix;
        } else {
            return (substr($filename, 0, -strlen($extension)) . $preSuffix . '.' . $extension);
        }
    }


    /**
     * Переводит таблицу переименований в utf и заменяет разделители на /
     * @param string $dataDir
     * @return string[]
     */
    public function localizeTable($dataDir)
    {
        $newTable = [];
        $len = strlen($dataDir);
        foreach ($this->namesTable as $old => $new) {
            $newTable[Translit::toUtf(self::fixSeparators(substr($old, $len)))] = self::fixSeparators(substr($new,
                $len));
        }
        return $newTable;
    }


    /**
     * Переводит таблицу переименований в указанную кодировку
     * @param string[] $table
     * @param string $encoding
     * @return string[]
     */
    public static function convertTable($table, $encoding)
    {
        $newTable = [];
        foreach ($table as $old => $new) {
            $newTable[iconv('utf-8', $encoding, $old)] = $new;
        }
        return $newTable;
    }


    /**
     * @param $string
     * @return mixed
     */
    public static function fixSeparators($string)
    {
        return str_replace('\\', '/', $string);
    }

}