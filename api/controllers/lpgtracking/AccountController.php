<?php

namespace api\controllers\lpgtracking;

use api\components\HttpException;
use common\actions\MockupFormAction;
use common\helpers\Project;
use common\helpers\Randomizer;

class AccountController extends Controller
{

    public $hasJson = ['change-password'];

    public function actionProfile()
    {
        $data = [
            'User' => [
                'Name'        => Randomizer::name(),
                'Email'       => \Yii::$app->user->identity->Identity,
                'PhoneNumber' => Randomizer::phoneNumber(),
                'Address'     => Randomizer::name(4),
            ],
            'Role' => [
                'Name'            => \Yii::$app->user->identity->Role,
                'AllowedPlatform' => \Yii::$app->user->identity->AllowedPlatform,
            ],
            'Agent'                  => [
                'Code'         => Randomizer::code(),
                'Name'         => Randomizer::name(rand(2, 3), 'PT.'),
                'PhoneNumber'  => Randomizer::phoneNumber(),
                'Address'      => Randomizer::address(),
                'Neighborhood' => Randomizer::sequenceString(rand(1, 10)),
                'Hamlet'       => Randomizer::sequenceString(rand(1, 10)),
            ],
        ];

        if (\Yii::$app->user->identity->Role == 'Outlet') {
            $data['Outlet'] = [
                'Code'         => Randomizer::string(10, 10),
                'Name'         => Randomizer::name(3),
                'PhoneNumber'  => Randomizer::phoneNumber(),
                'ProvinceName' => 'Jawa Timur',
                'RegencyName'  => 'Malang',
                'DistrictName' => 'Lowokwaru',
                'VillageName'  => 'Mojolangu',
                'Address'      => Randomizer::address(),
                'Neighborhood' => Randomizer::string(5),
                'Hamlet'       => Randomizer::string(5),
                'Latitude'     => Randomizer::double(-8, -7),
                'Longitude'    => Randomizer::double(110, 112),
                'Wide'         => rand(100, 200),
            ];

            $outletTypes = [
                'small'  => 'Pangkalan Kecil',
                'medium' => 'Pangkalan Sedang',
                'large'  => 'Pangkalan Besar',
            ];

            foreach ($outletTypes as $type => $typeString) {
                if (rand(0, 1)) {
                    $data['Outlet']['Type']       = $type;
                    $data['Outlet']['TypeString'] = $typeString;
                }
            }
        } else {
            $data['Outlet'] = null;
        }

        return [
            'Name'        => 'Get Profile Success',
            'Message'     => 'Profile loaded successfully.',
            'Code'        => 10020800,
            'Status'      => 200,
            'Data'        => $data,
            'Errors'      => [],
            'RequestTime' => Project::getRequestTime(),
            'Meta'        => [],
        ];
    }

    public function actions()
    {
        return [
            'change-password' => [
                'class'          => MockupFormAction::class,
                'rules'          => [
                    [['OldPassword', 'Password', 'ConfirmPassword'], 'required'],
                    [['OldPassword', 'Password', 'ConfirmPassword'], 'string', 'min' => 8, 'max' => 32],
                    [
                        'ConfirmPassword',
                        'compare',
                        'compareAttribute' => 'Password',
                        'message'          => 'Password confirmation must be same as Password'
                    ],
                    [
                        'OldPassword',
                        'compare',
                        'compareValue' => 'password',
                        'message'      => 'Incorrect Old Password supplied'
                    ]
                ],
                'successName'    => 'Change Password Success',
                'successMessage' => 'Password has changed successfully',
                'successCode'    => 11020900,
                'successData'    => [],
            ]
        ];
    }

    protected function verbs()
    {
        return [
            'profile'         => ['get'],
            'change-password' => ['post'],
        ];
    }
}