<?php
if (!defined('_VALID_MOS') && !defined('_JEXEC')) die('Direct Access to ' . basename(__FILE__) . ' is not allowed.');

$session = JFactory::getSession();
$arr = $session->get("array", "default");
// PHP_vehiclemanager::showTabs();
global $hide_js, $Itemid, $mosConfig_live_site, $mosConfig_absolute_path,$database;
global $limit, $total, $limitstart, $task, $paginations, $mainframe, $vehiclemanager_configuration, $option, $my;
//if ($option != 'com_vehiclemanager' && $task != 'owner_vehicles')
  //  PHP_vehiclemanager::showTabs();
if(!empty($params['page_heading']) && $params['show_page_heading'] != 0){
    $veh_header = $params['page_heading'];
} else {
    $veh_header = $currentcat->header;
}
$watermark_path = ($vehiclemanager_configuration['watermark']['show'] == 1) ? 'watermark/' : '';
$watermark = ($vehiclemanager_configuration['watermark']['show'] == 1) ? true : false;
require_once($mosConfig_absolute_path . "/components/com_vehiclemanager/functions.php");

function words_limit($input_text, $limit = 50, $end_str = '') {
                    $input_text = strip_tags($input_text);
                    $words = explode(' ', $input_text);
                    if ($limit < 1 || sizeof($words) <= $limit):
                        return $input_text;

                    endif;

                    $words = array_slice($words, 0, $limit);
                    $out = implode(' ', $words);
                    return $out . $end_str;

                }

