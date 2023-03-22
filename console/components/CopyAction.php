<?php
/**
 * Created by PhpStorm.
 * User: Nadzif Glovory
 * Date: 3/8/2020
 * Time: 1:05 AM
 */

namespace console\components;


use Carbon\Carbon;
use common\helpers\Generator;
use yii\db\Connection;
use yii\db\Exception;
use yii\db\IntegrityException;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

class CopyAction extends \yii\base\Action
{
    /** @var Connection */
    public $dbOrigin;
    /** @var Connection */
    public $dbDestination;

    public $tableOrigin;
    public $tableDestination;

    public $attributePlacements = [];

    /**
     * 'replace'             => [
     * 'mobile' => ['/\D/', '']
     * ]
     *
     */
    public $replace = [];
    /** [$columnName => $Length] */
    public $generatedColumns    = [];
    public $timestampAttributes = [];
    public $timestampFormat     = 'Y-m-d H:i:s';

    public function run()
    {
        $items = (new Query())->select('*')
            ->from($this->tableOrigin)
            ->all($this->dbOrigin);

        $total = count($items);

        $connection = $this->dbDestination;

        $timestamp = Carbon::now()->format($this->timestampFormat);

        $success = 0;
        $failed  = 0;
        foreach ($items as $index => $item) {
            Generator::log(\Yii::getAlias('@console') . DIRECTORY_SEPARATOR . 'runtime' . DIRECTORY_SEPARATOR
                . 'logs' . DIRECTORY_SEPARATOR . 'migrate-progress.log', $this->tableOrigin . ' ' . $index . PHP_EOL);

            $attributes = [];
            foreach ($this->attributePlacements as $origin => $destination) {
                $data = $item[$origin];
                if ($replaceString = ArrayHelper::getValue($this->replace, $origin)) {
                    $data = preg_replace($replaceString[0], $replaceString[1], $data);
                }

                $attributes[$destination] = $data;
            }

            foreach ($this->generatedColumns as $columnName => $length) {
                $attributes[$columnName] = \Yii::$app->security->generateRandomString($length);
            }

            foreach ($this->timestampAttributes as $timestampAttribute) {
                $attributes[$timestampAttribute] = $timestamp;
            }

            try {
                $connection->createCommand()->insert($this->tableDestination, $attributes)->execute();
                $success++;
//                echo 'success migrating ' . $this->tableOrigin . ' ' . $success . '/' . $total . PHP_EOL;

            } catch (IntegrityException $e) {
                $failed++;
                Generator::log(\Yii::getAlias('@console') . DIRECTORY_SEPARATOR . 'runtime' . DIRECTORY_SEPARATOR
                    . 'logs' . DIRECTORY_SEPARATOR . 'migrate.log', $e->getMessage());
            } catch (Exception $e) {
                $failed++;
                Generator::log(\Yii::getAlias('@console') . DIRECTORY_SEPARATOR . 'runtime' . DIRECTORY_SEPARATOR
                    . 'logs' . DIRECTORY_SEPARATOR . 'migrate.log', $e->getMessage());
            }

        }

        $history = $this->tableDestination . ' RECORD(' . $total . ') FAILED(' . $failed . ')' . PHP_EOL;

        Generator::log(\Yii::getAlias('@console') . DIRECTORY_SEPARATOR . 'runtime' . DIRECTORY_SEPARATOR
            . 'logs' . DIRECTORY_SEPARATOR . 'history.log', $history);

        echo $history;
    }


}