<?php
function points_init() {
	$labels = array(
		'name'              => _x( 'Points Group', 'taxonomy general name', 'gg-map' ),
		'singular_name'     => _x( 'Points Group', 'taxonomy singular name', 'gg-map' ),
		'search_items'      => __( 'Search Points Group', 'gg-map' ),
		'all_items'         => __( 'All Points Group', 'gg-map' ),
		'parent_item'       => __( 'Parent Points Group', 'gg-map' ),
		'parent_item_colon' => __( 'Parent Points Group:', 'gg-map' ),
		'edit_item'         => __( 'Edit Points Group', 'gg-map' ),
		'update_item'       => __( 'Update Points Group', 'gg-map' ),
		'add_new_item'      => __( 'Add New Points Group', 'gg-map' ),
		'new_item_name'     => __( 'New Points Group Name', 'gg-map' ),
		'menu_name'         => __( 'Points Group', 'gg-map' ),
	);

	$args = array(
		'hierarchical'      => true,
		'labels'            => $labels,
		'show_ui'           => true,
		'show_admin_column' => true,
		'query_var'         => true,
		'rewrite'           => array( 'slug' => 'points_taxonomy' ),
	);
	register_taxonomy( 'points_taxonomy', array( 'points' ), $args );
}
add_action( 'init', 'points_init' );


function taxonomy_save_image($term_id) {
	$isCat = array();
	foreach (get_categories(array('hide_empty' => 0)) as $key => $value) {
		$isCat[] = $value->term_taxonomy_id;
	}
	if (!in_array($term_id,$isCat)) {
		taxonomy_update_image( $term_id );
	}
}
add_action( 'edit_term', 'taxonomy_save_image', 10, 3 );

?>