<?php

namespace app\controllers;

use app\models\ProjectUser;
use Yii;
use app\models\Message;
use app\models\Level;
use app\models\Project;
use app\models\ProjectSearch;
use app\models\MessageSearch;
use yii\db\Exception;
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

        $functionName = strtolower($post['level']) . 'Log';
        if (method_exists($this, $functionName)) {
            return $this->$functionName($post, $AuthKey);
        } else {
            return $this->defaultLog($post, $AuthKey);
        }
    }

    protected function salescheckLog($post, $AuthKey)
    {
        $project = Project::find()->where(['auth_key' => $AuthKey])->one();
        if (!$project) {
            return ['status' => 'Failed authorisation'];
        }
        $projectId = $project->getAttribute('id');

        $levelKey = $post['level'];
        $level = Level::find()->where(['key' => $levelKey])->one();
        if($level == null) {
            $level = new Level();
            $level->key = $post['level'];
            $level->save();
        }

        $body = json_decode($post['data']);
        if ($this->isAnyMessage($body)) {
            $levelId = $level->getAttribute('id');
            if ($levelId && $projectId) {
                foreach ($body as $itemMessage) {
                    $message = new Message();

                    $message->project_id = $projectId;
                    $message->level_id = $levelId;
                    $message->ip = $post['ip'];
                    $message->message = json_encode($itemMessage);
                    $message->create = $itemMessage->date;
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
                $message->create = $body->date;
                $message->save();
            }
        }

        return ['status' => 'success'];
    }


    protected function defaultLog($post, $AuthKey)
    {
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
            $levelId = $level->getAttribute('id');
            if ($levelId && $projectId) {
                foreach ($body as $itemMessage) {
                    $message = new Message();

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

    public function actionGet()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $request = Yii::$app->request;
        $params = $request->get();

        switch ($params['entity']) {
            case 'messages':
                return $this->getMessages($params);
            case 'project':
                return $this->getProject($params);
            case 'levels':
                return $this->getLevels($params);
            default :
                return ['status' => 'Entity not specified or does not exist.'];
        }

    }

    protected function getLevels($params)
    {
        if (isset($params['token'])) {
            $projectUser = $this->getProjectUserByToken($params['token']);
            if ($projectUser == null) {
                return ['status' => 'Token invalidated'];
            }
            $messages = Message::find()->distinct(true)->select('level_id')->where(['project_id' => $projectUser->project_id])->all();

            $ids = [];
            foreach ($messages as $message)
            {
                $ids[] = $message->level_id;
            }

            return Level::find()->where(['in', 'id', $ids])->all();

        } else {
            return ['status' => 'Token invalidated'];
        }
    }

    protected function getProject($params)
    {
        if (isset($params['token'])) {
            $projectUser = $this->getProjectUserByToken($params['token']);
            if ($projectUser == null) {
                return ['status' => 'Token invalidated'];
            }
            return Project::find()->where(['id' => $projectUser->project_id])->one();

        } else {
            return ['status' => 'Token invalidated'];
        }
    }

    protected function getMessages($params)
    {
        if (isset($params['token'])) {
            $projectUser = $this->getProjectUserByToken($params['token']);
            $filer = [];

            if ($projectUser == null) {
                return ['status' => 'Token invalidated'];
            } else {
                $filer['project_id'] = $projectUser->project_id;
            }

            if ($params['level'] !== '') {
                $level = $this->getLevelByCode($params['level']);

                if ($level == null) {
                    return ['status' => 'Level code invalidated'];
                } else {
                    $filer['level_id'] = $level->id;
                }
            }

            $messageRepo = Message::find()->where($filer);
            if($params['count'] != 0 && $params['page'] !== 0) {
                $cloned = clone $messageRepo;

                $pages = new Pagination(['totalCount' => $cloned->count(), 'pageSize' => $params['count']]);
                $pages->pageSizeParam = false;
                $messages = $messageRepo->offset($pages->offset)
                    ->limit($pages->limit)
                    ->all();
            } else {
                $messages = $messageRepo->all();
            }
            return $messages;
        } else {
            return ['status' => 'Token invalidated'];
        }
    }

    protected function isAnyMessage($data)
    {
        if (is_array($data)) {
            foreach ($data as $item) {
                if (!is_array($item)) {
                    return false;
                }
            }
        } else {
            return false;
        }

        return true;
    }

    protected function getProjectUserByToken($token)
    {
        return ProjectUser::find()
            ->where(['token' => $token])
            ->one();
    }

    protected function getLevelByCode($level)
    {
        return Level::find()
            ->where(['like', 'key', $level])
            ->one();
    }
}
