<?php
use yii\helpers\Html;
use yii\helpers\Url;

$filteredApplications = $applications;
if ($filterStatus != 'all') {
    $filteredApplications = array_filter($filteredApplications, function($a) use ($filterStatus) {
        if ($filterStatus == 'pending') {
            return $a->status == 0;
        } elseif ($filterStatus == 'closed') {
            return $a->status == 1;
        }
        return true;
    });
}
?>

<?php if (empty($filteredApplications)): ?>
    <div class="empty-state">
        <i class="fas fa-inbox"></i>
        <p>Заявок пока нет</p>
    </div>
<?php else: ?>
    <table class="applications-table">
        <thead>
            <tr>
                <th>Тема</th>
                <th>Тип</th>
                <?php if ($isAdmin): ?>
                    <th>Пользователь</th>
                <?php endif; ?>
                <th>Статус</th>
                <th>Дата подачи</th>
                <th>Действия</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($filteredApplications as $app): ?>
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
                <?php if ($isAdmin): ?>
                    <td>
                        <?= Html::a($app->user->getFullName(), ['admin/users'], ['style' => 'text-decoration: none;']) ?>
                    </td>
                <?php endif; ?>
                <td>
                    <?php if ($app->status == 0): ?>
                        <span class="status-badge status-new">Ожидает ответа</span>
                    <?php else: ?>
                        <span class="status-badge status-closed">Рассмотрено</span>
                    <?php endif; ?>
                </td>
                <td><?= Yii::$app->formatter->asDate($app->created_at, 'dd.MM.yyyy') ?></td>
                <td>
                    <?= Html::a('Просмотр', ['application/view', 'id' => $app->id], ['class' => 'status-badge back-btn']) ?>
                    
                    <?php if ($isAdmin): ?>
                        <?= Html::a('Удалить', ['admin/delete-application', 'id' => $app->id], [
                            'class' => 'status-badge back-btn',
                            'data-confirm' => 'Вы уверены, что хотите удалить эту заявку?',
                            'data-method' => 'post',
                        ]) ?>
                    <?php else: ?>
                        <?php if ($app->status == 0 && !$isBanned): ?>
                            <?= Html::a('Редактировать', ['application/update-application', 'id' => $app->id], ['class' => 'status-badge back-btn']) ?>
                        <?php endif; ?>
                        <?= Html::a('Удалить', ['application/delete-application', 'id' => $app->id], [
                            'class' => 'status-badge back-btn',
                            'data-confirm' => 'Вы уверены, что хотите удалить эту заявку?',
                            'data-method' => 'post',
                        ]) ?>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>