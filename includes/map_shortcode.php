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
      $map .= '<div class="map-filters">';
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
      <div id="map" style="width: 100%; height: 500px;"
          data-zoom_min = "'.$map_acf['zoom']['min'].'"
          data-zoom_max = "'.$map_acf['zoom']['max'].'"
          data-north_east = "'.$north_east.'"
          data-south_west = "'.$south_west.'"
          data-mapId="'.$map_id.'"
          data-mapUrl="'.$map_url.'"
          data-iconUrl="'.plugin_dir_url( __DIR__ ).'images/icons/default.svg"></div>
    ';
    return $map;
  }
  add_shortcode( 'show_map', 'create_shortcode' );
?>