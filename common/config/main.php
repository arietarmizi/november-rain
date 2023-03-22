<?php

return [
    'aliases'    => [
        '@bower'  => '@vendor/bower-asset',
        '@npm'    => '@vendor/npm-asset',
        '@nadzif' => '@vendor/nadzif',
    ],
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'timeZone'   => 'Asia/Jakarta',
    'components' => [
        'formatter'   => [
            'defaultTimeZone'        => 'Asia/Jakarta',
            'dateFormat'             => 'dd MMMM yyyy',
            'decimalSeparator'       => ',',
            'thousandSeparator'      => '.',
            'currencyCode'           => 'IDR',
            'numberFormatterOptions' => [
                7 => 0,
                6 => 0,
                2 => 0
            ],
        ],
        'cache'       => [
            'class' => 'yii\caching\FileCache',
        ],
    ],
];
