RCartModule
==========

Модуль для управления содержимым корзины, содержит все типичные виджеты. Создан на основе [EShoppingCart](https://github.com/yiiext/shopping-cart-component)

Терминология
==========

Чтобы избжать путанницы, определяю:
* **Модель-донор** - модель, которую пользователь добавляет в корзину (например, на базе CActiveRecord)
* **Модель данных** - модель, которая добавляется в корзину фактически.

Технически, ничто не помешает сделать так, чтобы модель принадлежала к обоим типам, но это в большинстве случаев приведёт к тому, что в сессии будут хранится избыточные данные

Подключение
==========

* Модель-донор должна иметь метод getCartModel(), который возвращает модель данных, созданную на основе текущей модели-донора. Например:

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

* Подключить модуль RCartModule к приложению

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

* Модель данных, в свою очередь, должна:
 * Быть наследником CComponent
 * Иметь атрибут id
 * Иметь метод getPrice(), возвращающий цену товара

```php
	public function getPrice() {
		return $this->_price;
	}
```

Внешний вид виджетов можно легко переопределить с помощью тем.

* Кнопка "Добавить в корзину" добавляется виджетом RCartAddWidget

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