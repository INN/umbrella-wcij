<?php
/**
 * Compatibility functions for the Republication Tracker Tool plugin
 *
 * @link https://github.com/INN/republication-tracker-tool
 * @link https://wordpress.org/plugins/republication-tracker-tool/
 */

/**
* Hide the Republication sharing widget on posts that are
* included in the downloads post type
*
* @return bool Whether or not the sharing widget should be hidden
* @link https://secure.helpscout.net/conversation/1016863827/4711?folderId=2730118 convo leading to why
* @since Republication Tracker Tool version 1.0.2
*/
function remove_republish_button_from_downloads( $hide_republication_widget, $post ){
	if( true !== $hide_republication_widget ){
		// if the current post is in this category, return true
		if ( 'download' === $post->post_type ) {
			// returning true will cause the filter to hide the button
			$hide_republication_widget = true;
		}
	}
	return $hide_republication_widget;
}
add_filter( 'hide_republication_widget', 'remove_republish_button_from_downloads', 10, 2 );
