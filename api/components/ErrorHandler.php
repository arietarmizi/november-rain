<?php

namespace api\components;

use common\helpers\Project;
use yii\base\ErrorException;
use yii\base\Exception;
use yii\base\UserException;
use yii\web\HttpException;

/**
 * Class ErrorHandler
 * @package api\components
 *
 * Advanced error handler that include 'data' as the return value.
 */
class ErrorHandler extends \yii\web\ErrorHandler
{

    /**
     * Converts an exception into an array.
     * @param \Exception|\Error $exception the exception being converted
     * @return array the array representation of the exception.
     */
    protected function convertExceptionToArray($exception)
    {
        if (!YII_DEBUG && !$exception instanceof UserException && !$exception instanceof HttpException) {
            $exception = new HttpException(500, \Yii::t('yii', 'An internal server error occurred.'));
        }

        $array = [
            'Name' => ($exception instanceof Exception || $exception instanceof ErrorException) ? $exception->getName() : 'Exception',
            'Message' => $exception->getMessage(),
            'Code' => $exception->getCode(),
        ];
        if ($exception instanceof HttpException) {
            $array['Status'] = $exception->statusCode;
        }else{
            $array['Status'] = $exception->statusCode;
        }

        if($exception instanceof \api\components\HttpException) {
            $array['Data'] = $exception->getData();
            $array['Errors'] = $exception->getErrors();
            $array['Meta'] = [];
        }else{
            $array['Data'] = [];
            $array['Errors'] =[];
            $array['Meta'] = [];
        }

        if (YII_DEBUG) {
            $array['Errors']['StackTrace']['Type'] = get_class($exception);
            if (!$exception instanceof UserException) {
                $array['Errors']['StackTrace']['File'] = $exception->getFile();
                $array['Errors']['StackTrace']['Line'] = $exception->getLine();
                $array['Errors']['StackTrace']['Trace'] = explode("\n", $exception->getTraceAsString());
                if ($exception instanceof \yii\db\Exception) {
                    $array['Errors']['StackTrace']['ErrorInfo'] = $exception->errorInfo;
                }
            }
        }
        if (($prev = $exception->getPrevious()) !== null) {
            $array['Errors']['StackTrace']['Previous'] = $this->convertExceptionToArray($prev);
        }

        $array['RequestTime'] = Project::getRequestTime();

        return $array;
    }
}

