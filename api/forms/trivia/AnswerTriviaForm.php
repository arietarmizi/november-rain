<?php

namespace api\forms\trivia;


use api\components\BaseForm;
use Carbon\Carbon;
use common\helpers\CoordinateHelper;
use common\models\Trivia;
use common\models\TriviaAnswer;
use common\models\TriviaQuestion;
use common\models\User;
use common\models\UserPoint;
use common\models\UserTriviaAnswer;
use common\models\UserTriviaLog;
use yii\helpers\ArrayHelper;

class AnswerTriviaForm extends BaseForm
{

    public $triviaQuestionId;
    public $triviaAnswerId;
    public $latitude;
    public $longitude;

    /** @var User */
    private $_user;

    /** @var TriviaQuestion */
    private $_triviaQuestion;
    /** @var Trivia */
    private $_trivia;
    /** @var TriviaAnswer */
    private $_triviaAnswer;


    /** @var double */
    private $_answerScore = 0.0;
    /** @var double */
    private $_foundScore = 0.0;
    /** @var double */
    private $_bonusScore = 0.0;
    /** @var double */
    private $_totalScore = 0.0;
    /** @var bool */
    private $_triviaDone = false;

    /** @var bool */
    private $_alreadyFound = false;

    public function rules()
    {
        return [
            [['triviaQuestionId', 'triviaAnswerId', 'latitude', 'longitude'], 'required'],
            ['triviaQuestionId', 'validateQuestion'],
            ['triviaAnswerId', 'validateAnswer'],
            [['latitude', 'longitude'], 'double'],
            ['latitude', 'validateRange'],
        ];
    }

    public function validateQuestion($attribute, $param = [])
    {
        $this->_user = \Yii::$app->user->identity;

        $this->_triviaQuestion = TriviaQuestion::find()
            ->joinWith(['trivia'])
            ->where([
                TriviaQuestion::tableName() . '.id' => $this->triviaQuestionId,
            ])
            ->one();

        if (!$this->_triviaQuestion) {
            $this->addError($attribute, \Yii::t('app', 'Question not found'));
        } else {
            $this->_trivia = $this->_triviaQuestion->trivia;

            if ($this->_triviaQuestion->status != TriviaQuestion::STATUS_ACTIVE) {
                $this->addError($attribute, \Yii::t('app', 'Question status currently {status}', [
                    'status' => ArrayHelper::getValue(TriviaQuestion::statuses(), $this->_triviaQuestion->status)
                ]));
            }

            $now = Carbon::now()->format('Y-m-d H:i:s');

            if ($this->_trivia->status != Trivia::STATUS_ACTIVE) {
                $this->addError($attribute, \Yii::t('app', 'Trivia status currently {status}', [
                    'status' => ArrayHelper::getValue(Trivia::statuses(), $this->_trivia->status)
                ]));
            }

            if ($now < $this->_trivia->appearedAt || $now > $this->_trivia->disappearedAt) {
                $this->addError($attribute, \Yii::t('app', 'Invalid trivia time'));
            }
        }
    }


    public function validateRange($attribute, $param = [])
    {
        if ($this->_triviaQuestion) {
            $latFrom = $this->latitude;
            $lonFrom = $this->longitude;
            $latTo   = $this->_trivia->latitude;
            $lonTo   = $this->_trivia->longitude;

            $distance = 10000;

            if (!CoordinateHelper::inRange($latFrom, $lonFrom, $latTo, $lonTo, $distance)) {
                $this->addError($attribute, \Yii::t('app', 'Cannot answer further than {distance} meters', [
                    'distance' => $distance
                ]));
            }
        }
    }

    public function validateAnswer($attribute, $param = [])
    {
        $this->_alreadyFound = UserTriviaLog::find()
            ->where([
                'userId'   => $this->_user->id,
                'triviaId' => $this->_triviaQuestion->triviaId,
                'action'   => UserTriviaLog::ACTION_FOUND,
                'status'   => UserTriviaLog::STATUS_ACTIVE
            ])
            ->exists();

        $isAnswered = UserTriviaAnswer::find()
            ->where([
                'userId'           => $this->_user->id,
                'triviaQuestionId' => $this->triviaQuestionId,
            ])
            ->exists();

        if ($isAnswered) {
            $this->addError($attribute, \Yii::t('app', 'This question already answered'));
        }

        $this->_triviaAnswer = TriviaAnswer::find()
            ->where([
                'id'               => $this->triviaAnswerId,
                'triviaQuestionId' => $this->triviaQuestionId,
                'status'           => TriviaAnswer::STATUS_ACTIVE
            ])
            ->one();

        if (!$this->_triviaAnswer) {
            $this->addError($attribute, \Yii::t('app', 'Invalid answer'));
        }
    }

