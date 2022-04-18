<?php


namespace app\models\Notifications;


interface NotifyModelInterface
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

    public function notify(array $data);
}