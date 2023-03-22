<?php

namespace api\components;

use api\components\BaseForm;
use api\components\HttpException;
use api\components\Response;
use yii\base\Action;
use yii\base\InvalidConfigException;
use yii\web\ForbiddenHttpException;

/**
 * Class FormAction
 *
 * @package frontend\components
 *
 * Must be used with ContentTypeFilter for json request
 */
class FormAction extends Action
{
    /** @var string */
    public $formClass;

    /** @var int */
    public $apiCodeSuccess;

    /** @var int */
    public $apiCodeFailed;

    /** @var int */
    public $statusSuccess = 200;

    /** @var int */
    public $statusFailed = 400;

    /** @var string */
    public $messageSuccess = 'Submit form success';

    /** @var string */
    public $messageFailed = 'Submit form failed';

    /** @var bool Whether the user can access this action or not */
    public $canAccess = true;

    /**
     * @since 2018-05-06 23:18:53
     * @throws InvalidConfigException
     */
    public function init()
    {
        if ($this->formClass === null) {
            throw new InvalidConfigException(\Yii::t('app', '$formClass must be set.'));
        }
    }

    /**
     * @since 2018-05-06 23:18:57
     * @return Response
     * @throws HttpException
     * @throws InvalidConfigException
     */
    public function run()
    {
        $formClass = $this->formClass;

        /** @var BaseForm $form */
        $form             = new $formClass();
        $form->attributes = \Yii::$app->request->getBodyParams();

        if ($form->validate() && $form->submit()) {
            $response          = new Response();
            $response->name    = \Yii::t('app', 'Success');
            $response->message = $this->messageSuccess;
            $response->code    = $this->apiCodeSuccess;
            $response->status  = $this->statusSuccess;
            $response->data    = $form->response();
            $response->meta    = $form->meta();

            return $response;
        }

        throw new HttpException($this->statusFailed, $this->messageFailed, $form->errors, $this->apiCodeFailed);
    }

    /**
     * @since 2018-05-10 15:56:23
     * @return bool
     * @throws ForbiddenHttpException
     */
    protected function beforeRun()
    {
        if ($this->canAccess instanceof \Closure) {
            $this->canAccess = \call_user_func($this->canAccess);
        }

        if (!$this->canAccess) {
            throw new ForbiddenHttpException(\Yii::t('app', 'You do not have right to access this page'),
                $this->apiCodeFailed);
        }

        return parent::beforeRun();
    }
}
