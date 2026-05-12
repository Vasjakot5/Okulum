<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

$this->title = 'Добавить статью';

$user = Yii::$app->user->identity;
$isAdmin = ($user->role == 1);
?>

<div class="create-page">
    <div class="create-container">
        <div class="create-header">
            <h1>
                <i class="fas fa-pen-alt"></i>
                Добавить статью
            </h1>
            <p>Выберите тип материала</p>
            <?php if ($isAdmin): ?>
                <div class="status-closed status-badge">
                    <i class="fas fa-user-shield"></i> Вы администратор — ваши статьи будут опубликованы сразу (без модерации)
                </div>
            <?php endif; ?>
        </div>
        
        <div class="type-tabs">
            <a href="#" class="type-tab back-btn" data-type="person"><i class="fas fa-users"></i> Знаменитость</a>
            <a href="#" class="type-tab back-btn" data-type="vehicle"><i class="fas fa-cogs"></i> Техника</a>
            <a href="#" class="type-tab back-btn" data-type="opening"><i class="fas fa-compass"></i> Открытие</a>
            <a href="#" class="type-tab back-btn" data-type="event"><i class="fas fa-calendar-alt"></i> Событие</a>
            <a href="#" class="type-tab back-btn" data-type="monument"><i class="fas fa-landmark"></i> Памятник</a>
            <a href="#" class="type-tab back-btn" data-type="weapon"><i class="fas fa-shield-alt"></i> Оружие</a>
            <a href="#" class="type-tab back-btn" data-type="clothe"><i class="fas fa-tshirt"></i> Одежда</a>
        </div>
        
        <div class="form-card form-event active" id="form-event">
            <?php $form = ActiveForm::begin(['action' => Url::to(['application/create-article', 'type' => 'event']), 'options' => ['enctype' => 'multipart/form-data']]); ?>
            <?= $form->field($eventForm, 'name')->textInput(['placeholder' => 'Название события']) ?>
            <?= $form->field($eventForm, 'img')->fileInput(['class' => 'file-input']) ?>
            <div class="form-hint">JPG, PNG, GIF, WEBP до 5MB</div>
            <?= $form->field($eventForm, 'date')->input('date') ?>
            <?= $form->field($eventForm, 'descr')->textarea(['rows' => 6, 'placeholder' => 'Описание события']) ?>
            <?= $form->field($eventForm, 'countries_id')->dropDownList($eventForm->getCountriesList(), ['prompt' => 'Выберите страну']) ?>
            <?= $form->field($eventForm, 'cities_id')->dropDownList($eventForm->getCitiesList(), ['prompt' => 'Выберите город']) ?>
            <div class="text-center">
                <?= Html::submitButton('<i class="fas fa-paper-plane"></i> ' . ($isAdmin ? 'Опубликовать' : 'Отправить на модерацию'), ['class' => 'back-btn']) ?>          
            </div>
            <?php ActiveForm::end(); ?>
        </div>
        
        <div class="form-card form-opening" id="form-opening">
            <?php $form = ActiveForm::begin(['action' => Url::to(['application/create-article', 'type' => 'opening']), 'options' => ['enctype' => 'multipart/form-data']]); ?>
            <?= $form->field($openingForm, 'name')->textInput(['placeholder' => 'Название открытия']) ?>
            <?= $form->field($openingForm, 'img')->fileInput(['class' => 'file-input']) ?>
            <div class="form-hint">JPG, PNG, GIF, WEBP до 5MB</div>
            <?= $form->field($openingForm, 'date')->input('date') ?>
            <?= $form->field($openingForm, 'descr')->textarea(['rows' => 6, 'placeholder' => 'Описание открытия']) ?>
            <?= $form->field($openingForm, 'countries_id')->dropDownList($openingForm->getCountriesList(), ['prompt' => 'Выберите страну']) ?>
            <?= $form->field($openingForm, 'cities_id')->dropDownList($openingForm->getCitiesList(), ['prompt' => 'Выберите город']) ?>
            <div class="text-center">
                <?= Html::submitButton('<i class="fas fa-paper-plane"></i> ' . ($isAdmin ? 'Опубликовать' : 'Отправить на модерацию'), ['class' => 'back-btn']) ?>          
            </div>
            <?php ActiveForm::end(); ?>
        </div>
        
        <div class="form-card form-person" id="form-person">
            <?php $form = ActiveForm::begin(['action' => Url::to(['application/create-article', 'type' => 'person']), 'options' => ['enctype' => 'multipart/form-data']]); ?>
            <div class="row">
                <div><?= $form->field($personForm, 'name')->textInput(['placeholder' => 'Имя']) ?></div>
                <div><?= $form->field($personForm, 'last_name')->textInput(['placeholder' => 'Фамилия']) ?></div>
            </div>
            <?= $form->field($personForm, 'patronymic')->textInput(['placeholder' => 'Отчество (если есть)']) ?>
            <?= $form->field($personForm, 'img')->fileInput(['class' => 'file-input']) ?>
            <div class="form-hint">JPG, PNG, GIF, WEBP до 5MB</div>
            <?= $form->field($personForm, 'type')->textInput(['placeholder' => 'Должность/Тип (Император, Ученый, Писатель...)']) ?>
            <?= $form->field($personForm, 'descr')->textarea(['rows' => 6, 'placeholder' => 'Биография']) ?>
            <?= $form->field($personForm, 'quote')->textarea(['rows' => 3, 'placeholder' => 'Цитата (если есть)']) ?>
            <div class="row">
                <div><?= $form->field($personForm, 'date_born')->input('date') ?></div>
                <div><?= $form->field($personForm, 'date_death')->input('date') ?></div>
            </div>
            <?= $form->field($personForm, 'countries_id')->dropDownList($personForm->getCountriesList(), ['prompt' => 'Выберите страну']) ?>
            <?= $form->field($personForm, 'cities_id')->dropDownList($personForm->getCitiesList(), ['prompt' => 'Выберите город']) ?>
            <div class="text-center">
                <?= Html::submitButton('<i class="fas fa-paper-plane"></i> ' . ($isAdmin ? 'Опубликовать' : 'Отправить на модерацию'), ['class' => 'back-btn']) ?>          
            </div>
            <?php ActiveForm::end(); ?>
        </div>
        
        <div class="form-card form-vehicle" id="form-vehicle">
            <?php $form = ActiveForm::begin(['action' => Url::to(['application/create-article', 'type' => 'vehicle']), 'options' => ['enctype' => 'multipart/form-data']]); ?>
            <?= $form->field($vehicleForm, 'name')->textInput(['placeholder' => 'Название техники']) ?>
            <?= $form->field($vehicleForm, 'img')->fileInput(['class' => 'file-input']) ?>
            <div class="form-hint">JPG, PNG, GIF, WEBP до 5MB</div>
            <?= $form->field($vehicleForm, 'descr')->textarea(['rows' => 6, 'placeholder' => 'Описание']) ?>
            <div class="row">
                <div><?= $form->field($vehicleForm, 'type')->dropDownList([
                    'Военная' => 'Военная',
                    'Гражданская' => 'Гражданская',
                ], ['prompt' => 'Выберите тип']) ?></div>
                <div><?= $form->field($vehicleForm, 'status')->dropDownList([
                    'В строю' => 'В строю',
                    'Музейный экспонат' => 'Музейный экспонат',
                    'Списан' => 'Списан',
                    'Опытный образец' => 'Опытный образец',
                ], ['prompt' => 'Выберите статус']) ?></div>
            </div>
            <?= $form->field($vehicleForm, 'countries_id')->dropDownList($vehicleForm->getCountriesList(), ['prompt' => 'Выберите страну']) ?>
            <?= $form->field($vehicleForm, 'cities_id')->dropDownList($vehicleForm->getCitiesList(), ['prompt' => 'Выберите город']) ?>
            <div class="text-center">
                <?= Html::submitButton('<i class="fas fa-paper-plane"></i> ' . ($isAdmin ? 'Опубликовать' : 'Отправить на модерацию'), ['class' => 'back-btn']) ?>          
            </div>
            <?php ActiveForm::end(); ?>
        </div>
        
        <div class="form-card form-monument" id="form-monument">
            <?php $form = ActiveForm::begin(['action' => Url::to(['application/create-article', 'type' => 'monument']), 'options' => ['enctype' => 'multipart/form-data']]); ?>
            <?= $form->field($monumentForm, 'name')->textInput(['placeholder' => 'Название памятника']) ?>
            <?= $form->field($monumentForm, 'img')->fileInput(['class' => 'file-input']) ?>
            <div class="form-hint">JPG, PNG, GIF, WEBP до 5MB</div>
            <?= $form->field($monumentForm, 'status')->dropDownList([
                'Памятник' => 'Памятник',
                'Скульптура' => 'Скульптура',
                'Мемориал' => 'Мемориал',
                'Картина' => 'Картина',
                'Монумент' => 'Монумент',
                'Стела' => 'Стела',
                'Бюст' => 'Бюст',
                'Фонтан' => 'Фонтан',
                'Арка' => 'Арка',
                'Церковь' => 'Церковь',
                'Часовня' => 'Часовня',
            ], ['prompt' => 'Выберите тип']) ?>
            <?= $form->field($monumentForm, 'descr')->textarea(['rows' => 6, 'placeholder' => 'Описание']) ?>
            <?= $form->field($monumentForm, 'countries_id')->dropDownList($monumentForm->getCountriesList(), ['prompt' => 'Выберите страну']) ?>
            <?= $form->field($monumentForm, 'cities_id')->dropDownList($monumentForm->getCitiesList(), ['prompt' => 'Выберите город']) ?>
            <div class="text-center">
                <?= Html::submitButton('<i class="fas fa-paper-plane"></i> ' . ($isAdmin ? 'Опубликовать' : 'Отправить на модерацию'), ['class' => 'back-btn']) ?>          
            </div>
            <?php ActiveForm::end(); ?>
        </div>
        
        <div class="form-card form-weapon" id="form-weapon">
            <?php $form = ActiveForm::begin(['action' => Url::to(['application/create-article', 'type' => 'weapon']), 'options' => ['enctype' => 'multipart/form-data']]); ?>
            <?= $form->field($weaponForm, 'name')->textInput(['placeholder' => 'Название оружия']) ?>
            <?= $form->field($weaponForm, 'img')->fileInput(['class' => 'file-input']) ?>
            <div class="form-hint">JPG, PNG, GIF, WEBP до 5MB</div>
            <?= $form->field($weaponForm, 'status')->dropDownList([
                'Состоит на вооружении' => 'Состоит на вооружении',
                'Снято с вооружения' => 'Снято с вооружения',
                'Состоит на вооружении (ограниченно)' => 'Состоит на вооружении (ограниченно)',
            ], ['prompt' => 'Выберите статус']) ?>
            <?= $form->field($weaponForm, 'descr')->textarea(['rows' => 6, 'placeholder' => 'Описание']) ?>
            <?= $form->field($weaponForm, 'countries_id')->dropDownList($weaponForm->getCountriesList(), ['prompt' => 'Выберите страну']) ?>
            <?= $form->field($weaponForm, 'cities_id')->dropDownList($weaponForm->getCitiesList(), ['prompt' => 'Выберите город']) ?>
            <div class="text-center">
                <?= Html::submitButton('<i class="fas fa-paper-plane"></i> ' . ($isAdmin ? 'Опубликовать' : 'Отправить на модерацию'), ['class' => 'back-btn']) ?>          
            </div>
            <?php ActiveForm::end(); ?>
        </div>
        
        <div class="form-card form-clothe" id="form-clothe">
            <?php $form = ActiveForm::begin(['action' => Url::to(['application/create-article', 'type' => 'clothe']), 'options' => ['enctype' => 'multipart/form-data']]); ?>
            <?= $form->field($clotheForm, 'name')->textInput(['placeholder' => 'Название одежды']) ?>
            <?= $form->field($clotheForm, 'img')->fileInput(['class' => 'file-input']) ?>
            <div class="form-hint">JPG, PNG, GIF, WEBP до 5MB</div>
            <?= $form->field($clotheForm, 'status')->dropDownList([
                'Народный костюм' => 'Народный костюм',
                'Военная форма' => 'Военная форма',
            ], ['prompt' => 'Выберите тип']) ?>
            <?= $form->field($clotheForm, 'descr')->textarea(['rows' => 6, 'placeholder' => 'Описание']) ?>
            <?= $form->field($clotheForm, 'countries_id')->dropDownList($clotheForm->getCountriesList(), ['prompt' => 'Выберите страну']) ?>
            <?= $form->field($clotheForm, 'cities_id')->dropDownList($clotheForm->getCitiesList(), ['prompt' => 'Выберите город']) ?>
            <div class="text-center">
                <?= Html::submitButton('<i class="fas fa-paper-plane"></i> ' . ($isAdmin ? 'Опубликовать' : 'Отправить на модерацию'), ['class' => 'back-btn']) ?>          
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>