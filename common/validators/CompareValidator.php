<?php

namespace common\validators;

use api\components\HttpException;

class CompareValidator extends \yii\validators\CompareValidator
{

    const TYPE_ARRAY = 'array';

    public $compareValues;

    protected function compareValues($operator, $type, $value, $compareValue)
    {
        if($this->type != self::TYPE_ARRAY){
            return parent::compareValues($operator, $type, $value, $compareValue);
        }else{
            foreach ($this->compareValues as $compareValue) {
                return parent::compareValues($operator, $type, $value, $compareValue);
            }
        }
    }
}