<?php

namespace app\controllers;

use Yii;
use app\models\Message;
use app\models\Level;
use app\models\Project;
use app\models\ProjectSearch;
use app\models\MessageSearch;
use yii\helpers\Url;
use yii\rest\ActiveController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * ApiController implements the CRUD actions for Message model.
 */
class ApiController extends ActiveController
{
    public $modelClass = 'app\models\Message';

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [];
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

    public function actionLog()
    {

        $request                    = Yii::$app->request;
        $post                       = $request->post();
        $headers                    = $request->headers;
        $AuthKey                    = $headers->get('Authentication-Key');
        Yii::$app->response->format = Response::FORMAT_JSON;


        if (!$AuthKey) {
            return ['status' => 'Authorisation Needed'];
        }

        $project = Project::find()->where(['auth_key' => $AuthKey])->one();
        if (!$project) {
            return ['status' => 'Failed authorisation'];
        }
        $projectId = $project->getAttribute('id');

        $levelKey = $post['level'];
        $level = Level::find()->where(['key' => $levelKey])->one();
        if($level == null) {
            $level      = new Level();
            $level->key = $post['level'];
            $level->save();
        }

        $body = json_decode($post['data']);
        if ($this->isAnyMessage($body)) {
            foreach ($body as $itemMessage) {
                $message = new Message();
                $levelId = $level->getAttribute('id');

                if ($levelId && $projectId) {
                    $message->project_id = $projectId;
                    $message->level_id = $levelId;
                    $message->ip = $post['ip'];
                    $message->message = json_encode($itemMessage);
                    $message->create = date('Y-m-d H:i:s');
                    $message->save();
                }
            }
        } else {
            $message = new Message();
            $levelId = $level->getAttribute('id');

            if ($levelId && $projectId) {
                $message->project_id = $projectId;
                $message->level_id = $levelId;
                $message->ip = $post['ip'];
                $message->message = $post['data'];
                $message->create = date('Y-m-d H:i:s');
                $message->save();
            }
        }

        return ['status' => 'success'];
    }

    protected function isAnyMessage($data)
    {
        if (is_array($data)) {
            foreach ($data as $item) {
                if (!is_object($item)) {
                    return false;
                }
            }
        } else {
            return false;
        }

        return true;
    }
}
