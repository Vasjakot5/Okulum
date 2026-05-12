<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

$this->registerJs("
    var addCommentUrl = '" . Url::to(['site/add-comment']) . "';
    var editCommentUrl = '" . Url::to(['site/edit-comment']) . "';
    var deleteCommentUrl = '" . Url::to(['site/delete-comment']) . "';
", \yii\web\View::POS_BEGIN);

$isBanned = false;
if (!Yii::$app->user->isGuest) {
    $currentUser = Yii::$app->user->identity;
    $isBanned = $currentUser->isBanned();
}
?>

<div class="comments-wrapper">
    <div class="comments-title">
        <h3>
            <i class="fas fa-comment-dots"></i>
            Обсуждение
        </h3>
        <span class="comments-count">
            <?= count($comments) ?> комментариев
        </span>
    </div>

    <?php if (!Yii::$app->user->isGuest): ?>
        
        <?php if ($isBanned): ?>
        <div class="add-comment-card" style="background: rgba(255,0,0,0.1); border-color: rgba(255,0,0,0.3);">
            <div class="empty-state" style="padding: 60px 20px;">
                <i class="fas fa-ban" style="color: #ff6b6b; font-size: 48px; margin-bottom: 15px;"></i>
                <p style="color: #ff6b6b; font-size: 16px; margin-bottom: 10px;">Вы не можете оставлять комментарии</p>
                <p style="font-size: 12px; color: rgba(255,255,255,0.6);">Ваш аккаунт заблокирован.</p>
            </div>
        </div>
        <?php else: ?>
        <div class="add-comment-card">
            <div class="card-header">
                <div class="header-user">
                    <?php 
                    $currentUser = Yii::$app->user->identity;
                    $avatarUrl = $currentUser->getAvatarUrl();
                    ?>
                    <?php if ($currentUser->photo && file_exists(Yii::getAlias('@webroot/avatars/' . $currentUser->photo))): ?>
                        <img src="<?= Yii::getAlias('@web/avatars/' . $currentUser->photo) ?>" 
                             class="user-avatar-img" 
                             style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;">
                    <?php else: ?>
                        <div class="user-avatar">
                            <i class="fas fa-user"></i>
                        </div>
                    <?php endif; ?>
                    <div>
                        <strong class="user-name"><?= Html::encode($currentUser->name . ' ' . $currentUser->last_name) ?></strong>
                        <div class="user-label">Оставьте свой комментарий</div>
                    </div>
                </div>
            </div>
            
            <div class="card-body">
                <?php $form = ActiveForm::begin([
                    'action' => Url::to(['site/add-comment']),
                    'options' => ['class' => 'ajax-comment-form'],
                    'id' => 'comment-form-' . $entityType . '-' . $entityId
                ]); ?>
                
                <?= Html::hiddenInput('entity_type', $entityType) ?>
                <?= Html::hiddenInput('entity_id', $entityId) ?>
                <?= Html::hiddenInput('parent_id', 0, ['id' => 'parent-id-' . $entityType . '-' . $entityId]) ?>
                
                <div class="form-group">
                    <?= Html::textarea('content', '', [
                        'class' => 'comment-input',
                        'rows' => 4,
                        'placeholder' => 'Что вы думаете об этом? Поделитесь своим мнением...',
                        'id' => 'comment-content-' . $entityType . '-' . $entityId
                    ]) ?>
                </div>
                
                <div style="display: flex; justify-content: flex-end;">
                    <?= Html::submitButton('<i class="fas fa-paper-plane" style="margin-right: 8px;"></i> Опубликовать', [
                        'class' => 'submit-comment-btn'
                    ]) ?>
                </div>
                
                <?php ActiveForm::end(); ?>
            </div>
        </div>
        <?php endif; ?>
        
    <?php else: ?>
    <div class="login-prompt">
        <i class="fas fa-lock"></i>
        <p>Присоединяйтесь к обсуждению!</p>
        <p class="hint">
            <?= Html::a('Войдите в аккаунт', ['/auth/login']) ?> 
            или <?= Html::a('зарегистрируйтесь', ['/auth/register']) ?>, чтобы оставить комментарий
        </p>
    </div>
    <?php endif; ?>

    <div class="comments-feed" id="comments-list-<?= $entityType . '-' . $entityId ?>">
        <?php if (empty($comments)): ?>
        <div class="empty-state">
            <i class="fas fa-comments"></i>
            <h4>Пока нет комментариев</h4>
            <p>Будьте первым, кто поделится своим мнением</p>
        </div>
        <?php else: ?>
            <?php foreach ($comments as $comment): ?>
            <div class="comment-thread" id="comment-<?= $comment->id ?>" data-comment-id="<?= $comment->id ?>" data-user-id="<?= $comment->user_id ?>">
                <div class="comment-main">
                    <div class="comment-flex">
                        <div class="comment-left">
                            <?php 
                            $commentUser = $comment->user;
                            $commentAvatarUrl = $commentUser->getAvatarUrl();
                            ?>
                            <?php if ($commentUser->photo && file_exists(Yii::getAlias('@webroot/avatars/' . $commentUser->photo))): ?>
                                <img src="<?= Yii::getAlias('@web/avatars/' . $commentUser->photo) ?>" 
                                     class="comment-avatar-img" 
                                     style="width: 48px; height: 48px; border-radius: 50%; object-fit: cover;">
                            <?php else: ?>
                                <div class="comment-avatar">
                                    <i class="fas fa-user-circle"></i>
                                </div>
                            <?php endif; ?>
                            
                            <div class="comment-content">
                                <div class="comment-header">
                                    <span class="comment-author">
                                        <?= Html::encode($commentUser->name . ' ' . $commentUser->last_name) ?>
                                    </span>
                                    <span class="comment-time">
                                        <i class="far fa-clock"></i> 
                                        <?= Yii::$app->formatter->asRelativeTime($comment->created_at) ?>
                                    </span>
                                    <?php if ($comment->updated_at && $comment->updated_at != $comment->created_at): ?>
                                    <span class="edited-badge">
                                        <i class="fas fa-pencil-alt"></i> изменено
                                    </span>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="comment-text" id="comment-text-<?= $comment->id ?>">
                                    <?= nl2br(Html::encode($comment->content)) ?>
                                </div>
                                
                                <div class="edit-form" id="edit-form-<?= $comment->id ?>">
                                    <textarea id="edit-content-<?= $comment->id ?>" class="edit-textarea" rows="3"><?= Html::encode($comment->content) ?></textarea>
                                    <div class="edit-actions">
                                        <button type="button" class="btn-action save-edit-btn" data-id="<?= $comment->id ?>">Сохранить</button>
                                        <button type="button" class="btn-action cancel-edit-btn" data-id="<?= $comment->id ?>">Отмена</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="comment-actions">
                            <button type="button" class="btn-action reply-trigger" data-id="<?= $comment->id ?>">
                                <i class="fas fa-reply"></i> Ответить
                            </button>
                            
                            <?php if ($comment->user_id == Yii::$app->user->id || (!Yii::$app->user->isGuest && Yii::$app->user->identity->role == 1)): ?>
                            <button type="button" class="btn-action edit-comment-trigger" data-id="<?= $comment->id ?>">
                                <i class="fas fa-edit"></i> Редактировать
                            </button>
                            <button type="button" class="btn-action delete-comment-trigger" data-id="<?= $comment->id ?>">
                                <i class="fas fa-trash-alt"></i> Удалить
                            </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <?php if ($comment->comments): ?>
                <div class="replies-section">
                    <?php foreach ($comment->comments as $reply): ?>
                    <div class="reply-card" id="reply-<?= $reply->id ?>">
                        <div class="reply-flex">
                            <?php 
                            $replyUser = $reply->user;
                            $replyAvatarUrl = $replyUser->getAvatarUrl();
                            ?>
                            <?php if ($replyUser->photo && file_exists(Yii::getAlias('@webroot/avatars/' . $replyUser->photo))): ?>
                                <img src="<?= Yii::getAlias('@web/avatars/' . $replyUser->photo) ?>" 
                                     class="reply-avatar-img" 
                                     style="width: 32px; height: 32px; border-radius: 50%; object-fit: cover;">
                            <?php else: ?>
                                <div class="reply-avatar">
                                    <i class="fas fa-reply-all"></i>
                                </div>
                            <?php endif; ?>
                            <div class="reply-body">
                                <div class="reply-header">
                                    <span class="reply-author">
                                        <?= Html::encode($replyUser->name . ' ' . $replyUser->last_name) ?>
                                    </span>
                                    <span class="reply-time">
                                        <?= Yii::$app->formatter->asRelativeTime($reply->created_at) ?>
                                    </span>
                                </div>
                                <div class="reply-text" id="reply-text-<?= $reply->id ?>">
                                    <?= nl2br(Html::encode($reply->content)) ?>
                                </div>
                                <?php if ($reply->user_id == Yii::$app->user->id || (!Yii::$app->user->isGuest && Yii::$app->user->identity->role == 1)): ?>
                                <div class="reply-actions">
                                    <button type="button" class="btn-action delete-reply-trigger" data-id="<?= $reply->id ?>">
                                        <i class="fas fa-trash-alt"></i> Удалить
                                    </button>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
                
                <div class="reply-form-container" id="reply-form-<?= $comment->id ?>">
                    <div class="reply-form-inner">
                        <div class="reply-form-avatar">
                            <i class="fas fa-reply"></i>
                        </div>
                        <div class="reply-form-field">
                            <textarea id="reply-content-<?= $comment->id ?>" class="reply-textarea" rows="2" placeholder="Напишите ответ..."></textarea>
                            <div class="reply-form-buttons">
                                <button type="button" class="btn-action submit-reply-btn" data-id="<?= $comment->id ?>">Ответить</button>
                                <button type="button" class="btn-action cancel-reply-btn" data-id="<?= $comment->id ?>">Отмена</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>