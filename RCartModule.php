<?php

YiiBase::setPathOfAlias('cart', dirname(__FILE__));

YiiBase::import('cart.controllers.RCartController');
YiiBase::import('cart.components.RCartBehavior');
YiiBase::import('cart.widgets.RCartWidget');
YiiBase::import('cart.widgets.RCartAddWidget');

class RCartModule extends CWebModule {

	const MODE_REPLACE = 'replace';
	const MODE_INCREMENT = 'increment';

	private $_items;

	public $sessionKey = __CLASS__;
	public $modelClass = 'Product';

	private static $_registered = false;

	public $controllerMap = array(
		'cart' => array(
			'class' => 'cart.controllers.RCartController'
		)
	);

	public $defaultController = 'cart';

	public $jsOptions = array(
		'linkClass' => 'addToCart',
		'widgetSelector' => '#cartWidget',
		'loadingClass' => 'loading',
	);

	public $jsSuccessCallback;

	public $addMode = self::MODE_REPLACE;

	public function init() {
		$this->restoreState();
		parent::init();
	}

	public function restoreState() {
		$this->_items = array();
		$data = @unserialize(Yii::app()->getUser()->getState($this->sessionKey));
		if (is_array($data)) {
			foreach ($data as $row) {
				if(!isset($row['model']))
					continue;
				$quantity = (int)$row['quantity'];
				$this->put($row['model'], $quantity, false);
			}
		}
	}

	public function saveState() {
		$state = [];
		foreach($this->_items as $i => $item) {
			$state[$i]['quantity'] = $item->getQuantity();
			$item->detachBehaviors();
			$state[$i]['model'] = $item;
		}
		Yii::app()->getUser()->setState($this->sessionKey, serialize($state));
		$this->restoreState();
	}

	public function put($item, $quantity = 1, $save = true, $replace = true) {
		if(!$this->hasItem($item->id) || $replace) {
			$behavior = new RCartBehavior();
			$behavior->enabled = true;
			$item->attachBehavior('RCartBehavior', $behavior);
			$item->setQuantity($quantity);
			$this->_items[$item->id] = $item;
		} else
			$this->_items[$item->id]->setQuantity($this->_items[$item->id]->getQuantity() + $quantity);
		if($save)
			$this->saveState();
	}

	public function add($item) {
		$this->put($item);
	}

	public function remove($id) {
		unset($this->_items[$id]);
		$this->saveState();
	}

	public function getItems() {
		return $this->_items;
	}

	public function hasItem($id) {
		return isset($this->_items[$id]);
	}

	public function getTotal($discount = true) {
		$total = 0;
		foreach($this->_items as $item)
			$total += $item->getSumPrice($discount);
		return $total;
	}

	public function getTotalQuantity() {
		$total = 0;
		foreach($this->_items as $item)
			$total += $item->getQuantity();
		return $total;
	}

	public function getCount() {
		return count($this->_items);
	}

	public function registerScripts() {
		if(self::$_registered)
			return;
		$assetsDir = Yii::app()->assetManager->publish(dirname(__FILE__) . '/assets', false, -1, YII_DEBUG);
		/** @var CClientScript $clientScript */
		$clientScript = Yii::app()->clientScript;
		$clientScript->registerScriptFile($assetsDir . '/cart.js');
		$jsOptions = CJavaScript::encode($this->jsOptions);
		$clientScript->registerScript(__CLASS__, <<<JAVASCRIPT
$.fn.addToCart({$jsOptions});
JAVASCRIPT
			, CClientScript::POS_READY);
		self::$_registered = true;
	}

}
