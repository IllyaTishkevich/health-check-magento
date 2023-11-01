<?php


namespace app\controllers\apiControllers\notification;

use app\models\LevelNotification;
use Yii;
use \app\controllers\apiControllers\AbstractApi;

class Save extends AbstractApi
{

    public function execute()
    {
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
}