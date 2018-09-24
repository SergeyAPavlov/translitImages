<?php
/**
 * Created by PhpStorm.
 * Date: 22.09.2018
 * Time: 17:34
 */

namespace sergey_pavlov\panorama_test;


/**
 * Class Images
 * @package sergey_pavlov\panorama_test
 */
class Images
{

    /**
     * Заменяет в html-файле ссылки на изображения по таблице
     * @param string $content
     * @param string[] $table
     * @return string
     */
    public static function replaceSrc($content, $table)
    {
        $find = self::find($content);
        $images = $find[1];

        $parts = [];
        $current = 0;
        foreach ($images as $image) {
            $url = $image[0];
            $position = $image[1];
            $before = substr($content, $current, $position - $current);
            $parts[] = $before;
            $charset = self::contentCharset($content);
            if (!is_null($charset) AND !(strtolower($charset) == 'utf-8')) {
                $url = iconv($charset, 'utf-8', $url);
            }
            if (!array_key_exists($url, $table)) {
                $parts[] = $url;
            } else {
                $parts[] = $table[$url];
            }
            $current += strlen($before) + strlen($url);
        }
        $parts[] = substr($content, $current);
        return implode('', $parts);

    }

    /**
     * Найти теги img в html
     * @param string $content
     * @return mixed
     */
    public static function find($content)
    {
        preg_match_all(
            '/<img[^>]+src="?\'?([^"\']+)"?\'?[^>]*>/i',
            $content, $images,
            PREG_PATTERN_ORDER + PREG_OFFSET_CAPTURE
        );
        return $images;
    }


    /**
     * Узнать кодировку html-файла по метатегу
     * @param string $content
     * @return string|null
     */
    public static function contentCharset($content)
    {
        if (preg_match('/meta.*?charset\=([^"]*).*?\>/i', $content, $matches)) {
            $meta = $matches[0];
            $parts = explode('"', $meta);
            if (!empty($parts[1])) {
                return $parts[1];
            } else {
                return null;
            }
        } else {
            return null;
        }
    }
}