<?php

namespace api\forms\scan;

use api\components\HttpException;
use backend\base\FormModel;
use Carbon\Carbon;
use common\models\Activity;
use common\models\ActivityQrCode;
use common\models\Company;
use common\models\Event;
use common\models\loyalty\Device;
use common\models\Participant;
use common\models\Rundown;
use common\models\User;
use common\models\UserPoint;
use common\models\UserPresence;
use Yii;
use yii\helpers\ArrayHelper;


class PresenceForm extends FormModel
{
    const DELAY_SCAN_MINUTE = 2;
    public    $qrCode;
    public    $volume;
    public    $_meta;
    protected $_deviceIdentifier;
    /** @var User */
    private $_user;
    /** @var Company */
    private $_company;
    /** @var Rundown */
    private $_rundown;
    private $_validCode;
    private $_typeScan;
    private $_activityQrCode;

    public function init()
    {
        parent::init();
        $this->_deviceIdentifier = Yii::$app->request->headers->get('X-Device-identifier', null);
    }

    public function rules()
    {
        return [
            [['qrCode', 'deviceIdentifier'], 'required'],
            ['qrCode', 'validateQrCode'],
        ];
    }

    /**
     * @return mixed
     */
    public function getDeviceIdentifier()
    {
        return $this->_deviceIdentifier;
    }

    public function afterValidate()
    {
        parent::afterValidate();
    }

    public function validateQrCode($attribute, $params)
    {
        $now = Carbon::now()->timezone('Asia/Jakarta');
        if ($this->qrCode) {
            $parts           = parse_url($this->qrCode);
            $query           = ArrayHelper::getValue($parts, 'query');
            $path            = ArrayHelper::getValue($parts, 'path');
            $explodeModule   = explode('/', $path);
            $this->_typeScan = isset($explodeModule[count($explodeModule) - 1]) ? $explodeModule[count($explodeModule) - 1] : FALSE;
            parse_str($query, $query);

            if ($this->_typeScan === 'user-presence') {
                $this->_validCode = ArrayHelper::getValue($query, 'id');
            }
            if ($this->_typeScan === 'activity-qr-code') {
                $this->_validCode = ArrayHelper::getValue($query, 'code');
            }
            if (!$this->_typeScan || !$this->_validCode) {
                $this->addError($attribute, Yii::t('app', 'Invalid qr code!'));
            }
            if ($this->_typeScan == 'user-presence') {
                $this->_rundown = Rundown::find()->where([
                    Rundown::tableName() . '.id'         => $this->_validCode,
                    Rundown::tableName() . '.status'     => Rundown::STATUS_ACTIVE,
                    Participant::tableName() . '.userId' => \Yii::$app->user->id
                ])
                    ->leftJoin(Participant::tableName(), Participant::tableName() . '.eventId = ' . Event::tableName() . '.id')
                    ->joinWith(['event'])
                    ->one();

                if ($this->_rundown) {
                    $start = Carbon::parse($this->_rundown->start);
                    $end   = Carbon::parse($this->_rundown->end);
                    if (!$now->isBetween($start, $end)) {
                         $this->addError($attribute,  Yii::t('app', 'Rundown belum dimulai atau telah selesai!'));
                    }
                    $alReadyPresence = UserPresence::find()->where([
                        'rundownId' => $this->_rundown->id,
                        'userId'    => \Yii::$app->user->id
                    ])->one();
                    if ($alReadyPresence) {
                         $this->addError($attribute,  Yii::t('app', 'Anda telah menghadiri acara ini!'));
                    }
                } else {
                     $this->addError($attribute,  Yii::t('app', 'Anda bukan peserta acara ini!'));
                }
            } else if ($this->_typeScan == 'activity-qr-code') {
                $this->_activityQrCode = ActivityQrCode::find()->where([
                    ActivityQrCode::tableName() . '.code'   => $this->_validCode,
                    ActivityQrCode::tableName() . '.status' => ActivityQrCode::STATUS_AVAILABLE,
                    Activity::tableName() . '.status'       => Activity::STATUS_ACTIVE,
                    Rundown::tableName() . '.status'        => Rundown::STATUS_ACTIVE,
                    Event::tableName() . '.status'          => Event::STATUS_ACTIVE,
                ])->joinWith(['activity'])->one();
                $scanAllowed           = $this->_activityQrCode ? true : false;
                if ($this->_activityQrCode) {
                    $start = Carbon::parse($this->_activityQrCode->activity->rundown->start);
                    $end   = Carbon::parse($this->_activityQrCode->activity->rundown->end);
                    if (!$now->isBetween($start, $end)) {
                         $this->addError($attribute,  Yii::t('app', 'Rundown belum dimulai atau telah selesai!'));
                    }
                    $allowedCodeAllowances = ArrayHelper::getColumn($this->_activityQrCode->activityQrCodeAllowances,
                        'userType');
                    $userTypeIncluded      = ArrayHelper::isIn(\Yii::$app->user->identity->type, $allowedCodeAllowances);
                    $scanAllowed           &= $userTypeIncluded;

                    if ($this->_activityQrCode->uniqueUser) {
                        $countClaimed = UserPoint::find()->where([
                            'code'   => $this->_validCode,
                            'userId' => \Yii::$app->user->id
                        ])->count();
                        $scanAllowed  &= $countClaimed == 0;
                    } else {
                        $claimedActivityQrCode = ActivityQrCode::find()->where(['code' => $this->_validCode])->one();
                        $userPoint             = UserPoint::find()
                            ->where([
                                'code'   => $this->_validCode,
                                'userId' => \Yii::$app->user->id
                            ])
                            ->orderBy([UserPoint::tableName() . '.createdAt' => \SORT_DESC]);
                        if ($lastScanned = $userPoint->one()) {
                            $lastDateScan = Carbon::parse($lastScanned->createdAt);
                            $lastDateScan->addMinute(self::DELAY_SCAN_MINUTE);
                            if ($lastDateScan->isAfter($now)) {
                                 $this->addError($attribute,  Yii::t('app', 'Harap tunggu {minute} menit untuk pemindaian lainnya!',[
                                    'minute' =>$lastDateScan->diff($now)->format('%H:%I:%S')
                                ]));
                            }
                        }
                        $maximumScan = $userPoint->count();
                        if ($maximumScan >= $claimedActivityQrCode->maximumScan) {
                             $this->addError($attribute,  Yii::t('app', 'Kamu sudah melakukan scan maksimal pada event ini!'));
                        }
                    }
                    if ($this->_activityQrCode->claimed == $this->_activityQrCode->quota) {
                         $this->addError($attribute,  Yii::t('app', 'Quota exceeded'));
                    }
                    if ($scanAllowed == false) {
                         $this->addError($attribute,  Yii::t('app', 'Anda tidak diperbolehkan untuk mengklaim kode ini!'));
                    }
                } else {
                     $this->addError($attribute,  Yii::t('app', 'Kode qr tidak ditemukan atau telah diklaim!'));
                }
            }
        }
    }

