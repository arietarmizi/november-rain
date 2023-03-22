<?php
/**
 * Created by PhpStorm.
 * User: Nadzif Glovory
 * Date: 11/27/2020
 * Time: 9:57 AM
 */

namespace api\forms\account;

use api\components\BaseForm;
use api\components\HttpException;
use common\models\User;
use nadzif\file\FileManager;
use nadzif\file\models\File;
use yii\web\UploadedFile;

class SetImageForm extends BaseForm
{
    public  $image;
    private $allowedImageExtensions = ['jpg', 'jpeg', 'png'];
    /** @var File */
    private $_file;

    public function rules()
    {
        return [
            [['image'], 'file', 'mimeTypes' => ['image/*'], 'extensions' => $this->allowedImageExtensions],
        ];
    }

    public function submit()
    {
        $fileInstance = UploadedFile::getInstanceByName('image');
        /** @var FileManager $fileManager */
        $fileManager        = \Yii::$app->fileManager;
        $fileManager->alias = '@api';
        $folder             = 'user/';
        /** @var User $user */
        $user = \Yii::$app->user->identity;

        if ($fileInstance && $folder) {
            $this->_file  = $fileManager->upload($fileInstance, $folder);
            $user->fileId = $this->_file->id;
        }
        $success = true;
        if ($user->validate()) {
            if ($user->hasErrors()) {
                $this->addErrors($user->errors);
                throw new HttpException(400, 'bruh', $user->errors);
            } else {
                $success &= $user->save();
            }
        }
        return $success;

    }

    /**
     * @return mixed
     *
     * How to format the data
     * @since 2018-05-06 23:08:33
     */
    public function response()
    {
        return [

        ];
    }

    public function meta()
    {
        return null;
    }
}