<?php
use yii\helpers\Url;
use yii\helpers\Html;
$this->title = $person->name . ' ' . $person->last_name;
?>

<div class="person-page">
    <div class="person-container">
        <div class="person-hero">
            <div class="person-photo">
                <?php if ($person->img): ?>
                    <img src="<?= Yii::getAlias('@web/popular_humans_imgs') . '/' . $person->img ?>" 
                         alt="<?= Html::encode($person->name . ' ' . $person->last_name) ?>">
                <?php else: ?>
                    <div class="no-photo">
                        <i class="fas fa-user fa-5x"></i>
                    </div>
                <?php endif; ?>
            </div>
            <div class="person-info">
                <h1 class="person-name">
                    <?= Html::encode($person->name . ' ' . $person->last_name) ?>
                </h1>
                <?php if ($person->patronymic): ?>
                    <p class="person-patronymic"><?= Html::encode($person->patronymic) ?></p>
                <?php endif; ?>
                <p class="person-type">
                    <i class="fas fa-briefcase"></i> Должность:  <?= Html::encode($person->type) ?>
                </p>
                <p class="person-dates">
                    <i class="fas fa-calendar-alt"></i> 
                    Дата жизни и смерти: <?= date('d.m.Y', strtotime($person->date_born)) ?>
                    <?= $person->date_death ? ' - ' . date('d.m.Y', strtotime($person->date_death)) : ' - н.в.' ?>
                </p>
                <?php if ($person->countries): ?>
                    <p class="person-country">
                        <i class="fas fa-globe"></i>
                        <a href="<?= Url::to(['site/country', 'id' => $person->countries->id]) ?>" style="text-decoration: none"> 
                        Страна: <?= Html::encode($person->countries->name) ?>
                        </a>
                    </p>
                <?php endif; ?>
                <?php if ($person->cities): ?>
                    <p class="person-city">
                        <i class="fas fa-city"></i> 
                        <a href="<?= Url::to(['site/city', 'id' => $person->cities->id]) ?>">
                           Город: <?= Html::encode($person->cities->name) ?>
                        </a>
                    </p>
                <?php endif; ?>
            </div>
        </div>
        <?php if ($person->quote): ?>
        <div class="person-quote">
            <div class="quote-icon">
                <i class="fas fa-quote-left"></i>
            </div>
            <div class="quote-text">
                "<?= Html::encode($person->quote) ?>"
            </div>
        </div>
        <?php endif; ?>
        
        <div class="person-biography">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 15px;">
                <h2><i class="fas fa-book-open"></i> Биография</h2>
                <button type="button" class="speak-btn" data-text="<?= strip_tags($person->descr) ?>">
                    <i class="fas fa-volume-up"></i> Озвучить
                </button>
            </div>
            <div class="biography-content">
                <?= nl2br(Html::encode($person->descr)) ?>
            </div>
        </div>
    </div>
</div>

<?= $this->render('_comments', [
    'comments' => $comments,
    'entityType' => 'person',
    'entityId' => $person->id
]) ?>

<div class="person-back">
    <a href="<?= Url::to(['site/catalog', 'type' => 'humans']) ?>" class="back-btn">
        <i class="fas fa-arrow-left"></i> Вернуться к списку
    </a>
    <a href="<?= Url::to(['site/index']) ?>" class="back-btn">
        <i class="fas fa-map-marked-alt"></i> Вернуться на карту
    </a>
</div>