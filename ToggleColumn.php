<?php

namespace dixonstarter\togglecolumn;

use Yii;
use Closure;
use yii\helpers\Html;
use yii\web\View;
use yii\helpers\Url;
use yii\grid\DataColumn;
use yii\helpers\ArrayHelper;

/**
 * Toggle update column in gridveiw
 * @author Sathit Seethaphon <dixonsatit@gmail.com>
 */
class ToggleColumn extends DataColumn
{

    public $urlCreator;

    public $controller;

    public $action = 'toggle-update';

    public $content;

    //public $contentOptions = ['class'=>'text-center'];

    /**
     * [$linkTemplateOn link template for on value]
     * @example
     * default <a class="toggle-column" data-pjax="0" href="{url}">{label}</a>
     * icon & label <a class="toggle-column btn btn-primary btn-xs btn-block" data-pjax="0" href="{url}"><i class="glyphicon glyphicon-ok"></i> {label}</a>'
     * icon <a class="toggle-column btn btn-primary btn-xs btn-block" data-pjax="0" href="{url}"><i class="glyphicon glyphicon-ok"></i></a>
     * @var string
     */
    public $linkTemplateOn    = '<a class="toggle-column btn btn-primary btn-xs btn-block" data-pjax="0" href="{url}"><i  class="glyphicon glyphicon-ok"></i> {label}</a>';

    /**
     * [$linkTemplateOn link template for off value]
     * @example
     * default <a class="toggle-column" data-pjax="0" href="{url}">{label}</a>
     * icon & label <a class="toggle-column btn btn-info btn-xs btn-block" data-pjax="0" href="{url}"><i class="glyphicon glyphicon-off"></i> {label}</a>
     * icon <a class="toggle-column btn btn-info btn-xs btn-block" data-pjax="0" href="{url}"><i class="glyphicon glyphicon-time"></i> </a>
     * @var string
     */
    public $linkTemplateOff   = '<a class="toggle-column btn btn-default btn-xs btn-block" data-pjax="0" href="{url}"><i  class="glyphicon glyphicon-remove"></i> {label}</a>';

    /**
     * Array toggle items
     * @var array
     */
    public $items = [
      'on' => ['label'=>'On', 'value'=>1],
      'off' => ['label'=>'Off', 'value'=>0],
    ];

    public function init()
    {
      parent::init();
      $this->setFilter();
      $this->registerJs();
    }

    public function setFilter()
    {
      $model = $this->grid->filterModel;
      if(method_exists($model,'getToggleItems'))
      {
        $this->filter = $this->filter == false ? $model->getItemFilter() : $this->filter;
      }
    }

    public function getIsOn($value,$model){
      return $value == $model->onValue;
    }

    private function getLabel($key,$model)
    {
        return $this->getIsOn($key,$model) ? $model->onLabel : $model->offLabel;
    }

    private function setToggleItems($model){
      if(method_exists($model,'getToggleItems')){
        $this->items = $model->getToggleItems();
      }
    }

    public function createUrl($action, $model, $key, $index)
    {
        if (is_callable($this->urlCreator)) {
            return call_user_func($this->urlCreator, $action, $model, $key, $index);
        } else {
            $params = is_array($key) ? $key : ['id' => (string) $key];
            $params[0] = $this->controller ? $this->controller . '/' . $action : $action;
            return Url::toRoute($params);
        }
    }

    protected function renderTemplate($model, $key, $index)
    {
        $url = $this->createUrl($this->action,$model, $key, $index);
        $value = parent::renderDataCellContent($model, $key, $index);
        return strtr($this->getIsOn($value,$model) ? $this->linkTemplateOn : $this->linkTemplateOff, [
               '{url}' => Html::encode($url),
               '{label}' => $this->getLabel($value,$model),
        ]);
    }

    protected function renderDataCellContent($model, $key, $index)
    {
       $this->setToggleItems($model);
       $url = $this->createUrl($this->action,$model, $key, $index);
       if ($this->content !== null) {
            return call_user_func($this->content, $url, $model, $key, $index, $this);
        } else {
            return $this->renderTemplate($model, $key, $index);
        }
    }

    public function registerJs()
    {
        $js = <<<JS
$("a.toggle-column").on("click", function(e) {
    e.preventDefault();
    var pjaxId = $(e.target).closest(".grid-view").parent().attr("id");
    $(this).button('loading');
    $.post($(this).attr("href"), function(data) {
      $.pjax.reload({container:"#" + pjaxId});
    });
    return false;
});
JS;
        $this->grid->view->registerJs($js, View::POS_READY, 'dixonstarter-toggle-column');
    }
}
