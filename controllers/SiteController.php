<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use app\models\Countries;
use app\models\CityCountries;
use app\models\Cities;
use app\models\Events;
use app\models\Openings;
use app\models\Vehicles;
use app\models\PopularHumans;
use app\models\Monuments;
use app\models\Weapons;
use app\models\Clothes;
use app\models\Comments;
use app\components\ModerationService;

class SiteController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['add-comment', 'edit-comment', 'delete-comment'],
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    public function beforeAction($action)
    {
        if (in_array($action->id, ['add-comment', 'edit-comment', 'delete-comment'])) {
            $this->enableCsrfValidation = false;
        }
        return parent::beforeAction($action);
    }

    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    public function actionIndex()
    {
        $countries = Countries::find()->orderBy('date_origin')->all();
        
        $citiesWithPositions = [];
        
        foreach ($countries as $country) {
            $cityCountries = CityCountries::find()
                ->where(['country_id' => $country->id])
                ->with('city')
                ->orderBy(['city_countries.y' => SORT_ASC, 'city_countries.x' => SORT_ASC])
                ->all();
            
            $cities = [];
            foreach ($cityCountries as $cc) {
                if ($cc->city) {
                    $x = $cc->x !== null ? floatval($cc->x) : 50;
                    $y = $cc->y !== null ? floatval($cc->y) : 50;
                    
                    $cities[] = [
                        'id' => $cc->city->id,
                        'name' => $cc->city->name,
                        'population' => $cc->city->population,
                        'descr' => $cc->city->descr,
                        'flag' => $cc->city->flag,
                        'x' => $x,
                        'y' => $y,
                    ];
                }
            }
            
            $citiesWithPositions[$country->id] = $cities;
        }
        
        $countryId = Yii::$app->request->get('country_id', 1);
        $selectedCountry = Countries::findOne($countryId);
        
        if (!$selectedCountry) {
            $selectedCountry = Countries::findOne(1);
        }

        $popularHumans = PopularHumans::find()->with('countries')->all();
        
        return $this->render('index', [
            'countries' => $countries,
            'citiesWithPositions' => $citiesWithPositions,
            'selectedCountry' => $selectedCountry,
            'popularHumans' => $popularHumans,
        ]);
    }

    public function actionGetCities($countryId)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $cityCountries = CityCountries::find()
            ->where(['country_id' => $countryId])
            ->with('city')
            ->all();
        
        $cities = [];
        foreach ($cityCountries as $cc) {
            if ($cc->city) {
                $cities[] = [
                    'id' => $cc->city->id,
                    'name' => $cc->city->name,
                    'population' => $cc->city->population,
                    'descr' => $cc->city->descr,
                    'flag' => $cc->city->flag,
                ];
            }
        }
        
        return $cities;
    }

    public function actionGetCityInfo($cityId)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $city = Cities::findOne($cityId);
        
        if (!$city) {
            return ['error' => 'Город не найден, ID: ' . $cityId];
        }
        
        return [
            'id' => $city->id,
            'name' => $city->name,
            'population' => $city->population,
            'descr' => $city->descr,
            'flag' => $city->flag,
        ];
    }

    public function actionAbout()
    {
        return $this->render('about');
    }

    public function actionRules()
    {
        return $this->render('rules');
    }

    public function actionGetCountryData($country_id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $selectedCountry = Countries::findOne($country_id);
        
        if (!$selectedCountry) {
            return [
                'success' => false, 
                'message' => 'Страна не найдена. ID: ' . $country_id
            ];
        }
        
        $citiesWithPositions = [];
        
        $cityCountries = CityCountries::find()
            ->where(['country_id' => $country_id])
            ->with('city')
            ->all();
        
        $cities = [];
        foreach ($cityCountries as $cc) {
            if ($cc->city) {
                $x = $cc->x !== null ? floatval($cc->x) : 50;
                $y = $cc->y !== null ? floatval($cc->y) : 50;
                
                $cities[] = [
                    'id' => $cc->city->id,
                    'name' => $cc->city->name,
                    'x' => $x,
                    'y' => $y,
                ];
            }
        }
        
        $citiesWithPositions[$selectedCountry->id] = $cities;
        
        $headerHtml = $this->renderPartial('_header_content', [
            'selectedCountry' => $selectedCountry,
        ]);
        
        $mapHtml = $this->renderPartial('_map_content', [
            'selectedCountry' => $selectedCountry,
            'citiesWithPositions' => $citiesWithPositions,
        ]);
        
        return [
            'success' => true,
            'headerHtml' => $headerHtml,
            'mapHtml' => $mapHtml,
        ];
    }

    public function actionCity($id)
    {
        $city = Cities::find()
            ->with(['countries', 'events', 'popularHumans', 'openings', 'vehicles', 'monuments', 'weapons', 'clothes'])
            ->where(['id' => $id])
            ->one();
        
        if (!$city) {
            throw new NotFoundHttpException('Город не найден');
        }
        
        $currentCountry = $city->countries[0] ?? null;
        $events = $city->events;
        $popularHumans = $city->popularHumans;
        $openings = $city->openings;
        $vehicles = $city->vehicles;
        $monuments = $city->monuments;
        $weapons = $city->weapons;
        $clothes = $city->clothes;
        
        $comments = Comments::find()
            ->where(['cities_id' => $id, 'parent_id' => null])
            ->with(['user', 'comments.user'])
            ->orderBy(['created_at' => SORT_DESC])
            ->all();
        
        return $this->render('city', [
            'city' => $city,
            'currentCountry' => $currentCountry,
            'events' => $events,
            'popularHumans' => $popularHumans,
            'openings' => $openings,
            'vehicles' => $vehicles,
            'monuments' => $monuments,
            'weapons' => $weapons,
            'clothes' => $clothes,
            'comments' => $comments,
        ]);
    }

    public function actionPerson($id)
    {
        $person = PopularHumans::find()
            ->with(['countries', 'cities'])
            ->where(['id' => $id])
            ->one();
        
        if (!$person) {
            throw new NotFoundHttpException('Страница не найдена');
        }
        
        $comments = Comments::find()
            ->where(['popular_humans_id' => $id, 'parent_id' => null])
            ->with(['user', 'comments.user'])
            ->orderBy(['created_at' => SORT_DESC])
            ->all();
        
        return $this->render('person', [
            'person' => $person,
            'comments' => $comments,
        ]);
    }

    public function actionEvent($id)
    {
        $event = Events::find()
            ->with(['countries', 'cities'])
            ->where(['id' => $id])
            ->one();
        
        if (!$event) {
            throw new NotFoundHttpException('Событие не найдено');
        }
        
        $comments = Comments::find()
            ->where(['events_id' => $id, 'parent_id' => null])
            ->with(['user', 'comments.user'])
            ->orderBy(['created_at' => SORT_DESC])
            ->all();
        
        return $this->render('event', [
            'event' => $event,
            'comments' => $comments,
        ]);
    }

    public function actionOpening($id)
    {
        $opening = Openings::find()
            ->with(['countries', 'cities'])
            ->where(['id' => $id])
            ->one();
        
        if (!$opening) {
            throw new NotFoundHttpException('Открытие не найдено');
        }
        
        $comments = Comments::find()
            ->where(['openings_id' => $id, 'parent_id' => null])
            ->with(['user', 'comments.user'])
            ->orderBy(['created_at' => SORT_DESC])
            ->all();
        
        return $this->render('opening', [
            'opening' => $opening,
            'comments' => $comments,
        ]);
    }

    public function actionVehicle($id)
    {
        $vehicle = Vehicles::find()
            ->with(['countries', 'cities'])
            ->where(['id' => $id])
            ->one();
        
        if (!$vehicle) {
            throw new NotFoundHttpException('Техника не найдена');
        }
        
        $comments = Comments::find()
            ->where(['vehicles_id' => $id, 'parent_id' => null])
            ->with(['user', 'comments.user'])
            ->orderBy(['created_at' => SORT_DESC])
            ->all();
        
        return $this->render('vehicle', [
            'vehicle' => $vehicle,
            'comments' => $comments,
        ]);
    }

    public function actionMonument($id)
    {
        $monument = Monuments::find()
            ->with(['countries', 'cities'])
            ->where(['id' => $id])
            ->one();
        
        if (!$monument) {
            throw new NotFoundHttpException('Памятник не найден');
        }
        
        $comments = Comments::find()
            ->where(['monuments_id' => $id, 'parent_id' => null])
            ->with(['user', 'comments.user'])
            ->orderBy(['created_at' => SORT_DESC])
            ->all();
        
        return $this->render('monument', [
            'monument' => $monument,
            'comments' => $comments,
        ]);
    }

    public function actionWeapon($id)
    {
        $weapon = Weapons::find()
            ->with(['countries', 'cities'])
            ->where(['id' => $id])
            ->one();
        
        if (!$weapon) {
            throw new NotFoundHttpException('Оружие не найдено');
        }
        
        $comments = Comments::find()
            ->where(['weapons_id' => $id, 'parent_id' => null])
            ->with(['user', 'comments.user'])
            ->orderBy(['created_at' => SORT_DESC])
            ->all();
        
        return $this->render('weapon', [
            'weapon' => $weapon,
            'comments' => $comments,
        ]);
    }

    public function actionClothe($id)
    {
        $clothe = Clothes::find()
            ->with(['countries', 'cities'])
            ->where(['id' => $id])
            ->one();
        
        if (!$clothe) {
            throw new NotFoundHttpException('Одежда не найдена');
        }
        
        $comments = Comments::find()
            ->where(['clothes_id' => $id, 'parent_id' => null])
            ->with(['user', 'comments.user'])
            ->orderBy(['created_at' => SORT_DESC])
            ->all();
        
        return $this->render('clothe', [
            'clothe' => $clothe,
            'comments' => $comments,
        ]);
    }

