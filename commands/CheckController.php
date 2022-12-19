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
use app\models\Sites;

/**
 * Ckecker Comands List
 *
 * Class CheckController
 * @package app\commands
 */
class CheckController extends Controller
{
    const LEVEL_CODE_STATUS = 'SERVER_STATUS';

    const LEVEL_CODE_GIT = 'GIT_CONFIG_AVAILABLE';

    public function aactionDiscordTest()
    {
        $webhookurl = "https://discord.com/api/webhooks/1048348071249055824/3VN32bdWXA5UBauxGEQSaKgcjCaIMkjx0zW9CRdztr9wJcoVr0sA_CrkpX1WwmPJF6YL";
        $json_data = json_encode([
            "content" => "Hello World! This is message line ;) And here is the mention, use userID <@12341234123412341>",
            "tts" => false,

        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );


        $ch = curl_init( $webhookurl );
        curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
        curl_setopt( $ch, CURLOPT_POST, 1);
        curl_setopt( $ch, CURLOPT_POSTFIELDS, $json_data);
        curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt( $ch, CURLOPT_HEADER, 0);
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1);

        $response = curl_exec( $ch );
        curl_close( $ch );
    }

    public function aactionIndex($message = 'hello world')
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

    /**
     * Сheck if URLs of all projects are available
     *
     * @throws \yii\base\InvalidConfigException
     */
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
                    $client = new Client();
                    $response = $client->createRequest()->setMethod('GET')->setUrl(
                        $project->url
                    )->send();
                    if ($response->isOk) {
                        echo "ok".PHP_EOL;
                    } else {
                        if($response->getStatusCode() != 503) {
                            $this->processMessage($url,
                                $project,
                                'themself',
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
    }

    /**
     * Сheck if URLs of sites list are available
     *
     * @throws \yii\base\InvalidConfigException
     */
    public function actionSitesList() {
        $sites = Sites::find()->where(['cron_status' => Sites::PENDING])->limit(10)->all();
        foreach ($sites as $site) {
            $client = new Client();
            try {
                $url = $site->site_url;
                if (substr($url, -1) !== '/') {
                    $url = $url . '/';
                }
                $response = $client->createRequest()->setMethod('GET')->setUrl(
                    $url . '.git/config'
                )->send();

                $site->status_code = $response->statusCode;
                $site->cron_status = Sites::COMPILTE;
                if ($response->isOk) {
                    $content = $response->content;
                    if (str_contains($content, 'repositoryformatversion')
                        || str_contains($content, '[remote "origin"]')) {
                        $site->available = '.git';
                    }
                }
                $site->save();
            } catch (Exception $e) {
                $site->cron_status = Sites::ERROR;
                $site->save();
                echo $e->getMessage().PHP_EOL;
            }
        }

    }

    /**
     * Check if file .git/config are available
     *
     * @throws \yii\base\InvalidConfigException
     */
    public function actionGitConfig()
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
                    $url . '.git/config'
                )->send();

                if ($response->isOk) {
                    $this->processMessage($url,
                        $project,
                        'themself',
                        'File .git/config is available.',
                        self::LEVEL_CODE_GIT,
                        $response->getStatusCode());
                }
            } catch (Exception $e) {
                echo $e->getMessage().PHP_EOL;
            }
        }
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