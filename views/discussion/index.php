<?php
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Обсуждения';

$user = Yii::$app->user->identity;
$isAdmin = ($user->role == 1);
?>

<div class="discussions-page">
    <div class="discussions-container">
        <div class="discussions-header">
            <h1>
                <i class="fas fa-comments"></i>
                <?= Html::encode($this->title) ?>
            </h1>
            <p>Все обсуждения на портале</p>
            <?= Html::a('<i class="fas fa-plus"></i> Создать обсуждение', ['create'], ['class' => 'back-btn']) ?>
        </div>
        
        <div class="discussions-card">
            <?php if (empty($discussions)): ?>
                <div class="empty-state">
                    <i class="fas fa-comments"></i>
                    <h4>Пока нет обсуждений</h4>
                    <p>Будьте первым, кто создаст обсуждение</p>
                </div>
            <?php else: ?>
                <div class="discussions-list">
                    <?php foreach ($discussions as $disc): ?>
                        <?php 
                        $showDiscussion = true;
                        
                        if ($disc->is_admin_only == 1) {
                            if (!$isAdmin) {
                                $showDiscussion = false;
                            }
                        }
                        
                        if (!$showDiscussion) {
                            continue;
                        }
                        
                        $messagesCount = \app\models\Comments::find()
                            ->where(['discussions_id' => $disc->id, 'parent_id' => null])
                            ->count();
                        ?>
                        <a href="<?= Url::to(['view', 'id' => $disc->id]) ?>" class="discussion-item2" id="discussion-<?= $disc->id ?>">
                            <div class="discussion-item-main">
                                <div class="discussion-item-header">
                                    <h3 class="discussion-item-title">
                                        <?= Html::encode($disc->title) ?>
                                    </h3>
                                    <div class="discussion-item-meta">
                                        <span class="discussion-item-stats">
                                            <i class="fas fa-comment"></i> 
                                            <?= $messagesCount ?> <?= $messagesCount % 10 == 1 && $messagesCount % 100 != 11 ? 'сообщение' : ($messagesCount % 10 >= 2 && $messagesCount % 10 <= 4 && ($messagesCount % 100 < 10 || $messagesCount % 100 >= 20) ? 'сообщения' : 'сообщений') ?>
                                        </span>
                                        <span class="discussion-item-date">
                                            <i class="far fa-clock"></i> 
                                            <?= Yii::$app->formatter->asRelativeTime($disc->updated_at) ?>
                                        </span>
                                    </div>
                                </div>
                                <div class="discussion-item-author">
                                    <i class="fas fa-user"></i> 
                                    <?= Html::encode($disc->user->name . ' ' . $disc->user->last_name) ?>
                                    <?php if ($disc->is_admin_only == 1 && $isAdmin): ?>
                                        <span style="margin-left: 10px; font-size: 12px;">
                                            <i class="fas fa-shield-alt"></i> Админское
                                        </span>
                                    <?php endif; ?>
                                </div>
                                <div class="discussion-item-preview">
                                    <?= Html::encode(mb_substr(strip_tags($disc->content), 0, 150)) ?>
                                    <?php if (mb_strlen(strip_tags($disc->content)) > 150): ?>...<?php endif; ?>
                                </div>
                            </div>
                        </a>
                    <?php endforeach; ?>
                    
                    <?php 
                    $hasVisibleDiscussions = false;
                    foreach ($discussions as $disc) {
                        if ($disc->is_admin_only == 0 || ($disc->is_admin_only == 1 && $isAdmin)) {
                            $hasVisibleDiscussions = true;
                            break;
                        }
                    }
                    if (!$hasVisibleDiscussions): 
                    ?>
                        <div class="empty-state">
                            <i class="fas fa-comments"></i>
                            <h4>Нет доступных обсуждений</h4>
                            <p><?= $isAdmin ? 'Нет созданных обсуждений' : 'Нет доступных для вас обсуждений' ?></p>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>