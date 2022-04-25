<?php


namespace app\models\Notifications;

use Yii;

class NotifyEmail extends \yii\base\BaseObject implements \app\models\Notifications\NotifyModelInterface
{

    /**
     * глобаный метод который вызывается при нотификации.
     * получает массив данных нужных для конкретной натификации.
     * например для почты это [
     *      'message' => *текст сообщения*,
     *      'emails' => [
     *          'mail1@mail.ru',
     *          'mail2@gmail.com'
     *      ]
     * ]
     *
     * @param array $data
     * @return mixed
     */
    public function notify(array $data)
    {
        if (isset($data['emails']) && !empty($data['emails'])) {
            foreach ($data['emails'] as  $k => $email) {
                Yii::$app->mailer->compose()
                    ->setFrom('from@domain.com')
                    ->setTo($email)
                    ->setSubject('HealthCheck Notify')
                    ->setTextBody($data['message'])
//                    ->setHtmlBody('<b>текст сообщения в формате HTML</b>')
                    ->send();
            }
        }
    }
}