<?php
/**
 * Add some custom javascript to the donation form's page
 * @filter gform_pre_render https://www.gravityhelp.com/documentation/article/gform_pre_render/
 * @link https://www.gravityhelp.com/documentation/article/gform_product_total/
 * @since March 2017
 */
function wcij_donation_form_custom_floppy_bits( $form, $ajax, $field_values ) {
	?>
	<script>
		var wcij_gform_mods = {
			total: 0,

			per_month: function( total ) {
				jQuery( ".showy-hidey" ).addClass( "visuallyhidden" );
				if ( total < 8 ) {
					jQuery( ".friend.monthly.showy-hidey" ).removeClass( "visuallyhidden" );
				}
				else if ( total < 22 ) {
					jQuery( ".investor.monthly.showy-hidey" ).removeClass( "visuallyhidden" );
				}
				else if ( total < 42 ) {
					jQuery( ".benefactor.monthly.showy-hidey" ).removeClass( "visuallyhidden" );
				}
				else if ( total < 84 ) {
					jQuery( ".leader.monthly.showy-hidey" ).removeClass( "visuallyhidden" );
				}
				else if ( total < 417 ) {
					jQuery( ".directors-circle.monthly.showy-hidey" ).removeClass( "visuallyhidden" );
				}
				else if ( 417 <= total ) {
					jQuery( ".presidents-circle.monthly.showy-hidey" ).removeClass( "visuallyhidden" );
				}
			},

			per_year: function( total ) {
				jQuery( ".showy-hidey" ).addClass( "visuallyhidden" );
				if ( total < 100 ) {
					jQuery( ".friend.annual.showy-hidey" ).removeClass( "visuallyhidden" );
				}
				else if ( total < 250 ) {
					jQuery( ".investor.annual.showy-hidey" ).removeClass( "visuallyhidden" );
				}
				else if ( total < 500 ) {
					jQuery( ".benefactor.annual.showy-hidey" ).removeClass( "visuallyhidden" );
				}
				else if ( total < 1000 ) {
					jQuery( ".leader.annual.showy-hidey" ).removeClass( "visuallyhidden" );
				}
				else if ( total < 5000 ) {
					jQuery( ".directors-circle.annual.showy-hidey" ).removeClass( "visuallyhidden" );
				}
				else if ( 5000 <= total ) {
					jQuery( ".presidents-circle.annual.showy-hidey" ).removeClass( "visuallyhidden" );
				}
			},

			one_time: function( total ) {
				jQuery( ".gfield_html.monthly.showy-hidey" ).addClass( "visuallyhidden" );
				jQuery( ".gfield_html.annual.showy-hidey" ).addClass( "visuallyhidden" );
				jQuery( ".gfield_html.one-time.showy-hidey" ).removeClass( "visuallyhidden" );
			},

			gform_product_total: function( total, formId ) {
				switch ( jQuery( "input[name=input_15]:checked" ).val() ) {
					case "Per Month":
						wcij_gform_mods.per_month( total );
						break;
					case "Per Year":
						wcij_gform_mods.per_year( total );
						break;
					case "One Time":
						wcij_gform_mods.one_time( total );
						break;
				}
				// do nothing with the total
				return total;
			},
		}

		gform.addFilter( 'gform_product_total', wcij_gform_mods.gform_product_total );

	</script>
	<?php

	// do nothing to the form
	return $form;
}
// 1 is prod
add_action( 'gform_pre_render_1', 'wcij_donation_form_custom_floppy_bits', 10, 3 );
