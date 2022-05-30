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

    public function actionStat()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $request = Yii::$app->request;
        $params = $request->get();

        if (isset($params['token'])) {
            $projectUser = $this->getProjectUserByToken($params['token']);

            $messageRepo = Message::find();

            if ($projectUser == null) {
                return ['status' => 'Token invalidated'];
            } else {
                $filter = ['=', 'project_id', $projectUser->project_id];
                $messageRepo->andWhere($filter);
            }

            if (isset($params['level'])) {
                $level = $this->getLevelByCode($params['level']);
                $filter = ['=', 'level_id', $level->id];
                $messageRepo->andWhere($filter);
            } else {
                return ['status' => 'Level code invalidated'];
            }

            if (isset($params['from']) && isset($params['to'])) {
                $from = gmdate("Y-m-d H:i:s",$params['from']);
                $to = gmdate("Y-m-d H:i:s",$params['to']);
                $filter = ['>=', 'create', $from];
                $messageRepo->andWhere($filter);
                $filter = ['<=', 'create', $to];
                $messageRepo->andWhere($filter);
            } else {
                return ['status' => 'timestamp invalidated'];
            }

            $messages = $messageRepo->all();

            return [strtolower($params['level']) => $this->createStat($messages, $params)];
        } else {
            return ['status' => 'Token invalidated'];
        }
    }

    public function actionMesstat() {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $request = Yii::$app->request;
        $params = $request->get();

        if (isset($params['token'])) {
            $projectUser = $this->getProjectUserByToken($params['token']);

            $messageRepo = Message::find();

            if (isset($params['id'])) {
                $originMessage = Message::find()
                    ->andWhere(['=', 'project_id', $projectUser->project_id])
                    ->andWhere(['=', 'id', $params['id']])->one();
            }

            if ($projectUser == null) {
                return ['status' => 'Token invalidated'];
            } else {
                $filter = ['=', 'project_id', $projectUser->project_id];
                $messageRepo->andWhere($filter);
            }

            if ($originMessage) {
                $filter = ['=', 'level_id', $originMessage->level_id];
                $messageRepo->andWhere($filter);
            } else {
                return ['status' => 'Level code invalidated'];
            }

            if ($originMessage) {
                $filter = ['like', 'message', $originMessage->message];
                $messageRepo->andWhere($filter);
            } else {
                return ['status' => 'Something went wrong'];
            }

            if (isset($params['from']) && isset($params['to'])) {
                $from = gmdate("Y-m-d H:i:s",$params['from']);
                $to = gmdate("Y-m-d H:i:s",$params['to']);
                $filter = ['>=', 'create', $from];
                $messageRepo->andWhere($filter);
                $filter = ['<=', 'create', $to];
                $messageRepo->andWhere($filter);
            } else {
                return ['status' => 'timestamp invalidated'];
            }

            $messages = $messageRepo->all();

            $level = Level::find()->where(['id' =>  $originMessage->level_id])->one();
            return [strtolower($level->key) => $this->createStat($messages, $params)];
        } else {
            return ['status' => 'Token invalidated'];
        }
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

    protected function createStat($messages, $params)
    {
        $from = $params['from'];
        $to = $params['to'];

        $stat = [
            'stat' => [],
            'sets' => []
        ];
        if (isset($params['step'])) {
            $step = $params['step'];
        } else {
            $diff = $to - $from;
            if ($diff <= (60 * 60 * 24 * 2)) {
                $step = 60 * 60;
            } elseif ($diff <= (60 * 60 * 24 * 7)) {
                $step = 60 * 60 * 4;
            } elseif ($diff <= (60 * 60 * 24 * 30)) {
                $step = 60 * 60 * 24;
            } else {
                $step = 60 * 60 * 24 * 2;
            }
        }
        $stat['sets']['step'] = $step;
        $max = 0;
        for ($i = $from; $i + $step < $to; $i += $step) {
            $elem['label'] =  date("Y-m-d H:i:s",$i) . ' - ' . date("Y-m-d H:i:s",$i+$step);
            $counter = 0;
            foreach ($messages as $message) {
                $timestamp = strtotime($message->create);
                if ($timestamp >= $i && $timestamp <= $i + $step) {
                    $counter++;
                }
            }
            $elem['count'] = $counter;
            if ($counter > $max) {
                $max = $counter;
            }
            $stat['stat'][] = $elem;
        }
        $stat['sets']['max'] = $max;
        return $stat;
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
                    $message->ip = $itemMessage->ip;
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
                $message->ip = $body->ip;
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
            $result = [
                'rows' => [],
                'pagination' => []
            ];

            $projectUser = $this->getProjectUserByToken($params['token']);
            $filter = [];

            $messageRepo = Message::find();

            if ($projectUser == null) {
                return ['status' => 'Token invalidated'];
            } else {
                $filter = ['=', 'project_id', $projectUser->project_id];
                $messageRepo->andWhere($filter);
            }
            if (isset($params['level'])) {
                $level = $this->getLevelByCode($params['level']);

                if ($level == null) {
                    return ['status' => 'Level code invalidated'];
                } else {
                    $filter = ['=', 'level_id', $level->id];
                    $messageRepo->andWhere($filter);
                }
            }

            if (isset($params['id'])) {
                $filter = ['=', 'id', $params['id']];
                $messageRepo->andWhere($filter);
            } else {
                if (isset($params['from']) && isset($params['to'])) {
                    $from = gmdate("Y-m-d H:i:s", $params['from']);
                    $to = gmdate("Y-m-d H:i:s", $params['to']);
                    $filter = ['>=', 'create', $from];
                    $messageRepo->andWhere($filter);
                    $filter = ['<=', 'create', $to];
                    $messageRepo->andWhere($filter);
                }
            }

            if (isset($params['ip'])) {
                $filter = ['like', 'ip', $params['ip']];
                $messageRepo->andWhere($filter);
            }

            if(isset($params['message'])) {
                $filter = ['like', 'message', $params['message']];
                $messageRepo->andWhere($filter);
            }

            if(isset($params['count']) && isset($params['page'])) {
                $cloned = clone $messageRepo;

                $pages = new Pagination(['totalCount' => $cloned->count(), 'pageSize' => $params['count']]);

                $pages->pageSizeParam = false;
                $messages = $messageRepo->offset($pages->offset)
                    ->limit($pages->limit)
                    ->all();

                $pageCount = ceil($pages->totalCount / $params['count']);

                $result['pagination'] = [
                    'count' => $params['count'],
                    'page' => $params['page'],
                    'pageCount' => $pageCount,
                    'prevPage' => $params['page'] != 1 ? $params['page'] - 1 : false,
                    'nextPage' => $params['page'] < $pageCount ? $params['page'] + 1 : false
                ];
            } else {
                $messages = $messageRepo->all();
            }
            $result['rows'] = $messages;

            return $result;
        } else {
            return ['status' => 'Token invalidated'];
        }
    }

    protected function isAnyMessage($data)
    {
        return is_array($data);
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
