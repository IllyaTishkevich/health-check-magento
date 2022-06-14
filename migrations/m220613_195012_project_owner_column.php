<?php

use yii\db\Migration;

/**
 * Class m220613_195012_project_owner_column
 */
class m220613_195012_project_owner_column extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%project}}', 'owner', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%project}}', 'owner');
    }
}
