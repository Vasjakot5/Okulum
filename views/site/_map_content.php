<?php
use yii\helpers\Html;
use yii\helpers\Url;
?>

<div class="map-wrapper">
    <div class="map-container" id="mapContainer">
        <img src="<?= Yii::getAlias('@web/countries_imgs') . '/' . $selectedCountry->map ?>" 
             alt="<?= Html::encode($selectedCountry->name) ?>"
             class="map-image" id="mapImage">
        
        <?php 
        $currentCountryCities = isset($citiesWithPositions[$selectedCountry->id]) 
            ? $citiesWithPositions[$selectedCountry->id] 
            : [];
        
        $countryColors = [
            1 => '#ff0000',
            2 => '#FFD800',
            3 => '#0094FF'
        ];
        $color = isset($countryColors[$selectedCountry->id]) ? $countryColors[$selectedCountry->id] : '#666';
        
        $isAdmin = !Yii::$app->user->isGuest && Yii::$app->user->identity->role == 1;
        
        foreach ($currentCountryCities as $city): 
            $x = isset($city['x']) && $city['x'] !== null ? $city['x'] : 50;
            $y = isset($city['y']) && $city['y'] !== null ? $city['y'] : 50;
        ?>
        <div class="city-point" 
             data-x="<?= $x ?>"
             data-y="<?= $y ?>"
             data-city-id="<?= isset($city['id']) ? $city['id'] : 0 ?>"
             data-city-name="<?= isset($city['name']) ? Html::encode($city['name']) : '' ?>">
            <span class="city-name"><?= isset($city['name']) ? Html::encode($city['name']) : 'Город' ?></span>
            <span class="city-diamond" style="background-color: <?= $color ?>;"></span>
        </div>
        <?php endforeach; ?>
    </div>
    <div class="map-controls">
        <div class="zoom-controls">
            <button class="zoom-btn" onclick="zoomIn()" title="Приблизить"><i class="fas fa-plus"></i></button>
            <button class="zoom-btn" onclick="zoomOut()" title="Отдалить"><i class="fas fa-minus"></i></button>
            <button class="zoom-btn" onclick="resetZoom()" title="Сбросить масштаб"><i class="fas fa-expand-alt"></i></button>
            <?php if ($isAdmin): ?>
            <button id="editCoordsModeBtn" class="zoom-btn edit-mode-btn" title="Режим редактирования (перетаскивай города мышкой)">
                <i class="fas fa-arrows-alt"></i>
            </button>
            <a href="<?= Url::to(['admin/create-city']) ?>" class="zoom-btn add-city-btn" title="Добавить город" style="text-decoration: none;">
                <i class="fas fa-plus"></i>
            </a>
            <?php endif; ?>
            <div class="zoom-level" id="zoomLevel">100%</div>
        </div>
    </div>

    <div class="burger-menu-container">
        <div class="burger-header" onclick="toggleBurgerMenu()">
            <div class="burger-lines">
                <div class="burger-line"></div>
                <div class="burger-line"></div>
                <div class="burger-line"></div>
            </div>
        </div>
        
        <div id="burgerDropdown" class="burger-dropdown">
            <a href="<?= Url::to(['site/catalog', 'type' => 'cities', 'country_id' => $selectedCountry->id ?? 1]) ?>" class="burger-menu-item">
                <i class="fas fa-city"></i>
                <span>Города</span>
            </a>
            <a href="<?= Url::to(['site/catalog', 'type' => 'events', 'country_id' => $selectedCountry->id ?? 1]) ?>" class="burger-menu-item">
                <i class="fas fa-calendar-alt"></i>
                <span>События</span>
            </a>
            <a href="<?= Url::to(['site/catalog', 'type' => 'openings', 'country_id' => $selectedCountry->id ?? 1]) ?>" class="burger-menu-item">
                <i class="fas fa-compass"></i>
                <span>Открытия</span>
            </a>
            <a href="<?= Url::to(['site/catalog', 'type' => 'humans', 'country_id' => $selectedCountry->id ?? 1]) ?>" class="burger-menu-item">
                <i class="fas fa-users"></i>
                <span>Знаменитые люди</span>
            </a>
            <a href="<?= Url::to(['site/catalog', 'type' => 'vehicles', 'country_id' => $selectedCountry->id ?? 1]) ?>" class="burger-menu-item">
                <i class="fas fa-cogs"></i>
                <span>Техника</span>
            </a>
            <a href="<?= Url::to(['site/catalog', 'type' => 'monuments', 'country_id' => $selectedCountry->id ?? 1]) ?>" class="burger-menu-item">
                <i class="fas fa-landmark"></i>
                <span>Памятники</span>
            </a>
            <a href="<?= Url::to(['site/catalog', 'type' => 'weapons', 'country_id' => $selectedCountry->id ?? 1]) ?>" class="burger-menu-item">
                <i class="fas fa-shield-alt"></i>
                <span>Оружие</span>
            </a>
            <a href="<?= Url::to(['site/catalog', 'type' => 'clothes', 'country_id' => $selectedCountry->id ?? 1]) ?>" class="burger-menu-item">
                <i class="fas fa-tshirt"></i>
                <span>Одежда</span>
            </a>
        </div>
    </div>
