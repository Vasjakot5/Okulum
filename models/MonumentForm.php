<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\web\UploadedFile;
use app\models\Monuments;

class MonumentForm extends Model
{
    public $name;
    public $img;
    public $status;
    public $descr;
    public $countries_id;
    public $cities_id;
    
    private $imageFile;
    
    public function rules()
    {
        return [
            [['name', 'status', 'descr', 'countries_id'], 'required'],
            ['name', 'string', 'max' => 256],
            ['status', 'string', 'max' => 50],
            ['descr', 'string'],
            ['img', 'file', 'extensions' => 'jpg, jpeg, png, gif, webp', 'maxSize' => 5 * 1024 * 1024, 'skipOnEmpty' => true],
            [['countries_id', 'cities_id'], 'integer'],
        ];
    }
    
    public function attributeLabels()
    {
        return [
            'name' => 'Название',
            'img' => 'Изображение',
            'status' => 'Тип памятника',
            'descr' => 'Описание',
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
            $path = Yii::getAlias('@webroot/monument_imgs/');
            if (!file_exists($path)) mkdir($path, 0755, true);
            if (!$this->imageFile->saveAs($path . $fileName)) {
                $this->addError('img', 'Ошибка загрузки');
                return false;
            }
        }
        
        $model = new Monuments();
        $model->name = $this->name;
        $model->img = $fileName;
        $model->status = $this->status;
        $model->descr = $this->descr;
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