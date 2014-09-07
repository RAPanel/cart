<?php

return array(
	'modules' => array(
		'cart' => 'cart.RCartModule',
		'modelClass' => 'Product',
		'jsOptions' => array(
			'linkClass' => 'addToCart',
			'widgetSelector' => '#cartWidget',
			'loadingClass' => 'loading',
		),
		'jsSuccessCallback' => '',
		'addMode' => 'replace',
	),
);