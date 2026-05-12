<?php
use yii\helpers\Html;

if (empty($comments)): ?>
    <div style="text-align: center; padding: 50px; background: rgba(0,0,0,0.15); border-radius: 12px;">
        <i class="fas fa-comments" style="font-size: 48px; opacity: 0.3; margin-bottom: 15px; display: block;"></i>
        <p style="margin: 0; color: rgba(255,255,255,0.5);">Пока нет сообщений. Напишите первое!</p>
    </div>
<?php else: ?>
    <?php foreach ($comments as $comment): ?>
        <div class="message-item" data-id="<?= $comment->id ?>" style="margin-bottom: 12px; background: rgba(0,0,0,0.2); border-radius: 10px; padding: 10px 12px;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                <div style="display: flex; gap: 10px; flex: 1;">
                    <div style="width: 32px; height: 32px; background: rgba(255,70,70,0.5); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-user" style="font-size: 14px; color: white;"></i>
                    </div>
                    <div style="flex: 1;">
                        <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap; margin-bottom: 4px;">
                            <strong style="color: #ffd700; font-size: 13px;"><?= Html::encode($comment->user->name . ' ' . $comment->user->last_name) ?></strong>
                            <span style="font-size: 10px; color: rgba(255,255,255,0.4);"><?= Yii::$app->formatter->asRelativeTime($comment->created_at) ?></span>
                        </div>
                        <div style="color: rgba(255,255,255,0.85); font-size: 13px; line-height: 1.5;"><?= nl2br(Html::encode($comment->content)) ?></div>
                    </div>
                </div>
                <?php if ($comment->user_id == Yii::$app->user->id): ?>
                    <button class="delete-msg" data-id="<?= $comment->id ?>" style="background: none; border: none; color: rgba(255,100,100,0.6); cursor: pointer; padding: 4px;">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>