<?php

use yii\db\Migration;

/**
 * Class m220602_212359_notification_level_add_active
 */
class m220602_212359_notification_level_add_active extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%level_notification}}', 'active', $this->boolean(false));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%level_notification}}', 'active');
    }

}
