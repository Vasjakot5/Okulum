<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\MaskedInput;

$this->title = 'Регистрация';
?>

<div class="register-page">
    <div class="register-container">
        <div class="register-header">
            <h1>
                <i class="fas fa-user-plus"></i>
                <?= Html::encode($this->title) ?>
            </h1>
            <p>Создайте новый аккаунт</p>
        </div>
        
        <div class="register-card">
            <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>
            
            <div class="row" style="display: flex; flex-wrap: wrap; gap: 20px;">
                <div style="flex: 1; min-width: 200px;">
                    <?= $form->field($model, 'name')->textInput(['maxlength' => true, 'placeholder' => 'Введите имя']) ?>
                </div>
                <div style="flex: 1; min-width: 200px;">
                    <?= $form->field($model, 'last_name')->textInput(['maxlength' => true, 'placeholder' => 'Введите фамилию']) ?>
                </div>
            </div>
            
            <?= $form->field($model, 'email')->textInput(['maxlength' => true, 'placeholder' => 'example@mail.com']) ?>
            
            <?= $form->field($model, 'phone')->widget(MaskedInput::class, [
                'mask' => '+7(999)999-99-99',
                'options' => ['placeholder' => '+7(999)999-99-99']
            ]) ?>
            
            <?= $form->field($model, 'avatar_file')->fileInput(['accept' => 'image/*', 'class' => 'file-input']) ?>
            <div class="form-hint">
                <i class="fas fa-info-circle"></i> Поддерживаемые форматы: JPG, PNG, GIF, WEBP. Максимум 5MB
            </div>
            
            <div class="row" style="display: flex; flex-wrap: wrap; gap: 20px; margin-top: 10px;">
                <div style="flex: 1; min-width: 200px;">
                    <?= $form->field($model, 'password')->passwordInput(['maxlength' => true, 'placeholder' => 'Минимум 6 символов']) ?>
                </div>
                <div style="flex: 1; min-width: 200px;">
                    <?= $form->field($model, 'password_repeat')->passwordInput(['maxlength' => true, 'placeholder' => 'Повторите пароль']) ?>
                </div>
            </div>
            
            <?= $form->field($model, 'agree_terms')->checkbox([
                'template' => '<label class="checkbox-label">{input} <span>Я согласен с условиями использования</span></label>',
            ]) ?>
            
            <div class="form-group text-center" style="margin-top: 25px;">
                <?= Html::submitButton('<i class="fas fa-check-circle"></i> Зарегистрироваться', ['class' => 'back-btn']) ?>
            </div>
            
            <?php ActiveForm::end(); ?>
            
            <div class="register-footer">
                Уже есть аккаунт? <?= Html::a('Войти', ['auth/login']) ?>
            </div>
        </div>
    </div>
</div>