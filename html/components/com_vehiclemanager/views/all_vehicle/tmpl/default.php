<?php
if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' );
/**
*
* @package VehicleManager
* @copyright 2020 by Ordasoft
* @author Andrey Kvasnevskiy - OrdaSoft (akbet@mail.ru); Rob de Cleen (rob@decleen.com);
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* Homepage: https://ordasoft.com/
*
* */
$session = JFactory::getSession();
$arr = $session->get("array", "default");

global $hide_js, $Itemid, $mosConfig_live_site, $mosConfig_absolute_pat,$database, $my;
global $limit, $total, $limitstart, $task, $paginations, $mainframe, $vehiclemanager_configuration, $option;
if(!empty($params['page_heading']) && $params['show_page_heading'] != 0){
    $veh_header = $params['page_heading'];
} else {
    isset($currentcat->header)?$veh_header = $currentcat->header:$veh_header='';
}

//$watermark_path = ($vehiclemanager_configuration['watermark']['show'] == 1) ? 'watermark/' : '';
$watermark = ($vehiclemanager_configuration['watermark']['show'] == 1) ? true : false;

if($option != 'com_vehiclemanager') PHP_vehiclemanager::showTabs();

?>
<!--[if IE]>
<style type="text/css">
  .basictable {
    zoom: 1;     /* triggers hasLayout */
    }  /* Only IE can see inside the conditional comment
    and read this CSS rule. Don't ever use a normal HTML
    comment inside the CC or it will close prematurely. */
</style>
<![endif]-->

<script type="text/javascript">
    function vm_rent_request_submitbutton() {
        var form = document.userForm;
        if (form.user_name.value == "") {
            alert( "<?php echo _VEHICLE_MANAGER_INFOTEXT_JS_RENT_REQ_NAME; ?>" );
        } else if (form.user_email.value == "" || !vm_isValidEmail(form.user_email.value)) {
            alert( "<?php echo _VEHICLE_MANAGER_INFOTEXT_JS_RENT_REQ_EMAIL; ?>" );
        } else if (form.user_mailing == "") {
            alert( "<?php echo _VEHICLE_MANAGER_INFOTEXT_JS_RENT_REQ_MAILING; ?>" );
        } else if (form.rent_until.value == "") {
            alert( "<?php echo _VEHICLE_MANAGER_INFOTEXT_JS_RENT_REQ_UNTIL; ?>" );
        } else {
            form.submit();
        }
    }

    function vm_isValidEmail(str) {
        return (str.indexOf("@") > 1);
    }

    function vm_allreordering(){
      if(document.orderForm.order_direction.value=='asc')
        document.orderForm.order_direction.value='desc';
      else document.orderForm.order_direction.value='asc';

      document.orderForm.submit();
    }

</script>

<noscript>Javascript is required to use Vehicle Manager <a href="https://ordasoft.com/vehicle-manager-joomla-car-rental-dealer-software-for-rent-and-sell-cars" title="car rental dealer software">car rental dealer software</a>, <a href="https://ordasoft.com/car-dealer-website-development.html" title="car dealer website development" >car dealer website development</a></noscript>
<script>

jQuerOs(document).ready(function () {
    function maxMargin(item){
        var A = item, max = 0, elem;
        A.each(function () {

        var margin = parseInt( jQuerOs(this).css('margin-left'), 10) ;

        if ( margin > max)
        max = margin, elem = this;

            if (max > 0) {
                // statement
                jQuerOs('.okno_V').css('margin-bottom', max);
            } else {
                jQuerOs('.okno_V').css('margin-bottom', '10px');
            }

        });
    };


        maxMargin(jQuerOs('.okno_V'));

    });

</script>

<?php positions_vm($params->get('singleuser01'));?>
<?php positions_vm($params->get('singlecategory01')); ?>
                    <div class="componentheading<?php echo $params->get('pageclass_sfx'); ?>">
                    <?php
                     if (!$params->get('wrongitemid')){
                       echo $veh_header;
                     }
                     else{
                     $parametrs=$mainframe->getParams();
                     echo $parametrs->toObject()->page_title;
                     }
                    ?>
                    </div>


<?php positions_vm($params->get('singleuser02'));?>
<?php positions_vm($params->get('singlecategory02'));
?>

