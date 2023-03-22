<?php

namespace api\controllers\lpgtracking;

use api\components\HttpException;
use Carbon\Carbon;
use common\actions\MockupFormAction;
use common\actions\MockupListAction;
use common\helpers\Project;
use common\helpers\Randomizer;
use yii\helpers\ArrayHelper;

class DeliveryOrderController extends Controller
{
    public $hasJson = [
        'accept',
        'reject',
        'deliver',
        'receive',
    ];

    public function actions()
    {
        return [
            'list'    => [
                'class'          => MockupListAction::class,
                'successName'    => 'Get Delivery Order List Success',
                'successMessage' => 'Delivery orders loaded successfully.',
                'successCode'    => 10031000,
                'data'           => function ($index) {
                    $totalQuantity = rand(20, 100);

                    $procedures = [
                        'New'         => 'Order Baru',
                        'Accepted'    => 'Diterima Transportir',
                        'Rejected'    => 'Ditolak Transportir',
                        'Rescheduled' => 'Dijadwalkan Ulang',
                        'OnDelivery'  => 'Dalam Perjalanan',
                        'Received'    => 'DIterima Pangkalan',
                        'Completed'   => 'Selesai'
                    ];

                    $currentProcedure = Randomizer::valueIn(array_keys($procedures));

                    $receivedQuantity = null;

                    $scheduledAt  = Randomizer::futureDateTime(2, 10, 'days');
                    $ETA          = null;
                    $deliveryOn   = null;
                    $receivingOn  = null;
                    $rejectReason = null;
                    if (ArrayHelper::isIn($currentProcedure, ['Rejected'])) {
                        $rejectReason = Randomizer::name(9);
                    }

                    $vehicle          = null;
                    $transporterName  = null;
                    $transporterPhone = null;

                    if (ArrayHelper::isIn($currentProcedure, ['OnDelivery'])) {
                        $deliveryOn = Carbon::parse($scheduledAt)->addMinutes(rand(5, 30))->format(\DateTime::ATOM);
                        $ETA        = Carbon::parse($scheduledAt)->addDays(rand(2, 4))->format(\DateTime::ATOM);

                        $vehicle          = Randomizer::licensePlateNumber();
                        $transporterName  = Randomizer::name();
                        $transporterPhone = Randomizer::phoneNumber();
                    }

                    if (ArrayHelper::isIn($currentProcedure, ['Received', 'Completed'])) {
                        $receivedQuantity = $totalQuantity - rand(0, $totalQuantity - 10);

                        $deliveryOn  = Carbon::parse($scheduledAt)->addMinutes(rand(5, 30))->format(\DateTime::ATOM);
                        $ETA         = Carbon::parse($scheduledAt)->addDays(rand(2, 4))->format(\DateTime::ATOM);
                        $receivingOn = Carbon::parse($ETA)->addHours(rand(0, 24))->format(\DateTime::ATOM);

                        $vehicle          = Randomizer::licensePlateNumber();
                        $transporterName  = Randomizer::name();
                        $transporterPhone = Randomizer::phoneNumber();
                    }

                    return [
                        'Id'                     => Randomizer::uuid(),
                        'Code'                   => Randomizer::code(),
                        'PaymentMethod'          => strtoupper(Randomizer::string(10, 10)),
                        'TotalReserved'          => $totalQuantity,
                        'TotalReceived'          => $receivedQuantity,
                        'Agent'                  => [
                            'Code'         => Randomizer::code(),
                            'Name'         => Randomizer::name(3, 'PT.'),
                            'PhoneNumber'  => Randomizer::phoneNumber(),
                            'Address'      => Randomizer::address(),
                            'Neighborhood' => Randomizer::string(5),
                            'Hamlet'       => Randomizer::string(5),

                        ],
                        'Outlet'                 => [
                            'Code'         => Randomizer::code(),
                            'Name'         => Randomizer::name(3),
                            'PhoneNumber'  => Randomizer::phoneNumber(),
                            'ProvinceName' => 'Jawa Timur',
                            'RegencyName'  => 'Malang',
                            'DistrictName' => 'Lowokwaru',
                            'VillageName'  => 'Mojolangu',
                            'Address'      => Randomizer::address(),
                            'Neighborhood' => Randomizer::sequenceString(rand(1, 10)),
                            'Hamlet'       => Randomizer::sequenceString(rand(1, 10)),
                            'Latitude'     => Randomizer::double(-8, -7),
                            'Longitude'    => Randomizer::double(110, 112),
                            'Wide'         => rand(100, 200),
                        ],
                        'DeliveryOrderSchedule'  => [
                            'TransporterName'           => $transporterName,
                            'TransporterPhoneNumber'    => $transporterPhone,
                            'VehicleLicensePlateNumber' => $vehicle,
                            'ScheduledAt'               => $scheduledAt,
                            'DeliveryOn'                => $deliveryOn,
                            'EstimatedTimeArrival'      => $ETA,
                            'ReceivingOn'               => $receivingOn,
                            'RejectReason'              => $rejectReason,
                        ],
                        'CurrentProcedure'       => $currentProcedure,
                        'CurrentProcedureString' => $procedures[$currentProcedure],
                        'Status'                 => 'Active',
                        'CreatedAt'              => Carbon::now()
                            ->subDays(\Yii::$app->request->getQueryParam('page'))
                            ->subMinutes($index * rand(8, 10))
                            ->subSeconds($index * rand(48, 60))
                            ->format(\DateTime::ATOM)
                    ];
                },
            ],
            'accept'  => [
                'class'            => MockupFormAction::class,
                'rules'            => [
                    [['DeliveryOrderId'], 'required'],
                    [['DeliveryOrderId'], 'string'],
                ],
                'customValidation' => function () {
                    if (!\Yii::$app->request->headers->get('X-Device-Identifier')) {
                        throw new HttpException(400, 'Device Identifier must be  defined.', [], [], 10010102);
                    }

                    $identity = \Yii::$app->user->identity;

                    if ($identity['Role'] != 'Transporter') {
                        throw new HttpException(300, 'Role cannot access this page', [], [], 10031201);
                    }

                    if (rand(0, 1)) {
                        throw new HttpException(300, 'Delivery order procedure should be new', [], [], 10031202);
                    }
                },
                'successName'      => 'Delivery Order Accepted',
                'successMessage'   => 'Delivery order procedure has been changed to accepted.',
                'successCode'      => 10031200,
                'successData'      => [
                    'Id'                     => 'POST:DeliveryOrderId',
                    'Code'                   => Randomizer::code(),
                    'TotalReserved'          => rand(20, 100),
                    'TotalReceived'          => null,
                    'ReceivedImageUrls'      => null,
                    'Agent'                  => [
                        'Code'         => Randomizer::code(),
                        'Name'         => Randomizer::name(rand(2, 3), 'PT.'),
                        'PhoneNumber'  => Randomizer::phoneNumber(),
                        'Address'      => Randomizer::address(),
                        'Neighborhood' => Randomizer::sequenceString(rand(1, 10)),
                        'Hamlet'       => Randomizer::sequenceString(rand(1, 10)),

                    ],
                    'Outlet'                 => [
                        'Code'         => Randomizer::code(),
                        'Name'         => Randomizer::name(3),
                        'PhoneNumber'  => Randomizer::phoneNumber(),
                        'ProvinceName' => 'Jawa Timur',
                        'RegencyName'  => 'Malang',
                        'DistrictName' => 'Lowokwaru',
                        'VillageName'  => 'Mojolangu',
                        'Address'      => Randomizer::address(),
                        'Neighborhood' => Randomizer::sequenceString(rand(1, 10)),
                        'Hamlet'       => Randomizer::sequenceString(rand(1, 10)),
                        'Latitude'     => Randomizer::double(-8, -7),
                        'Longitude'    => Randomizer::double(110, 112),
                        'Wide'         => rand(100, 200),
                    ],
                    'DeliveryOrderSchedule'  => [
                        'TransporterName'           => Randomizer::name(),
                        'TransporterPhoneNumber'    => Randomizer::phoneNumber(),
                        'VehicleLicensePlateNumber' => Randomizer::licensePlateNumber(),
                        'ScheduledAt'               => Randomizer::futureDateTime(1, 5, 'days'),
                        'DeliveryOn'                => null,
                        'EstimatedTimeArrival'      => null,
                        'ReceivingOn'               => null,
                        'RejectReason'              => null,
                    ],
                    'Materials'              => [
                        [
                            'Id'          => Randomizer::uuid(),
                            'ItemId'      => Randomizer::uuid(),
                            'Code'        => Randomizer::code(),
                            'Name'        => Randomizer::name(2),
                            'Unit'        => 'BO' . Randomizer::valueIn(['3', '6', '12']),
                            'Reserved'    => rand(20, 100),
                            'Received'    => null,
                            'NoteReceipt' => null,
                        ],
                        [
                            'Id'          => Randomizer::uuid(),
                            'ItemId'      => Randomizer::uuid(),
                            'Code'        => Randomizer::code(),
                            'Name'        => Randomizer::name(2),
                            'Unit'        => 'BO' . Randomizer::valueIn(['3', '6', '12']),
                            'Reserved'    => rand(20, 100),
                            'Received'    => null,
                            'NoteReceipt' => null,
                        ],
                        [
                            'Id'          => Randomizer::uuid(),
                            'ItemId'      => Randomizer::uuid(),
                            'Code'        => Randomizer::code(),
                            'Name'        => Randomizer::name(2),
                            'Unit'        => 'BO' . Randomizer::valueIn(['3', '6', '12']),
                            'Reserved'    => rand(20, 100),
                            'Received'    => null,
                            'NoteReceipt' => null,
                        ],
                    ],
                    'Histories'              => [
                        [
                            'Procedure'       => 'New',
                            'ProcedureString' => 'Order Baru',
                            'CreatedAt'       => Randomizer::pastDateTime(60, 3600),
                        ],
                        [
                            'Procedure'       => 'Accepted',
                            'ProcedureString' => 'Disetujui Transportir',
                            'CreatedAt'       => Carbon::now()->format(DATE_ATOM),
                        ],
                    ],
                    'CurrentProcedure'       => 'Accepted',
                    'CurrentProcedureString' => 'Disetujui Transportir',
                    'Status'                 => 'Active',
                    'CreatedAt'              => Carbon::now()->format(\DateTime::ATOM)
                ],
                'referencedKeys'   => ['Id']
            ],
            'reject'  => [
                'class'            => MockupFormAction::class,
                'rules'            => [
                    [['DeliveryOrderId', 'Reason'], 'required'],
                    [['DeliveryOrderId', 'Reason'], 'string'],
                    [['AllowReschedule'], 'boolean'],
                    [['RescheduleAfter'], 'required', 'when' => function ($model) { return $model->AllowReschedule; }],
                    [['RescheduleAfter'], 'date', 'format' => 'php:Y-m-d'],
                ],
                'customValidation' => function () {
                    if (!\Yii::$app->request->headers->get('X-Device-Identifier')) {
                        throw new HttpException(400, 'Device Identifier must be  defined.', [], [], 10010102);
                    }

                    $identity = \Yii::$app->user->identity;

                    if ($identity['Role'] != 'Transporter') {
                        throw new HttpException(300, 'Role cannot access this page', [], [], 10031301);
                    }

                    if (rand(0, 1)) {
                        throw new HttpException(300, 'Delivery order procedure should be new', [], [], 10031302);
                    }
                },
                'successName'      => 'Delivery Order Rejected',
                'successMessage'   => 'Delivery order procedure has been changed to rejected.',
                'successCode'      => 10031300,
                'successData'      => [
                    'Id'                     => 'POST:DeliveryOrderId',
                    'Code'                   => Randomizer::code(),
                    'TotalReserved'          => rand(20, 100),
                    'TotalReceived'          => null,
                    'ReceivedImageUrls'      => null,
                    'Agent'                  => [
                        'Code'         => Randomizer::code(),
                        'Name'         => Randomizer::name(rand(2, 3), 'PT.'),
                        'PhoneNumber'  => Randomizer::phoneNumber(),
                        'Address'      => Randomizer::address(),
                        'Neighborhood' => Randomizer::sequenceString(rand(1, 10)),
                        'Hamlet'       => Randomizer::sequenceString(rand(1, 10)),

                    ],
                    'Outlet'                 => [
                        'Code'         => Randomizer::code(),
                        'Name'         => Randomizer::name(3),
                        'PhoneNumber'  => Randomizer::phoneNumber(),
                        'ProvinceName' => 'Jawa Timur',
                        'RegencyName'  => 'Malang',
                        'DistrictName' => 'Lowokwaru',
                        'VillageName'  => 'Mojolangu',
                        'Address'      => Randomizer::address(),
                        'Neighborhood' => Randomizer::sequenceString(rand(1, 10)),
                        'Hamlet'       => Randomizer::sequenceString(rand(1, 10)),
                        'Latitude'     => Randomizer::double(-8, -7),
                        'Longitude'    => Randomizer::double(110, 112),
                        'Wide'         => rand(100, 200),
                    ],
                    'DeliveryOrderSchedule'  => [
                        'TransporterName'           => Randomizer::name(),
                        'TransporterPhoneNumber'    => Randomizer::phoneNumber(),
                        'VehicleLicensePlateNumber' => Randomizer::licensePlateNumber(),
                        'ScheduledAt'               => Randomizer::futureDateTime(1, 5, 'days'),
                        'DeliveryOn'                => null,
                        'EstimatedTimeArrival'      => null,
                        'ReceivingOn'               => null,
                        'RejectReason'              => 'POST:Reason',
                    ],
                    'Materials'              => [
                        [
                            'Id'          => Randomizer::uuid(),
                            'ItemId'      => Randomizer::uuid(),
                            'Code'        => Randomizer::code(),
                            'Name'        => Randomizer::name(2),
                            'Unit'        => 'BO' . Randomizer::valueIn(['3', '6', '12']),
                            'Reserved'    => rand(20, 100),
                            'Received'    => null,
                            'NoteReceipt' => null,
                        ],
                        [
                            'Id'          => Randomizer::uuid(),
                            'ItemId'      => Randomizer::uuid(),
                            'Code'        => Randomizer::code(),
                            'Name'        => Randomizer::name(2),
                            'Unit'        => 'BO' . Randomizer::valueIn(['3', '6', '12']),
                            'Reserved'    => rand(20, 100),
                            'Received'    => null,
                            'NoteReceipt' => null,
                        ],
                        [
                            'Id'          => Randomizer::uuid(),
                            'ItemId'      => Randomizer::uuid(),
                            'Code'        => Randomizer::code(),
                            'Name'        => Randomizer::name(2),
                            'Unit'        => 'BO' . Randomizer::valueIn(['3', '6', '12']),
                            'Reserved'    => rand(20, 100),
                            'Received'    => null,
                            'NoteReceipt' => null,
                        ],
                    ],
                    'Histories'              => [
                        [
                            'Procedure'       => 'New',
                            'ProcedureString' => 'Order Baru',
                            'CreatedAt'       => Randomizer::pastDateTime(60, 3600),
                        ],
                        [
                            'Procedure'       => 'Rejected',
                            'ProcedureString' => 'Ditolak Transportir',
                            'CreatedAt'       => Carbon::now()->format(DATE_ATOM),
                        ],
                    ],
                    'CurrentProcedure'       => 'Rejected',
                    'CurrentProcedureString' => 'Ditolak Transportir',
                    'Status'                 => 'Active',
                    'CreatedAt'              => Carbon::now()->format(\DateTime::ATOM)
                ],
                'referencedKeys'   => ['Id', 'DeliveryOrderSchedule.RejectReason']
            ],
            'deliver' => [
                'class'            => MockupFormAction::class,
                'rules'            => [
                    [['DeliveryOrderId', 'EstimatedTimeArrival'], 'required'],
                    [['DeliveryOrderId'], 'string'],
                    [['EstimatedTimeArrival'], 'date', 'format' => 'php:Y-m-d'],
                    [
                        ['EstimatedTimeArrival'],
                        'compare',
                        'operator'     => '>',
                        'compareValue' => Carbon::now()->format(DATE_ATOM)
                    ],
                ],
                'customValidation' => function () {
                    if (!\Yii::$app->request->headers->get('X-Device-Identifier')) {
                        throw new HttpException(400, 'Device Identifier must be  defined.', [], [], 10010102);
                    }

                    $identity = \Yii::$app->user->identity;

                    if ($identity['Role'] != 'Transporter') {
                        throw new HttpException(300, 'Role cannot access this page', [], [], 10031401);
                    }

                    if (rand(0, 1)) {
                        if (rand(0, 1)) {
                            throw new HttpException(300, 'Delivery order procedure should be accepted', [], [],
                                10031402);
                        }
                    }
                },
                'successName'      => 'Delivery Order Shipped',
                'successMessage'   => 'Delivery order procedure has been changed to on delivery.',
                'successCode'      => 10031400,
                'successData'      => [
                    'Id'                     => 'POST:DeliveryOrderId',
                    'Code'                   => Randomizer::code(),
                    'TotalReserved'          => rand(20, 100),
                    'TotalReceived'          => null,
                    'ReceivedImageUrls'      => null,
                    'Agent'                  => [
                        'Code'         => Randomizer::code(),
                        'Name'         => Randomizer::name(rand(2, 3), 'PT.'),
                        'PhoneNumber'  => Randomizer::phoneNumber(),
                        'Address'      => Randomizer::address(),
                        'Neighborhood' => Randomizer::sequenceString(rand(1, 10)),
                        'Hamlet'       => Randomizer::sequenceString(rand(1, 10)),

                    ],
                    'Outlet'                 => [
                        'Code'         => Randomizer::code(),
                        'Name'         => Randomizer::name(3),
                        'PhoneNumber'  => Randomizer::phoneNumber(),
                        'ProvinceName' => 'Jawa Timur',
                        'RegencyName'  => 'Malang',
                        'DistrictName' => 'Lowokwaru',
                        'VillageName'  => 'Mojolangu',
                        'Address'      => Randomizer::address(),
                        'Neighborhood' => Randomizer::sequenceString(rand(1, 10)),
                        'Hamlet'       => Randomizer::sequenceString(rand(1, 10)),
                        'Latitude'     => Randomizer::double(-8, -7),
                        'Longitude'    => Randomizer::double(110, 112),
                        'Wide'         => rand(100, 200),
                    ],
                    'DeliveryOrderSchedule'  => [
                        'TransporterName'           => Randomizer::name(),
                        'TransporterPhoneNumber'    => Randomizer::phoneNumber(),
                        'VehicleLicensePlateNumber' => Randomizer::licensePlateNumber(),
                        'ScheduledAt'               => Randomizer::futureDateTime(1, 5, 'days'),
                        'DeliveryOn'                => Carbon::now()->format(DATE_ATOM),
                        'EstimatedTimeArrival'      => 'POST:EstimatedTimeArrival',
                        'ReceivingOn'               => null,
                        'RejectReason'              => 'POST:Reason',
                    ],
                    'Materials'              => [
                        [
                            'Id'          => Randomizer::uuid(),
                            'ItemId'      => Randomizer::uuid(),
                            'Code'        => Randomizer::code(),
                            'Name'        => Randomizer::name(2),
                            'Unit'        => 'BO' . Randomizer::valueIn(['3', '6', '12']),
                            'Reserved'    => rand(20, 100),
                            'Received'    => null,
                            'NoteReceipt' => null,
                        ],
                        [
                            'Id'          => Randomizer::uuid(),
                            'ItemId'      => Randomizer::uuid(),
                            'Code'        => Randomizer::code(),
                            'Name'        => Randomizer::name(2),
                            'Unit'        => 'BO' . Randomizer::valueIn(['3', '6', '12']),
                            'Reserved'    => rand(20, 100),
                            'Received'    => null,
                            'NoteReceipt' => null,
                        ],
                        [
                            'Id'          => Randomizer::uuid(),
                            'ItemId'      => Randomizer::uuid(),
                            'Code'        => Randomizer::code(),
                            'Name'        => Randomizer::name(2),
                            'Unit'        => 'BO' . Randomizer::valueIn(['3', '6', '12']),
                            'Reserved'    => rand(20, 100),
                            'Received'    => null,
                            'NoteReceipt' => null,
                        ],
                    ],
                    'Histories'              => [
                        [
                            'Procedure'       => 'New',
                            'ProcedureString' => 'Order Baru',
                            'CreatedAt'       => Randomizer::pastDateTime(1800, 3600),
                        ],
                        [
                            'Procedure'       => 'Accepted',
                            'ProcedureString' => 'Disetujui Transportir',
                            'CreatedAt'       => Randomizer::pastDateTime(60, 1800),
                        ],
                        [
                            'Procedure'       => 'OnDelivery',
                            'ProcedureString' => 'Dalam Perjalanan',
                            'CreatedAt'       => Carbon::now()->format(DATE_ATOM),
                        ],
                    ],
                    'CurrentProcedure'       => 'OnDelivery',
                    'CurrentProcedureString' => 'Dalam Perjalanan',
                    'Status'                 => 'Active',
                    'CreatedAt'              => Carbon::now()->format(\DateTime::ATOM)
                ],
                'referencedKeys'   => [
                    'Id',
                    'DeliveryOrderSchedule.RejectReason',
                    'DeliveryOrderSchedule.EstimatedTimeArrival'
                ]
            ],
            'receive' => [
                'class'            => MockupFormAction::class,
                'rules'            => [
                    [['DeliveryOrderId', 'Materials', 'Latitude', 'Longitude', 'MediaIds'], 'required'],
                    [['DeliveryOrderId'], 'string'],
                    [['Latitude', 'Longitude'], 'double'],
                    [['Materials', 'MediaIds'], 'safe'],
                ],
                'customValidation' => function ($model) {
                    if (!\Yii::$app->request->headers->get('X-Device-Identifier')) {
                        throw new HttpException(400, 'Device Identifier must be  defined.', [], [], 10010102);
                    }

                    $identity = \Yii::$app->user->identity;

//                    if ($identity['Role'] != 'Outlet') {
//                        throw new HttpException(300, 'Role cannot access this page', [], [], 10031501);
//                    }

                    $materials = \Yii::$app->request->post('Materials');

                    foreach ($materials as $index => $material) {
                        if (!$material['Received']) {
                            $model->addError("Materials[$index][Received]",
                                'All material receive quantity should be defined');
                            break;
                        }
                    }

                    if (!rand(0, 2)) {
                        $index = rand(0, count($materials) - 1);
                        $model->addError("Materials[$index][NoteReceipt]",
                            'Note should be defined when received quantity less than delivered quantity');
                    }

                    if (rand(0, 1)) {
                        if (!rand(0, 2)) {
                            throw new HttpException(300, 'Delivery order procedure should be on delivery', [], [],
                                10031402);
                        }

                        if (!rand(0, 2)) {
                            $missed = Randomizer::valueIn([100, 150, 200, 250, 300, 350]);
                            throw new HttpException(400,
                                "Allowed maximum range to receive is $missed meters from outlet location", [], [],
                                10031503);
                        }
                    }
                },
                'successName'      => 'Delivery Order Received',
                'successMessage'   => 'Delivery order procedure has been changed to received.',
                'successCode'      => 10031500,
                'successData'      => [
                    'Id'                     => 'POST:DeliveryOrderId',
                    'Code'                   => Randomizer::code(),
                    'TotalReserved'          => rand(20, 100),
                    'TotalReceived'          => null,
                    'ReceivedImageUrls'      => null,
                    'Agent'                  => [
                        'Code'         => Randomizer::code(),
                        'Name'         => Randomizer::name(rand(2, 3), 'PT.'),
                        'PhoneNumber'  => Randomizer::phoneNumber(),
                        'Address'      => Randomizer::address(),
                        'Neighborhood' => Randomizer::sequenceString(rand(1, 10)),
                        'Hamlet'       => Randomizer::sequenceString(rand(1, 10)),

                    ],
                    'Outlet'                 => [
                        'Code'         => Randomizer::code(),
                        'Name'         => Randomizer::name(3),
                        'PhoneNumber'  => Randomizer::phoneNumber(),
                        'ProvinceName' => 'Jawa Timur',
                        'RegencyName'  => 'Malang',
                        'DistrictName' => 'Lowokwaru',
                        'VillageName'  => 'Mojolangu',
                        'Address'      => Randomizer::address(),
                        'Neighborhood' => Randomizer::sequenceString(rand(1, 10)),
                        'Hamlet'       => Randomizer::sequenceString(rand(1, 10)),
                        'Latitude'     => Randomizer::double(-8, -7),
                        'Longitude'    => Randomizer::double(110, 112),
                        'Wide'         => rand(100, 200),
                    ],
                    'DeliveryOrderSchedule'  => [
                        'TransporterName'           => Randomizer::name(),
                        'TransporterPhoneNumber'    => Randomizer::phoneNumber(),
                        'VehicleLicensePlateNumber' => Randomizer::licensePlateNumber(),
                        'ScheduledAt'               => Randomizer::futureDateTime(1, 5, 'days'),
                        'DeliveryOn'                => Carbon::now()->format(DATE_ATOM),
                        'EstimatedTimeArrival'      => 'POST:EstimatedTimeArrival',
                        'ReceivingOn'               => null,
                        'RejectReason'              => 'POST:Reason',
                    ],
                    'Materials'              => [
                        [
                            'Id'          => Randomizer::uuid(),
                            'ItemId'      => Randomizer::uuid(),
                            'Code'        => Randomizer::code(),
                            'Name'        => Randomizer::name(2),
                            'Unit'        => 'BO' . Randomizer::valueIn(['3', '6', '12']),
                            'Reserved'    => 100,
                            'Received'    => 80,
                            'NoteReceipt' => "Stock masih ada",
                        ],
                        [
                            'Id'          => Randomizer::uuid(),
                            'ItemId'      => Randomizer::uuid(),
                            'Code'        => Randomizer::code(),
                            'Name'        => Randomizer::name(2),
                            'Unit'        => 'BO' . Randomizer::valueIn(['3', '6', '12']),
                            'Reserved'    => 200,
                            'Received'    => 200,
                            'NoteReceipt' => null,
                        ],
                        [
                            'Id'          => Randomizer::uuid(),
                            'ItemId'      => Randomizer::uuid(),
                            'Code'        => Randomizer::code(),
                            'Name'        => Randomizer::name(2),
                            'Unit'        => 'BO' . Randomizer::valueIn(['3', '6', '12']),
                            'Reserved'    => 150,
                            'Received'    => 150,
                            'NoteReceipt' => null,
                        ],
                    ],
                    'Histories'              => [
                        [
                            'Procedure'       => 'New',
                            'ProcedureString' => 'Order Baru',
                            'CreatedAt'       => Randomizer::pastDateTime(3600, 5400),
                        ],
                        [
                            'Procedure'       => 'Accepted',
                            'ProcedureString' => 'Disetujui Transportir',
                            'CreatedAt'       => Randomizer::pastDateTime(1800, 3600),
                        ],
                        [
                            'Procedure'       => 'OnDelivery',
                            'ProcedureString' => 'Dalam Perjalanan',
                            'CreatedAt'       => Randomizer::pastDateTime(60, 1800),
                        ],
                        [
                            'Procedure'       => 'Received',
                            'ProcedureString' => 'Diterima Outlet',
                            'CreatedAt'       => Carbon::now()->format(DATE_ATOM),
                        ],
                    ],
                    'CurrentProcedure'       => 'Received',
                    'CurrentProcedureString' => 'Diterima Outlet',
                    'Status'                 => 'Active',
                    'CreatedAt'              => Carbon::now()->format(\DateTime::ATOM)
                ],
                'referencedKeys'   => [
                    'Id',
                    'DeliveryOrderSchedule.RejectReason',
                    'DeliveryOrderSchedule.EstimatedTimeArrival'
                ]
            ]
        ];
    }