?>
<noscript>Javascript is required to use Vehicle Manager <a href="https://ordasoft.com/vehicle-manager-joomla-car-rental-dealer-software-for-rent-and-sell-cars" title="car rental dealer software">car rental dealer software</a>, <a href="https://ordasoft.com/car-dealer-website-development.html" title="car dealer website development" >car dealer website development</a></noscript>

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
<?php positions_vm($params->get('singleuser01')); ?>
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

          if(!$params->get('wrongitemid')): ?>

            <span class="col_02">
            <?php //echo $currentcat->descrip; ?>
            </span>
            <?php //if ($currentcat->img != null && $currentcat->align == 'right') {
              ?>
            <span class="col_03">
                <!--<img src="<?php echo $currentcat->img; ?>" align="<?php echo $currentcat->align; ?>" alt="?" />-->
            </span>

        <?php endif ?>
             <span class="col_04">
               <?php if ($params->get( 'show_input_print_pdf')){?>
                    <a onclick="window.open(this.href,'win2','status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=640,height=480,directories=no,location=no'); return false;" rel="nofollow"
                    href="<?php echo $mosConfig_live_site; ?>/index.php?option=com_vehiclemanager&amp;task=all_vehicle&amp;Itemid=<?php echo $Itemid; ?>&amp;limitstart=<?php echo $limitstart; ?>&amp;printItem=pdf" title="PDF"  rel="nofollow">
<?php
    if (version_compare(JVERSION, "1.6.0", "lt")){
?>
            <img src="<?php echo $mosConfig_live_site; ?>/media/system/images/pdf_button.png" alt="PDF" />
          <?php
            } else{
          ?>
           <i class="fa fa-file"></i>
<?php
    }
?>
                    </a>
                <?php } ?>
            </span>
            <span class="col_05">
              <?php if ($params->get( 'show_input_print_view')){?>
                    <a onclick="window.open(this.href,'win2','status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=640,height=480,directories=no,location=no');  return false;" rel="nofollow"
                    href="<?php echo $mosConfig_live_site; ?>/index.php?option=com_vehiclemanager&amp;task=all_vehicle&amp;Itemid=<?php echo $Itemid; ?>&amp;limitstart=<?php echo $limitstart; ?>&amp;printItem=print&amp;tmpl=component" title="Print"  rel="nofollow">
<?php
    if (version_compare(JVERSION, "1.6.0", "lt")){
?>
      <img src="<?php echo $mosConfig_live_site; ?>/media/system/images/printButton.png" alt="Print" >
<?php
    } else{
?>
      <i class="fa fa-print"></i>
<?php
    }
?>
      </a>
     <?php } ?>
       </span>
      <span class="col_06">

      <?php if ($params->get( 'show_input_mail_to')):?>

       <?php if (version_compare(JVERSION, '1.6', 'lt')):?>

          <a href="<?php echo $mosConfig_live_site; ?>/index.php?option=com_mailto&amp;tmpl=component&amp;link=<?php $url = JUri::getInstance();
          echo base64_encode($url->toString()); ?>"
          title="E-mail"
          onclick="window.open(this.href,'win2','width=400,height=350,menubar=yes,resizable=yes'); return false;">
          <i class="fa fa-envelope"></i>

        <?php else:?>

    <?php

      require_once JPATH_SITE . '/components/com_mailto/helpers/mailto.php';
      $url = JUri::getInstance();
      $url_c = $url->toString();
      $link = 'index.php?option=com_mailto&amp;tmpl=component&amp;Itemid='.$Itemid.'&link='.MailToHelper::addLink($url_c);?>
      <a href="<?php echo sefRelToAbs($link);?>"
      title="E-mail"
      onclick="window.open(this.href,'win2','width=400,height=350,menubar=yes,resizable=yes'); return false;">
    <?php if (version_compare(JVERSION, "1.6.0", "lt")): ?>
      <img src="<?php echo $mosConfig_live_site; ?>/media/system/images/emailButton.png" alt="E-mail" />
        <?php else: ?>
      <i class="fa fa-envelope"></i>
       <?php  endif ?>
       </a>

       <?php endif ?>

    <?php endif ?>

       </span>

       </div>

      <div class="VEH-collumn-xs-2 VEH-collumn-sm-2 VEH-collumn-md-2 VEH-collumn-lg-2">

        <?php if ($params->get('rss_show')) {
            $rss_search_url = '';
            foreach ($_GET as $key => $value) {
                $rss_search_url .= $key . '=' . $value . '&';
            }
          ?>
          <span class="col_07">
              <!-- <a href="<?php echo $mosConfig_live_site;?>/index.php?option=com_vehiclemanager&task=show_rss_categories&Itemid=<?php echo $Itemid;?>"> -->
              <a href='<?php echo JRoute::_("index.php?" . $rss_search_url . "rss=1")?>'>
                  <img src="./components/com_vehiclemanager/images/rss2.png" alt="Category RSS" align="right" title="Category RSS"/>
              </a>
          </span>
        <?php } ?>
      </div>
     </div>

<!--====  End of Pre Button   ====-->



<?php positions_vm($params->get('singleuser02')); ?>
<?php positions_vm($params->get('showsearch02')); ?>

<!-- Connect Google or Open map START-->
<?php
$map_uniq_id = "332896";
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

<?php if ($params->get('show_order_by')):?>

<div class="all_vehicle_search VEH-row">

    <div class="VEH-collumn-xs-12 VEH-collumn-sm-12 VEH-collumn-md-12 VEH-collumn-lg-12">

        <?php positions_vm($params->get('singlecategory03')); ?>

    </div>

<!--=================================
=            Add Vehicle            =
==================================-->

    <?php if(!$params->get('wrongitemid')): ?>

            <?php if($params->get('show_input_add_vehicle')): ?>

                <div class="vm_addVehicle VEH-collumn-xs-12 VEH-collumn-sm-4 VEH-collumn-md-3 VEH-collumn-lg-3">
                <?php HTML_vehiclemanager::showAddButton(); ?>
                <?php  positions_vm($params->get('singlecategory10')); ?>
                </div>

            <?php endif; ?>

     <?php endif; ?>

<!--====  End of Add Vehicle  ====-->





<!--=================================
=            ShowOrderBy            =
==================================-->

