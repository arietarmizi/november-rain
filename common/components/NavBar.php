<?php

namespace common\components;

use yii\base\Widget;
use yii\bootstrap\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

/**
 * NavBar::widget([
 *     'brand_options' => [
 *         'image' => [
 *             'src' => Url::to('images/logo.png'),
 *             'class' => 'logo'
 *         ],
 *         'link' => [
 *             'href' => Url::home()
 *         ],
 *     ],
 *     'profile_options' => [
 *         'title' => 'Nama',
 *         'sub_title' => 'Jabatan',
 *         'image' => [
 *             'src' => 'https://via.placeholder.com/500'
 *         ],
 *         'items' => [
 *             ['label' => 'Home', 'url' => ['/site/index'], 'options' => ['class' => 'dropdown-item']],
 *             '<div class="dropdown-divider"></div>',
 *             ['label' => 'Home', 'url' => ['/site/index'], 'options' => ['class' => 'dropdown-item']],
 *         ],
 *     ],
 * ]);
 */
class NavBar extends Widget
{
    public $options             = [];
    public $brand_options       = [];
    public $profile_options     = [];
    public $notificationOptions = [];

    public function init()
    {
        parent::init();

        $options = ['class' => 'navbar navbar-header navbar-header-fixed'];
        echo Html::beginTag('header', $options);
        echo Html::a(Html::tag('i', null, ['data-feather' => 'menu']), '#',
            ['id' => 'mainMenuOpen', 'class' => 'burger-menu']);
        $this->render_brand();
        echo Html::beginTag('div', ['class' => 'navbar-menu-wrapper', 'id' => 'navbarMenu']);
        $this->render_brand(true);
    }

    public function render_brand($is_mobile = false)
    {
        if ($options = $this->brand_options) {
            $img_options  = ArrayHelper::remove($options, 'image', []);
            $link_options = ArrayHelper::remove($options, 'link', []);

            if (!isset($link_options['class'])) {
                Html::addCssClass($link_options, 'df-logo');
            }

            $class = $is_mobile ? 'navbar-menu-header' : 'navbar-brand';

            echo Html::beginTag('div', ['class' => $class]);
            echo Html::tag('a', Html::tag('img', null, $img_options), $link_options);
            if ($is_mobile) {
                echo Html::a(Html::tag('i', null, ['data-feather' => 'x']), '#', ['id' => 'mainMenuClose']);
            }
            echo Html::endTag('div');
        }
    }

    public function run()
    {
        echo Html::endTag('div');
        echo Html::beginTag('div', ['class' => 'navbar-right']);
        $this->renderNotification();
        $this->render_profile();
        echo Html::endTag('div');
        echo Html::endTag('header');
    }

