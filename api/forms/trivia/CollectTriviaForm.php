<?php

namespace api\forms\trivia;

use api\components\BaseForm;
use Carbon\Carbon;
use common\helpers\CoordinateHelper;
use common\models\Trivia;
use common\models\User;
use common\models\UserPoint;
use common\models\UserTriviaLog;
use yii\helpers\ArrayHelper;

class CollectTriviaForm extends BaseForm
{

    public $triviaId;
    public $latitude;
    public $longitude;

    /** @var Trivia */
    private $_trivia;
    /** @var User */
    private $_user;

    /** @var bool */
    private $_alreadyFound = false;
    /** @var double */
    private $_obtainedScore = 0.0;

    public function rules()
    {
        return [
            [['triviaId', 'latitude', 'longitude'], 'required'],
            ['triviaId', 'validateTrivia'],
            ['triviaId', 'checkAvailability'],
            [['latitude', 'longitude'], 'double'],
            ['latitude', 'validateRange'],
        ];
    }

    public function validateTrivia($attribute, $param = [])
    {
        $this->_user = \Yii::$app->user->identity;

        $this->_trivia = Trivia::find()
            ->where([Trivia::tableName() . '.id' => $this->$attribute])
            ->one();

        if (!$this->_trivia) {
            $this->addError($attribute, \Yii::t('app', 'Trivia not found'));
        } else {
            if ($this->_trivia->status != Trivia::STATUS_ACTIVE) {
                $this->addError($attribute, \Yii::t('app', 'Trivia status currently {status}', [
                    'status' => ArrayHelper::getValue(Trivia::statuses(), $this->_trivia->status)
                ]));
            }

            $now = Carbon::now()->format('Y-m-d H:i:s');

            if ($now < $this->_trivia->appearedAt || $now > $this->_trivia->disappearedAt) {
                $this->addError($attribute, \Yii::t('app', 'Invalid trivia time'));
            }
        }
    }

    public function checkAvailability($attribute, $param = [])
    {
        $this->_alreadyFound = UserTriviaLog::find()
            ->where([
                'userId'   => $this->_user->id,
                'triviaId' => $this->$attribute,
                'action'   => UserTriviaLog::ACTION_FOUND,
                'status'   => UserTriviaLog::STATUS_ACTIVE
            ])
            ->exists();
    }

    public function validateRange($attribute, $param = [])
    {
        if ($this->_trivia) {
            $latFrom = $this->latitude;
            $lonFrom = $this->longitude;
            $latTo   = $this->_trivia->latitude;
            $lonTo   = $this->_trivia->longitude;

            $distance = 10000;

            if (!CoordinateHelper::inRange($latFrom, $lonFrom, $latTo, $lonTo, $distance)) {
                $this->addError($attribute, \Yii::t('app', 'Cannot collect further than {distance} meters', [
                    'distance' => $distance
                ]));
            }
        }
    }

    public function submit()
    {
        $success = true;

        if (!$this->_alreadyFound) {
            $triviaFoundLog           = new UserTriviaLog();
            $triviaFoundLog->userId   = $this->_user->id;
            $triviaFoundLog->triviaId = $this->triviaId;
            $triviaFoundLog->action   = UserTriviaLog::ACTION_FOUND;
            $triviaFoundLog->score    = $this->_trivia->scoreFound;
            $success                  &= $triviaFoundLog->save();

            if ($triviaFoundLog->score > 0) {
                $userPoint            = new UserPoint();
                $userPoint->userId    = $this->_user->id;
                $userPoint->code      = 'TF' . date('ymdHis');
                $userPoint->reference = UserPoint::REFERENCE_TRIVIA_FOUND;
                $userPoint->refId     = $triviaFoundLog->id;
                $userPoint->point     = $triviaFoundLog->score;
                $success              &= $userPoint->save();

                $this->_obtainedScore += $triviaFoundLog->score;
            }
        }

        return $success;
    }

    public function response()
    {
        return [
            'obtainedScore' => $this->_obtainedScore,
        ];
    }
}