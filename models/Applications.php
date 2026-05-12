<?php

namespace app\models;

use Yii;

class Applications extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'applications';
    }

    public function rules()
    {
        return [
            [['status'], 'default', 'value' => 0],
            [['name', 'type', 'descr', 'user_id'], 'required'],
            [['descr', 'answer'], 'string'],
            [['user_id', 'status'], 'integer'],
            [['name'], 'string', 'max' => 100],
            [['type'], 'string', 'max' => 20],
            [['file'], 'string', 'max' => 256],
            [['created_at', 'updated_at'], 'safe'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Тема',
            'type' => 'Тип',
            'descr' => 'Описание',
            'file' => 'Файл',
            'user_id' => 'Пользователь',
            'status' => 'Статус',
            'answer' => 'Ответ',
            'created_at' => 'Дата создания',
            'updated_at' => 'Дата обновления',
        ];
    }

    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }
    
    public function beforeSave($insert)
    {
        if ($insert) {
            $this->created_at = date('Y-m-d H:i:s');
        }
        $this->updated_at = date('Y-m-d H:i:s');
        
        return parent::beforeSave($insert);
    }
}