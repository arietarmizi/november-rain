<?php

namespace api\forms\gallery;

use abei2017\emoji\Emoji;
use api\components\BaseForm;
use common\models\PhotoCompetition;
use common\models\PhotoCompetitionReply;

class GalleryCompetitionReplyForm extends BaseForm
{

    public $photoCompetitionId;
    public $reply;
    /** @var PhotoCompetition */
    private $_photoCompetitionId;

    public function rules()
    {
        return [
            [['photoCompetitionId', 'reply'], 'required'],
            ['photoCompetitionId', 'validatePhotoCompetition'],
        ];
    }

    public function validatePhotoCompetition($attribute, $params)
    {
        $this->_photoCompetitionId = PhotoCompetition::find()
            ->where([
                PhotoCompetition::tableName() . '.id'     => $this->photoCompetitionId,
                PhotoCompetition::tableName() . '.status' => PhotoCompetition::STATUS_ACTIVE,
            ])->one();
        if (!$this->_photoCompetitionId) {
            $this->addError($attribute, \Yii::t('app', 'Photo Competition not found'));
        }
    }

    public function submit()
    {
        $success     = true;
        $connection  = \Yii::$app->db;
        $transaction = $connection->beginTransaction();

        try {
            /** @var PhotoCompetitionReply $photoCompetitionReply */
            $photoCompetitionReply                     = new PhotoCompetitionReply();
            $photoCompetitionReply->userId             = \Yii::$app->user->id;
            $photoCompetitionReply->photoCompetitionId = $this->photoCompetitionId;
            $photoCompetitionReply->reply              = Emoji::toShort($this->reply);
            $success                                   &= $photoCompetitionReply->save();
            $success ? $transaction->commit() : $transaction->rollBack();
        } catch (\Exception $e) {
            $success = false;
        }

        return $success;
    }

    public function response()
    {
        return [];
    }
}
