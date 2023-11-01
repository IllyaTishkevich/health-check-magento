<?php


namespace app\controllers\apiControllers;


use app\models\Level;
use app\models\ProjectUser;

abstract class AbstractApi
{
    public abstract function execute();

    protected function isAnyMessage($data)
    {
        return is_array($data);
    }

    protected function getRequestIp()
    {
        $request = Yii::$app->request;
        $cdn = $request->headers->get('cdn-loop');
        if ($cdn) {
            $ip = $request->headers->get('cf-connecting-ip');
        } else {
            $ip = $request->getUserIP();
        }

        return $ip;
    }

    protected function generateKey($length = 16) {
        $max = ceil($length / 40);
        $random = '';
        for ($i = 0; $i < $max; $i ++) {
            $random .= sha1(microtime(true).mt_rand(10000,90000));
        }
        return substr($random, 0, $length);
    }

    protected function getProjectUserByToken($token)
    {
        return ProjectUser::find()
            ->where(['token' => $token])
            ->one();
    }

    protected function getLevelByCode($level)
    {
        return Level::find()
            ->where(['like', 'key', $level])
            ->one();
    }

    protected function parseDateParam($string)
    {
        return str_replace('p', ':',
            str_replace('d', '-',
                str_replace('T', ' ',$string)
            )
        );
    }
}