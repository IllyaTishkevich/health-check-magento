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

    public function actionRun($url = '')
    {
        if (!$url) {
            return ExitCode::NOINPUT;
        }

        $client = new Client();
        try {
            $response = $client->createRequest()->setMethod('GET')->setUrl(
                $url . '/health_check.php'
            )->send();
            if ($response->isOk) {
                echo "ok\r\n";
            } else {
                //todo need real ip
                $this->processMessage($url, '127.0.0.1');
                echo "Host is not alive\r\n";
            }
        } catch (Exception $e) {
            //todo need real ip

            $this->processMessage($url, '127.0.0.1');
        }
    }

    private function processMessage($url, $ip)
    {
        $project = Project::find()->where(['project_url' => $url])->one();
        if (!$project) {
            echo "Can't find the project \r\n";

            return ExitCode::NOINPUT;
        }
        $projectId = $project->getAttribute('id');
        $level     = Level::find()->where(['key' => 'error'])->one();
        if ($level == null) {
            $level      = new Level();
            $level->key = "error";
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