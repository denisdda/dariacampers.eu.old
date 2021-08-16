jQuery(document).ready(function($){
	/**
	 * Setup Off Canvas navigation
	 */
	// detect off-canvas and offcanvas type
	var $offcanvas = $('.t4-offcanvas'),
		$close = $('button.close'),
		$toggle = $('.t4-offcanvas-toggle');
	if (!$offcanvas.length) {
		$toggle.hide();
		return;
	}
	// detect effect
	var effect = parseInt($offcanvas.data('effect'));
	if (!effect) effect = 1; // default
	$offcanvas.addClass('oc-effect-' + effect);

	// add class to body
	$('body').addClass('oc-effect-' + effect);

	// add st-container, st-pusher
	var sttype = [1,2,4,5,9,10,11,12,13].indexOf(effect) == -1 ? 2 : 1;
	var $container = $('.t4-wrapper'), 
		$pusher = $('.t4-wrapper-inner'), 
		$content = $('.t4-content');

	// $container.addClass('st-container');
	// $pusher.addClass('st-pusher');
	// $content.addClass('st-content');

	// move offcanvas into pusher for some type
	if ([1,2,4,5,9,10,11,12,13].indexOf(effect) == -1) {
		$offcanvas.prependTo($pusher);
	}


	/**
	 * Init event
	 */
	var resetToggle = function (e) {
		var $target = $(e.target);
		if ($target.hasClass('oc-close') || $target.closest($offcanvas).length == 0 || $target.hasClass('close')) {
			$('body').removeClass('t4-offcanvas-open');
			$('body').off('click', resetToggle);
			$content.focus();
		}
	}
	
	$toggle.on('click', function (e) {
		e.stopPropagation();
		e.preventDefault();		
		$('body').addClass('t4-offcanvas-open');
		// listen to exit event
		$('body').on('click touchstart', resetToggle);
		$close.on('click touchstart', resetToggle);
	});

	$offcanvas.show();
});