    public function actionDetail($Id)
    {
        $possibleProcedures = [
            [
                'New'        => 'Order Baru',
                'Accepted'   => 'Diterima Transportir',
                'OnDelivery' => 'Dalam Perjalanan',
                'Received'   => 'Diterima Pangkalan',
                'Completed'  => 'Selesai'
            ],
            [
                'New'      => 'Order Baru',
                'Rejected' => 'Ditolak Transportir',
            ],
            [
                'New'         => 'Order Baru',
                'Rejected'    => 'Ditolak Transportir',
                'Rescheduled' => 'Dijadwalkan Ulang',
                'Accepted'    => 'Diterima Transportir',
                'OnDelivery'  => 'Dalam Perjalanan',
                'Received'    => 'Diterima Pangkalan',
                'Completed'   => 'Selesai'
            ]
        ];

        $procedures = $possibleProcedures[rand(0, 2)];

        $currentProcedure = Randomizer::valueIn(array_keys($procedures));

        $items   = [];
        $history = [];

        $totalQuantity    = 0;
        $receivedQuantity = null;

        for ($iC = 0; $iC < rand(2, 7); $iC++) {
            $quantity    = rand(20, 100);
            $received    = null;
            $noteReceipt = null;

            if (ArrayHelper::isIn($currentProcedure, ['Received', 'Completed'])) {
                $received         = $quantity - rand(0, $quantity * 0.8);
                $receivedQuantity += $received;
                if ($quantity != $received) {
                    $noteReceipt = Randomizer::name(5);
                }
            }

            $items[] = [
                'Id'          => Randomizer::uuid(),
                'ItemId'      => Randomizer::uuid(),
                'Code'        => Randomizer::code(),
                'Name'        => Randomizer::name(2),
                'Unit'        => 'BO' . Randomizer::valueIn(['3', '6', '12']),
                'Reserved'    => $quantity,
                'Received'    => $received,
                'NoteReceipt' => $noteReceipt,
            ];

            $totalQuantity += $quantity;
        }


        $scheduledAt  = Randomizer::futureDateTime(2, 10, 'days');
        $deliveryOn   = null;
        $ETA          = null;
        $receivingOn  = null;
        $rejectReason = null;

        if (ArrayHelper::isIn($currentProcedure, ['Rejected'])) {
            $rejectReason = Randomizer::name(9);
        }

        $vehicle          = null;
        $transporterName  = null;
        $transporterPhone = null;

        if (ArrayHelper::isIn($currentProcedure, ['OnDelivery'])) {
            $deliveryOn = Carbon::parse($scheduledAt)->addMinutes(rand(5, 30))->format(\DateTime::ATOM);
            $ETA        = Carbon::parse($scheduledAt)->addDays(rand(2, 4))->format(\DateTime::ATOM);

            $vehicle          = Randomizer::licensePlateNumber();
            $transporterName  = Randomizer::name();
            $transporterPhone = Randomizer::phoneNumber();
        }

        $imageUrls = null;

        if (ArrayHelper::isIn($currentProcedure, ['Received', 'Completed'])) {
            $deliveryOn  = Carbon::parse($scheduledAt)->addMinutes(rand(5, 30))->format(\DateTime::ATOM);
            $ETA         = Carbon::parse($scheduledAt)->addDays(rand(2, 4))->format(\DateTime::ATOM);
            $receivingOn = Carbon::parse($ETA)->addHours(rand(0, 24))->format(\DateTime::ATOM);
            $imageUrls   = Randomizer::imageUrls(1, 5);

            $vehicle          = Randomizer::licensePlateNumber();
            $transporterName  = Randomizer::name();
            $transporterPhone = Randomizer::phoneNumber();
        }


        $iZ = 8;
        foreach ($procedures as $key => $string) {
            $createdAt = Carbon::now()
                ->subDays($iZ)
                ->subHour(rand(1, 23))
                ->subMinute(rand(1, 59))
                ->subSeconds(rand(1, 59))
                ->format(\DateTime::ATOM);

            if ($key == 'OnDelivery') {
                $createdAt = $deliveryOn;
            }

            if ($key == 'Received') {
                $createdAt = $receivingOn;
            }

            $history[] = [
                'Procedure'       => $key,
                'ProcedureString' => $string,
                'CreatedAt'       => $createdAt,
            ];

            $iZ--;

            if ($key == $currentProcedure) {
                break;
            }
        }

        $data = [
            'Id'                     => Randomizer::uuid(),
            'Code'                   => Randomizer::code(),
            'TotalReserved'          => $totalQuantity,
            'TotalReceived'          => $receivedQuantity,
            'ReceivedImageUrls'      => $imageUrls,
            'Agent'                  => [
                'Code'         => Randomizer::code(),
                'Name'         => Randomizer::name(rand(2, 3), 'PT.'),
                'PhoneNumber'  => Randomizer::phoneNumber(),
                'Address'      => Randomizer::address(),
                'Neighborhood' => Randomizer::sequenceString(rand(1, 10)),
                'Hamlet'       => Randomizer::sequenceString(rand(1, 10)),

            ],
            'Outlet'                 => [
                'Code'         => Randomizer::code(),
                'Name'         => Randomizer::name(3),
                'PhoneNumber'  => Randomizer::phoneNumber(),
                'ProvinceName' => 'Jawa Timur',
                'RegencyName'  => 'Malang',
                'DistrictName' => 'Lowokwaru',
                'VillageName'  => 'Mojolangu',
                'Address'      => Randomizer::address(),
                'Neighborhood' => Randomizer::sequenceString(rand(1, 10)),
                'Hamlet'       => Randomizer::sequenceString(rand(1, 10)),
                'Latitude'     => Randomizer::double(-8, -7),
                'Longitude'    => Randomizer::double(110, 112),
                'Wide'         => rand(100, 200),
            ],
            'DeliveryOrderSchedule'  => [
                'TransporterName'           => $transporterName,
                'TransporterPhoneNumber'    => $transporterPhone,
                'VehicleLicensePlateNumber' => $vehicle,
                'ScheduledAt'               => $scheduledAt,
                'DeliveryOn'                => $deliveryOn,
                'EstimatedTimeArrival'      => $ETA,
                'ReceivingOn'               => $receivingOn,
                'RejectReason'              => $rejectReason,
            ],
            'Materials'              => $items,
            'Histories'              => $history,
            'CurrentProcedure'       => $currentProcedure,
            'CurrentProcedureString' => $procedures[$currentProcedure],
            'Status'                 => 'Active',
            'CreatedAt'              => Carbon::now()->format(\DateTime::ATOM)
        ];

        return [
            'Name'        => 'Get Delivery Order Detail Success',
            'Message'     => 'Delivery order information loaded successfully',
            'Code'        => 10031100,
            'Status'      => 200,
            'Data'        => $data,
            'Errors'      => [],
            'RequestTime' => Project::getRequestTime(),
            'Meta'        => [],
        ];
    }

}