    public function renderNotification()
    {
        if ($options = $this->notificationOptions) {
            "<div class=\"dropdown dropdown-notification\">
          <a href=\"\" class=\"dropdown-link new-indicator\" data-toggle=\"dropdown\">
            <svg xmlns=\"http://www.w3.org/2000/svg\" width=\"24\" height=\"24\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\" class=\"feather feather-bell\"><path d=\"M22 17H2a3 3 0 0 0 3-3V9a7 7 0 0 1 14 0v5a3 3 0 0 0 3 3zm-8.27 4a2 2 0 0 1-3.46 0\"></path></svg>
            <span>2</span>
          </a>
          <div class=\"dropdown-menu dropdown-menu-right\">
            <div class=\"dropdown-header\">Notifications</div>
            <a href=\"\" class=\"dropdown-item\">
              <div class=\"media\">
                <div class=\"avatar avatar-sm avatar-online\"><img src=\"../../assets/img/img6.jpg\" class=\"rounded-circle\" alt=\"\"></div>
                <div class=\"media-body mg-l-15\">
                  <p>Congratulate <strong>Socrates Itumay</strong> for work anniversaries</p>
                  <span>Mar 15 12:32pm</span>
                </div><!-- media-body -->
              </div><!-- media -->
            </a>
            <a href=\"\" class=\"dropdown-item\">
              <div class=\"media\">
                <div class=\"avatar avatar-sm avatar-online\"><img src=\"../../assets/img/img8.jpg\" class=\"rounded-circle\" alt=\"\"></div>
                <div class=\"media-body mg-l-15\">
                  <p><strong>Joyce Chua</strong> just created a new blog post</p>
                  <span>Mar 13 04:16am</span>
                </div><!-- media-body -->
              </div><!-- media -->
            </a>
            <a href=\"\" class=\"dropdown-item\">
              <div class=\"media\">
                <div class=\"avatar avatar-sm avatar-online\"><img src=\"../../assets/img/img7.jpg\" class=\"rounded-circle\" alt=\"\"></div>
                <div class=\"media-body mg-l-15\">
                  <p><strong>Althea Cabardo</strong> just created a new blog post</p>
                  <span>Mar 13 02:56am</span>
                </div><!-- media-body -->
              </div><!-- media -->
            </a>
            <a href=\"\" class=\"dropdown-item\">
              <div class=\"media\">
                <div class=\"avatar avatar-sm avatar-online\"><img src=\"../../assets/img/img9.jpg\" class=\"rounded-circle\" alt=\"\"></div>
                <div class=\"media-body mg-l-15\">
                  <p><strong>Adrian Monino</strong> added new comment on your photo</p>
                  <span>Mar 12 10:40pm</span>
                </div><!-- media-body -->
              </div><!-- media -->
            </a>
            <div class=\"dropdown-footer\"><a href=\"\">View all Notifications</a></div>
          </div><!-- dropdown-menu -->
        </div>";

            $containerOptions = ArrayHelper::getValue($options, 'containerOptions', [
                'class' => 'dropdown dropdown-notification'
            ]);

            $linkOptions = ArrayHelper::merge([
                'class'         => 'dropdown-link new-indicator',
                'data-toggle'   => 'dropdown',
                'aria-expanded' => 'true'
            ], ArrayHelper::getValue($options, 'linkOptions', []));

            $icon  = ArrayHelper::getValue($options, 'icon', 'bell');
            $items = ArrayHelper::getValue($options, 'items', []);

            $notificationIcon = Html::tag('i', false, ['data-feather' => $icon]);

            $dropdownOptions = ArrayHelper::merge([
                'class' => 'dropdown-menu dropdown-menu-right'
            ], ArrayHelper::getValue($options, 'dropdownOptions', []));

            $headerOptions = ArrayHelper::merge([
                'class' => 'dropdown-header',
                'label' => \Yii::t('app', 'Notifications')
            ], ArrayHelper::getValue($options, 'headerOptions', []));

            $footerOptions = ArrayHelper::merge([
                'class'       => 'dropdown-footer',
                'label'       => \Yii::t('app', 'View All Notifications'),
                'url'         => '#',
                'linkOptions' => []
            ], ArrayHelper::getValue($options, 'footerOptions', []));

            $itemOptions = ArrayHelper::merge([
                'link'    => ['class' => 'dropdown-item'],
                'content' => ['class' => 'media'],
                'body'    => ['class' => 'media-body'],
                'time'    => []
            ], ArrayHelper::getValue($options, 'itemOptions', []));

            $allNotification = '';
            $totalUnread     = 0;

            $unreadStatus = ArrayHelper::getValue($options, 'unreadStatus', 'unread');
            foreach ($items as $item) {
                if (ArrayHelper::getValue($item, 'status', $unreadStatus) == $unreadStatus) {
                    $totalUnread++;
                }

                $itemLinkOptions = ArrayHelper::merge(
                    ArrayHelper::getValue($itemOptions, 'link', []),
                    ArrayHelper::getValue($item, 'linkOptions', [])
                );

                $itemContentOptions = ArrayHelper::merge(
                    ArrayHelper::getValue($itemOptions, 'content', []),
                    ArrayHelper::getValue($item, 'contentOptions', [])
                );

                $itemLabel = Html::beginTag('div', $itemContentOptions);
                if ($avatar = ArrayHelper::getValue($item, 'avatar', false)) {
                    $itemLabel .= Html::beginTag('div', ['class' => 'avatar avatar-sm avatar-online']);
                    $itemLabel .= Html::img($avatar, ['class' => 'rounded-circle']);
                    $itemLabel .= Html::endTag('div');

                    $itemOptions['body']['class'] = 'media-body mg-l-15';
                }

                $itemLabel .= Html::beginTag('div', ArrayHelper::getValue($itemOptions, 'body', []));
                $itemLabel .= Html::tag('p', ArrayHelper::getValue($item, 'notification'));
                if ($time = ArrayHelper::getValue($item, 'time')) {
                    $itemLabel .= Html::tag('span', \Yii::$app->formatter->asDatetime($time),
                        ArrayHelper::getValue($itemOptions, 'time', []));
                }
                $itemLabel .= Html::endTag('div');

                $itemLabel .= Html::endTag('div');

                $url             = ArrayHelper::getValue($item, 'url', '#');
                $allNotification .= Html::a($itemLabel, Json::decode($url), $itemLinkOptions);
            }

            $itemBadge = $totalUnread ? Html::tag('span', $totalUnread) : '';

            echo Html::beginTag('div', ['class' => $containerOptions]);
            echo Html::a($notificationIcon . $itemBadge, '#', $linkOptions);

            echo Html::beginTag('div', $dropdownOptions);
            echo Html::tag('div', $headerOptions['label'], $headerOptions);

            echo $allNotification;

            $footerLabel = Html::a($footerOptions['label'], $footerOptions['url'], $footerOptions['linkOptions']);

            echo Html::tag('div', $footerLabel, $footerOptions);
            echo Html::endTag('div');

            echo Html::endTag('div');
        }
    }