    public function submit()
    {
        $success = true;
        if ($this->_triviaQuestion && $this->_triviaAnswer) {
            $userAnswer                   = new UserTriviaAnswer();
            $userAnswer->userId           = $this->_user->id;
            $userAnswer->triviaQuestionId = $this->_triviaQuestion->id;
            $userAnswer->triviaAnswerId   = $this->_triviaAnswer->id;
            $userAnswer->score            = $this->_triviaAnswer->score;
            $success                      &= $userAnswer->save();

            if ($userAnswer->score > 0) {
                $userPoint            = new UserPoint();
                $userPoint->userId    = $this->_user->id;
                $userPoint->code      = 'TA' . date('ymdHis');
                $userPoint->reference = UserPoint::REFERENCE_TRIVIA_ANSWER;
                $userPoint->refId     = $userAnswer->id;
                $userPoint->point     = $userAnswer->score;
                $success              &= $userPoint->save();

                $this->_answerScore += $userAnswer->score;
                $this->_totalScore  += $userAnswer->score;
            }

            if (!$this->_alreadyFound) {
                $triviaFoundLog           = new UserTriviaLog();
                $triviaFoundLog->userId   = $this->_user->id;
                $triviaFoundLog->triviaId = $this->_triviaQuestion->triviaId;
                $triviaFoundLog->action   = UserTriviaLog::ACTION_FOUND;
                $triviaFoundLog->score    = $this->_triviaQuestion->trivia->scoreFound;
                $success                  &= $triviaFoundLog->save();

                if ($triviaFoundLog->score > 0) {
                    $userPoint            = new UserPoint();
                    $userPoint->userId    = $this->_user->id;
                    $userPoint->code      = 'TF' . date('ymdHis');
                    $userPoint->reference = UserPoint::REFERENCE_TRIVIA_FOUND;
                    $userPoint->refId     = $triviaFoundLog->id;
                    $userPoint->point     = $triviaFoundLog->score;
                    $success              &= $userPoint->save();

                    $this->_foundScore += $triviaFoundLog->score;
                    $this->_totalScore += $triviaFoundLog->score;
                }
            }

            if ($success) {
                $answeredQuestion = UserTriviaAnswer::find()
                    ->select(['triviaQuestionId'])
                    ->where([
                        UserTriviaAnswer::tableName() . '.userId' => $this->_user->id,
                        UserTriviaAnswer::tableName() . '.status' => UserTriviaAnswer::STATUS_ACTIVE,
                        Trivia::tableName() . '.id'               => $this->_triviaQuestion->triviaId,
                    ])
                    ->joinWith(['triviaQuestion', 'triviaQuestion.trivia'])
                    ->asArray()
                    ->all();

                $answeredQuestionIds = ArrayHelper::getColumn($answeredQuestion, 'triviaQuestionId');

                if ($answeredQuestionIds) {
                    $remainingQuestion = TriviaQuestion::find()
                        ->where([
                            TriviaQuestion::tableName() . '.triviaId' => $this->_triviaQuestion->triviaId,
                            TriviaQuestion::tableName() . '.status'   => TriviaQuestion::STATUS_ACTIVE
                        ])
                        ->andWhere(['not in', TriviaQuestion::tableName() . '.id', $answeredQuestionIds])
                        ->exists();

                    if (!$remainingQuestion) {
                        $triviaLog           = new UserTriviaLog();
                        $triviaLog->userId   = $this->_user->id;
                        $triviaLog->triviaId = $this->_triviaQuestion->triviaId;
                        $triviaLog->action   = UserTriviaLog::ACTION_DONE;
                        $triviaLog->score    = $this->_triviaQuestion->trivia->scoreBonus;
                        $success             &= $triviaLog->save();

                        if ($triviaLog->score > 0) {
                            $bonusPoint            = new UserPoint();
                            $bonusPoint->userId    = $this->_user->id;
                            $bonusPoint->code      = 'TB' . date('ymdHis');
                            $bonusPoint->reference = UserPoint::REFERENCE_TRIVIA_BONUS;
                            $bonusPoint->refId     = $triviaLog->id;
                            $bonusPoint->point     = $triviaLog->score;
                            $success               &= $bonusPoint->save();

                            $this->_bonusScore += $triviaLog->score;
                            $this->_totalScore += $triviaLog->score;
                        }

                        $this->_triviaDone = true;
                    }
                }
            }
        }

        return $success;
    }

    public function response()
    {
        return [
            'question'    => $this->_triviaQuestion->question,
            'answer'      => $this->_triviaAnswer->answer,
            'answerScore' => $this->_answerScore,
            'foundScore'  => $this->_foundScore,
            'bonusScore'  => $this->_bonusScore,
            'totalScore'  => $this->_totalScore,
            'isCompleted' => $this->_triviaDone
        ];
    }
}