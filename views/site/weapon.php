<?php
use yii\helpers\Url;
use yii\helpers\Html;
$this->title = $weapon->name;
?>

<div class="person-page">
    <div class="person-container">
        <div class="person-hero-vertical">
            <div class="person-photo-vertical">
                <?php if ($weapon->img): ?>
                    <img src="<?= Yii::getAlias('@web/weapon_imgs') . '/' . $weapon->img ?>" 
                         alt="<?= Html::encode($weapon->name) ?>">
                <?php else: ?>
                    <div class="no-photo-vertical">
                        <i class="fas fa-shield-alt fa-5x"></i>
                    </div>
                <?php endif; ?>
            </div>
            <div class="person-info-vertical">
                <h1 class="person-name">
                    <?= Html::encode($weapon->name) ?>
                </h1>
                <p class="person-type">
                    <i class="fas fa-tag"></i> Статус: <?= Html::encode($weapon->status) ?>
                </p>
                <?php if ($weapon->countries): ?>
                    <p class="person-country">
                        <i class="fas fa-globe"></i> 
                        <a href="<?= Url::to(['site/country', 'id' => $weapon->countries->id]) ?>" style="text-decoration: none">
                        Страна производства: <?= Html::encode($weapon->countries->name) ?>
                        </a>
                    </p>
                <?php endif; ?>
                <?php if ($weapon->cities): ?>
                    <p class="person-city">
                        <i class="fas fa-city"></i> 
                        <a href="<?= Url::to(['site/city', 'id' => $weapon->cities->id]) ?>">
                            Город производства: <?= Html::encode($weapon->cities->name) ?>
                        </a>
                    </p>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="person-biography">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 15px;">
                <h2><i class="fas fa-info-circle"></i> Описание</h2>
                <button type="button" class="speak-btn" data-text="<?= strip_tags($weapon->descr) ?>">
                    <i class="fas fa-volume-up"></i> Озвучить
                </button>
            </div>
            <div class="biography-content">
                <?= nl2br(Html::encode($weapon->descr)) ?>
            </div>
        </div>
    </div>
</div>

<?= $this->render('_comments', [
    'comments' => $comments,
    'entityType' => 'weapon',
    'entityId' => $weapon->id
]) ?>

<div class="person-back">
    <a href="<?= Url::to(['site/catalog', 'type' => 'weapons']) ?>" class="back-btn">
        <i class="fas fa-arrow-left"></i> Вернуться к списку
    </a>
    <a href="<?= Url::to(['site/index']) ?>" class="back-btn">
        <i class="fas fa-map-marked-alt"></i> Вернуться на карту
    </a>
</div>