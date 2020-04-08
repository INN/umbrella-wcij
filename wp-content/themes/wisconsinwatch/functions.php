<?php
// This site is an INN Member
if ( !defined( 'INN_MEMBER' ) ) {
    define( 'INN_MEMBER', true) ;
}

// This site is hosted by INN
if ( !defined( 'INN_HOSTED' ) ) {
    define( 'INN_HOSTED', true) ;
}

/**
 * Largo APIs
 */
require_once( get_template_directory() . '/largo-apis.php' );

/**
 * include child theme function files
 *
 * @link https://github.com/INN/largo/issues/1494
 */
function wcij_includes() {
	$includes = array(
		'/inc/custom-post-types.php',
		'/inc/metaboxes.php',
		'/inc/donation-form.php',
	);

	// Perform load
	foreach ( $includes as $include ) {
		if ( 0 === validate_file( get_stylesheet_directory() . $include ) ) {
			require_once( get_stylesheet_directory() . $include );
		}
	}
}
add_action( 'after_setup_theme', 'wcij_includes', 10 ); // must run after function Largo() which runs with priority 10


/**
 * Enqueue editor styles
 */
function wcij_editor_styles() {
	$suffix = ( LARGO_DEBUG )? '' : '.min';
	add_editor_style( 'css/child' . $suffix . '.css' );
}
add_action( 'after_setup_theme', 'wcij_editor_styles', 15 ); // running at 15 so it comes after Gutenberg's styles

/**
 * Include compiled style.css
 */
function child_stylesheet() {
	wp_dequeue_style( 'largo-child-styles' );
	$suffix = ( LARGO_DEBUG )? '' : '.min';
	wp_enqueue_style(
		'wcij',
		get_stylesheet_directory_uri() . '/css/child' . $suffix . '.css',
		null,
		filemtime( get_stylesheet_directory() . '/css/child' . $suffix . '.css' )
	);
}
add_action( 'wp_enqueue_scripts', 'child_stylesheet', 20 );

/**
 * register an extra image size used for cat/tax archive pages
 */
add_image_size( 'rect_thumb', 800, 600, true );

/**
 * add a widget area like the "article bottom" area but at the bottom of blog posts
 */
function wcij_register_sidebars() {
	register_sidebar( array(
		'name' 			=> __( 'Blog Post Bottom', 'largo' ),
		'id' 			=> 'blog-post-bottom',
		'description' 	=> __( 'A unique widget area for the bottom of blog posts (as opposed to articles)', 'largo' ),
		'before_widget' => '<aside id="%1$s" class="%2$s clearfix">',
		'after_widget' 	=> "</aside>",
		'before_title' 	=> '<h3 class="widgettitle">',
		'after_title' 	=> '</h3>',
	) );
	register_sidebar( array(
		'name' 			=> __( 'Header Newsletter Signup', 'wcij' ),
		'id' 			=> 'header-newsletter-signup',
		'description' 	=> __( 'Displayed at the top of the homepage on desktop, and on no other pages.', 'largo' ),
		'before_widget' => '<aside id="%1$s" class="%2$s clearfix">',
		'after_widget' 	=> "</aside>",
		'before_title' 	=> '<h3 class="widgettitle">',
		'after_title' 	=> '</h3>',
	) );
}
add_action( 'widgets_init', 'wcij_register_sidebars' );

/**
 * allow shortcodes and oembed in text widgets
 */
add_filter( 'widget_text', 'do_shortcode', 20 );
add_filter( 'widget_text', array( $wp_embed, 'autoembed'), 8 );

/**
 * Enqueue JS
 */
function largo_child_enqueue() {
	wp_enqueue_script(
		'largo-child',
		get_stylesheet_directory_uri() . '/js/largo-child.js',
		array('jquery'),
		filemtime( get_stylesheet_directory() . '/js/largo-child.js' )
	);
}
add_action( 'wp_enqueue_scripts', 'largo_child_enqueue' );

/**
 * Don't strip links surrounding images
 */
function wcij_after_setup_theme() {
	remove_filter( 'the_content', 'largo_attachment_image_link_remove_filter');
}
add_action('after_setup_theme', 'wcij_after_setup_theme', 99);

/**
 * Add typekit
 */
function wcij_typekit() { ?>
	<script type="text/javascript" src="//use.typekit.net/dpf3ziv.js"></script>
	<script type="text/javascript">try{Typekit.load();}catch(e){}</script>
<?php
}
add_action( 'wp_head', 'wcij_typekit' );

function wcij_tinymce_allowed_tags() {
	global $allowedposttags;
	$allowedposttags['script'] = array(
		'type' => array(),
		'src' => array()
	);
}
add_action( 'init', 'wcij_tinymce_allowed_tags' );

function wcij_mip_metrics_tag() { ?>
	<!-- MIP Google Tag Manager -->
	<noscript><iframe src="//www.googletagmanager.com/ns.html?id=GTM-W8RSCN"
	height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
	<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
	new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
	j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
	'//www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
	})(window,document,'script','mipData','GTM-W8RSCN');</script>
	<!-- End MIP Google Tag Manager -->
<?php
}
add_action( 'wp_head', 'wcij_mip_metrics_tag' );

/**
 * Header actions for tagline
 * @since Largo 0.5.5
 */
function wcij_largo_header_before_largo_header() {
	if ( is_front_page() || is_home() ) {
		?>
			<h3>Protect the Vulnerable <span>&#183;</span> Expose Wrongdoing <span>&#183;</span> Explore Solutions</h3>
		<?php
	}
}
add_action( 'largo_header_before_largo_header', 'wcij_largo_header_before_largo_header' );

/**
 * Header actions for newsletter signup form
 * @since Largo 0.5.5
 */
function wcij_largo_header_after_largo_header() {
	if ( is_front_page() || is_home() ) {
	?>
	<div class="newsletter-signup">
		<?php
			dynamic_sidebar( 'header-newsletter-signup' );
		?>
	</div>
	<?php
	}
}
add_action( 'largo_header_after_largo_header', 'wcij_largo_header_after_largo_header' );
