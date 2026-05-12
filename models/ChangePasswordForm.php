<?php

namespace app\models;

use Yii;
use yii\base\Model;

class ChangePasswordForm extends Model
{
    public $current_password;
    public $new_password;
    public $new_password_repeat;
    
    private $_user;
    
    public function __construct($user, $config = [])
    {
        $this->_user = $user;
        parent::__construct($config);
    }
    
    public function rules()
    {
        return [
            [['current_password', 'new_password', 'new_password_repeat'], 'required', 'message' => 'Поле обязательно для заполнения'],
            ['new_password', 'string', 'min' => 6, 'message' => 'Пароль должен быть не менее 6 символов'],
            ['new_password_repeat', 'compare', 'compareAttribute' => 'new_password', 'message' => 'Пароли не совпадают'],
            ['current_password', 'validateCurrentPassword'],
        ];
    }
    
    public function attributeLabels()
    {
        return [
            'current_password' => 'Текущий пароль',
            'new_password' => 'Новый пароль',
            'new_password_repeat' => 'Повторите новый пароль',
        ];
    }
    
    public function validateCurrentPassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            if (!$this->_user->validatePassword($this->$attribute)) {
                $this->addError($attribute, 'Неверный текущий пароль');
            }
        }
    }
    
    public function changePassword()
    {
        if ($this->validate()) {
            $this->_user->setPassword($this->new_password);
            $this->_user->updated_at = date('Y-m-d H:i:s');
            return $this->_user->save();
        }
        return false;
    }
}