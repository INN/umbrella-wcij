jQuery(document).ready(function($) {
	var $fieldBylinesCheckbox = $( '#site_uses_bylines' )
		$fieldNoBylinePolicy = $( '#no_byline_policy' );

	if ( $fieldBylinesCheckbox.prop( 'checked' ) ) {
		$fieldNoBylinePolicy.closest( 'tr' ).hide();
	}

	$fieldBylinesCheckbox.change(function() {
		$fieldNoBylinePolicy.closest( 'tr' ).toggle();
	});

	var $singlePage = $( '#sitewide_policies_single_page' )
		$singlePageDisable = $( '.not-single' );

	if ( $singlePage.prop( 'checked' ) ) {
		$singlePageDisable.addClass( 'deemphasize' );
	}

	$singlePage.change(function() {
		$singlePageDisable.toggleClass( 'deemphasize' );
	});
});
