<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\web\UploadedFile;
use app\models\PopularHumans;

class PopularHumanForm extends Model
{
    public $name;
    public $last_name;
    public $patronymic;
    public $img;
    public $type;
    public $descr;
    public $quote;
    public $date_born;
    public $date_death;
    public $countries_id;
    public $cities_id;
    
    private $imageFile;
    
    public function rules()
    {
        return [
            [['name', 'last_name', 'type', 'descr', 'date_born', 'countries_id'], 'required'],
            [['name', 'last_name'], 'string', 'max' => 50],
            ['patronymic', 'string', 'max' => 100],
            ['type', 'string', 'max' => 50],
            ['descr', 'string'],
            ['quote', 'string'],
            ['date_death', 'safe'],
            ['img', 'file', 'extensions' => 'jpg, jpeg, png, gif, webp', 'maxSize' => 5 * 1024 * 1024, 'skipOnEmpty' => true],
            [['countries_id', 'cities_id'], 'integer'],
        ];
    }
    
    public function attributeLabels()
    {
        return [
            'name' => 'Имя',
            'last_name' => 'Фамилия',
            'patronymic' => 'Отчество',
            'img' => 'Фото',
            'type' => 'Должность/Тип',
            'descr' => 'Биография',
            'quote' => 'Цитата',
            'date_born' => 'Дата рождения',
            'date_death' => 'Дата смерти',
            'countries_id' => 'Страна',
            'cities_id' => 'Город',
        ];
    }
    
    public function setImageFile($file)
    {
        $this->imageFile = $file;
    }
    
    public function save()
    {
        if (!$this->validate()) {
            return false;
        }
        
        $fileName = null;
        if ($this->imageFile) {
            $fileName = time() . '_' . Yii::$app->user->id . '.' . $this->imageFile->extension;
            $path = Yii::getAlias('@webroot/popular_humans_imgs/');
            if (!file_exists($path)) mkdir($path, 0755, true);
            if (!$this->imageFile->saveAs($path . $fileName)) {
                $this->addError('img', 'Ошибка загрузки');
                return false;
            }
        }
        
        $model = new PopularHumans();
        $model->name = $this->name;
        $model->last_name = $this->last_name;
        $model->patronymic = $this->patronymic;
        $model->img = $fileName;
        $model->type = $this->type;
        $model->descr = $this->descr;
        $model->quote = $this->quote;
        $model->date_born = $this->date_born;
        $model->date_death = $this->date_death;
        $model->countries_id = $this->countries_id;
        $model->cities_id = $this->cities_id;
        $model->user_id = Yii::$app->user->id;
        
        if (Yii::$app->user->identity->role == 1) {
            $model->moderation_status = 1;
        } else {
            $model->moderation_status = 0;
        }

        
        return $model->save();
    }
    
    public function getCountriesList()
    {
        return \yii\helpers\ArrayHelper::map(\app\models\Countries::find()->all(), 'id', 'name');
    }
    
    public function getCitiesList()
    {
        return \yii\helpers\ArrayHelper::map(\app\models\Cities::find()->orderBy(['name' => SORT_ASC])->all(), 'id', 'name');
    }
}