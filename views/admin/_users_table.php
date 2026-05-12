<?php
use yii\helpers\Html;
use yii\helpers\Url;

$filteredUsers = $users;

if ($filterStatus != 'all') {
    $filteredUsers = array_filter($filteredUsers, function($u) use ($filterStatus) {
        if ($filterStatus == 'active') {
            return !$u->isBanned();
        } elseif ($filterStatus == 'banned') {
            return $u->isBanned();
        } elseif ($filterStatus == 'admin') {
            return $u->role == 1;
        }
        return true;
    });
}

if (!empty($searchQuery)) {
    $filteredUsers = array_filter($filteredUsers, function($u) use ($searchQuery) {
        return stripos($u->name, $searchQuery) !== false || 
               stripos($u->last_name, $searchQuery) !== false || 
               stripos($u->email, $searchQuery) !== false;
    });
}
?>

<?php if (empty($filteredUsers)): ?>
    <div class="empty-state">
        <i class="fas fa-users"></i>
        <p>Пользователи не найдены</p>
    </div>
<?php else: ?>
    <table class="users-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>ФИО</th>
                <th>Email</th>
                <th>Телефон</th>
                <th>Роль</th>
                <th>Статус</th>
                <th>Нарушений</th>
                <th>Действия</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($filteredUsers as $u): ?>
                <tr>
                    <td><?= $u->id ?></td>
                    <td>
                        <?= Html::a($u->getFullName(), ['admin/user-view', 'id' => $u->id], ['style' => 'color: #ffd700; text-decoration: none;']) ?>
                    </td>
                    <td><?= Html::encode($u->email) ?></td>
                    <td><?= Html::encode($u->phone) ?></td>
                    <td>
                        <?php if ($u->role == 1): ?>
                            <span class="status-new status-badge">Администратор</span>
                        <?php else: ?>
                            <span class="status-closed status-badge">Пользователь</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($u->isBanned()): ?>
                            <span class="status-new status-badge" style="margin-top: 25px;">Заблокирован</span>
                            <?php if ($u->ban_until): ?>
                                <br><small>до <?= Yii::$app->formatter->asDate($u->ban_until, 'dd.MM.yyyy') ?></small>
                            <?php endif; ?>
                        <?php else: ?>
                            <span class="status-closed status-badge">Активен</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <span class="violations-count <?= $u->violations_count > 0 ? 'has-violations' : '' ?>">
                            <?= $u->violations_count ?>
                        </span>
                    </td>
                    <td>
                        <div class="action-buttons" style="display: flex; gap: 5px; flex-wrap: wrap;">
                            <?= Html::a('Просмотр', ['admin/user-view', 'id' => $u->id], ['class' => 'back-btn status-badge', 'title' => 'Просмотр']) ?>
                            
                            <?php if (!$u->isBanned() && $u->id != Yii::$app->user->id): ?>
                                <button class="ban-btn back-btn status-badge" data-id="<?= $u->id ?>" data-name="<?= $u->getFullName() ?>">
                                    Забанить
                                </button>
                            <?php endif; ?>
                            
                            <?php if ($u->isBanned() && $u->id != Yii::$app->user->id): ?>
                                <button class="unban-btn back-btn status-badge" data-id="<?= $u->id ?>" data-name="<?= $u->getFullName() ?>">
                                    Разблокировать
                                </button>
                            <?php endif; ?>
                            
                            <?php if ($u->role == 0 && $u->id != Yii::$app->user->id): ?>
                                <button class="make-admin-btn back-btn status-badge" data-id="<?= $u->id ?>" data-name="<?= $u->getFullName() ?>">
                                    Дать права
                                </button>
                            <?php endif; ?>
                            
                            <?php if ($u->role == 1 && $u->id != Yii::$app->user->id): ?>
                                <button class="remove-admin-btn back-btn status-badge" data-id="<?= $u->id ?>" data-name="<?= $u->getFullName() ?>">
                                    Снять права
                                </button>
                            <?php endif; ?>
                            
                            <?php if ($u->id != Yii::$app->user->id): ?>
                                <button class="reset-violations-btn back-btn status-badge" data-id="<?= $u->id ?>" data-name="<?= $u->getFullName() ?>">
                                    Снять наказания
                                </button>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
