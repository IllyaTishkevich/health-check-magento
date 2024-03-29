<?php


namespace app\controllers\apiControllers\notification;

use app\models\LevelNotification;
use Yii;
use app\controllers\apiControllers\AbstractApi;

class Remove extends AbstractApi
{

    public function execute($params)
    {
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
}