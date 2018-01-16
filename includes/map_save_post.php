<?php

function custom_button(){
    $current_post_type =  get_post_type();

    $html  = '<div id="major-publishing-actions" style="overflow:hidden">';
    $html .= '<div id="publishing-action">';
    $html .= '<input type="submit" accesskey="p" tabindex="5" value="Generate Map" class="button-primary" id="custom" name="create_map">';
    $html .= '</div>';
    $html .= '</div>';
    if($current_post_type == 'maps'){
        echo $html;
    }
}
add_action( 'post_submitbox_misc_actions', 'custom_button' );

function map_save_post( $post_id ) {

    // If this is just a revision, don't send the email.
    if ( wp_is_post_revision( $post_id ) )
        return;

    if(isset($_POST['create_map']) && has_post_thumbnail()){
        require 'libs/maptiler.php';
        $feat_image = get_attached_file( get_post_thumbnail_id($post_id));
        $upload_dir = wp_upload_dir();
        $basedir = $upload_dir['basedir'];
        $tiles_path = $basedir.'/maps/map-'.$post_id;
        if(isset($feat_image)){
            $map_tiler = new MapTiler($feat_image, array(
                'tiles_path' => $tiles_path,
                'zoom_min' => 0,
                'zoom_max' => 5,
                'format' => 'png',
                'fill_color' => '#dddddd',
                'force' => true
            ));
            //execute
            try {
                $map_tiler->process(true);
                add_post_meta($post_id, 'map_url', $upload_dir['baseurl'].'/maps/map-'.$post_id.'/{z}/{x}/{y}.png', true);
            } catch (Exception $e) {
                echo $e->getMessage();
                echo '<br>';
                echo $e->getTraceAsString();
                exit();
            }
        }
    }

}
add_action( 'save_post', 'map_save_post' );
?>