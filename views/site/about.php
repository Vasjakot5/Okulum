<?php
use yii\helpers\Url;
use yii\helpers\Html;
$this->title = 'О проекте';
?>

<div class="about-page">
    <div class="about-container">
        <h1 class="text-center" style="opacity: 0; animation: fadeInUp 0.5s ease forwards 0.2s;">О проекте</h1>
        <div class="city-description-container" style="opacity: 0; animation: fadeInUp 0.8s ease forwards 0.2s;">
            <div class="city-description">
                <div class="description-text">
                    <p><strong>Окулум</strong> — это интерактивный исторический проект, который позволяет изучать историю России через изменяющуюся карту.</p>
                    <p>Сайт предназначен для <strong>студентов</strong>, <strong>преподавателей</strong> и <strong>всех интересующихся историей</strong>, чтобы они могли увидеть, как менялась страна на протяжении веков.</p>
                    <p>Здесь вы найдете:</p>
                    <ul style="margin-top: 10px; padding-left: 20px;">
                        <li>Интерактивные карты России разных эпох</li>
                        <li>Информацию о городах и их истории</li>
                        <li>Знаменитых людей, оставивших след в истории</li>
                        <li>Важнейшие события и открытия</li>
                        <li>Технику, оружие, одежду разных периодов</li>
                        <li>Памятники культурного наследия</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="city-section" style="opacity: 0; animation: fadeInUp 0.8s ease forwards 0.4s;">
            <div class="section-header">
                <h2><i class="fas fa-target"></i> Цели проекта</h2>
            </div>
            <div class="goals-grid">
                <div class="goal-card">
                    <i class="fas fa-graduation-cap"></i>
                    <h3>Образование</h3>
                    <p>Сделать изучение истории России наглядным и увлекательным</p>
                </div>
                <div class="goal-card">
                    <i class="fas fa-map-marked-alt"></i>
                    <h3>Визуализация</h3>
                    <p>Показать территориальные изменения России в разные периоды</p>
                </div>
                <div class="goal-card">
                    <i class="fas fa-users"></i>
                    <h3>Доступность</h3>
                    <p>Предоставить бесплатный доступ к историческим материалам</p>
                </div>
                <div class="goal-card">
                    <i class="fas fa-search"></i>
                    <h3>Исследование</h3>
                    <p>Помочь исследователям и студентам в изучении истории</p>
                </div>
            </div>
        </div>

        <div class="city-section" style="opacity: 0; animation: fadeInUp 0.8s ease forwards 0.6s;">
            <div class="section-header">
                <h2><i class="fas fa-question-circle"></i> Как пользоваться сайтом</h2>
            </div>
            <div class="steps-grid">
                <div class="step-card">
                    <i class="fas fa-map"></i>
                    <h3>Изучайте карту</h3>
                    <p>На главной странице выберите период (Российская империя, СССР или РФ)</p>
                </div>
                <div class="step-card">
                    <i class="fas fa-city"></i>
                    <h3>Исследуйте города</h3>
                    <p>Нажимайте на точки на карте, чтобы узнать о городах</p>
                </div>
                <div class="step-card">
                    <i class="fas fa-book-open"></i>
                    <h3>Открывайте каталог</h3>
                    <p>В каталоге собраны все материалы по категориям</p>
                </div>
                <div class="step-card">
                    <i class="fas fa-search"></i>
                    <h3>Ищите нужное</h3>
                    <p>Используйте поиск и фильтры для быстрого нахождения информации</p>
                </div>
            </div>
        </div>

        <div class="city-section" style="opacity: 0; animation: fadeInUp 0.8s ease forwards 0.8s;">
            <div class="section-header">
                <h2><i class="fas fa-user"></i> О создателе</h2>
            </div>
            <div class="creator-card">
                <div class="creator-photo">
                    <img src="<?= Yii::getAlias('@web/imgs/avatar.jpg') ?>" 
                         alt="Создатель сайта">
                </div>
                <div class="creator-info">
                    <h3>Разработчик проекта</h3>
                    <p>Здравствуйте! Меня зовут Василий Рудных и это мой проект под названием "Окулум".</p>
                    <p>Данный проект создан для того, чтобы сделать историю России более доступной и понятной. Интерактивная карта позволяет наглядно увидеть, как менялись границы государства, развивались города и культура на протяжении веков.</p>
                    <p>Все материалы собраны из открытых источников и адаптированы для удобного изучения. Сайт будет постоянно пополняться новыми городами, событиями и личностями.</p>
                     <hr>
                    <div class="creator-socials">
                        <p>Связь со мной:</p>
                        <p>
                        <a href="https://vk.com/id283614217" target="_blank" class="letter-btn">
                            <i class="fab fa-vk"></i>
                        </a>
                        </p>
                        <p>
                        <a href="https://t.me/Redsith" target="_blank" class="letter-btn">
                            <i class="fab fa-telegram-plane"></i>
                        </a>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="person-back">
            <a href="<?= Url::to(['site/index']) ?>" class="back-btn">
                <i class="fas fa-map-marked-alt"></i> Перейти на карту
            </a>
            <a href="<?= Url::to(['site/catalog']) ?>" class="back-btn">
                <i class="fas fa-book-open"></i> Открыть каталог
            </a>
        </div>
    </div>
</div>