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
      var mapData;
      var map_filters = $('.map-filters');
      var mapID;
      var iconurl;
      var markers = [];
      let win = $(window);
      let annotate_link = $('.annotate .annotate-link');

      function resize(){
        $('#map').css({
          'width' : $('.wrap').width(),
          'height' : '550px',
        });
      }
      resize();
      win.on('resize', ()=>{
        resize();
      });
      function handleData(data){
        $.each(data, function(key, post){
          if(post.locations){
            $.each(post.locations, function(k, location){
              addMarker(location, post)
            })
          }
        })
        if(markers.length){
          var group = new L.featureGroup(markers);
          map.setMinZoom(3);
          map.fitBounds(group.getBounds());
          map.setMinZoom(mapData.zoom_min);
          showHideMarker();
          setTimeout(function(){
            panToDefaultMarker();
          },1000)
        }
      }
      function showLatLng(latlng, oldLatLng){
        if(mapData.edit) {
          var lat = $('.lat');
          var lng = $('.lng');

          lat.val(latlng.lat);
          lng.val(latlng.lng);

          if(oldLatLng){
            var inputField = $('[data-key="field_5a55d07ae87ef"] .acf-row').not('.acf-clone');
            $.each(inputField, function(){
              var _this = $(this);
              var input = _this.find('input');
              var inputLat = $(input[0]);
              var inputLng = $(input[1]);
              if(inputLat.val() == oldLatLng.lat && inputLng.val() == oldLatLng.lng){
                inputLat.val(latlng.lat)
                inputLng.val(latlng.lng)
              }
            })
          }
        }
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
            iconSize:     [icone.width*2, icone.height*2],
            iconAnchor:   [icone.width*2/2, icone.height*2/2],
            popupAnchor:  [0, -1*(icone.height*4/2)],
          }),
          "6":L.icon({
            iconUrl: icone.image,
            iconSize:     [icone.width*2, icone.height*2],
            iconAnchor:   [icone.width*2/2, icone.height*2/2],
            popupAnchor:  [0, -1*(icone.height*4/2)],
          }),
        }
        icon.popup_resize = {
          "1":new L.Point(0, -(icone.height/4)+10),
          "2":new L.Point(0, -(icone.height/2)+10),
          "3":new L.Point(0, -icone.height+10),
          "4":new L.Point(0, -icone.height+10),
          "5":new L.Point(0, -icone.height*2+10),
          "6":new L.Point(0, -icone.height*2+10)
        }
        return icon;
      }
      function addMarker(location, content){
        if(!location.latlng) return false;
        var zoom = map._zoom;
        var opt = {
          'draggable' : false,
          'clickable' : true,
          'clicked': false,
          'clean': false,
          'map' : map,
        };
        if(mapData.edit) {
          opt.draggable = true;
        }
        var icone = addIconDefault(location);
        opt.icon = icone.marker_resize[zoom];
        if(icone.marker_resize != undefined){
          opt.icon_resize = icone.marker_resize;
        }
        if(icone.popup_resize != undefined){
          opt.popup_resize = icone.popup_resize;
        }
        var marker = L.marker(location.latlng, opt).addTo(map);
        if(content){
          marker.content = content;
          if(content.title){
            marker.popup = L.popup(popupOptions({}))
            .setLatLng(location.latlng)
            .setContent(popupContent(location, content))
          }

        }
        markers.push(marker)
        marker.on('click dragend',function(mk){
          showLatLng(mk.target._latlng, marker.oldLatLng);
          if(marker.popup){
            marker.popup.setLatLng(mk.target._latlng).openOn(map)
          }
        });
        marker.on('dragstart',function(mk){
          marker.oldLatLng = mk.target._latlng
        });
        marker.on('mouseover',function(mk){
          if(content.title){
            marker.bindTooltip(content.title, tooltipOptions({})).openTooltip();;
          }
        });
      }
      function popupContent(location, content){
        var str;
        if(content.url){
          str = '<a class="ggmap-popup" target="_blank" href="'+content.url+'">';
        }else{
          str = '<div class="ggmap-popup">';
        }
        if(content.image){
          str += '<div class="ggmap-contentleft" style="background-image:url('+content.image+')"></div>';
        }
        str += '<div class="ggmap-contentright">';
        if(content && content.title){
          str += '<h3 class="ggmap-title">'+content.title+'</h3>';
        }
        if(content && content.description){
          str += '<div class="ggmap-desc">'+content.description+'</div>';
        }
        str += '</div>';
        if(content.url){
          str += '</a>';
        }else{
          str += '</div>';
        }
        return str;
      }
      function popupOptions(options){
        if(!options) return;
        options.offset = new L.Point(10, 10);
        options.maxWidth = "400";
        options.minWidth = "300";
        return options;
      }
      function tooltipOptions(options){
        if(!options) return;
        options.sticky = true;
        // options.offset = new L.Point(10, 10);
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
      function panToDefaultMarker(location_id){
        if(location_id){
          moveToMarker(location_id);
        }else{
          if(getParameterByName("location") || getParameterByName("post")){
            location_id = getParameterByName("location") || getParameterByName("post")
            moveToMarker(location_id);
          }
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
          mapData = data;
          iconurl = mapData.iconurl;
          mapID = mapData.mapid;
          var tiles = L.tileLayer(mapData.mapurl, {
            minZoom: mapData.zoom_min || 2,
            maxZoom: mapData.zoom_max || 6,
            noWrap: true,
            tms: true
          });

          map = L.map('map', {
            center: [0, 0],
            zoom:1,
            minZoom: mapData.zoom_min || 2,
            maxZoom: mapData.zoom_max || 6,
            layers:[tiles],
            zoomControl: false,
            // dragging: false,
            // tap: false
          });
          var northEast = '';
          var southWest = '';
          if(mapData.north_east){
            northEast = L.latLng(mapData.north_east[0], mapData.north_east[1]);
          }
          if(mapData.south_west){
            southWest = L.latLng(mapData.south_west[0], mapData.south_west[1]);
          }
          if(southWest && southWest){
            var bounds = new L.LatLngBounds(southWest, northEast);
            map.setMaxBounds(bounds);
            setTimeout(function(){
              map.panTo(bounds.getCenter());
            })
          }

          if(mapData.edit) {
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
                showHideMarker();
              })
            }
          });
          map.addControl(new L.Control.Zoomslider({
            stepHeight: 15
          }));
          map.addControl(new L.Control.Fullscreen());
          mapElm.on("touchstart", onTwoFingerDrag);
          mapElm.on("touchend", onTwoFingerDrag);
        }

      }
      function onTwoFingerDrag (e) {
        // console.log(e.originalEvent.touches);
        if (e.originalEvent.type === 'touchstart' && e.originalEvent.touches.length === 1) {
          $(this).addClass('swiping')
          disableMap(true);
        }
        if (e.originalEvent.type === 'touchstart' && e.originalEvent.touches.length > 1) {
          $(this).removeClass('swiping')
          disableMap(false);
        }

        if (e.originalEvent.type === 'touchend') {
          $(this).removeClass('swiping');
          disableMap(false);
        }
      }
      function disableMap(disable){
        if(disable){
          // map.scrollWheelZoom.disable();
          // map.touchZoom.disable();
          // map.doubleClickZoom.disable();
          // map.boxZoom.disable();
          // map.keyboard.disable();
          map.dragging.disable();
          if(map.tap){
            map.tap.disable();
          }
        }else{
          // map.scrollWheelZoom.enable();
          // map.touchZoom.enable();
          // map.doubleClickZoom.enable();
          // map.boxZoom.enable();
          // map.keyboard.enable();
          map.dragging.enable();
          if(map.tap){
            map.tap.enable();
          }
        }
      }
      function showHideMarker(){
        var zoom = map.getZoom();
        if(markers){
          $.each(markers, function(key, marker){
            if(zoom<3){
              map.removeLayer(marker)
            }else{
              marker.addTo(map);
            }
          })
        }
      }
      if(map_filters.length){
        map_filters.find('input').on('change', function(e){
          var val_arr = [];
          map_filters.find('input:checked').each(function () {
            val_arr.push($(this).val());
          });
          getData(val_arr.join(', '));
        })
      }
      if(annotate_link.length){
        annotate_link.click(function(){
          var location_id = $(this).data().location_id;
          if(location_id){
            panToDefaultMarker(location_id);
          }
        })
      }
      initMap();
      getData();
    });
})( jQuery );