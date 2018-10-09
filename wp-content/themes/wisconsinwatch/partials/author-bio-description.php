<?php
/**
 * Author bio template used on author archives and the Largo Author Bio Widget
 *
 * This file references many variables that are not explicitly set in
 *     var_export( $author_obj );
 * or in
 *     get_object_vars( $author_obj );
 *
 * That these variables work at all is because those variables are being
 * fetched by the WP_User class' magic function __get():
 * https://codex.wordpress.org/Class_Reference/WP_User#get.28_.24key_.29
 * https://developer.wordpress.org/reference/classes/wp_user/__get/
 *
 * @see inc/widgets/largo-author-bio.php
 * @link https://secure.helpscout.net/conversation/677513636/2558/?folderId=1219602
 * @link https://github.com/INN/largo/issues/1471
 * @since Largo 0.5.5.4
 */

/*
 * Author name
 *
 * Because this template is displayed in widgets and archives alike,
 * it makes sense to not link to the archive on the archive's own page.
 */
if ( is_author() ) {
	echo '<h1 class="fn n">' . $author_obj->display_name . '</h1>';
} else {
	printf( __( '<h3 class="widgettitle">About <span class="fn n"><a class="url" href="%1$s" rel="author" title="See all posts by %2$s">%2$s</a></span></h3>', 'largo' ),
		get_author_posts_url( $author_obj->ID, $author_obj->user_nicename ),
		esc_attr( $author_obj->display_name )
	);
}

/*
 * Avatar
 *
 * Supports:
 * 
 * - Largo's own custom avatar function
 * - gravatar
 * - Co-Authors Plus Guest Authors
 * - Presspoint/Paupress
 */
if ( largo_has_avatar( $author_obj->user_email ) || $author_obj->paupress_pp_avatar ) {
	echo '<div class="photo">' . get_avatar( $author_obj->ID, 96, '', $author_obj->display_name ) . '</div>';
} elseif ( $author_obj->type == 'guest-author' && get_the_post_thumbnail( $author_obj->ID ) ) {
	$photo = get_the_post_thumbnail( $author_obj->ID, array( 96,96 ) );
	$photo = str_replace( 'attachment-96x96 wp-post-image', 'avatar avatar-96 photo', $photo );
	echo '<div class="photo">' . $photo . '</div>';
}

/*
 * Job Titles
 *
 * Supports:
 * - Largo's custom job titles
 * - PauPress/Presspoint profile titles
 */
$show_job_titles = of_get_option('show_job_titles');
if ( $show_job_titles ) {
	if ( ! empty( $author_obj->job_title ) ) {
		echo '<p class="job-title">' . esc_attr( $author_obj->job_title ) . '</p>';
	} else if ( !empty( $author_obj->title ) ) {
		echo '<p class="job-title">' . esc_attr( $author_obj->title ) . '</p>';
	}
}

/*
 * Description
 *
 * Supports WordPress' native 'description' meta, and therefore:
 * - Largo's custom user descriptions
 * - Presspoint's custom user descriptions
 */
if ( $author_obj->description ) {
	echo '<p>' . esc_attr( $author_obj->description ) . '</p>';
}
