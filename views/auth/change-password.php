<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Смена пароля';
?>

<div class="change-password-page">
    <div class="change-password-container">
        <div class="change-password-header">
            <h1>
                <i class="fas fa-key"></i>
                <?= Html::encode($this->title) ?>
            </h1>
            <p>Измените ваш пароль</p>
        </div>
        
        <div class="change-password-card">
            <?php if (Yii::$app->session->hasFlash('success')): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> <?= Yii::$app->session->getFlash('success') ?>
                </div>
            <?php endif; ?>
            
            <?php if (Yii::$app->session->hasFlash('error')): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle"></i> <?= Yii::$app->session->getFlash('error') ?>
                </div>
            <?php endif; ?>
            
            <?php $form = ActiveForm::begin(); ?>
            
            <?= $form->field($model, 'current_password')->passwordInput(['maxlength' => true]) ?>
            <?= $form->field($model, 'new_password')->passwordInput(['maxlength' => true]) ?>
            <?= $form->field($model, 'new_password_repeat')->passwordInput(['maxlength' => true]) ?>
            
            <div class="form-buttons">
                <?= Html::submitButton('<i class="fas fa-save"></i> Сменить пароль', ['class' => 'form-btn']) ?>
                <?= Html::a('<i class="fas fa-times"></i> Отмена', ['auth/profile'], ['class' => 'form-btn']) ?>
            </div>
            
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>