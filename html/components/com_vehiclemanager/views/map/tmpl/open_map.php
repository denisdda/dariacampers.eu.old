<?php
if (!defined('_VALID_MOS') && !defined('_JEXEC')) die('Direct Access to ' . basename(__FILE__) . ' is not allowed.');
/**
 *
 * @package  VehicleManager
 * @copyright 2020 by Ordasoft
 * @author Andrey Kvasnevskiy - OrdaSoft (akbet@mail.ru); Rob de Cleen (rob@decleen.com);
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * Homepage: https://ordasoft.com/
 */

  /*That is OLD code in any case:
  global $vehiclemanager_configuration, $doc, $mosConfig_live_site, $database, $Itemid;*/
  /*// That is last version of the code. If global will be need, uncomment this piece of code:
  global $vehiclemanager_configuration, $mosConfig_live_site, $database;*/

  $doc = JFactory::getDocument();

  $doc->addScript("//cdn.polyfill.io/v2/polyfill.min.js?features=requestAnimationFrame,Element.prototype.classList,URL");
  // $doc->addScript("//openlayers.org/en/v4.6.5/build/ol.js");
  // $doc->addStyleSheet("//openlayers.org/en/v4.6.5/css/ol.css");
  $doc->addScript("//cdnjs.cloudflare.com/ajax/libs/ol3/4.6.5/ol.js");
  $doc->addStyleSheet("//cdnjs.cloudflare.com/ajax/libs/ol3/4.6.5/ol.css");
     ?>

<style>

      .os_ol_popup {
        min-width: <?php echo $vehiclemanager_configuration['fotogal']['width'] ; ?>px;
        max-width: <?php echo $vehiclemanager_configuration['fotogal']['width'] ; ?>px;
      }
