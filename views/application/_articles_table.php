<?php
use yii\helpers\Html;
use yii\helpers\Url;

$filteredArticles = $articles;
if ($filterStatus != 'all') {
    $filteredArticles = array_filter($filteredArticles, function($a) use ($filterStatus) {
        if ($filterStatus == 'pending') {
            return $a->moderation_status == 0;
        } elseif ($filterStatus == 'approved') {
            return $a->moderation_status == 1;
        }
        return true;
    });
}
?>

<?php if (empty($filteredArticles)): ?>
    <div class="empty-state">
        <i class="fas fa-file-alt"></i>
        <p>
            <?php if ($filterStatus == 'pending'): ?>
                Нет статей на модерации
            <?php elseif ($filterStatus == 'approved'): ?>
                Нет одобренных статей
            <?php else: ?>
                Статей пока нет
            <?php endif; ?>
        </p>
    </div>
<?php else: ?>
    <table class="applications-table">
        <thead>
            <tr>
                <th>Название</th>
                <th>Тип</th>
                <?php if ($isAdmin): ?>
                    <th>Автор</th>
                <?php endif; ?>
                <th>Статус</th>
                <th>Дата подачи</th>
                <th>Действия</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($filteredArticles as $article): ?>
                <?php
                $articleUrl = '';
                $typeParam = '';
                
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
                    <td><a href="<?= $articleUrl ?>" style="color: #ff6b6b; text-decoration: none;"><?= Html::encode($article->name) ?></a></td>
                    <td><?= $typeName ?></td>
                    <?php if ($isAdmin): ?>
                        <td>
                            <?php if ($article->user): ?>
                                <?= Html::a($article->user->getFullName(), ['admin/users'], ['style' => 'text-decoration: none;']) ?>
                            <?php else: ?>
                                <span style="color: #888;">Система</span>
                            <?php endif; ?>
                        </td>
                    <?php endif; ?>
                    <td>
                        <?php if ($article->moderation_status == 0): ?>
                            <span class="status-badge status-new">На модерации</span>
                        <?php else: ?>
                            <span class="status-badge status-closed">Одобрено</span>
                        <?php endif; ?>
                    </td>
                    <td><?= Yii::$app->formatter->asDate($article->created_at, 'dd.MM.yyyy') ?></td>
                    <td>
                        <?= Html::a('Просмотр', $articleUrl, ['class' => 'status-badge back-btn']) ?>
                        
                        <?php if ($isAdmin): ?>
                            <?php if ($article->moderation_status == 0): ?>
                                <?= Html::a('Одобрить', ['admin/approve-article', 'type' => $typeParam, 'id' => $article->id], [
                                    'class' => 'status-badge back-btn',
                                    'data-confirm' => 'Одобрить эту статью?',
                                    'data-method' => 'post',
                                ]) ?>
                                <?= Html::a('Отклонить', ['admin/reject-article', 'type' => $typeParam, 'id' => $article->id], [
                                    'class' => 'status-badge back-btn',
                                    'data-confirm' => 'Удалить эту статью?',
                                    'data-method' => 'post',
                                ]) ?>
                            <?php else: ?>
                                <?= Html::a('Удалить', ['admin/reject-article', 'type' => $typeParam, 'id' => $article->id], [
                                    'class' => 'status-badge back-btn',
                                    'data-confirm' => 'Удалить эту статью?',
                                    'data-method' => 'post',
                                ]) ?>
                            <?php endif; ?>
                        <?php else: ?>
                            <?php if (!$isBanned && $article->moderation_status == 0 && $article->user_id == Yii::$app->user->id): ?>
                                <?= Html::a('Редактировать', ['application/update-article', 'type' => $typeParam, 'id' => $article->id], ['class' => 'status-badge back-btn']) ?>
                                <?= Html::a('Удалить', ['application/delete-article', 'type' => $typeParam, 'id' => $article->id], [
                                    'class' => 'status-badge back-btn',
                                    'data-confirm' => 'Вы уверены, что хотите удалить эту статью?',
                                    'data-method' => 'post',
                                ]) ?>
                            <?php endif; ?>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>