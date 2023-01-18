<?php

namespace app\controllers;

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
        return $behaviors;
    }

    public function actionLog()
    {
        $request                    = Yii::$app->request;
        $post                       = $request->post();
        $headers                    = $request->headers;
        $AuthKey                    = $headers->get('Authentication-Key');
        Yii::$app->response->format = Response::FORMAT_JSON;

        if (!$AuthKey) {
            return ['error' => 'Authorisation Needed'];
        }

        if (str_starts_with(strtolower($post['level']), 'js_')) {
            $functionName = strtolower($post['level']) . 'JsLog';
            $functionName = str_replace('_', '', $functionName);
            $post['ip'] = $request->getUserIp();
            if (method_exists($this, $functionName)) {
                return $this->$functionName($post, $AuthKey);
            } else {
                return $this->defaultJsLog($post, $AuthKey);
            }
        } else {
            $functionName = strtolower($post['level']) . 'Log';

            if (method_exists($this, $functionName)) {
                return $this->$functionName($post, $AuthKey);
            } else {
                return $this->defaultLog($post, $AuthKey);
            }
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
                return ['error' => 'Token invalidated'];
            } else {
                $filter = ['=', 'project_id', $projectUser->project_id];
                $messageRepo->andWhere($filter);
            }

            if (isset($params['level'])) {
                $level = $this->getLevelByCode($params['level']);
                $filter = ['=', 'level_id', $level->id];
                $messageRepo->andWhere($filter);
            } else {
                return ['error' => 'Level code invalidated'];
            }

            return [strtolower($params['level']) => $this->createStat($messageRepo, $params)];
        } else {
            return ['error' => 'Token invalidated'];
        }
    }

    public function actionDaystat()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $request = Yii::$app->request;
        $params = $request->get();

        if (isset($params['token'])) {
            $projectUser = $this->getProjectUserByToken($params['token']);

            $messageRepo = Message::find();

            if ($projectUser == null) {
                return ['error' => 'Token invalidated'];
            } else {
                $filter = ['=', 'project_id', $projectUser->project_id];
                $messageRepo->andWhere($filter);
            }


            $from = gmdate("Y-m-d H:i:s",time());

            $to = gmdate("Y-m-d H:i:s",$params['to']);
            $filter = ['>=', 'create', $from];
            $messageRepo->andWhere($filter);
            $filter = ['<=', 'create', $to];
            $messageRepo->andWhere($filter);

            $messages = $messageRepo->all();

            return [strtolower($params['level']) => $this->createStat($messages, $params)];
        } else {
            return ['error' => 'Token invalidated'];
        }
    }

    public function actionSavenotification()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            $request = Yii::$app->request;
            $params = $request->get();
            if (isset($params['token'])) {
                $projectUser = $this->getProjectUserByToken($params['token']);
                $paramsInput = (array)json_decode(file_get_contents('php://input'));
                $paramsPost = $request->post();
                if (count($paramsInput) > 0 && count($paramsPost) == 0) {
                    $params = $paramsInput;
                } else {
                    $params = $paramsPost;
                }

                if (isset($params['id'])) {
                    $notif = LevelNotification::findOne(['id' => $params['id']]);
                    if ($notif && $params['project_id'] == $projectUser->project_id) {
                        $notif->notification_id = $params['notification_id'];
                        $notif->settings = $params['settings'];
                        $notif->active = $params['active'];
                    } else {
                        return ['error' => 'id invalidated'];
                    }
                } else {
                    $notif = new LevelNotification();
                    $notif->notification_id = $params['notification_id'];
                    $notif->settings = $params['settings'];
                    $notif->active = $params['active'];
                    $notif->level_id = $params['level_id'];
                    $notif->project_id = $projectUser->project_id;
                }

                $notif->save();
                return ['id' => $notif->id];
            } else {
                return ['error' => 'Token invalidated'];
            }
    }

    public function actionRemoveuser()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $request = Yii::$app->request;
        $params = $request->get();
        if (isset($params['token'])) {
            $projectUser = $this->getProjectUserByToken($params['token']);
            $project = Project::findOne(['id' => $projectUser->project_id]);

            if ($projectUser->user_id == $project->owner && $project->owner != $params['id']) {
                $projectUser = ProjectUser::find()
                    ->andWhere(['=', 'project_id', $project->id])
                    ->andWhere(['=', 'user_id', $params['id']])
                    ->one();
                $projectUser->delete();
            } else {
                return ['error' => 'You can\'t do this'];
            }
        } else {
            return ['error' => 'Token invalidated'];
        }
    }

    public function actionAdduser()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $request = Yii::$app->request;
        $params = $request->get();
        if (isset($params['token'])) {
            $projectUser = $this->getProjectUserByToken($params['token']);
            $project = Project::findOne(['id' => $projectUser->project_id]);

            if ($projectUser->user_id == $project->owner) {
                $user = User::findOne(['email' => $params['email']]);
                $projectUserNew = new ProjectUser();
                $projectUserNew->project_id = $projectUser->project_id;
                $projectUserNew->user_id = $user->id;
                $projectUserNew->save();
                $user->active_project = $projectUser->project_id;
                $user->save();
                return ['status' => 'ok'];
            } else {
                return ['error' => 'You can\'t do this'];
            }
        } else {
            return ['error' => 'Token invalidated'];
        }
    }

    public function actionRemovenotification()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $request = Yii::$app->request;
        $params = $request->get();
        if (isset($params['token'])) {
            $projectUser = $this->getProjectUserByToken($params['token']);
            $notif = LevelNotification::findOne(['id' => $params['id']]);

            if ($notif->project_id == $projectUser->project_id) {
                $notif->delete();
                return ;
            } else {
                return ['error' => 'Something went wrong'];
            }
        } else {
            return ['error' => 'Token invalidated'];
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
                return ['error' => 'Token invalidated'];
            } else {
                $filter = ['=', 'project_id', $projectUser->project_id];
                $messageRepo->andWhere($filter);
            }

            if ($originMessage) {
                $filter = ['=', 'level_id', $originMessage->level_id];
                $messageRepo->andWhere($filter);
            } else {
                return ['error' => 'Level code invalidated'];
            }

            if ($originMessage) {
                $filter = ['like', 'message', $originMessage->message];
                $messageRepo->andWhere($filter);
            } else {
                return ['error' => 'Something went wrong'];
            }

            $level = Level::find()->where(['id' =>  $originMessage->level_id])->one();
            return [strtolower($level->key) => $this->createStat($messageRepo, $params)];
        } else {
            return ['error' => 'Token invalidated'];
        }
    }

    public function actionGet()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $params = Yii::$app->request->get();

        switch ($params['entity']) {
            case 'messages':
                return $this->getMessages($params);
            case 'project':
                return $this->getProject($params);
            case 'levels':
                return $this->getLevels($params);
            case 'notifications':
                return $this->getNotifications($params);
            case 'senders':
                return $this->getSenders($params);
            case 'users':
                return $this->getUsers($params);
            default :
                return ['error' => 'Entity not specified or does not exist.'];
        }

    }

    public function actionSignin()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $request = Yii::$app->request;
        $params = $request->get();

        $user = User::findOne(['username' => $params['login']]);
        $result = ['error' => 'Something went wrong'];
        if($user && $user->validatePassword($params['password'])) {
            $project = Project::findOne(['auth_key' => $params['key']]);
            if ($project) {
                $projectUser = ProjectUser::findOne(['user_id' => $user->id, 'project_id' => $project->id]);
                $key = $this->generateKey(16);
                $projectUser->token = $key;
                $projectUser->save();
                $result = ['token' => $projectUser->token];
                }
        }
        return $result;
    }

    public function actionSetsetting()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $request = Yii::$app->request;
        $params = $request->get();

        if (isset($params['token'])) {
            $projectUser = $this->getProjectUserByToken($params['token']);
            if ($projectUser == null) {
                return ['error' => 'Token invalidated'];
            }

            $project = Project::findOne(['id' => $projectUser->project_id]);
            $project[$params['entity']] = $params['value'];
            $project->save();

            return ['stat' => 'ok'];
        } else {
            return ['error' => 'Token invalidated'];
        }

    }

    protected function createStat($messagesRepo, $params)
    {
        $fromVal = $this->parseDateParam($params['from']);
        $toVal = $this->parseDateParam($params['to']);
        $from = strtotime($fromVal);
        $to = strtotime($toVal);
        $stat = [
            'stat' => [],
            'sets' => []
        ];
        if (isset($params['step'])) {
            $step = $params['step'];
        } else {
            $diff = $to - $from;
            if ($diff <= (60 * 30)) {
                $step = 60;
            } elseif ($diff <= (60 * 60  * 3)) {
                $step = 60 * 5;
            } elseif ($diff <= (60 * 60  * 6)) {
                $step = 60 * 10;
            } elseif ($diff <= (60 * 60  * 24)) {
                $step = 60 * 30;
            } elseif ($diff <= (60 * 60 * 24 * 2)) {
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
        if ($from + $step < $to) {
            for ($i = $from; $i + $step <= $to; $i += $step) {
                $elem['label'] = date("Y-m-d H:i:s", $i) . ' - ' . date("Y-m-d H:i:s", $i + $step);
                $newRepo = clone $messagesRepo;

                $fromStep = date("Y-m-d H:i:s", $i);
                $toStep = date("Y-m-d H:i:s", $i + $step);
                $filter = ['>=', 'create', $fromStep];
                $newRepo->andWhere($filter);
                $filter = ['<=', 'create', $toStep];
                $newRepo->andWhere($filter);

                $counter = $newRepo->count();
                $elem['count'] = $counter;
                if ($counter > $max) {
                    $max = $counter;
                }
                $stat['stat'][] = $elem;
            }
        } else {
            $elem['label'] = date("Y-m-d H:i:s", $from) . ' - ' . date("Y-m-d H:i:s", $to);
            $counter = 0;
            $messages = $messagesRepo->all();
            foreach ($messages as $message) {
                $timestamp = strtotime($message->create);
                if ($timestamp >= $from && $timestamp <= $to) {
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
            return ['error' => 'Failed authorisation'];
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
            return ['error' => 'Failed authorisation'];
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

    protected function defaultJsLog($post, $AuthKey)
    {
        $project = Project::find()->where(['auth_key' => $AuthKey])->one();
        if (!$project) {
            return ['error' => 'Failed authorisation'];
        }
        $projectId = $project->getAttribute('id');

        $levelKey = str_replace('JS_', '', $post['level']);
        $level = Level::find()->where(['key' => $levelKey])->one();
        if($level == null) {
            $level = new Level();
            $level->key = $levelKey;
            $level->save();
        }

        $message = new JsMessage();
        $levelId = $level->getAttribute('id');

        if ($levelId && $projectId) {
            $message->level_id = $levelId;
            $message->ip = $post['ip'];
            $message->message = $post['message'];
            $message->trace = $post['trace'];
            $message->events = json_encode($post['events']);
            $message->create = date('Y-m-d H:i:s');
            $message->user_id = $post['user-id'];
            $message->user_agent = $post['agent'];
            $message->url = str_replace($project->url, '', $post['url']);
            $message->save();
        }
        return ['id' => $message->id];
    }

    protected function getLevels($params)
    {
        if (isset($params['token'])) {
            if ($params['token'] !== 'all') {
                $projectUser = $this->getProjectUserByToken($params['token']);
                if ($projectUser == null) {
                    return ['error' => 'Token invalidated'];
                }
                $messages = Message::find()->distinct(true)->select('level_id')->where(['project_id' => $projectUser->project_id])->all();

                $ids = [];
                foreach ($messages as $message) {
                    $ids[] = $message->level_id;
                }

                return Level::find()->where(['in', 'id', $ids])->all();
            } else {
                return Level::find()->all();
            }
        } else {
            return ['error' => 'Token invalidated'];
        }
    }

    protected function getNotifications($params)
    {
        if (isset($params['token'])) {
            $projectUser = $this->getProjectUserByToken($params['token']);

            if ($projectUser == null) {
                return ['error' => 'Token invalidated'];
            }

            $notifications = LevelNotification::findAll(['project_id' => $projectUser->project_id]);
            return ['row' => $notifications];
        } else {
            return ['error' => 'Token invalidated'];
        }
    }

    protected function getUsers($params)
    {
        if (isset($params['token'])) {
            $projectUser = $this->getProjectUserByToken($params['token']);
            $project = $project = Project::find()->where(['id' => $projectUser->project_id])->one();
            if ($projectUser == null) {
                return ['error' => 'Token invalidated'];
            }
            $projectUsers = ProjectUser::findAll(['project_id' => $projectUser->project_id]);
            $users = [];
            foreach ($projectUsers as $pUser) {
                $user = User::findOne(['id' => $pUser->user_id]);
                $users[] = [
                    'id' => $user->id,
                    'role' => $project->owner == $user->id ? 'Owner' : 'User',
                    'email' => $user->email
                ];
            }
            return $users;
        } else {
            return ['error' => 'Token invalidated'];
        }
    }

    protected function getProject($params)
    {
        if (isset($params['token'])) {
            $projectUser = $this->getProjectUserByToken($params['token']);
            if ($projectUser == null) {
                return ['error' => 'Token invalidated'];
            }
            $project = Project::find()->where(['id' => $projectUser->project_id])->one();
            $user = User::findOne(['id' => $project->owner]);
            return [
                'id' => $project->id,
                'url' => $project->url,
                'name' => $project->name,
                'auth_key' => $project->auth_key,
                'owner' => $user->email,
                'gmt' => $project->gmt,
                'enable_server_check' => $project->enable_server_check
            ];
        } else {
            return ['error' => 'Token invalidated'];
        }
    }

    protected function getSenders($params)
    {
        if (isset($params['token'])) {
            $projectUser = $this->getProjectUserByToken($params['token']);
            if ($projectUser == null) {
                return ['error' => 'Token invalidated'];
            }
            return Notification::find()->all();

        } else {
            return ['error' => 'Token invalidated'];
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
                return ['error' => 'Token invalidated'];
            } else {
                $filter = ['=', 'project_id', $projectUser->project_id];
                $messageRepo->andWhere($filter);
            }
            if (isset($params['level'])) {
                $level = $this->getLevelByCode($params['level']);

                if ($level == null) {
                    return ['error' => 'Level code invalidated'];
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
                    $from = $this->parseDateParam($params['from']);
                    $to = $this->parseDateParam($params['to']);
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
                    ->orderBy(['id' => SORT_DESC])
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
            return ['error' => 'Token invalidated'];
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

    protected function parseDateParam($string)
    {
        return str_replace('p', ':',
                    str_replace('d', '-',
                        str_replace('T', ' ',$string)
                    )
        );
    }

    protected function generateKey($length = 16) {
        $max = ceil($length / 40);
        $random = '';
        for ($i = 0; $i < $max; $i ++) {
            $random .= sha1(microtime(true).mt_rand(10000,90000));
        }
        return substr($random, 0, $length);
    }
}
