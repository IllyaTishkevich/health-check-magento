<?php


namespace app\cron;
use app\framework\cron\AbstractJob;
use app\models\Sites;
use yii\httpclient\Client;
use yii\httpclient\Exception;


class CheckGit extends AbstractJob
{

    public function execute()
    {
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

        return true;
    }
}