<?php
if (count($rows) > 0 ): ?>

    <?php
    $sort_arr['order_field'] = $params->get('sort_arr_order_field');
    $sort_arr['order_direction'] = $params->get('sort_arr_order_direction');
    ?>

    <?php positions_vm($params->get('singleuser03')); ?>
    <?php positions_vm($params->get('showsearch03')); ?>

                <?php $option_type = protectInjectionWithoutQuote('option'); ?>

                <?php if($option_type != 'com_simplemembership'): ?>

                  <div id="ShowOrderBy" class="
                  <?php if (!$params->get('show_input_button_search') && $params->get('show_input_add_vehicle')): ?>
                    VEH-collumn-xs-12 VEH-collumn-sm-8 VEH-collumn-md-5 VEH-collumn-md-push-4 VEH-collumn-lg-5 VEH-collumn-lg-push-4 right_position
                  <?php else: ?>
                    VEH-collumn-xs-12 VEH-collumn-sm-8 VEH-collumn-md-5 VEH-collumn-lg-5
                  <?php endif ?>
                  ">

                    <form method="POST" action="<?php echo sefRelToAbs($_SERVER["REQUEST_URI"]); ?>" name="orderForm_v">
                        <input type="hidden" id="order_direction" name="order_direction" value="<?php echo $sort_arr['order_direction']; ?>" >
                        <a title="Click to sort by this column." onclick="javascript:vm_allreordering();return false;" href="#">
                            <img alt="" src="<?php echo $mosConfig_live_site; ?>/media/system/images/sort_<?php echo $sort_arr['order_direction']; ?>.png"></a>
                        <?php echo _VEHICLE_MANAGER_LABEL_ORDER_BY; ?> <select size="1" class="inputbox" onchange="javascript:document.orderForm_v.order_direction.value='asc'; document.orderForm_v.submit();" id="order_field" name="order_field">
                            <option  value="ordering" <?php if ($sort_arr['order_field'] == "ordering") echo 'selected="selected"'; ?>> <?php
                              echo _VEHICLE_MANAGER_LABEL_ORDERING; ?></option>
                            <option value="date" <?php if ($sort_arr['order_field'] == "date") echo 'selected="selected"'; ?> >  <?php echo _VEHICLE_MANAGER_LABEL_DATE; ?> </option>
                            <option value="price" <?php if ($sort_arr['order_field'] == "price") echo 'selected="selected"'; ?> > <?php echo _VEHICLE_MANAGER_LABEL_PRICE; ?></option>
                            <option value="maker" <?php if ($sort_arr['order_field'] == "maker") echo 'selected="selected"'; ?> >  <?php echo _VEHICLE_MANAGER_LABEL_MODEL; ?></option>
                            <option value="vtitle" <?php if ($sort_arr['order_field'] == "vtitle") echo 'selected="selected"'; ?> > <?php echo _VEHICLE_MANAGER_LABEL_TITLE; ?></option></select>
                    </form>
                </div>


                <?php endif ?>

              <?php endif ?>

<!--====  End of ShowOrderBy  ====-->



<!--======================================
=            showSearchButton            =
=======================================-->

<?php if(!$params->get('wrongitemid')): ?>

<?php if ($params->get('show_input_button_search')):?>

        <div class="search_button_vehicle_container
      <?php if (count($rows) <= 0 || $option_type == 'com_simplemembership'  && $params->get('show_input_add_vehicle')): //disable order enable add button ?>
          VEH-collumn-xs-12 VEH-collumn-xs-push-0 VEH-collumn-sm-4 VEH-collumn-sm-push-4 VEH-collumn-md-3 VEH-collumn-md-push-5 VEH-collumn-lg-4 VEH-collumn-lg-push-5
      <?php elseif(!$params->get('show_input_add_vehicle') && $option_type != 'com_simplemembership' || count($rows) > 0  ): //disable  add button  enable order  ?>
          VEH-collumn-xs-12 VEH-collumn-xs-push-0 VEH-collumn-sm-4 VEH-collumn-sm-push-0 VEH-collumn-md-3 VEH-collumn-md-push-4 VEH-collumn-lg-4 VEH-collumn-lg-push-3
      <?php else: ?>
          VEH-collumn-xs-12 VEH-collumn-sm-12 VEH-collumn-md-4 VEH-collumn-lg-4
      <?php endif; ?>">

            <?php HTML_vehiclemanager::showSearchButton();  ?>

      </div>

