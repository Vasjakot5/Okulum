<?php
use yii\helpers\Html;
use yii\helpers\Url;
?>

<?php if (empty($cities)): ?>
    <div class="empty-state">
        <i class="fas fa-city"></i>
        <p>Городов не найдено</p>
    </div>
<?php else: ?>
    <table class="applications-table">
        <thead>
            <tr>
                <th>Название</th>
                <th>Население</th>
                <th>Флаг</th>
                <th>Страны</th>
                <th>Действия</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($cities as $city): ?>
                <?php
                $countryNames = [];
                foreach ($city->countries as $country) {
                    $countryNames[] = Html::encode($country->name);
                }
                ?>
                <tr>
                    <td>
                        <?= Html::a(Html::encode($city->name), ['site/city', 'id' => $city->id], ['target' => '_blank', 'style' => 'color: #ff6b6b; text-decoration: none;']) ?>
                    </td>
                    <td><?= number_format($city->population, 0, ',', ' ') ?></td>
                    <td>
                        <?php if ($city->flag && file_exists(Yii::getAlias('@webroot/flags_imgs/' . $city->flag))): ?>
                            <img src="<?= Yii::getAlias('@web/flags_imgs/' . $city->flag) ?>" style="width: 50px; height: 30px; object-fit: cover; border-radius: 3px;">
                        <?php else: ?>
                            <span style="color: #888;">нет</span>
                        <?php endif; ?>
                    </td>
                    <td><?= implode(', ', $countryNames) ?></td>
                    <td>
                        <div class="action-buttons" style="display: flex; gap: 5px; flex-wrap: wrap;">
                            <?= Html::a('Редактировать', ['update-city', 'id' => $city->id], ['class' => 'back-btn status-badge', 'title' => 'Редактировать город']) ?>
                            <?= Html::a('Удалить', ['delete-city', 'id' => $city->id], [
                                'class' => 'back-btn status-badge delete-city-btn',
                                'title' => 'Удалить город',
                                'data-id' => $city->id,
                                'data-name' => Html::encode($city->name),
                            ]) ?>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>