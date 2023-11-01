<?php

namespace app\controllers;

use app\controllers\apiControllers\Daystat;
use app\controllers\apiControllers\Savenotification;
use app\models\LevelNotification;
use app\models\Notification;
use app\models\ProjectUser;
use app\models\User;
use Yii;
use app\models\Message;
use app\models\JsMessage;
use app\models\Level;
use app\models\Project;
use app\models\ProjectSearch;
use app\models\MessageSearch;
use yii\base\ViewRenderer;
use yii\db\Exception;
use yii\db\Query;
use yii\helpers\Url;
use yii\rest\ActiveController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\data\Pagination;

/**
 * ApiController implements the CRUD actions for Message model.
 */
class ApiController extends ActiveController
{
    public $modelClass = 'app\models\Message';

    const API_NAMESPACE = '\app\controllers\apiControllers\\';

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'options' => [
                'class' => 'yii\rest\OptionsAction',
            ],
        ];
    }

    /**
     * Finds the Message model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id
     *
     * @return Message the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Message::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        Yii::$app->response->headers->set('Access-Control-Allow-Origin', '*');
        Yii::$app->response->headers->set('Access-Control-Allow-Headers', '*');
        return $behaviors;
    }

    public function actionIndex()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $params = Yii::$app->request->get();
        if (isset($params['action'])) {
            $class = static::API_NAMESPACE . $params['action'];
            $model = new $class();
            return $model->execute();
        } else {
            return ['error' => 'Something went wrong'];
        }

    }
    public function actionSavenotification()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $model = new \app\controllers\apiControllers\notification\Save();
        return $model->execute();
    }

}
