<?php
	function wpt_points_post_type() {
		$labels = array(
			'name'               => __( 'Points' ),
			'singular_name'      => __( 'Points' ),
			'add_new'            => __( 'Add New Points' ),
			'add_new_item'       => __( 'Add New Points' ),
			'edit_item'          => __( 'Edit Points' ),
			'new_item'           => __( 'Add New Points' ),
			'view_item'          => __( 'View Points' ),
			'search_items'       => __( 'Search Points' ),
			'not_found'          => __( 'No Points found' ),
			'not_found_in_trash' => __( 'No Points found in trash' ),
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
			'rewrite'              => array( 'slug' => 'points' ),
			'has_archive'          => true,
			'menu_position'        => 30,
			'menu_icon'            => 'dashicons-calendar-alt',
			'register_meta_box_cb' => 'wpt_add_points_metaboxes',
		);
		register_post_type( 'points', $args );
	}
	add_action( 'init', 'wpt_points_post_type' );

  function wpt_points_location() {

		global $post;
		$upload_dir = wp_upload_dir();
		$map_obj = get_field('map_id', $post->ID);
		$map_url = get_field('map_url', $map_obj->ID);
		$map_acf = array(
			'set_max_bounds' => get_field('set_max_bounds', $map_obj->ID),
			'zoom' => get_field('zoom', $map_obj->ID),
			'north_east' => get_field('north_east', $map_obj->ID),
			'south_west' => get_field('south_west', $map_obj->ID)
		);
		$north_east = $map_acf['north_east']['lat'] ? '['.$map_acf['north_east']['lat'].', '.$map_acf['north_east']['lng'].']' : "";
		$south_west = $map_acf['south_west']['lat'] ? '['.$map_acf['south_west']['lat'].', '.$map_acf['south_west']['lng'].']' : "";
		if($map_acf['set_max_bounds'] !== true){
			$north_east =  "";
			$south_west = "";
		}
		echo '
            <div style="margin-bottom: 20px;">
                Lat: <input class="lat" type="text" name="lat" style="width: 250px" />
                Lng: <input class="lng" type="text" name="lng" style="width: 250px" />
            </div>
            <div id="map" style="width: 100%; height: 500px;"
								data-edit = "true"
								data-zoom_min = "'.$map_acf['zoom']['min'].'"
                data-zoom_max = "'.$map_acf['zoom']['max'].'"
								data-north_east = "'.$north_east.'"
                data-south_west = "'.$south_west.'"
                data-mapId="'.$map_obj->ID.'"
                data-mapUrl="'.$map_url.'"
                data-iconUrl="'.plugin_dir_url( __DIR__ ).'images/icons/default.svg"></div>
		';
	}

	function wpt_add_points_metaboxes() {
		add_meta_box(
			'wpt_points_location',
			'View Map',
			'wpt_points_location',
			'points',
			'side',
			'default'
		);
	}
	add_action( 'add_meta_boxes', 'wpt_add_points_metaboxes' );
 ?>