<?php

namespace api\components;

/**
 * Class HttpException
 *
 * Special HttpException for API. There is data property.
 *
 * @package api\components
 * @author  Haqqi <me@haqqi.net>
 */
class HttpException extends \yii\web\HttpException
{
    private $_data;
    private $_errors;

    public function __construct(
        $status,
        $message = null,
        $data = [],
        $errors = [],
        $code = 0,
        \Exception $previous = null
    ) {
        parent::__construct($status, $message, $code, $previous);

        $this->_data = $data;
        $this->_errors = $errors;
    }

    public function getData()
    {
        return $this->_data;
    }

    public function getErrors()
    {
        return $this->_errors;
    }


}
