<?php


namespace app\models\Helper;


class Helper
{
    public static function displayOnGrid($message)
    {
        $result = '';

        try {
            $messageArray = json_decode($message);
            $result = isset($messageArray->message)
                ? $messageArray->message
                : \yii\helpers\StringHelper::truncate($message, 64);

        } catch (\Exception $e) {
            $result = \yii\helpers\StringHelper::truncate($message, 64);
        }
        return $result;
    }
}