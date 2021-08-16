<?php
if (!defined('_VALID_MOS') && !defined('_JEXEC')) die('Direct Access to ' . basename(__FILE__) . ' is not allowed.');
/**
*
* @package VehicleManager
* @copyright 2020 by Ordasoft
* @author Andrey Kvasnevskiy - OrdaSoft (akbet@mail.ru); Rob de Cleen (rob@decleen.com);
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* Homepage: https://ordasoft.com/
*
* */
/*That is OLD code in any case:
  global $vehiclemanager_configuration, $doc, $mosConfig_live_site, $database, $Itemid;*/
  /*// That is last version of the code. If global will be need, uncomment this piece of code:
  global $vehiclemanager_configuration, $mosConfig_live_site, $database;*/

    // $doc = JFactory::getDocument();
    /*$api_key = "key=" . $vehiclemanager_configuration['api_key'] ;
    $doc->addScript("//maps.googleapis.com/maps/api/js?$api_key");*/

$doc = JFactory::getDocument();
if ($vehiclemanager_configuration['google_openmap']['show'] == '1') {
    if ( isset($vehiclemanager_configuration['api_key']) && $vehiclemanager_configuration['api_key'] ) {
        $api_key = "key=" . $vehiclemanager_configuration['api_key'];
    } else {
      $api_key = JFactory::getApplication()->enqueueMessage("<a target='_blank' href='//developers.google.com/maps/documentation/geocoding/get-api-key'>" . _VEHICLE_MANAGER_GOOGLEMAP_API_KEY_LINK_MESSAGE . "</a>", _VEHICLE_MANAGER_GOOGLEMAP_API_KEY_ERROR);
    }
    if ( checkJavaScriptIncludedVEH("maps.googleapis.com") === false ) {
        // $doc->addScript("//maps.googleapis.com/maps/api/js?$api_key&libraries=places");
        $doc->addScript("//maps.googleapis.com/maps/api/js?$api_key&libraries=places",'text/javascript', true,true);
    }
}
?>

      <script type="text/javascript">
      // Instead of two function below (window.addEvent and window.onload) we use the function vm_loc_map_addLoadEvent in the end of script. That is optimal solution in many case: when we use a few modules VehicleManager Location Map on one page; when we display tab Location map on view my_vehicle...
      /*window.addEvent('domready', function() {
          vm_initialize2<?php // echo $map_uniq_id; ?>();
      });*/
      /*window.onload = function() {
          vm_initialize2<?php // echo $map_uniq_id; ?>();
      };*/


      function vm_initialize2<?php echo $map_uniq_id; ?>(){
          var map;
          var marker = new Array();
          var myOptions = {
      <?php
              if ( isset($params) && $params->get('scroll_wheel') == '1' ) echo "scrollwheel: true,";
              else echo "scrollwheel: false,";?>
              <?php if ( isset($params) && $params->get('menu_map') == '0' ) echo "
              mapTypeControl: false,";
              else echo "
              mapTypeControl: true,
              mapTypeControlOptions: {
                  style: google.maps.MapTypeControlStyle.HORIZONTAL_BAR,
                  position: google.maps.ControlPosition.TOP_CENTER
              }, "; ?>
              <?php if ( isset($params) && $params->get('control_map') == '0' ) echo "
              zoomControl: false,
              panControl: false,
              streetViewControl: false";
              else echo "
              zoomControl: true,
              zoomControlOptions: {
                  style: google.maps.ZoomControlStyle.DEFAULT
              },
              panControl: true,
              streetViewControl: true,
              streetViewControlOptions: {
                  position: google.maps.ControlPosition.LEFT_BOTTOM
              } "; 
            ?>
              //mapTypeId: google.maps.MapTypeId.ROADMAP

          };
          var imgCatalogPath = "<?php echo $mosConfig_live_site; ?>/components/com_vehiclemanager/";
          var map = new google.maps.Map(document.getElementById("vm_map_canvas<?php echo $map_uniq_id; ?>"), myOptions);
          var bounds = new google.maps.LatLngBounds ();

          var oldZoom = null;
          google.maps.event.addListenerOnce(map, "zoom_changed", function() { 
            oldZoom = map.getZoom();  

                    map.setZoom(oldZoom > 16 ? oldZoom-2 : oldZoom);       
          });

          var remove_id_list = [];

          <?php
          //$newArr = explode(",", _VEHICLE_MANAGER_LOCATION_MARKER);
          $j = 0;
          for ($i = 0;$i < count($rows);$i++) {
            if ($rows[$i]->vlatitude && $rows[$i]->vlongitude) {
                if( is_file($mosConfig_live_site.'components/com_vehiclemanager/images/marker-'.$rows[$i]->vtype.'.png') ){

                     $numPick = '/images/marker-'.$rows[$i]->vtype.'.png';
                }
                else  $numPick = '/images/marker-1.png';   

          ?>

              var srcForPic = "<?php echo $numPick; ?>";
              var image = '';
              if(srcForPic.length){
                  var image = new google.maps.MarkerImage(imgCatalogPath + srcForPic,
                      new google.maps.Size(32, 39),
                      new google.maps.Point(0,0),
                      new google.maps.Point(50, 50));
              }

              marker.push(new google.maps.Marker({
                  icon: image,
                  position: new google.maps.LatLng(<?php echo $rows[$i]->vlatitude; ?>,
                   <?php echo $rows[$i]->vlongitude; ?>),
                  map: map,
                  title: "<?php echo $database->Quote($rows[$i]->vtitle); ?>"
              }));

              bounds.extend(new google.maps.LatLng(<?php echo $rows[$i]->vlatitude; ?>,
                <?php echo $rows[$i]->vlongitude; ?>));

              var infowindow  = new google.maps.InfoWindow({});

              info_wind_<?php echo $map_uniq_id; ?> = function() {
                <?php
                if (strlen($rows[$i]->vtitle) > 45)
                    $vtitle = mb_substr($rows[$i]->vtitle, 0, 25) . '...';
                else {
                    $vtitle = $rows[$i]->vtitle;
                }
                ?>
                var title =  "<?php echo $database->Quote( $vtitle ) ; ?>";
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

                   infowindow.setContent(contentStr);
                   infowindow.setOptions( { maxWidth: <?php echo $vehiclemanager_configuration['fotogal']['width'] ; ?> });
                   infowindow.open(map,marker[<?php echo $j; ?>]);
              };


              google.maps.event.addListener(marker[<?php echo $j; ?>], 'mouseover', info_wind_<?php echo $map_uniq_id; ?> );
              //mousedown - need because mouseover not work for mobile
              google.maps.event.addListener(marker[<?php echo $j; ?>], 'mousedown', info_wind_<?php echo $map_uniq_id; ?> );
              
              var myLatlng = new google.maps.LatLng(<?php echo $rows[$i]->vlatitude;
               ?>,<?php echo $rows[$i]->vlongitude; ?>);
              var myZoom = <?php echo $rows[$i]->map_zoom; ?>;
              <?php
              $j++;
            }
          }
  ?>
          if (<?php echo $j; ?> > 1){
            map.fitBounds(bounds);

            // Add a marker clusterer to manage the markers.
            var markerCluster = new MarkerClusterer(map, marker,
                {imagePath: 'https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/m'});
          } 
          else if (<?php echo $j; ?>==1) {map.setCenter(myLatlng);map.setZoom(myZoom)}
          else {map.setCenter(new google.maps.LatLng(0,0));map.setZoom(0);}


        }

        if (typeof vm_loc_map_addLoadEvent !== "function")
        {
            function vm_loc_map_addLoadEvent(func) {
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

        }


      vm_loc_map_addLoadEvent(vm_initialize2<?php echo $map_uniq_id; ?>);

</script>
<script src="https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/markerclusterer.js">
</script>

