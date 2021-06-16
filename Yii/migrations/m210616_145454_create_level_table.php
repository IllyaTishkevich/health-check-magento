<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%level}}`.
 */
class m210616_145454_create_level_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%level}}', [
            'id' => $this->primaryKey(),
            'key' => $this->string()
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%level}}');
    }
}
