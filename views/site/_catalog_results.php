<?php
use yii\helpers\Url;
use yii\helpers\Html;

$totalPages = $totalPages ?? 1;
$currentPage = $currentPage ?? 1;
$totalCount = $totalCount ?? count($data);
?>

<?php if (empty($data)): ?>
    <div class="no-results">
        <i class="fas fa-search"></i>
        <p>Ничего не найдено</p>
        <p class="no-results-hint">Попробуйте изменить поисковый запрос</p>
    </div>
<?php else: ?>
    <div class="items-grid">
        <?php 
        $index = 0;
        foreach ($data as $item): 
            if (isset($item['moderation_status']) && $item['moderation_status'] == 0) {
                continue;
            }
            if (isset($item->moderation_status) && $item->moderation_status == 0) {
                continue;
            }
            
            if ($type == 'all') {
                if ($item['source'] == 'cities') {
                    $url = Url::to(['site/city', 'id' => $item['id']]);
                    $icon = 'fa-city';
                    $imgPath = Yii::getAlias('@web/flags_imgs');
                    $hasImg = !empty($item['img']);
                    $img = $item['img'];
                } elseif ($item['source'] == 'events') {
                    $url = Url::to(['site/event', 'id' => $item['id']]);
                    $icon = 'fa-calendar-alt';
                    $imgPath = Yii::getAlias('@web/events_imgs');
                    $hasImg = !empty($item['img']);
                    $img = $item['img'];
                } elseif ($item['source'] == 'openings') {
                    $url = Url::to(['site/opening', 'id' => $item['id']]);
                    $icon = 'fa-compass';
                    $imgPath = Yii::getAlias('@web/openings_imgs');
                    $hasImg = !empty($item['img']);
                    $img = $item['img'];
                } elseif ($item['source'] == 'humans') {
                    $url = Url::to(['site/person', 'id' => $item['id']]);
                    $icon = 'fa-user';
                    $imgPath = Yii::getAlias('@web/popular_humans_imgs');
                    $hasImg = !empty($item['img']);
                    $img = $item['img'];
                } elseif ($item['source'] == 'vehicles') {
                    $url = Url::to(['site/vehicle', 'id' => $item['id']]);
                    $icon = 'fa-cogs';
                    $imgPath = Yii::getAlias('@web/vehicles_imgs');
                    $hasImg = !empty($item['img']);
                    $img = $item['img'];
                } elseif ($item['source'] == 'monuments') {
                    $url = Url::to(['site/monument', 'id' => $item['id']]);
                    $icon = 'fa-landmark';
                    $imgPath = Yii::getAlias('@web/monument_imgs');
                    $hasImg = !empty($item['img']);
                    $img = $item['img'];
                } elseif ($item['source'] == 'weapons') {
                    $url = Url::to(['site/weapon', 'id' => $item['id']]);
                    $icon = 'fa-shield-alt';
                    $imgPath = Yii::getAlias('@web/weapon_imgs');
                    $hasImg = !empty($item['img']);
                    $img = $item['img'];
                } elseif ($item['source'] == 'clothes') {
                    $url = Url::to(['site/clothe', 'id' => $item['id']]);
                    $icon = 'fa-tshirt';
                    $imgPath = Yii::getAlias('@web/clothes_imgs');
                    $hasImg = !empty($item['img']);
                    $img = $item['img'];
                } else {
                    $url = '#';
                    $icon = 'fa-book';
                    $imgPath = '';
                    $hasImg = false;
                    $img = '';
                }
            } else {
                if ($type == 'humans') {
                    $url = Url::to(['site/person', 'id' => $item->id]);
                    $imgPath = Yii::getAlias('@web/popular_humans_imgs');
                    $hasImg = !empty($item->img);
                    $img = $item->img ?? '';
                    $icon = 'fa-user';
                } elseif ($type == 'vehicles') {
                    $url = Url::to(['site/vehicle', 'id' => $item->id]);
                    $imgPath = Yii::getAlias('@web/vehicles_imgs');
                    $hasImg = !empty($item->img);
                    $img = $item->img ?? '';
                    $icon = 'fa-cogs';
                } elseif ($type == 'openings') {
                    $url = Url::to(['site/opening', 'id' => $item->id]);
                    $imgPath = Yii::getAlias('@web/openings_imgs');
                    $hasImg = !empty($item->img);
                    $img = $item->img ?? '';
                    $icon = 'fa-compass';
                } elseif ($type == 'events') {
                    $url = Url::to(['site/event', 'id' => $item->id]);
                    $imgPath = Yii::getAlias('@web/events_imgs');
                    $hasImg = !empty($item->img);
                    $img = $item->img ?? '';
                    $icon = 'fa-calendar-alt';
                } elseif ($type == 'cities') {
                    $url = Url::to(['site/city', 'id' => $item->id]);
                    $imgPath = Yii::getAlias('@web/flags_imgs');
                    $hasImg = !empty($item->flag);
                    $img = $item->flag ?? '';
                    $icon = 'fa-city';
                } elseif ($type == 'monuments') {
                    $url = Url::to(['site/monument', 'id' => $item->id]);
                    $imgPath = Yii::getAlias('@web/monument_imgs');
                    $hasImg = !empty($item->img);
                    $img = $item->img ?? '';
                    $icon = 'fa-landmark';
                } elseif ($type == 'weapons') {
                    $url = Url::to(['site/weapon', 'id' => $item->id]);
                    $imgPath = Yii::getAlias('@web/weapon_imgs');
                    $hasImg = !empty($item->img);
                    $img = $item->img ?? '';
                    $icon = 'fa-shield-alt';
                } elseif ($type == 'clothes') {
                    $url = Url::to(['site/clothe', 'id' => $item->id]);
                    $imgPath = Yii::getAlias('@web/clothes_imgs');
                    $hasImg = !empty($item->img);
                    $img = $item->img ?? '';
                    $icon = 'fa-tshirt';
                } else {
                    $url = '#';
                    $imgPath = '';
                    $hasImg = false;
                    $img = '';
                    $icon = 'fa-book';
                }
            }
        ?>
            <a href="<?= $url ?>" class="item-card-link" style="animation: fadeInUp 0.3s ease forwards <?= $index * 0.05 ?>s; opacity: 0;">
                <div class="item-card">
                    <div class="item-row">
                        <?php if ($hasImg && $img): ?>
                            <img src="<?= $imgPath . '/' . $img ?>" class="item-img square" alt="<?= Html::encode($item['name'] ?? $item->name ?? '') ?>">
                        <?php else: ?>
                            <div class="item-no-img square">
                                <i class="fas <?= $icon ?>"></i>
                            </div>
                        <?php endif; ?>
                        
                        <div class="item-info">
                            <?php if ($type == 'all'): ?>
                                <h3><?= Html::encode($item['name']) ?></h3>
                                <p class="item-sub">
                                    <?php if ($item['source'] == 'cities'): ?>
                                        <i class="fas fa-city"></i> Город
                                    <?php elseif ($item['source'] == 'events'): ?>
                                        <i class="fas fa-calendar-alt"></i> Событие
                                    <?php elseif ($item['source'] == 'openings'): ?>
                                        <i class="fas fa-compass"></i> Открытие 
                                    <?php elseif ($item['source'] == 'humans'): ?>
                                        <i class="fas fa-users"></i> <?= Html::encode($item['extra']) ?>
                                    <?php elseif ($item['source'] == 'vehicles'): ?>
                                        <i class="fas fa-cogs"></i> Техника
                                    <?php elseif ($item['source'] == 'monuments'): ?>
                                        <i class="fas fa-landmark"></i> <?= Html::encode($item['extra']) ?>
                                    <?php elseif ($item['source'] == 'weapons'): ?>
                                        <i class="fas fa-shield-alt"></i> <?= Html::encode($item['extra']) ?>
                                    <?php elseif ($item['source'] == 'clothes'): ?>
                                        <i class="fas fa-tshirt"></i> <?= Html::encode($item['extra']) ?>
                                    <?php endif; ?>
                                </p>
                            <?php elseif ($type == 'humans'): ?>
                                <h3><?= Html::encode($item->name . ' ' . $item->last_name) ?></h3>
                                <p class="item-sub"><?= Html::encode($item->type) ?></p>
                                <p class="item-date">
                                    <?= date('Y', strtotime($item->date_born)) ?>
                                    <?= $item->date_death ? ' - ' . date('Y', strtotime($item->date_death)) : '' ?>
                                </p>
                            <?php elseif ($type == 'vehicles'): ?>
                                <h3><?= Html::encode($item->name) ?></h3>
                                <p class="item-sub"><?= Html::encode($item->type) ?> | <?= Html::encode($item->status) ?></p>
                            <?php elseif ($type == 'openings'): ?>
                                <h3><?= Html::encode($item->name) ?></h3>
                                <p class="item-sub"><?= date('Y', strtotime($item->date)) ?></p>
                            <?php elseif ($type == 'events'): ?>
                                <h3><?= Html::encode($item->name) ?></h3>
                                <p class="item-sub"><?= date('Y', strtotime($item->date)) ?></p>
                            <?php elseif ($type == 'cities'): ?>
                                <h3><?= Html::encode($item->name) ?></h3>
                                <p class="item-sub"><i class="fas fa-users"></i> <?= number_format($item->population, 0, '', ' ') ?> чел.</p>
                            <?php elseif ($type == 'monuments'): ?>
                                <h3><?= Html::encode($item->name) ?></h3>
                                <p class="item-sub"><?= Html::encode($item->status) ?></p>
                            <?php elseif ($type == 'weapons'): ?>
                                <h3><?= Html::encode($item->name) ?></h3>
                                <p class="item-sub"><?= Html::encode($item->status) ?></p>
                            <?php elseif ($type == 'clothes'): ?>
                                <h3><?= Html::encode($item->name) ?></h3>
                                <p class="item-sub"><?= Html::encode($item->status) ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <?php if ($type == 'all'): ?>
                        <p class="item-descr"><?= Html::encode(mb_substr($item['descr'], 0, 120)) ?>...</p>
                    <?php elseif ($type == 'humans'): ?>
                        <p class="item-descr"><?= Html::encode(mb_substr($item->descr, 0, 120)) ?>...</p>
                    <?php elseif ($type == 'vehicles'): ?>
                        <p class="item-descr"><?= Html::encode(mb_substr($item->descr, 0, 120)) ?>...</p>
                    <?php elseif ($type == 'openings'): ?>
                        <p class="item-descr"><?= Html::encode(mb_substr($item->descr, 0, 120)) ?>...</p>
                    <?php elseif ($type == 'events'): ?>
                        <p class="item-descr"><?= Html::encode(mb_substr($item->descr, 0, 120)) ?>...</p>
                    <?php elseif ($type == 'cities'): ?>
                        <p class="item-descr"><?= Html::encode(mb_substr($item->descr, 0, 120)) ?>...</p>
                    <?php elseif ($type == 'monuments'): ?>
                        <p class="item-descr"><?= Html::encode(mb_substr($item->descr, 0, 120)) ?>...</p>
                    <?php elseif ($type == 'weapons'): ?>
                        <p class="item-descr"><?= Html::encode(mb_substr($item->descr, 0, 120)) ?>...</p>
                    <?php elseif ($type == 'clothes'): ?>
                        <p class="item-descr"><?= Html::encode(mb_substr($item->descr, 0, 120)) ?>...</p>
                    <?php endif; ?>
                </div>
            </a>
        <?php 
        $index++;
        endforeach; 
        ?>
    </div>
    
    <?php if ($totalPages > 1): ?>
    <div class="pagination-container" data-total-pages="<?= $totalPages ?>">
        <div class="pagination">
            <a href="#" class="pagination-btn prev-btn <?= $currentPage <= 1 ? 'disabled' : '' ?>" data-page="<?= $currentPage - 1 ?>">
                <i class="fas fa-chevron-left"></i> Назад
            </a>
            
            <?php
            $startPage = max(1, $currentPage - 2);
            $endPage = min($totalPages, $currentPage + 2);
            
            if ($startPage > 1): ?>
                <a href="#" class="pagination-btn" data-page="1">1</a>
                <?php if ($startPage > 2): ?>
                    <span class="pagination-dots">...</span>
                <?php endif; ?>
            <?php endif; ?>
            
            <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                <a href="#" class="pagination-btn <?= $i == $currentPage ? 'active' : '' ?>" data-page="<?= $i ?>">
                    <?= $i ?>
                </a>
            <?php endfor; ?>
            
            <?php if ($endPage < $totalPages): ?>
                <?php if ($endPage < $totalPages - 1): ?>
                    <span class="pagination-dots">...</span>
                <?php endif; ?>
                <a href="#" class="pagination-btn" data-page="<?= $totalPages ?>"><?= $totalPages ?></a>
            <?php endif; ?>
            
            <a href="#" class="pagination-btn next-btn <?= $currentPage >= $totalPages ? 'disabled' : '' ?>" data-page="<?= $currentPage + 1 ?>">
                Вперед <i class="fas fa-chevron-right"></i>
            </a>
        </div>
    </div>
    <?php endif; ?>
<?php endif; ?>