<!-- Connect Google or Open map START-->
<?php
$map_uniq_id = "543210";
if (($task!='rent_request_vehicle')&&($vehiclemanager_configuration['location_map']==1)) { ?>
    <div id="vm_map_canvas<?php echo $map_uniq_id; ?>" class="vm_map_canvas vm_map_canvas_03"></div>
<?php
    if ( $vehiclemanager_configuration['google_openmap']['show'] == 0 ) { ?>
        <!-- //for open map add div for Popup -->
        <div id="os_ol_popup<?php echo $map_uniq_id; ?>" class="os_ol_popup">
            <a href="#" id="os_ol_popup-closer<?php echo $map_uniq_id; ?>" class="os_ol_popup-closer"></a>
            <div id="os_ol_popup-content<?php echo $map_uniq_id; ?>"></div>
        </div>
    <?php
        // Include Open map
        // HTML_vehiclemanager::add_open_map($rows);
        require getLayoutPath::getLayoutPathCom('com_vehiclemanager', 'map', 'open_map');
    } else {
        // Include Google map
        // HTML_vehiclemanager::add_google_map($rows, $watermark);
        require getLayoutPath::getLayoutPathCom('com_vehiclemanager', 'map', 'google_map');
    }
} ?>
<!-- Connect Google or Open map END-->

<div class="all_vehicle_search VEH-row">

    <div class="VEH-collumn-xs-12 VEH-collumn-sm-12 VEH-collumn-md-12 VEH-collumn-lg-12">

        <?php positions_vm($params->get('singlecategory03')); ?>

    </div>


    <?php
    if (count($rows) > 0):
      $sort_arr['order_field'] = $params->get('sort_arr_order_field');
      $sort_arr['order_direction'] = $params->get('sort_arr_order_direction');
     ?>

    <?php positions_vm($params->get('singleuser03'));?>
    <?php positions_vm($params->get('singlecategory04')); ?>

    <?php
    if($params->get('show_orderrequest')):?>

                             <div id="ShowOrderBy" class="
              <?php if (!$params->get('show_input_button_search') && $params->get('show_input_add_vehicle')): ?>
                VEH-collumn-xs-12 VEH-collumn-sm-8 VEH-collumn-md-5 VEH-collumn-md-push-4 VEH-collumn-lg-5 VEH-collumn-lg-push-4 right_position
              <?php else: ?>
                VEH-collumn-xs-12 VEH-collumn-sm-8 VEH-collumn-md-5 VEH-collumn-lg-5
              <?php endif ?>
              ">
                                <form method="POST" action="<?php echo sefRelToAbs($_SERVER["REQUEST_URI"]);?>" name="orderForm">
                                <input type="hidden" id="order_direction" name="order_direction" value="<?php echo $sort_arr['order_direction']; ?>" >
                                <a title="Click to sort by this column." onclick="javascript:vm_allreordering();return false;" href="#">
                                  <img alt="" src="<?php echo $mosConfig_live_site; ?>/media/system/images/sort_<?php echo $sort_arr['order_direction']; ?>.png"></a>
                                  <?php echo _VEHICLE_MANAGER_LABEL_ORDER_BY; ?> <select size="1" class="inputbox" onchange="javascript:document.orderForm.order_direction.value='asc'; document.orderForm.submit();" id="order_field" name="order_field">

                                  <option  value="ordering" <?php if ($sort_arr['order_field'] == "ordering") echo 'selected="selected"'; ?>> <?php
                        echo _VEHICLE_MANAGER_LABEL_ORDERING; ?></option>
                                  <option value="date" <?php if($sort_arr['order_field'] == "date") echo 'selected="selected"'; ?> >  <?php echo _VEHICLE_MANAGER_LABEL_DATE; ?> </option>
                                  <option value="price" <?php if($sort_arr['order_field'] == "price") echo 'selected="selected"'; ?> > <?php echo _VEHICLE_MANAGER_LABEL_PRICE; ?></option>
                                  <option value="maker" <?php if($sort_arr['order_field'] == "maker") echo 'selected="selected"'; ?> >  <?php echo _VEHICLE_MANAGER_LABEL_MODEL; ?></option>
                                  <option value="vtitle" <?php if($sort_arr['order_field'] == "vtitle") echo 'selected="selected"'; ?> > <?php echo _VEHICLE_MANAGER_LABEL_TITLE; ?></option></select>
                                </form>
                            </div>
    <?php endif ?>
  <!-- show_orderrequest -->

   <?php endif ?>
  <!--  (count($rows) > 0) -->


    <?php
    if (!$params->get('wrongitemid')):?>
        <?php if ($params->get('show_input_button_search')):?>

            <div class="search_button_vehicle_container
      <?php if ( (!$params->get('show_orderrequest') || count($rows) <= 0) && $params->get('show_input_add_vehicle')): //disable order enable add button ?>
          VEH-collumn-xs-12 VEH-collumn-xs-push-0 VEH-collumn-sm-4 VEH-collumn-sm-push-4 VEH-collumn-md-3 VEH-collumn-md-push-5 VEH-collumn-lg-4 VEH-collumn-lg-push-5
      <?php elseif(!$params->get('show_input_add_vehicle') && count($rows) > 0 && $params->get('show_orderrequest')): //disable  add button  enable order  ?>
          VEH-collumn-xs-12 VEH-collumn-xs-push-0 VEH-collumn-sm-4 VEH-collumn-sm-push-0 VEH-collumn-md-3 VEH-collumn-md-push-4 VEH-collumn-lg-4 VEH-collumn-lg-push-3
      <?php else: ?>
          VEH-collumn-xs-12 VEH-collumn-sm-12 VEH-collumn-md-4 VEH-collumn-lg-4
      <?php endif; ?>">


                <?php HTML_vehiclemanager::showSearchButton();  ?>

            </div>

         <?php endif; ?>
    <!-- wrongitemid -->

  <?php endif; ?>
  <!-- show_input_button_search -->

    <div class="VEH-collumn-xs-12 VEH-collumn-sm-12 VEH-collumn-md-12 VEH-collumn-lg-12">
        <?php positions_vm($params->get('singlecategory10')); ?>
    </div>

