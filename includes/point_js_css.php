<?php
	function my_admin_footer_function() {
		wp_enqueue_script( 'leaflet-script', plugin_dir_url( __DIR__ ) . 'js/libs/leaflet/leaflet.js', array( 'jquery' ), '20141010', true );
		wp_enqueue_script( 'leaflet-zoomslider-script', plugin_dir_url( __DIR__ ) . 'js/libs/leaflet/l.control.zoomslider.js', array( 'jquery' ), '20141010', true );
		wp_enqueue_script( 'leaflet-fullscreen-script', plugin_dir_url( __DIR__ ) . 'js/libs/leaflet/leaflet.fullscreen.js', array( 'jquery' ), '20141010', true );
		wp_enqueue_script( 'leaflet-scriptsmaps',  plugin_dir_url( __DIR__ ) . 'js/scriptsmaps.js', array( 'jquery' ), '20141010', true );
		wp_localize_script( 'leaflet-scriptsmaps', 'ajaxObj', array(
			'ajaxurl' => admin_url( 'admin-ajax.php' )
			) );
	}
	add_action('admin_footer', 'my_admin_footer_function');
	function myadmin_stylesheet() {
		wp_enqueue_style( 'leaflet-zoomslider-css', plugin_dir_url( __DIR__ ) . 'js/libs/leaflet/l.control.zoomslider.css' );
		wp_enqueue_style( 'leaflet-fullscreen-css', plugin_dir_url( __DIR__ ) . 'js/libs/leaflet/leaflet.fullscreen.css' );
		wp_enqueue_style( 'admin_css', plugin_dir_url( __DIR__ ) . 'js/libs/leaflet/leaflet.css' );
		wp_enqueue_style( 'ggmap_css', plugin_dir_url( __DIR__ ) . 'css/style.css' );
	}
	add_action('admin_head', 'myadmin_stylesheet' );


	function twentyfifteen_scripts() {
		wp_enqueue_script( 'leaflet-script', plugin_dir_url( __DIR__ ) . 'js/libs/leaflet/leaflet.js', array( 'jquery' ), '20141010', true );
		wp_enqueue_script( 'leaflet-zoomslider-script', plugin_dir_url( __DIR__ ) . 'js/libs/leaflet/l.control.zoomslider.js', array( 'jquery' ), '20141010', true );
		wp_enqueue_script( 'leaflet-fullscreen-script', plugin_dir_url( __DIR__ ) . 'js/libs/leaflet/leaflet.fullscreen.js', array( 'jquery' ), '20141010', true );
		wp_enqueue_script( 'leaflet-scriptsmaps',  plugin_dir_url( __DIR__ ) . 'js/scriptsmaps.js', array( 'jquery' ), '20141010', true );
		wp_localize_script( 'leaflet-scriptsmaps', 'ajaxObj', array(
			'ajaxurl' => admin_url( 'admin-ajax.php' )
			) );
			wp_enqueue_style( 'leaflet-zoomslider-css', plugin_dir_url( __DIR__ ) . 'js/libs/leaflet/l.control.zoomslider.css' );
			wp_enqueue_style( 'leaflet-fullscreen-css', plugin_dir_url( __DIR__ ) . 'js/libs/leaflet/leaflet.fullscreen.css' );
			wp_enqueue_style( 'leaflet-css', plugin_dir_url( __DIR__ ) . 'js/libs/leaflet/leaflet.css' );
		wp_enqueue_style( 'ggmap_css', plugin_dir_url( __DIR__ ) . 'css/style.css' );
	}
	add_action( 'wp_enqueue_scripts', 'twentyfifteen_scripts' );
?>