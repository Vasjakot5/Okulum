<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;

$this->title = 'Редактировать город: ' . Html::encode($model->name);

$countriesList = \app\models\Countries::find()->orderBy(['name' => SORT_ASC])->all();
?>

<div class="create-city-page">
    <div class="create-city-container">
        <div class="create-city-header">
            <h1>
                <i class="fas fa-edit"></i>
                <?= Html::encode($this->title) ?>
            </h1>
            <p>Редактирование информации о городе</p>
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
                    <div class="form-hint">JPG, PNG, GIF, WEBP до 5MB. Оставьте пустым, если не хотите менять флаг</div>
                    <?php if ($model->flag && file_exists(Yii::getAlias('@webroot/flags_imgs/' . $model->flag))): ?>
                    <div class="current-image" style="text-align: center; margin-top: 10px;">
                        <label style="color: #ffd700; display: block;">Текущий флаг:</label>
                        <img src="<?= Yii::getAlias('@web/flags_imgs/' . $model->flag) ?>" style="width: 120px; height: 80px; object-fit: cover; border-radius: 3px; margin: 0 auto; display: block;">
                        <small style="color: #888;">(останется, если не выберете новый)</small>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="row">
                <div class="col">
                    <label class="control-label">Координаты на карте</label>
                    <div style="display: flex; gap: 15px;">
                        <div style="flex: 1;">
                            <label style="color: #888; font-size: 12px;">X (горизонталь):</label>
                            <input type="number" name="x" id="city-x" class="form-control" step="1" value="<?= $currentX ?? 50 ?>" placeholder="0-100">
                        </div>
                        <div style="flex: 1;">
                            <label style="color: #888; font-size: 12px;">Y (вертикаль):</label>
                            <input type="number" name="y" id="city-y" class="form-control" step="1" value="<?= $currentY ?? 50 ?>" placeholder="0-100">
                        </div>
                    </div>
                    <div class="form-hint">По умолчанию 50, 50 (центр карты). Можно изменить позже на карте.</div>
                </div>
            </div>
            
            <div class="row">
                <div class="col">
                    <label class="control-label">Страны, в которых находится город</label>
                    <div class="countries-checkboxes">
                        <?php foreach ($countriesList as $country): ?>
                            <label class="checkbox-label">
                                <input type="checkbox" name="countries[]" value="<?= $country->id ?>" <?= in_array($country->id, $selectedCountries) ? 'checked' : '' ?>>
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
            
            <div class="form-buttons" style="display: flex; gap: 15px; justify-content: center; margin-top: 30px;">
                <?= Html::submitButton('<i class="fas fa-save"></i> Сохранить изменения', ['class' => 'back-btn']) ?>
                <?= Html::a('<i class="fas fa-times"></i> Отмена', ['cities'], ['class' => 'back-btn']) ?>
            </div>
            
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
