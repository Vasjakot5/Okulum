<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Создать обсуждение';
?>

<div class="create-discussion-page">
    <div class="create-discussion-container">
        <h1 class="create-discussion-title">
            <i class="fas fa-plus-circle"></i>
            Создать обсуждение
        </h1>
        
        <div class="create-discussion-card">
            <?php $form = ActiveForm::begin(); ?>
            
            <?= $form->field($model, 'title')->textInput(['placeholder' => 'Название темы', 'class' => 'create-discussion-input']) ?>
            
            <?= $form->field($model, 'content')->textarea(['rows' => 6, 'placeholder' => 'Опишите тему обсуждения...', 'class' => 'create-discussion-textarea']) ?>
            <?php if ($isAdmin): ?>
            <div class="form-group text-center">
                <label>
                    <input type="checkbox" name="is_admin_only" value="1"> 
                    <i class="fas fa-lock"></i> Сделать обсуждение доступным только для администраторов
                </label>
                <div class="form-hint">Обычные пользователи не увидят это обсуждение</div>
            </div>
            <?php endif; ?>
            <div class="create-discussion-buttons">
                <?= Html::submitButton('<i class="fas fa-save"></i> Создать', ['class' => 'back-btn']) ?>
            </div>
            
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
