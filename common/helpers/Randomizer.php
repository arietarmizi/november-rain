<?php

namespace common\helpers;

use Carbon\Carbon;
use Ramsey\Uuid\Uuid;

class Randomizer
{
    public static function uuid()
    {
        return Uuid::uuid4();
    }

    public static function valueIn($array)
    {
        return $array[rand(0, sizeof($array) - 1)];
    }

    public static function futureDateTime($min = 1, $max = 3600, $addMethod = 'minutes')
    {
        if ($addMethod == 'days') {
            return Carbon::now()->addDays(rand($min, $max))->format(\DateTime::ATOM);
        }
        return Carbon::now()->addMinutes(rand($min, $max))->format(\DateTime::ATOM);
    }

    public static function pastDateTime($min = 1, $max = 3600, $addMethod = 'minutes')
    {
        if ($addMethod == 'days') {
            return Carbon::now()->subDays(rand($min, $max))->format(\DateTime::ATOM);
        }
        return Carbon::now()->subMinutes(rand($min, $max))->format(\DateTime::ATOM);
    }

    public static function email($domain = [])
    {
        if (!$domain) {
            $domain = ['mail.com', 'domain.com'];
        }

        return str_replace(' ', '', strtolower(self::name(1, '', '@' . self::valueIn($domain))));
    }

    public static function licensePlateNumber()
    {
        $prefixes = ['b', 'd', 'f', 'g', 'h', 'l', 'm', 'n', 'p', 'r', 's', 'w', 'a', 'e'];
        $prefix   = $prefixes[rand(0, sizeof($prefixes) - 1)];
        $suffix   = self::string(1, 3);

        return strtoupper($prefix . ' ' . rand(1, 9999) . ' ' . $suffix);
    }

    public static function sequenceString($string, $length = 4, $appendChar = '0')
    {
        return str_pad($string, $length, '0', STR_PAD_LEFT);
    }

    public static function code()
    {
        return strtoupper(self::string(2, 2)) .
            rand(18, 23) .
            self::sequenceString(rand(1, 12), 2) .
            self::sequenceString(rand(1, 30), 2) .
            self::sequenceString(rand(1, 99));
    }

    public static function name($wordsLength = 2, $prefix = '', $suffix = '', $ucWords = true)
    {
        $co   = ['b', 'd', 'f','k', 'l', 'n', 's', 't'];
        $vo   = ['a', 'u', 'e'];
        $rareChar = ['h', 'r', 'o', 'i'];

        $coLength   = count($co);
        $voLength   = count($vo);
        $rareCharLength = count($rareChar);

        $arrNames  = [];

        if ($prefix) {
            $arrNames[] = $prefix;
        }

        for ($iD = 0; $iD < $wordsLength; $iD++) {
            $nameLength   = rand(4, 9);
            $selectedName = '';
            $latestVar = rand(0, 1);

            for ($iC = 0; $iC < $nameLength; $iC++) {
                if ($latestVar == 0) {
                    $selectedName .= $co[rand(1, $coLength) - 1];
                    if (rand(0, 100) % 7 == 0 && $iC!= $nameLength-1) {
                        $selectedName .= $rareChar[rand(1, $rareCharLength) - 1];
                    }
                    $latestVar = 1;
                } else {
                    $selectedName .= $vo[rand(1, $voLength) - 1];
                    $latestVar    = 0;
                }
            }

            $arrNames[] = $selectedName;
        }

        if ($suffix) {
            $arrNames[] = $suffix;
        }

        $generatedName = implode(' ', $arrNames);

        return $ucWords ? ucwords($generatedName) : $generatedName;
    }

    public static function string($min = 1, $max = 4)
    {
        $chars = [
            'b',
            'c',
            'd',
            'f',
            'g',
            'h',
            'j',
            'k',
            'l',
            'm',
            'n',
            'p',
            'q',
            'r',
            's',
            't',
            'v',
            'w',
            'x',
            'y',
            'z',
            'a',
            'i',
            'u',
            'e',
            'o'
        ];

        $rand   = rand($min, $max);
        $string = '';

        for ($iC = $rand - 1; $iC >= 0; $iC--) {
            $char   = $chars[rand(0, sizeof($chars) - 1)];
            $string .= $char;
        }

        return $string;
    }

    public static function phoneNumber()
    {
        return '08' . (string)rand(11111111, 99999999999);
    }

    public static function double($min, $max)
    {
        $randomDouble = rand(11111111, 99999999) / 100000000;
        return (double)(rand($min, $max) + $randomDouble);
    }

    public static function address()
    {
        $address = self::name(rand(2, 3), 'Jalan');

        if (rand(0, 9) %3 ==0) {
            $address .= ' '.self::valueIn(['I', 'II', 'III', 'IV', 'V']);
        }

        $address .= ' No. ' . rand(1, 999);
        if (rand(0, 1)) {
            $address .= self::valueIn(['A', 'B', 'C']);
        }
        return $address;
    }

    public static function imageUrl($width = 200, $height = 300)
    {
        return 'https://picsum.photos/id/' . rand(1, 1000) . '/' . $width . '/' . $height;
    }

    public static function imageUrls($min = 1, $max = 3, $width = 200, $height = 300)
    {
        $urls = [];
        for ($iC = 0; $iC < rand($min, $max); $iC++) {
            $urls[] = self::imageUrl($width, $height);
        }

        return $urls;
    }

}