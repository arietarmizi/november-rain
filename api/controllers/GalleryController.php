<?php

namespace api\controllers;

use api\components\Controller;
use api\components\FormAction;
use api\config\ApiCode;
use api\filters\ContentTypeFilter;
use api\forms\gallery\CompetitionDayListForm;
use api\forms\gallery\CompetitionDetailForm;
use api\forms\gallery\CompetitionListForm;
use api\forms\gallery\CompetitionWinnerDayForm;
use api\forms\gallery\CompetitionWinnerDetailForm;
use api\forms\gallery\CompetitionWinnerForm;
use api\forms\gallery\GalleryCompetitionReplyForm;
use api\forms\gallery\GalleryCompetitionReplyListForm;
use api\forms\gallery\PhotoCompetitionForm;
use api\forms\gallery\PhotoCompetitionUpdateForm;
use Yii;

class GalleryController extends Controller
{

    public function behaviors()
    {
        $behaviors                        = parent::behaviors();
        $behaviors['content-type-filter'] = [
            'class'       => ContentTypeFilter::class,
            'contentType' => ContentTypeFilter::TYPE_APPLICATION_JSON,
            'only'        => [
                'competition-reply',
                'competition-reply-list',
                'update',
                'competition-list',
                'competition-day-list',
                'competition-winner-list',
                'competition-winner-day-list',
                'competition-detail',
                'competition-winner-detail'
            ]
        ];

        $behaviors['content-type-filter-multipart'] = [
            'class'       => ContentTypeFilter::class,
            'contentType' => ContentTypeFilter::TYPE_APPLICATION_MULTIPART,
            'only'        => [
                'create',
            ]
        ];

        return $behaviors;
    }


