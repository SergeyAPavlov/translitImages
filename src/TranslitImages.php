<?php
/**
 * Created by PhpStorm.
 * Date: 23.09.2018
 * Time: 23:10
 */

namespace sergey_pavlov\panorama_test;

use ZipArchive;


class TranslitImages
{

    public static $log = [];
    public $dataDir;

    /**
     * TranslitImages constructor.
     * @param string $dataDir
     */
    public function __construct($dataDir)
    {
        $this->dataDir = $dataDir;
    }


    public function remakeArchive($dirname, $filename)
    {

        $zip = new ZipArchive();
        $rename = new Rename();
        if ($zip->open($filename) === true) {
            for ($i = 0; $i < $zip->numFiles; $i++) {
                $rename->unzip($i, $zip, $dirname);
            }
            $zip->close();
            unlink($filename);
        }

        $round = new GoRound();
        $files = $round->globAllFiles($dirname . '/*');

        $renameTable = $rename->localizeTable($dirname . '/');
        foreach ($files as $file) {
            if (pathinfo($file, PATHINFO_EXTENSION) == 'html') {
                $content = file_get_contents($file);
                if ($content) {
                    $newHtm = Images::replaceSrc($content, $renameTable);
                    $puts = TranslitImages::putContents($file, $newHtm);
                    if (!$puts) {
                        self::$log[] = 'putContents error: file ' . $newHtm;
                    }
                }
            }
        }

        $newZip = new ZipDir();

        $archive_name = uniqid() . "_.zip";
        if ($newZip->open($archive_name, ZipArchive::CREATE) === true) {
            $files = $round->globAllFiles($dirname . '/*');
            $newZip->addDirectory($files, $dirname);
            $close = $newZip->close();
            if (!$close) {
                self::$log[] = 'zip close error: file ' . $archive_name;
            }
        }
        return $archive_name;

    }

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