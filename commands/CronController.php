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
                $result = $manager->executeJob($job);
                if ($result) {
                    Yii::info(get_class($job) . ' was complite.', 'cron');
                } else {
                    Yii::info(get_class($job) . ' was falsed.', 'cron');
                }
            } catch (\Exception $exception) {
                Yii::error($exception->getMessage(), 'cron');
            } catch (ErrorException $exception) {
                Yii::error($exception->getMessage(), 'cron');
            }
        }

    }
}