</style>
      <script type="text/javascript">

        // Instead of two function below (window.addEvent and window.onload) we use the function vm_loc_map_addLoadEvent in the end of script. That is optimal solution in many case: when we use a few modules VehicleManager Location Map on one page; when we display tab Location map on view my_vehicle...
        /*window.addEvent('domready', function() {
            vm_initialize2<?php // echo $map_uniq_id; ?>();
        });*/
        /*window.onload = function() {
            vm_initialize2<?php // echo $map_uniq_id; ?>();
        };*/
      var ol_point;
      var map_view;
      var map<?php echo $map_uniq_id; ?>;
      var pointFeature;
      var map_layer;

      function vm_initialize2<?php echo $map_uniq_id; ?>(){

        //if map already created: refresh
        if (map<?php echo $map_uniq_id; ?> instanceof ol.Map) {
            map<?php echo $map_uniq_id; ?>.updateSize();
            return ;
        }

          var container = document.getElementById('os_ol_popup<?php echo $map_uniq_id; ?>');
          var content = document.getElementById('os_ol_popup-content<?php echo $map_uniq_id; ?>');
          var closer = document.getElementById('os_ol_popup-closer<?php echo $map_uniq_id; ?>');

          // Create an overlay to anchor the popup to the map.
          var overlay = new ol.Overlay({
            element: container,
            autoPan: true,
            autoPanAnimation: {
              duration: 250
            }
          });

          // Add a click handler to hide the popup. @return {boolean} Don't follow the href.
          closer.onclick = function() {
            overlay.setPosition(undefined);
            closer.blur();
            return false;
          };


          var lon = Number(<?php echo protectInjectionWithoutQuote('vh_lon',''); ?>);
          var lat = Number(<?php echo protectInjectionWithoutQuote('vh_lat',''); ?>);

          // var london = ol.proj.fromLonLat([-0.12755, 51.507222]);
          var lonLat = ol.proj.fromLonLat([lon, lat]);

          map_view = new ol.View({
            // center: london,
            center: lonLat,
            zoom: 5
          });

          map<?php echo $map_uniq_id; ?> = new ol.Map({
            layers: [
              new ol.layer.Tile({
                source: new ol.source.OSM()
              })
            ],
            target: 'vm_map_canvas<?php echo $map_uniq_id; ?>',
            controls: ol.control.defaults({
              attributionOptions: {
                collapsible: false
              }
            }),
            overlays: [overlay], //for popup
            view: map_view
          });

      var imgCatalogPath = "<?php echo $mosConfig_live_site; ?>/components/com_vehiclemanager/";
      ol_point = "";
      pointFeatures = new Array() ;
      pointFeaturesArray = new Array();


        <?php
          //$newArr = explode(",", _VEHICLE_MANAGER_LOCATION_MARKER);
          $j = 0;
          for ($i = 0;$i < count($rows);$i++) {
            if ($rows[$i]->vlatitude && $rows[$i]->vlongitude) {
                if( is_file($mosConfig_live_site.'components/com_vehiclemanager/images/marker-'.$rows[$i]->vtype.'.png') ){

                     $numPick = '/images/marker-'.$rows[$i]->vtype.'.png';
                }
                else  $numPick = '/images/marker-2.png';   

          ?>

              var srcForPic = "<?php echo $numPick; ?>";
              var image = '';
              if(srcForPic.length){
                  var image_point_src = imgCatalogPath + srcForPic;
              } else var image_point_src = imgCatalogPath + "/images/marker-2.png";

            <?php
                if (strlen($rows[$i]->vtitle) > 45)
                    $vtitle = mb_substr($rows[$i]->vtitle, 0, 25) . '...';
                else {
                    $vtitle = $rows[$i]->vtitle;
                }
            ?>
                var title =  "<?php echo $database->Quote( $vtitle ) ?>";
            <?php
                //for local images
                $imageURL = ($rows[$i]->image_link);
                if ($imageURL == '') $imageURL = _VEHICLE_MANAGER_NO_PICTURE_BIG;

                $watermark_path = ($vehiclemanager_configuration['watermark']['show'] == 1) ? 'watermark/' : '';
                $watermark = ($vehiclemanager_configuration['watermark']['show'] == 1) ? true : false;
                $file_name = vm_picture_thumbnail($imageURL,
                    $vehiclemanager_configuration['fotogal']['width'],
                    $vehiclemanager_configuration['fotogal']['high'], $watermark);

                $file = $mosConfig_live_site . '/components/com_vehiclemanager/photos/' . $file_name;
            ?>
                var imgUrl =  "<?php echo $file; ?>";

            <?php if(!incorrect_price($rows[$i]->price)):?>
              var price =  "<?php echo $rows[$i]->price; ?>";
              var priceunit =  "<?php echo $rows[$i]->priceunit; ?>";
            <?php else:?>
              var price =  "<?php echo $rows[$i]->price; ?>";
              var priceunit="";
            <?php endif;

            // For mod_vehiclemanagerr_location_map:
            if ( !isset($new_target) ) $new_target = '_blank';                

            ?>



             var contentStr = '<div>'+
                '<a onclick=window.open("<?php echo JRoute::_("index.php?option=com_vehiclemanager&task=view_vehicle&id={$rows[$i]->id}&catid={$rows[$i]->idcat}&Itemid={$Itemid}");?>",target="<?php echo $new_target; ?>") >'+
               '<img width = "100%" src = "'+imgUrl+'">'+
               '</a>' +
                  '</div>'+
                  '<div id="marker_link">'+
                      '<a onclick=window.open("<?php echo JRoute::_("index.php?option=com_vehiclemanager&task=view_vehicle&id={$rows[$i]->id}&catid={$rows[$i]->idcat}&Itemid={$Itemid}");?>",target="<?php echo $new_target; ?>") >' + title + '</a>'+
                  '</div>'+
                  '<div id="marker_price">'+
                      '<a onclick=window.open("<?php echo JRoute::_("index.php?option=com_vehiclemanager&task=view_vehicle&id={$rows[$i]->id}&catid={$rows[$i]->idcat}&Itemid={$Itemid}");?>",target="<?php echo $new_target; ?>") >' + price +' ' + priceunit + '</a>'+
              '</div>';

              ol_point = ol.proj.fromLonLat([<?php echo $rows[$i]->vlongitude; ?>, <?php echo $rows[$i]->vlatitude; ?>]);
              pointFeature = new ol.Feature({
                geometry:new ol.geom.Point(ol_point),
                image_point_src: image_point_src, 
                os_text_cnt: contentStr,
              } ) ;
              pointFeatures.push( 1 );
              //pointFeaturesArray[<?php echo $i ; ?>] = pointFeature ;
              pointFeaturesArray.push( pointFeature) ;


              // var styleMarkerCache = {};
              // map_layer = new ol.layer.Vector({
              //       source: new ol.source.Vector({
              //         features: [pointFeature]
              //       }),
              //       style: function(feature) {
              //         console.log(":1111111111:");
              //         console.log(feature.get('features'));
              //         var size = feature.get('features').length;
              //         if(size != 1 ) return ;
              //         var style = styleMarkerCache[size];
              //         if (!style) {
              //           style = new ol.style.Style({
              //             image: new ol.style.Icon(/** @type {olx.style.IconOptions} */ ({
              //               anchor: [0.52, 31],
              //               anchorXUnits: 'fraction',
              //               anchorYUnits: 'pixels',
              //               opacity: 0.95,
              //               src: image_point_src
              //             }))
              //            }
              //           );
              //           styleMarkerCache[size] = style;
              //         }
              //         return style;
              //       },
              //       os_text_cnt:contentStr,
              //     });

              //  map<?php //echo $map_uniq_id; ?>.addLayer(map_layer);

// var array = [];
//       for (var i in layer._layers) {
//         array.push(layer._layers[i]);
//       }
//       return this.removeLayers(array);






              <?php
              $j++;
            }
          }
           ?>


          if(ol_point != "") {

            //Start group marker- cluster
            var source = new ol.source.Vector({
              features: pointFeaturesArray
            });

            var clusterSource = new ol.source.Cluster({
              distance: 40,
              source: source
            });

            var styleClusterCache = {};
            var clusters = new ol.layer.Vector({
              source: clusterSource,
              style: function(feature) {
                var size = feature.get('features').length;

                if(size == 1 ){
                  var style = styleClusterCache[ feature.get('features')[0].get('image_point_src') ];
                } else {
                  var style = styleClusterCache[size];
                }


                if (!style) {
                  if(size == 1 ){
                      style = new ol.style.Style({
                        image: new ol.style.Icon(/** @type {olx.style.IconOptions} */ ({
                          anchor: [0.52, 31],
                          anchorXUnits: 'fraction',
                          anchorYUnits: 'pixels',
                          opacity: 0.95,
                          src: feature.get('features')[0].get('image_point_src') 
                        }))
                       }
                      );
                  } else {

                    style = new ol.style.Style({
                      image: new ol.style.Icon(/** @type {olx.style.IconOptions} */ ({
                        anchor: [0.51, 28],
                        anchorXUnits: 'fraction',
                        anchorYUnits: 'pixels',
                        opacity: 0.95,
                        src: "<?php echo $mosConfig_live_site.'/components/com_vehiclemanager/images/m1.png' ; ?>"
                      })),
                      text: new ol.style.Text({
                        text: size.toString(),
                        fill: new ol.style.Fill({
                          color: 'black'
                        }),
                        font: '14px "Helvetica Neue", Arial'
                      })
                      
                    });
                  }


                  styleClusterCache[size] = style;
                }
                return style;
              }
            });
            map<?php echo $map_uniq_id; ?>.addLayer(clusters);
            //end group marker- cluster

             var select= new ol.interaction.Select({layers : map_layer });
             var selectedFeature=select.getFeatures().item(0); //the selected feature

  //          map.on('singleclick', function(evt) {
            map<?php echo $map_uniq_id; ?>.on('pointermove', function(evt) {
              var show_info = map<?php echo $map_uniq_id; ?>.forEachFeatureAtPixel(evt.pixel, function(feature, layer) {
                  //you can add a condition on layer to restrict the listener
                  var size = feature.get('features').length;
                  if(size == 1) return feature.get('features')[0].get('os_text_cnt')
                  else if ( size > 1 ){
                   
                    return "";
                  } else return "";

                  });
              if ( show_info !="" && show_info != undefined ) {
                  //here you can add you code to display the coordinates or whatever you want to do
                  //console.log("aaaaaaaaaaaaa",[layer.get('os_text_cnt')]);
                  var coordinate = evt.coordinate;
                  // var hdms = ol.coordinate.toStringHDMS(ol.proj.transform(
                  //     coordinate, 'EPSG:3857', 'EPSG:4326'));

                  content.innerHTML = show_info ;

                  overlay.setPosition(coordinate);
              } ;

              //SET MOUSE CURSOR
              var hit = this.forEachFeatureAtPixel(evt.pixel, function(feature, layer) {
                  return true;
              }); 
              if (hit) {
                  this.getTargetElement().style.cursor = 'pointer';
              } else {
                  this.getTargetElement().style.cursor = '';
              }

            });
            // added because pointermove - not work on mobile
            map<?php echo $map_uniq_id; ?>.on('singleclick', function(evt) {
              var show_info = map<?php echo $map_uniq_id; ?>.forEachFeatureAtPixel(evt.pixel, function(feature, layer) {
                  //you can add a condition on layer to restrict the listener
                  var size = feature.get('features').length;
                  if(size == 1) return feature.get('features')[0].get('os_text_cnt')
                  else if ( size > 1 ){
                    var coordinate = evt.coordinate;
                    map<?php echo $map_uniq_id; ?>.getView().setCenter( coordinate );
                    var zoom = map<?php echo $map_uniq_id; ?>.getView().getZoom();
                    map<?php echo $map_uniq_id; ?>.getView().setZoom( zoom + 2);
                    
                    return "";
                  } else return "";

                  });
              if ( show_info !="" && show_info != undefined ) {
                  //here you can add you code to display the coordinates or whatever you want to do
                  //console.log("aaaaaaaaaaaaa",[layer.get('os_text_cnt')]);
                  var coordinate = evt.coordinate;
                  // var hdms = ol.coordinate.toStringHDMS(ol.proj.transform(
                  //     coordinate, 'EPSG:3857', 'EPSG:4326'));

                  content.innerHTML = show_info ;

                  overlay.setPosition(coordinate);
              }else {
                //HIDE MARKER INFO WINDOW
                 closer = document.getElementById('os_ol_popup-closer<?php echo $map_uniq_id; ?>');
                 closer.click();
              };

            });
            

            //chnage map zoom and bound all markers
            if( pointFeatures.length > 1 && ol_point != "" ){
              //Create an empty extent that we will gradually extend
              var extent = ol.extent.createEmpty();
              pointFeaturesArray.forEach(function(feature) {
                 ol.extent.extend(extent, feature.getGeometry().getExtent() );
              });


              //Finally fit the map's view to our combined extent
              map<?php echo $map_uniq_id; ?>.getView().fit(extent, map<?php echo $map_uniq_id; ?>.getSize());
            var zoom = map<?php echo $map_uniq_id; ?>.getView().getZoom();

            if(zoom > 26 ) map<?php echo $map_uniq_id; ?>.getView().setZoom( zoom - 8 );
            else if(zoom > 20 ) map<?php echo $map_uniq_id; ?>.getView().setZoom( zoom - 5 );
            else if(zoom > 16 ) map<?php echo $map_uniq_id; ?>.getView().setZoom( zoom - 2 );
                            
            } else if( pointFeatures.length == 1 && ol_point != ""){
//            console.log("333333333333333333333333333333333333333333");
              map<?php echo $map_uniq_id; ?>.getView().setCenter( ol_point );
              map<?php echo $map_uniq_id; ?>.getView().setZoom(10);
            }




//             //chnage map zoom and bound all markers
//             if( pointFeatures.length > 1 && ol_point != "" ){
//               //Create an empty extent that we will gradually extend
//               var extent = ol.extent.createEmpty();
//               var set_extent = false ;
// //console.log("11111111111111111:",extent);

//               map<?php echo $map_uniq_id; ?>.getLayers().forEach(function(layer) {
//                   //If this is actually a group, we need to create an inner loop to go through its individual layers
//                   if(layer instanceof ol.layer.Group) {
//                       layer.getLayers().forEach(function(groupLayer) {
//                           //If this is a vector layer, add it to our extent
//                           if(groupLayer instanceof ol.layer.Vector){
//                               ol.extent.extend(extent, groupLayer.getSource().getExtent());
//                               set_extent =  true ;
//                           }
//                       });
//                   }
//                   else if(layer instanceof ol.layer.Vector){
//                      ol.extent.extend(extent, layer.getSource().getExtent());
//                      set_extent =  true ;
//                   } 

//               });

//               if( set_extent ) {
//                 //Finally fit the map's view to our combined extent
//                 map<?php echo $map_uniq_id; ?>.getView().fit(extent, map<?php echo $map_uniq_id; ?>.getSize());
//               } else {
//              console.log("222222222222222222222");
//             //     map<?php echo $map_uniq_id; ?>.getView().setCenter( ol_point );
//             //     map<?php echo $map_uniq_id; ?>.getView().setZoom(7);
//               }
//             } else if( pointFeatures.length == 1 && ol_point != ""){
//             console.log("333333333333333333333333333333333333333333");
//               map<?php echo $map_uniq_id; ?>.getView().setCenter( ol_point );
//               map<?php echo $map_uniq_id; ?>.getView().setZoom(10);
//             }

          }

        }


        setTimeout('vm_initialize2<?php echo $map_uniq_id; ?>()',50);

            /*function vm_loc_map_addLoadEvent(func) {
             var oldonload = window.onload;
             if (typeof window.onload != 'function') {
                window.onload = func;
              } else {
                window.onload = function() {
                  if (oldonload) {
                    oldonload();
                  }
                  func();
                }
              }
            }
          vm_loc_map_addLoadEvent(vm_initialize2<?php echo $map_uniq_id; ?>());*/

      </script>
