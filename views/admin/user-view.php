<?php
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Пользователь: ' . $user->getFullName();

$isAdmin = (Yii::$app->user->identity->role == 1);
?>

<div class="applications-page">
    <div class="applications-container">
        <div class="applications-header" style="text-align: center; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 20px; margin-bottom: 30px;">
            <h1 style="margin: 0 0 5px 0;">
                <i class="fas fa-user-circle"></i>
                <?= Html::encode($this->title) ?>
            </h1>
            <p style="margin: 0; opacity: 0.7;">Просмотр профиля пользователя</p>
        </div>
        
        <div class="applications-card">
            <div class="section-title" style="text-align: left;">
                <i class="fas fa-info-circle"></i> Информация о пользователе
            </div>
            
            <table class="applications-table">
                <tbody>
                    <tr>
                        <th style="width: 180px;">ID:</th>
                        <td><?= $user->id ?></td>
                    </tr>
                    <tr>
                        <th>Имя:</th>
                        <td><?= Html::encode($user->name) ?></td>
                    </tr>
                    <tr>
                        <th>Фамилия:</th>
                        <td><?= Html::encode($user->last_name) ?></td>
                    </tr>
                    <tr>
                        <th>Email:</th>
                        <td><?= Html::encode($user->email) ?></td>
                    </tr>
                    <tr>
                        <th>Телефон:</th>
                        <td><?= Html::encode($user->phone) ?></td>
                    </tr>
                    <tr>
                        <th>Роль:</th>
                        <td>
                            <?php if ($user->role == 1): ?>
                                <span class="status-badge status-new">Администратор</span>
                            <?php else: ?>
                                <span class="status-badge status-closed">Пользователь</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th>Статус:</th>
                        <td>
                            <?php if ($user->isBanned()): ?>
                                <span class="status-badge status-new" style="margin-top: 40px">Заблокирован</span>
                                <?php if ($user->ban_until): ?>
                                    <div style="margin-top: 5px; font-size: 12px; color: rgba(255,255,255,0.5);">
                                        до: <?= Yii::$app->formatter->asDatetime($user->ban_until) ?><br>
                                        Причина: <?= Html::encode($user->ban_reason) ?>
                                    </div>
                                <?php endif; ?>
                            <?php else: ?>
                                <span class="status-badge status-closed">Активен</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th>Нарушений:</th>
                        <td style="<?= $user->violations_count > 0 ? 'color: #ff6b6b; font-weight: bold;' : '' ?>">
                            <?= $user->violations_count ?>
                        </td>
                    </tr>
                    <tr>
                        <th>Дата регистрации:</th>
                        <td><?= Yii::$app->formatter->asDatetime($user->created_at) ?></td>
                    </tr>
                </tbody>
            </table>
            
            <div class="text-center" style="margin-top: 20px;">
                <?php if (!$user->isBanned() && $user->id != Yii::$app->user->id): ?>
                    <button class="back-btn ban-user-btn" data-id="<?= $user->id ?>" data-name="<?= $user->getFullName() ?>">
                        <i class="fas fa-ban"></i> Заблокировать
                    </button>
                <?php endif; ?>
                <?php if ($user->isBanned() && $user->id != Yii::$app->user->id): ?>
                    <button class="back-btn unban-user-btn" data-id="<?= $user->id ?>" data-name="<?= $user->getFullName() ?>">
                        <i class="fas fa-check-circle"></i> Разблокировать
                    </button>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="applications-card" style="margin-top: 20px">
            <div class="section-title" style="text-align: left;">
                <i class="fas fa-comment-dots"></i> Комментарии к статьям
                <span class="badge" style="background: rgba(0, 0, 0, 0.48); padding: 3px 10px; border-radius: 20px; font-size: 12px; border: 1px solid rgba(105, 105, 105, 0.48); float: right;"><?= count($articleComments) ?> комментариев</span>
            </div>
            
            <?php if (empty($articleComments)): ?>
                <div class="empty-state">
                    <i class="fas fa-comments"></i>
                    <p>У пользователя нет комментариев к статьям</p>
                </div>
            <?php else: ?>
                <?php foreach ($articleComments as $comment): ?>
                    <div class="comment-thread" style="margin-bottom: 15px;">
                        <div class="comment-main">
                            <div class="comment-flex">
                                <div class="comment-left">
                                    <div class="comment-content">
                                        <div class="comment-header">
                                            <span class="comment-author">
                                                <?php
                                                $entityLink = '#';
                                                $entityName = 'Комментарий';
                                                
                                                if ($comment->cities_id) {
                                                    $city = \app\models\Cities::findOne($comment->cities_id);
                                                    if ($city) {
                                                        $entityLink = Url::to(['site/city', 'id' => $comment->cities_id]);
                                                        $entityName = 'Город: ' . Html::encode($city->name);
                                                    }
                                                } elseif ($comment->events_id) {
                                                    $event = \app\models\Events::findOne($comment->events_id);
                                                    if ($event) {
                                                        $entityLink = Url::to(['site/event', 'id' => $comment->events_id]);
                                                        $entityName = 'Событие: ' . Html::encode($event->name);
                                                    }
                                                } elseif ($comment->openings_id) {
                                                    $opening = \app\models\Openings::findOne($comment->openings_id);
                                                    if ($opening) {
                                                        $entityLink = Url::to(['site/opening', 'id' => $comment->openings_id]);
                                                        $entityName = 'Открытие: ' . Html::encode($opening->name);
                                                    }
                                                } elseif ($comment->popular_humans_id) {
                                                    $human = \app\models\PopularHumans::findOne($comment->popular_humans_id);
                                                    if ($human) {
                                                        $entityLink = Url::to(['site/person', 'id' => $comment->popular_humans_id]);
                                                        $entityName = 'Знаменитость: ' . Html::encode($human->name . ' ' . $human->last_name);
                                                    }
                                                } elseif ($comment->vehicles_id) {
                                                    $vehicle = \app\models\Vehicles::findOne($comment->vehicles_id);
                                                    if ($vehicle) {
                                                        $entityLink = Url::to(['site/vehicle', 'id' => $comment->vehicles_id]);
                                                        $entityName = 'Техника: ' . Html::encode($vehicle->name);
                                                    }
                                                } elseif ($comment->monuments_id) {
                                                    $monument = \app\models\Monuments::findOne($comment->monuments_id);
                                                    if ($monument) {
                                                        $entityLink = Url::to(['site/monument', 'id' => $comment->monuments_id]);
                                                        $entityName = 'Памятник: ' . Html::encode($monument->name);
                                                    }
                                                } elseif ($comment->weapons_id) {
                                                    $weapon = \app\models\Weapons::findOne($comment->weapons_id);
                                                    if ($weapon) {
                                                        $entityLink = Url::to(['site/weapon', 'id' => $comment->weapons_id]);
                                                        $entityName = 'Оружие: ' . Html::encode($weapon->name);
                                                    }
                                                } elseif ($comment->clothes_id) {
                                                    $cloth = \app\models\Clothes::findOne($comment->clothes_id);
                                                    if ($cloth) {
                                                        $entityLink = Url::to(['site/clothe', 'id' => $comment->clothes_id]);
                                                        $entityName = 'Одежда: ' . Html::encode($cloth->name);
                                                    }
                                                }
                                                ?>
                                                <?= $entityName ?>
                                            </span>
                                            <span class="comment-time">
                                                <?= Yii::$app->formatter->asRelativeTime($comment->created_at) ?>
                                            </span>
                                        </div>
                                        <div class="comment-text">
                                            <?= nl2br(Html::encode($comment->content)) ?>
                                        </div>
                                        <div class="comment-actions" style="float:right; margin-top: 10px; text-align: right;">
                                            <?= Html::a('<i class="fas fa-eye"></i> Просмотр', $entityLink, ['class' => 'btn-action view-comment-btn', 'target' => '_blank']) ?>
                                            <?php if ($isAdmin): ?>
                                                <button class="btn-action delete-comment-btn" data-id="<?= $comment->id ?>">
                                                    <i class="fas fa-trash-alt"></i> Удалить
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        
        <div class="applications-card" style="margin-top: 20px">
            <div class="section-title" style="text-align: left;">
                <i class="fas fa-comments"></i> Сообщения в обсуждениях
                <span class="count-badge" style="float: right; background: rgba(0, 0, 0, 0.48); padding: 3px 10px; border-radius: 20px; font-size: 12px; border: 1px solid rgba(105, 105, 105, 0.48)"><?= count($discussionMessages) ?> сообщений</span>
            </div>
            
            <?php if (empty($discussionMessages)): ?>
                <div class="empty-state">
                    <i class="fas fa-comments"></i>
                    <p>У пользователя нет сообщений в обсуждениях</p>
                </div>
            <?php else: ?>
                <?php foreach ($discussionMessages as $msg): ?>
                    <div class="comment-thread" style="margin-bottom: 15px;">
                        <div class="comment-main">
                            <div class="comment-flex">
                                <div class="comment-left">
                                    <div class="comment-content">
                                        <div class="comment-header">
                                            <span class="comment-author">
                                                <?= Html::encode($msg->discussions->title ?? 'Обсуждение') ?>
                                            </span>
                                            <span class="comment-time">
                                                <?= Yii::$app->formatter->asRelativeTime($msg->created_at) ?>
                                            </span>
                                        </div>
                                        <div class="comment-text">
                                            <?= nl2br(Html::encode($msg->content)) ?>
                                        </div>
                                        <div class="comment-actions" style="float:right; margin-top: 10px; text-align: right;">
                                            <?= Html::a('<i class="fas fa-eye"></i> Просмотр', ['discussion/view', 'id' => $msg->discussions_id], ['class' => 'btn-action view-discussion-btn', 'target' => '_blank']) ?>
                                            <?php if ($isAdmin): ?>
                                                <button class="btn-action delete-msg-btn" data-id="<?= $msg->id ?>">
                                                    <i class="fas fa-trash-alt"></i> Удалить
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        
        <div class="applications-card" style="margin-top: 20px">
            <div class="section-title" style="text-align: left;">
                <i class="fas fa-newspaper"></i> Статьи пользователя
                <span class="count-badge" style="float: right; background: rgba(0, 0, 0, 0.48); padding: 3px 10px; border-radius: 20px; font-size: 12px; border: 1px solid rgba(105, 105, 105, 0.48)"><?= count($articles) ?> статей</span>
            </div>
            
            <?php if (empty($articles)): ?>
                <div class="empty-state">
                    <i class="fas fa-file-alt"></i>
                    <p>У пользователя нет статей</p>
                </div>
            <?php else: ?>
                <table class="applications-table">
                    <thead>
                        <tr>
                            <th>Название</th>
                            <th>Тип</th>
                            <th>Статус</th>
                            <th>Дата</th>
                            <th>Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($articles as $article): ?>
                            <?php
                            $articleUrl = '';
                            $typeParam = '';
                            $typeName = '';
                            
                            if ($article instanceof \app\models\Events) {
                                $articleUrl = Url::to(['site/event', 'id' => $article->id]);
                                $typeParam = 'event';
                                $typeName = 'Событие';
                            } elseif ($article instanceof \app\models\Openings) {
                                $articleUrl = Url::to(['site/opening', 'id' => $article->id]);
                                $typeParam = 'opening';
                                $typeName = 'Открытие';
                            } elseif ($article instanceof \app\models\PopularHumans) {
                                $articleUrl = Url::to(['site/person', 'id' => $article->id]);
                                $typeParam = 'human';
                                $typeName = 'Знаменитость';
                            } elseif ($article instanceof \app\models\Vehicles) {
                                $articleUrl = Url::to(['site/vehicle', 'id' => $article->id]);
                                $typeParam = 'vehicle';
                                $typeName = 'Техника';
                            } elseif ($article instanceof \app\models\Monuments) {
                                $articleUrl = Url::to(['site/monument', 'id' => $article->id]);
                                $typeParam = 'monument';
                                $typeName = 'Памятник';
                            } elseif ($article instanceof \app\models\Weapons) {
                                $articleUrl = Url::to(['site/weapon', 'id' => $article->id]);
                                $typeParam = 'weapon';
                                $typeName = 'Оружие';
                            } elseif ($article instanceof \app\models\Clothes) {
                                $articleUrl = Url::to(['site/clothe', 'id' => $article->id]);
                                $typeParam = 'clothe';
                                $typeName = 'Одежда';
                            }
                            ?>
                            <tr>
                                <td><?= Html::encode($article->name) ?></td>
                                <td><?= $typeName ?></td>
                                <td>
                                    <?php if ($article->moderation_status == 0): ?>
                                        <span class="status-badge status-new">На модерации</span>
                                    <?php else: ?>
                                        <span class="status-badge status-closed">Одобрено</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= Yii::$app->formatter->asDate($article->created_at, 'dd.MM.yyyy') ?></td>
                                <td>
                                    <?= Html::a('<i class="fas fa-eye"></i> Просмотр', $articleUrl, ['class' => 'btn-action view-article-btn', 'target' => '_blank']) ?>
                                    <?php if ($isAdmin): ?>
                                        <button class="btn-action delete-article-btn" data-type="<?= $typeParam ?>" data-id="<?= $article->id ?>" data-name="<?= Html::encode($article->name) ?>">
                                            <i class="fas fa-trash-alt"></i> Удалить
                                        </button>
                                    <?php endif; ?>
                                
                            </td>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
        
        <div class="applications-card" style="margin-top: 20px">
            <div class="section-title" style="text-align: left;">
                <i class="fas fa-comments"></i> Обсуждения пользователя
                <span class="count-badge" style="float: right; background: rgba(0, 0, 0, 0.48); padding: 3px 10px; border-radius: 20px; font-size: 12px; border: 1px solid rgba(105, 105, 105, 0.48)"><?= count($discussions) ?> обсуждений</span>
            </div>
            
            <?php if (empty($discussions)): ?>
                <div class="empty-state">
                    <i class="fas fa-comments"></i>
                    <p>У пользователя нет созданных обсуждений</p>
                </div>
            <?php else: ?>
                <?php foreach ($discussions as $disc): ?>
                    <div class="comment-thread" style="margin-bottom: 15px;">
                        <div class="comment-main">
                            <div class="comment-flex">
                                <div class="comment-left">
                                    <div class="comment-content">
                                        <div class="comment-header">
                                            <span class="comment-author">
                                                <?= Html::encode($disc->title) ?>
                                            </span>
                                            <span class="comment-time">
                                                <?= Yii::$app->formatter->asRelativeTime($disc->created_at) ?>
                                            </span>
                                        </div>
                                        <div class="comment-text">
                                            <?= Html::encode(mb_substr(strip_tags($disc->content), 0, 200)) ?>
                                            <?php if (mb_strlen(strip_tags($disc->content)) > 200): ?>...<?php endif; ?>
                                        </div>
                                        <div class="comment-actions" style="margin-top: 10px; float: right;">
                                            <?= Html::a('<i class="fas fa-eye"></i> Просмотр', ['discussion/view', 'id' => $disc->id], ['class' => 'btn-action view-discussion-btn', 'target' => '_blank']) ?>
                                            <?php if ($isAdmin): ?>
                                                <button class="btn-action delete-discussion-btn" data-id="<?= $disc->id ?>" data-title="<?= Html::encode($disc->title) ?>">
                                                    <i class="fas fa-trash-alt"></i> Удалить
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        
        <div class="applications-card" style="margin-top: 20px">
            <div class="section-title" style="text-align: left;">
                <i class="fas fa-ticket-alt"></i> Заявки пользователя
                <span class="count-badge" style="float: right; background: rgba(0, 0, 0, 0.48); padding: 3px 10px; border-radius: 20px; font-size: 12px; border: 1px solid rgba(105, 105, 105, 0.48)"><?= count($applications) ?> заявок</span>
            </div>
            
            <?php if (empty($applications)): ?>
                <div class="empty-state">
                    <i class="fas fa-inbox"></i>
                    <p>У пользователя нет заявок</p>
                </div>
            <?php else: ?>
                <table class="applications-table">
                    <thead>
                        <tr>
                            <th>Тема</th>
                            <th>Тип</th>
                            <th>Статус</th>
                            <th>Дата</th>
                            <th>Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($applications as $app): ?>
                            <tr>
                                <td><?= Html::encode($app->name) ?></td>
                                <td>
                                    <?php
                                    $types = [
                                        'bug' => 'Ошибка',
                                        'question' => 'Вопрос',
                                        'suggestion' => 'Предложение',
                                        'violation' => 'Нарушение правила',
                                    ];
                                    echo $types[$app->type] ?? $app->type;
                                    ?>
                                </td>
                                <td>
                                    <?php if ($app->status == 0): ?>
                                        <span class="status-badge status-new">Ожидает ответа</span>
                                    <?php else: ?>
                                        <span class="status-badge status-closed">Рассмотрено</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= Yii::$app->formatter->asDate($app->created_at, 'dd.MM.yyyy') ?></td>
                                <td>
                                    <?= Html::a('<i class="fas fa-eye"></i> Просмотр', ['application/view', 'id' => $app->id], ['class' => 'btn-action view-application-btn', 'target' => '_blank']) ?>
                                    <?php if ($isAdmin): ?>
                                        <button class="btn-action delete-application-btn" data-id="<?= $app->id ?>" data-name="<?= Html::encode($app->name) ?>">
                                            <i class="fas fa-trash-alt"></i> Удалить
                                        </button>
                                    <?php endif; ?>
                                
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
        
        <div class="text-center" style="margin-top: 20px;">
            <a href="<?= Url::to(['admin/users']) ?>" class="back-btn">
                <i class="fas fa-arrow-left"></i> Назад к списку пользователей
            </a>
        </div>
        
    </div>
