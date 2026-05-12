<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

$this->title = 'Заявка #' . $application->id;

$user = Yii::$app->user->identity;
$isAdmin = ($user->role == 1);
?>

<div class="view-page">
    <div class="view-container">
        <div class="view-header">
            <h1>
                <i class="fas fa-ticket-alt"></i>
                <?= Html::encode($this->title) ?>
            </h1>
            <p>Просмотр обращения</p>
        </div>
        
        <div class="view-card">
            <!-- Информация о заявке -->
            <div class="info-block">
                <div class="info-label">Тема заявки</div>
                <div class="info-value"><?= Html::encode($application->name) ?></div>
            </div>
            
            <div class="info-block">
                <div class="info-label">Тип заявки</div>
                <div class="info-value">
                    <?php
                    $types = [
                        'bug' => 'Ошибка',
                        'question' => 'Вопрос',
                        'suggestion' => 'Предложение',
                        'violation' => 'Нарушение правила',
                    ];
                    echo $types[$application->type] ?? $application->type;
                    ?>
                </div>
            </div>
            
            <?php if ($isAdmin && $application->user): ?>
            <div class="info-block">
                <div class="info-label">Автор</div>
                <div class="info-value">
                    <?= Html::a($application->user->getFullName(), ['admin/users'], ['style' => 'color: #ffd700; text-decoration: none;']) ?>
                </div>
            </div>
            <?php endif; ?>
            
            <div class="info-block">
                <div class="info-label">Описание</div>
                <div class="info-value"><?= nl2br(Html::encode($application->descr)) ?></div>
            </div>
            
            <div class="info-block">
                <div class="info-label">Вложение</div>
                <div class="info-value">
                    <?php if ($application->file && file_exists(Yii::getAlias('@webroot/applications/' . $application->file))): ?>
                        <?= Html::a('<i class="fas fa-download"></i> Скачать файл', Yii::getAlias('@web/applications/' . $application->file), [
                            'class' => 'back-btn',
                            'target' => '_blank'
                        ]) ?>
                    <?php else: ?>
                        <span style="color: rgba(255,255,255,0.5);">Файл не прикреплен</span>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="info-block">
                <div class="info-label">Статус</div>
                <div class="info-value">
                    <?php if ($application->status == 0): ?>
                        <span class="status-badge status-new">Ожидает ответа</span>
                    <?php else: ?>
                        <span class="status-badge status-closed">Рассмотрено</span>
                    <?php endif; ?>
                </div>
            </div>

            <div class="answer-block" id="answer-block">
                <div class="answer-label">
                    <i class="fas fa-reply"></i> Ответ администратора
                    <?php if ($isAdmin && $application->answer): ?>
                        <button type="button" id="edit-answer-btn" class="filter-btn status-badge">
                            <i class="fas fa-edit"></i> Редактировать ответ
                        </button>
                    <?php endif; ?>
                </div>
                <div id="answer-display" class="answer-value" style="<?= $isAdmin && $application->answer ? '' : '' ?>">
                    <?php if ($application->answer): ?>
                        <?= nl2br(Html::encode($application->answer)) ?>
                    <?php else: ?>
                        <span style="color: rgba(255,255,255,0.5);">Ответ еще не дан</span>
                    <?php endif; ?>
                </div>
                
                <!-- Форма редактирования ответа (для админа) -->
                <?php if ($isAdmin): ?>
                <div id="answer-edit-form" style="display: none; margin-top: 15px;">
                    <?php $form = ActiveForm::begin([
                        'action' => Url::to(['admin/update-answer', 'id' => $application->id]),
                        'method' => 'post',
                    ]); ?>
                    
                    <div class="form-group">
                        <textarea name="answer" class="comment-input" rows="5" style="width: 100%; padding: 12px; background: rgba(0,0,0,0.5); border: 1px solid rgba(255,255,255,0.1); border-radius: 10px; color: white; resize: vertical;"><?= Html::encode($application->answer) ?></textarea>
                    </div>
                    
                    <div class="text-center" style="margin-top: 15px;">
                        <button type="submit" class="back-btn">
                            <i class="fas fa-save"></i> Сохранить изменения
                        </button>
                        <button type="button" id="cancel-edit-btn" class="back-btn">
                            <i class="fas fa-times"></i> Отмена
                        </button>
                    </div>
                    
                    <?php ActiveForm::end(); ?>
                </div>
                <?php endif; ?>
            </div>
            
            <?php if ($isAdmin && !$application->answer): ?>
            <div class="answer-form-block" style="margin-top: 20px; padding-top: 20px; border-top: 1px solid rgba(255,255,255,0.1);">
                <div class="answer-label" style="margin-bottom: 15px;">
                    <i class="fas fa-reply"></i> Добавить ответ
                </div>
                <?php $form = ActiveForm::begin([
                    'action' => Url::to(['admin/answer-application', 'id' => $application->id]),
                    'method' => 'post',
                ]); ?>
                
                <div class="form-group">
                    <textarea name="answer" class="comment-input" rows="5" placeholder="Введите ответ на заявку..." style="width: 100%; padding: 12px; background: rgba(0,0,0,0.5); border: 1px solid rgba(255,255,255,0.1); border-radius: 10px; color: white; resize: vertical;"><?= Html::encode($application->answer) ?></textarea>
                </div>
                
                <input type="hidden" name="status" value="1">
                
                <div class="text-center" style="margin-top: 15px;">
                    <button type="submit" class="back-btn">
                        <i class="fas fa-paper-plane"></i> Отправить ответ
                    </button>
                </div>
                
                <?php ActiveForm::end(); ?>
            </div>
            <?php endif; ?>
            
            <div class="text-center" style="margin-top: 20px;">
                <?php if ($isAdmin): ?>
                    <a href="<?= Url::to(['application/my-applications']) ?>" class="back-btn">
                        <i class="fas fa-arrow-left"></i> К списку заявок
                    </a>
                <?php else: ?>
                    <a href="<?= Url::to(['application/my-applications']) ?>" class="back-btn">
                        <i class="fas fa-arrow-left"></i> К моим заявкам
                    </a>
                <?php endif; ?>
                
                <?php if (!$isAdmin): ?>
                    <a href="<?= Url::to(['application/help']) ?>" class="back-btn" style="margin-left: 10px;">
                        <i class="fas fa-plus"></i> Новая заявка
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php
$this->registerJsFile('@web/js/main.js', ['depends' => [\yii\web\JqueryAsset::class]]);
?>