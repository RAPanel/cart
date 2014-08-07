<?php
/**
 * Created by PhpStorm.
 * User: Rugalev
 * Date: 05.08.14
 * Time: 17:42
 */

class RCartBehavior extends CBehavior {

	private $_quantity;

	private $_discount = 0;

	public function getQuantity() {
		return $this->_quantity;
	}

	public function setQuantity($quantity) {
		$this->_quantity = $quantity;
	}

	public function getDiscount() {
		return $this->_discount;
	}

	public function setDiscount($discount) {
		$this->_discount = $discount;
	}

	public function getTotalPrice($discount = true) {
		return $this->getQuantity() * ($discount ? $this->getOwner()->getPrice() - $this->_discount : $this->getOwner()->getPrice());
	}

	public function getPrice() {
		return 0;
	}

}