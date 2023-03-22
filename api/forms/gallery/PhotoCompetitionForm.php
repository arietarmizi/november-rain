<?php

namespace api\forms\gallery;

use api\components\BaseForm;
use common\models\Event;
use common\models\PhotoCompetition;
use common\models\User;
use nadzif\file\FileManager;
use nadzif\file\models\File;
use yii\web\UploadedFile;

class PhotoCompetitionForm extends BaseForm
{

    public  $day;
    public  $eventId;
    public  $fileId;
    public  $image;
    private $allowedImageExtensions = ['jpg', 'jpeg', 'png'];

    /** @var File */
    private $_file;
    /** @var Event */
    private $_eventId;
    /** @var User */
    private $_userId;

    public function rules()
    {
        return [
            [['day', 'eventId'], 'required'],
            [['image'], 'file', 'mimeTypes' => ['image/*'], 'extensions' => $this->allowedImageExtensions],
        ];
    }

    public function submit()
    {
        $fileInstance = UploadedFile::getInstanceByName('image');
        /** @var FileManager $fileManager */
        $fileManager        = \Yii::$app->fileManager;
        $fileManager->alias = '@api';
        $folder             = 'gallery/';

        if ($fileInstance && $folder) {
            $this->_file               = $fileManager->upload($fileInstance, $folder);
            $photoCompetition          = new PhotoCompetition();
            $photoCompetition->fileId  = $this->_file->id;
            $photoCompetition->userId  = \Yii::$app->user->id;
            $photoCompetition->eventId = $this->eventId;
            $photoCompetition->day     = $this->day;
            $photoCompetition->status  = PhotoCompetition::STATUS_ACTIVE;

            return $photoCompetition->save();
        }

        return false;
    }

    public function response()
    {
        return [];
    }
}