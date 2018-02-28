<?php
	function wpt_maps_post_type() {
		$labels = array(
			'name'               => __( 'Maps' ),
			'singular_name'      => __( 'Maps' ),
			'add_new'            => __( 'Add New Maps' ),
			'add_new_item'       => __( 'Add New Maps' ),
			'edit_item'          => __( 'Edit Maps' ),
			'new_item'           => __( 'Add New Maps' ),
			'view_item'          => __( 'View Maps' ),
			'search_items'       => __( 'Search Maps' ),
			'not_found'          => __( 'No maps found' ),
			'not_found_in_trash' => __( 'No maps found in trash' ),
			// 'taxonomies'          => array( 'category' ),
		);
		$supports = array(
			'title',
			'editor',
			'thumbnail'
		);
		$args = array(
			'labels'               => $labels,
			'supports'             => $supports,
			'public'               => true,
			'capability_type'      => 'post',
			'rewrite'              => array( 'slug' => 'maps' ),
			'has_archive'          => true,
			'menu_position'        => 30,
			'menu_icon'            => 'dashicons-calendar-alt',
			'register_meta_box_cb' => 'wpt_add_maps_metaboxes',
		);
		register_post_type( 'maps', $args );
	}
	add_action( 'init', 'wpt_maps_post_type' );

	function wpt_maps_location() {

		global $post;
		$upload_dir = wp_upload_dir();
		$map_url = get_field('map_url', $post->ID);
		$map_acf = array(
			'set_max_bounds' => get_field('set_max_bounds', $post->ID),
			'zoom' => get_field('zoom', $post->ID),
			'north_east' => get_field('north_east', $post->ID),
			'south_west' => get_field('south_west', $post->ID)
		);
		$north_east = $map_acf['north_east']['lat'] ? '['.$map_acf['north_east']['lat'].', '.$map_acf['north_east']['lng'].']' : "";
		$south_west = $map_acf['south_west']['lat'] ? '['.$map_acf['south_west']['lat'].', '.$map_acf['south_west']['lng'].']' : "";
		if($map_acf['set_max_bounds'] !== true){
			$north_east =  "";
			$south_west = "";
		}
		$str = '<div style="margin-bottom: 20px;">
                Lat: <input class="lat" type="text" name="lat" style="width: 250px" />
                Lng: <input class="lng" type="text" name="lng" style="width: 250px" />
            </div>
            <div id="map" style="width: 100%; height: 500px;"
                data-edit = "true"
                data-zoom_min = "'.$map_acf['zoom']['min'].'"
                data-zoom_max = "'.$map_acf['zoom']['max'].'"
                data-north_east = "'.$north_east.'"
                data-south_west = "'.$south_west.'"
                data-mapId="'.$post->ID.'"
                data-mapUrl="'.$map_url.'"
								data-iconUrl="'.plugin_dir_url( __DIR__ ).'images/icons/default.svg"></div>';
		if($map_url){
			echo $str;
		}
	}

	function wpt_add_maps_metaboxes() {
		add_meta_box(
			'wpt_maps_location',
			'View Map',
			'wpt_maps_location',
			'maps',
			'side',
			'default'
		);
	}
	add_action( 'add_meta_boxes', 'wpt_add_maps_metaboxes' );
 ?>