    public function submit()
    {
        if ($this->_typeScan == 'user-presence') {
            $userPresence            = new UserPresence();
            $userPresence->userId    = \Yii::$app->user->id;
            $userPresence->rundownId = $this->_rundown->id;
            return $userPresence->save();
        } else if ($this->_typeScan == 'activity-qr-code') {
            /** @var Connection $db */
            $db          = Yii::$app->db;
            $transaction = $db->beginTransaction();
            $success     = true;
            try {
                if ($this->_activityQrCode) {
                    ActivityQrCode::updateAll(['claimed' => $this->_activityQrCode->claimed + 1],
                        ['code' => $this->_validCode]);
                    $success = true;
                    if ($success) {
                        $userPoint             = new UserPoint();
                        $userPoint->userId     = \Yii::$app->user->id;
                        $userPoint->activityId = $this->_activityQrCode->activityId;
                        $userPoint->point      = $this->_activityQrCode->point;
                        $userPoint->code       = $this->_activityQrCode->code;
                        $success               = $userPoint->save();
                    }
                }
                if ($success) {
                    $transaction->commit();
                } else {
                    $transaction->rollBack();
                    $success = false;
                }
            } catch (\Exception $e) {
                $transaction->rollBack();
                $success = false;
            }
            return $success;
        }
    }

    /**
     * @return array|mixed
     */

    public function response()
    {
        return [];
    }

    public function meta()
    {
        return $this->_meta;
    }
}