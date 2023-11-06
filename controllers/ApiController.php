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

    /**
     * запускает контроллеры из app\controllers\ApiControllers
     * класс контроллера указывается в config\web.php
     * $config = [
     *      'UrlManager' => [
     *          'rules' => [
     *              [
     *                  'defaults' => ['action' => CLASSNAME]
     * @return string[]
     */
    public function actionRun()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $params = $this->collectRequestParams();
        if (isset($params['action'])) {
            $class = static::API_NAMESPACE . $params['action'];
            $model = new $class();
            return $model->execute($params);
        } else {
            return ['error' => 'Something went wrong'];
        }

    }

    protected function collectRequestParams()
    {
        $request = Yii::$app->request;
        $getParams = $request->get();
        $post = $request->post();
        $paramsInput = (array)json_decode(file_get_contents('php://input'));
        $token = $request->headers->get('Authentication-Key');

        $params = array_merge($getParams, $post, $paramsInput);
        $params['token'] = $token;

        return $params;
    }
}