</div>

<?php positions_vm($params->get('singleuser04'));?>
<?php positions_vm($params->get('singlecategory05')); ?>
                 <div id="gallery"
                 data-columns-lg = <?php echo $vehiclemanager_configuration['veh_data_columns_lg']; ?>

                 data-columns-md = <?php echo $vehiclemanager_configuration['veh_data_columns_md']; ?>

                 data-columns-sm = <?php echo $vehiclemanager_configuration['veh_data_columns_sm']; ?>

                 data-columns-xs = <?php echo $vehiclemanager_configuration['veh_data_columns_xs']; ?>
                    >

                     <?php $total = count($rows);
                     //if (isset($_GET['lang'])) $lang = $_GET['lang']; else $lang = '*';
                     foreach ($rows as $row) {
                    // if($row->language == $lang or $row->language == '*' or $lang ==  '*'){
                    $link= 'index.php?option=com_vehiclemanager&amp;task=view_vehicle&amp;id=' . $row->id . '&amp;catid=' . $row->catid . '&amp;Itemid=' . $Itemid;
                         $imageURL = $row->image_link;
                         ?>


              <div class="okno_V">
                             <div class="okno_img" style = "position:relative;">
                             <a href="<?php echo sefRelToAbs($link);?>" style="text-decoration: none" >
                                <?php
                                $file_name = vm_picture_thumbnail($imageURL,$vehiclemanager_configuration['fotogallery']['high'],
                                    $vehiclemanager_configuration['fotogallery']['width'], $watermark);

                                    $file=$mosConfig_live_site . '/components/com_vehiclemanager/photos/'. $file_name;

                                echo '<img alt="'.$row->vtitle.'" title="'.$row->vtitle.'" src="' .$file. '">';
                                ?>
                             </a>

                    <?php

                          if ($row->listing_type){
                                if ($row->listing_type != 1){
                                    echo '<div class="vm_col_sale">'._VEHICLE_MANAGER_OPTION_FOR_SALE.'</div>';
                                } else{
                                    echo '<div class="vm_col_rent">'._VEHICLE_MANAGER_OPTION_FOR_RENT.'</div>';
                                }
                          }


                    if ($row->listing_status){
            if ($row->listing_status != 0){
              $listing_status1 = explode(',', _VEHICLE_MANAGER_OPTION_LISTING_STATUS);

              $ls = 1;
              foreach ($listing_status1 as $listing_status2) {
                  $listing_status[$ls] = $listing_status2;
                  $ls++;
              }

              echo '<div class="vm_listing_status">'.$listing_status[$row->listing_status].'</div>';
            }
          }
                    ?>

                    <!-- add wishlist marker -->
                 <?php if ($params->get('show_add_to_wishlist')) { ?>
                    <span class="fa-stack fa-lg i-wishlist i-wishlist-all"  >
                    <?php if (in_array($row->id, $params->get('wishlist'))) { ?>
                        <i class="fa fa-star fa-stack-1x" id="icon<?php echo $row->id ?>" title="<?php echo _VEHICLE_MANAGER_TOOLTIP_REMOVE_FROM_WISHLIST ?>" onclick="addToWishlist(<?php echo $row->id ?>, <?php echo $my->id ?>)"></i>
                    <?php } else { ?>
                        <i class="fa fa-star-o fa-stack-1x" id="icon<?php echo $row->id ?>" title="<?php echo _VEHICLE_MANAGER_TOOLTIP_ADD_TO_WISHLIST ?>" onclick="addToWishlist(<?php echo $row->id ?>, <?php echo $my->id ?>)"></i>
                    <?php } ?>
                    </span>
                  <?php } ?>
                    <!-- end add wishlist marker -->
                  </div>
                 <div class="textvehicle">
                      <h4 class="titlevehicle">
                         <a href="<?php echo sefRelToAbs($link); ?>" >
                            <?php if(strlen($row->vtitle)>45) echo substr($row->vtitle,0,25),'...';
                            else {echo $row->vtitle;}?>
                         </a>
                      </h4>
                        <?php if ($row->maker != '' || $row->vmodel !== 0 && trim($row->vmodel) !== "") {
                          ?>

                            <div class="vm_text_model">
                             <i class="fa fa-car"></i>
                            <?php if ($row->maker != '' && $row->maker != 'other') {
                          ?>
                                                          <span class="vm_maker"><?php echo $row->maker; ?></span>
                      <?php } if ($row->vmodel !== 0 && trim($row->vmodel) !== "") { ?>
                                                          <span class="vm_model"><?php echo $row->vmodel; ?></span>
                                                      <?php
                                                  } ?>
                          </div>

                              <?php
                            }
                            if ($row->year != 0) { ?>
                            <div class="vm_text">
                                <i class="fa fa-calendar"></i>
                                <span><?php echo _VEHICLE_MANAGER_LABEL_ISSUE_YEAR; ?>:</span>
                                <span><?php echo $row->year; ?></span>
                            </div>
                          <?php } 
                          if ( trim($row->mileage) !== '' ) { ?>
                                <div class="vm_text" >
                                    <i class="fa fa-tachometer"></i>
                                    <span><?php echo _VEHICLE_MANAGER_LABEL_MILEAGE; ?>:</span>
                                    <span><?php echo trim($row->mileage); ?></span>
                                </div>
                          <?php } ?>
               </div>
                                    <div class="vm_viewlist">
                                     <a href="<?php echo sefRelToAbs($link); ?>" style="display: block">
                                        <?php
                                         if ($params->get('show_pricerequest') && $row->price != '' && $row->priceunit != '') {
                                        ?>
                                                         <div class="price">

                                                        <?php if(!incorrect_price($row->price)): ?>
                                                        <?php
                                                          if ($vehiclemanager_configuration['price_unit_show'] == '1'){
                                                                    if ($vehiclemanager_configuration['sale_separator'] == '1'){
                                                                        echo formatMoney($row->price, $vehiclemanager_configuration['sale_fraction'], $vehiclemanager_configuration['price_format']), ' ', $row->priceunit;
                                                                    }
                                                                    else {
                                                                        echo $row->price, ' ', $row->priceunit;
                                                                    }
                                                          }else {
                                                              if ($vehiclemanager_configuration['sale_separator'])
                                                                  echo $row->priceunit, ' ', formatMoney($row->price, $vehiclemanager_configuration['sale_fraction'], $vehiclemanager_configuration['price_format']);
                                                              else echo $row->priceunit, ' ', $row->price;
                                                          }
                                                        ?>
                                                        <?php else: ?>
                                                            <?php echo $row->price; ?>
                                                        <?php endif; ?>

                                                     </div>
                                        <?php
                                         }
                                        ?>
                                          <span><?php echo _VEHICLE_MANAGER_LABEL_VIEW_LISTING; ?></span></a>
                                          <div style="clear: both;"></div>
                                  </div>
                         </div><!--okno-->
                     <?php
                     //}
                     } ?>
    <div class='clear'></div>
    </div><!--gallery-->
    <?php
                    if ($params->get('show_rentstatus') && $params->get('show_rentrequest') && $params->get('rent_save')) {// && $available)
                        ?>
            <div class="componentheading<?php echo $params->get('pageclass_sfx'); ?>">
    <?php echo _VEHICLE_MANAGER_LABEL_RENT_INFORMATIONS; ?>
                <input type="hidden" name="vehicleid" id="vehicleid" value="<?php echo $row->id ?>" maxlength="80" />
            </div>
            <form action="<?php echo sefRelToAbs("index.php");?>" name="userForm" method="post">
            <div class="basictable_56 basictable">
                <div class="row_01">
                    <span class="col_01">
              <?php echo _VEHICLE_MANAGER_LABEL_RENT_REQUEST_NAME; ?>:<br />
                         <input class="inputbox" type="text" name="user_name" size="38" maxlength="80" />
                    </span>
                    <span class="col_02">
              <?php echo _VEHICLE_MANAGER_LABEL_RENT_REQUEST_EMAIL; ?>:<br />
                         <input class="inputbox" type="text" name="user_email" size="30" maxlength="80" />
                    </span>
               </div>
           </div>
           <div class="basictable_57 basictable">
                <div class="row_01">
                    <span class="col_01">
<?php echo _VEHICLE_MANAGER_LABEL_RENT_REQUEST_MAILING; ?>:<br />
<?php
            //    editorArea('editor1', '', 'user_mailing', '400', '200', '30', '5');
              ?>
<textarea name ="user_mailing"></textarea>
                    </span>
                    <span class="col_02">
                <br />
                <p>
                    <?php echo _VEHICLE_MANAGER_LABEL_RENT_REQUEST_FROM; ?>:<br />
                    <?php echo JHtml::_('calendar',date("Y-m-d"), 'rent_from','rent_from','%Y-%m-%d' );  ?>
                </p>
                <p>
                    <?php echo _VEHICLE_MANAGER_LABEL_RENT_REQUEST_UNTIL; ?>:<br />
                    <?php echo JHtml::_('calendar',date("Y-m-d"), 'rent_until','rent_until','%Y-%m-%d' ); ?>
                </p>
                    </span>
                </div>
           </div>

<?php } ?>
            <br/>
            <div class="basictable_58 basictable page_navigation">
                <div class="row_02">
                         <?php
                         $paginations = $arr;
                    if ($paginations && ( $pageNav->total > $pageNav->limit )) {
                        echo $pageNav->getPagesLinks( );
                    }
                         ?>
                </div>
                <div class="row_03">
                    <span class="col_01">
