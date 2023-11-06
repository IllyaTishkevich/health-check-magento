<?php


namespace app\controllers\apiControllers;

use app\controllers\apiControllers\Stat;
use app\models\Message;
use Yii;

class DayStat extends Stat
{
    public function execute()
    {
        $request = Yii::$app->request;
        $params = $request->get();
        $params['token'] = Yii::$app->request->headers->get('Authentication-Key');

        if (isset($params['token'])) {
            $projectUser = $this->getProjectUserByToken($params['token']);

            $messageRepo = Message::find();

            if ($projectUser == null) {
                return ['error' => 'Token invalidated'];
            } else {
                $filter = ['=', 'project_id', $projectUser->project_id];
                $messageRepo->andWhere($filter);
            }
            $from = gmdate("Y-m-d H:i:s",time());
            $to = gmdate("Y-m-d H:i:s",$params['to']);
            $filter = ['>=', 'create', $from];
            $messageRepo->andWhere($filter);
            $filter = ['<=', 'create', $to];
            $messageRepo->andWhere($filter);
            $messages = $messageRepo->all();

            return [strtolower($params['level']) => $this->createStat($messages, $params)];
        } else {
            return ['error' => 'Token invalidated'];
        }
    }
}