public function actionAddComment()
{
    Yii::$app->response->format = Response::FORMAT_JSON;
    
    if (Yii::$app->user->isGuest) {
        return ['success' => false, 'message' => 'Необходимо авторизоваться'];
    }
    
    $banCheck = ModerationService::checkBan(Yii::$app->user->id);
    if ($banCheck['is_banned']) {
        return [
            'success' => false, 
            'message' => $banCheck['message'],
            'is_banned' => true
        ];
    }
    
    $request = Yii::$app->request;
    $content = $request->post('content');
    $parentId = $request->post('parent_id');
    $entityType = $request->post('entity_type');
    $entityId = $request->post('entity_id');
    
    if (empty($content)) {
        return ['success' => false, 'message' => 'Комментарий не может быть пустым'];
    }
    
    $check = ModerationService::validateAndRecord(
        $content,
        Yii::$app->user->id,
        null
    );
    
    if ($check['has_violation']) {
        return [
            'success' => false,
            'message' => $check['message'],
            'is_banned' => $check['is_banned']
        ];
    }
    
    $comment = new Comments();
    $comment->content = $content;
    $comment->user_id = Yii::$app->user->id;
    $comment->parent_id = $parentId ?: null;
    $comment->created_at = date('Y-m-d H:i:s');
    
    switch ($entityType) {
        case 'city':
            $comment->cities_id = $entityId;
            break;
        case 'event':
            $comment->events_id = $entityId;
            break;
        case 'opening':
            $comment->openings_id = $entityId;
            break;
        case 'person':
            $comment->popular_humans_id = $entityId;
            break;
        case 'vehicle':
            $comment->vehicles_id = $entityId;
            break;
        case 'monument':
            $comment->monuments_id = $entityId;
            break;
        case 'weapon':
            $comment->weapons_id = $entityId;
            break;
        case 'clothe':
            $comment->clothes_id = $entityId;
            break;
        case 'discussion':
            $comment->discussions_id = $entityId;
            break;
        default:
            return ['success' => false, 'message' => 'Неверный тип сущности: ' . $entityType];
    }
    
    if ($comment->save()) {
        $user = Yii::$app->user->identity;
        
        if ($entityType == 'discussion') {
            $discussion = \app\models\Discussions::findOne($entityId);
            if ($discussion) {
                $discussion->messages_count = Comments::find()->where(['discussions_id' => $entityId])->count();
                $discussion->updated_at = date('Y-m-d H:i:s');
                $discussion->save();
            }
        }
        
        return [
            'success' => true,
            'message' => 'Комментарий добавлен',
            'comment' => [
                'id' => $comment->id,
                'content' => $comment->content,
                'created_at' => Yii::$app->formatter->asRelativeTime($comment->created_at),
                'created_at_full' => Yii::$app->formatter->asDatetime($comment->created_at),
                'user' => [
                    'id' => $user->id,
                    'username' => ($user->name ?? '') . ' ' . ($user->last_name ?? ''),
                    'avatar' => $user->avatar ?? null,
                ],
                'parent_id' => $comment->parent_id,
                'user_id' => $comment->user_id,
            ]
        ];
    }
    
    return ['success' => false, 'message' => 'Ошибка при сохранении комментария'];
}
    
    public function actionEditComment()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        if (Yii::$app->user->isGuest) {
            return ['success' => false, 'message' => 'Необходимо авторизоваться'];
        }
        
        $request = Yii::$app->request;
        $id = $request->post('id');
        $content = $request->post('content');
        
        if (empty($content)) {
            return ['success' => false, 'message' => 'Комментарий не может быть пустым'];
        }
        
        $check = ModerationService::validateAndRecord(
            $content,
            Yii::$app->user->id,
            null
        );
        
        if ($check['has_violation']) {
            return [
                'success' => false,
                'message' => 'Редактирование отклонено: ' . $check['message'],
                'is_banned' => $check['is_banned']
            ];
        }
        
        $comment = Comments::findOne($id);
        
        if (!$comment) {
            return ['success' => false, 'message' => 'Комментарий не найден'];
        }
        
        if ($comment->user_id != Yii::$app->user->id && Yii::$app->user->identity->role != 1) {
            return ['success' => false, 'message' => 'У вас нет прав для редактирования этого комментария'];
        }
        
        $comment->content = $content;
        $comment->updated_at = date('Y-m-d H:i:s');
        
        if ($comment->save()) {
            return [
                'success' => true,
                'message' => 'Комментарий обновлен',
                'comment' => [
                    'id' => $comment->id,
                    'content' => $comment->content,
                    'updated_at' => Yii::$app->formatter->asRelativeTime($comment->updated_at),
                    'is_edited' => true,
                ]
            ];
        }
        
        return ['success' => false, 'message' => 'Ошибка при редактировании комментария'];
    }
    
    public function actionDeleteComment($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        if (Yii::$app->user->isGuest) {
            return ['success' => false, 'message' => 'Необходимо авторизоваться'];
        }
        
        $comment = Comments::findOne($id);
        
        if (!$comment) {
            return ['success' => false, 'message' => 'Комментарий не найден'];
        }
        
        if ($comment->user_id != Yii::$app->user->id && Yii::$app->user->identity->role != 1) {
            return ['success' => false, 'message' => 'У вас нет прав для удаления этого комментария'];
        }
        
        Comments::deleteAll(['parent_id' => $id]);
        
        if ($comment->delete()) {
            return ['success' => true, 'message' => 'Комментарий удален'];
        }
        
        return ['success' => false, 'message' => 'Ошибка при удалении комментария'];
    }

    public function actionCatalog($type = 'all', $letter = null, $country_id = 'all', $search = null, $page = 1)
    {
        $currentCountry = null;
        $limit = 12;
        $offset = ($page - 1) * $limit;
        
        if ($country_id !== 'all') {
            $currentCountry = Countries::findOne($country_id);
        }
        
        $title = 'Все материалы';
        $data = [];
        $totalCount = 0;
        $totalPages = 1;
        
        if ($type == 'all') {
            $allData = [];
            
            $citiesQuery = Cities::find()
                ->select([
                    'cities.id', 
                    'cities.name', 
                    new \yii\db\Expression("'cities' as source"), 
                    'cities.flag as img', 
                    'cities.population as extra', 
                    'cities.descr'
                ]);
            if ($country_id !== 'all') {
                $citiesQuery->leftJoin('city_countries', 'cities.id = city_countries.city_id')
                    ->andWhere(['city_countries.country_id' => $country_id])
                    ->distinct();
            }
            $cities = $citiesQuery->asArray()->all();
            foreach ($cities as &$city) {
                $city['extra_display'] = '🏙️ Город';
                $city['type_icon'] = 'fa-city';
                $city['type_name'] = 'Город';
            }
            $allData = array_merge($allData, $cities);
            
            $eventsQuery = Events::find()
                ->select([
                    'events.id', 
                    'events.name', 
                    new \yii\db\Expression("'events' as source"), 
                    'events.img', 
                    new \yii\db\Expression("YEAR(events.date) as extra_year"), 
                    'events.descr'
                ]);
            if ($country_id !== 'all') {
                $eventsQuery->andWhere(['events.countries_id' => $country_id]);
            }
            $events = $eventsQuery->asArray()->all();
            foreach ($events as &$event) {
                $event['extra'] = $event['extra_year'] ? '📅 ' . $event['extra_year'] : '📅';
                $event['extra_display'] = $event['extra'];
                $event['type_icon'] = 'fa-calendar-alt';
                $event['type_name'] = 'Событие';
                unset($event['extra_year']);
            }
            $allData = array_merge($allData, $events);
            
            $openingsQuery = Openings::find()
                ->select([
                    'openings.id', 
                    'openings.name', 
                    new \yii\db\Expression("'openings' as source"), 
                    'openings.img', 
                    new \yii\db\Expression("YEAR(openings.date) as extra_year"), 
                    'openings.descr'
                ]);
            if ($country_id !== 'all') {
                $openingsQuery->andWhere(['openings.countries_id' => $country_id]);
            }
            $openings = $openingsQuery->asArray()->all();
            foreach ($openings as &$opening) {
                $opening['extra'] = $opening['extra_year'] ? '📅 ' . $opening['extra_year'] : '📅';
                $opening['extra_display'] = $opening['extra'];
                $opening['type_icon'] = 'fa-compass';
                $opening['type_name'] = 'Открытие';
                unset($opening['extra_year']);
            }
            $allData = array_merge($allData, $openings);
            
            $humansQuery = PopularHumans::find()
                ->select([
                    'popular_humans.id', 
                    new \yii\db\Expression("CONCAT(popular_humans.name, ' ', popular_humans.last_name) as name"), 
                    new \yii\db\Expression("'humans' as source"), 
                    'popular_humans.img', 
                    'popular_humans.type as extra', 
                    'popular_humans.descr'
                ]);
            if ($country_id !== 'all') {
                $humansQuery->andWhere(['popular_humans.countries_id' => $country_id]);
            }
            $humans = $humansQuery->asArray()->all();
            foreach ($humans as &$human) {
                $human['extra_display'] = '👤 ' . ($human['extra'] ?? '');
                $human['type_icon'] = 'fa-user';
                $human['type_name'] = 'Знаменитый человек';
            }
            $allData = array_merge($allData, $humans);
            
            $vehiclesQuery = Vehicles::find()
                ->select([
                    'vehicles.id', 
                    'vehicles.name', 
                    new \yii\db\Expression("'vehicles' as source"), 
                    'vehicles.img', 
                    new \yii\db\Expression("CONCAT(vehicles.type, ' | ', vehicles.status) as extra"), 
                    'vehicles.descr'
                ]);
            if ($country_id !== 'all') {
                $vehiclesQuery->andWhere(['vehicles.countries_id' => $country_id]);
            }
            $vehicles = $vehiclesQuery->asArray()->all();
            foreach ($vehicles as &$vehicle) {
                $vehicle['extra_display'] = '🚗 ' . ($vehicle['extra'] ?? '');
                $vehicle['type_icon'] = 'fa-cogs';
                $vehicle['type_name'] = 'Техника';
            }
            $allData = array_merge($allData, $vehicles);
            
            $monumentsQuery = Monuments::find()
                ->select([
                    'monuments.id', 
                    'monuments.name', 
                    new \yii\db\Expression("'monuments' as source"), 
                    'monuments.img', 
                    'monuments.status as extra', 
                    'monuments.descr'
                ]);
            if ($country_id !== 'all') {
                $monumentsQuery->andWhere(['monuments.countries_id' => $country_id]);
            }
            $monuments = $monumentsQuery->asArray()->all();
            foreach ($monuments as &$monument) {
                $monument['extra_display'] = '🏛️ ' . ($monument['extra'] ?? '');
                $monument['type_icon'] = 'fa-landmark';
                $monument['type_name'] = 'Памятник';
            }
            $allData = array_merge($allData, $monuments);
            
            $weaponsQuery = Weapons::find()
                ->select([
                    'weapons.id', 
                    'weapons.name', 
                    new \yii\db\Expression("'weapons' as source"), 
                    'weapons.img', 
                    'weapons.status as extra', 
                    'weapons.descr'
                ]);
            if ($country_id !== 'all') {
                $weaponsQuery->andWhere(['weapons.countries_id' => $country_id]);
            }
            $weapons = $weaponsQuery->asArray()->all();
            foreach ($weapons as &$weapon) {
                $weapon['extra_display'] = '⚔️ ' . ($weapon['extra'] ?? '');
                $weapon['type_icon'] = 'fa-shield-alt';
                $weapon['type_name'] = 'Оружие';
            }
            $allData = array_merge($allData, $weapons);
            
            $clothesQuery = Clothes::find()
                ->select([
                    'clothes.id', 
                    'clothes.name', 
                    new \yii\db\Expression("'clothes' as source"), 
                    'clothes.img', 
                    'clothes.status as extra', 
                    'clothes.descr'
                ]);
            if ($country_id !== 'all') {
                $clothesQuery->andWhere(['clothes.countries_id' => $country_id]);
            }
            $clothes = $clothesQuery->asArray()->all();
            foreach ($clothes as &$cloth) {
                $cloth['extra_display'] = '👕 ' . ($cloth['extra'] ?? '');
                $cloth['type_icon'] = 'fa-tshirt';
                $cloth['type_name'] = 'Одежда';
            }
            $allData = array_merge($allData, $clothes);
            
            if ($search && $search !== '') {
                $allData = array_filter($allData, function($item) use ($search) {
                    return stripos($item['name'], $search) !== false;
                });
            }
            
            if (!$search && $letter && $letter !== 'all') {
                $allData = array_filter($allData, function($item) use ($letter) {
                    $firstChar = mb_strtoupper(mb_substr($item['name'], 0, 1));
                    return $firstChar === $letter;
                });
            }
            
            usort($allData, function($a, $b) {
                return strcmp($a['name'], $b['name']);
            });
            
            $totalCount = count($allData);
            $totalPages = ceil($totalCount / $limit);
            $data = array_slice($allData, $offset, $limit);
            
        } else {
            switch ($type) {
                case 'cities':
                    $query = Cities::find();
                    $title = 'Города';
                    if ($country_id !== 'all') {
                        $query->leftJoin('city_countries', 'cities.id = city_countries.city_id')
                            ->andWhere(['city_countries.country_id' => $country_id])
                            ->distinct();
                    }
                    break;
                case 'events':
                    $query = Events::find();
                    $title = 'События';
                    if ($country_id !== 'all') {
                        $query->andWhere(['countries_id' => $country_id]);
                    }
                    break;
                case 'openings':
                    $query = Openings::find();
                    $title = 'Открытия';
                    if ($country_id !== 'all') {
                        $query->andWhere(['countries_id' => $country_id]);
                    }
                    break;
                case 'humans':
                    $query = PopularHumans::find();
                    $title = 'Знаменитые люди';
                    if ($country_id !== 'all') {
                        $query->andWhere(['countries_id' => $country_id]);
                    }
                    break;
                case 'vehicles':
                    $query = Vehicles::find();
                    $title = 'Техника';
                    if ($country_id !== 'all') {
                        $query->andWhere(['countries_id' => $country_id]);
                    }
                    break;
                case 'monuments':
                    $query = Monuments::find();
                    $title = 'Памятники';
                    if ($country_id !== 'all') {
                        $query->andWhere(['countries_id' => $country_id]);
                    }
                    break;
                case 'weapons':
                    $query = Weapons::find();
                    $title = 'Оружие';
                    if ($country_id !== 'all') {
                        $query->andWhere(['countries_id' => $country_id]);
                    }
                    break;
                case 'clothes':
                    $query = Clothes::find();
                    $title = 'Одежда';
                    if ($country_id !== 'all') {
                        $query->andWhere(['countries_id' => $country_id]);
                    }
                    break;
                default:
                    $query = Cities::find();
                    $title = 'Города';
                    if ($country_id !== 'all') {
                        $query->leftJoin('city_countries', 'cities.id = city_countries.city_id')
                            ->andWhere(['city_countries.country_id' => $country_id])
                            ->distinct();
                    }
                    break;
            }
            
            if ($search && $search !== '') {
                $query->andWhere(['like', 'name', $search]);
            }
            
            if (!$search && $letter && $letter !== 'all') {
                $query->andWhere(['like', 'name', $letter . '%', false]);
            }
            
            $totalCount = $query->count();
            $totalPages = ceil($totalCount / $limit);
            $data = $query->orderBy(['name' => SORT_ASC])
                ->limit($limit)
                ->offset($offset)
                ->all();
        }
        
        $letters = ['А', 'Б', 'В', 'Г', 'Д', 'Е', 'Ё', 'Ж', 'З', 'И', 'Й', 'К', 'Л', 'М', 'Н', 'О', 'П', 'Р', 'С', 'Т', 'У', 'Ф', 'Х', 'Ц', 'Ч', 'Ш', 'Щ', 'Э', 'Ю', 'Я'];
        
        if (Yii::$app->request->isAjax) {
            return $this->renderAjax('_catalog_results', [
                'data' => $data,
                'type' => $type,
                'totalPages' => $totalPages,
                'currentPage' => $page,
                'totalCount' => $totalCount,
            ]);
        }
        
        return $this->render('catalog', [
            'data' => $data,
            'type' => $type,
            'title' => $title,
            'letters' => $letters,
            'currentLetter' => $letter,
            'selectedCountryId' => $country_id,
            'currentCountry' => $currentCountry,
            'searchQuery' => $search,
            'totalPages' => $totalPages,
            'currentPage' => $page,
            'totalCount' => $totalCount,
        ]);
    }

    public function actionCountry($id)
    {
        $country = Countries::find()
            ->with(['capital', 'cities', 'popularHumans', 'events', 'openings', 'vehicles', 'monuments', 'weapons', 'clothes'])
            ->where(['id' => $id])
            ->one();
        
        if (!$country) {
            throw new NotFoundHttpException('Страна не найдена');
        }
        
        $cities = $country->cities;
        $capital = $country->capital;
        $popularHumans = $country->popularHumans;
        $events = $country->events;
        $openings = $country->openings;
        $vehicles = $country->vehicles;
        $monuments = $country->monuments;
        $weapons = $country->weapons;
        $clothes = $country->clothes;
        
        $comments = Comments::find()
            ->where(['discussions_id' => $id, 'parent_id' => null])
            ->with(['user', 'comments.user'])
            ->orderBy(['created_at' => SORT_DESC])
            ->all();
        
        return $this->render('country', [
            'country' => $country,
            'capital' => $capital,
            'cities' => $cities,
            'popularHumans' => $popularHumans,
            'events' => $events,
            'openings' => $openings,
            'vehicles' => $vehicles,
            'monuments' => $monuments,
            'weapons' => $weapons,
            'clothes' => $clothes,
            'comments' => $comments,
        ]);
    }
}