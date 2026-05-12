<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\filters\AccessControl;
use yii\helpers\Html;
use app\models\Discussions;
use app\models\Comments;
use app\models\User;
use app\components\ModerationService;

class DiscussionController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['create', 'view', 'my', 'send', 'delete-message', 'edit-message', 'delete-discussion'],
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['create', 'view', 'my', 'send', 'delete-message', 'edit-message', 'delete-discussion'],
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    public function beforeAction($action)
    {
        if (in_array($action->id, ['send', 'delete-message', 'edit-message', 'delete-discussion'])) {
            $this->enableCsrfValidation = false;
        }
        return parent::beforeAction($action);
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
    
    private function checkBanAjax()
    {
        $banCheck = ModerationService::checkBan(Yii::$app->user->id);
        if ($banCheck['is_banned']) {
            return [
                'success' => false, 
                'message' => $banCheck['message'],
                'is_banned' => true
            ];
        }
        return null;
    }

    public function actionIndex()
    {
        $discussions = Discussions::find()
            ->orderBy(['updated_at' => SORT_DESC])
            ->all();
        
        return $this->render('index', [
            'discussions' => $discussions,
        ]);
    }

    public function actionCreate()
    {
        if ($this->checkBan()) {
            return $this->redirect(['index']);
        }
        
        $model = new Discussions();
        $user = Yii::$app->user->identity;
        $isAdmin = ($user->role == 1);
        
        if ($model->load(Yii::$app->request->post())) {
            $violations = ModerationService::checkText($model->title . ' ' . $model->content);
            if (!empty($violations)) {
                $user->addViolation(null, 'insult', 'Запрещенные слова: ' . implode(', ', $violations));
                Yii::$app->session->setFlash('danger', 'Ваше сообщение содержит запрещенные выражения. Нарушение зафиксировано.');
                return $this->redirect(['create']);
            }
            
            $model->user_id = $user->id;
            
            if ($isAdmin) {
                $model->is_admin_only = Yii::$app->request->post('is_admin_only', 0);
            } else {
                $model->is_admin_only = 0;
            }
            
            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Обсуждение создано!');
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }
        
        return $this->render('create', [
            'model' => $model,
            'isAdmin' => $isAdmin,
        ]);
    }

    public function actionView($id)
    {
        $discussion = Discussions::findOne($id);
        if (!$discussion) {
            throw new NotFoundHttpException('Обсуждение не найдено');
        }
        
        $comments = Comments::find()
            ->where(['discussions_id' => $id, 'parent_id' => null])
            ->with(['user'])
            ->orderBy(['created_at' => SORT_ASC])
            ->all();
        
        return $this->render('view', [
            'discussion' => $discussion,
            'comments' => $comments,
        ]);
    }

    public function actionMy()
    {
        $discussions = Discussions::find()
            ->where(['user_id' => Yii::$app->user->id])
            ->orderBy(['updated_at' => SORT_DESC])
            ->all();
        
        return $this->render('my', [
            'discussions' => $discussions,
        ]);
    }

    public function actionSend()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $banResult = $this->checkBanAjax();
        if ($banResult !== null) {
            return $banResult;
        }

        $content = Yii::$app->request->post('content');
        $discussionId = Yii::$app->request->post('discussion_id');
        $parentId = Yii::$app->request->post('parent_id', null);
        
        if (empty($content)) {
            return ['success' => false, 'message' => 'Введите сообщение'];
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
        $comment->discussions_id = $discussionId;
        $comment->parent_id = !empty($parentId) ? $parentId : null;
        $comment->created_at = date('Y-m-d H:i:s');
        
        if ($comment->save()) {
            $discussion = Discussions::findOne($discussionId);
            if ($discussion) {
                $discussion->updated_at = date('Y-m-d H:i:s');
                $discussion->save(false);
            }
            
            $user = Yii::$app->user->identity;
            
            return [
                'success' => true,
                'comment' => [
                    'id' => $comment->id,
                    'content' => nl2br(Html::encode($comment->content)),
                    'user_name' => Html::encode($user->name . ' ' . $user->last_name),
                    'user_id' => $user->id,
                    'created_at' => Yii::$app->formatter->asRelativeTime($comment->created_at),
                ]
            ];
        }
        
        return ['success' => false, 'message' => 'Ошибка при сохранении'];
    }

    public function actionEditMessage()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $id = Yii::$app->request->post('id');
        $content = Yii::$app->request->post('content');
        
        $foundWords = ModerationService::checkText($content);
        if (!empty($foundWords)) {
            return [
                'success' => false, 
                'message' => 'Редактирование отклонено. Сообщение содержит запрещенные выражения: ' . implode(', ', $foundWords)
            ];
        }
        
        $comment = Comments::findOne($id);
        if (!$comment) {
            return ['success' => false, 'message' => 'Сообщение не найдено'];
        }
        
        if ($comment->user_id != Yii::$app->user->id) {
            return ['success' => false, 'message' => 'Нет прав'];
        }
        
        $comment->content = $content;
        $comment->updated_at = date('Y-m-d H:i:s');
        
        if ($comment->save()) {
            return ['success' => true, 'content' => nl2br(Html::encode($content))];
        }
        
        return ['success' => false, 'message' => 'Ошибка'];
    }

    public function actionDeleteMessage($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $comment = Comments::findOne($id);
        
        if (!$comment) {
            return ['success' => false, 'message' => 'Сообщение не найдено'];
        }
        
        if ($comment->user_id != Yii::$app->user->id) {
            return ['success' => false, 'message' => 'Нет прав'];
        }
        
        Comments::deleteAll(['parent_id' => $id]);
        
        if ($comment->delete()) {
            return ['success' => true];
        }
        
        return ['success' => false, 'message' => 'Ошибка'];
    }

    public function actionGetMessages($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $comments = Comments::find()
            ->where(['discussions_id' => $id, 'parent_id' => null])
            ->with(['user'])
            ->orderBy(['created_at' => SORT_ASC])
            ->all();
        
        $messages = [];
        foreach ($comments as $comment) {
            $replies = Comments::find()
                ->where(['parent_id' => $comment->id])
                ->with(['user'])
                ->orderBy(['created_at' => SORT_ASC])
                ->all();
            
            $repliesData = [];
            foreach ($replies as $reply) {
                $repliesData[] = [
                    'id' => $reply->id,
                    'content' => nl2br(Html::encode($reply->content)),
                    'user_name' => Html::encode($reply->user->name . ' ' . $reply->user->last_name),
                    'user_id' => $reply->user_id,
                    'created_at' => Yii::$app->formatter->asRelativeTime($reply->created_at),
                ];
            }
            
            $messages[] = [
                'id' => $comment->id,
                'content' => nl2br(Html::encode($comment->content)),
                'user_name' => Html::encode($comment->user->name . ' ' . $comment->user->last_name),
                'user_id' => $comment->user_id,
                'created_at' => Yii::$app->formatter->asRelativeTime($comment->created_at),
                'replies' => $repliesData,
            ];
        }
        
        return ['success' => true, 'messages' => $messages];
    }

    public function actionDeleteDiscussion()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $id = Yii::$app->request->post('id');
        $discussion = Discussions::findOne($id);
        
        if (!$discussion) {
            return ['success' => false, 'message' => 'Обсуждение не найдено'];
        }
        
        if ($discussion->user_id != Yii::$app->user->id) {
            return ['success' => false, 'message' => 'Нет прав для удаления'];
        }
        
        Comments::deleteAll(['discussions_id' => $id]);
        
        if ($discussion->delete()) {
            return ['success' => true];
        }
        
        return ['success' => false, 'message' => 'Ошибка при удалении'];
    }
}