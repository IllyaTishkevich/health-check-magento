<?php


namespace app\controllers\apiControllers;

use app\controllers\apiControllers\AbstractApi;
use Yii;
use yii\db\Query;

class Stat extends AbstractApi
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

            if (!isset($params['level'])) {
                return ['error' => 'Level code invalidated'];
            }

            $level = $this->getLevelByCode($params['level']);
            $params['where'] = "`project_id` = '$projectUser->project_id' AND `level_id` = '$level->id'";
            $result = $this->createStat($params);
            return [strtolower($params['level']) => $result];
        } else {
            return ['error' => 'Token invalidated'];
        }
    }

    protected function createStat($params)
    {
        $fromVal = $this->parseDateParam($params['from']);
        $toVal = $this->parseDateParam($params['to']);

        $where = isset($params['where']) ? $params['where']
            . " AND `create` >= '$fromVal' and `create` <= '$toVal'" :
            "`create` >= '$fromVal' AND `create` <= '$toVal'";

        $fields = (new Query())->select('create')->from('message')
            ->where($where)->all();

        $stat = [
            'stat' => [],
            'sets' => []
        ];

        $from = strtotime($fromVal);
        $to = strtotime($toVal);
        if (isset($params['step'])) {
            $step = $params['step'];
        } else {
            $diff = $to - $from;
            if ($diff <= (60 * 30)) {
                $step = 60;
            } elseif ($diff <= (60 * 60  * 3)) {
                $step = 60 * 5;
            } elseif ($diff <= (60 * 60  * 6)) {
                $step = 60 * 10;
            } elseif ($diff <= (60 * 60  * 24)) {
                $step = 60 * 30;
            } elseif ($diff <= (60 * 60 * 24 * 2)) {
                $step = 60 * 60;
            } elseif ($diff <= (60 * 60 * 24 * 7)) {
                $step = 60 * 60 * 4;
            } elseif ($diff <= (60 * 60 * 24 * 30)) {
                $step = 60 * 60 * 24;
            } else {
                $step = 60 * 60 * 24 * 2;
            }
        }
        $stat['sets']['step'] = $step;
        $max = 0;

        for ($i = $from; $i + $step <= $to; $i += $step) {
            $elem['label'] = date("Y-m-d H:i:s", $i) . ' - ' . date("Y-m-d H:i:s", $i + $step);
            $inRange = array_filter($fields, function ($count) use ($i, $step) {
                $timestamp = strtotime($count['create']);
                return $i <= $timestamp && $timestamp <= $i + $step;
            });
            $elem['count'] = count($inRange);;
            $stat['stat'][] = $elem;

            if ($elem['count'] > $max) {
                $max = $elem['count'];
            }
        }
        $stat['sets']['max'] = $max;
        return $stat;
    }
}