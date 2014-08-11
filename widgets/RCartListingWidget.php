<?php

/**
 * Class RCartListingWidget
 *
 * @property RCartModule $module
 */
class RCartListingWidget extends CWidget {

	public $form = false;
	public $total = true;
	public $moduleId = 'cart';

	public function run() {
		$items = $this->module->getItems();
		$dataProvider = $this->getDataProvider($items);
		$this->render('cartListing', compact('dataProvider'));
	}

	public function getDataProvider($items) {
		return new CArrayDataProvider($items);
	}

	/**
	 * @return RCartModule
	 */
	public function getModule() {
		return Yii::app()->getModule($this->moduleId);
	}
} 