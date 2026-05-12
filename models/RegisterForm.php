<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\web\UploadedFile;

class RegisterForm extends Model
{
    public $name;
    public $last_name;
    public $email;
    public $phone;
    public $password;
    public $password_repeat;
    public $agree_terms;
    public $avatar_file;

    public function rules()
    {
        return [
            [['name', 'last_name', 'email', 'phone', 'password', 'password_repeat'], 'required', 'message' => 'Это поле обязательно для заполнения'],
            ['agree_terms', 'required', 'requiredValue' => 1, 'message' => 'Вы должны согласиться с условиями использования'],
            [['name', 'last_name'], 'string', 'max' => 50],
            [['email', 'password'], 'string', 'max' => 256],
            [['phone'], 'string', 'max' => 20],
            ['email', 'email'],
            ['email', 'unique', 'targetClass' => User::class, 'message' => 'Этот email уже зарегистрирован'],
            ['phone', 'match', 'pattern' => '/^\+7\(\d{3}\)\d{3}-\d{2}-\d{2}$/', 'message' => 'Телефон должен быть в формате: +7(999)999-99-99'],
            ['phone', 'unique', 'targetClass' => User::class, 'message' => 'Этот телефон уже зарегистрирован'],
            ['password', 'string', 'min' => 6],
            ['password_repeat', 'compare', 'compareAttribute' => 'password', 'message' => 'Пароли не совпадают'],
            ['avatar_file', 'file', 'extensions' => 'jpg, jpeg, png, gif, webp', 'maxSize' => 5 * 1024 * 1024, 'skipOnEmpty' => true],
        ];
    }

    public function attributeLabels()
    {
        return [
            'name' => 'Имя',
            'last_name' => 'Фамилия',
            'email' => 'Почта',
            'phone' => 'Телефон',
            'password' => 'Пароль',
            'password_repeat' => 'Повторите пароль',
            'agree_terms' => 'Я согласен с условиями использования',
            'avatar_file' => 'Аватар',
        ];
    }

    public function register()
    {
        if (!$this->validate()) {
            return false;
        }

        $user = new User();
        $user->name = $this->name;
        $user->last_name = $this->last_name;
        $user->email = $this->email;
        $user->phone = $this->phone;
        $user->setPassword($this->password);
        $user->role = 0;
        
        $avatar = UploadedFile::getInstance($this, 'avatar_file');
        if ($avatar && $avatar->tempName) {
            $fileName = time() . '_' . Yii::$app->security->generateRandomString(10) . '.' . $avatar->extension;
            $path = Yii::getAlias('@webroot/avatars/');
            
            if (!file_exists($path)) {
                mkdir($path, 0755, true);
            }
            
            if ($avatar->saveAs($path . $fileName)) {
                $user->photo = $fileName;
            }
        }
        
        return $user->save();
    }
}