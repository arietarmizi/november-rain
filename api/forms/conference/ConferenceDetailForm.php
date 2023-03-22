<?php

namespace api\forms\conference;

use api\components\BaseForm;
use api\components\HttpException;
use common\models\Conference;
use common\models\ConferenceSession;
use common\models\ConferenceSessionReference;
use yii\helpers\ArrayHelper;

class ConferenceDetailForm extends BaseForm
{
    public $eventId;
    public $id;
    public $pageSize;
    public $_items;
    public $_meta;

    public function rules()
    {
        return [
            [['eventId', 'id'], 'required']
        ];
    }

    public function submit()
    {
        $query        = Conference::find()->where([
            Conference::tableName() . '.id'      => $this->id,
            Conference::tableName() . '.eventId' => $this->eventId,
            Conference::tableName() . '.status'  => Conference::STATUS_ACTIVE
        ])->one();
        $this->_items = $query;
        if (!$this->_items) {
            throw new HttpException(400, \Yii::t('app', 'Data not found'));
        }
        $this->_items = ArrayHelper::toArray($this->_items, [
            Conference::class => [
                'name',
                'description',
                'start'   => function ($model) {
                    return \Yii::$app->formatter->asDate($model->start);
                },
                'end'     => function ($model) {
                    return \Yii::$app->formatter->asDate($model->end);
                },
                'location',
                'latitude',
                'longitude',
                'status'  => function ($model) {
                    return Conference::statuses()[$model->status];
                },
                'session' => function ($model) {
                    /** @var Conference $model */
                    $session = [];
                    foreach ($model->conferenceSessions as $sessions) {
                        /** @var ConferenceSession $sessions */
                        $references = [];
                        foreach ($sessions->conferenceSessionReferences as $reference) {
                            /** @var ConferenceSessionReference $reference */
                            $files = [];
                            foreach ($reference->conferenceSessionReferenceFiles as $file) {
                                $files[] = [
                                    'name'    => $file->name,
                                    'fileUrl' => isset($file->file) ? $file->file->source : null,
                                    'status'  => $file->status
                                ];
                            }
                            $references[] = [
                                'name'        => $reference->name,
                                'description' => $reference->description,
                                'order'       => $reference->order,
                                'status'      => $reference->status,
                                'files'       => $files
                            ];
                        }
                        $session[] = [
                            'id'                => $sessions->id,
                            'name'              => $sessions->name,
                            'speakerName'       => $sessions->speaker->name,
                            'speakerProfile'    => isset($sessions->speaker->file) ? $sessions->speaker->file->source : null,
                            'speakerEducation'  => $sessions->speaker->education,
                            'speakerExperience' => $sessions->speaker->experience,
                            'description'       => $sessions->description,
                            'status'            => $sessions->status,
                            'reference'         => $references,
                        ];
                    }
                    return $session;
                },
            ],
        ]);
        return true;
    }

    public function response()
    {
        return $this->_items;
    }
}