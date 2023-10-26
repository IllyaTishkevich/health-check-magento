<?php

use yii\db\Migration;

/**
 * Class m231026_092320_remove_forigin_key
 */
class m231026_092320_remove_forigin_key extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropForeignKey('message_to_project','message');
        $this->dropForeignKey('message_to_level','message');
        $this->dropIndex('message_to_project','message');
        $this->dropIndex('message_to_level','message');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->addForeignKey('message_to_project','message','project_id','project','id');
        $this->addForeignKey('message_to_level','message','level_id','level','id');
    }
}