</div>

<script>
$(document).ready(function() {
    var csrfToken = $('meta[name="csrf-token"]').attr('content');
    
    $(document).on('click', '.view-comment-btn', function() {
        var url = $(this).data('url');
        if (url && url !== '#') {
            window.open(url, '_blank');
        } else {
            window.open($(this).attr('href'), '_blank');
        }
    });
    
    $(document).on('click', '.view-discussion-btn', function() {
        var url = $(this).data('url');
        if (url && url !== '#') {
            window.open(url, '_blank');
        } else {
            window.open($(this).attr('href'), '_blank');
        }
    });
    
    $(document).on('click', '.view-article-btn', function() {
        var url = $(this).data('url');
        if (url && url !== '#') {
            window.open(url, '_blank');
        } else {
            window.open($(this).attr('href'), '_blank');
        }
    });
    
    $(document).on('click', '.view-application-btn', function() {
        var url = $(this).data('url');
        if (url && url !== '#') {
            window.open(url, '_blank');
        } else {
            window.open($(this).attr('href'), '_blank');
        }
    });
    
    $('.ban-user-btn').on('click', function() {
        var id = $(this).data('id');
        var name = $(this).data('name');
        var days = prompt('На сколько дней заблокировать пользователя "' + name + '"?', '1');
        
        if (days && days > 0) {
            var reason = prompt('Причина блокировки:', 'Нарушение правил сайта');
            if (reason !== null) {
                $.ajax({
                    url: '<?= Url::to(['admin/ban-user']) ?>',
                    type: 'POST',
                    data: { id: id, days: days, reason: reason },
                    headers: { 'X-CSRF-Token': csrfToken },
                    success: function(response) {
                        if (response.success) {
                            alert(response.message);
                            location.reload();
                        } else {
                            alert(response.message);
                        }
                    },
                    error: function() {
                        alert('Ошибка при блокировке');
                    }
                });
            }
        }
    });
    
    $('.unban-user-btn').on('click', function() {
        var id = $(this).data('id');
        var name = $(this).data('name');
        
        if (confirm('Разблокировать пользователя "' + name + '"?')) {
            $.ajax({
                url: '<?= Url::to(['admin/unban-user']) ?>',
                type: 'POST',
                data: { id: id },
                headers: { 'X-CSRF-Token': csrfToken },
                success: function(response) {
                    if (response.success) {
                        alert(response.message);
                        location.reload();
                    } else {
                        alert(response.message);
                    }
                },
                error: function() {
                    alert('Ошибка при разблокировке');
                }
            });
        }
    });
    
    $(document).on('click', '.delete-comment-btn', function() {
        var id = $(this).data('id');
        if (confirm('Удалить этот комментарий?')) {
            $.ajax({
                url: '<?= Url::to(['admin/delete-comment']) ?>',
                type: 'POST',
                data: { id: id },
                headers: { 'X-CSRF-Token': csrfToken },
                success: function(response) {
                    if (response.success) {
                        alert(response.message);
                        location.reload();
                    } else {
                        alert(response.message);
                    }
                },
                error: function() {
                    alert('Ошибка при удалении комментария');
                }
            });
        }
    });
    
    $(document).on('click', '.delete-msg-btn', function() {
        var id = $(this).data('id');
        if (confirm('Удалить это сообщение?')) {
            $.ajax({
                url: '<?= Url::to(['admin/delete-message']) ?>',
                type: 'POST',
                data: { id: id },
                headers: { 'X-CSRF-Token': csrfToken },
                success: function(response) {
                    if (response.success) {
                        alert(response.message);
                        location.reload();
                    } else {
                        alert(response.message);
                    }
                },
                error: function() {
                    alert('Ошибка при удалении сообщения');
                }
            });
        }
    });
    
    $(document).on('click', '.delete-article-btn', function() {
        var type = $(this).data('type');
        var id = $(this).data('id');
        var name = $(this).data('name');
        
        if (confirm('Удалить статью "' + name + '"?')) {
            $.ajax({
                url: '<?= Url::to(['admin/delete-article']) ?>',
                type: 'POST',
                data: { type: type, id: id },
                headers: { 'X-CSRF-Token': csrfToken },
                success: function(response) {
                    if (response.success) {
                        alert(response.message);
                        location.reload();
                    } else {
                        alert(response.message);
                    }
                },
                error: function() {
                    alert('Ошибка при удалении статьи');
                }
            });
        }
    });
    
    $(document).on('click', '.delete-discussion-btn', function() {
        var id = $(this).data('id');
        var title = $(this).data('title');
        
        if (confirm('Удалить обсуждение "' + title + '" вместе со всеми сообщениями?')) {
            $.ajax({
                url: '<?= Url::to(['admin/delete-discussion']) ?>',
                type: 'POST',
                data: { id: id },
                headers: { 'X-CSRF-Token': csrfToken },
                success: function(response) {
                    if (response.success) {
                        alert(response.message);
                        location.reload();
                    } else {
                        alert(response.message);
                    }
                },
                error: function() {
                    alert('Ошибка при удалении обсуждения');
                }
            });
        }
    });
    
    $(document).on('click', '.delete-application-btn', function() {
        var id = $(this).data('id');
        var name = $(this).data('name');
        
        if (confirm('Удалить заявку "' + name + '"?')) {
            $.ajax({
                url: '<?= Url::to(['admin/delete-application', 'id' => '']) ?>' + id,
                type: 'POST',
                data: {},
                headers: { 'X-CSRF-Token': csrfToken },
                success: function(response) {
                    if (response.success) {
                        alert(response.message);
                        location.reload();
                    } else {
                        alert(response.message);
                    }
                },
                error: function() {
                    alert('Ошибка при удалении заявки');
                }
            });
        }
    });
});
</script>