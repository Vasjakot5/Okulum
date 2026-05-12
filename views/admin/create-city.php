<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;

$this->title = 'Добавить город';
?>

<div class="create-city-page">
    <div class="create-city-container">
        <div class="create-city-header">
            <h1>
                <i class="fas fa-plus-circle"></i>
                <?= Html::encode($this->title) ?>
            </h1>
            <p>Заполните информацию о новом городе</p>
        </div>
        
        <div class="create-city-card">
            <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>
            
            <div class="row">
                <div class="col">
                    <?= $form->field($model, 'name')->textInput(['placeholder' => 'Название города', 'class' => 'form-control']) ?>
                </div>
                <div class="col">
                    <?= $form->field($model, 'population')->textInput(['placeholder' => 'Население', 'type' => 'number', 'class' => 'form-control']) ?>
                </div>
            </div>
            
            <div class="row">
                <div class="col">
                    <?= $form->field($model, 'flagFile')->fileInput(['class' => 'file-input', 'accept' => 'image/*']) ?>
                    <div class="form-hint">JPG, PNG, GIF, WEBP до 5MB</div>
                </div>
            </div>
            
            <div class="row">
                <div class="col">
                    <label class="control-label">Координаты на карте</label>
                    <div style="display: flex; gap: 15px;">
                        <div style="flex: 1;">
                            <?= $form->field($model, 'x')->textInput(['type' => 'number', 'step' => 1, 'class' => 'form-control', 'placeholder' => '0-100'])->label(false) ?>
                        </div>
                        <div style="flex: 1;">
                            <?= $form->field($model, 'y')->textInput(['type' => 'number', 'step' => 1, 'class' => 'form-control', 'placeholder' => '0-100'])->label(false) ?>
                        </div>
                    </div>
                    <div class="form-hint">По умолчанию 50, 50 (центр карты). Можно изменить позже на карте.</div>
                </div>
            </div>
            
            <div class="row">
                <div class="col">
                    <label class="control-label">Страны, в которых находится город</label>
                    <div class="countries-checkboxes">
                        <?php foreach ($countries as $country): ?>
                            <label class="checkbox-label">
                                <input type="checkbox" name="CityForm[countries][]" value="<?= $country->id ?>">
                                <span class="country-flag-mini">
                                    <?php if ($country->flag && file_exists(Yii::getAlias('@webroot/countries_imgs/' . $country->flag))): ?>
                                        <img src="<?= Yii::getAlias('@web/countries_imgs/' . $country->flag) ?>" style="width: 24px; height: 16px; object-fit: cover; margin-right: 8px;">
                                    <?php else: ?>
                                        <i class="fas fa-flag"></i>
                                    <?php endif; ?>
                                    <?= Html::encode($country->name) ?>
                                </span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                    <div class="form-hint">Выберите все страны, в которых расположен этот город</div>
                </div>
            </div>
            
            <?= $form->field($model, 'descr')->textarea(['rows' => 6, 'placeholder' => 'Описание города', 'class' => 'form-control']) ?>
            
            <div class="form-buttons">
                <?= Html::submitButton('<i class="fas fa-save"></i> Сохранить город', ['class' => 'back-btn']) ?>
            </div>
            
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>