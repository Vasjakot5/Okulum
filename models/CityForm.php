<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\web\UploadedFile;

class CityForm extends Model
{
    public $name;
    public $population;
    public $descr;
    public $flagFile;
    public $x;
    public $y;
    public $countries = [];
    
    public function rules()
    {
        return [
            [['name', 'population', 'descr'], 'required', 'message' => 'Поле обязательно для заполнения'],
            [['name'], 'string', 'max' => 100],
            [['population'], 'integer', 'min' => 0],
            [['descr'], 'string'],
            [['x', 'y'], 'number', 'min' => 0, 'max' => 100],
            [['x', 'y'], 'default', 'value' => 50],
            [['countries'], 'each', 'rule' => ['integer']],
            [['countries'], 'required', 'message' => 'Выберите хотя бы одну страну'],
            [['flagFile'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg, jpeg, gif, webp', 'maxSize' => 5 * 1024 * 1024],
        ];
    }
    
    public function attributeLabels()
    {
        return [
            'name' => 'Название города',
            'population' => 'Население',
            'descr' => 'Описание',
            'flagFile' => 'Флаг',
            'x' => 'Координата X',
            'y' => 'Координата Y',
            'countries' => 'Страны',
        ];
    }
    
    public function getCountriesList()
    {
        return Countries::find()->orderBy(['name' => SORT_ASC])->all();
    }
    
    public function save()
    {
        if (!$this->validate()) {
            return false;
        }
        
        $transaction = Yii::$app->db->beginTransaction();
        
        try {
            $city = new Cities();
            $city->name = $this->name;
            $city->population = $this->population;
            $city->descr = $this->descr;
            
            if ($this->flagFile) {
                $fileName = time() . '_' . rand(1000, 9999) . '.' . $this->flagFile->extension;
                $path = Yii::getAlias('@webroot/flags_imgs/');
                if (!file_exists($path)) {
                    mkdir($path, 0777, true);
                }
                if ($this->flagFile->saveAs($path . $fileName)) {
                    $city->flag = $fileName;
                }
            }
            
            if (!$city->save()) {
                $transaction->rollBack();
                return false;
            }
            
            foreach ($this->countries as $countryId) {
                $cc = new CityCountries();
                $cc->city_id = $city->id;
                $cc->country_id = $countryId;
                $cc->x = $this->x;
                $cc->y = $this->y;
                if (!$cc->save()) {
                    $transaction->rollBack();
                    return false;
                }
            }
            
            $transaction->commit();
            return true;
            
        } catch (\Exception $e) {
            $transaction->rollBack();
            return false;
        }
    }
}