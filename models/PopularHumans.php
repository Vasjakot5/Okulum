<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "popular_humans".
 *
 * @property int $id
 * @property string $name
 * @property string $last_name
 * @property string $patronymic
 * @property string $img
 * @property string $type
 * @property string $descr
 * @property string $quote
 * @property string $date_born
 * @property string|null $date_death
 * @property int $countries_id
 * @property int|null $cities_id
 *
 * @property Cities $cities
 * @property Comments[] $comments
 * @property Countries $countries
 */
class PopularHumans extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'popular_humans';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['date_death', 'cities_id'], 'default', 'value' => null],
            [['name', 'last_name', 'patronymic', 'img', 'type', 'descr', 'date_born', 'countries_id'], 'required'],
            [['descr', 'quote'], 'string'],
            [['date_born', 'date_death'], 'safe'],
            [['countries_id', 'cities_id'], 'integer'],
            [['name', 'last_name', 'type'], 'string', 'max' => 50],
            [['patronymic'], 'string', 'max' => 100],
            [['img'], 'string', 'max' => 256],
            [['user_id', 'moderation_status'], 'integer'],
            ['moderation_status', 'default', 'value' => 0],
            [['countries_id'], 'exist', 'skipOnError' => true, 'targetClass' => Countries::class, 'targetAttribute' => ['countries_id' => 'id']],
            [['cities_id'], 'exist', 'skipOnError' => true, 'targetClass' => Cities::class, 'targetAttribute' => ['cities_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'last_name' => 'Last Name',
            'patronymic' => 'Patronymic',
            'img' => 'Img',
            'type' => 'Type',
            'descr' => 'Descr',
            'quote' => 'Quote',
            'date_born' => 'Date Born',
            'date_death' => 'Date Death',
            'countries_id' => 'Countries ID',
            'cities_id' => 'Cities ID',
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
     * Gets query for [[Comments]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getComments()
    {
        return $this->hasMany(Comments::class, ['popular_humans_id' => 'id']);
    }

    /**
     * Gets query for [[Countries]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCountries()
    {
        return $this->hasOne(Countries::class, ['id' => 'countries_id']);
    }

    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }
}
