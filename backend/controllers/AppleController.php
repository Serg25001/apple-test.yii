<?php
namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;
use backend\models\Apple;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

class AppleController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'create-random' => ['POST'],
                    'fall' => ['POST'],
                    'eat' => ['POST'],
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Список яблок
     */
    public function actionIndex()
    {
        $apples = Apple::find()->orderBy(['id' => SORT_DESC])->all();

        // Обновим статусы всех яблок (чтобы пометить гнилые)
        foreach ($apples as $a) {
            $a->refreshStatus();
        }

        return $this->render('index', [
            'apples' => $apples,
        ]);
    }

    /**
     * Сгенерировать N случайных яблок
     */
    public function actionCreateRandom()
    {
        $n = (int)Yii::$app->request->post('n', 5);
        $n = max(1, min(100, $n));
        for ($i = 0; $i < $n; $i++) {
            Apple::createRandom();
        }
        return $this->redirect(['index']);
    }

    /**
     * Упасть
     */
    public function actionFall()
    {
        $id = (int)Yii::$app->request->post('id');
        $apple = $this->findModel($id);
        try {
            $apple->fall();
            Yii::$app->session->setFlash('success', 'Яблоко упало.');
        } catch (\DomainException $e) {
            Yii::$app->session->setFlash('error', $e->getMessage());
        }
        return $this->redirect(['index']);
    }

    /**
     * Съесть
     */
    public function actionEat()
    {
        $id = (int)Yii::$app->request->post('id');
        $percent = (float)Yii::$app->request->post('percent');
        $apple = $this->findModel($id);
        try {
            $res = $apple->eat($percent);
            if ($res === 'deleted') {
                Yii::$app->session->setFlash('success', 'Яблоко полностью съедено и удалено.');
            } else {
                Yii::$app->session->setFlash('success', 'Откусили ' . $percent . '%');
            }
        } catch (\DomainException $e) {
            Yii::$app->session->setFlash('error', $e->getMessage());
        }
        return $this->redirect(['index']);
    }

    /**
     * Удалить вручную
     */
    public function actionDelete()
    {
        $id = (int)Yii::$app->request->post('id');
        $apple = $this->findModel($id);
        $apple->delete();
        Yii::$app->session->setFlash('success', 'Яблоко удалено.');
        return $this->redirect(['index']);
    }

    protected function findModel($id): Apple
    {
        if (($model = Apple::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('Яблоко не найдено');
    }
}