<?php endif; ?>

<?php endif; ?>


<!--====  End of showSearchButton  ====-->


</div>
<?php endif;?>

<?php

if (count($rows) > 0) {
                    positions_vm($params->get('singleuser04')); ?>
                <?php positions_vm($params->get('showsearch04')); ?>

    <div id="list">
            <?php
            $available = false;
            $k = 0;
            $total = count($rows);
            $g_item_count = 0;
            //if (isset($_GET['lang'])) $lang = $_GET['lang']; else $lang = '*';
            foreach ($rows as $row) {
        $link = 'index.php?option=com_vehiclemanager&amp;task=view_vehicle&amp;id=' . $row->id . '&amp;catid=' . $row->catid . '&amp;Itemid=' . $Itemid;
        $g_item_count++;
        ?>
        <div class="row_auto VEH-row">

        <div class="VEH-collumn-xs-12 VEH-collumn-sm-5 VEH-collumn-md-3 VEH-collumn-lg-3">



            <div style="position:relative; text-align:center">
                        <?php
                        $vehicle = $row;
                        //for local images
                        $imageURL = $vehicle->image_link;
                        ?>

         <a href="<?php echo sefRelToAbs($link); ?>" style="text-decoration: none" >
                        <?php
                            $file_name = vm_picture_thumbnail($imageURL, $vehiclemanager_configuration['foto']['high'],
                              $vehiclemanager_configuration['foto']['width'], $watermark);

                              $file = $mosConfig_live_site . '/components/com_vehiclemanager/photos/' . $file_name;

                            echo '<img alt="' . $vehicle->vtitle . '" title="' . $vehicle->vtitle . '" src="' . $file . '" border="0" class="little">';
                        ?>
                    </a>
                     <?php
                     // print_r($params->get('show_rentstatus'));exit;
                    if ($params->get('show_rentstatus'))
                    {
                        $data1 = JFactory::getDBO();
                        $query = "SELECT  b.rent_from , b.rent_until  FROM #__vehiclemanager_rent  AS b " .
                                " LEFT JOIN #__vehiclemanager_vehicles AS c ON b.fk_vehicleid = c.id " .
                                " WHERE c.id=" . $row->id . " AND c.published='1' AND c.approved='1' AND b.rent_return IS NULL";
                        $data1->setQuery($query);
                        $rents1 = $data1->loadObjectList();
                        ?>
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

              </div>



              <div class="VEH-collumn-xs-12 VEH-collumn-sm-6 VEH-collumn-md-9 VEH-collumn-lg-9 vm_list_item_text_container">

              <span class="col_02">
                    <a href="<?php echo sefRelToAbs($link); ?>" class="category<?php echo $params->get('pageclass_sfx'); ?>">
                        <?php positions_vm(words_limit($row->vtitle, 10, '...')); ?>
                    </a>
                </span>
                <div class="vm_price">
                          <?php

      if(!incorrect_price($row->price)){

      if( ($row->price != '' && $row->priceunit != '') and ($params->get('show_pricerequest') ) ){
        if ($vehiclemanager_configuration['price_unit_show'] == '1'){
          if ($vehiclemanager_configuration['sale_separator'])
                 {
                ?>

                  <div class="col_06_07">
                  <span class="col_07">
                    <?php echo formatMoney($row->price, $vehiclemanager_configuration['sale_fraction'], $vehiclemanager_configuration['price_format']); ?>
                  </span>
                <?php

                ?>
                  <span class="col_06">
                    <?php echo $row->priceunit; ?>
                  </span>
                  </div>
                <?php
                }else {
                ?>
                  <div class="col_06_07">
                  <span class="col_07">
                    <?php echo $row->price; ?>
                  </span>
                <?php
                ?>
                  <span class="col_06">
                    <?php echo $row->priceunit; ?>
                  </span>
                  </div>
                <?php
            }
          }else{
            if ($vehiclemanager_configuration['sale_separator'])
                  {
                ?>

                  <div class="col_06_07">
                  <span class="col_06">
                    <?php echo $row->priceunit; ?>
                  </span>
                <?php

                ?>
                  <span class="col_07">
                    <?php echo formatMoney($row->price, $vehiclemanager_configuration['sale_fraction'], $vehiclemanager_configuration['price_format']); ?>
                  </span>
                  </div>
                <?php
                   }else {
                ?>
                  <div class="col_06_07">
                  <span class="col_06">
                    <?php echo $row->priceunit; ?>
                  </span>
                <?php
                ?>
                  <span class="col_07">
                    <?php echo $row->price; ?>
                  </span>
                  </div>
                <?php
              }
            }
          }
        }else{
          echo $row->price;
        }

          ?>
                </div>
      <br />
<?php if ($row->maker != '' || $row->vmodel !== 0 && trim($row->vmodel) !== "") {
    ?>
      <div class="vm_model">
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
                            } ?>
      <div class="col_03">
                                    <?php positions_vm(words_limit($row->description, 30, '...')); ?>
      </div>
      <div class="vm_type_catlist">
          <?php
                $link1 = 'index.php?option=com_vehiclemanager&amp;task=showCategory&amp;catid=' . $row->catid . '&amp;Itemid=' . $Itemid;
                ?>
          <div class="col_12">
            <i class='fa fa-tag'></i>
            <a href="<?php echo JRoute::_($link1, false); ?>" class="category<?php echo $params->get('pageclass_sfx'); ?>">
        <?php echo $row->category_titel; ?>
            </a>
            </div>
      <?php
 if ($row->year != 0) {
                            ?>
                            <div class="col_04">
                                <i class="fa fa-calendar"></i>
                                <span><?php echo _VEHICLE_MANAGER_LABEL_ISSUE_YEAR; ?>:</span>
                                <span><?php echo $row->year; ?></span>
                            </div>
        <?php }
        if ( trim($row->mileage) !== '' ) { ?>
                                <div class="col_05" >
                                    <i class="fa fa-tachometer"></i>
                                    <span><?php echo _VEHICLE_MANAGER_LABEL_MILEAGE; ?>:</span>
                                    <span><?php echo trim($row->mileage); ?></span>
                                </div>
        <?php }
        if($params->get('hits')){ ?>
          <div class="col_10_11">
            <span class="col_10">
            <?php echo "<i class='fa fa-eye'></i>" . "&nbsp;" . _VEHICLE_MANAGER_LABEL_HITS; ?>
            </span>
            <span class="col_11">
            <?php echo $row->hits; ?>
          </span>
        </div>
<?php }; ?>
  <?php
      }?>
      </div>
      </div>
  </div>  <!--  row_auto  -->
    <?php } // ENDFOR ?>
    </div>   <!--  list  -->

    <?php positions_vm($params->get('singleuser05')); ?>
    <?php positions_vm($params->get('showsearch05')); ?>
    <?php
} //if (count($rows) > 0 )

