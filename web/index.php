<?php

require_once '../vendor/autoload.php';

use sergey_pavlov\panorama_test\View;
use sergey_pavlov\panorama_test\TranslitImages;

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
    $app = new TranslitImages(DATA_DIR);
    $archive_name = $app->remakeArchive($dirname, $filename);

    $fields['newname'] = $archive_name;
    $fields['log'] = TranslitImages::$log;

}
View::display('index.php', $fields);