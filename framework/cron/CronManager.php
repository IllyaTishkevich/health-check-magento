<?php


namespace app\framework\cron;

use app\framework\cron\AbstractJob;

class CronManager
{
    protected $list = false;

    public function getList()
    {
        if ($this->list == false) {
            $this->list = $this->loadJobsModels();
        }

        return $this->list;
    }

    protected function loadJobsModels()
    {
        $files = scandir(__DIR__ . '/../../cron/');
        $list = [];
        foreach ($files as $file) {
            if(preg_match('/\.(php)/', $file)){
                $classNamespace = 'app\cron\\' . str_replace('.php', '', $file);
                $class = new $classNamespace();
                if (is_subclass_of($class, '\app\framework\cron\AbstractJob')) {
                    $list[] = $class;
                }
            }
        }

        return $list;
    }

    public function executeJob(\app\framework\cron\AbstractJob $job)
    {
        if ($job::isEnable()) {
            return $job->execute();
        } else {
            return false;
        }
    }
}