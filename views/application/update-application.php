<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

$this->title = 'Редактирование заявки';
?>

<div class="update-page">
    <div class="update-container">
        <div class="update-header">
            <h1>
                <i class="fas fa-edit"></i>
                Редактирование заявки
            </h1>
            <p>Заявка от <?= Yii::$app->formatter->asDate($application->created_at, 'dd.MM.yyyy') ?></p>
        </div>
        
        <div class="update-card">
            <?php $activeForm = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>
            
            <?= $activeForm->field($model, 'name')->textInput(['placeholder' => 'Тема заявки']) ?>
            
            <?= $activeForm->field($model, 'type')->dropDownList([
                'bug' => 'Ошибка',
                'question' => 'Вопрос',
                'suggestion' => 'Предложение',
                'violation' => 'Нарушение правила',
            ], ['prompt' => 'Выберите тип']) ?>
            
            <?= $activeForm->field($model, 'descr')->textarea(['rows' => 6, 'placeholder' => 'Описание проблемы']) ?>
            
            <?php if ($application->file): ?>
            <div class="current-file">
                <label>Текущий файл:</label><br>
                <a href="<?= Yii::getAlias('@web/uploads/applications/' . $application->file) ?>" target="_blank">
                    <i class="fas fa-download"></i> <?= Html::encode($application->file) ?>
                </a>
            </div>
            <?php endif; ?>
            
            <?= $activeForm->field($model, 'file')->fileInput(['class' => 'file-input']) ?>
            <div class="form-hint">JPG, PNG, PDF, DOC, DOCX до 10MB. Оставьте пустым, если не хотите менять файл.</div>
            
            <div class="form-buttons">
                <?= Html::submitButton('<i class="fas fa-save"></i> Сохранить', ['class' => 'back-btn']) ?>
                <?= Html::a('<i class="fas fa-times"></i> Отмена', ['application/my-applications'], ['class' => 'back-btn']) ?>
            </div>
            
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>