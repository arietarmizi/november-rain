<?php
/**
 * Created by PhpStorm.
 * User: Nadzif Glovory
 * Date: 11/24/2019
 * Time: 5:53 PM
 */

namespace common\widgets;


use unclead\multipleinput\MultipleInput as UncleadMultipleInput;

class MultipleInput extends UncleadMultipleInput
{
    public $allowEmptyList = true;
    public $max            = true;
    public $iconSource     = UncleadMultipleInput::ICONS_SOURCE_FONTAWESOME;
    public $enableError    = true;
    public $sortable       = false;
}