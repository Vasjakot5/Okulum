<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Вход';
?>

<style>
.login-page {
    background-color: #121922;
    min-height: 100vh;
    padding: 40px 0;
}

.login-container {
    max-width: 450px;
    margin: 0 auto;
    padding: 0 20px;
}

.login-header {
    text-align: center;
    margin-bottom: 40px;
    animation: fadeInUp 0.6s ease forwards;
}

.login-header h1 {
    font-size: 36px;
    margin: 0;
    color: white;
}

.login-header h1 i {
    color: rgba(255, 0, 0, 0.7);
    margin-right: 15px;
}

.login-header p {
    color: rgba(255, 255, 255, 0.7);
    margin-top: 10px;
}

.login-card {
    background: rgba(0, 0, 0, 0.2);
    border-radius: 20px;
    padding: 30px;
    border: 1px solid rgba(255, 255, 255, 0.1);
    animation: fadeInUp 0.6s ease forwards;
}

.form-group {
    margin-bottom: 20px;
}

.control-label {
    display: block;
    margin-bottom: 8px;
    color: #ffd700;
    font-weight: 500;
}

.form-control {
    width: 100%;
    padding: 12px 15px;
    background: rgba(0, 0, 0, 0.5) !important;
    border: 1px solid rgba(255, 255, 255, 0.1) !important;
    border-radius: 10px;
    color: #ffffff !important;
    font-size: 14px;
    transition: all 0.3s;
}

.form-control:focus {
    background: rgba(0, 0, 0, 0.6) !important;
    border-color: rgba(255, 0, 0, 0.7) !important;
    outline: none;
}

.form-control::placeholder {
    color: rgba(255, 255, 255, 0.5);
}

.help-block {
    color: rgba(255, 100, 100, 0.8);
    font-size: 12px;
    margin-top: 5px;
}

.checkbox-label {
    display: flex;
    align-items: center;
    gap: 10px;
    cursor: pointer;
}

.checkbox-label input {
    width: 18px;
    height: 18px;
    cursor: pointer;
}

.checkbox-label span {
    color: rgba(255, 255, 255, 0.8);
    font-size: 14px;
}

.btn-login {
    width: 100%;
    padding: 12px;
    background: rgba(255, 0, 0, 0.7);
    border: none;
    border-radius: 30px;
    color: white;
    font-size: 16px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s;
}

.btn-login:hover {
    background: rgba(255, 0, 0, 0.9);
    transform: translateY(-2px);
}

.login-footer {
    text-align: center;
    margin-top: 20px;
    padding-top: 20px;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
}

.login-footer a {
    color: rgba(255, 0, 0, 0.7);
    text-decoration: none;
}

.login-footer a:hover {
    text-decoration: underline;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@media (max-width: 768px) {
    .login-card {
        padding: 20px;
    }
}
</style>

<div class="login-page">
    <div class="login-container">
        <div class="login-header">
            <h1>
                <i class="fas fa-sign-in-alt"></i>
                <?= Html::encode($this->title) ?>
            </h1>
            <p>Войдите в свой аккаунт</p>
        </div>
        
        <div class="login-card">
            <?php $form = ActiveForm::begin(['id' => 'login-form']); ?>
            
            <?= $form->field($model, 'email')->textInput(['autofocus' => true, 'placeholder' => 'example@mail.com']) ?>
            
            <?= $form->field($model, 'password')->passwordInput(['placeholder' => 'Введите пароль']) ?>
            
            <?= $form->field($model, 'rememberMe')->checkbox([
                'template' => '<label class="checkbox-label">{input} <span>Запомнить меня</span></label>',
            ]) ?>
            
            <div class="form-group text-center" style="margin-top: 25px;">
                <?= Html::submitButton('<i class="fas fa-arrow-right"></i> Войти', ['class' => 'back-btn', 'name' => 'login-button']) ?>
            </div>
            
            <?php ActiveForm::end(); ?>
            
            <div class="login-footer">
                Ещё не зарегистрированы? <?= Html::a('Зарегистрироваться', ['auth/register']) ?>
            </div>
        </div>
    </div>
</div>