<?php
/**
 * Created by PhpStorm.
 * User: Rugalev
 * Date: 06.08.14
 * Time: 15:09
 */

class RCartWidget extends CWidget {

	public $moduleId = 'cart';

	public function run() {
		$total = $this->getModule()->getTotal();
		$totalWithoutDiscount = $this->getModule()->getTotal(false);
		$count = $this->getModule()->getCount();
		$this->render('cartWidget', compact('total', 'totalWithoutDiscount', 'count'));
	}

	/**
	 * @return RCartModule
	 */
	public function getModule() {
		return Yii::app()->getModule($this->moduleId);
	}
}
