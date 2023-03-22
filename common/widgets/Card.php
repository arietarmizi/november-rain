<?php
/**
 * Created by PhpStorm.
 * User: Nadzif Glovory
 * Date: 5/14/2018
 * Time: 2:08 PM
 */

namespace common\widgets;


use yii\base\Widget;
use yii\helpers\Html;

class Card extends Widget
{
    const TEXT_POSITION_LEFT   = 'text-left';
    const TEXT_POSITION_RIGHT  = 'text-right';
    const TEXT_POSITION_CENTER = 'text-center';

    public $title;
    public $titleHeading  = 'h6';
    public $textPosition  = false;
    public $options       = ['class' => 'card card-bd-0'];
    public $headerOptions = ['class' => 'card-header'];
    public $footerOptions = ['class' => 'card-footer'];
    public $buttons       = [];

    public function init()
    {

        ob_start();
        ob_implicit_flush(false);
    }

    public function run()
    {
        $content = ob_get_clean();

        echo Html::beginTag('div', $this->options);

        if (isset($this->title)) {
            $title = Html::tag($this->titleHeading, $this->title,
                ['class' => 'mg-b-0 tx-14 tx-inverse']);
            echo Html::tag('div', $title, $this->headerOptions);
        }

        echo Html::tag('div', $content, ['class' => 'card-body']);


        if ($this->buttons) {
            echo Html::tag('div', implode('&nbsp;', $this->buttons),
                $this->footerOptions);
        }

        echo Html::endTag('div');
    }

}