<?php
                    if ($params->get('show_rentstatus') && $params->get('show_rentrequest') && !$params->get('rent_save')) {// ____
?>
                                    <br />
                              <!--      <input type="submit" name="submit" value="<?php //echo _VEHICLE_MANAGER_LABEL_BUTTON_RENT_REQU; ?>" class="button" />
                                    <br />-->
<?php
                    } else if ($params->get('show_rentstatus') && $params->get('show_rentrequest') && $params->get('rent_save')) {// && $available)
?>
                                    <input type="button" class="button" value="<?php echo _VEHICLE_MANAGER_LABEL_BUTTON_RENT_REQU_SAVE; ?>" onclick="vm_rent_request_submitbutton()" />
                        <?php } else {
 ?>
                        &nbsp;
                        <?php
                    }
                        ?>
                    </span>
                </div>
            </div>
              <input type="hidden" name="option" value="<?php echo $option;?>"/>
              <input type="hidden" name="task" value="save_rent_request_vehicle"/>
    <?php
        if($option != 'com_vehiclemanager'){
    ?>
              <input type="hidden" name="tab" value="getmyvehiclestab"/>
              <input type="hidden" name="is_show_data" value="1"/>
    <?php
        }
    ?>
              <input type="hidden" name="Itemid" value="<?php echo $Itemid; ?>"/>
              <input type="hidden" name="vehicleid" value="<?php echo $rows[0]->id;?>"/>
            </form>
  <div class="basictable_59">
    <?php
    mosHTML::BackButton($params, $hide_js); ?>
  </div>

<?php positions_vm($params->get('singlecategory09')); ?>

<!-- Modal -->
<a href="#aboutus" class="vehicle-button-about"></a>
<a href="#vehicle-modal-css" class="vehicle-overlay" id="vehicle-aboutus" style="display: none;"></a>
<div class="vehicle-popup">
    <div class="vehicle-modal-text">
        Please past text to modal
    </div>
    <a class="vehicle-close" title="Close" href="#vehicle-close"></a>
</div>
<p style="text-align: center; font-size: 12px;"><a title="Car rental dealer software" href="https://ordasoft.com/vehicle-manager-joomla-car-rental-dealer-software-for-rent-and-sell-cars" target="_blank">Car rental dealer software</a> by OrdaSoft.</p>