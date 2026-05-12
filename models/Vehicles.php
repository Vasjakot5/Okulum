<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "vehicles".
 *
 * @property int $id
 * @property string $name
 * @property string $img
 * @property string $descr
 * @property string $type
 * @property string $status
 * @property int $countries_id
 * @property int|null $cities_id
 *
 * @property Cities $cities
 * @property Comments[] $comments
 * @property Countries $countries
 */
class Vehicles extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'vehicles';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['cities_id'], 'default', 'value' => null],
            [['name', 'img', 'descr', 'type', 'status', 'countries_id'], 'required'],
            [['descr'], 'string'],
            [['countries_id', 'cities_id'], 'integer'],
            [['name'], 'string', 'max' => 50],
            [['img'], 'string', 'max' => 256],
            [['user_id', 'moderation_status'], 'integer'],
            ['moderation_status', 'default', 'value' => 0],
            [['type', 'status'], 'string', 'max' => 20],
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
            'img' => 'Img',
            'descr' => 'Descr',
            'type' => 'Type',
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
     * Gets query for [[Comments]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getComments()
    {
        return $this->hasMany(Comments::class, ['vehicles_id' => 'id']);
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
