<?php
  //Khởi tạo function cho shortcode
  function create_shortcode($args, $content) {
    $map_filters = get_field_object(MAP_FILTERS);
    $map = "";
    if(!isset($args['map_id'])) return '';
    $map_id = $args['map_id'];
    $map_url = get_field('map_url', $map_id);
    $map_acf = array(
      'set_max_bounds' => get_field('set_max_bounds', $map_id),
      'zoom' => get_field('zoom', $map_id),
			'north_east' => get_field('north_east', $map_id),
			'south_west' => get_field('south_west', $map_id)
		);
		$north_east = $map_acf['north_east']['lat'] ? '['.$map_acf['north_east']['lat'].', '.$map_acf['north_east']['lng'].']' : "";
    $south_west = $map_acf['south_west']['lat'] ? '['.$map_acf['south_west']['lat'].', '.$map_acf['south_west']['lng'].']' : "";
    if($map_acf['set_max_bounds'] !== true){
			$north_east =  "";
			$south_west = "";
		}
    if(isset($map_filters) && $map_filters){
      $index = 0;
      $map .= '<h3 style="font-size: 20px">Lọc theo: </h3><div class="map-filters">';
      foreach($map_filters['choices'] as $key => $item){
        $index++;
        $map .= '<div class="form-check">
        <input type="checkbox" value="'.$item.'" id="defaultCheck'.$index.'" name="map_filters[]">
        <label class="form-check-label" for="defaultCheck'.$index.'">
        '.$item.'
        </label>
        </div>';
      }
      $map .= '</div>';
    }
    $map .= '
      <div id="map" style="max-width:100%;max-height:100%;width: 500px; height: 500px;"
          data-zoom_min = "'.$map_acf['zoom']['min'].'"
          data-zoom_max = "'.$map_acf['zoom']['max'].'"
          data-north_east = "'.$north_east.'"
          data-south_west = "'.$south_west.'"
          data-mapId="'.$map_id.'"
          data-mapUrl="'.$map_url.'"
          data-iconUrl="'.plugin_dir_url( __DIR__ ).'images/icons/default.svg"></div>
    ';

    $points_taxonomy = get_terms(array(
      'taxonomy' => 'points_taxonomy',
      'hide_empty' => false,
    ));
    if(isset($points_taxonomy)):
      $map .= '<div class="map-annotate"><div class="list-annotate card-columns">';
      foreach($points_taxonomy as $item) :
        $args = array(
          'post_type' => 'points',
          'posts_per_page' => -1,
          'tax_query' => array(
            array(
            'taxonomy' => 'points_taxonomy',
            'field' => 'term_id',
            'terms' => $item->term_id,
            )
          )
        );
        $wp_query = new WP_Query( $args );


        $map .= '<div class="annotate card">';
        $map .= '<h3 class="annotate-title">'.$item->name.'</h3>';
        if ( $wp_query->have_posts() ):
          $map .= '<ul>';
          while ( $wp_query->have_posts() ) : $wp_query->the_post();
            $acf_array = array(
              'icon' => get_field('icon', get_the_ID())
            );
            // dump($acf_array);
            $map .= '<li><a class="annotate-link" href="#" data-location_id="'.get_the_ID().'">';
            if(isset($acf_array['icon'])):
              $map .= '<img class="annotate-icon" src="'.$acf_array['icon'].'"/>';
            endif;
            $map .= get_the_title().'</a></li>';
          endwhile;
          $map .= '</ul>';
        endif;
        wp_reset_query();
        $map .= '</div>';
      endforeach;
      $map .= '</div></div>';
    endif;


    return $map;
  }
  add_shortcode( 'show_map', 'create_shortcode' );
?>