<?php
/** @var CDataProvider $dataProvider */
/** @var RCartListingWidget $this */
$widget = $this;

if ($this->form)
	echo CHtml::beginForm(array('/' . $this->moduleId . '/cart/index'), 'post', array(
		'class' => 'order-form',
	));

$columns = array(
	'name' => array(
		'header' => Yii::t('cart', 'Product name'),
		'name' => 'name',
	),
	'quantity' => array(
		'header' => Yii::t('cart', 'Quantity'),
		'type' => 'raw',
		'value' => function ($data) {
				return CHtml::textField('Quantity[' . $data->id . ']', $data->quantity, array('class' => 'quantity'));
			}
	),
	'price' => array(
		'header' => Yii::t('cart', 'Price'),
		'value' => function ($data) use ($widget) {
				return $widget->module->getFormattedPrice($data->price);
			},
	),
	'totalPrice' => array(
		'header' => Yii::t('cart', 'Total price'),
		'value' => function ($data) use ($widget) {
				return $widget->module->getFormattedPrice($data->totalPrice);
			},
	),
	'remove' => array(
		'header' => Yii::t('cart', 'Remove'),
		'type' => 'raw',
		'value' => function ($data) use ($widget) {
				return CHtml::link('X', array('/' . $widget->moduleId . '/cart/remove', 'id' => $data->id));
			},
	)
);

if (!$widget->form) {
	unset($columns['quantity']);
	unset($columns['remove']);
}

$this->widget('zii.widgets.grid.CGridView', array(
	'dataProvider' => $dataProvider,
	'columns' => $columns,
));
if ($this->total):
	?>
	<div class="total">
		<?= Yii::t('order', 'Total') ?>: <?= $this->module->getFormattedPrice($this->module->getTotal()) ?>
	</div>
<?php
endif;
if ($this->form):
	?>
	<div class="buttons">
		<input type="submit" name="recountPrice" value="<?= Yii::t('order', 'Recount price') ?>"/>
		<input type="submit" name="process" value="<?= Yii::t('order', 'Process to order') ?>"/>
	</div>
	<?php
	echo CHtml::endForm();
endif;