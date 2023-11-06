<?php


namespace app\controllers\apiControllers;

use app\framework\ConfigManager;
use app\models\Project;
use Yii;
use app\controllers\apiControllers\AbstractApi;

class SetSetting extends AbstractApi
{

    public function execute()
    {
        $request = Yii::$app->request;
        $params = $request->get();
        $params['token'] = Yii::$app->request->headers->get('Authentication-Key');

        if (isset($params['token'])) {
            $projectUser = $this->getProjectUserByToken($params['token']);
            if ($projectUser == null) {
                return ['error' => 'Token invalidated'];
            }

            $project = Project::findOne(['id' => $projectUser->project_id]);
            if($project->hasAttribute( $params['entity'])) {
                $project[$params['entity']] = $params['value'];
                $project->save();
            } else {
                $configManager = new ConfigManager();
                $result = $configManager->setConfigSet($params['entity'], $params['value'], $project->id);
                if ($result) {
                    return ['stat' => 'ok'];
                } else {
                    return ['error' => 'Something went wrong.'];
                }
            }

            return ['stat' => 'ok'];
        } else {
            return ['error' => 'Token invalidated'];
        }
    }
}