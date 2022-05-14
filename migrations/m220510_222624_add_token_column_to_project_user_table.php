<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%project_user}}`.
 */
class m220510_222624_add_token_column_to_project_user_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%project_user}}', 'token', $this->string(64));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%project_user}}', 'token');
    }
}
