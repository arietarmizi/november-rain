<?php

namespace common\components;

use yii\base\Widget;
use yii\bootstrap4\Html;
use yii\helpers\ArrayHelper;

/**
 * Dropdown::widget([
 *     'options' => [],
 *     'items' => [
 *         [
 *             'label' => 'Level 1 - Dropdown A',
 *             'url' => '#'
 *         ]
 *     ]
 * ]);
 */

class Dropdown extends Widget
{
    public $options = [];
    public $items = [];

    public function init()
    {
        parent::init();
        Html::addCssClass($this->options, 'navbar-menu-sub');
    }

    public function run()
    {
        $is_multi = ArrayHelper::remove($this->options, 'multi', false);
        if ($this->items) {
            if ($is_multi) {
                ArrayHelper::remove($this->options, 'class');
                $this->options['is_multi'] = true;

                echo Html::beginTag('div', ['class' => 'navbar-menu-sub']);
                echo Html::beginTag('div', ['class' => 'd-lg-flex']);
                foreach ($this->items as $key => $item) {
                    $this->render_items($item, $this->options);
                }
                echo Html::endTag('div');
                echo Html::endTag('div');
            } else {
                $this->render_items($this->items, $this->options);
            }
        }
    }

    public function render_items($items, $options = [])
    {
        echo Nav::widget([
            'options' => $options,
            'items' => $items,
            'is_sub' => true
        ]);
    }
}
