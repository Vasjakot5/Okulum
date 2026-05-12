<?php
use yii\helpers\Url;
use yii\helpers\Html;
$this->title = $clothe->name;
?>

<div class="person-page">
    <div class="person-container">
        <div class="person-hero">
            <div class="person-photo">
                <?php if ($clothe->img): ?>
                    <img src="<?= Yii::getAlias('@web/clothes_imgs') . '/' . $clothe->img ?>" 
                         alt="<?= Html::encode($clothe->name) ?>">
                <?php else: ?>
                    <div class="no-photo">
                        <i class="fas fa-tshirt fa-5x"></i>
                    </div>
                <?php endif; ?>
            </div>
            <div class="person-info">
                <h1 class="person-name">
                    <?= Html::encode($clothe->name) ?>
                </h1>
                <p class="person-type">
                    <i class="fas fa-tag"></i> Тип: <?= Html::encode($clothe->status) ?>
                </p>
                <?php if ($clothe->countries): ?>
                    <p class="person-country">
                        <i class="fas fa-globe"></i>
                        <a href="<?= Url::to(['site/country', 'id' => $clothe->countries->id]) ?>" style="text-decoration: none">
                        Страна: <?= Html::encode($clothe->countries->name) ?>
                        </a>
                    </p>
                <?php endif; ?>
                <?php if ($clothe->cities): ?>
                    <p class="person-city">
                        <i class="fas fa-city"></i> 
                        <a href="<?= Url::to(['site/city', 'id' => $clothe->cities->id]) ?>">
                            Город: <?= Html::encode($clothe->cities->name) ?>
                        </a>
                    </p>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="person-biography">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 15px;">
                <h2><i class="fas fa-info-circle"></i> Описание</h2>
                <button type="button" class="speak-btn" data-text="<?= strip_tags($clothe->descr) ?>">
                    <i class="fas fa-volume-up"></i> Озвучить
                </button>
            </div>
            <div class="biography-content">
                <?= nl2br(Html::encode($clothe->descr)) ?>
            </div>
        </div>
    </div>
</div>

<?= $this->render('_comments', [
    'comments' => $comments,
    'entityType' => 'clothe',
    'entityId' => $clothe->id
]) ?>

<div class="person-back">
    <a href="<?= Url::to(['site/catalog', 'type' => 'clothes']) ?>" class="back-btn">
        <i class="fas fa-arrow-left"></i> Вернуться к списку
    </a>
    <a href="<?= Url::to(['site/index']) ?>" class="back-btn">
        <i class="fas fa-map-marked-alt"></i> Вернуться на карту
    </a>
</div>