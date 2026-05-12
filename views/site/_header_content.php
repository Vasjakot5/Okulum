<?php
use yii\helpers\Html;
use yii\helpers\Url;
?>

<div class="country-info">
    <?php if ($selectedCountry->flag): ?>
        <img src="<?= Yii::getAlias('@web/flags_imgs') . '/' . $selectedCountry->flag ?>" 
             alt="<?= Html::encode($selectedCountry->name) ?>"
             class="country-flag">
    <?php endif; ?>
    <h1 class="country-name">
        <?= Html::encode($selectedCountry->name) ?>
    </h1>
    <div class="country-years">
        <?= date('Y', strtotime($selectedCountry->date_origin)) ?>
        <?= $selectedCountry->date_end ? ' - ' . date('Y', strtotime($selectedCountry->date_end)) : ' - Настоящее время' ?>
    </div>
    <a href="<?= Url::to(['site/country', 'id' => $selectedCountry->id]) ?>" class="country-details-btn">
        <i class="fas fa-info-circle"></i> Подробнее о стране
    </a>
</div>