<?php

namespace app\controllers;


use app\models\ProjectUser;
use yii\web\Controller;
use Yii;


class StatController extends Controller {

    public function actionIndex()
    {
        if (Yii::$app->user->isGuest) {
            return $this->response->redirect('/site/login');
        }
        $this->setToken();
        return $this->render('index');
    }

    public function actionItem()
    {
        if (Yii::$app->user->isGuest) {
            return $this->response->redirect('/site/login');
        }
        $this->setToken();
        return $this->render('item');
    }

    public function getToken()
    {
        $key = $this->generateKey(16);

        $projectId = $this->getCurrentProject();
        if($projectId !== null) {
            $projectUser = ProjectUser::find()
                ->where(['user_id' => Yii::$app->user->getIdentity()->getId(), 'project_id' => $projectId])
                ->one();
            $projectUser->token = $key;
            $projectUser->save();
        }

        return $key;
    }

    protected function generateKey($length = 16) {
        $max = ceil($length / 40);
        $random = '';
        for ($i = 0; $i < $max; $i ++) {
            $random .= sha1(microtime(true).mt_rand(10000,90000));
        }
        return substr($random, 0, $length);
    }

    protected function getCurrentProject()
    {
        return Yii::$app->user->getIdentity()->getAttribute('active_project');
    }

    protected function setToken()
    {
        $cookies = Yii::$app->response->cookies;
        $cookies->remove('magenmagic-hc-token');
        $cookies->add(new \yii\web\Cookie([
            'name' => 'magenmagic-hc-token',
            'value' => $this->getToken(),
            'httpOnly' => false
        ]));
    }
}
