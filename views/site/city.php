<?php
use yii\helpers\Url;
use yii\helpers\Html;
$this->title = $city->name;

$cleanDescription = strip_tags($city->descr);
$cleanDescription = preg_replace('/\s+/', ' ', $cleanDescription);
$cleanDescription = trim($cleanDescription);
?>

<div class="site-city">
    <div class="city-header" style="opacity: 0; animation: fadeInUp 0.6s ease forwards;">
        <div class="city-header-content">
            <div class="city-info">
                <?php if ($city->flag): ?>
                    <img src="<?= Yii::getAlias('@web/flags_imgs') . '/' . $city->flag ?>" 
                         alt="<?= Html::encode($city->name) ?>"
                         class="city-flag">
                <?php endif; ?>
                <h1 class="city">
                    <?= Html::encode($city->name) ?>
                </h1>
                <?php if ($currentCountry): ?>
                <div class="city-country">
                    <?php 
                    $countriesList = [];
                    foreach ($city->countries as $country) {
                        $countriesList[] = Html::encode($country->name);
                    }
                    echo implode(' • ', $countriesList);
                    ?>
                </div>
                <?php endif; ?>
                <div class="person-city">
                    <i class="fas fa-users"></i> Население: <?= number_format($city->population, 0, '', ' ') ?> чел.
                </div>
            </div>
        </div>
    </div>

    <div class="city-description-container" style="opacity: 0; animation: fadeInUp 0.8s ease forwards 0.2s;">
        <div class="city-description">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 15px;">
                <h2 style="margin: 0;"><i class="fas fa-book-open"></i> Об истории города</h2>
                <button type="button" class="speak-btn" data-text="<?= Html::encode($cleanDescription) ?>">
                    <i class="fas fa-volume-up"></i> Озвучить
                </button>
            </div>
            <div class="description-text">
                <?= nl2br(Html::encode($city->descr)) ?>
            </div>
        </div>
    </div>

    <?php if (!empty($popularHumans)): ?>
    <div class="city-section" style="opacity: 0; animation: fadeInUp 0.8s ease forwards 0.4s;">
        <div class="section-header">
            <h2><i class="fas fa-users"></i> Знаменитые люди из этого города</h2>
        </div>
        <div class="items-grid">
            <?php foreach ($popularHumans as $human): ?>
            <a href="<?= Url::to(['site/person', 'id' => $human->id]) ?>" class="item-card-link">
                <div class="item-card">
                    <div class="item-row">
                        <?php if ($human->img): ?>
                            <img src="<?= Yii::getAlias('@web/popular_humans_imgs') . '/' . $human->img ?>" class="item-img square" alt="<?= Html::encode($human->name) ?>">
                        <?php else: ?>
                            <div class="item-no-img square">
                                <i class="fas fa-user"></i>
                            </div>
                        <?php endif; ?>
                        <div class="item-info">
                            <h3><?= Html::encode($human->name . ' ' . $human->last_name) ?></h3>
                            <p class="item-sub"><?= Html::encode($human->type) ?></p>
                            <p class="item-date">
                                <?= date('Y', strtotime($human->date_born)) ?>
                                <?= $human->date_death ? ' - ' . date('Y', strtotime($human->date_death)) : '' ?>
                            </p>
                        </div>
                    </div>
                    <p class="item-descr"><?= Html::encode(mb_substr($human->descr, 0, 120)) ?>...</p>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <?php if (!empty($events)): ?>
    <div class="city-section" style="opacity: 0; animation: fadeInUp 0.8s ease forwards 0.6s;">
        <div class="section-header">
            <h2><i class="fas fa-calendar-alt"></i> События в этом городе</h2>
        </div>
        <div class="items-grid">
            <?php foreach ($events as $event): ?>
            <a href="<?= Url::to(['site/event', 'id' => $event->id]) ?>" class="item-card-link">
                <div class="item-card">
                    <div class="item-row">
                        <?php if ($event->img): ?>
                            <img src="<?= Yii::getAlias('@web/events_imgs') . '/' . $event->img ?>" class="item-img square" alt="<?= Html::encode($event->name) ?>">
                        <?php else: ?>
                            <div class="item-no-img square">
                                <i class="fas fa-calendar-alt"></i>
                            </div>
                        <?php endif; ?>
                        <div class="item-info">
                            <h3><?= Html::encode($event->name) ?></h3>
                            <p class="item-sub"><?= date('Y', strtotime($event->date)) ?></p>
                        </div>
                    </div>
                    <p class="item-descr"><?= Html::encode(mb_substr($event->descr, 0, 120)) ?>...</p>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <?php if (!empty($openings)): ?>
    <div class="city-section" style="opacity: 0; animation: fadeInUp 0.8s ease forwards 0.8s;">
        <div class="section-header">
            <h2><i class="fas fa-compass"></i> Открытия и изобретения в этом городе</h2>
        </div>
        <div class="items-grid">
            <?php foreach ($openings as $opening): ?>
            <a href="<?= Url::to(['site/opening', 'id' => $opening->id]) ?>" class="item-card-link">
                <div class="item-card">
                    <div class="item-row">
                        <?php if ($opening->img): ?>
                            <img src="<?= Yii::getAlias('@web/openings_imgs') . '/' . $opening->img ?>" class="item-img square" alt="<?= Html::encode($opening->name) ?>">
                        <?php else: ?>
                            <div class="item-no-img square">
                                <i class="fas fa-compass"></i>
                            </div>
                        <?php endif; ?>
                        <div class="item-info">
                            <h3><?= Html::encode($opening->name) ?></h3>
                            <p class="item-sub"><?= date('Y', strtotime($opening->date)) ?></p>
                        </div>
                    </div>
                    <p class="item-descr"><?= Html::encode(mb_substr($opening->descr, 0, 120)) ?>...</p>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <?php if (!empty($vehicles)): ?>
    <div class="city-section" style="opacity: 0; animation: fadeInUp 0.8s ease forwards 1s;">
        <div class="section-header">
            <h2><i class="fas fa-cogs"></i> Техника из этого города</h2>
        </div>
        <div class="items-grid">
            <?php foreach ($vehicles as $vehicle): ?>
            <a href="<?= Url::to(['site/vehicle', 'id' => $vehicle->id]) ?>" class="item-card-link">
                <div class="item-card">
                    <div class="item-row">
                        <?php if ($vehicle->img): ?>
                            <img src="<?= Yii::getAlias('@web/vehicles_imgs') . '/' . $vehicle->img ?>" class="item-img square" alt="<?= Html::encode($vehicle->name) ?>">
                        <?php else: ?>
                            <div class="item-no-img square">
                                <i class="fas fa-cogs"></i>
                            </div>
                        <?php endif; ?>
                        <div class="item-info">
                            <h3><?= Html::encode($vehicle->name) ?></h3>
                            <p class="item-sub"><?= Html::encode($vehicle->type) ?> | <?= Html::encode($vehicle->status) ?></p>
                        </div>
                    </div>
                    <p class="item-descr"><?= Html::encode(mb_substr($vehicle->descr, 0, 120)) ?>...</p>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <?php if (!empty($monuments)): ?>
    <div class="city-section" style="opacity: 0; animation: fadeInUp 0.8s ease forwards 1.2s;">
        <div class="section-header">
            <h2><i class="fas fa-landmark"></i> Памятники в этом городе</h2>
        </div>
        <div class="items-grid">
            <?php foreach ($monuments as $monument): ?>
            <a href="<?= Url::to(['site/monument', 'id' => $monument->id]) ?>" class="item-card-link">
                <div class="item-card">
                    <div class="item-row">
                        <?php if ($monument->img): ?>
                            <img src="<?= Yii::getAlias('@web/monument_imgs') . '/' . $monument->img ?>" class="item-img square" alt="<?= Html::encode($monument->name) ?>">
                        <?php else: ?>
                            <div class="item-no-img square">
                                <i class="fas fa-landmark"></i>
                            </div>
                        <?php endif; ?>
                        <div class="item-info">
                            <h3><?= Html::encode($monument->name) ?></h3>
                            <p class="item-sub"><?= Html::encode($monument->status) ?></p>
                        </div>
                    </div>
                    <p class="item-descr"><?= Html::encode(mb_substr($monument->descr, 0, 120)) ?>...</p>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <?php if (!empty($weapons)): ?>
    <div class="city-section" style="opacity: 0; animation: fadeInUp 0.8s ease forwards 1.4s;">
        <div class="section-header">
            <h2><i class="fas fa-shield-alt"></i> Оружие из этого города</h2>
        </div>
        <div class="items-grid">
            <?php foreach ($weapons as $weapon): ?>
            <a href="<?= Url::to(['site/weapon', 'id' => $weapon->id]) ?>" class="item-card-link">
                <div class="item-card">
                    <div class="item-row">
                        <?php if ($weapon->img): ?>
                            <img src="<?= Yii::getAlias('@web/weapon_imgs') . '/' . $weapon->img ?>" class="item-img square" alt="<?= Html::encode($weapon->name) ?>">
                        <?php else: ?>
                            <div class="item-no-img square">
                                <i class="fas fa-shield-alt"></i>
                            </div>
                        <?php endif; ?>
                        <div class="item-info">
                            <h3><?= Html::encode($weapon->name) ?></h3>
                            <p class="item-sub"><?= Html::encode($weapon->status) ?></p>
                        </div>
                    </div>
                    <p class="item-descr"><?= Html::encode(mb_substr($weapon->descr, 0, 120)) ?>...</p>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <?php if (!empty($clothes)): ?>
    <div class="city-section" style="opacity: 0; animation: fadeInUp 0.8s ease forwards 1.6s;">
        <div class="section-header">
            <h2><i class="fas fa-tshirt"></i> Одежда из этого города</h2>
        </div>
        <div class="items-grid">
            <?php foreach ($clothes as $cloth): ?>
            <a href="<?= Url::to(['site/clothe', 'id' => $cloth->id]) ?>" class="item-card-link">
                <div class="item-card">
                    <div class="item-row">
                        <?php if ($cloth->img): ?>
                            <img src="<?= Yii::getAlias('@web/clothes_imgs') . '/' . $cloth->img ?>" class="item-img square" alt="<?= Html::encode($cloth->name) ?>">
                        <?php else: ?>
                            <div class="item-no-img square">
                                <i class="fas fa-tshirt"></i>
                            </div>
                        <?php endif; ?>
                        <div class="item-info">
                            <h3><?= Html::encode($cloth->name) ?></h3>
                            <p class="item-sub"><?= Html::encode($cloth->status) ?></p>
                        </div>
                    </div>
                    <p class="item-descr"><?= Html::encode(mb_substr($cloth->descr, 0, 120)) ?>...</p>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <?= $this->render('_comments', [
        'comments' => $comments,
        'entityType' => 'city',
        'entityId' => $city->id
    ]) ?>
    <div class="person-back">
        <a href="<?= Url::to(['site/catalog', 'type' => 'cities']) ?>" class="back-btn">
            <i class="fas fa-arrow-left"></i> Вернуться к списку
        </a>
        <a href="<?= Url::to(['site/index']) ?>" class="back-btn">
            <i class="fas fa-map-marked-alt"></i> Вернуться на карту
        </a>
    </div>
</div>