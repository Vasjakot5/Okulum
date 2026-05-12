<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

$this->title = 'Редактирование статьи';

$typeNames = [
    'event' => 'Событие',
    'opening' => 'Открытие',
    'human' => 'Знаменитость',
    'vehicle' => 'Техника',
    'monument' => 'Памятник',
    'weapon' => 'Оружие',
    'clothe' => 'Одежда',
];
?>

<div class="update-page">
    <div class="update-container">
        <div class="update-header">
            <h1>
                <i class="fas fa-edit"></i>
                Редактирование <?= $typeNames[$type] ?>
            </h1>
            <p>ID: <?= $article->id ?></p>
        </div>
        
        <div class="update-card">
            <?php $activeForm = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>
            
            <?php if ($type == 'event'): ?>
                <?= $activeForm->field($form, 'name')->textInput(['placeholder' => 'Название события']) ?>
                <?= $activeForm->field($form, 'date')->input('date') ?>
                <?= $activeForm->field($form, 'descr')->textarea(['rows' => 6, 'placeholder' => 'Описание события']) ?>
                <?= $activeForm->field($form, 'countries_id')->dropDownList($form->getCountriesList(), ['prompt' => 'Выберите страну']) ?>
                <?= $activeForm->field($form, 'cities_id')->dropDownList($form->getCitiesList(), ['prompt' => 'Выберите город']) ?>
                
            <?php elseif ($type == 'opening'): ?>
                <?= $activeForm->field($form, 'name')->textInput(['placeholder' => 'Название открытия']) ?>
                <?= $activeForm->field($form, 'date')->input('date') ?>
                <?= $activeForm->field($form, 'descr')->textarea(['rows' => 6, 'placeholder' => 'Описание открытия']) ?>
                <?= $activeForm->field($form, 'countries_id')->dropDownList($form->getCountriesList(), ['prompt' => 'Выберите страну']) ?>
                <?= $activeForm->field($form, 'cities_id')->dropDownList($form->getCitiesList(), ['prompt' => 'Выберите город']) ?>
                
            <?php elseif ($type == 'human'): ?>
                <div class="row">
                    <div><?= $activeForm->field($form, 'name')->textInput(['placeholder' => 'Имя']) ?></div>
                    <div><?= $activeForm->field($form, 'last_name')->textInput(['placeholder' => 'Фамилия']) ?></div>
                </div>
                <?= $activeForm->field($form, 'patronymic')->textInput(['placeholder' => 'Отчество']) ?>
                <?= $activeForm->field($form, 'type')->textInput(['placeholder' => 'Должность/Тип']) ?>
                <?= $activeForm->field($form, 'descr')->textarea(['rows' => 6, 'placeholder' => 'Биография']) ?>
                <?= $activeForm->field($form, 'quote')->textarea(['rows' => 3, 'placeholder' => 'Цитата']) ?>
                <div class="row">
                    <div><?= $activeForm->field($form, 'date_born')->input('date') ?></div>
                    <div><?= $activeForm->field($form, 'date_death')->input('date') ?></div>
                </div>
                <?= $activeForm->field($form, 'countries_id')->dropDownList($form->getCountriesList(), ['prompt' => 'Выберите страну']) ?>
                <?= $activeForm->field($form, 'cities_id')->dropDownList($form->getCitiesList(), ['prompt' => 'Выберите город']) ?>
                
            <?php elseif ($type == 'vehicle'): ?>
                <?= $activeForm->field($form, 'name')->textInput(['placeholder' => 'Название техники']) ?>
                <?= $activeForm->field($form, 'descr')->textarea(['rows' => 6, 'placeholder' => 'Описание']) ?>
                <div class="row">
                    <div><?= $activeForm->field($form, 'type')->textInput(['placeholder' => 'Тип (Военная/Гражданская)']) ?></div>
                    <div><?= $activeForm->field($form, 'status')->textInput(['placeholder' => 'Статус']) ?></div>
                </div>
                <?= $activeForm->field($form, 'countries_id')->dropDownList($form->getCountriesList(), ['prompt' => 'Выберите страну']) ?>
                <?= $activeForm->field($form, 'cities_id')->dropDownList($form->getCitiesList(), ['prompt' => 'Выберите город']) ?>
                
            <?php elseif ($type == 'monument'): ?>
                <?= $activeForm->field($form, 'name')->textInput(['placeholder' => 'Название памятника']) ?>
                <?= $activeForm->field($form, 'status')->textInput(['placeholder' => 'Тип памятника']) ?>
                <?= $activeForm->field($form, 'descr')->textarea(['rows' => 6, 'placeholder' => 'Описание']) ?>
                <?= $activeForm->field($form, 'countries_id')->dropDownList($form->getCountriesList(), ['prompt' => 'Выберите страну']) ?>
                <?= $activeForm->field($form, 'cities_id')->dropDownList($form->getCitiesList(), ['prompt' => 'Выберите город']) ?>
                
            <?php elseif ($type == 'weapon'): ?>
                <?= $activeForm->field($form, 'name')->textInput(['placeholder' => 'Название оружия']) ?>
                <?= $activeForm->field($form, 'status')->textInput(['placeholder' => 'Статус оружия']) ?>
                <?= $activeForm->field($form, 'descr')->textarea(['rows' => 6, 'placeholder' => 'Описание']) ?>
                <?= $activeForm->field($form, 'countries_id')->dropDownList($form->getCountriesList(), ['prompt' => 'Выберите страну']) ?>
                <?= $activeForm->field($form, 'cities_id')->dropDownList($form->getCitiesList(), ['prompt' => 'Выберите город']) ?>
                
            <?php elseif ($type == 'clothe'): ?>
                <?= $activeForm->field($form, 'name')->textInput(['placeholder' => 'Название одежды']) ?>
                <?= $activeForm->field($form, 'status')->textInput(['placeholder' => 'Тип одежды']) ?>
                <?= $activeForm->field($form, 'descr')->textarea(['rows' => 6, 'placeholder' => 'Описание']) ?>
                <?= $activeForm->field($form, 'countries_id')->dropDownList($form->getCountriesList(), ['prompt' => 'Выберите страну']) ?>
                <?= $activeForm->field($form, 'cities_id')->dropDownList($form->getCitiesList(), ['prompt' => 'Выберите город']) ?>
                
            <?php endif; ?>
            
            <?php if ($article->img): ?>
            <div class="current-image">
                <label>Текущее изображение</label><br>
                <?php
                $imgPath = '';
                if ($type == 'event') $imgPath = '@web/events_imgs/';
                elseif ($type == 'opening') $imgPath = '@web/openings_imgs/';
                elseif ($type == 'human') $imgPath = '@web/popular_humans_imgs/';
                elseif ($type == 'vehicle') $imgPath = '@web/vehicles_imgs/';
                elseif ($type == 'monument') $imgPath = '@web/monument_imgs/';
                elseif ($type == 'weapon') $imgPath = '@web/weapon_imgs/';
                elseif ($type == 'clothe') $imgPath = '@web/clothes_imgs/';
                ?>
                <img src="<?= Yii::getAlias($imgPath . $article->img) ?>" alt="<?= Html::encode($article->name) ?>">
            </div>
            <?php endif; ?>
            
            <?= $activeForm->field($form, 'img')->fileInput(['class' => 'file-input']) ?>
            <div class="form-hint">JPG, PNG, GIF, WEBP до 5MB. Оставьте пустым, если не хотите менять изображение.</div>
            
            <div class="form-buttons">
                <?= Html::submitButton('<i class="fas fa-save"></i> Сохранить', ['class' => 'back-btn']) ?>
                <?= Html::a('<i class="fas fa-times"></i> Отмена', ['application/my-applications'], ['class' => 'back-btn']) ?>
            </div>
            
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>