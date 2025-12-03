<?php

use \yii\db\Migration;

class m190124_110200_add_verification_token_column_to_user_table extends Migration
{
    public function up()
    {
        $this->addColumn('{{%user}}', 'verification_token', $this->string()->defaultValue(null));
        // Заполняем таблицу администратором
        $this->insert('{{%user}}', [
            'id' => 1,
            'username' => 'admin',
            'auth_key' => 'jGTCRzDkQYc7ChZkzWrv9x0WMBbQCQS8',
            'password_hash' => '$2y$13$6vdZ3U5tK/3vLWbOm3n48e6N/dcqsF8539hxnz3s9FxAb/VY.MezW',
            'password_reset_token' => null,
            'email' => 'admin@example.com',
            'status' => 10,
            'created_at' => 1764687591,
            'updated_at' => 1764687591,
            'verification_token' => null,
        ]);
    }

    public function down()
    {
        $this->dropColumn('{{%user}}', 'verification_token');
    }
}
