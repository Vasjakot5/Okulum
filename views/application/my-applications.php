<?php
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Заявки и статьи';

$user = Yii::$app->user->identity;
$isAdmin = ($user->role == 1);
$isBanned = $user->isBanned();

$filterStatus = Yii::$app->request->get('filter', 'all');
$appFilterStatus = Yii::$app->request->get('app_filter', 'all');
?>

<div class="applications-page">
    <div class="applications-container">
        <div class="applications-header">
            <h1>
                <i class="fas fa-list"></i>
                <?= Html::encode($this->title) ?>
            </h1>
            <p><?= $isAdmin ? 'Управление заявками и статьями пользователей' : 'Ваши обращения и статьи на модерации' ?></p>
        </div>
        
        <div class="applications-card">
            <div class="section-title">
                <i class="fas fa-ticket-alt"></i> Заявки в поддержку
                <?php if ($isAdmin): ?>
                    <div style="float: right; display: flex; gap: 10px;">
                        <span class="badge" style="background: rgba(0, 0, 0, 0.48); padding: 3px 10px; border-radius: 20px; font-size: 12px; border: 1px solid rgba(105, 105, 105, 0.48)">
                            Всего: <span id="app-total-count"><?= count($applications) ?></span>
                        </span>
                        <span class="badge" style="background: rgba(0, 0, 0, 0.48); padding: 3px 10px; border-radius: 20px; font-size: 12px; border: 1px solid rgba(105, 105, 105, 0.48)">
                            В ожидании: <span id="app-pending-count"><?= count(array_filter($applications, function($a) { return $a->status == 0; })) ?></span>
                        </span>
                        <span class="badge" style="background: rgba(0, 0, 0, 0.48); padding: 3px 10px; border-radius: 20px; font-size: 12px; border: 1px solid rgba(105, 105, 105, 0.48)">
                            Рассмотрено: <span id="app-closed-count"><?= count(array_filter($applications, function($a) { return $a->status == 1; })) ?></span>
                        </span>
                    </div>
                <?php endif; ?>
            </div>
            
            <?php if ($isAdmin): ?>
            <div class="filter-buttons" style="margin-bottom: 20px; display: flex; gap: 10px; flex-wrap: wrap;">
                <button class="app-filter-btn filter-btn back-btn <?= $appFilterStatus == 'all' ? 'active' : '' ?>" data-filter="all" data-type="application">
                    Все заявки
                </button>
                <button class="app-filter-btn filter-btn back-btn <?= $appFilterStatus == 'pending' ? 'active' : '' ?>" data-filter="pending" data-type="application">
                    В ожидании
                </button>
                <button class="app-filter-btn filter-btn back-btn <?= $appFilterStatus == 'closed' ? 'active' : '' ?>" data-filter="closed" data-type="application">
                    Рассмотрено
                </button>
            </div>
            <?php endif; ?>
            
            <div id="applications-container">
                <?= $this->render('_applications_table', [
                    'applications' => $applications,
                    'isAdmin' => $isAdmin,
                    'isBanned' => $isBanned,
                    'filterStatus' => $appFilterStatus
                ]) ?>
            </div>
            
            <?php if (!$isAdmin && !$isBanned): ?>
                <div class="text-center">
                    <a href="<?= Url::to(['application/help']) ?>" class="back-btn" style="margin-top:20px;">
                        <i class="fas fa-plus"></i> Новая заявка
                    </a>
                </div>
            <?php endif; ?>
        </div>
    
        <div class="applications-card" style="margin-top: 20px">
            <div class="section-title">
                <i class="fas fa-newspaper"></i> Статьи
                <?php if ($isAdmin): ?>
                    <div style="float: right; display: flex; gap: 10px;">
                        <span class="badge" style="background: rgba(0, 0, 0, 0.48); padding: 3px 10px; border-radius: 20px; font-size: 12px; border: 1px solid rgba(105, 105, 105, 0.48)">
                            Всего: <span id="total-count"><?= count($articles) ?></span>
                        </span>
                        <span class="badge" style="background: rgba(0, 0, 0, 0.48); padding: 3px 10px; border-radius: 20px; font-size: 12px; border: 1px solid rgba(105, 105, 105, 0.48)">
                            На модерации: <span id="pending-count"><?= count(array_filter($articles, function($a) { return $a->moderation_status == 0; })) ?></span>
                        </span>
                        <span class="badge" style="background: rgba(0, 0, 0, 0.48); padding: 3px 10px; border-radius: 20px; font-size: 12px; border: 1px solid rgba(105, 105, 105, 0.48)">
                            Одобрено: <span id="approved-count"><?= count(array_filter($articles, function($a) { return $a->moderation_status == 1; })) ?></span>
                        </span>
                    </div>
                <?php endif; ?>
            </div>
            
            <?php if ($isAdmin): ?>
            <div class="filter-buttons" style="margin-bottom: 20px; display: flex; gap: 10px; flex-wrap: wrap;">
                <button class="article-filter-btn filter-btn back-btn <?= $filterStatus == 'all' ? 'active' : '' ?>" data-filter="all" data-type="article">
                    Все статьи
                </button>
                <button class="article-filter-btn filter-btn back-btn <?= $filterStatus == 'pending' ? 'active' : '' ?>" data-filter="pending" data-type="article">
                    На модерации
                </button>
                <button class="article-filter-btn filter-btn back-btn <?= $filterStatus == 'approved' ? 'active' : '' ?>" data-filter="approved" data-type="article">
                    Одобренные
                </button>
            </div>
            <?php endif; ?>
            
            <div id="articles-container">
                <?= $this->render('_articles_table', [
                    'articles' => $articles,
                    'isAdmin' => $isAdmin,
                    'isBanned' => $isBanned,
                    'filterStatus' => $filterStatus
                ]) ?>
            </div>
            
            <?php if (!$isAdmin && !$isBanned): ?>
                <div class="text-center">
                    <a href="<?= Url::to(['application/create-article']) ?>" class="back-btn" style="margin-top:20px;">
                        <i class="fas fa-plus"></i> Добавить статью
                    </a>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="text-center">
            <?php if ($isAdmin): ?>
                <a href="<?= Url::to(['auth/profile']) ?>" class="back-btn" style="margin-top:10px">
                    <i class="fas fa-users"></i> Панель администратора
                </a>
            <?php endif; ?>
            <a href="<?= Url::to(['site/index']) ?>" class="back-btn" style="margin-top:10px;">
                <i class="fas fa-home"></i> На главную
            </a>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('.app-filter-btn').on('click', function() {
        var filter = $(this).data('filter');
        
        $('.app-filter-btn').removeClass('active');
        $(this).addClass('active');
        $('.app-filter-btn').css('background', '');
        $(this).css('background', 'rgba(255, 0, 0, 0.7)');
        
        $.ajax({
            url: '<?= Url::to(['application/filter-applications']) ?>',
            type: 'GET',
            data: { filter: filter },
            dataType: 'json',
            beforeSend: function() {
                $('#applications-container').html('<div class="empty-state"><i class="fas fa-spinner fa-spin"></i><p>Загрузка...</p></div>');
            },
            success: function(response) {
                if (response.success) {
                    $('#applications-container').html(response.html);
                    $('#app-total-count').text(response.total);
                    $('#app-pending-count').text(response.pending);
                    $('#app-closed-count').text(response.closed);
                } else {
                    $('#applications-container').html('<div class="empty-state"><i class="fas fa-exclamation-triangle"></i><p>Ошибка загрузки</p></div>');
                }
            },
            error: function() {
                $('#applications-container').html('<div class="empty-state"><i class="fas fa-exclamation-triangle"></i><p>Ошибка загрузки</p></div>');
            }
        });
    });
    
    $('.article-filter-btn').on('click', function() {
        var filter = $(this).data('filter');
        
        $('.article-filter-btn').removeClass('active');
        $(this).addClass('active');
        $('.article-filter-btn').css('background', '');
        $(this).css('background', 'rgba(255, 0, 0, 0.7)');
        
        $.ajax({
            url: '<?= Url::to(['application/filter-articles']) ?>',
            type: 'GET',
            data: { filter: filter },
            dataType: 'json',
            beforeSend: function() {
                $('#articles-container').html('<div class="empty-state"><i class="fas fa-spinner fa-spin"></i><p>Загрузка...</p></div>');
            },
            success: function(response) {
                if (response.success) {
                    $('#articles-container').html(response.html);
                    $('#total-count').text(response.total);
                    $('#pending-count').text(response.pending);
                    $('#approved-count').text(response.approved);
                } else {
                    $('#articles-container').html('<div class="empty-state"><i class="fas fa-exclamation-triangle"></i><p>Ошибка загрузки</p></div>');
                }
            },
            error: function() {
                $('#articles-container').html('<div class="empty-state"><i class="fas fa-exclamation-triangle"></i><p>Ошибка загрузки</p></div>');
            }
        });
    });
});
</script>