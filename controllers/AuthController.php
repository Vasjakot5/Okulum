<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\web\UploadedFile;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\RegisterForm;
use app\models\User;

class AuthController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['logout', 'profile', 'update-profile', 'change-password'],
                'rules' => [
                    [
                        'actions' => ['logout', 'profile', 'update-profile', 'change-password'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }
    
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();
        return $this->goHome();
    }

    public function actionRegister()
    {
        $model = new RegisterForm();
        
        if ($model->load(Yii::$app->request->post())) {
            $model->avatar_file = UploadedFile::getInstance($model, 'avatar_file');
            
            if ($model->register()) {
                Yii::$app->session->setFlash('success', 'Регистрация прошла успешно!');
                return $this->redirect(['login']);
            }
        }

        return $this->render('register', [
            'model' => $model,
        ]);
    }
    
    public function actionProfile()
    {
        $user = Yii::$app->user->identity;
        
        return $this->render('profile', [
            'user' => $user,
        ]);
    }
    
    public function actionUpdateProfile()
    {
        $user = Yii::$app->user->identity;
        
        if (Yii::$app->request->isPost) {
            $user->load(Yii::$app->request->post());
            
            $avatar = UploadedFile::getInstance($user, 'avatar_file');
            if ($avatar && $avatar->tempName) {
                $oldAvatar = $user->photo;
                $fileName = time() . '_' . Yii::$app->security->generateRandomString(10) . '.' . $avatar->extension;
                $path = Yii::getAlias('@webroot/avatars/');
                
                if (!file_exists($path)) {
                    mkdir($path, 0755, true);
                }
                
                if ($avatar->saveAs($path . $fileName)) {
                    $user->photo = $fileName;
                    
                    if ($oldAvatar && file_exists($path . $oldAvatar)) {
                        unlink($path . $oldAvatar);
                    }
                }
            }
            
            if ($user->save()) {
                Yii::$app->session->setFlash('success', 'Профиль обновлен');
                return $this->redirect(['profile']);
            }
        }
        
        return $this->render('update-profile', [
            'user' => $user,
        ]);
    }
    
    public function actionChangePassword()
    {
        $user = Yii::$app->user->identity;
        $model = new \app\models\ChangePasswordForm($user);
        
        if ($model->load(Yii::$app->request->post()) && $model->changePassword()) {
            Yii::$app->session->setFlash('success', 'Пароль успешно изменен');
            return $this->redirect(['profile']);
        }
        
        return $this->render('change-password', [
            'model' => $model,
        ]);
    }
}