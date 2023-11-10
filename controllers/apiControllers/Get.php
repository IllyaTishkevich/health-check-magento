<?php


namespace app\controllers\apiControllers;

use app\models\Level;
use app\models\LevelNotification;
use app\models\Message;
use app\models\Notification;
use app\models\Project;
use app\models\ProjectUser;
use app\models\User;
use Yii;
use app\controllers\apiControllers\AbstractApi;
use yii\data\Pagination;
use app\framework\ConfigManager;

class Get extends AbstractApi
{

    public function execute($params)
    {
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
                if ($pUser->add != null || $project->owner == $user->id) {
                    $users[] = [
                        'id' => $user->id,
                        'role' => $project->owner == $user->id ? 'Owner' : 'User',
                        'email' => $user->email
                    ];
                }
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
            $result = [
                'id' => $project->id,
                'url' => $project->url,
                'name' => $project->name,
                'auth_key' => $project->auth_key,
                'owner' => $user->email,
            ];

            $configManager = new ConfigManager();
            $config = $configManager->getConfigList($project->id);
            return array_merge($result, $config);
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
}