<?php
function delTree($dir)
{
    $files = array_diff(scandir($dir), array('.', '..'));

    foreach ($files as $file) {
        (is_dir("$dir/$file")) ? delTree("$dir/$file") : unlink("$dir/$file");
    }

    return rmdir($dir);
}

function custom_button($post_id){
    $current_post_type =  get_post_type();
    $post_acf = array(
        'image' => get_field('image', $post_id)
    );
    $html  = '<div id="major-publishing-actions" style="overflow:hidden">';
    $html .= '<div id="publishing-action">';
    $html .= '<input type="submit" accesskey="p" tabindex="5" value="Generate Map" class="button-primary" id="custom" name="create_map">';
    $html .= '</div>';
    $html .= '</div>';
    if($current_post_type == 'maps' && isset($post_acf['image'])){
        echo $html;
    }
}
add_action( 'post_submitbox_misc_actions', 'custom_button' );

function map_save_post( $post_id ) {

    // If this is just a revision, don't send the email.
    if ( wp_is_post_revision( $post_id ) )
        return;
    if(isset($_POST['create_map'])){
        require 'libs/maptiler.php';
        $feat_image = get_attached_file( get_post_thumbnail_id($post_id));
        $post_acf = array(
            'image' => get_field('image', $post_id)
        );
        $upload_dir = wp_upload_dir();
        $basedir = $upload_dir['basedir'];
        $tiles_path = $basedir.'/maps/map-'.$post_id;
        if(isset($post_acf['image'])):
            foreach($post_acf['image'] as $key => $item) :
                $post_acf['image'][$key]['image_path'] = get_attached_file($item['ID']);
            endforeach;
        endif;
        if (file_exists($tiles_path)) {
            delTree($tiles_path);
        }
        $map_tiler_option = array(
            'tiles_path' => $tiles_path,
            'zoom_min' => 1,
            'zoom_max' => 2,
            'format' => 'png',
            'fill_color' => '#dddddd',
            'force' => true
        );
        if(metadata_exists('post', $post_id, 'map_url')){
            update_post_meta($post_id, 'map_url', $upload_dir['baseurl'].'/maps/map-'.$post_id.'/{z}/{x}/{y}.png');
        }else{
            add_post_meta($post_id, 'map_url', $upload_dir['baseurl'].'/maps/map-'.$post_id.'/{z}/{x}/{y}.png', true);
        }
        if(isset($post_acf['image']['level_1'])){
            $image_path = $post_acf['image']['level_1']['image_path'];
            $map_tiler_option['zoom_min'] = 1;
            $map_tiler_option['zoom_max'] = 2;
            $map_tiler = new MapTiler($image_path, $map_tiler_option);
            //execute
            try {
                $map_tiler->process(true);
            } catch (Exception $e) {
                echo $e->getMessage();
                echo '<br>';
                echo $e->getTraceAsString();
                exit();
            }
        }
        if(isset($post_acf['image']['level_2'])){
            $image_path = $post_acf['image']['level_2']['image_path'];
            $map_tiler_option['zoom_min'] = 3;
            $map_tiler_option['zoom_max'] = 4;
            $map_tiler = new MapTiler($image_path, $map_tiler_option);
            //execute
            try {
                $map_tiler->process(true);
            } catch (Exception $e) {
                echo $e->getMessage();
                echo '<br>';
                echo $e->getTraceAsString();
                exit();
            }
        }
        if(isset($post_acf['image']['level_3'])){
            $image_path = $post_acf['image']['level_3']['image_path'];
            $map_tiler_option['zoom_min'] = 5;
            $map_tiler_option['zoom_max'] = 6;
            $map_tiler = new MapTiler($image_path, $map_tiler_option);
            //execute
            try {
                $map_tiler->process(true);
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