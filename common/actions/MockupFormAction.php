<?php

namespace common\actions;

use api\components\HttpException;
use common\helpers\Project;
use yii\base\Action;
use yii\base\DynamicModel;
use yii\helpers\ArrayHelper;

class MockupFormAction extends Action
{
    public $rules;

    public $successName;
    public $successMessage;
    public $successCode;
    public $successData;

    public $referencedKeys = [];

    public $customValidation;

    private $_postData = [];

    protected function beforeRun()
    {
        $this->_postData =  \Yii::$app->request->post();

        $model = DynamicModel::validateData($this->_postData, $this->rules);

        if (is_callable($this->customValidation)) {
            $this->customValidation = \call_user_func($this->customValidation, $model);
        }

        if ($errors = $model->getErrors()) {
            throw new HttpException(400, "Validation Error", [], ['Validation' => $errors]);
        }

        return parent::beforeRun();
    }

    public function run()
    {

        foreach ($this->referencedKeys as $referencedKey){

            $currentData = ArrayHelper::getValue($this->successData, $referencedKey);
            $dataSetting = explode(":", $currentData, 2);

            if(sizeof($dataSetting) == 2){
                $dataSource = [];
                $responseData = null;

                if($dataSetting[0] == 'POST'){
                    $dataSource = $this->_postData;
                }

                if($dataSetting[0] == 'CHECKIDENTITY'){
                    foreach (Project::getAppConfig($this->controller->appKey, 'identities') as $identity) {
                        if ($identity['Identity'] == $this->_postData['Identity']) {
                            $dataSource = $identity;
                            break;
                        }
                    }
                }

                $responseData = ArrayHelper::getValue($dataSource, $dataSetting[1]);

                ArrayHelper::setValue($this->successData, $referencedKey, $responseData);
            }
        }

        return [
            'Name'    => $this->successName,
            'Message' => $this->successMessage,
            'Code'    => $this->successCode,
            'Status'  => 200,
            'Data'    => $this->successData,
            'Errors'  => [],
            'RequestTime'  => Project::getRequestTime(),
            'Meta'    => [],

        ];
    }
}