<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

class User extends ActiveRecord implements IdentityInterface
{
    public $avatar_file;
    public $current_password;
    public $new_password;
    public $new_password_repeat;

    const BAN_STATUS_NONE = 0;
    const BAN_STATUS_TEMP = 1;
    const BAN_STATUS_PERMANENT = 2;

    public static function tableName()
    {
        return 'users';
    }

    public function rules()
    {
        return [
            [['name', 'last_name', 'email', 'phone'], 'required'],
            [['name', 'last_name'], 'string', 'max' => 50],
            [['email', 'password'], 'string', 'max' => 256],
            [['phone'], 'string', 'max' => 20],
            ['email', 'email'],
            ['email', 'unique', 'targetClass' => User::class, 'message' => 'Этот email уже используется'],
            ['phone', 'match', 'pattern' => '/^\+7\(\d{3}\)\d{3}-\d{2}-\d{2}$/', 'message' => 'Телефон должен быть в формате: +7(999)999-99-99'],
            ['phone', 'unique', 'targetClass' => User::class, 'message' => 'Этот телефон уже используется'],
            ['role', 'default', 'value' => 0],
            [['created_at', 'updated_at', 'photo'], 'safe'],
            [['ban_status', 'violations_count'], 'integer'],
            [['ban_reason'], 'string'],
            [['ban_until'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Имя',
            'last_name' => 'Фамилия',
            'email' => 'Email',
            'phone' => 'Телефон',
            'photo' => 'Аватар',
            'password' => 'Пароль',
            'role' => 'Роль',
            'created_at' => 'Дата регистрации',
            'updated_at' => 'Дата обновления',
            'ban_status' => 'Статус бана',
            'ban_reason' => 'Причина бана',
            'ban_until' => 'Бан до',
            'violations_count' => 'Количество нарушений',
        ];
    }

    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        return null;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getAuthKey()
    {
        return null;
    }

    public function validateAuthKey($authKey)
    {
        return false;
    }

    public static function findByEmail($email)
    {
        return static::findOne(['email' => $email]);
    }

    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password);
    }

    public function setPassword($password)
    {
        $this->password = Yii::$app->security->generatePasswordHash($password);
    }
    
    public function getFullName()
    {
        return $this->name . ' ' . $this->last_name;
    }
    
    public function getAvatarUrl()
    {
        if ($this->photo && file_exists(Yii::getAlias('@webroot/avatars/' . $this->photo))) {
            return Yii::getAlias('@web/avatars/' . $this->photo);
        }
        return Yii::getAlias('@web/img/default-avatar.png');
    }
    
    public function beforeSave($insert)
    {
        if ($insert) {
            $this->created_at = date('Y-m-d H:i:s');
        }
        $this->updated_at = date('Y-m-d H:i:s');
        
        return parent::beforeSave($insert);
    }

    public function isBanned()
    {
        if ($this->ban_status == self::BAN_STATUS_PERMANENT) {
            return true;
        }
        
        if ($this->ban_status == self::BAN_STATUS_TEMP && $this->ban_until) {
            $banUntil = strtotime($this->ban_until);
            if ($banUntil > time()) {
                return true;
            } else {
                $this->ban_status = self::BAN_STATUS_NONE;
                $this->ban_reason = null;
                $this->ban_until = null;
                $this->save(false);
                return false;
            }
        }
        
        return false;
    }

    public function getBanRemainingTime()
    {
        if ($this->ban_status == self::BAN_STATUS_TEMP && $this->ban_until) {
            $remaining = strtotime($this->ban_until) - time();
            if ($remaining > 0) {
                $days = floor($remaining / 86400);
                $hours = floor(($remaining % 86400) / 3600);
                return "{$days} д. {$hours} ч.";
            }
        }
        return null;
    }

    public function addViolation($commentId, $violationType, $violationText)
    {
        $this->violations_count++;
        $this->save(false);
        
        $this->checkAndBan();
    }

    private function checkAndBan()
    {
        $violationsCount = $this->violations_count;
        
        if ($violationsCount == 1) {
            Yii::$app->session->setFlash('danger', 'Предупреждение! Ваше сообщение нарушает правила сайта.');
        }
        
        if ($violationsCount == 2) {
            $this->ban_status = self::BAN_STATUS_TEMP;
            $this->ban_reason = 'Повторное нарушение правил. Блокировка на 1 день.';
            $this->ban_until = date('Y-m-d H:i:s', strtotime('+1 days'));
            $this->save(false);
            Yii::$app->session->setFlash('danger', $this->ban_reason);
        }
        
        if ($violationsCount == 3) {
            $this->ban_status = self::BAN_STATUS_TEMP;
            $this->ban_reason = 'Систематическое нарушение правил. Блокировка на 3 дня.';
            $this->ban_until = date('Y-m-d H:i:s', strtotime('+3 days'));
            $this->save(false);
            Yii::$app->session->setFlash('danger', $this->ban_reason);
        }
        
        if ($violationsCount >= 4 && $violationsCount <= 6) {
            $days = $violationsCount * 2;
            $this->ban_status = self::BAN_STATUS_TEMP;
            $this->ban_reason = "Многократное нарушение правил. Блокировка на {$days} дней.";
            $this->ban_until = date('Y-m-d H:i:s', strtotime("+{$days} days"));
            $this->save(false);
            Yii::$app->session->setFlash('danger', $this->ban_reason);
        }
        
        if ($violationsCount >= 7) {
            $this->ban_status = self::BAN_STATUS_PERMANENT;
            $this->ban_reason = 'Ваш аккаунт заблокирован навсегда за систематическое игнорирование правил сайта.';
            $this->save(false);
            Yii::$app->session->setFlash('danger', $this->ban_reason);
        }
    }
}