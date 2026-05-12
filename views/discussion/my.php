<?php
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Мои обсуждения';
?>

<div class="my-discussions-page">
    <div class="my-discussions-container">
        <div class="my-discussions-header">
            <h1>
                <i class="fas fa-comments"></i>
                <?= Html::encode($this->title) ?>
            </h1>
            <p>Все созданные вами обсуждения</p>
        </div>
        
        <div class="my-discussions-card">
            <?php if (empty($discussions)): ?>
                <div class="empty-state">
                    <i class="fas fa-comments"></i>
                    <h4>У вас пока нет обсуждений</h4>
                    <p>Создайте первое обсуждение, чтобы начать диалог</p>
                </div>
            <?php else: ?>
                <div class="discussions-list">
                    <?php foreach ($discussions as $discussion): ?>
                        <div class="discussion-item" id="discussion-<?= $discussion->id ?>">
                            <div class="discussion-item-main">
                                <div class="discussion-item-header">
                                    <h3 class="discussion-item-title">
                                        <?= Html::encode($discussion->title) ?>
                                    </h3>
                                    <span class="discussion-item-date">
                                        <i class="far fa-clock"></i> 
                                        <?= Yii::$app->formatter->asRelativeTime($discussion->created_at) ?>
                                    </span>
                                </div>
                                <div class="discussion-item-preview">
                                    <?= Html::encode(mb_substr(strip_tags($discussion->content), 0, 150)) ?>
                                    <?php if (mb_strlen(strip_tags($discussion->content)) > 150): ?>...<?php endif; ?>
                                </div>
                                <div class="discussion-item-footer">
                                    <div class="discussion-item-stats">
                                        <span class="stat-item">
                                            <i class="fas fa-comment"></i> 
                                            <?= $discussion->getMessages()->count() ?> сообщений
                                        </span>
                                    </div>
                                    <div class="discussion-item-actions">
                                        <?= Html::a('<i class="fas fa-eye"></i> Просмотр', ['view', 'id' => $discussion->id], ['class' => 'back-btn  profile-btn btn-view ']) ?>
                                        <button type="button" class="delete-discussion-btn profile-btn back-btn" data-id="<?= $discussion->id ?>" data-title="<?= Html::encode($discussion->title) ?>" style="color: white !important;">
                                            <i class="fas fa-trash-alt"></i> Удалить
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            
            <div class="text-center" style="margin-top: 30px;">
                <?= Html::a('<i class="fas fa-plus"></i> Создать обсуждение', ['create'], ['class' => 'back-btn']) ?>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('.delete-discussion-btn').on('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        var id = $(this).data('id');
        var title = $(this).data('title');
        
        if (confirm('Удалить обсуждение "' + title + '"? Все сообщения в нем будут также удалены.')) {
            $.ajax({
                url: '<?= Url::to(['discussion/delete-discussion']) ?>',
                type: 'POST',
                data: { id: id },
                dataType: 'json',
                success: function(res) {
                    if (res.success) {
                        $('#discussion-' + id).fadeOut(300, function() {
                            $(this).remove();
                            if ($('.discussion-item').length === 0) {
                                location.reload();
                            }
                        });
                    } else {
                        alert(res.message);
                    }
                },
                error: function() {
                    alert('Ошибка при удалении');
                }
            });
        }
    });
});
</script>