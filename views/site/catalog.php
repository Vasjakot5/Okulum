<?php
use yii\helpers\Url;
use yii\helpers\Html;
$this->title = $title;
?>

<div class="catalog-page">
    <div class="container">
        <div class="catalog-header">
            <h1>
                <?php if ($type == 'all'): ?>
                    <i class="fas fa-book-open"></i> Все материалы
                <?php elseif ($type == 'humans'): ?>
                    <i class="fas fa-users"></i> <?= Html::encode($title) ?>
                <?php elseif ($type == 'vehicles'): ?>
                    <i class="fas fa-cogs"></i> <?= Html::encode($title) ?>
                <?php elseif ($type == 'openings'): ?>
                    <i class="fas fa-compass"></i> <?= Html::encode($title) ?>
                <?php elseif ($type == 'events'): ?>
                    <i class="fas fa-calendar-alt"></i> <?= Html::encode($title) ?>
                <?php elseif ($type == 'cities'): ?>
                    <i class="fas fa-city"></i> <?= Html::encode($title) ?>
                <?php elseif ($type == 'monuments'): ?>
                    <i class="fas fa-landmark"></i> <?= Html::encode($title) ?>
                <?php elseif ($type == 'weapons'): ?>
                    <i class="fas fa-shield-alt"></i> <?= Html::encode($title) ?>
                <?php elseif ($type == 'clothes'): ?>
                    <i class="fas fa-tshirt"></i> <?= Html::encode($title) ?>
                <?php else: ?>
                    <i class="fas fa-book-open"></i> <?= Html::encode($title) ?>
                <?php endif; ?>
            </h1>
        </div>
        
        <div class="country-selector">
            <a href="#" data-country="all" class="country-btn <?= $selectedCountryId == 'all' ? 'active' : '' ?>">
                Все страны
            </a>
            <a href="#" data-country="1" class="country-btn <?= $selectedCountryId == 1 ? 'active' : '' ?>">
                Российская империя
            </a>
            <a href="#" data-country="2" class="country-btn <?= $selectedCountryId == 2 ? 'active' : '' ?>">
                СССР
            </a>
            <a href="#" data-country="3" class="country-btn <?= $selectedCountryId == 3 ? 'active' : '' ?>">
                Российская Федерация
            </a>
        </div>
        
        <div class="catalog-types">
            <a href="#" data-type="all" class="type-btn <?= $type == 'all' ? 'active' : '' ?>">
                <i class="fas fa-star"></i> Все
            </a>
            <a href="#" data-type="cities" class="type-btn <?= $type == 'cities' ? 'active' : '' ?>">
                <i class="fas fa-city"></i> Города
            </a>
            <a href="#" data-type="events" class="type-btn <?= $type == 'events' ? 'active' : '' ?>">
                <i class="fas fa-calendar-alt"></i> События
            </a>
            <a href="#" data-type="openings" class="type-btn <?= $type == 'openings' ? 'active' : '' ?>">
                <i class="fas fa-compass"></i> Открытия
            </a>
            <a href="#" data-type="humans" class="type-btn <?= $type == 'humans' ? 'active' : '' ?>">
                <i class="fas fa-users"></i> Знаменитые люди
            </a>
            <a href="#" data-type="vehicles" class="type-btn <?= $type == 'vehicles' ? 'active' : '' ?>">
                <i class="fas fa-cogs"></i> Техника
            </a>
            <a href="#" data-type="monuments" class="type-btn <?= $type == 'monuments' ? 'active' : '' ?>">
                <i class="fas fa-landmark"></i> Памятники
            </a>
            <a href="#" data-type="weapons" class="type-btn <?= $type == 'weapons' ? 'active' : '' ?>">
                <i class="fas fa-shield-alt"></i> Оружие
            </a>
            <a href="#" data-type="clothes" class="type-btn <?= $type == 'clothes' ? 'active' : '' ?>">
                <i class="fas fa-tshirt"></i> Одежда
            </a>
        </div>
        
        <div class="search-container">
            <div class="search-wrapper">
                <input type="text" id="searchInput" placeholder="Поиск..." value="<?= Html::encode($searchQuery) ?>">
            </div>
            <div id="searchLoader" class="search-loader" style="display: none;">
                <div class="spinner"></div>
            </div>
        </div>
        
        <div id="alphabetFilter" class="alphabet-filter" style="display: <?= $searchQuery ? 'none' : 'flex'; ?>;">
            <a href="#" data-letter="all" class="letter-btn <?= $currentLetter == 'all' || !$currentLetter ? 'active' : '' ?>">
                Все
            </a>
            <?php foreach ($letters as $letter): ?>
                <a href="#" data-letter="<?= $letter ?>" class="letter-btn <?= $currentLetter == $letter ? 'active' : '' ?>">
                    <?= $letter ?>
                </a>
            <?php endforeach; ?>
        </div>
        
        <div id="catalogResults" class="catalog-results">
            <?= $this->render('_catalog_results', [
                'data' => $data, 
                'type' => $type,
                'totalPages' => $totalPages ?? 1,
                'currentPage' => $currentPage ?? 1,
                'totalCount' => $totalCount ?? count($data),
            ]) ?>
        </div>
    </div>
</div>