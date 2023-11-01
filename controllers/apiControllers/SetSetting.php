<?php


namespace app\controllers\apiControllers;

use app\models\Project;
use Yii;
use app\controllers\apiControllers\AbstractApi;

class SetSetting extends AbstractApi
{

    public function execute()
    {
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
}