<?php


namespace app\commands;

use app\framework\cron\CronManager;
use yii\console\Controller;
use yii\base\ErrorException;
use Yii;

/**
 * Cron Comands List
 *
 * Class CheckController
 * @package app\commands
 */

class CronController extends Controller
{
    public function __construct($id, $module, $config = [])
    {
        parent::__construct($id, $module, $config);
    }

    /**
     * Run cron schdulles
     *
     * @throws \yii\base\InvalidConfigException
     */
    public function actionRun()
    {
        $manager = new CronManager();
        $list = $manager->getList();
        Yii::info('Cron run.', 'cron');
        foreach ($list as $job) {
            try {
                if ($job::isEnable()) {
                    $manager->executeJob($job);
                }
            } catch (\Exception $exception) {
                Yii::error($exception->getMessage(), 'cron');
            } catch (ErrorException $exception) {
                Yii::error($exception->getMessage(), 'cron');
            }
        }

    }
}