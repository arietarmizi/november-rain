<?php

namespace console\controllers;

use common\models\Admin;
use common\models\SystemApp;
use common\models\SystemConfig;
use common\models\User;
use yii\console\Controller;
use yii\rbac\Role;
use yii\helpers\Console;
use console\setup\Development;
use common\models\Company;

class SetupController extends Controller
{

    public function actionIndex()
    {
        $this->actionDummy();
        $this->actionAdmin();
        $this->actionSystemApp();
    }


    public function actionDummy()
    {

        $company = new Company([
            'name'        => 'Default',
            'description' => 'Default',
        ]);
        $company->save();


        $user = new User([
            'companyId'    => $company->id,
            'type'         => User::TYPE_PUBLIC,
            'phoneNumber'  => '081212121212',
            'name'         => 'User Test',
            'email'        => 'test@company.com',
            'verified'     => 1,
            'passwordHash' => '$2y$13$hS23TJQ3FqzU6LDUP6oL0.55309PQbmSkS6KNI0ohyXdRFG6QrGp.'
        ]);
        $user->save();

        $company = new Company([
            'name'        => 'PT Membangun Indonesia',
            'description' => 'MBI',
        ]);
        $company->save();

    }

    public function actionAdmin()
    {

        $this->stdout("\nCreating roles...\n", Console::FG_BLUE);
        /** @var Role[] $admins */
        $roles = Development::createRole();

        foreach ($roles as $role) {
            $this->stdout($role->name . " role created!...\n", Console::FG_GREEN);
        }

        $this->stdout("\nCreating admins...\n", Console::FG_BLUE);
        /** @var Admin[] $admins */
        $admins = Development::createAdmin();

        foreach ($admins as $admin) {
            $this->stdout($admin->name . " admin created!...\n", Console::FG_GREEN);
        }
    }

    public function actionSystemApp()
    {
        $this->stdout("Prepare init data for development...\n");

        $this->stdout("\nCreating a new SystemApp...\n", Console::FG_BLUE);
        /** @var SystemApp $systemApp */
        $systemApp = Development::createSystemApp();
        $this->stdout($systemApp->name . " system app created!...\n", Console::FG_GREEN);

    }

}
