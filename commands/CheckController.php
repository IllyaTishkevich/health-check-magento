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

class CheckController extends Controller
{
    const LEVEL_CODE = 'SERVER_STATUS';

    public function actionIndex($message = 'hello world')
    {
        // Сообщение
        $message = "Line 1\r\nLine 2\r\nLine 3";

        // На случай если какая-то строка письма длиннее 70 символов мы используем wordwrap()
        $message = wordwrap($message, 70, "\r\n");

        // Отправляем
        mail('relikt.ilya@mail.ru', 'My Subject', $message);
        echo('df');
        return ExitCode::OK;
    }

    public function actionServers()
    {
        $projects = Project::find()->all();
        foreach ($projects as $project) {
            if (!$project->active || !$project->enable_server_check) {
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
                    echo "ok".PHP_EOL;
                } else {
                    //todo need real ip
                    $this->processMessage($url,
                        $project,
                        'themself',
                        $response->getStatusCode() . ' : '. $response->getContent());
                    echo "Host is not alive\r\n";
                }
            } catch (Exception $e) {
                //todo need real ip
                echo $e->getMessage().PHP_EOL;
                $this->processMessage($url, $project, 'themself',$e->getMessage());
            }
        }
    }

    private function processMessage($url, $project, $ip, $response = '')
    {
        $projectId = $project->getAttribute('id');
        $level = Level::find()->where(['key' => self::LEVEL_CODE])->one();
        if ($level == null) {
            $level = new Level();
            $level->key = self::LEVEL_CODE;
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
                'message' => 'Server not available.',
                'url' => $url,
                'error' => $response
            ]]);
            $message->save();
        }
    }
}