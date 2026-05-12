<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "clothes".
 *
 * @property int $id
 * @property string $name
 * @property string $img
 * @property string $descr
 * @property string $status
 * @property int $countries_id
 * @property int|null $cities_id
 *
 * @property Cities $cities
 * @property Countries $countries
 */
class Clothes extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'clothes';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['cities_id'], 'default', 'value' => null],
            [['name', 'img', 'descr', 'status', 'countries_id'], 'required'],
            [['descr'], 'string'],
            [['countries_id', 'cities_id'], 'integer'],
            [['name', 'img'], 'string', 'max' => 256],
            [['status'], 'string', 'max' => 50],
            [['user_id', 'moderation_status'], 'integer'],
            ['moderation_status', 'default', 'value' => 0],
            [['cities_id'], 'exist', 'skipOnError' => true, 'targetClass' => Cities::class, 'targetAttribute' => ['cities_id' => 'id']],
            [['countries_id'], 'exist', 'skipOnError' => true, 'targetClass' => Countries::class, 'targetAttribute' => ['countries_id' => 'id']],
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
            'img' => 'Img',
            'descr' => 'Descr',
            'status' => 'Status',
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
