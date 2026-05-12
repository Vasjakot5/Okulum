<?php
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Управление пользователями';

$user = Yii::$app->user->identity;
$isAdmin = ($user->role == 1);

$filterStatus = Yii::$app->request->get('filter', 'all');
$searchQuery = Yii::$app->request->get('search', '');
?>

<div class="users-page">
    <div class="users-container">
        <div class="users-header">
            <h1>
                <i class="fas fa-users"></i>
                <?= Html::encode($this->title) ?>
            </h1>
            <p>Управление пользователями системы</p>
        </div>
        
        <div class="users-card">
            <div class="section-title">
                <i class="fas fa-user-friends"></i> Список пользователей
                <div style="float: right; display: flex; gap: 10px;">
                    <span class="badge" style="background: rgba(0, 0, 0, 0.48); padding: 3px 10px; border-radius: 20px; font-size: 12px; border: 1px solid rgba(105, 105, 105, 0.48)">
                        Всего: <span id="total-count"><?= count($users) ?></span>
                    </span>
                    <span class="badge" style="background: rgba(0, 0, 0, 0.48); padding: 3px 10px; border-radius: 20px; font-size: 12px; border: 1px solid rgba(105, 105, 105, 0.48)">
                        Заблокированы: <span id="banned-count"><?= count(array_filter($users, function($u) { return $u->isBanned(); })) ?></span>
                    </span>
                    <span class="badge" style="background: rgba(0, 0, 0, 0.48); padding: 3px 10px; border-radius: 20px; font-size: 12px; border: 1px solid rgba(105, 105, 105, 0.48)">
                        Активны: <span id="active-count"><?= count(array_filter($users, function($u) { return !$u->isBanned(); })) ?></span>
                    </span>
                </div>
            </div>
            
            <div class="filter-buttons" style="display: flex; gap: 10px; flex-wrap: wrap; justify-content: space-between; align-items: center;">
                <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                    <button class="filter-btn <?= $filterStatus == 'all' ? 'active' : '' ?>" data-filter="all">
                        Все пользователи
                    </button>
                    <button class="filter-btn <?= $filterStatus == 'active' ? 'active' : '' ?>" data-filter="active">
                        Активные
                    </button>
                    <button class="filter-btn <?= $filterStatus == 'banned' ? 'active' : '' ?>" data-filter="banned">
                        Заблокированные
                    </button>
                    <button class="filter-btn<?= $filterStatus == 'admin' ? 'active' : '' ?>" data-filter="admin">
                        Администраторы
                    </button>
                </div>
                
                <div style="display: flex; gap: 10px;">
                    <input type="text" id="search-input" class="search-input" placeholder="Поиск по имени, email..." value="<?= Html::encode($searchQuery) ?>" style="padding: 8px 15px; background: rgba(0,0,0,0.5); border: 1px solid rgba(255,255,255,0.1); border-radius: 30px; color: white; width: 250px;">
                    <button id="search-btn" class="filter-btn">
                        <i class="fas fa-search"></i> Найти
                    </button>
                    <button id="reset-search" class="filter-btn">
                        <i class="fas fa-times"></i> Сброс
                    </button>
                </div>
            </div>
            
            <div id="users-container">
                <?= $this->render('_users_table', [
                    'users' => $users,
                    'filterStatus' => $filterStatus,
                    'searchQuery' => $searchQuery
                ]) ?>
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
    function loadUsers() {
        var filter = $('.filter-btn.active').data('filter') || 'all';
        var search = $('#search-input').val();
        
        $.ajax({
            url: '<?= Url::to(['admin/filter-users']) ?>',
            type: 'GET',
            data: { filter: filter, search: search },
            dataType: 'json',
            beforeSend: function() {
                $('#users-container').html('<div class="empty-state"><i class="fas fa-spinner fa-spin"></i><p>Загрузка...</p></div>');
            },
            success: function(response) {
                if (response.success) {
                    $('#users-container').html(response.html);
                    $('#total-count').text(response.total);
                    $('#banned-count').text(response.banned);
                    $('#active-count').text(response.active);
                } else {
                    $('#users-container').html('<div class="empty-state"><i class="fas fa-exclamation-triangle"></i><p>Ошибка загрузки</p></div>');
                }
            },
            error: function() {
                $('#users-container').html('<div class="empty-state"><i class="fas fa-exclamation-triangle"></i><p>Ошибка загрузки</p></div>');
            }
        });
    }
    
    $('.filter-btn').on('click', function() {
        $('.filter-btn').removeClass('active');
        $(this).addClass('active');
        loadUsers();
    });
    
    $('#search-btn').on('click', function() {
        loadUsers();
    });
    
    $('#search-input').on('keypress', function(e) {
        if (e.which === 13) {
            loadUsers();
        }
    });
    
    $('#reset-search').on('click', function() {
        $('#search-input').val('');
        loadUsers();
    });
});
</script>