    public function actions()
    {
        return [
            'competition-list'            => [
                'class'          => FormAction::class,
                'formClass'      => CompetitionListForm::class,
                'messageSuccess' => \Yii::t('app', 'Load Competition list success'),
                'messageFailed'  => \Yii::t('app', 'Load Competition list failed'),
                'apiCodeSuccess' => ApiCode::USER_REGISTER_SUCCESS,
                'apiCodeFailed'  => ApiCode::USER_REGISTER_FAILED,
                'statusSuccess'  => 200,
                'statusFailed'   => 400,
            ],
            'competition-day-list'        => [
                'class'          => FormAction::class,
                'formClass'      => CompetitionDayListForm::class,
                'messageSuccess' => \Yii::t('app', 'Load Competition Day list success'),
                'messageFailed'  => \Yii::t('app', 'Load Competition Day list failed'),
                'apiCodeSuccess' => ApiCode::USER_REGISTER_SUCCESS,
                'apiCodeFailed'  => ApiCode::USER_REGISTER_FAILED,
                'statusSuccess'  => 200,
                'statusFailed'   => 400,
            ],
            'competition-detail'          => [
                'class'          => FormAction::class,
                'formClass'      => CompetitionDetailForm::class,
                'messageSuccess' => \Yii::t('app', 'Load Competition Detail success'),
                'messageFailed'  => \Yii::t('app', 'Load Competition Detail failed'),
                'apiCodeSuccess' => ApiCode::USER_REGISTER_SUCCESS,
                'apiCodeFailed'  => ApiCode::USER_REGISTER_FAILED,
                'statusSuccess'  => 200,
                'statusFailed'   => 400,
            ],
            'competition-winner-list'     => [
                'class'          => FormAction::class,
                'formClass'      => CompetitionWinnerForm::class,
                'messageSuccess' => \Yii::t('app', 'Load Competition Winner list success'),
                'messageFailed'  => \Yii::t('app', 'Load Competition Winner list failed'),
                'apiCodeSuccess' => ApiCode::USER_REGISTER_SUCCESS,
                'apiCodeFailed'  => ApiCode::USER_REGISTER_FAILED,
                'statusSuccess'  => 200,
                'statusFailed'   => 400,
            ],
            'competition-winner-day-list' => [
                'class'          => FormAction::class,
                'formClass'      => CompetitionWinnerDayForm::class,
                'messageSuccess' => \Yii::t('app', 'Load Competition Winner Day list success'),
                'messageFailed'  => \Yii::t('app', 'Load Competition Winner Day list failed'),
                'apiCodeSuccess' => ApiCode::USER_REGISTER_SUCCESS,
                'apiCodeFailed'  => ApiCode::USER_REGISTER_FAILED,
                'statusSuccess'  => 200,
                'statusFailed'   => 400,
            ],
            'competition-winner-detail'   => [
                'class'          => FormAction::class,
                'formClass'      => CompetitionWinnerDetailForm::class,
                'messageSuccess' => \Yii::t('app', 'Load Competition Winner Detail success'),
                'messageFailed'  => \Yii::t('app', 'Load Competition Winner Detail failed'),
                'apiCodeSuccess' => ApiCode::USER_REGISTER_SUCCESS,
                'apiCodeFailed'  => ApiCode::USER_REGISTER_FAILED,
                'statusSuccess'  => 200,
                'statusFailed'   => 400,
            ],
            'create'                      => [
                'class'          => FormAction::class,
                'formClass'      => PhotoCompetitionForm::class,
                'messageSuccess' => Yii::t('app', 'Create Photo Competition success'),
                'messageFailed'  => Yii::t('app', 'Create Photo Competition failed'),
                'apiCodeSuccess' => ApiCode::USER_REGISTER_SUCCESS,
                'apiCodeFailed'  => ApiCode::USER_REGISTER_FAILED,
                'statusSuccess'  => 200,
                'statusFailed'   => 400,
            ],
            'competition-reply'           => [
                'class'          => FormAction::class,
                'formClass'      => GalleryCompetitionReplyForm::class,
                'messageSuccess' => Yii::t('app', 'Reply Photo Competition success'),
                'messageFailed'  => Yii::t('app', 'Reply Photo Competition failed'),
                'apiCodeSuccess' => ApiCode::USER_REGISTER_SUCCESS,
                'apiCodeFailed'  => ApiCode::USER_REGISTER_FAILED,
                'statusSuccess'  => 200,
                'statusFailed'   => 400,
            ],
            'competition-reply-list'      => [
                'class'          => FormAction::class,
                'formClass'      => GalleryCompetitionReplyListForm::class,
                'messageSuccess' => Yii::t('app', 'Load Reply Photo Competition success'),
                'messageFailed'  => Yii::t('app', 'Load Reply Photo Competition failed'),
                'apiCodeSuccess' => ApiCode::USER_REGISTER_SUCCESS,
                'apiCodeFailed'  => ApiCode::USER_REGISTER_FAILED,
                'statusSuccess'  => 200,
                'statusFailed'   => 400,
            ],
            'update'                      => [
                'class'          => FormAction::class,
                'formClass'      => PhotoCompetitionUpdateForm::class,
                'messageSuccess' => Yii::t('app', 'Update Photo Competition success'),
                'messageFailed'  => Yii::t('app', 'Update Photo Competition failed'),
                'apiCodeSuccess' => ApiCode::USER_REGISTER_SUCCESS,
                'apiCodeFailed'  => ApiCode::USER_REGISTER_FAILED,
                'statusSuccess'  => 200,
                'statusFailed'   => 400,
            ],
        ];
    }

    protected function verbs()
    {
        return [
            'create'                      => ['post'],
            'competition-reply'           => ['post'],
            'competition-reply-list'      => ['post'],
            'update'                      => ['post'],
            'competition-list'            => ['post'],
            'competition-day-list'        => ['post'],
            'competition-winner-list'     => ['post'],
            'competition-winner-day-list' => ['post'],
            'competition-detail'          => ['post'],
            'competition-winner-detail'   => ['post']
        ];
    }

}
