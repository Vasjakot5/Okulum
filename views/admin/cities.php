<?php
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Управление городами';

$countriesList = \app\models\Countries::find()->orderBy(['name' => SORT_ASC])->all();
?>

<div class="applications-page">
    <div class="applications-container">
        <div class="applications-header">
            <h1>
                <i class="fas fa-city"></i>
                <?= Html::encode($this->title) ?>
            </h1>
            <p>Управление городами и их расположением на карте</p>
        </div>
        
        <div class="applications-card">
            <div class="section-title">
                <i class="fas fa-list"></i> Список городов
                <div style="float: right;">
                    <?= Html::a('<i class="fas fa-plus"></i> Добавить город', ['create-city'], ['class' => 'back-btn status-badge']) ?>
                </div>
            </div>
            
            <div class="filter-buttons" style="margin-bottom: 20px; display: flex; gap: 10px; flex-wrap: wrap; align-items: center; justify-content: space-between;">
                <div style="display: flex; gap: 10px; flex-wrap: wrap; align-items: center;">
                    <span>Фильтр по стране:</span>
                    <select id="country-filter" class="back-btn status-badge" style="padding: 8px 15px;">
                        <option value="all">Все страны</option>
                        <?php foreach ($countriesList as $country): ?>
                            <option value="<?= $country->id ?>">
                                <?= Html::encode($country->name) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                    <input type="text" id="search-city" class="search-input status-badge" placeholder="Поиск по названию..." style="padding: 8px 15px; background: rgba(0,0,0,0.5); border: 1px solid rgba(255,255,255,0.1); border-radius: 30px; color: white; width: 250px;">
                    <button id="search-btn" class="back-btn status-badge">Найти</button>
                    <button id="reset-search" class="back-btn status-badge">Сбросить</button>
                </div>
            </div>
            
            <div id="cities-container">
                <div class="empty-state">
                    <i class="fas fa-spinner fa-spin"></i>
                    <p>Загрузка...</p>
                </div>
            </div>
        </div>
        
        <div class="text-center">
            <a href="<?= Url::to(['auth/profile']) ?>" class="back-btn" style="margin-top:10px">
                <i class="fas fa-arrow-left"></i> Назад в профиль
            </a>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    var isFirstLoad = true;
    
    function loadCities() {
        var countryId = $('#country-filter').val();
        var search = $('#search-city').val();
        
        $.ajax({
            url: '<?= Url::to(['admin/filter-cities']) ?>',
            type: 'GET',
            data: { country_id: countryId, search: search },
            dataType: 'json',
            beforeSend: function() {
                if (!isFirstLoad) {
                    $('#cities-container').html('<div class="empty-state"><i class="fas fa-spinner fa-spin"></i><p>Загрузка...</p></div>');
                }
            },
            success: function(response) {
                isFirstLoad = false;
                if (response.success) {
                    $('#cities-container').html(response.html);
                } else {
                    $('#cities-container').html('<div class="empty-state"><i class="fas fa-exclamation-triangle"></i><p>Ошибка загрузки</p></div>');
                }
            },
            error: function() {
                isFirstLoad = false;
                $('#cities-container').html('<div class="empty-state"><i class="fas fa-exclamation-triangle"></i><p>Ошибка загрузки</p></div>');
            }
        });
    }
    
    $('#country-filter').on('change', function() {
        loadCities();
    });

    $('#search-btn').on('click', function() {
        loadCities();
    });
    
    $('#reset-search').on('click', function() {
        $('#search-city').val('');
        loadCities();
    });
    
    $('#search-city').on('keypress', function(e) {
        if (e.which === 13) {
            loadCities();
        }
    });
    
    $(document).on('click', '.delete-city-btn', function(e) {
        e.preventDefault();
        var id = $(this).data('id');
        var name = $(this).data('name');
        
        if (confirm('Удалить город "' + name + '"?')) {
            $.ajax({
                url: '<?= Url::to(['admin/delete-city']) ?>',
                type: 'POST',
                data: { id: id },
                dataType: 'json',
                headers: {
                    'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        alert(response.message);
                        loadCities();
                    } else {
                        alert(response.message);
                    }
                },
                error: function() {
                    alert('Ошибка при удалении');
                }
            });
        }
    });
    
    loadCities();
});
</script>