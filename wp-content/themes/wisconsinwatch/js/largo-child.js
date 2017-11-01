jQuery(document).ready(function($) {

	var $body = $('body'),
		$sb = $('#sidebar'),
		$content = $('.entry-content'),
		ratio = window.devicePixelRatio || 1;

	// Things to do on single post pages
	if ( $('.single-post').length ) {

		//get the offset of the sidebar
		var sb_bottom = typeof(sb)== 'undefined' ? 0 : $sb.offset().top + $sb.outerHeight();

		// check big images and extend them
		$('.wp-caption.center > img, img.aligncenter').each(function() {
			if ( this.naturalWidth > ($content.width() * ratio) && $(this).offset().top > sb_bottom + 30 ) {
				$(this).removeAttr('width').removeAttr('height');
				if ( $(this).closest('.wp-caption', $content).length ) {
					$(this).closest('.wp-caption').addClass('extra-wide').css('max-width','none');
				} else {
					$(this).addClass('extra-wide').css('max-width', '130%');
				}
			}
		});

		// check anything hanging left and move it back
		$('.left, .alignleft, .align-left, .align-center.full.type-embed', $('.entry-content')).each( function() {
			if ( $(this).offset().top < sb_bottom + 30 ) {	// 30px buffer just to keep things looking clean
				$(this).addClass('no-bleed');
			} else {
				return false; //once we're passed the boundary, we can stop checking
			}
		});

	}

	// Newsletter signup form interaction
	$('#site-header .newsletter-signup .email_address').focus(function() {
		$(this).siblings('.toggleable').show();
	});

	$(document).mouseup(function(e) {
		var container = $("#site-header .newsletter-signup");
		if (!container.is(e.target) && container.has(e.target).length === 0)
			container.find('.toggleable').hide();
	});

	$('#site-header .newsletter-signup form').submit(function() {
		var valid = true;
		$('#site-header .newsletter-signup .error').hide();
		$(this).find('input').each(function(){
			if (!$(this)[0].checkValidity()) {
				valid = false;
				$(this).focus();
				$('#site-header .newsletter-signup .error')
					.text('Please complete the signup form.')
					.fadeIn(250);
				return false;
			}
		});
		if (!valid)
			return false;
		else {
			$("#site-header .newsletter-signup").find('.toggleable').hide();
			$('#site-header .newsletter-signup input.submit').attr({ disabled: 'disabled', value: 'Submitted' });
		}
	});

});
