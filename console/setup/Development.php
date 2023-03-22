<?php

namespace console\setup;

use common\models\Admin;
use common\models\SystemApp;
use common\models\SystemConfig;
use yii\base\BaseObject;
use yii\rbac\DbManager;

class Development extends BaseObject
{

    public static function createSystemApp()
    {
        $userService            = new SystemApp();
        $userService->name      = 'Android System App';
        $userService->appKey    = 'LMSAndroidKey';
        $userService->appSecret = 'LMSAndroidSecret';
        $userService->status    = SystemApp::STATUS_ACTIVE;
        $userService->type      = SystemApp::TYPE_DEVICE;
        $userService->save();

        return $userService;
    }

    public static function createSystemConfig($key, $value)
    {
        $systemComfig        = new SystemConfig();
        $systemComfig->key   = $key;
        $systemComfig->value = $value;
        $systemComfig->save();

        return $systemComfig;
    }

    public static function createRole()
    {
        /** @var DbManager $authManager */
        $authManager = \Yii::$app->authManager;

        $superuser              = $authManager->createRole(Admin::ROLE_SUPERUSER);
        $superuser->description = 'Site Superuser';

        $administrator              = $authManager->createRole(Admin::ROLE_ADMINISTRATOR);
        $administrator->description = 'Site Administrator';

        $supervisor              = $authManager->createRole(Admin::ROLE_SUPERVISOR);
        $supervisor->description = 'Site Supervisor';

        $authManager->add($superuser);
        $authManager->add($administrator);
        $authManager->add($supervisor);

        return [$superuser, $administrator, $supervisor];
    }

    public static function createAdmin()
    {
        /** @var DbManager $authManager */
        $authManager       = \Yii::$app->authManager;
        $roleSuperuser     = $authManager->getRole(Admin::ROLE_SUPERUSER);
        $roleAdministrator = $authManager->getRole(Admin::ROLE_ADMINISTRATOR);
        $roleSupervisor    = $authManager->getRole(Admin::ROLE_SUPERVISOR);

        $superuser        = new Admin();
        $superuser->name  = 'Superuser PTPL';
        $superuser->email = 'nsm@company.com';
        $superuser->setPassword('password');
        $superuser->phoneNumber = '081111111111';
        $superuser->save();

        $authManager->assign($roleSuperuser, $superuser->id);

        $administrator        = new Admin();
        $administrator->name  = 'Admin PTPL';
        $administrator->email = 'nsm-administrator@company.com';
        $administrator->setPassword('password');
        $administrator->phoneNumber = '082222222222';
        $administrator->save();

        $authManager->assign($roleAdministrator, $administrator->id);

        $supervisor        = new Admin();
        $supervisor->name  = 'Supervisor PTPL';
        $supervisor->email = 'nsm-supervisor@company.com';
        $supervisor->setPassword('password');
        $supervisor->phoneNumber = '083333333333';
        $supervisor->save();

        $authManager->assign($roleSupervisor, $supervisor->id);

        return [$superuser, $administrator, $supervisor];
    }

}
