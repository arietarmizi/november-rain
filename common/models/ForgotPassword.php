<?php

namespace common\models;

use Carbon\Carbon;
use common\base\ActiveRecord;
use yii\behaviors\AttributeBehavior;

/**
 * Class ForgotPassword
 *
 * @package common\models
 *
 * @property string $id
 * @property string $userId
 * @property string $code
 * @property string $identifier
 * @property string $ip
 * @property string $usedAt
 * @property string $status
 * @property string $createdAt
 * @property string $updatedAt
 *
 * @property User   $user
 */
class ForgotPassword extends ActiveRecord
{
    const STATUS_EXPIRED = 'expired';
    const STATUS_ACTIVE  = 'active';
    const STATUS_USED    = 'used';

    const SCENARIO_CREATE = 'create';

    const EXP_DURATION        = 60 * 10; // 10 minutes
    const RESEND_DURATION     = 60 * 1; // 1 minute
    const MAX_REQUEST_PER_DAY = 3;

    public static function getDb()
    {
        return \Yii::$app->db;
    }

    public static function tableName()
    {
        return '{{%forgot_password}}';
    }

    public static function statuses()
    {
        return [
            self::STATUS_ACTIVE  => \Yii::t('app', 'Active'),
            self::STATUS_EXPIRED => \Yii::t('app', 'Expired'),
            self::STATUS_USED    => \Yii::t('app', 'Used')
        ];
    }

    public function attributeLabels()
    {
        return [
            'id'         => \Yii::t('app', 'Id'),
            'userId'     => \Yii::t('app', 'User Id'),
            'code'       => \Yii::t('app', 'Code'),
            'identifier' => \Yii::t('app', 'Identifier'),
            'ip'         => \Yii::t('app', 'Ip'),
            'usedAt'     => \Yii::t('app', 'Used At'),
            'status'     => \Yii::t('app', 'status'),
            'createdAt'  => \Yii::t('app', 'Created At'),
            'updatedAt'  => \Yii::t('app', 'Updated At'),
        ];
    }

    public function init()
    {
        parent::init();

        $this->on(ActiveRecord::EVENT_BEFORE_INSERT, [$this, 'deactivateOtherCode']);
        $this->on(ActiveRecord::EVENT_AFTER_INSERT, [$this, 'sendPasswordResetToken']);
    }

    public function fields()
    {
        $fields = parent::fields();

        unset($fields['userId']);
        unset($fields['status']);
        unset($fields['usedAt']);

        return $fields;
    }

    public function rules()
    {
        return [
            [['userId', 'identifier', 'ip'], 'required'],
            ['status', 'default', 'value' => static::STATUS_ACTIVE],
        ];
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['code'] = [
            'class'      => AttributeBehavior::class,
            'value'      => function ($event) {
                return \sprintf("%06d", \rand(100000, 999999));
            },
            'attributes' => [
                ActiveRecord::EVENT_BEFORE_INSERT => 'code'
            ],
        ];

        return $behaviors;
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();

        $scenarios[static::SCENARIO_CREATE] = [
            'userId',
            'identifier',
            'ip'
        ];

        return $scenarios;
    }

    public function validateRequest($attribute, $params)
    {
        $yesterday = Carbon::now()->subDay(1)->format('Y-m-d H:i:s');


        /** @var ForgotPassword[] $forgotPasswords */
        $forgotPasswords = ForgotPassword::find()
            ->where([
                'userId' => $this->userId,
            ])
            ->andWhere(['>', 'createdAt', $yesterday])
            ->orderBy(['createdAt' => \SORT_DESC])
            ->all();

        if ($forgotPasswords) {
            if (\count($forgotPasswords) >= self::MAX_REQUEST_PER_DAY) {
                $this->addError('email',
                    'You have reached maximum number (' . static::MAX_REQUEST_PER_DAY . ') of forgot password today.');
            }
            $lastForgotPassword = $forgotPasswords[0];
            $lastForgotTime     =
                Carbon::createFromTimestamp(strtotime($lastForgotPassword->createdAt))->format('Y-m-d H:i:s');

            $diffSecond = Carbon::now()->diffInSeconds($lastForgotTime, true);
            $remaining  = static::RESEND_DURATION - $diffSecond;

            if ($remaining > 0) {
                $this->addError('email',
                    \Yii::t('app', 'Resend can only be requested after' . $remaining . 'seconds.'));
            }
        }

    }


    public function sendPasswordResetToken()
    {
        $this->refresh();
        try {
            \Yii::$app->mailer->htmlLayout = "@common/mail/layouts/html";
            \Yii::$app->mailer->textLayout = "@common/mail/layouts/html";
            $template                      = [
                'userName'    => $this->user->name,
                'code'        => $this->code,
                'expiredTime' => Carbon::createFromTimestamp(strtotime($this->createdAt))
                    ->addSecond(self::EXP_DURATION)
                    ->format('Y-m-d H:i:s')
            ];
            return \Yii::$app->mailer
                ->compose(
                    ['html' => '@common/mail/passwordResetToken-html'],
                    ['template' => $template]
                )
                ->setFrom([\Yii::$app->params['supportEmail'] => \Yii::$app->params['senderName']])
                ->setTo($this->user->email)
                ->setSubject('Password Reset Request from ' . \Yii::$app->params['appName'])
                ->send();
        } catch (\Exception $ex) {
            $this->addError('phoneNumber', 'Send mail failed');
        }
    }


    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'userId']);
    }

    public function deactivateOtherCode()
    {
        ForgotPassword::updateAll(
            ['status' => static::STATUS_EXPIRED],
            [
                'userId' => $this->user->id,
                'status' => static::STATUS_ACTIVE
            ]
        );
    }
}
