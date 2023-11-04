<?php


namespace app\framework;

use Yii;
use yii\db\Query;

class ConfigManager
{
    const MESSAGE_FILTER = 'message_filter';

    public function getConfigSet($path, $projectId = null)
    {
        if(!isset($projectId)) {
            $projectId = Yii::$app->user->getIdentity()->getAttribute('active_project');
        }

        $field = (new Query())->select('value')->from('setting')
            ->where("`path` = '$path' AND `project_id` = '$projectId'") ->one();

        return $field ? $field['value'] : false;
    }

    public function setConfigSet($path, $value, $projectId = null)
    {
        if(!isset($projectId)) {
            $projectId = Yii::$app->user->getIdentity()->getAttribute('active_project');
        }

        $field = (new Query())->select('id')->from('setting')
            ->where("`path` = '$path' AND `project_id` = '$projectId'") ->one();

        try {
            if ($field) {
                (new Query())->createCommand()->update('setting',
                    [
                        'value' => $value,
                        'update' => date('Y-m-d H:i:s')
                    ],
                    "id = " . $field['id']
                )->execute();

                return $value;
            } else {
                (new Query())->createCommand()->insert('setting',
                    [
                        'path' => $path,
                        'value' => $value,
                        'project_id' => $projectId,
                        'create' => date('Y-m-d H:i:s'),
                        'update' => date('Y-m-d H:i:s')
                    ]
                )->execute();

                return $value;
            }
        } catch (\Exception $exception) {
            Yii::error($exception->getMessage());
        }

        return false;
    }

    public function getConfigList($projectId = null)
    {
        if(!isset($projectId)) {
            $projectId = Yii::$app->user->getIdentity()->getAttribute('active_project');
        }

        $fields = (new Query())->select('path, value')->from('setting')
            ->where("`project_id` = '$projectId'") ->all();

        $list = [];
        foreach ($fields as $field) {
            $list[$field['path']] = $field['value'];
        }
        return $list;
    }
}