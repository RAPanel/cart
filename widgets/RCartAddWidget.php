<?php

class RCartAddWidget extends CWidget
{
	public $itemId;
	public $text = '';
	public $moduleId = 'cart';

	public function init() {
		$this->getModule()->registerScripts();
	}

	public function run()
	{
		$class = 'addToCart';
		if ($this->getModule()->hasItem($this->itemId)) {
			$url = $this->getUrl('remove');
			$class .= ' added';
		} else {
			$url = $this->getUrl('add');
		}
		$this->render('addToCart', compact('url', 'class'));
	}

	public function getUrl($action) {
		return array('cart/' . $action, 'id' => $this->itemId);
	}

	/**
	 * @return RCartModule
	 */
	public function getModule() {
		return Yii::app()->getModule($this->moduleId);
	}
}
