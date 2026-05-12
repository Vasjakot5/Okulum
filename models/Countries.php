<?php

namespace app\models;

use Yii;

class Countries extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'countries';
    }

    public function rules()
    {
        return [
            [['date_end', 'capital_id'], 'default', 'value' => null],
            [['name', 'flag', 'map', 'population', 'descr', 'date_origin'], 'required'],
            [['population', 'capital_id'], 'integer'],
            [['descr'], 'string'],
            [['date_origin', 'date_end'], 'safe'],
            [['name'], 'string', 'max' => 100],
            [['flag', 'map'], 'string', 'max' => 256],
            [['capital_id'], 'exist', 'skipOnError' => true, 'targetClass' => Cities::class, 'targetAttribute' => ['capital_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Название',
            'flag' => 'Флаг',
            'map' => 'Карта',
            'population' => 'Население',
            'descr' => 'Описание',
            'date_origin' => 'Дата основания',
            'date_end' => 'Дата окончания',
            'capital_id' => 'Столица',
        ];
    }

    public function getCityCountries()
    {
        return $this->hasMany(CityCountries::class, ['country_id' => 'id']);
    }

    public function getClothes()
    {
        return $this->hasMany(Clothes::class, ['countries_id' => 'id']);
    }

    public function getEvents()
    {
        return $this->hasMany(Events::class, ['countries_id' => 'id']);
    }

    public function getMonuments()
    {
        return $this->hasMany(Monuments::class, ['countries_id' => 'id']);
    }

    public function getOpenings()
    {
        return $this->hasMany(Openings::class, ['countries_id' => 'id']);
    }

    public function getPopularHumans()
    {
        return $this->hasMany(PopularHumans::class, ['countries_id' => 'id']);
    }

    public function getVehicles()
    {
        return $this->hasMany(Vehicles::class, ['countries_id' => 'id']);
    }

    public function getWeapons()
    {
        return $this->hasMany(Weapons::class, ['countries_id' => 'id']);
    }

    public function getCities()
    {
        return $this->hasMany(Cities::class, ['id' => 'city_id'])
            ->via('cityCountries');
    }

    public function getCapital()
    {
        return $this->hasOne(Cities::class, ['id' => 'capital_id']);
    }
}