<script>
$(document).ready(function() {
    var csrfToken = $('meta[name="csrf-token"]').attr('content');
    
    $(document).on('click', '.ban-btn', function() {
        var id = $(this).data('id');
        var name = $(this).data('name');
        
        var days = prompt('На сколько дней заблокировать пользователя "' + name + '"? (введите число дней)', '1');
        
        if (days && days > 0) {
            var reason = prompt('Укажите причину блокировки пользователя "' + name + '":', 'Нарушение правил сайта');
            if (reason !== null) {
                $.ajax({
                    url: '<?= Url::to(['admin/ban-user']) ?>',
                    type: 'POST',
                    data: { id: id, days: days, reason: reason },
                    dataType: 'json',
                    headers: {
                        'X-CSRF-Token': csrfToken
                    },
                    success: function(response) {
                        if (response.success) {
                            alert(response.message);
                            location.reload();
                        } else {
                            alert(response.message);
                        }
                    },
                    error: function(xhr) {
                        console.log(xhr.responseText);
                        alert('Ошибка при блокировке: ' + xhr.status);
                    }
                });
            }
        }
    });
    
    $(document).on('click', '.unban-btn', function() {
        var id = $(this).data('id');
        var name = $(this).data('name');
        
        if (confirm('Разблокировать пользователя "' + name + '"?')) {
            $.ajax({
                url: '<?= Url::to(['admin/unban-user']) ?>',
                type: 'POST',
                data: { id: id },
                dataType: 'json',
                headers: {
                    'X-CSRF-Token': csrfToken
                },
                success: function(response) {
                    if (response.success) {
                        alert(response.message);
                        location.reload();
                    } else {
                        alert(response.message);
                    }
                },
                error: function(xhr) {
                    alert('Ошибка при разблокировке: ' + xhr.status);
                }
            });
        }
    });
    
    $(document).on('click', '.make-admin-btn', function() {
        var id = $(this).data('id');
        var name = $(this).data('name');
        
        if (confirm('Назначить пользователя "' + name + '" администратором?')) {
            $.ajax({
                url: '<?= Url::to(['admin/make-admin']) ?>',
                type: 'POST',
                data: { id: id },
                dataType: 'json',
                headers: {
                    'X-CSRF-Token': csrfToken
                },
                success: function(response) {
                    if (response.success) {
                        alert(response.message);
                        location.reload();
                    } else {
                        alert(response.message);
                    }
                },
                error: function(xhr) {
                    alert('Ошибка: ' + xhr.status);
                }
            });
        }
    });
    
    $(document).on('click', '.remove-admin-btn', function() {
        var id = $(this).data('id');
        var name = $(this).data('name');
        
        if (confirm('Снять права администратора с пользователя "' + name + '"?')) {
            $.ajax({
                url: '<?= Url::to(['admin/remove-admin']) ?>',
                type: 'POST',
                data: { id: id },
                dataType: 'json',
                headers: {
                    'X-CSRF-Token': csrfToken
                },
                success: function(response) {
                    if (response.success) {
                        alert(response.message);
                        location.reload();
                    } else {
                        alert(response.message);
                    }
                },
                error: function(xhr) {
                    alert('Ошибка: ' + xhr.status);
                }
            });
        }
    });
    
    $(document).on('click', '.reset-violations-btn', function() {
        var id = $(this).data('id');
        var name = $(this).data('name');
        
        if (confirm('Сбросить счетчик нарушений пользователя "' + name + '"?')) {
            $.ajax({
                url: '<?= Url::to(['admin/reset-violations']) ?>',
                type: 'POST',
                data: { id: id },
                dataType: 'json',
                headers: {
                    'X-CSRF-Token': csrfToken
                },
                success: function(response) {
                    if (response.success) {
                        alert(response.message);
                        location.reload();
                    } else {
                        alert(response.message);
                    }
                },
                error: function(xhr) {
                    alert('Ошибка: ' + xhr.status);
                }
            });
        }
    });
});
</script>