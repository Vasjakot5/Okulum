<?php
use yii\helpers\Url;
use yii\helpers\Html;
$this->title = $opening->name;
?>

<div class="person-page">
    <div class="person-container">
        <div class="person-hero-vertical">
            <div class="person-photo-vertical">
                <?php if ($opening->img): ?>
                    <img src="<?= Yii::getAlias('@web/openings_imgs') . '/' . $opening->img ?>" 
                         alt="<?= Html::encode($opening->name) ?>">
                <?php else: ?>
                    <div class="no-photo-vertical">
                        <i class="fas fa-compass fa-5x"></i>
                    </div>
                <?php endif; ?>
            </div>
            <div class="person-info-vertical">
                <h1 class="person-name">
                    <?= Html::encode($opening->name) ?>
                </h1>
                <p class="person-type">
                    <i class="fas fa-calendar"></i> Дата: <?= date('d.m.Y', strtotime($opening->date)) ?>
                </p>
                <?php if ($opening->countries): ?>
                    <p class="person-country">
                        <i class="fas fa-globe"></i> 
                        <a href="<?= Url::to(['site/country', 'id' => $opening->countries->id]) ?>" style="text-decoration: none">
                        Страна: <?= Html::encode($opening->countries->name) ?>
                        </a>
                    </p>
                <?php endif; ?>
                <?php if ($opening->cities): ?>
                    <p class="person-city">
                        <i class="fas fa-city"></i> 
                        Город: <a href="<?= Url::to(['site/city', 'id' => $opening->cities->id]) ?>">
                            <?= Html::encode($opening->cities->name) ?>
                        </a>
                    </p>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="person-biography">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 15px;">
                <h2><i class="fas fa-info-circle"></i> Описание открытия</h2>
                <button type="button" class="speak-btn" data-text="<?= strip_tags($opening->descr) ?>">
                    <i class="fas fa-volume-up"></i> Озвучить
                </button>
            </div>
            <div class="biography-content">
                <?= nl2br(Html::encode($opening->descr)) ?>
            </div>
        </div>
    </div>
</div>

<?= $this->render('_comments', [
    'comments' => $comments,
    'entityType' => 'opening',
    'entityId' => $opening->id
]) ?>

<div class="person-back">
    <a href="<?= Url::to(['site/catalog', 'type' => 'openings']) ?>" class="back-btn">
        <i class="fas fa-arrow-left"></i> Вернуться к списку
    </a>
    <a href="<?= Url::to(['site/index']) ?>" class="back-btn">
        <i class="fas fa-map-marked-alt"></i> Вернуться на карту
    </a>
</div>