(function ($) {

	SS6 = window.SS6 || {};
	SS6.productList = SS6.productList || {};
	SS6.productList.AjaxFilter = SS6.productList.AjaxFilter || {};

	SS6.productList.AjaxFilter = function (ajaxMoreLoader) {
		var $productsWithControls = $('.js-product-list-ajax-filter-products-with-controls');
		var $productFilterForm = $('form[name="productFilter_form"]');
		var $showResultsButton = $('.js-product-filter-show-result-button');
		var $resetFilterButton = $('.js-product-filter-reset-button');

		this.init = function () {
			$productFilterForm.change(function () {
				$productsWithControls.addClass('js-disable');
				$('.js-product-list-ajax-filter-loading').show();
				history.replaceState({}, '', SS6.url.getBaseUrl() + '?' + $productFilterForm.serialize());
				submitFormWithAjax($productFilterForm.serialize());
			});

			$showResultsButton.click(function () {
				var $productList = $('.js-product-list');
				$('html, body').animate({ scrollTop: $productList.offset().top }, 'slow');
				return false;
			});

			$resetFilterButton.click(function () {
				$productsWithControls.addClass('js-disable');
				$('.js-product-list-ajax-filter-loading').show();
				$productFilterForm
					.find(':radio, :checkbox').removeAttr('checked').end()
					.find('textarea, :text, select').val('');
				var resetUrl = $(this).attr('href');
				history.replaceState({}, '', resetUrl);
				submitFormWithAjax();
				return false;
			});
		};

		var showProducts = function ($wrappedData) {
			var $productsHtml = $wrappedData.find('.js-product-list-ajax-filter-products-with-controls');
			$productsWithControls.html($productsHtml);
			$productsWithControls.show();
			$productsWithControls.removeClass('js-disable');
			ajaxMoreLoader.reInit();
			SS6.register.registerNewContent($productsWithControls);
		};

		var updateFilterCounts = function ($wrappedData) {
			var $existingCountElements = $('.js-product-filter-count');
			var $newCountElements = $wrappedData.find('.js-product-filter-count');

			$newCountElements.each(function () {
				var $newCountElement = $(this);
				var $existingCountElement = $existingCountElements
					.filter('[data-form-id="' + $newCountElement.data('form-id') + '"]');

				$existingCountElement.html($newCountElement.html());
			});
		};

		var submitFormWithAjax = function (submitData) {
			$.ajax({
				url: SS6.url.getBaseUrl(),
				data: submitData,
				success: function (data) {
					var $wrappedData = $($.parseHTML('<div>' + data + '</<div>>'));

					showProducts($wrappedData);
					updateFilterCounts($wrappedData);
					$('.js-product-list-ajax-filter-loading').hide();
				}
			});
		};

	};

})(jQuery);
