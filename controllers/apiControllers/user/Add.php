<?php


namespace app\controllers\apiControllers\user;

use app\models\Project;
use app\models\ProjectUser;
use app\models\User;
use Yii;
use app\controllers\apiControllers\AbstractApi;

class Add extends AbstractApi
{

    public function execute($params)
    {
        if (isset($params['token'])) {
            $projectUser = $this->getProjectUserByToken($params['token']);
            $project = Project::findOne(['id' => $projectUser->project_id]);

            if ($projectUser->user_id == $project->owner) {
                $user = User::findOne(['email' => $params['email']]);
                $projectUserNew = new ProjectUser();
                $projectUserNew->project_id = $projectUser->project_id;
                $projectUserNew->user_id = $user->id;
                $projectUserNew->add = $projectUser->user_id;
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
}