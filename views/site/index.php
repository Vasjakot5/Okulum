<?php
use yii\helpers\Url;
use yii\helpers\Html;
$this->title = 'Окулум';
?>

<div class="site-index">
    <div class="hello" id="helloSection">
        <div class="hello-section">
            <div class="hello-content">
                <div class="main">
                    <h1 class="text-center">
                        <img src="<?= Yii::getAlias('@web/imgs/icon.png') ?>" alt="Окулум" class="main-logo">кулум 
                    </h1>
                    <p class="subtitle">
                        История России под микроскопом
                    </p>
                </div>
                
                <div class="hello-quote-section">
                    <div class="hello-quote-text" id="quoteText">
                        <p class="quote-prefix">Загрузка...</p>
                        <p class="quote-text"></p>
                    </div>
                    <div class="hello-quote-image" id="quoteImage">
                        <div class="no-photo-small" style="width: 250px; height: 300px;">
                            <i class="fas fa-user fa-5x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="timeline-container" style="opacity: 0; animation: fadeInUp 0.6s ease forwards 0.2s;">
        <div class="timeline">
            <?php 
            $majorYears = [1721, 1922, 1991];
            $smallLinesBetween = 5;
            
            $countriesByYear = [];
            foreach ($countries as $country) {
                $year = (int)date('Y', strtotime($country->date_origin));
                if (in_array($year, $majorYears)) {
                    $countriesByYear[$year] = $country;
                }
            }
            
            $allLines = [];
            
            if (isset($countriesByYear[1721])) {
                $allLines[] = [
                    'isMajor' => true,
                    'year' => 1721,
                    'country' => $countriesByYear[1721]
                ];
            }
            
            for ($i = 0; $i < $smallLinesBetween; $i++) {
                $allLines[] = ['isMajor' => false];
            }
            
            if (isset($countriesByYear[1922])) {
                $allLines[] = [
                    'isMajor' => true,
                    'year' => 1922,
                    'country' => $countriesByYear[1922]
                ];
            }
            
            for ($i = 0; $i < $smallLinesBetween; $i++) {
                $allLines[] = ['isMajor' => false];
            }
            
            if (isset($countriesByYear[1991])) {
                $allLines[] = [
                    'isMajor' => true,
                    'year' => 1991,
                    'country' => $countriesByYear[1991]
                ];
            }
            ?>
            
            <?php foreach ($allLines as $index => $line): ?>
                <?php 
                $isMajor = $line['isMajor'];
                $country = $isMajor ? $line['country'] : null;
                $year = $isMajor ? $line['year'] : null;
                ?>
                
                <div class="timeline-item" style="opacity: 0; animation: fadeIn 0.5s ease forwards <?= 0.3 + ($index * 0.1) ?>s;">
                    <?php if ($isMajor && $country && $country->flag): ?>
                        <div class="flag-container" onclick="selectCountryAjax(<?= $country->id ?>)">
                            <img src="<?= Yii::getAlias('@web/flags_imgs') . '/' . $country->flag ?>" 
                                 alt="<?= Html::encode($country->name) ?>">
                        </div>
                    <?php endif; ?>
                    
                    <div class="timeline-line <?= $isMajor ? 'major' : 'minor' ?>"
                         <?php if ($isMajor && $country): ?>
                         onclick="selectCountryAjax(<?= $country->id ?>)"
                         <?php endif; ?>
                    >
                    </div>
                    
                    <?php if ($isMajor): ?>
                        <div class="timeline-year">
                            <?= $year ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div id="countryHeader" class="country-header" style="opacity: 0; animation: fadeInUp 0.6s ease forwards 0.4s;">
        <div id="countryHeaderContent">
            <?php if ($selectedCountry): ?>
                <?= $this->render('_header_content', ['selectedCountry' => $selectedCountry]) ?>
            <?php endif; ?>
        </div>
    </div>

    <div id="countryMap" class="country-map" style="opacity: 0; animation: fadeInUp 0.8s ease forwards 0.6s;">
        <div class="map-search-wrapper">
            <div class="map-search-container">
                <input type="text" id="mapCitySearch" placeholder="Поиск города на карте..." class="map-search-input">
                <div id="mapSearchResults" class="map-search-results"></div>
            </div>
        </div>
        
        <div id="countryMapContent">
            <?php if ($selectedCountry && $selectedCountry->map): ?>
                <?= $this->render('_map_content', [
                    'selectedCountry' => $selectedCountry,
                    'citiesWithPositions' => $citiesWithPositions,
                ]) ?>
            <?php else: ?>
                <div class="no-map">
                    <i class="fas fa-map"></i>
                    <p>Карта не найдена</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
