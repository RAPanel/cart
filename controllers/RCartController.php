<?php

/**
 * Class RCartController
 *
 * @property RCartModule $module
 */
class RCartController extends CController {

	public function actionIndex() {
		if(is_array($_POST['Quantity'])) {
			foreach($_POST['Quantity'] as $itemId => $quantity) {
				$item = $this->module->getCartModel($itemId);
				$this->module->put($item, $quantity, false, true);
			}
			$this->module->saveState();
			if(isset($_POST['process'])) {
				$this->processToOrder();
				Yii::app()->end(); // YOU SHALL NOT PASS!
			}
		}
		$this->render('index', compact('dataProvider'));
	}


	public function actionAdd($id, $quantity = 1) {
		$model = $this->module->getCartModel($id);
		if(!$model)
			$this->throwError(404);
		if($this->module->addMode == RCartModule::MODE_REPLACE)
			$this->module->put($model, $quantity);
		else
			$this->module->put($model, $quantity, true, false);
		$this->success($id);
	}

	public function actionRemove($id) {
		$model = $this->module->getCartModel($id);
		if(!$model)
			$this->throwError(404);
		$this->module->remove($model->id);
		$this->success($id);
	}

	protected function throwError($httpCode = 404) {
		if(Yii::app()->request->isAjaxRequest)
			throw new CHttpException($httpCode);
		elseif(Yii::app()->request->urlReferrer)
			$this->redirect(Yii::app()->request->urlReferrer);
		else
			echo "error";
		Yii::app()->end();
	}

	protected function success($id) {
		if(Yii::app()->request->isAjaxRequest)
			$this->response($this->getSuccessData($id));
		elseif(Yii::app()->request->urlReferrer)
			$this->redirect(Yii::app()->request->urlReferrer);
		Yii::app()->end();
	}

	protected function getSuccessData($id) {
		if($this->action->id == 'add')
			$href = CHtml::normalizeUrl(array('cart/remove', 'id' => $id));
		else
			$href = CHtml::normalizeUrl(array('cart/add', 'id' => $id));
		$data = array(
			'success' => true,
			'href' => $href,
			'cart' => array(
				'count' => $this->module->getCount(),
				'quantity' => $this->module->getQuantity(),
				'total' => $this->module->getTotal(),
				'totalWithoutDiscount' => $this->module->getTotal(false),
				'widget' => $this->widget('RCartWidget', array(), true),
			),
		);
		if($this->module->jsSuccessCallback)
			$data['callback'] = $this->module->jsSuccessCallback;
		return $data;
	}

	protected function response($data) {
		echo CJSON::encode($data);
	}

	public function processToOrder() {
		$total = $this->module->getTotal();
		$totalWODiscount = $this->module->getTotal(false);
		$discount = $total - $totalWODiscount;
		$orderData = $this->module->getSerializedItems();
		Yii::app()->getModule($this->module->orderModuleId)->beginProcessing($total, $discount, $orderData);
	}
}