if ($params->get('show_rentstatus') && $params->get('show_rentrequest') && $params->get('rent_save'))
{// && $available)
    ?>
    <div class="componentheading<?php echo $params->get('pageclass_sfx'); ?>">
    <?php echo _VEHICLE_MANAGER_LABEL_RENT_INFORMATIONS; ?>
        <input type="hidden" name="vehicleid" id="vehicleid" value="<?php echo $row->id ?>" maxlength="80" />
    </div>
    <form action="<?php echo sefRelToAbs("index.php"); ?>" name="userForm" method="post">
        <div class="basictable basictable_005">
            <div class="row_02">
                <?php
                global $my;
                if ($my->guest)
                {
                 ?>
                <span class="col_01">
        <?php echo _VEHICLE_MANAGER_LABEL_RENT_REQUEST_NAME; ?>:<br />
                    <input class="inputbox" type="text" name="user_name" size="38" maxlength="80" />
                    <input class="inputbox" type="hidden" name="user_id" size="38" maxlength="80" value="0" />
                </span>
        <br/>
                  <span class="col_02">
        <?php echo _VEHICLE_MANAGER_LABEL_RENT_REQUEST_EMAIL; ?>:<br />
        <input class="inputbox" type="text" name="user_email" size="30" maxlength="80" />
      </span>
      <?php
    } else
        {
    ?>
                <span class="col_01">
      <?php echo _VEHICLE_MANAGER_LABEL_RENT_REQUEST_NAME; ?>:<br />
      <input class="inputbox" disabled type="text" name="user_name" size="38" maxlength="80"
        value="<?php echo $my->name; ?>" />
                </span>
      <br/>
                <span class="col_02">
      <?php echo _VEHICLE_MANAGER_LABEL_RENT_REQUEST_EMAIL; ?>:<br />
      <input class="inputbox" disabled type="text" name="user_email" size="30" maxlength="80"
        value="<?php echo $my->email; ?>" />
    </span>
        <?php
                 }
                ?>
            </div>
        </div>
        <div class="basictable basictable_006">
            <div class="row_01">
                <div class="col_01">
    <?php echo _VEHICLE_MANAGER_LABEL_RENT_REQUEST_MAILING; ?>:<br />
    <?php
  ///////////////////////////////////////////////calendar///////////////////////
    ?>
    <textarea name ="user_mailing"></textarea>
    <script language="javascript" type="text/javascript">
<?php
    $vehicle_id_fordate =  $vehicle->id;
    $date_NA = available_dates($vehicle_id_fordate);
?>
    var unavailableDates = Array();
    jQuerOs(document).ready(function() {
      var k=0;
      <?php if(!empty($date_NA)){
        foreach ($date_NA as $N_A){ ?>
          unavailableDates[k]= '<?php echo $N_A; ?>';
          k++;
        <?php } ?>
      <?php } ?>
      function unavailableFrom(date) {
        dmy = date.getFullYear() + "-" + ('0'+(date.getMonth() + 1)).slice(-2) +
          "-" + ('0'+date.getDate()).slice(-2);
        if (jQuerOs.inArray(dmy, unavailableDates) == -1) {
          return [true, ""];
        } else {
          return [false, "", "Unavailable"];
        }
      }

      function unavailableUntil(date) {
        dmy = date.getFullYear() + "-" + ('0'+(date.getMonth() + 1)).slice(-2) +
          "-" + ('0'+(date.getDate()-("<?php  if(!$vehiclemanager_configuration['special_price']['show']) echo '1';?>"))).slice(-2);
        if (jQuerOs.inArray(dmy, unavailableDates) == -1) {
          return [true, ""];
        } else {
          return [false, "", "Unavailable"];
        }
      }

      jQuerOs( "#rent_from" ).datepicker({
        minDate: "+0",
        dateFormat: "<?php echo transforDateFromPhpToJquery_vm();?>",
        beforeShowDay: unavailableFrom,
        onClose: function () {

          var rent_from = jQuerOs(" #rent_from ").val();
          var rent_until = jQuerOs(" #rent_until ").val();
          jQuerOs.ajax({
            type: "POST",
            url: "index.php?option=com_vehiclemanager&task=ajax_rent_calcualete"
              +"&vid=<?php echo $vehicle->id; ?>&rent_from="+
              rent_from+"&rent_until="+rent_until,
            data: { " #do " : " #1 " },
            update: jQuerOs(" #message-here "),
            success: function( data ) {
              jQuerOs("#message-here").html(data);
              jQuerOs("#calculated_price").val(data);
            }
          });
        }
      });

      jQuerOs( "#rent_until" ).datepicker({
        minDate: "+0",
        dateFormat: "<?php echo transforDateFromPhpToJquery_vm();?>",
        beforeShowDay: unavailableUntil,
        onClose: function () {

          var rent_from = jQuerOs(" #rent_from ").val();
          var rent_until = jQuerOs(" #rent_until ").val();
          jQuerOs.ajax({
            type: "POST",
            url: "index.php?option=com_vehiclemanager&task=ajax_rent_calcualete"
              +"&vid=<?php echo $vehicle->id; ?>&rent_from="+
              rent_from+"&rent_until="+rent_until,
            data: { " #do " : " #1 " },
            update: jQuerOs(" #message-here "),
            success: function( data ) {
              jQuerOs("#message-here").html(data);
              jQuerOs("#calculated_price").val(data);
            }
          });
        }
      });
    });
    </script>
            <div class="row_06">
                <p><?php echo _VEHICLE_MANAGER_LABEL_RENT_REQUEST_FROM; ?>:</p>
                <p><input type="text" id="rent_from" name="rent_from"></p>
            </div>
            <div class="row_07">
                <p><?php echo _VEHICLE_MANAGER_LABEL_RENT_REQUEST_UNTIL; ?>:</p>
                <p><input type="text" id="rent_until" name="rent_until"></p>
            </div>
            <div id="price_1">
                <span><?php echo    _VEHICLE_MANAGER_LABEL_PRICE. ': '; ?></span>
                <span id="message-here"> </span>
            </div>
        </div>
    </div>
    <?php } ?>
    <div style="clear:both"></div>
    <div class="basictable page_navigation">
        <div class="row_02">
                <?php
                $paginations = $arr;
                if ($paginations && ( $pageNav->total > $pageNav->limit ))
                {
                    echo $pageNav->getPagesLinks();
                }
                ?>
        </div>
    </div>
    <input type="hidden" id ="calculated_price" name="calculated_price" value="">
    <input type="hidden" name="option" value="<?php echo $option; ?>"/>
    <input type="hidden" name="task" value="save_rent_request_vehicle"/>
