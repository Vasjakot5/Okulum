<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\MaskedInput;

$this->title = 'Редактирование профиля';
?>

<div class="update-profile-page">
    <div class="update-profile-container">
        <div class="update-profile-header">
            <h1>
                <i class="fas fa-edit"></i>
                <?= Html::encode($this->title) ?>
            </h1>
            <p>Измените свои данные</p>
        </div>
        
        <div class="update-profile-card">
            <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>
            
            <div class="row" style="display: flex; flex-wrap: wrap; gap: 30px;">
                <div style="flex: 2; min-width: 200px;">
                    <?= $form->field($user, 'name')->textInput(['maxlength' => true]) ?>
                    <?= $form->field($user, 'last_name')->textInput(['maxlength' => true]) ?>
                    <?= $form->field($user, 'email')->textInput(['maxlength' => true]) ?>
                    <?= $form->field($user, 'phone')->widget(MaskedInput::class, [
                        'mask' => '+7(999)999-99-99',
                    ]) ?>
                </div>
                <div style="flex: 1; text-align: center;">
                    <div class="current-avatar">
                        <label>Текущий аватар</label>
                        <?php if ($user->photo && file_exists(Yii::getAlias('@webroot/avatars/' . $user->photo))): ?>
                            <img src="<?= Yii::getAlias('@web/avatars/' . $user->photo) ?>" alt="Avatar">
                        <?php else: ?>
                            <div class="default-avatar">
                                <i class="fas fa-user"></i>
                            </div>
                        <?php endif; ?>
                    </div>
                    <?= $form->field($user, 'avatar_file')->fileInput(['accept' => 'image/*', 'class' => 'file-input']) ?>
                    <div class="form-hint">
                        <i class="fas fa-info-circle"></i> JPG, PNG, GIF, WEBP. Максимум 5MB
                    </div>
                </div>
            </div>
            
            <div class="form-buttons">
                <?= Html::submitButton('<i class="fas fa-save"></i> Сохранить', ['class' => 'profile-btn profile-btn-danger']) ?>
                <?= Html::a('<i class="fas fa-times"></i> Отмена', ['auth/profile'], ['class' => 'profile-btn']) ?>
            </div>
            
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>