<?php

namespace app\controllers;

use Yii;
use app\models\Sites;
use app\models\SitesSearch;
use yii\base\Model;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\UploadForm;
use yii\web\UploadedFile;

/**
 * SitesController implements the CRUD actions for Sites model.
 */
class SitesController extends Controller
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
                    'import' => ['POST']
                ],
            ],
        ];
    }

    /**
     * Lists all Sites models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new SitesSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $upload = new UploadForm();
        $uploadClean = clone $upload;
        if (Yii::$app->request->isPost) {
            $files = UploadedFile::getInstances($upload, 'file');
            foreach ($files as $file) {
                $upload->file = $file;
                if ($fileName = $upload->upload()) {
                    $this->importData($fileName);
                }
            }
        }

        return $this->render('index', [
            'upload' => $uploadClean,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Sites model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Sites model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Sites();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Sites model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Sites model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Sites model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Sites the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Sites::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    protected function importData($fileName) {
        $csvFile = file($fileName);
        $data = [];
        foreach ($csvFile as $line) {
            $data[] = str_getcsv($line);
        }
        $indexWebsite = array_search('Website', $data[0]);

        for ($i = 1; $i < count($data); $i++) {
            $url =$data[$i][$indexWebsite];
            $model = Sites::findOne(['site_url' => $url]);
            if ($model === null) {
                $model = new Sites();
                $model->site_url = $url;
                $model->cron_status = Sites::PENDING;
                $model->save();
            }
        }
    }
}
