<?php

namespace app\controllers;

use app\models\Helper\Helper;
use app\models\Project;
use app\models\ProjectUser;
use Yii;
use app\models\Message;
use app\models\MessageSearch;
use app\models\JsMessage;
use app\models\JsMessageSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * LogController implements the CRUD actions for Message model.
 */
class LogController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Message models.
     * @return mixed
     */
    public function actionIndex()
    {
        if(Yii::$app->user->getIdentity() === null) {
            return $this->redirect(Yii::$app->user->loginUrl);
        }
        $searchModel = new MessageSearch();
        $searchParam = Yii::$app->request->queryParams;

        $id = Yii::$app->user->getIdentity()->getAttribute('active_project');
        if($id === null) {
            $projectUser = ProjectUser::find()->where(['user_id' => Yii::$app->user->getIdentity()->getId()])->one();
            $id = $projectUser->getAttribute('project_id');
        }

        $searchParam['MessageSearch']['project_id'] = $id;

        $dataProvider = $searchModel->search($searchParam);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Lists all Message models.
     * @return mixed
     */
    public function actionJs()
    {
        if(Yii::$app->user->getIdentity() === null) {
            return $this->redirect(Yii::$app->user->loginUrl);
        }
        $searchModel = new JsMessageSearch();
        $searchParam = Yii::$app->request->queryParams;

        $id = Yii::$app->user->getIdentity()->getAttribute('active_project');
        $project = Project::findOne(['id' => $id]);
        if($id === null) {
            $projectUser = ProjectUser::find()->where(['user_id' => Yii::$app->user->getIdentity()->getId()])->one();
            $id = $projectUser->getAttribute('project_id');
        }

        $searchParam['MessageSearch']['project_id'] = $id;

        $dataProvider = $searchModel->search($searchParam);

        return $this->render('js', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Message model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        if(Yii::$app->user->getIdentity() === null) {
            return $this->redirect(Yii::$app->user->loginUrl);
        }

        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Message model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        if(Yii::$app->user->getIdentity() === null) {
            return $this->redirect(Yii::$app->user->loginUrl);
        }

        $model = new Message();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Message model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
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

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Message model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        if(Yii::$app->user->getIdentity() === null) {
            return $this->redirect(Yii::$app->user->loginUrl);
        }

        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Message model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Message the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Message::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function messageParser($row)
    {
        switch (gettype($row)) {
            case 'object':
                foreach ($row as $key => $item) {
                    echo "<div class='pars-row'>";
                    echo "<span>\"$key\"</span>:";
                    $this->messageParser($item);
                    echo "</div>";
                }
                return;
            case 'array':
                foreach ($row as $key => $item) {
                    echo "<div class='pars-row'>";
                    echo "<span>\"$key\"</span>:<span class=\"glyphicon glyphicon-triangle-bottom\" aria-hidden=\"true\"></span>";
                    echo "<div class='pars-row'>[";
                    $this->messageParser($item);
                    echo "]</div></div>";
                }
                return;
            case 'string':
                echo '<span>"' . htmlspecialchars($row, ENT_QUOTES) . '"</span>';
                return;
            case 'integer':
                echo '<span>"' . htmlspecialchars($row, ENT_QUOTES) . '"</span>';
                return;
        }
    }
}
