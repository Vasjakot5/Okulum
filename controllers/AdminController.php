<?php

namespace app\controllers;
use Yii;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\AccessControl;
use yii\web\NotFoundHttpException;
use app\models\User;
use app\models\Applications;
use app\models\Events;
use app\models\Openings;
use app\models\PopularHumans;
use app\models\Vehicles;
use app\models\Monuments;
use app\models\Weapons;
use app\models\Clothes;
use app\models\Cities;
use yii\web\UploadedFile;
use app\models\Countries;
use app\models\Comments;
use app\models\Discussions;
use app\models\CityForm;
use app\models\CityCountries;

class AdminController extends Controller
{

    public $enableCsrfValidation = false;   

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function ($rule, $action) {
                            return Yii::$app->user->identity->role == 1;
                        }
                    ],
                ],
            ],
        ];
    }

    public function beforeAction($action)
    {
        $actions = [
            'ban-user', 'unban-user', 'make-admin', 'remove-admin', 
            'reset-violations', 'filter-users', 'delete-comment', 
            'delete-message', 'delete-article', 'delete-discussion', 
            'delete-application', 'update-city-coordinates', 'update-city-all-countries', 'filter-cities', 'delete-city'
        ];
        
        if (in_array($action->id, $actions)) {
            $this->enableCsrfValidation = false;
        }
        
        return parent::beforeAction($action);
    }

    public function actionUsers()
    {
        $users = User::find()->orderBy(['id' => SORT_ASC])->all();
        
        return $this->render('users', [
            'users' => $users,
        ]);
    }

    public function actionBanUser()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        
        $id = Yii::$app->request->post('id');
        $days = Yii::$app->request->post('days', 1);
        $reason = Yii::$app->request->post('reason', 'Нарушение правил сайта');
        
        $user = User::findOne($id);
        if (!$user) {
            return ['success' => false, 'message' => 'Пользователь не найден'];
        }
        
        if ($user->id == Yii::$app->user->id) {
            return ['success' => false, 'message' => 'Вы не можете заблокировать себя'];
        }
        
        $admin = Yii::$app->user->identity;
        $adminName = $admin->getFullName();
        
        $user->ban_status = User::BAN_STATUS_TEMP;
        $user->ban_reason = "Заблокирован администратором {$adminName} на {$days} дней. Причина: {$reason}";
        $user->ban_until = date('Y-m-d H:i:s', strtotime("+{$days} days"));
        
        if ($user->save(false)) {
            return ['success' => true, 'message' => "Пользователь {$user->getFullName()} заблокирован на {$days} дней. Причина: {$reason}"];
        }
        
        return ['success' => false, 'message' => 'Ошибка при сохранении'];
    }

    public function actionUnbanUser()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        
        $id = Yii::$app->request->post('id');
        
        $user = User::findOne($id);
        if (!$user) {
            return ['success' => false, 'message' => 'Пользователь не найден'];
        }
        
        $user->ban_status = User::BAN_STATUS_NONE;
        $user->ban_reason = null;
        $user->ban_until = null;
        
        if ($user->save(false)) {
            return ['success' => true, 'message' => "Пользователь {$user->getFullName()} разблокирован"];
        }
        
        return ['success' => false, 'message' => 'Ошибка при разблокировке'];
    }

    public function actionMakeAdmin()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        
        $id = Yii::$app->request->post('id');
        
        $user = User::findOne($id);
        if (!$user) {
            return ['success' => false, 'message' => 'Пользователь не найден'];
        }
        
        $user->role = 1;
        $user->save(false);
        
        return ['success' => true, 'message' => "Пользователь {$user->getFullName()} назначен администратором"];
    }

    public function actionRemoveAdmin()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        
        $id = Yii::$app->request->post('id');
        
        $user = User::findOne($id);
        if (!$user) {
            return ['success' => false, 'message' => 'Пользователь не найден'];
        }
        
        $user->role = 0;
        $user->save(false);
        
        return ['success' => true, 'message' => "Права администратора сняты с пользователя {$user->getFullName()}"];
    }

    public function actionResetViolations()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        
        $id = Yii::$app->request->post('id');
        
        $user = User::findOne($id);
        if (!$user) {
            return ['success' => false, 'message' => 'Пользователь не найден'];
        }
        
        $user->violations_count = 0;
        $user->save(false);
        
        return ['success' => true, 'message' => "Счетчик нарушений пользователя {$user->getFullName()} сброшен"];
    }

    public function actionFilterUsers($filter = 'all', $search = '')
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        
        $users = User::find()->all();
        
        $total = count($users);
        $banned = count(array_filter($users, function($u) { return $u->isBanned(); }));
        $active = $total - $banned;
        
        $html = $this->renderPartial('_users_table', [
            'users' => $users,
            'filterStatus' => $filter,
            'searchQuery' => $search
        ]);
        
        return [
            'success' => true,
            'html' => $html,
            'total' => $total,
            'banned' => $banned,
            'active' => $active
        ];
    }

    public function actionAnswerApplication($id)
    {
        $application = Applications::findOne($id);
        if (!$application) {
            throw new NotFoundHttpException('Заявка не найдена');
        }
        
        if (Yii::$app->request->isPost) {
            $answer = Yii::$app->request->post('answer');
            $status = Yii::$app->request->post('status', 1);
            
            $application->answer = $answer;
            $application->status = $status;
            $application->updated_at = date('Y-m-d H:i:s');
            $application->save();
            
            Yii::$app->session->setFlash('success', 'Ответ сохранён');
            return $this->redirect(['application/view', 'id' => $application->id]);
        }
        
        return $this->render('answer-application', [
            'application' => $application,
        ]);
    }

    public function actionUpdateAnswer($id)
    {
        $application = Applications::findOne($id);
        if (!$application) {
            throw new NotFoundHttpException('Заявка не найдена');
        }
        
        if (Yii::$app->request->isPost) {
            $answer = Yii::$app->request->post('answer');
            
            $application->answer = $answer;
            $application->updated_at = date('Y-m-d H:i:s');
            $application->save();
            
            Yii::$app->session->setFlash('success', 'Ответ обновлен');
        }
        
        return $this->redirect(['application/view', 'id' => $id]);
    }

    public function actionApproveArticle($type, $id)
    {
        $article = $this->findArticle($type, $id);
        if (!$article) {
            throw new NotFoundHttpException('Статья не найдена');
        }
        
        $article->moderation_status = 1;
        $article->save();
        
        Yii::$app->session->setFlash('success', 'Статья одобрена');
        
        return $this->redirect(['application/my-applications']);
    }

    public function actionRejectArticle($type, $id)
    {
        $article = $this->findArticle($type, $id);
        if (!$article) {
            throw new NotFoundHttpException('Статья не найдена');
        }
        
        $article->delete();
        
        Yii::$app->session->setFlash('success', 'Статья удалена');
        
        return $this->redirect(['application/my-applications']);
    }

    public function actionDeleteApplication($id)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        
        $application = Applications::findOne($id);
        if (!$application) {
            return ['success' => false, 'message' => 'Заявка не найдена'];
        }
        
        if ($application->delete()) {
            return ['success' => true, 'message' => 'Заявка удалена'];
        }
        
        return ['success' => false, 'message' => 'Ошибка при удалении'];
    }

    public function actionDeleteComment()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        
        $id = Yii::$app->request->post('id');
        $comment = Comments::findOne($id);
        
        if (!$comment) {
            return ['success' => false, 'message' => 'Комментарий не найден'];
        }
        
        if ($comment->delete()) {
            return ['success' => true, 'message' => 'Комментарий удален'];
        }
        
        return ['success' => false, 'message' => 'Ошибка при удалении'];
    }

    public function actionDeleteArticle()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        
        $type = Yii::$app->request->post('type');
        $id = Yii::$app->request->post('id');
        
        $article = $this->findArticle($type, $id);
        
        if (!$article) {
            return ['success' => false, 'message' => 'Статья не найдена'];
        }
        
        if ($article->img) {
            $path = $this->getImagePath($type) . $article->img;
            if (file_exists($path)) {
                unlink($path);
            }
        }
        
        if ($article->delete()) {
            return ['success' => true, 'message' => 'Статья удалена'];
        }
        
        return ['success' => false, 'message' => 'Ошибка при удалении'];
    }

    public function actionDeleteMessage()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        
        $id = Yii::$app->request->post('id');
        $message = Comments::findOne($id);
        
        if (!$message) {
            return ['success' => false, 'message' => 'Сообщение не найдено'];
        }
        
        if ($message->delete()) {
            return ['success' => true, 'message' => 'Сообщение удалено'];
        }
        
        return ['success' => false, 'message' => 'Ошибка при удалении'];
    }

    public function actionDeleteDiscussion()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        
        $id = Yii::$app->request->post('id');
        $discussion = Discussions::findOne($id);
        
        if (!$discussion) {
            return ['success' => false, 'message' => 'Обсуждение не найдено'];
        }
        
        Comments::deleteAll(['discussions_id' => $id]);
        
        if ($discussion->delete()) {
            return ['success' => true, 'message' => 'Обсуждение удалено'];
        }
        
        return ['success' => false, 'message' => 'Ошибка при удалении'];
    }

    public function actionUserView($id)
    {
        $user = User::findOne($id);
        if (!$user) {
            throw new NotFoundHttpException('Пользователь не найден');
        }
        
        $articleComments = Comments::find()
            ->where(['user_id' => $id])
            ->andWhere(['discussions_id' => null])
            ->orderBy(['created_at' => SORT_DESC])
            ->all();
        
        $discussionMessages = Comments::find()
            ->where(['user_id' => $id])
            ->andWhere(['not', ['discussions_id' => null]])
            ->with(['discussions'])
            ->orderBy(['created_at' => SORT_DESC])
            ->all();
        
        $events = Events::find()->where(['user_id' => $id])->all();
        $openings = Openings::find()->where(['user_id' => $id])->all();
        $humans = PopularHumans::find()->where(['user_id' => $id])->all();
        $vehicles = Vehicles::find()->where(['user_id' => $id])->all();
        $monuments = Monuments::find()->where(['user_id' => $id])->all();
        $weapons = Weapons::find()->where(['user_id' => $id])->all();
        $clothes = Clothes::find()->where(['user_id' => $id])->all();
        
        $articles = array_merge($events, $openings, $humans, $vehicles, $monuments, $weapons, $clothes);
        usort($articles, function($a, $b) {
            return strtotime($b->created_at) - strtotime($a->created_at);
        });
        
        $discussions = Discussions::find()
            ->where(['user_id' => $id])
            ->orderBy(['created_at' => SORT_DESC])
            ->all();
        
        $applications = Applications::find()
            ->where(['user_id' => $id])
            ->orderBy(['created_at' => SORT_DESC])
            ->all();
        
        return $this->render('user-view', [
            'user' => $user,
            'articleComments' => $articleComments,    
            'discussionMessages' => $discussionMessages, 
            'articles' => $articles,
            'discussions' => $discussions,
            'applications' => $applications,
        ]);
    }

    private function findArticle($type, $id)
    {
        switch ($type) {
            case 'event':
                return Events::findOne($id);
            case 'opening':
                return Openings::findOne($id);
            case 'human':
                return PopularHumans::findOne($id);
            case 'vehicle':
                return Vehicles::findOne($id);
            case 'monument':
                return Monuments::findOne($id);
            case 'weapon':
                return Weapons::findOne($id);
            case 'clothe':
                return Clothes::findOne($id);
            default:
                return null;
        }
    }

    private function getImagePath($type)
    {
        $paths = [
            'event' => Yii::getAlias('@webroot/events_imgs/'),
            'opening' => Yii::getAlias('@webroot/openings_imgs/'),
            'human' => Yii::getAlias('@webroot/popular_humans_imgs/'),
            'vehicle' => Yii::getAlias('@webroot/vehicles_imgs/'),
            'monument' => Yii::getAlias('@webroot/monuments_imgs/'),
            'weapon' => Yii::getAlias('@webroot/weapons_imgs/'),
            'clothe' => Yii::getAlias('@webroot/clothes_imgs/'),
        ];
        
        return $paths[$type] ?? Yii::getAlias('@webroot/');
    }

    public function actionUpdateCityCoordinates()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        
        $cityId = Yii::$app->request->post('city_id');
        $countryId = Yii::$app->request->post('country_id');
        $x = Yii::$app->request->post('x');
        $y = Yii::$app->request->post('y');
        
        $log = date('Y-m-d H:i:s') . " - POST: " . json_encode(Yii::$app->request->post()) . "\n";
        file_put_contents('debug_coords.txt', $log, FILE_APPEND);
        
        if (!$cityId || !$countryId) {
            file_put_contents('debug_coords.txt', "  -> Missing city_id or country_id\n", FILE_APPEND);
            return ['success' => false, 'message' => 'Не указан город или страна'];
        }
        
        $cc = CityCountries::find()->where(['city_id' => $cityId, 'country_id' => $countryId])->one();
        
        if (!$cc) {
            file_put_contents('debug_coords.txt', "  -> CityCountry not found for city_id=$cityId, country_id=$countryId\n", FILE_APPEND);
            return ['success' => false, 'message' => 'Связь города со страной не найдена'];
        }
        
        $cc->x = $x;
        $cc->y = $y;
        
        if ($cc->save()) {
            file_put_contents('debug_coords.txt', "  -> SUCCESS: saved x=$x, y=$y\n", FILE_APPEND);
            return ['success' => true, 'message' => 'Координаты обновлены'];
        }
        
        file_put_contents('debug_coords.txt', "  -> ERROR: " . json_encode($cc->errors) . "\n", FILE_APPEND);
        return ['success' => false, 'message' => 'Ошибка при сохранении: ' . json_encode($cc->errors)];
    }

    public function actionUpdateCityAllCountries()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        
        $cityId = Yii::$app->request->post('city_id');
        $x = Yii::$app->request->post('x');
        $y = Yii::$app->request->post('y');
        
        $updated = CityCountries::updateAll(
            ['x' => $x, 'y' => $y],
            ['city_id' => $cityId]
        );
        
        if ($updated !== false) {
            return ['success' => true, 'message' => "Обновлено записей: $updated"];
        }
        
        return ['success' => false, 'message' => 'Ошибка при обновлении'];
    }

    public function actionCreateCity()
    {
        $model = new CityForm();
        $countries = Countries::find()->orderBy(['name' => SORT_ASC])->all();
        
        if ($model->load(Yii::$app->request->post())) {
            $model->flagFile = UploadedFile::getInstance($model, 'flagFile');
            
            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Город успешно добавлен!');
                return $this->redirect(['site/index']);
            } else {
                Yii::$app->session->setFlash('error', 'Ошибка при добавлении города');
            }
        }
        
        return $this->render('create-city', [
            'model' => $model,
            'countries' => $countries,
        ]);
    }

    public function actionCities()
    {
        $countryId = Yii::$app->request->get('country_id', 'all');
        
        if ($countryId != 'all') {
            $cityIds = CityCountries::find()
                ->where(['country_id' => $countryId])
                ->select('city_id')
                ->column();
            $cities = Cities::find()
                ->where(['id' => $cityIds])
                ->with(['countries'])
                ->orderBy(['name' => SORT_ASC])
                ->all();
        } else {
            $cities = Cities::find()
                ->with(['countries'])
                ->orderBy(['name' => SORT_ASC])
                ->all();
        }
        
        return $this->render('cities', [
            'cities' => $cities,
        ]);
    }

    public function actionFilterCities()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        
        $countryId = Yii::$app->request->get('country_id');
        $search = Yii::$app->request->get('search');
        
        $query = Cities::find()->with(['countries']);
        
        if ($countryId && $countryId != 'all') {
            $cityIds = CityCountries::find()
                ->where(['country_id' => $countryId])
                ->select('city_id')
                ->column();
            $query->andWhere(['id' => $cityIds]);
        }
        
        if ($search) {
            $query->andWhere(['like', 'name', $search]);
        }
        
        $cities = $query->orderBy(['name' => SORT_ASC])->all();
        
        $html = $this->renderPartial('_cities_table_ajax', [
            'cities' => $cities,
        ]);
        
        return ['success' => true, 'html' => $html];
    }

    public function actionUpdateCity($id)
    {
        $model = Cities::findOne($id);
        if (!$model) {
            throw new NotFoundHttpException('Город не найден');
        }
        
        $oldFlag = $model->flag;
        
        $cityCountries = CityCountries::find()->where(['city_id' => $id])->all();
        $selectedCountries = [];
        $currentX = 50;
        $currentY = 50;
        
        foreach ($cityCountries as $cc) {
            $selectedCountries[] = $cc->country_id;
            $currentX = $cc->x ?? 50;
            $currentY = $cc->y ?? 50;
        }
        
        if ($model->load(Yii::$app->request->post())) {
            $flagFile = UploadedFile::getInstance($model, 'flagFile');
            
            if ($flagFile && $flagFile->size > 0) {
                $fileName = time() . '_' . rand(1000, 9999) . '.' . $flagFile->extension;
                $path = Yii::getAlias('@webroot/flags_imgs/');
                if (!file_exists($path)) mkdir($path, 0777, true);
                
                if ($flagFile->saveAs($path . $fileName)) {
                    if ($oldFlag && file_exists($path . $oldFlag)) {
                        unlink($path . $oldFlag);
                    }
                    $model->flag = $fileName;
                } else {
                    $model->flag = $oldFlag;
                }
            } else {
                $model->flag = $oldFlag;
            }
            
            if ($model->save()) {
                CityCountries::deleteAll(['city_id' => $id]);
                $newCountries = Yii::$app->request->post('countries', []);
                $x = Yii::$app->request->post('x', 50);
                $y = Yii::$app->request->post('y', 50);
                
                foreach ($newCountries as $countryId) {
                    $cc = new CityCountries();
                    $cc->city_id = $id;
                    $cc->country_id = $countryId;
                    $cc->x = $x;
                    $cc->y = $y;
                    $cc->save();
                }
                
                Yii::$app->session->setFlash('success', 'Город обновлен');
                return $this->redirect(['cities']);
            } else {
                $model->flag = $oldFlag;
            }
        }
        
        $countries = Countries::find()->orderBy(['name' => SORT_ASC])->all();
        
        return $this->render('update-city', [
            'model' => $model,
            'countries' => $countries,
            'selectedCountries' => $selectedCountries,
            'currentX' => $currentX,
            'currentY' => $currentY,
        ]);
    }

    public function actionDeleteCity()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        
        $id = Yii::$app->request->post('id');
        $city = Cities::findOne($id);
        
        if (!$city) {
            return ['success' => false, 'message' => 'Город не найден'];
        }
        
        if ($city->flag) {
            $path = Yii::getAlias('@webroot/flags_imgs/' . $city->flag);
            if (file_exists($path)) {
                unlink($path);
            }
        }
        
        CityCountries::deleteAll(['city_id' => $id]);
        
        if ($city->delete()) {
            return ['success' => true, 'message' => 'Город удален'];
        }
        
        return ['success' => false, 'message' => 'Ошибка при удалении'];
    }
}