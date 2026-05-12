<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\web\UploadedFile;

class ApplicationForm extends Model
{
    public $name;
    public $type;
    public $descr;
    public $file;
    
    public function rules()
    {
        return [
            [['name', 'type', 'descr'], 'required', 'message' => 'Это поле обязательно для заполнения'],
            ['name', 'string', 'max' => 100],
            ['type', 'string', 'max' => 20],
            ['descr', 'string'],
            ['file', 'file', 'extensions' => 'jpg, jpeg, png, gif, pdf, doc, docx', 'maxSize' => 10 * 1024 * 1024, 'skipOnEmpty' => true],
            ['file', 'file', 'extensions' => 'jpg, jpeg, png, pdf, doc, docx', 'maxSize' => 10 * 1024 * 1024, 'skipOnEmpty' => true],
        ];
    }
    
    public function attributeLabels()
    {
        return [
            'name' => 'Тема заявки',
            'type' => 'Тип заявки',
            'descr' => 'Описание проблемы',
            'file' => 'Вложение (если есть)',
        ];
    }
    
    public function getTypeList()
    {
        return [
            'bug' => 'Ошибка',
            'question' => 'Вопрос',
            'suggestion' => 'Предложение',
            'violation' => 'Нарушение правила',
        ];
    }
    
    public function save()
    {
        if (!$this->validate()) {
            return false;
        }
        
        $application = new Applications();
        $application->name = $this->name;
        $application->type = $this->type;
        $application->descr = $this->descr;
        $application->user_id = Yii::$app->user->id;
        $application->status = 0;
        $application->answer = '';
        
        $file = UploadedFile::getInstance($this, 'file');
        if ($file && $file->tempName) {
            $fileName = time() . '_' . Yii::$app->user->id . '.' . $file->extension;
            $path = Yii::getAlias('@webroot/applications/');
            
            if (!file_exists($path)) {
                mkdir($path, 0755, true);
            }
            
            if ($file->saveAs($path . $fileName)) {
                $application->file = $fileName;
            }
        }
        
        return $application->save();
    }

    public function update($id)
    {
        if (!$this->validate()) {
            return false;
        }
        
        $application = Applications::findOne($id);
        if (!$application) {
            return false;
        }
        
        $application->name = $this->name;
        $application->type = $this->type;
        $application->descr = $this->descr;
        $application->updated_at = date('Y-m-d H:i:s');
        
        $file = UploadedFile::getInstance($this, 'file');
        if ($file && $file->tempName) {
            if ($application->file && file_exists(Yii::getAlias('@webroot/applications/' . $application->file))) {
                unlink(Yii::getAlias('@webroot/applications/' . $application->file));
            }
            
            $fileName = time() . '_' . Yii::$app->user->id . '.' . $file->extension;
            $path = Yii::getAlias('@webroot/applications/');
            
            if (!file_exists($path)) {
                mkdir($path, 0755, true);
            }
            
            if ($file->saveAs($path . $fileName)) {
                $application->file = $fileName;
            }
        }
        
        return $application->save();
    }
}