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
        var_dump($ip);
        var_dump($request->getUserIP());
        die();
        return $content;
    }
}