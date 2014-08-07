RCartModule
==========

Модуль для управления содержимым корзины, содержит все типичные виджеты. Создан на основе [EShoppingCart](https://github.com/yiiext/shopping-cart-component)

Подключение
==========

* Модели, которые можно добавлять в корзину (доноры) должны иметь метод getCartModel(), который возвращает объект (модель данных), добавляемый в корзину например:

```php
	public function getCartModel() {
		if($this->module_id != Module::getIdByUrl('catalog'))
			return false;
		$model = new CartItem();
		$model->setAttributes(array(
			'id' => $this->id,
			'name' => $this->name,
			'color' => $this->color,
			'photo' => $this->getIco('cart', 'link'),
			'href' => $this->getHref(true, true),
			'price' => $this->price,
			'parent_id' => $this->parent_id,
		), false);
		return $model;
	}
```

* Подключить компонент RCartModule к приложению

```php
	'modules' => array(
        'cart' => array(
	        'class' => 'application.modules.cart.RCartModule',
	        'modelClass' => 'Product', //Класс модели донора
	        'jsOptions' => array(
	            'linkClass' => 'addToCart', // Класс ссылки для добавления/удаления
	            'widgetSelector' => '#cartWidget', // Селектор блока корзины, который выводится в RCartWidget
	            'loadingClass' => 'loading', // Класс, добавляемый к элементам при загрузке страницы
	        ),
	        'jsSuccessCallback' => '', // JS функция, вызываемая при успешном добавлении/удалении из корзины. Можно оставить пустым
	        'addMode' => 'replace', // Метод, которым будет добавляться довар в случае если он уже есть в корзине (add или replace)
        ),
    ),
```


Так же можно вернуть $this, но это может привести к прожорливости приложения, т.к. вся корзина проходит через serialize() и сохраняется в сессию.

* Замапить контроллер RCartController как cart

* Объект, добавляемый в корзину, в свою очередь, должен:
 * Быть наследником CComponent
 * Иметь атрибут id
 * Иметь метод getPrice(), возвращающий цену товара.

```php
	public function getPrice() {
		return $this->_price;
	}
```

* Кнопка "Добавить в корзину" добавляется виджетом RCartAddWidget. В теме легко можно переопределить её внешний вид

```php
// Лучше писать абсолютный путь т.к. если RCartModule не импортируется при инициализации приложения, короткий путь 'cart' будет недоступен
$this->widget('application.modules.cart.widgets.RCartAddWidget', array(
	'id' => $id,
	'text' => $text,
));
```

* Виждет корзины добавляется виджетом RCartWidget

```php
$this->widget('application.modules.cart.widgets.RCartWidget');
```