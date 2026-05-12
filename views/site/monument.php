<?php
use yii\helpers\Url;
use yii\helpers\Html;
$this->title = $monument->name;
?>

<div class="person-page">
    <div class="person-container">
        <div class="person-hero-vertical">
            <div class="person-photo-vertical">
                <?php if ($monument->img): ?>
                    <img src="<?= Yii::getAlias('@web/monument_imgs') . '/' . $monument->img ?>" 
                         alt="<?= Html::encode($monument->name) ?>">
                <?php else: ?>
                    <div class="no-photo-vertical">
                        <i class="fas fa-landmark fa-5x"></i>
                    </div>
                <?php endif; ?>
            </div>
            <div class="person-info-vertical">
                <h1 class="person-name">
                    <?= Html::encode($monument->name) ?>
                </h1>
                <p class="person-type">
                    <i class="fas fa-tag"></i> Тип: <?= Html::encode($monument->status) ?>
                </p>
                <?php if ($monument->countries): ?>
                    <p class="person-country">
                        <i class="fas fa-globe"></i>
                        <a href="<?= Url::to(['site/country', 'id' => $monument->countries->id]) ?>" style="text-decoration: none">
                        Страна: <?= Html::encode($monument->countries->name) ?>
                        </a>
                    </p>
                <?php endif; ?>
                <?php if ($monument->cities): ?>
                    <p class="person-city">
                        <i class="fas fa-city"></i> 
                        Город: <a href="<?= Url::to(['site/city', 'id' => $monument->cities->id]) ?>">
                            <?= Html::encode($monument->cities->name) ?>
                        </a>
                    </p>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="person-biography">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 15px;">
                <h2><i class="fas fa-info-circle"></i> Описание</h2>
                <button type="button" class="speak-btn" data-text="<?= strip_tags($monument->descr) ?>">
                    <i class="fas fa-volume-up"></i> Озвучить
                </button>
            </div>
            <div class="biography-content">
                <?= nl2br(Html::encode($monument->descr)) ?>
            </div>
        </div>
    </div>
</div>

<?= $this->render('_comments', [
    'comments' => $comments,
    'entityType' => 'monument',
    'entityId' => $monument->id
]) ?>

<div class="person-back">
    <a href="<?= Url::to(['site/catalog', 'type' => 'monuments']) ?>" class="back-btn">
        <i class="fas fa-arrow-left"></i> Вернуться к списку
    </a>
    <a href="<?= Url::to(['site/index']) ?>" class="back-btn">
        <i class="fas fa-map-marked-alt"></i> Вернуться на карту
    </a>
</div>