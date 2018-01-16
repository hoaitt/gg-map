// A $( document ).ready() block.
(function( $ ) {
    'use strict';
    function getParameterByName(name, url) {
        if (!url) url = window.location.href;
        name = name.replace(/[\[\]]/g, "\\$&");
        var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
            results = regex.exec(url);
        if (!results) return null;
        if (!results[2]) return '';
        return decodeURIComponent(results[2].replace(/\+/g, " "));
    }
    $( document ).ready(function() {
      var map;
      var map_filters = $('#map-filters');
      var mapID;
      var iconurl;
      var markers = [];
      function handleData(data){
        $.each(data, function(key, post){
          if(post.locations){
            $.each(post.locations, function(k, location){
              addMarker(location, post)
            })
          }
        })
        var group = new L.featureGroup(markers);
        map.fitBounds(group.getBounds());
        setTimeout(function(){
          panToDefaultMarker();
        },1000)
      }
      function showLatLng(latlng){
        var lat = $('.lat');
        var lng = $('.lng');
        lat.val(latlng.lat)
        lng.val(latlng.lng)
      }
      function addIconDefault(options){
        var icon = {};
        var icone = {
              image: options.icon || iconurl,
              width: 20,
              height: 20,
            }
        icon.marker = L.icon({
          iconUrl: icone.icon,
          iconSize:     [icone.width/2, icone.height/2],
          iconAnchor:   [icone.width/4, icone.height/4],
          popupAnchor:  [0, -1*(icone.height/4)],
        });
        icon.marker_resize = {
          "1":L.icon({
            iconUrl: icone.image,
            iconSize:     [icone.width*0.5/2, icone.height*0.5/2],
            iconAnchor:   [icone.width*0.5/4, icone.height*0.5/4],
            popupAnchor:  [0, -1*(icone.height/4)],
          }),
          "2":L.icon({
            iconUrl: icone.image,
            iconSize:     [icone.width*0.75, icone.height*0.75],
            iconAnchor:   [icone.width*0.75/2, icone.height*0.75/2],
            popupAnchor:  [0, -1*(icone.height/2)],
          }),
          "3":L.icon({
            iconUrl: icone.image,
            iconSize:     [icone.width*1.20, icone.height*1.20],
            iconAnchor:   [icone.width*1.20/2, icone.height*1.20/2],
            popupAnchor:  [0, -1*(icone.height*4/2)],
          }),
          "4":L.icon({
            iconUrl: icone.image,
            iconSize:     [icone.width*1.5, icone.height*1.5],
            iconAnchor:   [icone.width*1.5/2, icone.height*1.5/2],
            popupAnchor:  [0, -1*(icone.height*4/2)],
          }),
          "5":L.icon({
            iconUrl: icone.image,
            iconSize:     [icone.width*3, icone.height*3],
            iconAnchor:   [icone.width*3/2, icone.height*3/2],
            popupAnchor:  [0, -1*(icone.height*4/2)],
          })
        }
        icon.popup_resize = {
          "1":new L.Point(0, -(icone.height/4)+10),
          "2":new L.Point(0, -(icone.height/2)+10),
          "3":new L.Point(0, -icone.height+10),
          "4":new L.Point(0, -icone.height+10),
          "5":new L.Point(0, -icone.height*2+10)
        }
        return icon;
      }
      function addMarker(location, content){
        if(!location.latlng) return false;
        var zoom = map._zoom;
        var opt = {
          'draggable' : true,
          'clickable' : true,
          'clicked': false,
          'clean': false,
          'map' : map,
        };
        var icone = addIconDefault(location);
        opt.icon = icone.marker_resize[zoom];
        if(icone.marker_resize != undefined){
          opt.icon_resize = icone.marker_resize;
        }
        if(icone.popup_resize != undefined){
          opt.popup_resize = icone.popup_resize;
        }
        var marker = L.marker(location.latlng, opt).addTo(map);
        if(location.description){
          marker.popup = L.popup(popupOptions())
          .setLatLng(location.latlng)
          .setContent(popupContent(location, content))
        }
        if(content){
          marker.content = content;
        }
        markers.push(marker)
        marker.on('click dragend',function(mk){
          showLatLng(mk.target._latlng)
          if(marker.popup){
            marker.popup.setLatLng(mk.target._latlng).openOn(map)
          }
        });
      }
      function popupContent(location, content){
        var str;
        if(location.url){
          str = '<a class="ggmap-popup" target="_blank" href="'+location.url+'">';
        }else{
          str = '<div class="ggmap-popup">';
        }
        if(location.image){
          str += '<div class="ggmap-contentleft"><img src="'+location.image+'" style="max-width:100%"/></div>';
        }
        str += '<div class="ggmap-contentright">';
        if(content && content.title){
          str += '<h3 class="ggmap-title">'+content.title+'</h3>';
        }
        if(location && location.description){
          str += '<div>'+location.description+'</div>';
        }
        str += '</div>';
        if(location.url){
          str += '</a>';
        }else{
          str += '</div>';
        }
        return str;
      }
      function popupOptions(options){
        if(!options) return;
        options.offset = new L.Point(10, 10);
        return options;
      }
      function moveToMarker(id){
        id = id||"";
        if(!id) return false;
        $.each(markers, function(key, item){
          if(item.content && item.content.post_id == id){
            map.setView(item.getLatLng(),3, {pan:{ animate:true}});
            setTimeout(function(){
              item.fireEvent('click');
            },500)
          }
        })
      }
      function panToDefaultMarker(){
        if(getParameterByName("location") || getParameterByName("post")){
          var location_id = getParameterByName("location") || getParameterByName("post")
          moveToMarker(location_id);
        }
      }
      function removeAllMarkers(){
        $.each(markers, function(key, item){
          map.removeLayer(item);
        })
        markers = [];
        map.closePopup();
      }
      function getData(map_filter){
        var params = {
          action: "block_action",
          mapid: mapID,
          map_filters: map_filter
        }
        $.ajax({
          type: 'GET',
          url: ajaxObj.ajaxurl,
          data: params,
          success: function(data) {
            removeAllMarkers();
            setTimeout(function(){
              handleData(data.data);
            },500)
          },
          error: function(error) { console.log('error', error) }
        }) // End of AJAX
      }
      function initMap(){
        var mapElm = $('#map');
        if(mapElm.length){
          var data = mapElm.data();
          iconurl = data.iconurl;
          mapID = data.mapid;
          var tiles = L.tileLayer(data.mapurl, {
            minZoom: 1,
            maxZoom: 5,
            noWrap: true,
            tms: true
          });

          map = L.map('map', {
            center: [0, 0],
            zoom:1,
            minZoom: 1,
            maxZoom: 5,
            layers:[tiles],
          });
          var northEast = '';
          var southWest = '';
          if(data.north_east){
            northEast = L.latLng(data.north_east[0], data.north_east[1]);
          }
          if(data.south_west){
            southWest = L.latLng(data.south_west[0], data.south_west[1]);
          }
          if(southWest && southWest){
            var bounds = new L.LatLngBounds(southWest, northEast);
            map.setMaxBounds(bounds);
            setTimeout(function(){
              map.panTo(bounds.getCenter());
            })
          }

          if(data.edit) {
            map.on('click', function(e){
              addMarker({'latlng':e.latlng});
              showLatLng(e.latlng);
            });
          }
          map.on('zoomend',function(){
            if(!markers) return false;
            var zoom = this.getZoom();
            if(markers){
              $.each(markers, function(key, marker){
                if(marker.options.icon_resize != undefined){
                  marker.setIcon(marker.options.icon_resize[zoom])
                }
                if(marker.options.popup_resize != undefined && marker.popup){
                  marker.popup.options.offset = marker.options.popup_resize[zoom]
                  map.closePopup();
                }
              })
            }
          });
        }
      }
      if(map_filters.length){
        map_filters.on('change', function(e){
          var val = $(this).val();
          getData(val);
        })
      }
      initMap();
      getData();
    });

})( jQuery );