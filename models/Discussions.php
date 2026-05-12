<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "discussions".
 *
 * @property int $id
 * @property string $title
 * @property string $content
 * @property int $user_id
 * @property string $created_at
 * @property string|null $updated_at
 * @property int|null $messages_count
 *
 * @property Comments[] $comments
 * @property Comments[] $comments0
 * @property User $user
 */
class Discussions extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'discussions';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['updated_at'], 'default', 'value' => null],
            [['messages_count'], 'default', 'value' => 0],
            [['title', 'content', 'user_id'], 'required'],
            [['content'], 'string'],
            ['is_admin_only', 'default', 'value' => 0],
            [['user_id', 'messages_count'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['title'], 'string', 'max' => 200],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Название',
            'content' => 'Описание',
            'user_id' => 'Автор',
            'created_at' => 'Создано',
            'messages_count' => 'Сообщений',
            'is_admin_only' => 'Только для администраторов',
        ];
    }

    /**
     * Gets query for [[Comments]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getComments()
    {
        return $this->hasMany(Comments::class, ['discussions_id' => 'id']);
    }

    /**
     * Gets query for [[Comments0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getComments0()
    {
        return $this->hasMany(Comments::class, ['discussions_id' => 'id']);
    }

    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    public function getMessages()
    {
        return $this->hasMany(Comments::class, ['discussions_id' => 'id'])->orderBy(['created_at' => SORT_ASC]);
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
