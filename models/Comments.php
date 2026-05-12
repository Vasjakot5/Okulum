<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "comments".
 *
 * @property int $id
 * @property string $content
 * @property int $user_id
 * @property int|null $cities_id
 * @property int|null $events_id
 * @property int|null $openings_id
 * @property int|null $popular_humans_id
 * @property int|null $vehicles_id
 * @property int|null $weapons_id
 * @property int|null $monuments_id
 * @property int|null $clothes_id
 * @property int|null $discussions_id
 * @property string $created_at
 * @property int|null $parent_id
 *
 * @property Cities $cities
 * @property Clothes $clothes
 * @property Comments[] $comments
 * @property Discussions $discussions
 * @property Events $events
 * @property Monuments $monuments
 * @property Openings $openings
 * @property Comments $parent
 * @property PopularHumans $popularHumans
 * @property User $user
 * @property Vehicles $vehicles
 * @property Weapons $weapons
 */
class Comments extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'comments';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['cities_id', 'events_id', 'openings_id', 'popular_humans_id', 'vehicles_id', 'weapons_id', 'monuments_id', 'clothes_id', 'discussions_id', 'parent_id'], 'default', 'value' => null],
            [['content', 'user_id'], 'required'],
            [['content'], 'string'],
            [['user_id', 'cities_id', 'events_id', 'openings_id', 'popular_humans_id', 'vehicles_id', 'weapons_id', 'monuments_id', 'clothes_id', 'discussions_id', 'parent_id'], 'integer'],
            [['created_at'], 'safe'],
            [['updated_at'], 'safe'],
            [['parent_id'], 'default', 'value' => null],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
            [['parent_id'], 'exist', 'skipOnError' => true, 'targetClass' => Comments::class, 'targetAttribute' => ['parent_id' => 'id']],
            [['cities_id'], 'exist', 'skipOnError' => true, 'targetClass' => Cities::class, 'targetAttribute' => ['cities_id' => 'id']],
            [['events_id'], 'exist', 'skipOnError' => true, 'targetClass' => Events::class, 'targetAttribute' => ['events_id' => 'id']],
            [['openings_id'], 'exist', 'skipOnError' => true, 'targetClass' => Openings::class, 'targetAttribute' => ['openings_id' => 'id']],
            [['popular_humans_id'], 'exist', 'skipOnError' => true, 'targetClass' => PopularHumans::class, 'targetAttribute' => ['popular_humans_id' => 'id']],
            [['vehicles_id'], 'exist', 'skipOnError' => true, 'targetClass' => Vehicles::class, 'targetAttribute' => ['vehicles_id' => 'id']],
            [['weapons_id'], 'exist', 'skipOnError' => true, 'targetClass' => Weapons::class, 'targetAttribute' => ['weapons_id' => 'id']],
            [['monuments_id'], 'exist', 'skipOnError' => true, 'targetClass' => Monuments::class, 'targetAttribute' => ['monuments_id' => 'id']],
            [['discussions_id'], 'exist', 'skipOnError' => true, 'targetClass' => Discussions::class, 'targetAttribute' => ['discussions_id' => 'id']],
            [['clothes_id'], 'exist', 'skipOnError' => true, 'targetClass' => Clothes::class, 'targetAttribute' => ['clothes_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'content' => 'Content',
            'user_id' => 'User ID',
            'cities_id' => 'Cities ID',
            'events_id' => 'Events ID',
            'openings_id' => 'Openings ID',
            'popular_humans_id' => 'Popular Humans ID',
            'vehicles_id' => 'Vehicles ID',
            'weapons_id' => 'Weapons ID',
            'monuments_id' => 'Monuments ID',
            'clothes_id' => 'Clothes ID',
            'discussions_id' => 'Discussions ID',
            'created_at' => 'Created At',
            'parent_id' => 'Parent ID',
        ];
    }

    /**
     * Gets query for [[Cities]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCities()
    {
        return $this->hasOne(Cities::class, ['id' => 'cities_id']);
    }

    /**
     * Gets query for [[Clothes]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getClothes()
    {
        return $this->hasOne(Clothes::class, ['id' => 'clothes_id']);
    }

    /**
     * Gets query for [[Comments]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getComments()
    {
        return $this->hasMany(Comments::class, ['parent_id' => 'id']);
    }

    /**
     * Gets query for [[Discussions]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDiscussions()
    {
        return $this->hasOne(Discussions::class, ['id' => 'discussions_id']);
    }

    /**
     * Gets query for [[Events]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEvents()
    {
        return $this->hasOne(Events::class, ['id' => 'events_id']);
    }

    /**
     * Gets query for [[Monuments]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMonuments()
    {
        return $this->hasOne(Monuments::class, ['id' => 'monuments_id']);
    }

    /**
     * Gets query for [[Openings]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOpenings()
    {
        return $this->hasOne(Openings::class, ['id' => 'openings_id']);
    }

    /**
     * Gets query for [[Parent]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getParent()
    {
        return $this->hasOne(Comments::class, ['id' => 'parent_id']);
    }

    /**
     * Gets query for [[PopularHumans]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPopularHumans()
    {
        return $this->hasOne(PopularHumans::class, ['id' => 'popular_humans_id']);
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    /**
     * Gets query for [[Vehicles]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getVehicles()
    {
        return $this->hasOne(Vehicles::class, ['id' => 'vehicles_id']);
    }

    /**
     * Gets query for [[Weapons]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getWeapons()
    {
        return $this->hasOne(Weapons::class, ['id' => 'weapons_id']);
    }

}
