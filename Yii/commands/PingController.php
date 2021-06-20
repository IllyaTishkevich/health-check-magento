<?php

namespace app\commands;

use yii\console\Controller;
use yii\console\ExitCode;
use yii\httpclient\Client;
use app\models\Message;
use app\models\Level;
use app\models\Project;
use app\models\ProjectSearch;
use app\models\MessageSearch;
use yii\httpclient\Exception;

class PingController extends Controller
{
    public function actionIndex($message = 'hello world')
    {
        echo $message . "\n";

        return ExitCode::OK;
    }

    public function actionRun()
    {
        $projects = Project::find()->all();

        foreach ($projects as $project) {
            $url = $project->url;

            $client = new Client();
            try {
                $response = $client->createRequest()->setMethod('GET')->setUrl(
                    $url . '/health_check.php'
                )->send();
                if ($response->isOk) {
                    echo "ok\r\n";
                } else {
                    //todo need real ip
                    $this->processMessage($url, $project,'127.0.0.1');
                    echo "Host is not alive\r\n";
                }
            } catch (Exception $e) {
                //todo need real ip

                $this->processMessage($url, '127.0.0.1');
            }
        }
    }

    private function processMessage($url, $project, $ip)
    {
        $projectId = $project->getAttribute('id');
        $level     = Level::find()->where(['key' => 'error_health_check'])->one();
        if ($level == null) {
            $level      = new Level();
            $level->key = "error_health_check";
            $level->save();
        }
        $levelId = $level->getAttribute('id');

        If ($projectId) {
            $message             = new Message();
            $message->project_id = (int)$projectId;
            $message->level_id   = (int)$levelId;
            $message->ip         = $ip;
            $message->message    = "Host is not alive";
            $message->create     = date('Y-m-d H:i:s');
            $message->save();
        }
    }
}