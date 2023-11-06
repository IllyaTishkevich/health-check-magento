<?php


namespace app\controllers\apiControllers\user;

use app\models\Project;
use app\models\ProjectUser;
use Yii;
use app\controllers\apiControllers\AbstractApi;

class Remove extends AbstractApi
{

    public function execute($params)
    {
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
}