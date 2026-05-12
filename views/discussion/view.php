<?php
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = $discussion->title;

$isBanned = false;
if (!Yii::$app->user->isGuest) {
    $currentUser = Yii::$app->user->identity;
    $isBanned = $currentUser->isBanned();
}

$this->registerJsFile('@web/js/discussion-chat.js', ['depends' => [\yii\web\JqueryAsset::class]]);

$chatConfig = [
    'discussionId' => $discussion->id,
    'currentUserId' => Yii::$app->user->id,
    'sendUrl' => Url::to(['discussion/send']),
    'deleteUrl' => Url::to(['discussion/delete-message']),
    'getMessagesUrl' => Url::to(['discussion/get-messages', 'id' => $discussion->id]),
];

$this->registerJs('initChat(' . json_encode($chatConfig) . ');', \yii\web\View::POS_END);
?>

<div class="discussion-page">
    <div class="discussion-container">
        
        <h1 class="discussion-title"><b><?= Html::encode($discussion->title) ?></b></h1>
        
        <div class="comment-thread discussion-description">
            <div class="comment-main">
                <div class="comment-flex">
                    <div class="comment-left">
                        <div class="comment-avatar">
                            <i class="fas fa-user"></i>
                        </div>
                        <div class="comment-content">
                            <div class="comment-header">
                                <span class="comment-author"><?= Html::encode($discussion->user->name . ' ' . $discussion->user->last_name) ?></span>
                                <span class="comment-time"><?= Yii::$app->formatter->asRelativeTime($discussion->created_at) ?></span>
                            </div>
                            <div class="comment-text"><?= nl2br(Html::encode($discussion->content)) ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="comments-wrapper">
            <div class="comments-title">
                <h3>
                    <i class="fas fa-comment-dots"></i>
                    Обсуждение
                </h3>
                <span class="comments-count" id="messages-count">0 сообщений</span>
            </div>
        </div>
        
        <div class="chat-container" id="chat-container">
            <div class="chat-messages" id="chat-messages">
                <div class="empty-state">
                    <i class="fas fa-comments"></i>
                    <h4>Загрузка сообщений...</h4>
                </div>
            </div>
        </div>
        
        <?php if ($isBanned): ?>
        <div class="add-comment-card" style="background: rgba(255,0,0,0.1); border-color: rgba(255,0,0,0.3);">
            <div class="empty-state" style="padding: 60px 20px;">
                <i class="fas fa-ban" style="color: #ff6b6b; font-size: 48px; margin-bottom: 15px;"></i>
                <p style="color: #ff6b6b; font-size: 16px; margin-bottom: 10px;">Вы не можете отправлять сообщения</p>
                <p style="font-size: 12px; color: rgba(255,255,255,0.6);">Ваш аккаунт заблокирован.</p>
            </div>
        </div>
        <?php else: ?>
        <div class="add-comment-card">
            <div class="card-header">
                <div class="header-user">
                    <?php 
                    $currentUser = Yii::$app->user->identity;
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
                        <div class="user-label">Напишите сообщение...</div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <textarea id="message-input" class="comment-input" rows="3" placeholder="Ваше сообщение..."></textarea>
                </div>
                <div style="display: flex; justify-content: flex-end;">
                    <button id="send-btn" class="submit-comment-btn">
                        <i class="fas fa-paper-plane"></i> Отправить
                    </button>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>