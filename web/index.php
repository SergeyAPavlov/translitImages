<?php

require_once '../vendor/autoload.php';
use sergey_pavlov\panorama_test\Translit;
use sergey_pavlov\panorama_test\Images;
use sergey_pavlov\panorama_test\GoRound;
use sergey_pavlov\panorama_test\ZipDir;
use sergey_pavlov\panorama_test\View;
use sergey_pavlov\panorama_test\Rename;
use sergey_pavlov\panorama_test\App;

const DATA_DIR = '../data';
setlocale(LC_ALL, 'en_US.UTF-8');

$fields = [];

if (!empty($_REQUEST['submit']) AND !empty($_FILES)) {
    $filename = DATA_DIR . DIRECTORY_SEPARATOR . $_FILES["filename"]["name"];
    $dirname = DATA_DIR . DIRECTORY_SEPARATOR . uniqid();
    mkdir($dirname);

    move_uploaded_file(
        $_FILES["filename"]["tmp_name"],
        $filename
    );

    $app = new App();
    $zip = new ZipArchive();
    $rename = new Rename();
    if ($zip->open($filename) === true) {
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $rename->unzip($i, $zip, $dirname);
        }
        $zip->close();
        //unlink($filename);
    }

    $round = new GoRound();
    $files = $round->globAllFiles($dirname . '/*');

    $renameTable = $rename->localizeTable($dirname . '/');
    foreach ($files as $file) {
        if (pathinfo($file, PATHINFO_EXTENSION) == 'html') {
            $content = file_get_contents($file);
            if ($content) {
                $newHtm = Images::replaceSrc($content, $renameTable);
                $puts = App::putContents($file, $newHtm);
            }
        }
    }

    $newZip = new ZipDir();

    $archive_name = uniqid() . "_.zip";
    if ($newZip->open($archive_name, ZipArchive::CREATE) === true) {
        // todo: в addDirectory  надо бы заменить addFile на addFromString, а то бардак с путями
        $newZip->addDirectory($dirname);
        $close = $newZip->close();
    }

    $fields['newname'] = $archive_name;

}
View::display('index.php', $fields);