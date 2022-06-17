<?php

use yii\db\Migration;

/**
 * Class m220617_180430_project_active_params
 */
class m220617_180430_project_active_params extends Migration
{
    public function safeUp()
    {
        $this->addColumn('{{%project}}', 'active', $this->boolean()->defaultValue(true));
        $this->addColumn('{{%project}}', 'enable_server_check', $this->boolean()->defaultValue(false));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%project}}', 'active');
        $this->dropColumn('{{%project}}', 'enable_server_check');
    }
}
