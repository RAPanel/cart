<?php

YiiBase::setPathOfAlias('cart', dirname(__FILE__));

YiiBase::import('cart.controllers.RCartController');
YiiBase::import('cart.components.RCartBehavior');
YiiBase::import('cart.widgets.RCartWidget');
YiiBase::import('cart.widgets.RCartAddWidget');

class RCartModule extends CWebModule
{

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

	public $priceFormat = array(
		'prefix' => '',
		'suffix' => ' р.',
		'decimals' => 2,
	);

	public $defaultController = 'cart';

	public $jsOptions = array(
		'linkClass' => 'addToCart',
		'widgetSelector' => '#cartWidget',
		'loadingClass' => 'loading',
	);

	public $jsSuccessCallback;

	public $addMode = self::MODE_REPLACE;

	public $orderModuleId = 'order';

	/**
	 * Must return discount amount
	 * Example: '$item->type == "book" ? $price * 0.2 : $price';
	 * @var mixed
	 */
	public $discountExpression = null;

	public function init()
	{
		$this->restoreState();
		parent::init();
	}

	public function restoreState()
	{
		$this->_items = array();
		$data = @unserialize(Yii::app()->getUser()->getState($this->sessionKey));
		if (is_array($data)) {
			foreach ($data as $row) {
				if (!isset($row['model']))
					continue;
				$quantity = (int)$row['quantity'];
				$this->put($row['model'], $quantity, false);
			}
		}
	}

	public function saveState()
	{
		Yii::app()->getUser()->setState($this->sessionKey, $this->getSerializedItems());
		$this->restoreState();
	}

	public function getSerializedItems() {
		$result = array();
		foreach ($this->_items as $i => $item) {
			$result[$i]['quantity'] = $item->getQuantity();
			$item->detachBehaviors();
			$result[$i]['model'] = $item;
		}
		$result = serialize($result);
		foreach($this->_items as $item) {
			$this->addBehavior($item);
		}
		return $result;
	}

	protected function addBehavior($item) {
		$behavior = new RCartBehavior();
		$behavior->enabled = true;
		$item->attachBehavior('RCartBehavior', $behavior);
	}

	public function put($item, $quantity = 1, $save = true, $replace = true)
	{
		if(!is_object($item))
			throw new CException("Incorrect type of item");
		if (!$this->hasItem($item->id) || $replace) {
			if ($quantity <= 0) {
				$this->remove($item->id);
				return;
			}
			$this->addBehavior($item);
			$item->setQuantity($quantity);
			$this->_items[$item->id] = $item;
		} else
			$this->_items[$item->id]->setQuantity($this->_items[$item->id]->getQuantity() + $quantity);
		if ($save)
			$this->saveState();
	}

	public function add($item)
	{
		$this->put($item);
	}

	public function remove($id)
	{
		unset($this->_items[$id]);
		$this->saveState();
	}

	public function getItems()
	{
		return $this->_items;
	}

	public function hasItem($id)
	{
		return isset($this->_items[$id]);
	}

	public function getTotal($discount = true)
	{
		$total = 0;
		foreach ($this->_items as $item)
			$total += $item->getTotalPrice($discount);
		return $total;
	}

	public function getQuantity()
	{
		$total = 0;
		foreach ($this->_items as $item)
			$total += $item->getQuantity();
		return $total;
	}

	public function getCount()
	{
		return count($this->_items);
	}

	public function registerScripts()
	{
		if (self::$_registered)
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

	public function getCartModel($id) {
		$model = CActiveRecord::model($this->modelClass)->findByPk($id);
		if(is_null($model) || !method_exists($model, 'getCartModel'))
			return false;
		$result = $model->getCartModel();
		if(!is_object($result))
			return false;
		return $result;
	}

	public function getCartUrl() {
		return array('/' . $this->id . '/cart/index');
	}

	public function getFormattedPrice($price) {
		return $this->priceFormat['prefix'] . sprintf("%01.{$this->priceFormat['decimals']}f", $price) . $this->priceFormat['suffix'];
	}

	public function getOrderProcessingUrl() {
		if($this->orderModuleId)
			return array('/' . $this->orderModuleId . '/order/index');
		else
			return null;
	}

	public function applyDiscount() {
		if($this->discountExpression)
		foreach($this->_items as $item) {
			$item->setDiscount(Yii::app()->evaluateExpression($this->discountExpression, array('item' => $item, 'price' => $item->price)));
		}
	}
}
