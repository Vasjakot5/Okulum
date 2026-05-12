<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "cities".
 *
 * @property int $id
 * @property string $name
 * @property string|null $flag
 * @property int $population
 * @property string $descr
 *
 * @property CityCountries[] $cityCountries
 * @property Clothes[] $clothes
 * @property Comments[] $comments
 * @property Events[] $events
 * @property Monuments[] $monuments
 * @property Openings[] $openings
 * @property PopularHumans[] $popularHumans
 * @property Vehicles[] $vehicles
 * @property Weapons[] $weapons
 */
class Cities extends \yii\db\ActiveRecord
{
    public $flagFile;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'cities';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['flag'], 'default', 'value' => null],
            [['name', 'population', 'descr'], 'required'],
            [['population'], 'integer'],
            [['descr'], 'string'],
            [['name'], 'string', 'max' => 100],
            [['flag'], 'string', 'max' => 256],
            [['flagFile'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg, jpeg, gif, webp', 'maxSize' => 5 * 1024 * 1024],
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
            'flag' => 'Flag',
            'population' => 'Population',
            'descr' => 'Descr',
        ];
    }

    /**
     * Gets query for [[CityCountries]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCityCountries()
    {
        return $this->hasMany(CityCountries::class, ['city_id' => 'id']);
    }

    /**
     * Gets query for [[Clothes]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getClothes()
    {
        return $this->hasMany(Clothes::class, ['cities_id' => 'id']);
    }

    /**
     * Gets query for [[Comments]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getComments()
    {
        return $this->hasMany(Comments::class, ['cities_id' => 'id']);
    }

    /**
     * Gets query for [[Events]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEvents()
    {
        return $this->hasMany(Events::class, ['cities_id' => 'id']);
    }

    /**
     * Gets query for [[Monuments]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMonuments()
    {
        return $this->hasMany(Monuments::class, ['cities_id' => 'id']);
    }

    public function getCountries()
    {
        return $this->hasMany(Countries::class, ['id' => 'country_id'])
            ->via('cityCountries');
    }

    /**
     * Gets query for [[Openings]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOpenings()
    {
        return $this->hasMany(Openings::class, ['cities_id' => 'id']);
    }

    /**
     * Gets query for [[PopularHumans]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPopularHumans()
    {
        return $this->hasMany(PopularHumans::class, ['cities_id' => 'id']);
    }

    /**
     * Gets query for [[Vehicles]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getVehicles()
    {
        return $this->hasMany(Vehicles::class, ['cities_id' => 'id']);
    }

    /**
     * Gets query for [[Weapons]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getWeapons()
    {
        return $this->hasMany(Weapons::class, ['cities_id' => 'id']);
    }

}
