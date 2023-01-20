<?php


namespace app\controllers;

use yii\httpclient\Client;
use yii\web\Controller;
use Yii;
use yii\web\Response;


class TrackerController extends Controller
{
    public function actionScript() {
        Yii::$app->response->format = Response::FORMAT_RAW;
        $request = Yii::$app->request;
        $content = $this->getView()->render('script', [], $this);
        echo '<pre>';
        $ip = $_SERVER['REMOTE_ADDR'];
        var_dump($this->getRequestIp());
        die();
        return $content;
    }

    protected function getRequestIp()
    {
        $request = Yii::$app->request;
        $cdn = $request->headers->get('cdn-loop');
        if ($cdn) {
            $ip = $request->headers->get('cf-connecting-ip');
        } else {
            $ip = $request->getUserIP();
        }

        return $ip;
    }
}