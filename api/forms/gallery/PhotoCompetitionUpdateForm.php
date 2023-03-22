<?php

namespace api\forms\gallery;


use common\models\PhotoCompetition;
use nadzif\base\models\FormModel;
use nadzif\behaviors\models\File;
use nadzif\behaviors\UploadBehavior;

class PhotoCompetitionUpdateForm extends FormModel
{
    public $day;
    public $image;

    public function rules()
    {

        return [
            [['day'], 'required'],
            ['day', 'integer'],
            [['image'], 'file', 'extensions' => ['jpg', 'jpeg', 'png']],
        ];
    }

    public function behaviors()
    {
        $behaviors                = parent::behaviors();
        $behaviors['uploadImage'] = [
            'class'         => UploadBehavior::class,
            'baseUrl'       => \Yii::$app->frontendUrlManager->baseUrl,
            'fileAttribute' => 'image',
            'targetModel'   => $this->model,
            'uploadAlias'   => File::ALIAS_FRONTEND,
            'uploadPath'    => 'uploads'
        ];

        return $behaviors;
    }

    public function attributeLabels()
    {
        return (new PhotoCompetition())->attributeLabels();
    }

    public function submit($runValidation = true)
    {
        $success = parent::submit($runValidation);

        if($success){
            if(!$this->model->fileId){
                PhotoCompetition::deleteAll(['id'=> $this->model->id]);
                return false;
            }
        }

        return $success;
    }

}