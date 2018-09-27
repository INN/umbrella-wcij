jQuery(document).ready(function($){

	// Responsive modal
	var $modal = $('#trust-indicators-modal');
	var $btn = $('#trust-indicators-button');
	var $close = $('.trust-indicators-close');

	$btn.click(function(){
		//$modal.html( html );
		$modal.show();
		$('#trust-indicators').unbind().click(function(e) {
			e.stopPropagation();
		});
	});

	$modal.click(function(){
		$modal.hide();
	});

	$close.click(function(){
		$modal.hide();
	});
});


