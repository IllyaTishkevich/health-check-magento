<?php

use yii\db\Migration;

/**
 * Class m220615_132332_project_gmt
 */
class m220615_132332_project_gmt extends Migration
{
    public function safeUp()
    {
        $this->addColumn('{{%project}}', 'gmt', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%project}}', 'gmt');
    }
}
