$(function() {
	$.fn.addToCart = function(options) {
		options = $.extend({
			linkClass: 'addToCart',
			widgetSelector: '#cartWidget',
			loadingClass: 'loading'
		}, options || {});
		$(document).on('click', 'a.' + options.linkClass, function () {
			var cart = $(options.widgetSelector);
			if(cart.length && cart.hasClass(options.loadingClass))
				return false;
			var link = $(this);
			if(link.hasClass(options.loadingClass))
				return false;
			var url = link.attr('href');
			link.addClass(options.loadingClass);
			cart.addClass(options.loadingClass);
			$.get(url, function (data) {
				var result = $.parseJSON(data);
				if (!result.success) {
					console.error('Cart error');
					return;
				}
				if(typeof result.callback == 'undefined' || result.callback.length == 0) {
					if (link.hasClass('added'))
						link.removeClass('added');
					else
						link.addClass('added');
					link.attr('href', result.href);
				} else {
					try {
						eval(result.callback + "(result)");
					} catch (e) {
						console.warn("Callback " + result.callback + " is not defined!");
					}
				}
				link.removeClass(options.loadingClass);
				if(typeof result.cart != 'undefined' && typeof result.cart.widget != 'undefined')
					cart.replaceWith(result.cart.widget);
			});
			return false;
		});
	};
});