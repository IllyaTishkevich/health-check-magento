<?php


namespace app\models\Helper;


class Helper
{
    public static function displayOnGrid($message, $length = 64)
    {
        $result = '';

        try {
            $messageArray = json_decode($message);
            if(isset($messageArray->message)) {
                if(is_array($messageArray->message)) {
                    $result = \yii\helpers\StringHelper::truncate(
                        implode($messageArray->message, PHP_EOL),
                        $length
                    );
                } else {
                    $result = $result = \yii\helpers\StringHelper::truncate(
                        $messageArray->message,
                        $length
                    );
                }
            } else {
                $result = \yii\helpers\StringHelper::truncate($message, $length);
            }
        } catch (\Exception $e) {
            $result = \yii\helpers\StringHelper::truncate($message, $length);
        }
        return $result;
    }
}