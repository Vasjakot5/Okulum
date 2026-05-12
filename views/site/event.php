<?php
use yii\helpers\Url;
use yii\helpers\Html;
$this->title = $event->name;
?>

<div class="person-page">
    <div class="person-container">
        <div class="person-hero-vertical">
            <div class="person-photo-vertical">
                <?php if ($event->img): ?>
                    <img src="<?= Yii::getAlias('@web/events_imgs') . '/' . $event->img ?>" 
                         alt="<?= Html::encode($event->name) ?>">
                <?php else: ?>
                    <div class="no-photo-vertical">
                        <i class="fas fa-calendar-alt fa-5x"></i>
                    </div>
                <?php endif; ?>
            </div>
            <div class="person-info-vertical">
                <h1 class="person-name">
                    <?= Html::encode($event->name) ?>
                </h1>
                <p class="person-type">
                    <i class="fas fa-calendar"></i> Дата: <?= date('d.m.Y', strtotime($event->date)) ?>
                </p>
                <?php if ($event->countries): ?>
                    <p class="person-country">
                        <i class="fas fa-globe"></i>
                        <a href="<?= Url::to(['site/country', 'id' => $event->countries->id]) ?>" style="text-decoration: none">
                        Страна: <?= Html::encode($event->countries->name) ?>
                        </a>
                    </p>
                <?php endif; ?>
                <?php if ($event->cities): ?>
                    <p class="person-city">
                        <i class="fas fa-city"></i> 
                        Город: <a href="<?= Url::to(['site/city', 'id' => $event->cities->id]) ?>">
                            <?= Html::encode($event->cities->name) ?>
                        </a>
                    </p>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="person-biography">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 15px;">
                <h2><i class="fas fa-info-circle"></i> Описание события</h2>
                <button type="button" class="speak-btn" data-text="<?= strip_tags($event->descr) ?>">
                    <i class="fas fa-volume-up"></i> Озвучить
                </button>
            </div>
            <div class="biography-content">
                <?= nl2br(Html::encode($event->descr)) ?>
            </div>
        </div>
    </div>
</div>

<?= $this->render('_comments', [
    'comments' => $comments,
    'entityType' => 'event',
    'entityId' => $event->id
]) ?>

<div class="person-back">
    <a href="<?= Url::to(['site/catalog', 'type' => 'events']) ?>" class="back-btn">
        <i class="fas fa-arrow-left"></i> Вернуться к списку
    </a>
    <a href="<?= Url::to(['site/index']) ?>" class="back-btn">
        <i class="fas fa-map-marked-alt"></i> Вернуться на карту
    </a>
</div>