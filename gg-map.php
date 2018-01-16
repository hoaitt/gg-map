<?php
/**
 * Plugin Name: Green Global Map
 * Plugin URI:
 * Description: Plugin tạo bản đồ cho các khu vui chơi giải trí của SunGroup.
 * Version: 1.0
 * Author: Thanh Hoai
 * Author URI:
 * License: GPLv2
 */
?>
<?php
    if ( !defined('MAP_FILTERS') )
        define('MAP_FILTERS', 'field_5a5c1af572004');

    function dump($data){
        echo '<pre>';
        var_dump($data);
        echo '</pre>';

    }
    function add_svg_to_upload_mimes( $upload_mimes ) {
        $upload_mimes['svg'] = 'image/svg+xml';
        $upload_mimes['svgz'] = 'image/svg+xml';
        return $upload_mimes;
    }
    add_filter( 'upload_mimes', 'add_svg_to_upload_mimes', 10, 1 );

    require_once( plugin_dir_path( __FILE__ ) . 'includes/map_post_type.php' );
    require_once( plugin_dir_path( __FILE__ ) . 'includes/map_save_post.php' );
    require_once( plugin_dir_path( __FILE__ ) . 'includes/map_api.php' );
    require_once( plugin_dir_path( __FILE__ ) . 'includes/map_shortcode.php' );
    // require_once( plugin_dir_path( __FILE__ ) . 'includes/map_custom_field.php' );
    // require_once( plugin_dir_path( __FILE__ ) . 'includes/acf-php-recovery.php' );

    require_once( plugin_dir_path( __FILE__ ) . 'includes/point_post_type.php' );
    require_once( plugin_dir_path( __FILE__ ) . 'includes/point_taxonomy.php' );
    require_once( plugin_dir_path( __FILE__ ) . 'includes/point_js_css.php' );
?>