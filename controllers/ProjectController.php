<?php

namespace app\controllers;

use app\models\Level;
use app\models\Message;
use app\models\ProjectUser;
use app\models\User;
use Yii;
use app\models\Project;
use app\models\ProjectSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\helpers\Url;

/**
 * ProjectController implements the CRUD actions for Project model.
 */
class ProjectController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class'   => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Project models.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        if(Yii::$app->user->getIdentity() === null) {
            return $this->redirect(Yii::$app->user->loginUrl);
        }

        $searchModel  = new ProjectSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render(
            'index',
            [
                'searchModel'  => $searchModel,
                'dataProvider' => $dataProvider,
            ]
        );
    }

    /**
     * Displays a single Project model.
     *
     *
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView()
    {
        if(Yii::$app->user->getIdentity() === null) {
            return $this->redirect(Yii::$app->user->loginUrl);
        }
        $id = Yii::$app->user->getIdentity()->getAttribute('active_project');
        return $this->render(
            'view',
            [
                'model' => $this->findModel($id),
            ]
        );
    }

    /**
     * Creates a new Project model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     *
     * @return mixed
     */
    public function actionCreate()
    {
        if(Yii::$app->user->getIdentity() === null) {
            return $this->redirect(Yii::$app->user->loginUrl);
        }

        $model = new Project();
        if ($model->load(Yii::$app->request->post()) && ($model->save() || $model->id)) {
            $id = Yii::$app->user->getIdentity()->getId();
            $user = User::findOne($id);
            $user->active_project = $model->id;
            $user->save();
            return $this->redirect(['view']);
        }

        return $this->render(
            'create',
            [
                'model' => $model,
            ]
        );
    }

    /**
     * Updates an existing Project model.
     * If update is successful, the browser will be redirected to the 'view' page.
     *
     * @param integer $id
     *
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        if(Yii::$app->user->getIdentity() === null) {
            return $this->redirect(Yii::$app->user->loginUrl);
        }

        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render(
            'update',
            [
                'model' => $model,
            ]
        );
    }

    /**
     * Deletes an existing Project model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     *
     * @param integer $id
     *
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        if(Yii::$app->user->getIdentity() === null) {
            return $this->redirect(Yii::$app->user->loginUrl);
        }

        $this->findModel($id)->delete();
        $prUsers = ProjectUser::findAll($id);
        foreach ($prUsers as $prUser) {
            $prUser->delete();
        }

        return $this->redirect(['index']);
    }

    /**
     * Finds the Project model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id
     *
     * @return Project the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Project::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionSelect()
    {
        try {
            $request              = Yii::$app->request;
            $post                 = $request->post();
            $id                   = Yii::$app->user->getIdentity()->getId();
            $user                 = User::findOne($id);
            $user->active_project = $post['id'];
            $user->save();
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return ['status' => 'project was active'];
        } catch (\Exception $e) {
            return [$e->getMessage()];
        }
    }

    public function generateKey($length = 16) {
        $max = ceil($length / 40);
        $random = '';
        for ($i = 0; $i < $max; $i ++) {
            $random .= sha1(microtime(true).mt_rand(10000,90000));
        }
        return substr($random, 0, $length);
    }
}
