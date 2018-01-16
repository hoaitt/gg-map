<?php
add_action( 'wp_ajax_block_action', 'block_action' ); // Use this action for logged in users
add_action( 'wp_ajax_nopriv_block_action', 'block_action' ); // Use this action for visitors

function block_action() {
  $mapID = $_GET['mapid'];
  $map_filters = $_GET['map_filters'];
  $map_obj = get_post($mapID);
  $arrayMap = [];
  $data_return = array(
    "title" => $map_obj->post_title,
    "data" => array()
  );

  $args = array(
    'post_type'		=> 'points',
    'posts_per_page'	=> -1,
    'meta_query'	=> array(
      'relation' => 'OR'
    ),
  );
  // query
  if(isset($map_filters)){
    $filters_arr = explode(', ', $map_filters);
    foreach($filters_arr as $filter) {
      array_push($args['meta_query'], array(
        'key' => 'point_type',
        'value' => $filter,
        'compare' => 'LIKE'
      ));
    }
  }
  $wp_query = new WP_Query( $args );
  while( $wp_query->have_posts() ) : $wp_query->the_post();
    $rows_acf = array(
      'map_id' => get_field('map_id',get_the_id()),
      'point_lat_lng' => get_field('point_lat_lng',get_the_id()),
      'icon' => get_field('icon',get_the_id()),
      'point_type' => get_field('point_type',get_the_id())
    );

    if($rows_acf['map_id']->ID == $mapID){
      $cat_arr = get_the_terms(get_the_id(), 'points_taxonomy');
      $ar = [
        "post_id" => get_the_id(),
        "title" => get_the_title(),
        "cat" => array(),
        "locations" => array()
      ];
      if( $rows_acf['point_lat_lng'] ) {
        foreach($rows_acf['point_lat_lng'] as $key => $row) {
          $ar['locations'][] = array(
            "latlng"=> array(
              "lat"=> (float)$row['lat'],
              "lng"=> (float)$row['lng'],
            ),
            "icon" => $rows_acf['icon'],
            "image" => $row['image'],
            "description" => $row['description'],
            "url" => $row['url']
          );
        }
      }
      if( $cat_arr ) {
        foreach($cat_arr as $key => $row) {
          $ar['cat'][] = array(
            "cat_id" => $row->term_id,
            "cat_name" => $row->name
          );
        }
      }
      $arrayMap[] = $ar;
    }
  endwhile;
  $data_return['data'] = $arrayMap;

  header('Content-Type: application/json');
  echo json_encode($data_return);
  die();
}



?>