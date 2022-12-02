<?php

use yii\db\Migration;

/**
 * Class m221202_214543_add_discord_notif
 */
class m221202_214543_add_discord_notif extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert('notification',
            ['name'=>'Discord WebHook', 'object_namespace' => '\app\models\Notifications\NotifDiscord']
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m221202_214543_add_discord_notif cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m221202_214543_add_discord_notif cannot be reverted.\n";

        return false;
    }
    */
}
