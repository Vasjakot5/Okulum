<?php

use yii\db\Migration;

class m260512_084012_default_admin extends Migration
{
    public function safeUp()
    {
        $password = Yii::$app->security->generatePasswordHash('Okulum');
        
        $this->insert('users', [
            'name' => 'Администратор',
            'last_name' => '',
            'email' => 'Okulum@gmail.com',
            'phone' => '+7(000)000-00-00',
            'password' => $password,
            'role' => 1,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
    }

    public function safeDown()
    {
        $this->delete('users', ['email' => 'Okulum@gmail.com']);
    }
}