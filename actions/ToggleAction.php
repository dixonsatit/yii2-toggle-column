<?php
namespace dixonstarter\togglecolumn\actions;

use Yii;
use yii\base\Action;
use yii\base\InvalidConfigException;
use yii\db\Expression;
use yii\web\Response;
use yii\web\NotFoundHttpException;
use yii\web\BadRequestHttpException;
use yii\web\MethodNotAllowedHttpException;

class ToggleAction extends Action
{
   public $modelClass;

   public $scenario  = 'default';

   public $attribute = 'status';

   public $condition;

   public function run($id)
   {
     Yii::$app->response->format = Response::FORMAT_JSON;
     if (Yii::$app->request->isAjax) {
           $model = $this->findModel($id);
           $model->setScenario($this->scenario);
           $model->setAttribute($this->attribute,$this->setValue($model->{$this->attribute},$model));
           if($model->save()){
             return ['success'=>true];
           }else{
             return ['success'=>false];
           }
     }
   }

   public function setValue($value,$model){
     if (is_callable($this->condition)) {
         return call_user_func($this->condition, $value);
     } else {
       return $model->offValue == $value ? $model->onValue : $model->offValue;
      }
   }

   protected function findModel($id)
   {
      $class = $this->modelClass;
      if ($id !== null && ($model = $class::findOne($id)) !== null) {
          return $model;
      } else {
          throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
      }
   }
}
