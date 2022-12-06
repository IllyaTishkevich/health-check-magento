<?php


namespace app\controllers;

use yii\httpclient\Client;
use yii\web\Controller;
use Yii;


class ToolsController extends Controller
{
    public function actionList()
    {
        return $this->render('list');
    }

    public function actionGit()
    {
        $request = Yii::$app->request;
        $params = $request->post();

        $content = false;
        $message = false;
        $url ='';
        if (isset($params['url'])) {
            try {
                $url = rtrim($params['url'], '/');
                $client = new Client();
                $response = $client->createRequest()->setMethod('GET')->setUrl(
                    $url . '/.git/config'
                )->send();
                $content = $response->getContent();
                if ($response->isOk) {
                    $message = '<div class="alert alert-danger" role="alert"><span class="glyphicon glyphicon-remove-circle" aria-hidden="true"></span>File .git/config is avaible. Fix it!</div>';
                } else {
                    $message = '<div class="alert alert-success" role="alert"><span class="glyphicon glyphicon-ok-circle" aria-hidden="true"></span>All right, baby!</div>';
                }
            } catch (\Exception $e) {
                $message = '<div class="alert alert-danger" role="alert"><span class="glyphicon glyphicon-remove-circle" aria-hidden="true"></span>Url not Avaible.</div>';
            }
        }

        return $this->render('git', [
            'content' => $content,
            'message' => $message
        ]);
    }
}
