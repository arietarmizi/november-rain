<?php

namespace common\components;

use yii\bootstrap4\Html;
use yii\bootstrap4\Nav as BootstrapNav;
use yii\helpers\ArrayHelper;

/**
 * $menuItems = [
 *     [
 *         'label' => 'Home',
 *         'url' => ['#'], 'items' => [
 *             [
 *                 'label' => 'Level 1 - Dropdown A',
 *                 'url' => '#'
 *             ]
 *         ],
 *     ],
 *     [
 *         'label' => 'Menu 1',
 *         'url' => ['#']
 *     ],
 *     [
 *         'label' => 'Menu 2',
 *         'url' => ['#'],
 *         'dropdownOptions' => [
 *             'multi' => true
 *         ],
 *         'items' => [
 *             [
 *                 ['label' => 'Level 1 - Dropdown A', 'url' => '#']
 *             ],
 *             [
 *                 ['label' => 'Level 1 - Dropdown A', 'url' => '#']
 *             ]
 *         ]
 *     ],
 * ];
 * 
 * echo Nav::widget([
 *     'options' => [
 *         'class' => 'nav navbar-menu',
 *     ],
 *     'items' => $menuItems,
 * ]);
 */

class Nav extends BootstrapNav
{
    public $dropdownClass = 'common\components\Dropdown';
    public $is_sub = false;

    public function init()
    {
        parent::init();

        $is_multi = ArrayHelper::remove($this->options, 'is_multi', false);
        if ($is_multi) ArrayHelper::remove($this->options, 'class');
    }

    public function run()
    {
        return $this->renderItems();
    }

    public function renderItem($item)
    {
        if (is_string($item)) {
            return $item;
        }
        if (!isset($item['label'])) {
            throw new InvalidConfigException("The 'label' option is required.");
        }
        $encodeLabel = isset($item['encode']) ? $item['encode'] : $this->encodeLabels;
        $label = $encodeLabel ? Html::encode($item['label']) : $item['label'];
        $options = ArrayHelper::getValue($item, 'options', []);
        $items = ArrayHelper::getValue($item, 'items');
        $url = ArrayHelper::getValue($item, 'url', '#');
        $linkOptions = ArrayHelper::getValue($item, 'linkOptions', []);
        $disabled = ArrayHelper::getValue($item, 'disabled', false);
        $active = $this->isItemActive($item);

        if (empty($items)) {
            $items = '';
        } else {
            Html::addCssClass($options, ['widget' => 'with-sub']);
            if (is_array($items)) {
                $items = $this->isChildActive($items, $active);
                $items = $this->renderDropdown($items, $item);
            }
        }

        if ($this->is_sub) {
            Html::addCssClass($options, 'nav-sub-item');
            Html::addCssClass($linkOptions, 'nav-sub-link');
        } else {
            Html::addCssClass($options, 'nav-item');
            Html::addCssClass($linkOptions, 'nav-link');
        }

        if ($disabled) {
            ArrayHelper::setValue($linkOptions, 'tabindex', '-1');
            ArrayHelper::setValue($linkOptions, 'aria-disabled', 'true');
            Html::addCssClass($linkOptions, 'disabled');
        } elseif ($this->activateItems && $active) {
            Html::addCssClass($linkOptions, 'active');
        }

        return Html::tag('li', Html::a($label, $url, $linkOptions) . $items, $options);
    }

    protected function renderDropdown($items, $parentItem)
    {
        $dropdownClass = $this->dropdownClass;
        return $dropdownClass::widget([
            'options' => ArrayHelper::getValue($parentItem, 'dropdownOptions', []),
            'items' => $items,
        ]);
    }
}