</div>

<?php if ($isAdmin): ?>
<div id="coordsModal" class="coords-modal" style="display: none;">
    <div class="coords-modal-content">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h3 style="color: #ffd700; margin: 0;"><i class="fas fa-map-marker-alt"></i> Редактирование координат</h3>
            <button class="close-modal-btn">&times;</button>
        </div>
        <div class="modal-body">
            <p><strong>Город:</strong> <span id="modal-city-name"></span></p>
            <p><strong>Страна:</strong> <span id="modal-country-name"></span></p>
            <div style="margin: 15px 0;">
                <label style="color: #ffd700;">Координата X (горизонталь):</label>
                <input type="number" id="coord-x" step="0.1" class="coord-input" placeholder="0-100">
            </div>
            <div style="margin: 15px 0;">
                <label style="color: #ffd700;">Координата Y (вертикаль):</label>
                <input type="number" id="coord-y" step="0.1" class="coord-input" placeholder="0-100">
            </div>
            <div style="margin: 15px 0;">
                <button id="pickOnMapBtn" class="pick-map-btn">
                    <i class="fas fa-mouse-pointer"></i> Выбрать на карте
                </button>
            </div>
        </div>
        <div style="display: flex; gap: 10px; margin-top: 20px;">
            <button id="saveCoordsBtn" class="save-coords-btn">Сохранить</button>
            <button id="cancelCoordsBtn" class="cancel-coords-btn">Отмена</button>
        </div>
    </div>
</div>

<div id="addCityModal" class="coords-modal" style="display: none;">
    <div class="coords-modal-content">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h3 style="color: #ffd700; margin: 0;"><i class="fas fa-plus-circle"></i> Добавить город</h3>
            <button class="close-add-modal-btn">&times;</button>
        </div>
        <div class="modal-body">
            <div style="margin: 15px 0;">
                <label style="color: #ffd700;">Название города:</label>
                <input type="text" id="new-city-name" class="coord-input" placeholder="Введите название города">
            </div>
            <div style="margin: 15px 0;">
                <label style="color: #ffd700;">Население:</label>
                <input type="number" id="new-city-population" class="coord-input" placeholder="Население">
            </div>
            <div style="margin: 15px 0;">
                <label style="color: #ffd700;">Флаг (необязательно):</label>
                <input type="file" id="new-city-flag" class="coord-input" accept="image/*">
            </div>
            <div style="margin: 15px 0;">
                <label style="color: #ffd700;">Описание:</label>
                <textarea id="new-city-descr" rows="4" class="coord-input" placeholder="Описание города"></textarea>
            </div>
            <div style="margin: 15px 0;">
                <label>Координаты:</label>
                <div style="display: flex; gap: 10px;">
                    <input type="number" id="new-city-x" step="0.1" class="coord-input" placeholder="X (0-100)" style="flex:1">
                    <input type="number" id="new-city-y" step="0.1" class="coord-input" placeholder="Y (0-100)" style="flex:1">
                </div>
                <button id="pickOnMapForNewBtn" class="pick-map-btn" style="margin-top: 10px; width: 100%;">
                    <i class="fas fa-mouse-pointer"></i> Выбрать на карте
                </button>
            </div>
        </div>
        <div style="display: flex; gap: 10px; margin-top: 20px;">
            <button id="saveNewCityBtn" class="save-coords-btn">Сохранить город</button>
            <button id="cancelAddCityBtn" class="cancel-coords-btn">Отмена</button>
        </div>
    </div>
</div>
<?php endif; ?>

<script>
window.currentCountryId = <?= $selectedCountry->id ?? 1 ?>;
window.updateCityAllCountriesUrl = "<?= Yii::$app->urlManager->createAbsoluteUrl(['admin/update-city-all-countries']) ?>";
window.currentCountryName = "<?= Html::encode($selectedCountry->name ?? '') ?>";
window.isAdmin = <?= (!Yii::$app->user->isGuest && Yii::$app->user->identity->role == 1) ? 'true' : 'false' ?>;
window.updateCoordsUrl = "<?= Yii::$app->urlManager->createAbsoluteUrl(['admin/update-city-coordinates']) ?>";
</script>