<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%apples}}`.
 */
class m210101_000001_create_apples_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%apples}}', [
            'id' => $this->primaryKey(),
            'color' => $this->string(20)->notNull(),
            'created_at' => $this->integer()->notNull(),
            'fell_at' => $this->integer()->null(),
            'status' => "ENUM('on_tree','on_ground','rotten') NOT NULL DEFAULT 'on_tree'",
            'eaten_percent' => $this->float()->notNull()->defaultValue(0),
            'updated_at' => $this->integer()->null(),
        ]);
        $this->createIndex('idx-apples-status', '{{%apples}}', 'status');
    }

    public function safeDown()
    {
        $this->dropTable('{{%apples}}');
    }
}
