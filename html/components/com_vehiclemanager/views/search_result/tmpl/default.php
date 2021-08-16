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

global $hide_js, $Itemid, $mosConfig_live_site, $mosConfig_absolute_path,$database;
global $limit, $total, $limitstart, $task, $paginations, $mainframe, $vehiclemanager_configuration, $option, $my;
$user=Jfactory::getUser();
if(isset($_REQUEST['userId'])){
    if($option != 'com_vehiclemanager' and $_REQUEST['userId'] == $user->id) PHP_vehiclemanager::showTabs();
}
else{
    if($option != 'com_vehiclemanager') PHP_vehiclemanager::showTabs();
}
if(!empty($params['page_heading']) && $params['show_page_heading'] != 0){
    $veh_header = $params['page_heading'];
} else {
    $veh_header = $currentcat->header;
}
$watermark_path = ($vehiclemanager_configuration['watermark']['show'] == 1) ? 'watermark/' : '';
$watermark = ($vehiclemanager_configuration['watermark']['show'] == 1) ? true : false;
//---------------------------

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
          if(document.orderForm_v.order_direction.value=='asc')
            document.orderForm_v.order_direction.value='desc';
          else document.orderForm_v.order_direction.value='asc';

          document.orderForm_v.submit();
        }

    </script>

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

<noscript>Javascript is required to use Vehicle Manager <a href="https://ordasoft.com/vehicle-manager-joomla-car-rental-dealer-software-for-rent-and-sell-cars" title="car rental dealer software">car rental dealer software</a>, <a href="https://ordasoft.com/car-dealer-website-development.html" title="car dealer website development" >car dealer website development</a></noscript>

<?php positions_vm($params->get('singleuser01'));?>
<?php positions_vm($params->get('showsearch01')); ?>
    <div class="componentheading<?php echo $params->get( 'pageclass_sfx' ); ?>">
      <?php echo $veh_header; ?>
    </div>


<!--================================
=            Pre Button            =
=================================-->


<div class="basictable basictable_01">
     <div class="pre_button VEH-row">

<div class="VEH-collumn-xs-10 VEH-collumn-sm-10 VEH-collumn-md-10 VEH-collumn-lg-10">


<?php
           // if ($currentcat->img != null && $currentcat->align == 'left' && $params->get('show_cat_pic')) {
?>
                <span class="col_01">
                  <!--<img src="<?php echo $currentcat->img; ?>" align="<?php echo $currentcat->align; ?>" alt="?"/>-->
                </span>
<?php
          //}
          if(!$params->get('wrongitemid')){
  ?>
            <span class="col_02">
            <?php //echo $currentcat->descrip; ?>
            </span>
            <?php //if ($currentcat->img != null && $currentcat->align == 'right') {
  ?>
            <span class="col_03">
                <!--<img src="<?php echo $currentcat->img; ?>" align="<?php echo $currentcat->align; ?>" alt="?" />-->
            </span>
            <?php //}
          } ?>

       </div>

     </div>

<!--====  End of Pre Button   ====-->



<?php
positions_vm($params->get('singleuser02'));
positions_vm($params->get('showsearch02'));
?>

<!-- Connect Google or Open map START-->
<?php
$map_uniq_id = "639827";
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
}
?>
<!-- Connect Google or Open map END-->

<?php
global $option;

