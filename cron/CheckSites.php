<?php


namespace app\cron;

use app\framework\ConfigManager;
use app\framework\cron\AbstractJob;
use app\models\Level;
use app\models\Message;
use app\models\Project;
use yii\httpclient\Client;
use yii\httpclient\Exception;

class CheckSites extends AbstractJob
{
    const LEVEL_CODE_STATUS = 'SERVER_STATUS';

    public function execute()
    {
        $projects = Project::find()->all();
        $configManager = new ConfigManager();
        foreach ($projects as $project) {
            $enableServerCheck = $configManager->getConfigSet(ConfigManager::ENABLE_SERVER_CHECK, $project->id);
            if (!$project->active || !$enableServerCheck) {
                continue;
            }

            $url = $project->url;
            if (substr($url, -1) !== '/') {
                $url = $url . '/';
            }

            $client = new Client();
            try {
                $response = $client->createRequest()->setMethod('GET')->setUrl(
                    $url . 'health_check.php'
                )->send();

                if ($response->isOk) {
                    return true;
                } else {
                    $client = new Client();
                    $response = $client->createRequest()->setMethod('GET')->setUrl(
                        $project->url . 'customer/account/login/'
                    )->send();
                    if ($response->isOk) {
                        return true;
                    } else {
                        if($response->getStatusCode() != 503) {
                            $this->processMessage($url,
                                $project,
                                'app',
                                $response->getContent(),
                                self::LEVEL_CODE_STATUS,
                                $response->getStatusCode());
                            echo $project->url . " : Host is not alive\r\n";
                        }
                    }
                }
            } catch (Exception $e) {
                //todo need real ip
                echo $e->getMessage().PHP_EOL;
                $this->processMessage($url, $project, 'themself',$e->getMessage(), self::LEVEL_CODE_STATUS);
            }
        }

        return true;
    }

    private function processMessage($url, $project, $ip, $messageString, $levelKey, $response = '')
    {
        $projectId = $project->getAttribute('id');
        $level = Level::find()->where(['key' => $levelKey])->one();
        if ($level == null) {
            $level = new Level();
            $level->key = $levelKey;
            $level->save();
        }
        $levelId = $level->getAttribute('id');

        If ($projectId) {
            $message             = new Message();
            $message->project_id = (int)$projectId;
            $message->level_id   = (int)$levelId;
            $message->ip         = $ip;
            $message->create     = date('Y-m-d H:i:s');
            $message->message    = json_encode([[
                'message' => $messageString,
                'url' => $url,
                'error' => $response
            ]]);
            $message->save();
        }
    }
}