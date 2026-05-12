<?php
use yii\helpers\Url;
use yii\helpers\Html;
$this->title = $vehicle->name;
?>

<div class="person-page">
    <div class="person-container">
        <div class="person-hero-vertical">
            <div class="person-photo-vertical">
                <?php if ($vehicle->img): ?>
                    <img src="<?= Yii::getAlias('@web/vehicles_imgs') . '/' . $vehicle->img ?>" 
                         alt="<?= Html::encode($vehicle->name) ?>">
                <?php else: ?>
                    <div class="no-photo-vertical">
                        <i class="fas fa-cogs fa-5x"></i>
                    </div>
                <?php endif; ?>
            </div>
            <div class="person-info-vertical">
                <h1 class="person-name">
                    <?= Html::encode($vehicle->name) ?>
                </h1>
                <p class="person-type">
                    <i class="fas fa-tag"></i> Тип: <?= Html::encode($vehicle->type) ?>
                </p>
                <p class="person-type">
                    <i class="fas fa-info-circle"></i> Статус: <?= Html::encode($vehicle->status) ?>
                </p>
                <?php if ($vehicle->countries): ?>
                    <p class="person-country">
                        <i class="fas fa-globe"></i> 
                        <a href="<?= Url::to(['site/country', 'id' => $vehicle->countries->id]) ?>" style="text-decoration: none">
                        Страна производства: <?= Html::encode($vehicle->countries->name) ?>
                        </a>
                    </p>
                <?php endif; ?>
                <?php if ($vehicle->cities): ?>
                    <p class="person-city">
                        <i class="fas fa-city"></i> 
                        Город производства: <a href="<?= Url::to(['site/city', 'id' => $vehicle->cities->id]) ?>">
                            <?= Html::encode($vehicle->cities->name) ?>
                        </a>
                    </p>
                <?php endif; ?>
            </div>
        </div>
        <div class="person-biography">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 15px;">
                <h2><i class="fas fa-info-circle"></i> Описание</h2>
                <button type="button" class="speak-btn" data-text="<?= strip_tags($vehicle->descr) ?>">
                    <i class="fas fa-volume-up"></i> Озвучить
                </button>
            </div>
            <div class="biography-content">
                <?= nl2br(Html::encode($vehicle->descr)) ?>
            </div>
        </div>
    </div>
</div>

<?= $this->render('_comments', [
    'comments' => $comments,
    'entityType' => 'vehicle',
    'entityId' => $vehicle->id
]) ?>

<div class="person-back">
    <a href="<?= Url::to(['site/catalog', 'type' => 'vehicles']) ?>" class="back-btn">
        <i class="fas fa-arrow-left"></i> Вернуться к списку
    </a>
    <a href="<?= Url::to(['site/index']) ?>" class="back-btn">
        <i class="fas fa-map-marked-alt"></i> Вернуться на карту
    </a>
</div>