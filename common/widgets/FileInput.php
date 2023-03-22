<?php
/**
 * Created by PhpStorm.
 * User: Nadzif Glovory
 * Date: 2/17/2020
 * Time: 9:34 AM
 */

namespace common\widgets;


class FileInput extends \kartik\file\FileInput
{
    public $pluginOptions = [
        'showPreview' => true,
        'showCaption' => true,
        'showRemove'  => true,
        'showUpload'  => false
    ];
}