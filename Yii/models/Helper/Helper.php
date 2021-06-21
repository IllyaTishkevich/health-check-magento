<?php


namespace app\models\Helper;


class Helper
{
    public static function displayOnGrid($message)
    {
        $result = '';

        try {
            $messageArray = json_decode($message);
            if(isset($messageArray->message)) {
                if(is_array($messageArray->message)) {
                    $result = \yii\helpers\StringHelper::truncate(
                        implode($messageArray->message, PHP_EOL),
                        64
                    );
                } else {
                    $result = $result = \yii\helpers\StringHelper::truncate(
                        $messageArray->message,
                        64
                    );
                }
            } else {
                $result = \yii\helpers\StringHelper::truncate($message, 64);
            }
        } catch (\Exception $e) {
            $result = \yii\helpers\StringHelper::truncate($message, 64);
        }
        return $result;
    }
}