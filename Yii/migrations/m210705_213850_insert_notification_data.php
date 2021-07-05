<?php

use yii\db\Migration;

/**
 * Class m210705_213850_insert_notification_data
 */
class m210705_213850_insert_notification_data extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert('notification',
            ['name'=>'SendMail', 'object_namespace' => '\app\models\Notifications\NotifyEmail']
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210705_213850_insert_notification_data cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210705_213850_insert_notification_data cannot be reverted.\n";

        return false;
    }
    */
}