if (count($rows) > 0) {

  if(protectInjectionWithoutQuote('option') == "com_vehiclemanager" || !$params->get('wrongitemid') || $params->get('show_search')){
?>
    <div class="all_vehicle_search VEH-row">

    <div class="VEH-collumn-xs-12 VEH-collumn-sm-12 VEH-collumn-md-12 VEH-collumn-lg-12">

        <?php positions_vm($params->get('singlecategory03')); ?>

    </div>


    <?php


     //END if($params->get('show_input_button_search')) стр.337
    // print_r($params->get('sort_arr_order_field'));exit;
    $sort_arr['order_field'] = $params->get('sort_arr_order_field');
    $sort_arr['order_direction'] = $params->get('sort_arr_order_direction');
    positions_vm($params->get('singleuser03'));
    positions_vm($params->get('singlecategory04'));

    ?>

    <!--=================================
    =            ShowOrderBy            =
    ==================================-->

    <?php if($params->get('show_orderrequest')) { ?>

  <?php

  if(protectInjectionWithoutQuote('option') == "com_vehiclemanager") {

  ?>

          <div id="ShowOrderBy" class="
                  <?php if (!$params->get('show_input_button_search') && $params->get('show_input_add_vehicle')): ?>
                    VEH-collumn-xs-12 VEH-collumn-sm-8 VEH-collumn-md-5 VEH-collumn-md-push-4 VEH-collumn-lg-5 VEH-collumn-lg-push-4 right_position
                  <?php else: ?>
                    VEH-collumn-xs-12 VEH-collumn-sm-8 VEH-collumn-md-5 VEH-collumn-lg-5
                  <?php endif ?>
                  ">
            <form method="POST" action="<?php echo sefRelToAbs($_SERVER["REQUEST_URI"]);?>" name="orderForm_v">
              <input type="hidden" id="order_direction" name="order_direction" value="<?php
                echo $sort_arr['order_direction']; ?>" >
              <a title="Click to sort by this column." onclick="javascript:vm_allreordering();return false;" href="#">
              <img alt="" src="<?php echo $mosConfig_live_site; ?>/media/system/images/sort_<?php echo $sort_arr['order_direction']; ?>.png"></a>
              <?php echo _VEHICLE_MANAGER_LABEL_ORDER_BY ; ?>
              <select size="1" class="inputbox"
               onchange="javascript:document.orderForm_v.order_direction.value='asc';
                document.orderForm_v.submit();" id="order_field" name="order_field">
                  <option  value="ordering" <?php if ($sort_arr['order_field'] == "ordering") echo 'selected="selected"'; ?>> <?php
                    echo _VEHICLE_MANAGER_LABEL_ORDERING; ?></option>
                  <option value="date" <?php if($sort_arr['order_field'] == "date")
                   echo 'selected="selected"'; ?> >  <?php echo _VEHICLE_MANAGER_LABEL_DATE; ?> </option>
                  <option value="price" <?php if($sort_arr['order_field'] == "price")
                    echo 'selected="selected"'; ?> > <?php echo _VEHICLE_MANAGER_LABEL_PRICE; ?></option>
                  <option value="maker" <?php if($sort_arr['order_field'] == "maker")
                   echo 'selected="selected"'; ?> >  <?php echo _VEHICLE_MANAGER_LABEL_MODEL; ?></option>
                  <option value="vtitle" <?php if($sort_arr['order_field'] == "vtitle")
                   echo 'selected="selected"'; ?> > <?php echo _VEHICLE_MANAGER_LABEL_TITLE; ?></option>
               </select>
            </form>
          </div>
      <?php } ?>

    <?php } ?>


    <!--====  End of ShowOrderBy  ====-->



<?php if(!$params->get('wrongitemid')): ?>

     <?php if ($params->get('show_input_button_search')):?>

        <div class="search_button_vehicle_container
            <?php
            //disable order enable add button

            if (!$params->get('show_orderrequest') || protectInjectionWithoutQuote('option') != "com_vehiclemanager"  && $params->get('show_input_add_vehicle')):

            ?>
                VEH-collumn-xs-12 VEH-collumn-xs-push-0 VEH-collumn-sm-4 VEH-collumn-sm-push-4 VEH-collumn-md-3 VEH-collumn-md-push-5 VEH-collumn-lg-4 VEH-collumn-lg-push-5
            <?php
            //disable  add button  enable order

            elseif(!$params->get('show_input_add_vehicle') && protectInjectionWithoutQuote('option') == "com_vehiclemanager" || $params->get('show_orderrequest')  ):

            ?>
                VEH-collumn-xs-12 VEH-collumn-xs-push-0 VEH-collumn-sm-4 VEH-collumn-sm-push-0 VEH-collumn-md-3 VEH-collumn-md-push-4 VEH-collumn-lg-4 VEH-collumn-lg-push-3
            <?php else: ?>
                VEH-collumn-xs-12 VEH-collumn-sm-12 VEH-collumn-md-4 VEH-collumn-lg-4
            <?php endif; ?>">

            <?php HTML_vehiclemanager::showSearchButton();  ?>

      </div>

      <?php endif; ?>

  <?php endif ?>


  </div> <!-- END all_vehicle_search -->


  <?php
}
 positions_vm($params->get('singleuser04'));?>
<?php positions_vm($params->get('showsearch04')); ?>
<!--**************************-->

<!--**************************-->
  <div id="gallery"
    data-columns-lg = <?php echo $vehiclemanager_configuration['veh_data_columns_lg']; ?>

    data-columns-md = <?php echo $vehiclemanager_configuration['veh_data_columns_md']; ?>

    data-columns-sm = <?php echo $vehiclemanager_configuration['veh_data_columns_sm']; ?>

    data-columns-xs = <?php echo $vehiclemanager_configuration['veh_data_columns_xs']; ?>
  >
    <?php $total = count($rows);
    foreach ($rows as $row) {
      $option = 'com_vehiclemanager';
      if ($option != "com_vehiclemanager") {//maybe need to delete later//save only else link
        $link = 'index.php?option=' . $option .
         '&task=view_vehicle&tab=getmyvehiclesTab&is_show_data=1&id=' .
          $row->id . '&catid=' . $row->catid . '&Itemid=' . $Itemid . '#tabs-2';
      } else {
        $link= 'index.php?option=' . $option .
         '&amp;task=view_vehicle&amp;id=' . $row->id . '&amp;catid=' .
          $row->catid . '&amp;Itemid=' . $Itemid;
      }
      $imageURL = $row->image_link;
      ?>
      <div class="okno_V">
         <div class="okno_img" style = "position:relative;">
         <a href="<?php echo sefRelToAbs($link);?>" style="text-decoration: none" >
         <?php
            $file_name = vm_picture_thumbnail($imageURL,
              $vehiclemanager_configuration['fotogallery']['high'],
              $vehiclemanager_configuration['fotogallery']['width'], $watermark);


              $file=$mosConfig_live_site . '/components/com_vehiclemanager/photos/'. $file_name;


            echo '<img alt="'.$row->vtitle.'" title="'.$row->vtitle.'" src="' .$file.
             '">';
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
          <!-- end wishlist marker -->
        </div>

        <div class="textvehicle">

          <h4 class="titlevehicle">
             <a href="<?php echo sefRelToAbs($link); ?>" >
                <?php if(strlen($row->vtitle)>45) echo substr($row->vtitle,0,25),'...';
                else {
                    echo $row->vtitle;
                }?>
             </a>
          </h4>
           <?php if ($row->maker != '' || $row->vmodel !== 0 && trim($row->vmodel) !== "") { ?>
              <div class="vm_text_model">
                <i class="fa fa-car"></i>
                <?php if ($row->maker != '' && $row->maker != 'other') { ?>
                  <span class="vm_maker"><?php echo $row->maker; ?></span>
                <?php } if ($row->vmodel !== 0 && trim($row->vmodel) !== "") { ?>
                <span class="vm_model"><?php echo $row->vmodel; ?></span>
                <?php } ?>
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
                          <?php

                          if(!incorrect_price($row->price)){

                                  if ($vehiclemanager_configuration['price_unit_show'] == '1'){
                                    if ($vehiclemanager_configuration['sale_separator'])
                                       echo formatMoney($row->price, $vehiclemanager_configuration['sale_fraction'],
                                        $vehiclemanager_configuration['price_format']), ' ', $row->priceunit;
                                    else echo $row->price, ' ', $row->priceunit;
                                  }else {
                                    if ($vehiclemanager_configuration['sale_separator'])
                                       echo $row->priceunit, ' ', formatMoney($row->price,
                                        $vehiclemanager_configuration['sale_fraction'], $vehiclemanager_configuration['price_format']);
                                    else echo $row->priceunit, ' ', $row->price;
                                  }

                            }else{
                              echo $row->price;
                            }

                          ?>
                                </div>
                        <?php } ?>

                        <span><?php echo _VEHICLE_MANAGER_LABEL_VIEW_LISTING; ?></span></a>
                        <div style="clear: both;"></div>
                </div>
      </div>
  <?php
      }
  ?>
  </div>

<form action="<?php echo sefRelToAbs("index.php");?>" name="userForm" method="post">
  <?php
  if ($params->get('show_rentstatus') && $params->get('show_rentrequest')
    && $params->get('rent_save')) {// && $available)
  ?>

    <div class="componentheading<?php echo $params->get('pageclass_sfx'); ?>">
    <?php echo _VEHICLE_MANAGER_LABEL_RENT_INFORMATIONS; ?>
        <input type="hidden" name="vehicleid" id="vehicleid" value="<?php echo $row->id ?>" maxlength="80" />
    </div>

    <div class="basictable_49 basictable">
      <div class="row_02">
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
    <div class="basictable_50 basictable">
      <div class="row_01">
        <span class="col_01">
          <?php echo _VEHICLE_MANAGER_LABEL_RENT_REQUEST_MAILING; ?>:<br />
          <?php //editorArea('editor1', '', 'user_mailing', '400', '200', '30', '5'); ?>
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

    <br/>
    <div class="basictable_51 page_navigation">
      <div class="row_03">
        <span clas="col_01">
          <?php
            if ($params->get('show_rentstatus') && $params->get('show_rentrequest')
             && !$params->get('rent_save')) {
          ?>
              <br />
          <!-- <input type="submit" name="submit" value="<?php
            echo _VEHICLE_MANAGER_LABEL_BUTTON_RENT_REQU; ?>" class="button" />
              <br />-->
          <?php
            } else if ($params->get('show_rentstatus') && $params->get('show_rentrequest')
               && $params->get('rent_save')) {// && $available)
          ?>
                <input type="button" class="button"
                  value="<?php echo _VEHICLE_MANAGER_LABEL_BUTTON_RENT_REQU_SAVE; ?>"
                     onclick="vm_rent_request_submitbutton()" />
      <?php } else { ?>
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
<?php
  }
?>

</form>

    <div class="basictable_51 page_navigation">
      <div class="row_02">
        <?php
        $paginations = $arr;
          if ($paginations && ( $pageNav->total > $pageNav->limit )) {
            echo $pageNav->getPagesLinks( );
          }
        ?>
      </div>

    </div>

<?php
}//if row > 0

   if ($is_exist_sub_categories && $vehiclemanager_configuration['single_subcategory_show']['show'] == 1) {
?>
    <?php positions_vm($params->get('singlecategory07')); ?>
    <div class="componentheading<?php echo $params->get('pageclass_sfx'); ?>">
    <?php echo _VEHICLE_MANAGER_LABEL_FETCHED_SUBCATEGORIES . " : " . $params->get('category_name'); ?></div>
    <?php positions_vm($params->get('singlecategory08')); ?>
<?php
     HTML_vehiclemanager::listCategories($params, $categories, $catid, $tabclass, $currentcat);
  }
?>
  <div class="basictable_59">
    <?php
    mosHTML::BackButton($params, $hide_js); ?>
  </div>

<?php positions_vm($params->get('singlecategory09')); ?>

<?php
positions_vm($params->get('singlecategory11'));
?>

<!-- Modal -->
<a href="#aboutus" class="vehicle-button-about"></a>

<a href="#vehicle-modal-css" class="vehicle-overlay" id="vehicle-aboutus" style="display: none;"></a>
<div class="vehicle-popup">
    <div class="vehicle-modal-text">
        Please past text to modal
    </div>

    <a class="vehicle-close" title="Close" href="#vehicle-close"></a>
</div>

</div>
<p style="text-align: center; font-size: 12px;"><a title="Car rental dealer software" href="https://ordasoft.com/vehicle-manager-joomla-car-rental-dealer-software-for-rent-and-sell-cars" target="_blank">Car rental dealer software</a> by OrdaSoft.</p>