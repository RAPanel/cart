<?php
/** @var CDataProvider $dataProvider */
/** @var RCartController $this */
$controller = $this;

echo CHtml::beginForm(array('/' . $this->module->id . '/' . $this->id . '/' . $this->action->id), 'post', array(
	'class' => 'order-form',
));

$this->widget('zii.widgets.grid.CGridView', array(
	'dataProvider' => $dataProvider,
	'columns' => array(
		'name' => array(
			'header' => Yii::t('order', 'Product name'),
			'name' => 'name',
		),
		'quantity' => array(
			'header' => Yii::t('order', 'Quantity'),
			'type' => 'raw',
			'value' => function ($data) {
					return CHtml::textField('Quantity[' . $data->id . ']', $data->quantity, array('class' => 'quantity'));
				}
		),
		'price' => array(
			'header' => Yii::t('order', 'Price'),
			'value' => function ($data) use ($controller) {
					return $controller->module->getFormattedPrice($data->price);
				},
		),
		'totalPrice' => array(
			'header' => Yii::t('order', 'Total price'),
			'value' => function ($data) use ($controller) {
					return $controller->module->getFormattedPrice($data->totalPrice);
				},
		),
	),
));
?>

	<div class="total">
		<?= Yii::t('order', 'Total') ?>: <?= $this->module->getFormattedPrice($this->module->getTotal()) ?>
	</div>

	<div class="buttons">
		<input type="submit" name="recountPrice" value="<?= Yii::t('order', 'Recount price') ?>"/>
		<input type="submit" name="process" value="<?= Yii::t('order', 'Process to order') ?>"/>
	</div>

<?= CHtml::endForm() ?>