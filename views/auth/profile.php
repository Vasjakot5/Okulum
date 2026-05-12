<?php
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Личный кабинет';
?>

<div class="profile-page">
    <div class="profile-container">
        <div class="profile-header">
            <h1>
                <i class="fas fa-user-circle"></i>
                <?= Html::encode($this->title) ?>
            </h1>
            <p>Управление вашим аккаунтом</p>
        </div>
        
        <div class="profile-card">
            <div class="profile-avatar-section">
                <div class="profile-avatar">
                    <?php if ($user->photo && file_exists(Yii::getAlias('@webroot/avatars/' . $user->photo))): ?>
                        <img src="<?= Yii::getAlias('@web/avatars/' . $user->photo) ?>" alt="Avatar">
                    <?php else: ?>
                        <div class="profile-default-avatar">
                            <i class="fas fa-user"></i>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="profile-name"><?= Html::encode($user->getFullName()) ?></div>
                <div class="profile-role"><?= $user->role == 0 ? 'Пользователь' : 'Администратор' ?></div>
            </div>
            
            <div class="profile-info-section">
                <table class="profile-table">
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
                        <th>Дата регистрации:</th>
                        <td><?= Yii::$app->formatter->asDatetime($user->created_at) ?></td>
                    </tr>
                    
                    <?php if ($user->isBanned()): ?>
                    <tr style="background: rgba(255,0,0,0.1);">
                        <th style="color: #ff6b6b;">Статус блокировки:</th>
                        <td style="color: #ff6b6b;">
                            <strong><?= $user->ban_reason ?></strong>
                            <?php if ($user->ban_status == 1 && $user->ban_until): ?>
                                <br><small>Блокировка до: <?= Yii::$app->formatter->asDatetime($user->ban_until) ?></small>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endif; ?>
                </table>
                
                <div class="profile-buttons">
                    <?php if (!$user->isBanned()): ?>
                        <?= Html::a('<i class="fas fa-edit"></i> Редактировать профиль', ['auth/update-profile'], ['class' => 'profile-btn']) ?>
                        <?= Html::a('<i class="fas fa-key"></i> Сменить пароль', ['auth/change-password'], ['class' => 'profile-btn']) ?>
                        <?= Html::a('<i class="fas fa-pen-alt"></i> Добавить статью', ['application/create-article'], ['class' => 'profile-btn']) ?>
                        <?= Html::a('<i class="fas fa-comments"></i> Мои обсуждения', ['discussion/my'], ['class' => 'profile-btn']) ?>
                        <?= Html::a('<i class="fas fa-plus-circle"></i> Создать обсуждение', ['discussion/create'], ['class' => 'profile-btn']) ?>
                        <?php if ($user->role == 1): ?>
                                <?= Html::a('<i class="fas fa-users"></i> Управление пользователями', ['admin/users'], ['class' => 'profile-btn']) ?>
                                <?= Html::a('<i class="fas fa-city"></i> Управление городами', ['admin/cities'], ['class' => 'profile-btn']) ?>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="ban-warning">
                            <i class="fas fa-ban"></i> 
                            Доступ к редактированию профиля, созданию обсуждений и добавлению статей заблокирован до окончания срока блокировки.
                        </div>
                    <?php endif; ?>
                                            <?= Html::a('<i class="fas fa-list"></i> Заявки и статьи', ['application/my-applications'], ['class' => 'profile-btn']) ?>
                    <?= Html::a('<i class="fas fa-sign-out-alt"></i> Выйти', ['auth/logout'], ['class' => 'profile-btn profile-btn-danger', 'data-method' => 'post']) ?>
                </div>
            </div>
        </div>
    </div>
</div>