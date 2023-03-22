<?php

namespace common\assets;

use yii\web\AssetBundle;
use yii\web\YiiAsset;

class CommonAsset extends AssetBundle {
    public $sourcePath = '@common/assets/files';
    public $css = [
        'css/dashforge.css',
        'css/dashforge.dashboard.css',
        'css/dashforge.auth.css',
        'css/dashforge.profile.css',
        'css/dashforge.calendar.css',
        'lib/ionicons/css/ionicons.min.css'
    ];
    public $js = [
        'lib/feather-icons/feather.min.js',
        'lib/perfect-scrollbar/perfect-scrollbar.min.js',
        'js/dashforge.js',
    ];
    public $depends = [
        YiiAsset::class
    ];
}