window.getCountryDataUrl = '<?= Url::to(['site/get-country-data']) ?>';
window.cityViewUrl = '<?= Url::to(['site/city']) ?>';
window.popularHumansImgsPath = '<?= Yii::getAlias('@web/popular_humans_imgs') ?>';
window.currentCountryId = <?= $selectedCountry ? $selectedCountry->id : 'null' ?>;

window.popularHumansData = <?= json_encode(array_map(function($human) {
    return [
        'type' => $human->type,
        'country_name' => $human->countries_id == 1 ? 'Российской империи' : ($human->countries_id == 2 ? 'СССР' : ($human->countries_id == 3 ? 'Российской Федерации' : ($human->countries->name ?? $human->countries_id))),
        'quote' => $human->quote,
        'img' => $human->img,
        'name' => $human->name,
        'last_name' => $human->last_name,
        'patronymic' => $human->patronymic ?? '',
        'date_born' => $human->date_born,
        'date_death' => $human->date_death
    ];
}, $popularHumans)) ?>;

const mapSearchInput = document.getElementById('mapCitySearch');
const mapSearchResults = document.getElementById('mapSearchResults');
let searchTimeoutMap;
let currentCities = [];

function updateCitiesList() {
    const cityPoints = document.querySelectorAll('.city-point');
    currentCities = [];
    cityPoints.forEach(point => {
        currentCities.push({
            id: point.dataset.cityId,
            name: point.dataset.cityName,
            x: parseFloat(point.dataset.x),
            y: parseFloat(point.dataset.y)
        });
    });
}

function searchCities(searchQuery) {
    if (!searchQuery.trim()) {
        mapSearchResults.classList.remove('show');
        return;
    }
    
    const filtered = currentCities.filter(city => 
        city.name.toLowerCase().includes(searchQuery.toLowerCase())
    );
    
    if (filtered.length > 0) {
        mapSearchResults.innerHTML = filtered.map(city => `
            <div class="search-result-item" data-city-id="${city.id}" data-city-x="${city.x}" data-city-y="${city.y}">
                <div class="search-result-name">${escapeHtml(city.name)}</div>
                <div class="search-result-population">Нажмите для перехода</div>
            </div>
        `).join('');
        mapSearchResults.classList.add('show');
        
        document.querySelectorAll('.search-result-item').forEach(item => {
            item.addEventListener('click', function() {
                const cityId = this.dataset.cityId;
                if (cityId && cityId !== '0') {
                    window.location.href = window.cityViewUrl + '?id=' + cityId;
                }
            });
        });
    } else {
        mapSearchResults.innerHTML = '<div class="search-result-item">Ничего не найдено</div>';
        mapSearchResults.classList.add('show');
    }
}

if (mapSearchInput) {
    mapSearchInput.addEventListener('input', function() {
        clearTimeout(searchTimeoutMap);
        searchTimeoutMap = setTimeout(() => {
            updateCitiesList();
            searchCities(this.value);
        }, 300);
    });
    
    document.addEventListener('click', function(e) {
        if (!mapSearchInput.contains(e.target) && !mapSearchResults.contains(e.target)) {
            mapSearchResults.classList.remove('show');
        }
    });
}

function escapeHtml(str) {
    if (!str) return '';
    return str.replace(/[&<>]/g, function(m) {
        if (m === '&') return '&amp;';
        if (m === '<') return '&lt;';
        if (m === '>') return '&gt;';
        return m;
    });
}
</script>