    public function render_profile()
    {
        if ($options = $this->profile_options) {
            $img_options = ArrayHelper::remove($options, 'image', []);

            if (!isset($img_options['class'])) {
                Html::addCssClass($img_options, 'rounded-circle');
            }

            $img = Html::tag('img', null, $img_options);

            echo Html::beginTag('div', ['class' => 'dropdown dropdown-profile']);

            echo Html::beginTag('a',
                ['href' => '#', 'class' => 'dropdown-link', 'data-toggle' => 'dropdown', 'data-display' => 'static']);
            echo Html::tag('div', $img, ['class' => 'avatar avatar-sm']);
            echo Html::endTag('a');

            $items = ArrayHelper::remove($options, 'items', false);
            if ($items) {
                $itm   = [
                    Html::tag('div', $img, ['class' => 'avatar avatar-lg mg-b-15']),
                    Html::tag('h6', $options['title'], ['class' => 'tx-semibold mg-b-5']),
                    Html::tag('p', $options['sub_title'], ['class' => 'mg-b-25 tx-12 tx-color-03'])
                ];
                $items = ArrayHelper::merge($itm, $items);

                echo Html::beginTag('div', ['class' => 'dropdown-menu dropdown-menu-right tx-13']);
                foreach ($items as $key => $item) {
                    if (is_array($item)) {
                        $icon = ArrayHelper::getValue($item, 'icon');

                        if ($icon) {
                            $label = Html::tag('i', null, ['data-feather' => $icon]);
                        } else {
                            $label = '';
                        }

                        $label        .= ArrayHelper::remove($item, 'label');
                        $url          = ArrayHelper::getValue($item, 'url', '#');
                        $link_options = ArrayHelper::getValue($item, 'options', []);

                        echo Html::a($label, $url, $link_options);
                    } else {
                        echo $item;
                    }
                }
                echo Html::endTag('div');
            }

            echo Html::endTag('div'); //dropdown dropdown-profile
        }
    }
}
