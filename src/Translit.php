<?php

namespace sergey_pavlov\panorama_test;

class Translit
{

    /**
     * Преобразует строку в транслит
     * @param string $string
     * @param string $encoding
     * @return null|string
     */
    public static function convert($string, $encoding = null)
    {
        $utf = self::toUtf($string, $encoding);
        if (!$utf) {
            return $utf;
        }
        return strtr($utf, self::tableConversion());
    }


    /**
     * @param string $string
     * @param string $encoding
     * @return null|string
     */
    public static function toUtf($string, $encoding = null)
    {
        if (is_null($encoding)) {
            $enc = self::detectEncoding($string);
            if ($enc === false) {
                return $string;
            }
            if (is_null($enc)) {
                return null;
            }
        } else {
            $enc = $encoding;
        }

        return self::ntChainDecode($string, $enc);
    }

    /**
     * Набор допустимых в именах файлов символов
     * @return array
     */
    public static function getSymbolSet()
    {
        $set = [
            'en' => 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ',
            'ru' => 'абвгдеёжзийклмнопрстуфхцчшщъыьэюяАБВГДЕЁЖЗИЙКЛМНОПРСТУФХЦЧШЩЪЫЬЭЮЯ',
            'alt' => '0123456789-_@$&.,./\\' . chr(239)
        ];
        return $set;
    }


    /**
     * @param $key
     * @return mixed
     */
    public static function getSet($key)
    {
        $set = self::getSymbolSet();
        return $set[$key];
    }


    /**
     * Таблица преобразования в транслит
     * @return array
     */
    public static function tableConversion()
    {
        return array(
            'а' => 'a',
            'б' => 'b',
            'в' => 'v',
            'г' => 'g',
            'д' => 'd',
            'е' => 'e',
            'ё' => 'e',
            'ж' => 'j',
            'з' => 'z',
            'и' => 'i',
            'й' => 'y',
            'к' => 'k',
            'л' => 'l',
            'м' => 'm',
            'н' => 'n',
            'о' => 'o',
            'п' => 'p',
            'р' => 'r',
            'с' => 's',
            'т' => 't',
            'у' => 'u',
            'ф' => 'f',
            'х' => 'h',
            'ц' => 'c',
            'ч' => 'ch',
            'ш' => 'sh',
            'щ' => 'shch',
            'ы' => 'y',
            'э' => 'e',
            'ю' => 'yu',
            'я' => 'ya',
            'ъ' => '',
            'ь' => '',
            'А' => 'A',
            'Б' => 'B',
            'В' => 'V',
            'Г' => 'G',
            'Д' => 'D',
            'Е' => 'E',
            'Ё' => 'E',
            'Ж' => 'J',
            'З' => 'Z',
            'И' => 'I',
            'Й' => 'Y',
            'К' => 'K',
            'Л' => 'L',
            'М' => 'M',
            'Н' => 'N',
            'О' => 'O',
            'П' => 'P',
            'Р' => 'R',
            'С' => 'S',
            'Т' => 'T',
            'У' => 'U',
            'Ф' => 'F',
            'Х' => 'H',
            'Ц' => 'C',
            'Ч' => 'CH',
            'Ш' => 'SH',
            'Щ' => 'SHCH',
            'Ы' => 'Y',
            'Э' => 'E',
            'Ю' => 'YU',
            'Я' => 'YA',
            'Ъ' => '',
            'Ь' => ''
        );

    }


    /**
     * @param string $string
     * @return bool|mixed|null
     */
    public static function detectEncoding($string)
    {
        $list = array(
            'utf-8',
            'windows-1251',
            'windows-1252',
            'iso-8859-1',
            'iso-8859-15',

            'UTF-8:cp437//IGNORE;cp437:cp865//IGNORE;cp866:UTF-8//IGNORE'
        );

        $available = self::getSet('en') . self::getSet('alt');

        if (empty(trim(strtr($string, $available, str_pad('', strlen($available), ' '))))) {
            return false;
        }

        foreach ($list as $item) {
            $available = self::getSet('ru') . self::getSet('en') . self::getSet('alt');
            $decode = self::ntChainDecode($string, $item);
            if (!$decode) {
                continue;
            }
            $clear = trim(strtr($decode, $available, str_pad('', strlen($available), ' ')));

            if (empty($clear)) {
                return $item;
            }
        }
        return null;
    }


    /**
     * @param string $string
     * @param string[][] $chain
     * @return string
     */
    public static function chainDecode($string, $chain)
    {
        foreach ($chain as $pair) {
            @$string = iconv($pair[0], $pair[1], $string);
        }
        return $string;
    }


    /**
     * Создает цепочку преобразований по текстовой нотации
     * например: 'UTF-8:cp437//IGNORE;cp437:cp865//IGNORE;cp866:UTF-8//IGNORE'
     * @param string $notation
     * @return string[][]
     */
    public static function toChain($notation)
    {
        $steps = explode(';', $notation);
        $chain = [];
        foreach ($steps as $step) {
            $chain[] = explode(':', $step);
        }

        $current = 'utf-8';

        for ($line = count($chain) - 1; $line >= 0; $line--) {
            $step = $chain[$line];
            if (count($step) == 1) {
                $chain[$line] = [$step[0], $current];
            }
            $chain[$line][0] = trim($chain[$line][0]);
            $chain[$line][1] = trim($chain[$line][1]);
            $current = $chain[$line][0];
        }

        return $chain;
    }


    /**
     * Перекодирует строку по цепочке преобразований
     * @param string $string
     * @param string $notation
     * @return string
     */
    public static function ntChainDecode($string, $notation)
    {
        return self::chainDecode($string, self::toChain($notation));
    }
}