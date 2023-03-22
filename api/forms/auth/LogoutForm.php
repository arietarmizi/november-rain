<?php

namespace frontend\forms\auth;

use api\components\BaseForm;

class LogoutForm extends BaseForm
{
    function submit()
    {
        return true;
    }

    function response()
    {
        return [];
    }
}
