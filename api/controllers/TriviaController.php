<?php

namespace api\controllers;

use api\actions\ListAction;
use api\components\Controller;
use api\components\FormAction;
use api\components\HttpException;
use api\components\Response;
use api\config\ApiCode;
use api\filters\ContentTypeFilter;
use api\forms\trivia\AnswerTriviaForm;
use api\forms\trivia\CollectTriviaForm;
use Carbon\Carbon;
use common\models\Trivia;
use common\models\TriviaAnswer;
use common\models\TriviaQuestion;
use common\models\User;
use common\models\UserTriviaAnswer;
use common\models\UserTriviaLog;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

class TriviaController extends Controller
{

    public function behaviors()
    {
        $behaviors                        = parent::behaviors();
        $behaviors['content-type-filter'] = [
            'class'       => ContentTypeFilter::class,
            'contentType' => ContentTypeFilter::TYPE_APPLICATION_JSON,
            'only'        => ['answer', 'collect']
        ];
        return $behaviors;
    }

    public function actions()
    {
        return [
            'list'    => [
                'class'             => ListAction::class,
                'query'             => function () {
                    $completedTrivia = UserTriviaLog::find()
                        ->where([
                            'status' => UserTriviaLog::STATUS_ACTIVE,
                            'userId' => \Yii::$app->user->id,
                            'action' => UserTriviaLog::ACTION_DONE
                        ])
                        ->asArray()
                        ->all();

                    if ($completedTrivia) {
                        $completedTriviaIds = ArrayHelper::getColumn($completedTrivia, 'triviaId');
                        return Trivia::find()
                            ->where(['status' => Trivia::STATUS_ACTIVE])
                            ->andWhere(['not in', 'id', $completedTriviaIds]);
                    }

                    return Trivia::find()->where(['status' => Trivia::STATUS_ACTIVE]);
                },
                'toArrayProperties' => [
                    Trivia::class => [
                        'id',
                        'fileUrl' => function ($model) { return ArrayHelper::getValue($model, 'file.source'); },
                        'name',
                        'latitude',
                        'longitude',
                        'altitude',
                        'scoreFound',
                        'scoreBonus',
                        'appearedAt',
                        'disappearedAt',
                    ]
                ],
                'apiCodeSuccess'    => 200,
                'apiCodeFailed'     => 400,
                'successMessage'    => \Yii::t('app', 'Get trivia list success'),
            ],
            'collect' => [
                'class'          => FormAction::class,
                'formClass'      => CollectTriviaForm::class,
                'messageSuccess' => \Yii::t('app', 'Collect trivia success'),
                'messageFailed'  => \Yii::t('app', 'Collect trivia failed'),
                'apiCodeSuccess' => ApiCode::USER_REGISTER_SUCCESS,
                'apiCodeFailed'  => ApiCode::USER_REGISTER_FAILED,
                'statusSuccess'  => 200,
                'statusFailed'   => 400,
            ],
            'answer'  => [
                'class'          => FormAction::class,
                'formClass'      => AnswerTriviaForm::class,
                'messageSuccess' => \Yii::t('app', 'Answer question success'),
                'messageFailed'  => \Yii::t('app', 'Answer question failed'),
                'apiCodeSuccess' => ApiCode::USER_REGISTER_SUCCESS,
                'apiCodeFailed'  => ApiCode::USER_REGISTER_FAILED,
                'statusSuccess'  => 200,
                'statusFailed'   => 400,
            ],
        ];
    }


    public function actionReset()
    {
        UserTriviaAnswer::deleteAll();
        UserTriviaLog::deleteAll();

        $response          = new Response();
        $response->name    = 'Success';
        $response->status  = 200;
        $response->message = \Yii::t('app', 'Trivia answer and log reset success');
        $response->code    = 0;

        return $response;
    }


    public function actionQuestion($id)
    {
        /** @var User $userIdentity */
        $userIdentity = \Yii::$app->user->identity;

        $triviaId = \Yii::$app->request->get('id');

        /** @var Trivia $trivia */
        $trivia      = Trivia::find()->where(['id' => $triviaId])->one();
        $requestTime = Carbon::now()->format('Y-m-d H:i:s');

        if ($trivia) {
            if ($trivia->status == Trivia::STATUS_ACTIVE) {
                if ($requestTime < $trivia->appearedAt || $requestTime > $trivia->disappearedAt) {
                    throw new HttpException(400, \Yii::t('app', 'Invalid trivia time'));
                }
            } else {
                throw new HttpException(400, \Yii::t('app', 'Trivia status currently {status}', [
                    'status' => ArrayHelper::getValue(Trivia::statuses(), $trivia->status)
                ]));
            }
        } else {
            throw new NotFoundHttpException();
        }

        $answeredQuestion = UserTriviaAnswer::find()
            ->select(['triviaQuestionId'])
            ->where([
                UserTriviaAnswer::tableName() . '.userId' => $userIdentity->id,
                UserTriviaAnswer::tableName() . '.status' => UserTriviaAnswer::STATUS_ACTIVE,
                Trivia::tableName() . '.id'               => $trivia->id,
            ])
            ->joinWith(['triviaQuestion', 'triviaQuestion.trivia'])
            ->asArray()
            ->all();

        $answeredQuestionIds = ArrayHelper::getColumn($answeredQuestion, 'triviaQuestionId');

        $qTriviaQuestion = TriviaQuestion::find()
            ->where([
                TriviaQuestion::tableName() . '.triviaId' => $trivia->id,
                TriviaQuestion::tableName() . '.status'   => TriviaQuestion::STATUS_ACTIVE
            ]);

        if ($answeredQuestionIds) {
            $qTriviaQuestion->andWhere(['not in', TriviaQuestion::tableName() . '.id', $answeredQuestionIds]);
        }

        /** @var TriviaQuestion $triviaQuestion */
        $triviaQuestion = $qTriviaQuestion->orderBy('rand()')->one();

        if (!$triviaQuestion) {
            throw new HttpException(400, \Yii::t('app', 'There is no more question for this trivia'));
        }

        $triviaAnswers = TriviaAnswer::find()
            ->where([
                'triviaQuestionId' => $triviaQuestion->id,
                'status'           => TriviaAnswer::STATUS_ACTIVE
            ])
            ->orderBy('rand()')
            ->all();

        $response          = new Response();
        $response->name    = 'Success';
        $response->status  = 200;
        $response->message = \Yii::t('app', 'Trivia question loaded');
        $response->code    = 0;

        if (!$triviaQuestion) {
            $response->message = \Yii::t('app', 'There is no question left');
            $response->code    = 1;
        }

        $response->data = [
            'id'       => $triviaQuestion->id,
            'question' => $triviaQuestion->question,
            'choices'  => ArrayHelper::toArray($triviaAnswers, [
                TriviaAnswer::class => ['id', 'answer']
            ]),
        ];

        return $response;
    }

    protected function verbs()
    {
        return [
            'list'     => ['get'],
            'question' => ['get'],
            'collect'  => ['post'],
            'answer'   => ['post'],
            'reset'    => ['get'],
        ];
    }

}