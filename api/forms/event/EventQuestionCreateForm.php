<?php

namespace api\forms\event;

use common\models\Event;
use common\models\EventQuestions;
use common\models\User;
use yii\db\Connection;
use api\components\BaseForm;

class EventQuestionCreateForm extends BaseForm
{
    public $eventId;
    public $userId;
    public $questions;

    /** @var Event */
    private $_event;
    /** @var User */
    private $_user;

    private $_productionAvailable = false;

    public function rules()
    {
        return [
            [['eventId', 'questions'], 'required'],
            ['eventId', 'validateEvent'],
        ];
    }

    public function validateCredential($attribute, $params = [])
    {
        $this->_user = \Yii::$app->user->identity;
        if (!$this->_user) {
            $this->addError($attribute, \Yii::t('app', 'User not found'));
        }

    }

    public function validateEvent($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $this->_event = Event::find()->where([
                'id'     => $this->eventId,
                'status' => Event::STATUS_ACTIVE
            ])->one();

            if ($this->_event) {
                $this->_productionAvailable = true;
            } else {
                $this->addError($attribute, \Yii::t('app', 'Event Not Fond'));
            }
        }

    }

    public function submit()
    {
        /** @var Connection $db */
        $db = \Yii::$app->db;

        $transaction = $db->beginTransaction();
        $success     = true;
        try {
            $question            = new EventQuestions();
            $question->eventId   = $this->_event->id;
            $question->userId    = \Yii::$app->user->id;
            $question->questions = $this->questions;
            $success             &= $question->save();
            if ($success) {
                $transaction->commit();
            } else {
                $transaction->rollBack();
                $success = false;
            }
        } catch (\Exception $e) {
            $transaction->rollBack();
            $success = false;
        }

        return $success;
    }

    /**
     * @return array|mixed
     */
    public function response()
    {
        return [
            'eventName' => $this->_event->name
        ];
    }
}