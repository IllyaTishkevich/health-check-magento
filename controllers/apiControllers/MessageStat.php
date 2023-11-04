<?php


namespace app\controllers\apiControllers;

use app\models\Level;
use app\models\Message;
use Yii;
use app\controllers\apiControllers\Stat;

class MessageStat extends Stat
{

    public function execute()
    {
        $request = Yii::$app->request;
        $params = $request->get();

        if (isset($params['token'])) {
            $projectUser = $this->getProjectUserByToken($params['token']);

            if (isset($params['id'])) {
                $originMessage = Message::find()
                    ->andWhere(['=', 'project_id', $projectUser->project_id])
                    ->andWhere(['=', 'id', $params['id']])->one();
            }

            if ($projectUser == null) {
                return ['error' => 'Token invalidated'];
            }

            if (!$originMessage) {
                return ['error' => 'Something went wrong.'];
            }

            $level = Level::find()->where(['id' =>  $originMessage->level_id])->one();

            $params['where'] = "`project_id` = '$projectUser->project_id' AND `level_id` = '$originMessage->level_id' 
                    AND `message` =  '$originMessage->message'";

            return [strtolower($level->key) => $this->createStat($params)];
        } else {
            return ['error' => 'Token invalidated'];
        }
    }
}