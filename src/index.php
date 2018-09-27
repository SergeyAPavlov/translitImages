<?php
/** @var $data */
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Интерфейс для перепаковки архивов с заменой русских букв в именах картинок</title>
</head>
<body>

<h4>
    <?php
    if (!empty($data['newname'])) {
        echo 'Файл для скачивания: <a href="' . $data['newname'] . '"> скачать </a>';
    }
    if (!empty($data['log'])) {
        print_r($data['log']);
    }
    ?>
</h4>
<form method="POST" action="" enctype="multipart/form-data">
    <table class="table">
        <tr>
            <td>Файл с архивом:</td>
            <td><input name="filename" type="file"></td>

        <tr>

        <tr>
            <td></td>
            <td><input name="submit" type="submit" value="Загрузить файл"></td>
        </tr>
    </table>
</form>

</body>
</html>
