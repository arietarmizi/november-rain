<?php

/**
 * Created by PhpStorm.
 * User: Nadzif Glovory
 * Date: 10/3/2018
 * Time: 2:43 PM
 */

namespace api\forms\account;

use api\components\BaseForm;
use api\components\HttpException;
use common\models\User;

class EditProfileForm extends BaseForm
{

    public $identityCardNumber;
    public $name;
    public $email;
    public $birthPlace;
    public $birthDate;
    public $password;
    public $phoneNumber;
    public $position;
    public $regionId;
    public $segmen;
    public $grade;
    public $instagram;
    public $address;
    public $villageId;
    public $hobby;
    public $blood;
    public $memberId;
    public $covidStatus;

    public function rules()
    {
        return [
            [['name', 'password', 'regionId'], 'required'],
            ['email', 'email'],
            [['identityCardNumber', 'email'], 'string', 'min' => 6, 'max' => 255],
            ['birthDate', 'date', 'format' => 'php:Y-m-d'],
            [
                ['email', 'identityCardNumber', 'phoneNumber'],
                'unique',
                'targetClass' => User::class,
                'filter'      =>
                    ['not in', 'id', \Yii::$app->user->id]
            ],
            [['password', 'birthPlace'], 'string'],
            ['password', 'validatePassword'],
        ];
    }

    public function attributeLabels()
    {
        return [];
    }

    public function scenarios()
    {
        return [
            self::SCENARIO_DEFAULT => [
                'password',
                'identityCardNumber',
                'name',
                'phoneNumber',
                'email',
                'birthPlace',
                'birthDate',
                'position',
                'regionId',
                'segmen',
                'grade',
                'instagram',
                'address',
                'villageId',
                'hobby',
                'blood',
                'memberId',
                'covidStatus',

            ]
        ];
    }

    public function validatePassword($attribute, $params)
    {
        /** @var User $user */
        $user = \Yii::$app->user->identity;
        if (!$user->validatePassword($this->password)) {
            $this->addError($attribute, \Yii::t('app', 'Invalid password.'));
        }
    }

    public function submit()
    {
        /** @var User $user */
        $user = \Yii::$app->user->identity;
        if ($this->identityCardNumber) {
            $user->identityCardNumber = $this->identityCardNumber;
        }
        if ($this->name) {
            $user->name = $this->name;
        }
        if ($this->email) {
            $user->email = $this->email;
        }
        if ($this->birthPlace) {
            $user->birthPlace = $this->birthPlace;
        }
        if ($this->birthDate) {
            $user->birthDate = $this->birthDate;
        }
        if ($this->phoneNumber) {
            $user->phoneNumber = $this->phoneNumber;
        }
        if ($this->position) {
            $user->position = $this->position;
        }
        if ($this->regionId) {
            $user->regionId = $this->regionId;
        }
        if ($this->segmen) {
            $user->segmen = $this->segmen;
        }
        if ($this->grade) {
            $user->grade = $this->grade;
        }
        if ($this->instagram) {
            $user->instagram = $this->instagram;
        }
        if ($this->address) {
            $user->address = $this->address;
        }
        if ($this->villageId) {
            $user->villageId = $this->villageId;
        }
        if ($this->hobby) {
            $user->hobby = $this->hobby;
        }
        if ($this->blood) {
            $user->blood = $this->blood;
        }
        if ($this->memberId) {
            $user->memberId = $this->memberId;
        }
        if ($this->covidStatus) {
            $user->covidStatus = $this->covidStatus;
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
        return [];
    }

}
