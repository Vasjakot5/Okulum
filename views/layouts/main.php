<?php

/** @var yii\web\View $this */
/** @var string $content */

use app\assets\AppAsset;
use app\widgets\Alert;
use yii\bootstrap5\Breadcrumbs;
use yii\bootstrap5\Html;
use yii\bootstrap5\Nav;
use yii\bootstrap5\NavBar;
use yii\helpers\Url;

AppAsset::register($this);

$this->registerCsrfMetaTags();
$this->registerCssFile('https://use.fontawesome.com/releases/v6.4.0/css/all.css');
$this->registerMetaTag(['charset' => Yii::$app->charset], 'charset');
$this->registerMetaTag(['name' => 'viewport', 'content' => 'width=device-width, initial-scale=1, shrink-to-fit=no']);
$this->registerMetaTag(['name' => 'description', 'content' => $this->params['meta_description'] ?? '']);
$this->registerMetaTag(['name' => 'keywords', 'content' => $this->params['meta_keywords'] ?? '']);
$this->registerLinkTag(['rel' => 'icon', 'type' => 'image/png', 'href' => Yii::getAlias('@web/imgs/icon.png')]);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" class="h-100">
<head>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
    <meta name="csrf-param" content="_csrf">
    <meta name="csrf-token" content="<?= Yii::$app->request->csrfToken ?>">
</head>

<body class="d-flex flex-column h-100">
<?php $this->beginBody() ?>

<header id="header">
    <?php
    NavBar::begin([
        'brandLabel' => '<div style="display: flex; align-items: flex-end; gap: 0;"><img src="' . Yii::getAlias('@web/imgs/icon.png') . '" style="height: 32px;"><span style="font-size: 20px; font-weight: 500; line-height: 1;">kulum</span></div>',
        'brandUrl' => Yii::$app->homeUrl,
        'options' => ['class' => 'navbar-expand-md navbar-dark fixed-top', 'style' => 'background: rgba(30, 40, 50, 0.95); border-bottom: 1px solid rgba(255, 255, 255, 0.1);'],
    ]);
    
    $menuItems = [
        ['label' => '<i class="fas fa-home"></i> Главная', 'url' => ['/site/index']],
        ['label' => '<i class="fas fa-book-open"></i> Каталог', 'url' => ['/site/catalog']],
    ];
    
    if (!Yii::$app->user->isGuest) {
        $menuItems[] = ['label' => '<i class="fas fa-comments"></i> Обсуждения', 'url' => ['/discussion/index']];
    }
    
    $menuItems[] = [
        'label' => '<i class="fas fa-bars"></i> Информация <i class="fas fa-caret-down"></i>',
        'items' => [
            ['label' => '<i class="fas fa-info-circle"></i> О проекте', 'url' => ['/site/about']],
            ['label' => '<i class="fas fa-shield-alt"></i> Правила сайта', 'url' => ['/site/rules']],
            ['label' => '<i class="fas fa-question-circle"></i> Техподдержка ', 'url' => ['/application/help']],
        ],
    ];
    
    if (Yii::$app->user->isGuest) {
        $menuItems[] = ['label' => '<i class="fas fa-sign-in-alt"></i> Вход', 'url' => ['/auth/login']];
    } else {
        $user = Yii::$app->user->identity;
        $userName = $user->name . ' ' . $user->last_name;
        
        $menuItems[] = ['label' => '<i class="fas fa-user-circle"></i> ' . Html::encode($userName), 'url' => ['/auth/profile']];
    }
    
    echo Nav::widget([
        'options' => ['class' => 'navbar-nav ms-auto'],
        'items' => $menuItems,
        'encodeLabels' => false,
    ]);
    
    NavBar::end();
    ?>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
</header>

<main id="main" class="flex-shrink-0" role="main">
    <div class="container">
        <?php if (!empty($this->params['breadcrumbs'])): ?>
            <?= Breadcrumbs::widget(['links' => $this->params['breadcrumbs']]) ?>
        <?php endif ?>
        <?= Alert::widget() ?>
        <?= $content ?>
    </div>
</main>

<footer id="footer" class="mt-auto py-4" style="background: rgba(30, 40, 50, 0.95); border-top: 1px solid rgba(255, 255, 255, 0.1);">
    <div class="container">
        <div class="row">
            <div class="col-md-4 text-center text-md-start">
                <div style="display: flex; align-items: flex-end; gap: 0; justify-content: center; justify-content-md-start;">
                    <img src="<?= Yii::getAlias('@web/imgs/icon.png') ?>" style="height: 28px;">
                    <h5 style="color: rgba(255, 0, 0, 0.7); margin: 0; line-height: 1;">kulum</h5>
                </div>
                <p class="text-center" style="color: rgba(255,255,255,0.7); font-size: 13px; margin-top: 15px;">
                    История России под микроскопом
                </p>
            </div>
            <div class="col-md-4 text-center">
                <h5 style="color: rgba(255, 0, 0, 0.7); margin-bottom: 15px;">Навигация</h5>
                <ul style="list-style: none; padding: 0; margin: 0;">
                    <li><a href="<?= Url::to(['/site/index']) ?>" style="color: rgba(255,255,255,0.7); text-decoration: none; font-size: 13px;">Главная</a></li>
                    <li><a href="<?= Url::to(['/site/catalog']) ?>" style="color: rgba(255,255,255,0.7); text-decoration: none; font-size: 13px;">Каталог</a></li>
                    <li><a href="<?= Url::to(['/site/about']) ?>" style="color: rgba(255,255,255,0.7); text-decoration: none; font-size: 13px;">О проекте</a></li>
                </ul>
            </div>
            <div class="col-md-4 text-center">
                <h5 style="color: rgba(255, 0, 0, 0.7); margin-bottom: 15px;">Социальные сети</h5>
                <div style="display: flex; gap: 15px; justify-content: center;">
                    <a href="https://vk.com/id283614217" target="_blank" class="letter-btn">
                        <i class="fab fa-vk"></i>
                    </a>
                    <a href="https://t.me/Redsith" target="_blank" class="letter-btn">
                        <i class="fab fa-telegram-plane"></i>
                    </a>
                </div>
                <p style="color: rgba(255,255,255,0.5); font-size: 12px; margin-top: 15px;">
                    &copy; Okulum <?= date('Y') ?>
                </p>
            </div>
        </div>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
