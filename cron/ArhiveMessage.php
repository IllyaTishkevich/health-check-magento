<?php


namespace app\cron;
use app\framework\ConfigManager;
use app\framework\cron\AbstractJob;
use app\models\Project;
use yii\db\Query;

class ArhiveMessage extends AbstractJob
{
    protected static $schedule = '0 4 * * *';

    public function execute()
    {
        $projects = Project::find()->all();
        $configManager = new ConfigManager();

        foreach ($projects as $project) {
            $dayCount = $configManager->getConfigSet(ConfigManager::ARHIVE_DAY_COUNT, $project->id);
            if ($dayCount) {
                $date = new \DateTime('-' . $dayCount . ' days');
                $dateFormatedQuery = $date->format('Y-m-d H:i:s');
                $rows = (new Query())->select('`l`.`key`, `m`.`message`, `m`.`create`, `m`.`ip`')
                    ->from('`message` as m')
                    ->leftJoin('`level` as l', '`m`.`level_id` = `l`.`id`')
                    ->where("`project_id` = '".$project->id."' and `create` < '".$dateFormatedQuery."'") ->all();
                $csvArray = [];
                foreach ($rows as $row) {
                    $csvArray[$row['key']][] = $row;
                }

                foreach ($csvArray as $key => $item) {
                    $buffer = fopen(__DIR__ . '/../archive/temp/'.$key.'.csv', 'w');
                    fputs($buffer, chr(0xEF) . chr(0xBB) . chr(0xBF));
                    foreach($item as $message) {
                        fputcsv($buffer, $message, ';');
                    }
                    fclose($buffer);
                }

                $dateFormatedFile = $date->format('Y-m-d-H:i:s');
                $name = str_replace(' ', '-',$project->name);
                $archiveString =  __DIR__ . '/../archive/temp';
                $result = shell_exec('cd '.$archiveString.' &&  tar -zcvf ./../'.$name.'-'.$dateFormatedFile.'.tar.gz ./');

                (new Query())->createCommand()->delete('message',
                    "`project_id` = '".$project->id."' and `create` < '".$dateFormatedQuery."'")->execute();
            }
        }

        return true;
    }
}