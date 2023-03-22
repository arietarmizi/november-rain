<?php

namespace common\widgets;

use navatech\roxymce\assets\TinyMceAsset;
use yii\base\InvalidParamException;
use yii\base\Widget;
use yii\bootstrap\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\View;

class TinyMce extends Widget {

    public $model;
    public $attribute;
    public $name = 'content';
    public $value;
    public $options;
    public $clientOptions = [];
    public $action;

    public function init() {
        parent::init();
        TinyMceAsset::register($this->view);
        if ($this->id === null) {
            if ($this->model !== null) {
                if ($this->attribute === null) {
                    throw new InvalidParamException('Field "attribute" is required');
                } else {
                    $model = $this->model;
                    if (method_exists($model, 'hasAttribute') && $model->hasAttribute($this->attribute)) {
                        $classNames = explode("\\", $model::className());
                        $this->id = end($classNames) . '_' . $this->attribute;
                    } else {
                        throw new InvalidParamException('Column "' . $this->attribute . '" not found in model');
                    }
                }
            } else {
                if ($this->name === null) {
                    throw new InvalidParamException('Field "name" is required');
                } else {
                    $this->id = $this->name;
                }
            }
        }
        $this->options['id'] = $this->id;
        $this->clientOptions = ArrayHelper::merge($this->clientOptions, [
                    'selector' => '#' . $this->id,
                    'branding'=>FALSE,
                    'plugins' => [
                        'advlist autolink autosave autoresize link image lists charmap print preview hr anchor pagebreak spellchecker',
                        'searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking',
                        'table contextmenu directionality emoticons template textcolor paste fullpage textcolor colorpicker textpattern'
                    ],
                    'theme' => 'modern',
                    'toolbar1' => 'newdocument fullpage | undo redo | styleselect formatselect fontselect fontsizeselect',
                    'toolbar2' => 'print preview media | forecolor backcolor emoticons | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image media code',
                    'image_advtab' => true,
        ]);
        if ($this->action === null) {
            $this->action = Url::to(['roxymce/default']);
        }
    }

    public function run() {
        $this->view->registerJs('$(function() {
			tinyMCE.init({' . substr(Json::encode($this->clientOptions), 1, - 1) . ',"file_browser_callback": RoxyFileBrowser});
		});', View::POS_HEAD);
        $this->view->registerJs('function RoxyFileBrowser(field_name, url, type, win) {
			var roxyMce = "' . $this->action . '";
			if(roxyMce.indexOf("?") < 0) {
				roxyMce += "?type=" + type;
			}
			else {
				roxyMce += "&type=" + type;
			}
			roxyMce += "&input=" + field_name + "&value=" + win.document.getElementById(field_name).value;
			if(tinyMCE.activeEditor.settings.language) {
				roxyMce += "&langCode=" + tinyMCE.activeEditor.settings.language;
			}
			tinyMCE.activeEditor.windowManager.open({
				file          : roxyMce,
				title         : "' . (array_key_exists('title', $this->clientOptions) ? $this->clientOptions['title'] : 'File Browser') . '",
				width         : 850,
				height        : 480,
				resizable     : "yes",
				plugins       : "media",
				inline        : "yes",
				close_previous: "no"
			}, {
				window: win,
				input : field_name
			});
			return false;
		}', View::POS_HEAD);
        if ($this->model !== null) {
            return Html::activeTextarea($this->model, $this->attribute, $this->options);
        } else {
            return Html::textarea($this->name, $this->value, $this->options);
        }
    }

}
