<?php

namespace api\assets;

use yii\web\AssetBundle;

/**
 * Main backend application asset bundle.
 */
class AppAsset extends AssetBundle {

    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/site.css',
        'css/custom.css',
        'css/glyphicons.css',
        'css/global/components.css',
        'http://fonts.googleapis.com/css?family=Raleway:400,100,200,300,500,600,800,700,900'
    ];
    public $js = [
        'js/custom.js'
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapPluginAsset'
    ];

}
