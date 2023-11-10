<?php


namespace app\models\Notifications;


use app\models\LevelNotification;
use app\models\Message;
use app\models\Notification;

class Handler
{
    public static function notify(Message $message, LevelNotification $levelNotification)
    {
        $notification = Notification::findOne([$levelNotification->notification_id]);
        if ($notification) {
            $notificationModel = new $notification->object_namespace();
            $data = json_decode($levelNotification->settings, true);
            $data['message'] = $message->message;
            $notificationModel->notify($data);
        }
    }
}