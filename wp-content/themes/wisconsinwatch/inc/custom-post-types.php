<?php
function wcij_post_types() {
  $labels = array(
    'name'               => 'Partner Downloads',
    'singular_name'      => 'Partner Download',
    'add_new'            => 'Add New',
    'add_new_item'       => 'Add Download Page',
    'edit_item'          => 'Edit Download Page',
    'new_item'           => 'New Download Page',
    'all_items'          => 'All Download Pages',
    'view_item'          => 'View Download Page',
    'search_items'       => 'Search Partner Downloads',
    'not_found'          => 'No entries found',
    'not_found_in_trash' => 'No entrie found in Trash',
    'parent_item_colon'  => '',
    'menu_name'          => 'Downloads'
  );

  $args = array(
    'labels'             => $labels,
    'public'             => true,
    'publicly_queryable' => true,
    'exclude_from_search'=> true,
    'show_ui'            => true,
    'show_in_menu'       => true,
    'query_var'          => true,
    'rewrite'            => array( 'slug' => 'download-content' ),
    'capability_type'    => 'post',
    'taxonomies'		 => array( 'category', 'post_tag', 'series' ),
    'has_archive'        => true,
    'hierarchical'       => false,
    'menu_position'      => 20,
    'supports'           => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'custom-fields' )
  );

  register_post_type( 'download', $args );
}
add_action( 'init', 'wcij_post_types' );