<?php

namespace app\controllers;


use yii\web\Controller;



class StatController extends Controller {

    public function actionIndex()
    {
        return $this->render('index');
    }


}