<?php
if ($option != 'com_vehiclemanager')
{
    ?>
        <input type="hidden" name="tab" value="getmyvehiclestab"/>
        <input type="hidden" name="is_show_data" value="1"/>
    <?php
}
?>
    <input type="hidden" name="Itemid" value="<?php echo $Itemid; ?>"/>
    <input type="hidden" name="vehicleid" value="<?php echo $rows[0]->id; ?>"/>
</form>
<?php
if ($is_exist_sub_categories && $vehiclemanager_configuration['single_subcategory_show']['show'] == 1)
{
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
<style type="text/css">
    #list img.little{
        /*height: <?php echo $params->get('minifotohigh'); ?>px;
        width:<?php echo $params->get('minifotowidth'); ?>px;*/
    }
    .okno_V{
        width: <?php echo $vehiclemanager_configuration['fotogallery']['width'] + 10; ?>px;
        height: <?php echo $vehiclemanager_configuration['fotogallery']['high'] + 65; ?>px;
    }
    .okno_V img{
        width:<?php echo $vehiclemanager_configuration['fotogallery']['width']; ?>px;
        max-height: <?php echo $vehiclemanager_configuration['fotogallery']['high']; ?>px;
    }
    .okno_V .textvehicle{
        width:<?php echo $vehiclemanager_configuration['fotogallery']['width']; ?>px;
    }
    div#price_1{
        float: left;
    }
    p#calculate_1{
        clear: both;
        margin: 0 0 9px;
    }
</style>
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