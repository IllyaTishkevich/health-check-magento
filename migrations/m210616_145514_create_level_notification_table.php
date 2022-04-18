<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%level_notification}}`.
 */
class m210616_145514_create_level_notification_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%level_notification}}', [
            'id' => $this->primaryKey(),
            'level_id' => $this->integer(),
            'notification_id' => $this->integer(),
            'project_id' => $this->integer(),
            'settings' => $this->text()
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%level_notification}}');
    }
}
