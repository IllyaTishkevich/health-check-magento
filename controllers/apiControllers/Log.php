<?php


namespace app\controllers\apiControllers;

use app\framework\ConfigManager;
use app\models\JsMessage;
use app\models\Level;
use app\models\Message;
use app\models\Project;
use app\controllers\apiControllers\AbstractApi;
use Yii;
use yii\web\Response;

class Log extends AbstractApi
{
    protected $messageFilterArrayString = null;

    public function execute($params)
    {
        if (!isset($params['token'])) {
            return ['error' => 'Authorisation Needed'];
        } else {
            $AuthKey = $params['token'];
        }

        $post = $params;
        if (str_starts_with(strtolower($post['level']), 'js_')) {
            $functionName = strtolower($post['level']) . 'JsLog';
            $functionName = str_replace('_', '', $functionName);
            $post['ip'] = $this->getRequestIp();
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

    protected function defaultLog($post, $AuthKey)
    {
        $project = Project::find()->where(['auth_key' => $AuthKey])->one();

        if (!$project) {
            return ['error' => 'Failed authorisation'];
        }
        $projectId = $project->getAttribute('id');

        if (isset($post['data'])) {
            if (!$this->validate($post['data'], $projectId)) {
                return ['status' => 'Did not pass the filter'];
            }
        }

        $levelKey = $post['level'];
        $level = Level::find()->where(['key' => $levelKey])->one();

        if($level == null) {
            $level = new Level();
            $level->key = $post['level'];
            $level->save();
        }

        if ($post['level'] == 'PLACEORDER') {
            ob_start();
            var_dump($post['data']);
            $trace = ob_get_clean();
            file_put_contents('placeorder.txt',$trace);
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
                    $message->create = isset($itemMessage->created_at)
                        ? $itemMessage->created_at : date('Y-m-d H:i:s');
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

        if (isset($post['message'])) {
            if (!$this->validate($post['data'], $projectId)) {
                return ['status' => 'Did not pass the filter'];
            }
        }

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

    protected function salescheckLog($post, $AuthKey)
    {
        $project = Project::find()->where(['auth_key' => $AuthKey])->one();
        if (!$project) {
            return ['error' => 'Failed authorisation'];
        }
        $projectId = $project->getAttribute('id');

        if (isset($post['data'])) {
            if (!$this->validate($post['data'], $projectId)) {
                return ['status' => 'Did not pass the filter'];
            }
        }

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

    protected function validate($message, $projectId)
    {
        $filtersStrings = $this->getMessageFilters($projectId);

        foreach ($filtersStrings as $string) {
            if (stripos($message, $string) !== false) {
                return false;
            }
        }

        return true;
    }

    protected function getMessageFilters($projectId)
    {
        if (!isset($this->messageFilterArrayString)) {
            $configManager = new ConfigManager();
            $filterString = $configManager->getConfigSet(ConfigManager::MESSAGE_FILTER, $projectId);
            $this->messageFilterArrayString = explode('|', $filterString);
        }

        return $this->messageFilterArrayString;
    }
}