<?php


namespace app\controllers\apiControllers;

use app\models\Project;
use app\models\ProjectUser;
use app\models\User;
use Yii;
use app\controllers\apiControllers\AbstractApi;

class SignIn extends AbstractApi
{

    public function execute($params)
    {
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
}