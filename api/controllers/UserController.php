<?php
namespace api\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use api\models\User;
use api\models\Content;
use api\models\BlackList;
use api\models\UserSubscription;
use api\models\ContentComment;
use common\models\forms\ChangePasswordForm;
use common\models\forms\ChangeProfileForm;
use common\models\forms\ChangeProfileAlertsForm;
use common\models\forms\ImageBase64Form;
use common\models\forms\UploadForm;
use yii\web\UploadedFile;
use yii\data\ActiveDataProvider;

class UserController extends BaseAuthController {

    public function behaviors() {
        $behaviors = parent::behaviors();
        $behaviors['access'] = [
            'class' => AccessControl::className(),
            'rules' => [
                [
                    'actions' => ['view'],
                    'allow' => true,
                ],
                [
                    'actions' => ['profile', 'content', 'avatar', 'delete-avatar', 'change-password', 'change-profile', 'change-alerts', 'black-list', 'followers', 'followings', 'delete', 'all-comments', 'my-comments', 'to-me-comments', 'to-my-contents', 'to-my-tours', 'tour', 'my-events'],
                    'allow' => true,
                    'roles' => ['@'],
                ]
            ],
        ];
        $behaviors['verbs'] = [
            'class' => VerbFilter::className(),
            'actions' => [
                'profile' => ['GET'],
                'view' => ['GET'],
                'content' => ['GET'],
                'tour' => ['GET'],
                'my-events' => ['GET'],
                'black-list' => ['GET'],
                'followers' => ['GET'],
                'followings' => ['GET'],
                'all-comments' => ['GET'],
                'my-comments' => ['GET'],
                'to-me-comments' => ['GET'],
                'to-my-contents' => ['GET'],
                'to-my-tours' => ['GET'],
                'avatar' => ['POST'],
                'delete-avatar' => ['POST'],
                'change-password' => ['POST'],
                'change-profile' => ['POST'],
                'change-alerts' => ['POST'],
                'delete' => ['DELETE']
            ],
        ];

        return $behaviors;
    }

    public function actionProfile() {
        return User::findOne(Yii::$app->user->id);
    }

    public function actionAvatar()
    {
        $image = null;
        $model = new UploadForm();
        $model->file = UploadedFile::getInstanceByName('file');

        if ($model->file && $model->validate()) {
            $image = $model->saveImage('/upload/users/');
        }

        if(!$image) {
            return $model;
        } else {
            $this->deleteAvatar();
            $identity = Yii::$app->user->identity;
            $identity->avatar_base_url = $image['base_url'];
            $identity->avatar_url = $image['file_name'];
            if($identity->save()) {
                return User::findOne(Yii::$app->user->id);
            }
        }
    }

    public function actionDeleteAvatar($userpic)
    {
        $this->deleteAvatar();
        Yii::$app->user->identity->avatar_base_url = null;
        Yii::$app->user->identity->avatar_url = null;
        Yii::$app->user->identity->save();
        return User::findOne(Yii::$app->user->id);
    }

    private function deleteAvatar(){
        $identity = Yii::$app->user->identity;
        $filepath = implode('', [$_SERVER['DOCUMENT_ROOT'], $identity->avatar_base_url, $identity->avatar_url]);
        if (file_exists($filepath)) {
            unlink($filepath);
        }
    }

    public function actionChangePassword(){
        $bodyParams = Yii::$app->getRequest()->getBodyParams();
        $model = new ChangePasswordForm();
        $model->load($bodyParams, '');
        if ($model->validate()) {
            return $model->changePassword();
        } else {
            return $model;
        }
    }

    public function actionChangeProfile(){
        $bodyParams = Yii::$app->getRequest()->getBodyParams();
        $model = new ChangeProfileForm();
        $model->load($bodyParams, '');
        if ($model->validate()) {
            return $model->changeProfile();
        } else {
            return $model;
        }
    }

    public function actionChangeAlerts(){
        $bodyParams = Yii::$app->getRequest()->getBodyParams();
        $model = new ChangeProfileAlertsForm();
        $model->load($bodyParams, '');
        if ($model->validate()) {
            return $model->changeAlerts();
        } else {
            return $model;
        }
    }

    public function actionBlackList(){
        return BlackList::find()->where(['user_id' => Yii::$app->user->id])->orderBy(['created_at' => SORT_DESC])->select(['id', 'block_id', 'created_at', 'updated_at'])->all();
    }

    public function actionFollowers(){
        return UserSubscription::find()->where(['follower_id' => Yii::$app->user->id])->orderBy(['created_at' => SORT_DESC])->select(['id', 'user_id', 'created_at', 'updated_at'])->all();
    }

    public function actionFollowings(){
        return UserSubscription::find()->where(['user_id' => Yii::$app->user->id])->orderBy(['created_at' => SORT_DESC])->select(['id', 'follower_id', 'created_at', 'updated_at'])->all();
    }

    public function actionDelete(){
        $user = User::findOne(Yii::$app->user->id);
        return $user->delete();
    }

    public function actionView($id){
        $user = User::find()->where(['username' => $id])->one();
        return $user;
    }

    public function actionMyEvents($page = 1, $perpage = 10){
        $where = ['user_id' => Yii::$app->user->id, 'is_tour' => 0];
        if(Yii::$app->user->can('moderator')) $where['is_tour'] = 1;        
        $activeData = new ActiveDataProvider([
            'query' => Content::find(),
            'pagination' => [
                'defaultPageSize' => $perpage,
                'validatePage' => false
            ],
        ]);
        $activeData->query->where($where)->orderBy(['created_at' => SORT_DESC]);
        return ['total' => $activeData->getTotalCount(), 'models' => $activeData->getModels()];
    }    

    public function actionContent(){
        $where = ['user_id' => Yii::$app->user->id, 'is_tour' => 0];
        //if(Yii::$app->user->can('moderator')) $where['is_tour'] = 1;
        return Content::find()->where($where)->orderBy(['created_at' => SORT_DESC])->all();
    }

    public function actionTour(){
        $where = ['user_id' => Yii::$app->user->id, 'is_tour' => 1];
        //if(!Yii::$app->user->can('moderator')) $where['is_tour'] = 0;
        return Content::find()->where($where)->orderBy(['created_at' => SORT_DESC])->all();
    }

    public function actionAllComments(){
        return ContentComment::find()
                ->joinWith('content', true)
                ->orFilterWhere(['content_comment.user_id' => Yii::$app->user->id])
                ->orFilterWhere(['content.user_id' => Yii::$app->user->id])
                ->orderBy(['created_at' => SORT_DESC])
                ->all();
    }

    public function actionMyComments(){
        return ContentComment::find()
                ->andFilterWhere(['user_id' => Yii::$app->user->id])
                ->orderBy(['created_at' => SORT_DESC])
                ->all();   
    }

    public function actionToMeComments(){
        return ContentComment::find()
                ->joinWith('content', true)
                ->andFilterWhere(['content.user_id' => Yii::$app->user->id])
                ->orderBy(['created_at' => SORT_DESC])
                ->all();
    }

    public function actionToMyContents(){
        return ContentComment::find()
                ->joinWith('content', true)
                ->andFilterWhere(['content.user_id' => Yii::$app->user->id, 'content.is_tour' => 0])
                ->orderBy(['created_at' => SORT_DESC])
                ->all();
    }

    public function actionToMyTours(){
        return ContentComment::find()
                ->joinWith('content', true)
                ->andFilterWhere(['content.user_id' => Yii::$app->user->id, 'content.is_tour' => 1])
                ->orderBy(['created_at' => SORT_DESC])
                ->all();
    }

}