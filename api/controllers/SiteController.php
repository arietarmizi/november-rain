<?php

namespace api\controllers;

use api\components\Controller;
use api\components\Response;
use common\models\Activity;
use common\models\ActivityQrCode;
use common\models\Event;
use common\models\Participant;
use common\models\Rundown;
use common\models\SystemConfig;
use common\models\UserPoint;
use common\models\UserPresence;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use Yii;
use yii\web\UploadedFile;

/**
 * Site controller
 */
class SiteController extends Controller
{
    public function behaviors()
    {
        $behaviors                              = parent::behaviors();
        $behaviors['authenticator']['except']   = ['index', 'android-version'];
        $behaviors['systemAppFilter']['except'] = ['index', 'android-version'];
        return $behaviors;
    }
    public function actionTest()
    {
        $response = new Response();
        $response->name    = \Yii::$app->name;
        $response->message = 'API is running';
        $response->code    = 0;
        $response->status  = 200;
        $response->data = \Yii::$app->request->post();

        return $response;

        $model = new \yii\base\DynamicModel(['fileUpload', 'noRek', 'an', 'bank', 'nominal']);
        $model->addRule(['noRek','an', 'bank', 'nominal'], 'safe')
            ->addRule(['fileUpload'], 'required')
            ->addRule(['fileUpload'], 'file', ['extensions' => ['jpg', 'jpeg']]);

        $model->fileUpload = UploadedFile::getInstanceByName("Payment[fileUpload]");



        // $response->data = \Yii::$app->request->post();
        // $response->data = $model;
        if (!$model->validate()) {
            $response->data = $model->errors;
            // $mime = FileHelper::getMimeType($model->fileUpload->tempName, null, false);
            // $response->data = $mime;
        } else {
            $response->data = $model;
        }

        if($model->load(\Yii::$app->request->post())){
            // do somenthing with model
            // $response->data = $model;
        }

        return $response;


        // $file = UploadedFile::getInstanceByName("Payment[fileUpload]");
        // // FileHelper::getExtensionsByMimeType($)
        // $mime = FileHelper::getMimeType($file->tempName, null, false);
        // $response = new Response();

        // $response->name    = \Yii::$app->name;
        // $response->message = 'API is running';
        // $response->code    = 0;
        // $response->status  = 200;
        // // // $response->data    = 'You are accessing this endpoint from ' . \Yii::$app->request->getUserIP();
        // // $response->data    = [
        // //     'file' => $file,
        // //     'mime' => $mime,
        // //     'ext' => FileHelper::getExtensionsByMimeType($mime)
        // // ];

        // $request          = \Yii::$app->request;
        // $form             = new \yii\base\DynamicModel(['fileUpload', 'noRek', 'an', 'bank', 'nominal']);
        // $form->addRule(['fileUpload'], 'file', ['extensions' => ['jpg', 'jpeg']]);
        // $form->addRule(['noRek', 'an', 'bank', 'nominal'], 'safe');
        // $form->fileUpload = UploadedFile::getInstanceByName("Payment[fileUpload]");

        // // ['fileUpload', 'file', 'extensions' => ['jpg', 'jpeg'],'wrongExtension' => 'gambar wajib jpg,jpeg', 'maxSize' => 2 * pow(1000, 2)]


        // $form->load($request->post());
        // $response->data = $form;
        // // $response->data = $request->post();
        // return $response;

        // if ($form->load($request->post())) {
        //     return $form;
        // }

        // die('asd');
    }

    public function actionIndex()
    {
        $response = new Response();

        $response->name    = \Yii::$app->name;
        $response->message = \Yii::t('app', 'API is running');
        $response->code    = 0;
        $response->status  = 200;
        $response->data    = \Yii::t('app', 'You are accessing this endpoint from ') . \Yii::$app->request->getUserIP();

        return $response;
    }
    public function actionAndroidVersion()
    {
        $version           = SystemConfig::getValueWithFallback(SystemConfig::KEY_ANDROID_VERSION, 1);
        $updateUrl         = SystemConfig::getValueWithFallback(SystemConfig::KEY_ANDROID_UPDATE_URL,
            'https://google.com');
        $response          = new Response();
        $response->name    = \Yii::t('app', 'Android Versions');
        $response->message = \Yii::t('app', 'Andorid Version Loaded');
        $response->code    = 0;
        $response->status  = 200;
        $response->data    = ['version' => $version, 'updateUrl' => $updateUrl];
        return $response;
    }

}
        