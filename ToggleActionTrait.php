<?php

namespace dixonstarter\togglecolumn;

use yii\helpers\ArrayHelper;
/**
 *
 */
trait ToggleActionTrait
{

  public function getItemFilter()
  {
    $items = [];
    foreach ($this->toggleItems as $key => $value) {
      $items[$value['value']] = $value['label'];
    }
    return $items;
  }

  public function getOnLabel()
  {
      return ArrayHelper::getValue(ArrayHelper::getValue($this->toggleItems,'on'),'label');
  }

  public function getOffLabel()
  {
      return ArrayHelper::getValue(ArrayHelper::getValue($this->toggleItems,'off'),'label');
  }

  public function getOnValue()
  {
      return ArrayHelper::getValue(ArrayHelper::getValue($this->toggleItems,'on'),'value');
  }

  public function getOffValue()
  {
      return ArrayHelper::getValue(ArrayHelper::getValue($this->toggleItems,'off'),'value');
  }
}
