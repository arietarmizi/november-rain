<?php

use Ramsey\Uuid\Uuid;

$uuidPattern     = trim(Uuid::VALID_PATTERN, '^$');
$alphaNumPattern = '\w+(-\w+)*';

return [
    ''                                       => 'site/index',
    'android-version'                        => 'site/android-version',
    //scan
    'scan/presence'                          => 'scan/presence',
    'scan/activity'                          => 'scan/activity',
    //auth
    'auth/register'                          => 'auth/register',
    'auth/login'                             => 'auth/login',
    'auth/login-verify-resend'               => 'auth/login-verify-resend',
    'auth/logout'                            => 'auth/logout',
    'account/point'                          => 'account/point',
    'account/profile'                        => 'account/profile',
    'account/change-password'                => 'account/change-password',
    'account/edit-profile'                   => 'account/edit-profile',
    'account/edit-profile-picture'           => 'account/edit-profile-picture',
    'account/add-contact'                    => 'account/add-contact',
    'account/remove-contact/<id:\w+(-\w+)*>' => 'account/remove-contact',
    'auth/change-phone-number'               => 'auth/change-phone-number',
    'auth/forgot-password'                   => 'auth/forgot-password',
    'auth/verify-reset-token'                => 'auth/verify-reset-token',
    'faq'                                    => 'faq/list',

    //conference
    'conference/list'                        => 'conference/list',
    'conference/detail'                      => 'conference/detail',
    //gallery
    'gallery/competition-list'               => 'gallery/competition-list',
    'gallery/competition-day-list'           => 'gallery/competition-day-list',
    'gallery/competition-detail'             => 'gallery/competition-detail',
    'gallery/competition-winner-list'        => 'gallery/competition-winner-list',
    'gallery/competition-winner-day-list'    => 'gallery/competition-winner-day-list',
    'gallery/competition-winner-detail'      => 'gallery/competition-winner-list',
    'gallery/create'                         => 'gallery/create',
    'gallery/update/<id:\w+(-\w+)*>'         => 'gallery/update',
    'gallery/competition-reply'              => 'gallery/competition-reply',
    'gallery/competition-reply-list'         => 'gallery/competition-reply-list',

    'news/list'                   => 'news/list',
    'news/detail/<id:\w+(-\w+)*>' => 'details/news',

    'ads/list'                   => 'ads/list',
    'ads/detail/<id:\w+(-\w+)*>' => 'details/ads',

    'inbox/list'                   => 'inbox/list',
    'inbox/search'                 => 'inbox/search',
    'inbox/change-status'          => 'inbox/change-status',
    'inbox/detail/<id:\w+(-\w+)*>' => 'details/inbox',

    'schedule/list'     => 'schedule/list',
    'schedule/day-list' => 'schedule/day-list',
    'schedule/detail'   => 'schedule/detail',

    'select-event/list'                   => 'select-event/list',
    'select-event/detail/<id:\w+(-\w+)*>' => 'select-event/detail',

    'event-question/create' => 'event-question/create',
    'event-question/list'   => 'event-question/list',
    'event-question/detail' => 'event-question/detail',

    'reward/list'                   => 'reward/list',
    'reward/detail/<id:\w+(-\w+)*>' => 'details/reward',

    'banner/list'                   => 'banner/list',
    'banner/detail/<id:\w+(-\w+)*>' => 'details/banner',

    'point-history'  => 'point-history',
    'point-klasemen' => 'top-user/list',
    'data/region'    => 'data/region',

    'user/point' => 'user/point',

    'trivia/question/<id:\w+(-\w+)*>' => 'trivia/question',
    'trivia/list'                     => 'trivia/list',
    'trivia/collect'                  => 'trivia/collect',
    'trivia/answer'                   => 'trivia/answer',
    'trivia/reset'                    => 'trivia/reset',
];