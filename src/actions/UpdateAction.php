<?php

namespace voskobovich\crud\actions;

use voskobovich\crud\controllers\BackendController;
use Yii;
use yii\db\ActiveRecord;
use yii\web\Response;
use yii\widgets\ActiveForm;


/**
 * Class UpdateAction
 * @package voskobovich\crud\actions
 */
class UpdateAction extends BaseAction
{
    /**
     * The route which will be transferred after the user action
     * @var string
     */
    public $redirectUrl = ['update', 'id' => ':primaryKey'];

    /**
     * View file
     * @var string
     */
    public $viewFile = 'update';

    /**
     * @var bool
     */
    public $enableAjaxValidation = true;

    /**
     * @return string
     * @throws \yii\web\NotFoundHttpException
     */
    public function run()
    {
        $pk = $this->getModelPk();

        /** @var ActiveRecord $model */
        $model = $this->findModel($pk);
        $model->scenario = $this->scenario;

        $params = Yii::$app->getRequest()->getBodyParams();
        if ($model->load($params)) {

            if ($this->enableAjaxValidation && Yii::$app->request->isAjax && !empty($params['ajax'])) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ActiveForm::validate($model);
            }

            if ($model->save()) {
                if ($this->successCallback) {
                    call_user_func($this->successCallback, $model);
                } else {
                    Yii::$app->session->setFlash('update:success');
                }

                if ($this->redirectUrl) {
                    return $this->redirect($model);
                }
            } else {
                if ($this->errorCallback) {
                    call_user_func($this->errorCallback, $model);
                } else {
                    Yii::$app->session->setFlash('update:error');
                }
            }
        }

        if (!$this->viewFile) {
            return null;
        }

        /** @var BackendController $controller */
        $controller = $this->controller;
        return $controller->render($this->viewFile, [
            'model' => $model
        ]);
    }
}