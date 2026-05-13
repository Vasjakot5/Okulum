<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use yii\web\UploadedFile;
use app\models\Applications;
use app\models\ApplicationForm;
use app\models\EventForm;
use app\models\OpeningForm;
use app\models\PopularHumanForm;
use app\models\VehicleForm;
use app\models\MonumentForm;
use app\models\WeaponForm;
use app\models\ClotheForm;
use app\models\Events;
use app\models\Openings;
use app\models\PopularHumans;
use app\models\Vehicles;
use app\models\Monuments;
use app\models\Weapons;
use app\models\Clothes;
use app\components\ModerationService;

class ApplicationController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['help', 'my-applications', 'view', 'create-article', 'delete-article', 'update-article', 'update-application'],
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['help', 'my-applications', 'view', 'create-article', 'delete-article', 'update-article', 'update-application'],
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }
    

    private function checkBan()
    {
        $user = Yii::$app->user->identity;
        if ($user && $user->isBanned()) {
            Yii::$app->session->setFlash('danger', '' . $user->ban_reason . ' Доступ к этой функции ограничен.');
            return true;
        }
        return false;
    }
    
    public function actionHelp()
    {
        $model = new ApplicationForm();
        
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Ваша заявка отправлена! Ожидайте ответа администратора.');
            return $this->redirect(['application/my-applications']);
        }
        
        return $this->render('help', [
            'model' => $model,
        ]);
    }
    
    public function actionMyApplications()
    {
        $user = Yii::$app->user->identity;
        $isAdmin = ($user->role == 1);
        
        if ($isAdmin) {
            $applications = Applications::find()
                ->where(['<>', 'user_id', Yii::$app->user->id])
                ->orderBy(['id' => SORT_DESC])
                ->all();
        } else {
            $applications = Applications::find()
                ->where(['user_id' => Yii::$app->user->id])
                ->orderBy(['id' => SORT_DESC])
                ->all();
        }
        
        $userId = Yii::$app->user->id;
        
        if ($isAdmin) {
            $events = Events::find()
                ->where(['<>', 'user_id', $userId])
                ->orderBy(['created_at' => SORT_DESC])
                ->all();
            
            $openings = Openings::find()
                ->where(['<>', 'user_id', $userId])
                ->orderBy(['created_at' => SORT_DESC])
                ->all();
            
            $humans = PopularHumans::find()
                ->where(['<>', 'user_id', $userId])
                ->orderBy(['created_at' => SORT_DESC])
                ->all();
            
            $vehicles = Vehicles::find()
                ->where(['<>', 'user_id', $userId])
                ->orderBy(['created_at' => SORT_DESC])
                ->all();
            
            $monuments = Monuments::find()
                ->where(['<>', 'user_id', $userId])
                ->orderBy(['created_at' => SORT_DESC])
                ->all();
            
            $weapons = Weapons::find()
                ->where(['<>', 'user_id', $userId])
                ->orderBy(['created_at' => SORT_DESC])
                ->all();
            
            $clothes = Clothes::find()
                ->where(['<>', 'user_id', $userId])
                ->orderBy(['created_at' => SORT_DESC])
                ->all();
        } else {
            $events = Events::find()
                ->where(['user_id' => $userId])
                ->orderBy(['created_at' => SORT_DESC])
                ->all();
            
            $openings = Openings::find()
                ->where(['user_id' => $userId])
                ->orderBy(['created_at' => SORT_DESC])
                ->all();
            
            $humans = PopularHumans::find()
                ->where(['user_id' => $userId])
                ->orderBy(['created_at' => SORT_DESC])
                ->all();
            
            $vehicles = Vehicles::find()
                ->where(['user_id' => $userId])
                ->orderBy(['created_at' => SORT_DESC])
                ->all();
            
            $monuments = Monuments::find()
                ->where(['user_id' => $userId])
                ->orderBy(['created_at' => SORT_DESC])
                ->all();
            
            $weapons = Weapons::find()
                ->where(['user_id' => $userId])
                ->orderBy(['created_at' => SORT_DESC])
                ->all();
            
            $clothes = Clothes::find()
                ->where(['user_id' => $userId])
                ->orderBy(['created_at' => SORT_DESC])
                ->all();
        }
        
        $articles = array_merge($events, $openings, $humans, $vehicles, $monuments, $weapons, $clothes);
        usort($articles, function($a, $b) {
            $timeA = $a->created_at ?? $a->id;
            $timeB = $b->created_at ?? $b->id;
            return strtotime($timeB) - strtotime($timeA);
        });
        
        $isBanned = $user->isBanned();
        
        return $this->render('my-applications', [
            'applications' => $applications,
            'articles' => $articles,
            'isBanned' => $isBanned,
            'user' => $user,
        ]);
    }
    
    public function actionView($id)
    {
        $application = Applications::findOne($id);
        
        if (!$application) {
            throw new NotFoundHttpException('Заявка не найдена');
        }
        
        $user = Yii::$app->user->identity;
        $isAdmin = ($user->role == 1);
        
        if (!$isAdmin && $application->user_id != Yii::$app->user->id) {
            throw new NotFoundHttpException('Заявка не найдена');
        }
        
        return $this->render('view', [
            'application' => $application,
        ]);
    }
    
    public function actionUpdateApplication($id)
    {
        $application = Applications::findOne($id);
        
        if (!$application || $application->user_id != Yii::$app->user->id) {
            throw new NotFoundHttpException('Заявка не найдена');
        }
        
        $model = new ApplicationForm();
        $model->name = $application->name;
        $model->type = $application->type;
        $model->descr = $application->descr;
        
        if ($model->load(Yii::$app->request->post()) && $model->update($id)) {
            Yii::$app->session->setFlash('success', 'Заявка обновлена');
            return $this->redirect(['my-applications']);
        }
        
        return $this->render('update-application', [
            'model' => $model,
            'application' => $application,
        ]);
    }
    
    public function actionUpdateArticle($type, $id)
    {
        if ($this->checkBan()) {
            return $this->redirect(['auth/profile']);
        }
        
        $userId = Yii::$app->user->id;
        $article = null;
        $form = null;
        
        switch ($type) {
            case 'event':
                $article = Events::findOne(['id' => $id, 'user_id' => $userId]);
                $form = new EventForm();
                if ($article) {
                    $form->name = $article->name;
                    $form->date = $article->date;
                    $form->descr = $article->descr;
                    $form->countries_id = $article->countries_id;
                    $form->cities_id = $article->cities_id;
                }
                break;
            case 'opening':
                $article = Openings::findOne(['id' => $id, 'user_id' => $userId]);
                $form = new OpeningForm();
                if ($article) {
                    $form->name = $article->name;
                    $form->date = $article->date;
                    $form->descr = $article->descr;
                    $form->countries_id = $article->countries_id;
                    $form->cities_id = $article->cities_id;
                }
                break;
            case 'human':
                $article = PopularHumans::findOne(['id' => $id, 'user_id' => $userId]);
                $form = new PopularHumanForm();
                if ($article) {
                    $form->name = $article->name;
                    $form->last_name = $article->last_name;
                    $form->patronymic = $article->patronymic;
                    $form->type = $article->type;
                    $form->descr = $article->descr;
                    $form->quote = $article->quote;
                    $form->date_born = $article->date_born;
                    $form->date_death = $article->date_death;
                    $form->countries_id = $article->countries_id;
                    $form->cities_id = $article->cities_id;
                }
                break;
            case 'vehicle':
                $article = Vehicles::findOne(['id' => $id, 'user_id' => $userId]);
                $form = new VehicleForm();
                if ($article) {
                    $form->name = $article->name;
                    $form->descr = $article->descr;
                    $form->type = $article->type;
                    $form->status = $article->status;
                    $form->countries_id = $article->countries_id;
                    $form->cities_id = $article->cities_id;
                }
                break;
            case 'monument':
                $article = Monuments::findOne(['id' => $id, 'user_id' => $userId]);
                $form = new MonumentForm();
                if ($article) {
                    $form->name = $article->name;
                    $form->status = $article->status;
                    $form->descr = $article->descr;
                    $form->countries_id = $article->countries_id;
                    $form->cities_id = $article->cities_id;
                }
                break;
            case 'weapon':
                $article = Weapons::findOne(['id' => $id, 'user_id' => $userId]);
                $form = new WeaponForm();
                if ($article) {
                    $form->name = $article->name;
                    $form->status = $article->status;
                    $form->descr = $article->descr;
                    $form->countries_id = $article->countries_id;
                    $form->cities_id = $article->cities_id;
                }
                break;
            case 'clothe':
                $article = Clothes::findOne(['id' => $id, 'user_id' => $userId]);
                $form = new ClotheForm();
                if ($article) {
                    $form->name = $article->name;
                    $form->status = $article->status;
                    $form->descr = $article->descr;
                    $form->countries_id = $article->countries_id;
                    $form->cities_id = $article->cities_id;
                }
                break;
            default:
                throw new NotFoundHttpException('Неверный тип');
        }
        
        if (!$article) {
            throw new NotFoundHttpException('Статья не найдена');
        }
        
        if ($form->load(Yii::$app->request->post())) {
            $form->setImageFile(UploadedFile::getInstance($form, 'img'));
        
            switch ($type) {
                case 'event':
                    $article->name = $form->name;
                    $article->date = $form->date;
                    $article->descr = $form->descr;
                    $article->countries_id = $form->countries_id;
                    $article->cities_id = $form->cities_id;
                    if ($form->getImageFile()) {
                        $oldImg = $article->img;
                        $fileName = time() . '_' . Yii::$app->user->id . '.' . $form->getImageFile()->extension;
                        $path = Yii::getAlias('@webroot/events_imgs/');
                        if ($form->getImageFile()->saveAs($path . $fileName)) {
                            if ($oldImg && file_exists($path . $oldImg)) unlink($path . $oldImg);
                            $article->img = $fileName;
                        }
                    }
                    break;
                case 'opening':
                    $article->name = $form->name;
                    $article->date = $form->date;
                    $article->descr = $form->descr;
                    $article->countries_id = $form->countries_id;
                    $article->cities_id = $form->cities_id;
                    if ($form->getImageFile()) {
                        $oldImg = $article->img;
                        $fileName = time() . '_' . Yii::$app->user->id . '.' . $form->getImageFile()->extension;
                        $path = Yii::getAlias('@webroot/openings_imgs/');
                        if ($form->getImageFile()->saveAs($path . $fileName)) {
                            if ($oldImg && file_exists($path . $oldImg)) unlink($path . $oldImg);
                            $article->img = $fileName;
                        }
                    }
                    break;
                case 'human':
                    $article->name = $form->name;
                    $article->last_name = $form->last_name;
                    $article->patronymic = $form->patronymic;
                    $article->type = $form->type;
                    $article->descr = $form->descr;
                    $article->quote = $form->quote;
                    $article->date_born = $form->date_born;
                    $article->date_death = $form->date_death;
                    $article->countries_id = $form->countries_id;
                    $article->cities_id = $form->cities_id;
                    if ($form->getImageFile()) {
                        $oldImg = $article->img;
                        $fileName = time() . '_' . Yii::$app->user->id . '.' . $form->getImageFile()->extension;
                        $path = Yii::getAlias('@webroot/popular_humans_imgs/');
                        if ($form->getImageFile()->saveAs($path . $fileName)) {
                            if ($oldImg && file_exists($path . $oldImg)) unlink($path . $oldImg);
                            $article->img = $fileName;
                        }
                    }
                    break;
                case 'vehicle':
                    $article->name = $form->name;
                    $article->descr = $form->descr;
                    $article->type = $form->type;
                    $article->status = $form->status;
                    $article->countries_id = $form->countries_id;
                    $article->cities_id = $form->cities_id;
                    if ($form->getImageFile()) {
                        $oldImg = $article->img;
                        $fileName = time() . '_' . Yii::$app->user->id . '.' . $form->getImageFile()->extension;
                        $path = Yii::getAlias('@webroot/vehicles_imgs/');
                        if ($form->getImageFile()->saveAs($path . $fileName)) {
                            if ($oldImg && file_exists($path . $oldImg)) unlink($path . $oldImg);
                            $article->img = $fileName;
                        }
                    }
                    break;
                case 'monument':
                    $article->name = $form->name;
                    $article->status = $form->status;
                    $article->descr = $form->descr;
                    $article->countries_id = $form->countries_id;
                    $article->cities_id = $form->cities_id;
                    if ($form->getImageFile()) {
                        $oldImg = $article->img;
                        $fileName = time() . '_' . Yii::$app->user->id . '.' . $form->getImageFile()->extension;
                        $path = Yii::getAlias('@webroot/monument_imgs/');
                        if ($form->getImageFile()->saveAs($path . $fileName)) {
                            if ($oldImg && file_exists($path . $oldImg)) unlink($path . $oldImg);
                            $article->img = $fileName;
                        }
                    }
                    break;
                case 'weapon':
                    $article->name = $form->name;
                    $article->status = $form->status;
                    $article->descr = $form->descr;
                    $article->countries_id = $form->countries_id;
                    $article->cities_id = $form->cities_id;
                    if ($form->getImageFile()) {
                        $oldImg = $article->img;
                        $fileName = time() . '_' . Yii::$app->user->id . '.' . $form->getImageFile()->extension;
                        $path = Yii::getAlias('@webroot/weapon_imgs/');
                        if ($form->getImageFile()->saveAs($path . $fileName)) {
                            if ($oldImg && file_exists($path . $oldImg)) unlink($path . $oldImg);
                            $article->img = $fileName;
                        }
                    }
                    break;
                case 'clothe':
                    $article->name = $form->name;
                    $article->status = $form->status;
                    $article->descr = $form->descr;
                    $article->countries_id = $form->countries_id;
                    $article->cities_id = $form->cities_id;
                    if ($form->getImageFile()) {
                        $oldImg = $article->img;
                        $fileName = time() . '_' . Yii::$app->user->id . '.' . $form->getImageFile()->extension;
                        $path = Yii::getAlias('@webroot/clothes_imgs/');
                        if ($form->getImageFile()->saveAs($path . $fileName)) {
                            if ($oldImg && file_exists($path . $oldImg)) unlink($path . $oldImg);
                            $article->img = $fileName;
                        }
                    }
                    break;
            }
            
            $article->updated_at = date('Y-m-d H:i:s');
            
            if ($article->save()) {
                Yii::$app->session->setFlash('success', 'Статья обновлена');
                return $this->redirect(['my-applications']);
            }
        }
        
        return $this->render('update-article', [
            'form' => $form,
            'article' => $article,
            'type' => $type,
        ]);
    }
    
    public function actionCreateArticle()
    {
        if ($this->checkBan()) {
            return $this->redirect(['auth/profile']);
        }
        
        $eventForm = new EventForm();
        $openingForm = new OpeningForm();
        $personForm = new PopularHumanForm();
        $vehicleForm = new VehicleForm();
        $monumentForm = new MonumentForm();
        $weaponForm = new WeaponForm();
        $clotheForm = new ClotheForm();
        
        $type = Yii::$app->request->get('type', 'event');
        $forms = [
            'event' => $eventForm,
            'opening' => $openingForm,
            'person' => $personForm,
            'vehicle' => $vehicleForm,
            'monument' => $monumentForm,
            'weapon' => $weaponForm,
            'clothe' => $clotheForm,
        ];
        
        $model = $forms[$type] ?? $eventForm;
        
        if ($model->load(Yii::$app->request->post())) {
            $model->setImageFile(UploadedFile::getInstance($model, 'img'));
            if ($model->save()) {
                if (Yii::$app->user->identity->role == 1) {
                    Yii::$app->session->setFlash('success', 'Статья успешно опубликована!');
                } else {
                    Yii::$app->session->setFlash('success', 'Статья отправлена на модерацию!');
                }
                return $this->redirect(['application/my-applications']);
            }
        }
        
        return $this->render('create-article', [
            'eventForm' => $eventForm,
            'openingForm' => $openingForm,
            'personForm' => $personForm,
            'vehicleForm' => $vehicleForm,
            'monumentForm' => $monumentForm,
            'weaponForm' => $weaponForm,
            'clotheForm' => $clotheForm,
            'activeType' => $type,
        ]);
    }
    
    public function actionDeleteArticle($type, $id)
    {
        if ($this->checkBan()) {
            return $this->redirect(['auth/profile']);
        }
        
        $userId = Yii::$app->user->id;
        
        switch ($type) {
            case 'event':
                $article = Events::findOne(['id' => $id, 'user_id' => $userId]);
                break;
            case 'opening':
                $article = Openings::findOne(['id' => $id, 'user_id' => $userId]);
                break;
            case 'human':
                $article = PopularHumans::findOne(['id' => $id, 'user_id' => $userId]);
                break;
            case 'vehicle':
                $article = Vehicles::findOne(['id' => $id, 'user_id' => $userId]);
                break;
            case 'monument':
                $article = Monuments::findOne(['id' => $id, 'user_id' => $userId]);
                break;
            case 'weapon':
                $article = Weapons::findOne(['id' => $id, 'user_id' => $userId]);
                break;
            case 'clothe':
                $article = Clothes::findOne(['id' => $id, 'user_id' => $userId]);
                break;
            default:
                throw new NotFoundHttpException('Неверный тип');
        }
        
        if (!$article) {
            throw new NotFoundHttpException('Статья не найдена');
        }
        
        if ($article->img) {
            $path = $this->getImagePath($type) . $article->img;
            if (file_exists($path)) {
                unlink($path);
            }
        }
        
        if ($article->delete()) {
            Yii::$app->session->setFlash('success', 'Статья удалена');
        } else {
            Yii::$app->session->setFlash('error', 'Ошибка при удалении');
        }
        
        return $this->redirect(['my-applications']);
    }
    
    private function getImagePath($type)
    {
        $paths = [
            'event' => Yii::getAlias('@webroot/events_imgs/'),
            'opening' => Yii::getAlias('@webroot/openings_imgs/'),
            'human' => Yii::getAlias('@webroot/popular_humans_imgs/'),
            'vehicle' => Yii::getAlias('@webroot/vehicles_imgs/'),
            'monument' => Yii::getAlias('@webroot/monument_imgs/'),
            'weapon' => Yii::getAlias('@webroot/weapon_imgs/'),
            'clothe' => Yii::getAlias('@webroot/clothes_imgs/'),
        ];
        
        return $paths[$type] ?? Yii::getAlias('@webroot/');
    }
    
    public function actionDeleteApplication($id)
    {
        $application = Applications::findOne($id);
        
        if (!$application || $application->user_id != Yii::$app->user->id) {
            throw new NotFoundHttpException('Заявка не найдена');
        }
        
        if ($application->delete()) {
            Yii::$app->session->setFlash('success', 'Заявка удалена');
        } else {
            Yii::$app->session->setFlash('error', 'Ошибка при удалении');
        }
        
        return $this->redirect(['my-applications']);
    }

    public function actionFilterArticles($filter = 'all')
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        
        $userId = Yii::$app->user->id;
        $isAdmin = (Yii::$app->user->identity->role == 1);
        
        if ($isAdmin) {
            $events = Events::find()->with(['user'])->all();
            $openings = Openings::find()->with(['user'])->all();
            $humans = PopularHumans::find()->with(['user'])->all();
            $vehicles = Vehicles::find()->with(['user'])->all();
            $monuments = Monuments::find()->with(['user'])->all();
            $weapons = Weapons::find()->with(['user'])->all();
            $clothes = Clothes::find()->with(['user'])->all();
        } else {
            $events = Events::find()->where(['user_id' => $userId])->all();
            $openings = Openings::find()->where(['user_id' => $userId])->all();
            $humans = PopularHumans::find()->where(['user_id' => $userId])->all();
            $vehicles = Vehicles::find()->where(['user_id' => $userId])->all();
            $monuments = Monuments::find()->where(['user_id' => $userId])->all();
            $weapons = Weapons::find()->where(['user_id' => $userId])->all();
            $clothes = Clothes::find()->where(['user_id' => $userId])->all();
        }
        
        $articles = array_merge($events, $openings, $humans, $vehicles, $monuments, $weapons, $clothes);
        usort($articles, function($a, $b) {
            return strtotime($b->created_at) - strtotime($a->created_at);
        });
        
        $total = count($articles);
        $pending = count(array_filter($articles, function($a) { return $a->moderation_status == 0; }));
        $approved = count(array_filter($articles, function($a) { return $a->moderation_status == 1; }));
        
        $html = $this->renderPartial('_articles_table', [
            'articles' => $articles,
            'isAdmin' => $isAdmin,
            'isBanned' => Yii::$app->user->identity->isBanned(),
            'filterStatus' => $filter
        ]);
        
        return [
            'success' => true,
            'html' => $html,
            'total' => $total,
            'pending' => $pending,
            'approved' => $approved
        ];
    }

    public function actionFilterApplications($filter = 'all')
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        
        $userId = Yii::$app->user->id;
        $isAdmin = (Yii::$app->user->identity->role == 1);
        
        if ($isAdmin) {
            $applications = Applications::find()->with(['user'])->orderBy(['created_at' => SORT_DESC])->all();
        } else {
            $applications = Applications::find()->where(['user_id' => $userId])->orderBy(['created_at' => SORT_DESC])->all();
        }
        
        $total = count($applications);
        $pending = count(array_filter($applications, function($a) { return $a->status == 0; }));
        $closed = count(array_filter($applications, function($a) { return $a->status == 1; }));
        
        $html = $this->renderPartial('_applications_table', [
            'applications' => $applications,
            'isAdmin' => $isAdmin,
            'isBanned' => Yii::$app->user->identity->isBanned(),
            'filterStatus' => $filter
        ]);
        
        return [
            'success' => true,
            'html' => $html,
            'total' => $total,
            'pending' => $pending,
            'closed' => $closed
        ];
    }
}
