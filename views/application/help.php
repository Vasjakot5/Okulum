<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\MaskedInput;

$this->title = 'Техподдержка';
?>

<div class="help-page">
    <div class="help-container">
        <div class="help-header">
            <h1>
                <i class="fas fa-question-circle"></i>
                <?= Html::encode($this->title) ?>
            </h1>
        </div>
        
        <div class="application-card">
            <h2><i class="fas fa-paper-plane"></i> Связаться с поддержкой</h2>
            
            <?php if (Yii::$app->session->hasFlash('success')): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> <?= Yii::$app->session->getFlash('success') ?>
                </div>
            <?php endif; ?>
            
            <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>
            
            <?= $form->field($model, 'name')->textInput(['placeholder' => 'Кратко опишите суть вопроса']) ?>
            
            <?= $form->field($model, 'type')->dropDownList($model->getTypeList(), ['prompt' => 'Выберите тип']) ?>
            
            <?= $form->field($model, 'descr')->textarea(['rows' => 5, 'placeholder' => 'Подробно опишите вашу проблему или вопрос...']) ?>
            
            <?= $form->field($model, 'file')->fileInput(['accept' => 'image/*,.pdf,.doc,.docx', 'class' => 'file-input']) ?>
            <div style="font-size: 12px; color: rgba(255,255,255,0.5); margin-top: -10px; margin-bottom: 15px;">
                <i class="fas fa-info-circle"></i> Поддерживаемые форматы: JPG, PNG, PDF, DOC, DOCX. Максимум 10MB
            </div>
            <div style="text-align: center; margin-top: 20px;">
                <?= Html::submitButton('<i class="fas fa-paper-plane"></i> Отправить заявку', ['class' => 'back-btn']) ?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
        
        <div style="text-align: center; margin-top: 20px;">
            <?= Html::a('<i class="fas fa-list"></i> Мои заявки', ['application/my-applications'], ['class' => 'back-btn']) ?>
        </div>
    </div>
</div>

<?php
$form = ActiveForm::class;
?>