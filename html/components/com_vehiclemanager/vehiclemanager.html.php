<?php
if (!defined('_VALID_MOS') && !defined('_JEXEC')) die('Direct Access to ' . basename(__FILE__) . ' is not allowed.');

/**
 *
 * @package  VehicleManager
* @copyright 2020 by Ordasoft
* @author Andrey Kvasnevskiy - OrdaSoft (akbet@mail.ru); Rob de Cleen (rob@decleen.com);
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* Homepage: https://ordasoft.com/
*
 * */
//require_once($mosConfig_absolute_path . "/libraries/joomla/plugin/helper.php");
jimport( 'joomla.plugin.helper' );
global $mosConfig_live_site;
///require_once($mosConfig_absolute_path . "/includes/HTML_toolbar.php");
//require_once($mosConfig_absolute_path . "/administrator/includes/toolbar.php");
jimport('joomla.html.toolbar');
if (version_compare(JVERSION, "3.0.0", "lt"))
    require_once($mosConfig_absolute_path . "/libraries/joomla/html/toolbar.php");


require_once($mosConfig_absolute_path . "/components/com_vehiclemanager/vehiclemanager.php");
require_once($mosConfig_absolute_path . "/components/com_vehiclemanager/functions.php");
//require_once($mosConfig_absolute_path . "/administrator/components/com_vehiclemanager/menubar_ext.php");

$doc = JFactory::getDocument();
$GLOBALS['doc'] = $doc;
$g_item_count = 0;

// include css
if(checkStylesIncludedVEH("/jquerOs-ui.min.css") === false ) {
    $doc->addStyleSheet(JURI::root() .'components/com_vehiclemanager/includes/jquerOs-ui.min.css');
}
if(checkStylesIncludedVEH("/font-awesome.min.css") === false ) {
    $doc->addStyleSheet('//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css');
}
if(checkStylesIncludedVEH("/bootstrapVEH.css") === false ) {
    $doc->addStyleSheet(JURI::root() .'components/com_vehiclemanager/includes/bootstrapVEH.css' );
}
if(checkStylesIncludedVEH("/vehiclemanager.css") === false ) {
    $doc->addStyleSheet(JURI::root() .'components/com_vehiclemanager/includes/vehiclemanager.css' );
}
if(checkStylesIncludedVEH("/custom.css") === false ) {
    $doc->addStyleSheet(JURI::root() .'components/com_vehiclemanager/includes/custom.css' );
}
if(checkStylesIncludedVEH("/tabcontent.css") === false ) {
    $doc->addStyleSheet(JURI::root() .'components/com_vehiclemanager/TABS/tabcontent.css');
}
//$doc->addStyleSheet('components/com_vehiclemanager/lightbox/css/lightbox.css');~~

// include js
if(checkJavaScriptIncludedVEH("/jQuerOs-2.2.4.min.js") === false ) {
    $doc->addScript(JURI::root() .'components/com_vehiclemanager/includes/jQuerOs-2.2.4.min.js');
}
$doc->addScriptDeclaration("jQuerOs=jQuerOs.noConflict();");
if(checkJavaScriptIncludedVEH("/jquerOs-ui.min.js") === false ) {
    $doc->addScript(JURI::root() ."components/com_vehiclemanager/includes/jquerOs-ui.min.js");
}
//$doc->addScript("components/com_vehiclemanager/includes/jquery-ui.js");
//$doc->addScriptDeclaration("jQuerOs=jQuerOs.noConflict();");


$doc->addScript(JURI::root() .'components/com_vehiclemanager/includes/functions.js');
$doc->addScript(JURI::root() .'components/com_vehiclemanager/TABS/tabcontent.js');
//doc->addScript('components/com_vehiclemanager/lightbox/js/lightbox-2.6.min.js');~~

$doc->addScript(JURI::root() .'components/com_vehiclemanager/includes/jquer.raty.js');

//add fancybox & swiper slider
if(checkJavaScriptIncludedVEH("/swiper-os.js") === false ) {
    $doc->addScript(JURI::root() . 'components/com_vehiclemanager/includes/swiper-os.js');
}
if(checkStylesIncludedVEH("/swiper.css") === false ) {
    $doc->addStyleSheet(JURI::root() . 'components/com_vehiclemanager/includes/swiper.css');
}
if(checkStylesIncludedVEH("/jquer.os_fancybox.css") === false ) {
    $doc->addStyleSheet(JURI::root() . 'components/com_vehiclemanager/includes/jquer.os_fancybox.css');
}

$doc->addStyleSheet(JURI::root() . 'components/com_vehiclemanager/includes/styleFuncyboxThumbs.css');

//$doc->addScript('components/com_vehiclemanager/includes/jquery-3.2.1.min.js');
if(checkJavaScriptIncludedVEH("/jquer.os_fancybox.js") === false ) {
    $doc->addScript(JURI::root() . 'components/com_vehiclemanager/includes/jquer.os_fancybox.js');
}
//end add fancybox & swiper slider


class HTML_vehiclemanager
{


static function showRentRequest(& $vehicles, & $currentcat, & $params, & $tabclass, & $catid, & $sub_categories, $is_exist_sub_categories)
{

        ///require_once( $GLOBALS['mosConfig_absolute_path'] . '/includes/pageNavigation.php' );
        ///$pageNav = new mosPageNav(0, 0, 0);
        $pageNav = new JPagination(0, 0, 0); // for J 1.6

        HTML_vehiclemanager::displayVehicles($vehicles, $currentcat, $params, $tabclass, $catid, $sub_categories, $is_exist_sub_categories, $pageNav);
        // add the formular for send to :-)
    }

    static function displayVehicles(&$rows, $currentcat, &$params, $tabclass, $catid, $categories, $is_exist_sub_categories, &$pageNav, $layout = "list")
    {
        global $mosConfig_absolute_path;
        $type = 'alone_category';
        if(empty($layout)){
            $layout = 'default';
        }

        require getLayoutPath::getLayoutPathCom('com_vehiclemanager',$type, $layout);
    }
    // add in version 3.9 for search_result
    static function displaySearchVehicles(&$rows, $currentcat, &$params, $tabclass, $catid, $categories, $is_exist_sub_categories, &$pageNav, $layout = "list", $layoutsearch="default")
    {
        global $mosConfig_absolute_path, $option;
        //print_r($layout);exit;

        //show on display serch form for search page result
        global $vehiclemanager_configuration;

        $type = 'search_result';
        if(empty($layout))
            $layout = 'default';

        if((!isset($layoutsearch) || empty($layoutsearch)) && isset($vehiclemanager_configuration['default_search_layout']) && !empty($vehiclemanager_configuration['default_search_layout'])){
            $layoutsearch = $vehiclemanager_configuration['default_search_layout'];
        }

        if($vehiclemanager_configuration['search_form_on_search_page_result_show']==1){
            PHP_vehiclemanager::showSearchVehicles($option, $catid, $option, $layoutsearch);
        }

        require_once getLayoutPath::getLayoutPathCom('com_vehiclemanager',$type, $layout);
    }

    static function displayAllVehicles(&$rows, &$params, $tabclass, &$pageNav, $layout = "list")
    {
        global $mosConfig_absolute_path;
        $type = 'all_vehicle';
        if(empty($layout))
            $layout = 'default';

        require getLayoutPath::getLayoutPathCom('com_vehiclemanager',$type, $layout);
    }


    /**
     * Displays the vehicle
     * rent Status
     */
    static function displayVehicle(& $vehicle, & $tabclass, & $params, & $currentcat, & $rating,
      & $vehicle_photos, &$videos, &$tracks, & $id, & $catid, & $vehicle_feature, & $currencys_price, & $layout = 'default')
    {

        if(!$catid) $catid = protectInjectionWithoutQuote('catid');

        global $mosConfig_absolute_path;
        $type = 'view_vehicle';
        if(empty($layout))
            $layout = 'default';
        require getLayoutPath::getLayoutPathCom('com_vehiclemanager',$type, $layout);
    }

// END function displayVehicle




                /**
                 * Display links to categories
                 */
                static function showCategories(&$params, &$categories, &$catid, &$tabclass, &$currentcat, $layout)
                {
                    global $mosConfig_absolute_path;
                    $type = 'all_categories';
                    if(empty($layout))
                        $layout = 'default';
                    require getLayoutPath::getLayoutPathCom('com_vehiclemanager',$type, $layout);

                }

//*********************************   for "serch button"   ******************************************
            static function showSearchButton(){
                global $mosConfig_live_site, $Itemid;
                ?>
                <div class="search_button_vehicle basictable_47 basictable">
                    <a href="<?php echo sefRelToAbs("index.php?option=com_vehiclemanager&task=show_search_vehicle&Itemid=$Itemid"); ?>" align="right">
                        <i class="fa fa-search"></i>
            <?php echo _VEHICLE_MANAGER_LABEL_SEARCH; ?>&nbsp;
                    </a>
                </div>
            <?php
            }
//*********************************   end for "search button"   ******************************************



    static function listCategoriesBigImg(&$params, $cat_all, $catid, $tabclass, $currentcat)
    {
        global $Itemid, $mosConfig_live_site;
        ?>
        <?php positions_vm($params->get('allcategories04')); ?>
        <div class="basictable_12 basictable">
            <div class="row_02 VEH-row">
                <?php positions_vm($params->get('allcategories05')); ?>

                <?php
                HTML_vehiclemanager::showInsertSubCategoryBigImg($catid, $cat_all, $params, $tabclass, $Itemid, 0);
                ?>
            </div>
        </div>
        <?php positions_vm($params->get('allcategories06')); ?>

        <?php
    }


    static function listCategories(&$params, $cat_all, $catid, $tabclass, $currentcat)
    {
        global $Itemid, $mosConfig_live_site;
        ?>
        <?php positions_vm($params->get('allcategories04')); ?>
        <div class="basictable_12 basictable">
            <div class="row_01">
                <span class=" col_01 sectiontableheader<?php echo $params->get('pageclass_sfx');
                 ?>"><?php echo _VEHICLE_MANAGER_LABEL_CATEGORY; ?></span>
            <span class="col_003 sectiontableheader<?php echo $params->get('pageclass_sfx');
               ?>"><?php echo _VEHICLE_MANAGER_LABEL_VEHICLES; ?> </span>

           </div>
           <div class="row_02">
            <span class="col_01">
                <?php positions_vm($params->get('allcategories05')); ?>

                <?php
                HTML_vehiclemanager::showInsertSubCategory($catid, $cat_all, $params, $tabclass, $Itemid, 0);
                ?>
            </span>
        </div>
    </div>
    <?php positions_vm($params->get('allcategories06')); ?>

    <?php
}

            /*
             * function for show subcategory
             */

            static function showInsertSubCategory($id, $cat_all, $params, $tabclass, $Itemid, $deep)
            {
                global $g_item_count, $vehiclemanager_configuration, $mosConfig_live_site;
                $deep++;
                for ($i = 0; $i < count($cat_all); $i++) {
                    if (($id == $cat_all[$i]->parent_id) && ($cat_all[$i]->display == 1))
                    {
                        $g_item_count++;

                        $link = 'index.php?option=com_vehiclemanager&amp;task=alone_category&amp;catid=' .
                        $cat_all[$i]->id . '&amp;Itemid=' . $Itemid;
                        ?>
                        <div class="basictable_13 basictable">
                            <div class="row_01 VEH-row">





                                        <div class="col_3 VEH-collumn-xs-12 VEH-collumn-sm-8 VEH-collumn-md-9 VEH-collumn-lg-10">

                                        <?php if ($deep != 1): ?>


                                <div class="cat_item_sub_cat_marker <?php echo "deep-level-".$deep;?>">

                                        <span class="category_icon_level">|_</span>

                                </div>

                                <?php endif ?>

                                <div class=" VEH-collumn-xs-12 VEH-collumn-sm-2 VEH-collumn-md-2 VEH-collumn-lg-2 vm_cat_img">
                                    <?php if (($params->get('show_cat_pic')) && ($cat_all[$i]->image != ""))
                                    { ?>
                                    <img src="<?php echo JURI::root(); ?>/images/stories/<?php echo $cat_all[$i]->image; ?>"
                                    alt="picture for subcategory"   />
                                    <?php } else
                                    {
                                        ?>
                                        <a <?php echo "href=" . sefRelToAbs($link); ?> class="category<?php
                                         echo $params->get('pageclass_sfx'); ?>" style="text-decoration: none"><img
                                         src="<?php echo JURI::root(); ?>/components/com_vehiclemanager/images/folder.png" alt="picture for subcategory"
                                          /></a>
                                         <?php } ?>
                                </div>

                                        <?php
                                        $count_veh = $cat_all[$i]->vehicles * 1;
                //if ($count_veh != 0)
                //{
                                        $disable_link = "";
                                        if ($cat_all[$i]->published != 1)
                                            $disable_link = "href='#' onClick = 'return false'";
                                        else
                                            $disable_link = "href='" . sefRelToAbs($link) . "'";
                                        ?>
                                        <a <?php echo $disable_link; ?> class="category<?php echo $params->get('pageclass_sfx'); ?>">
                                            <?php
                //} else
                //{
                //    echo "";
                //}
                                            ?>
                                            <?php echo $cat_all[$i]->title; ?>
                                        </a>
                                    </div>

                                    <div class=" VEH-collumn-xs-12 VEH-collumn-sm-1 VEH-collumn-md-1 VEH-collumn-lg-1 cat_item_count">
                                        <?php if ($cat_all[$i]->vehicles == '')
                                        echo "0";
                                        else echo $cat_all[$i]->vehicles; ?>
                                    </div>

                             </div>
                         </div>
                         <?php
                         if ($GLOBALS['subcategory_show'])
                            HTML_vehiclemanager::showInsertSubCategory($cat_all[$i]->id, $cat_all,
                             $params, $tabclass, $Itemid, $deep);
                }//end if ($id == $cat_all[$i]->parent_id)
            }//end for(...)
        }

            /*
             * function for show subcategory
             */

            static function showInsertSubCategoryBigImg($id, $cat_all, $params, $tabclass, $Itemid, $deep)
            {
                global $g_item_count, $vehiclemanager_configuration, $mosConfig_live_site;
                $mosConfig_absolute_path = $GLOBALS['mosConfig_absolute_path'] = JPATH_SITE;
                $deep++;
                for ($i = 0; $i < count($cat_all); $i++) {
                    if (($id == $cat_all[$i]->parent_id) && ($cat_all[$i]->display == 1))
                    {
                        $g_item_count++;

                        $link = 'index.php?option=com_vehiclemanager&amp;task=alone_category&amp;catid=' .
                        $cat_all[$i]->id . '&amp;Itemid=' . $Itemid;
                        ?>
                        <div class="row_img VEH-collumn-lg-4 VEH-collumn-md-4 VEH-collumn-sm-6 VEH-collumn-xs-12 <?php echo $tabclass[($g_item_count % 2)]; ?>">
                            <div class="col_01" >
                            <?php if (($params->get('show_cat_pic')) && ($cat_all[$i]->image != ""))
                            { ?>
                            <a href="<?php echo sefRelToAbs($link);?>" class="category<?php
                              echo $params->get('pageclass_sfx'); ?>" style="text-decoration: none; " >
                              <?php
                              if(!file_exists($mosConfig_absolute_path . '/components/com_vehiclemanager/photos/' .
                                  $cat_all[$i]->image ) )
                                copy ( $mosConfig_absolute_path."/images/stories/" . $cat_all[$i]->image,
                                    $mosConfig_absolute_path . '/components/com_vehiclemanager/photos/'  .
                                    $cat_all[$i]->image);
                            $file_name = vm_picture_thumbnail( $cat_all[$i]->image,
                              $vehiclemanager_configuration['fotogallery']['high'],
                              $vehiclemanager_configuration['fotogallery']['width']);
                            $file=$mosConfig_live_site . '/components/com_vehiclemanager/photos/'. $file_name;
                            echo '<img alt="picture for subcategory" title="'.$cat_all[$i]->title.'" src="' .$file. '">';
                            ?>
                        </a>

                        <?php } else
                        {
                            ?>
                            <a href="<?php echo sefRelToAbs($link);?>" class="category<?php
                              echo $params->get('pageclass_sfx'); ?>" style="text-decoration: none; " >
                              <?php
                              if(!file_exists($mosConfig_absolute_path .
                                  '/components/com_vehiclemanager/photos/folder.png'  ) )
                                copy ( $mosConfig_absolute_path."/components/com_vehiclemanager/images/folder.png" ,
                                    $mosConfig_absolute_path . '/components/com_vehiclemanager/photos/folder.png');
                            $file_name = vm_picture_thumbnail( 'folder.png',
                              $vehiclemanager_configuration['fotogallery']['high'],
                              $vehiclemanager_configuration['fotogallery']['width']);
                            $file=$mosConfig_live_site . '/components/com_vehiclemanager/photos/'. $file_name;
                            echo '<img alt="picture for subcategory" title="'.$cat_all[$i]->title.'" src="' .$file. '">';
                            ?>
                        </a>
                        <?php } ?>
                        <div class="bigm_title"><h4>
                            <a href="<?php echo sefRelToAbs($link);?>">
                                <?php echo $cat_all[$i]->title ?>(<?php if ($cat_all[$i]->vehicles == '')
                                echo "0";else echo $cat_all[$i]->vehicles; ?>)
                            </a>
                        </h4></div>
                    </div>
                </div>
                <?php
                }//end if ($id == $cat_all[$i]->parent_id)
            }//end for(...)
        }

//end function showInsertSubCategory($id, $cat_all)

        static function showSearchVehicles($params, $currentcat, $clist, $option, &$arraymakersmodels, $layout) {
            global $mosConfig_absolute_path;
        // $layout = $params->get('showsearchvehiclelayout', 'default'); // need when not realize layout select from admin
            $type = 'show_search_vehicle';
            if(empty($layout))
                $layout = 'default';
            require getLayoutPath::getLayoutPathCom('com_vehiclemanager',$type, $layout);
        }



static function showRentRequestThanks($params, $catid, $currentcat,$vehicle=NULL,$time_difference =NULL, $buy_rent=NULL)
{
    global $Itemid, $doc, $mosConfig_live_site, $hide_js, $option, $vehiclemanager_configuration;
    ?>
    <div class="componentheading<?php echo $params->get('pageclass_sfx'); ?>">
        <?php echo $currentcat->header; ?>
    </div>

    <div class="table_save_add_vehicle basictable">
        <?php if ($currentcat->img != null)
        { ?>
        <div class="col_01"><img src="<?php echo $currentcat->img; ?>" alt="?" /></div>
        <?php
    }
    ?>
    <div class="col_02"><?php echo $currentcat->descrip; ?></div>

    <?php
    if($vehicle){
        if($time_difference){
                $amount = $time_difference[0]; // price
                $currency_code = $time_difference[1] ; // priceunit
            }
            else{
                $amount= $vehicle->price;
                $currency_code = $vehicle->priceunit;
            }

            $amountLine='';
            $amountLine .= '<input type="hidden" name="amount" value="'.$amount.'" />'."\n";
        }

        ?>



        <?php


        if ($option != "com_vehiclemanager")
        {
            $path = $mosConfig_live_site . "/index.php?option=" . $option .
            "&amp;task=my_vehicles&amp;is_show_data=1&amp;Itemid=" . $_REQUEST['Itemid'] . "#tabs-2";
            $path_other = $mosConfig_live_site . "/index.php?option=" . $option
            . "&amp;task=view_user_vehicles&amp;is_show_data=1&amp;Itemid=" . $_REQUEST['Itemid'];
        } else
        {
            $path = $mosConfig_live_site .
            "/index.php?option=com_vehiclemanager&amp;task=my_vehicles&amp;Itemid=" . $_REQUEST['Itemid'];
            $path_other = $mosConfig_live_site .
            "/index.php?option=com_vehiclemanager&amp;task=showCategory&amp;catid=" .
            $catid . "&amp;Itemid=" . $_REQUEST['Itemid'];
        }
        ?>


        <div class="basictable_15 basictable">
            <span>

                <?php

            if($vehicle){
                if(($vehiclemanager_configuration['payment_buy_status']['show'] !='0' || $vehiclemanager_configuration['payment_rent_status']['show'] != '0')
                && !incorrect_price($vehicle->price)
                && $vehicle->price > 0 ) {
                    if(isset($amount) && isset($currency_code)) {

                        echo '<br/> '._VEHICLE_MANAGER_TOTAL_PRICE .$amount.' '.$currency_code;

                        $vehicle1 = $vehicle;
                        $vehicle1->price = $amount;
                        $vehicle1->priceunit= $currency_code;

                        HTML_vehiclemanager::getSaleForm($vehicle1, $vehiclemanager_configuration);
                    }
                }
            }
                ?>

                <input class="button" type="submit" ONCLICK="window.location.href='<?php
                //$user = JFactory::getUser();
                if($buy_rent == 1){
                    $trig = $vehiclemanager_configuration['input_link_rent'];
                }
                elseif($buy_rent == 2){
                    $trig = $vehiclemanager_configuration['input_link_sale'];
                }
                else{
                    $trig = 'default';
                }

                if($trig == 'default'){
                    $user = JFactory::getUser();

                    if(!$user->guest) {
                        if ($catid == 0) {
                            echo $path;
                        } else if (isset($_REQUEST['where']) && $_REQUEST['where'] == 2) {
                            echo sefRelToAbs($path_other);
                        } else {
                            echo sefRelToAbs($path);
                        }
                    } else {
                        echo sefRelToAbs($mosConfig_live_site . "/index.php?option=" . $option .
                           "&amp;Itemid=" . $_REQUEST['Itemid']);
                       }
                }
                elseif ($trig == 'redirect to current vehicle') {
                    $path = $mosConfig_live_site . "/index.php?option=com_vehiclemanager&task=view_vehicle&id=" . $_SESSION['id'] . "&catid=" . $_SESSION['catid'] . "&Itemid=" . $_SESSION['Itemid'];
                    echo $path;
                }
                else{
                    $path = $trig;
                    echo $path;
                }?>'"
                   value="OK">
               </span>
           </div>
       </div>

       <div class="basictable_16 basictable">
        <?php mosHTML::BackButton($params, $hide_js); ?>
    </div>

    <?php

}

//********************************************************************************************************
//********************************************************************************************************


static function getSaleForm($vehicle,$vehiclemanager_configuration){
    if($vehicle)
    {
        getHTMLPayPalVM($vehicle,$vehiclemanager_configuration['plugin_name_select']);
    }
}


//********************************************************************************************************
//********************************************************************************************************

static function showTabs(&$params, &$userid, &$username, &$comprofiler, &$option){
  global $Itemid; ?>
  <div class='button_margin'>
    <?php
    if ($params->get('show_cb')){
      if ($params->get('show_cb_registrationlevel')){ ?>
      <span class='vehicle_button'>
        <a href="<?php echo JRoute::_('index.php?option=' . $option .
         '&task=my_vehicles&tab=getmyvehiclesTab&name=' . $username .
         '&Itemid=' . $Itemid . '&is_show_data=1'); ?>">
         <?php echo JText::_(_VEHICLE_MANAGER_LABEL_TITLE_MY_VEHICLES); ?>
     </a>
 </span>
 <?php
}
}

if ($params->get('show_rent')){
  if ($params->get('show_rent_registrationlevel')){ ?>
  <span class='vehicle_button'>
      <a href="<?php echo JRoute::_('index.php?option=' . $option . '&task=rent_requests_vehicle&Itemid=' .
      $Itemid . $comprofiler); ?>"><?php echo JText::_(_VEHICLE_MANAGER_LABEL_TITLE_RENT_REQUEST); ?></a>
  </span>
  <?php
}
}
if ($params->get('show_buy')){
  if ($params->get('show_buy_registrationlevel')){ ?>
  <span class='vehicle_button'>
      <a href="<?php echo JRoute::_('index.php?option=' . $option .
      '&task=buying_requests_vehicle&Itemid=' . $Itemid . $comprofiler); ?>">
      <?php echo JText::_(_VEHICLE_MANAGER_LABEL_BUTTON_BUY_VEHICLE); ?>
  </a>
</span>
<?php
}
}
if ($params->get('show_history')){
  if ($params->get('show_history_registrationlevel')){ ?>
  <span class='vehicle_button'>
    <a href="<?php echo JRoute::_('index.php?option=' . $option .
     '&task=rent_history_vehicle&name=' . $username .
     '&user=' . $userid . '&Itemid=' . $Itemid .
     $comprofiler); ?>">
     <?php echo JText::_(_VEHICLE_MANAGER_TOOLBAR_ADMIN_RENT_HISTORY); ?>
 </a>
</span>
<?php
}
}
?>
</div>
<?php
}




  static function showRentVehicles($option, $main_veh, & $rows, & $userlist, $type)
  {
    global $my, $mosConfig_live_site, $mainframe, $doc, $Itemid,$vehiclemanager_configuration;
    ?>
    <div id="overDiv" style="position:absolute; visibility:hidden; z-index:1000;"></div>
    <form action="index.php" method="get" name="adminForm" id="adminForm">
        <?php
        if ($type == "rent" || $type == "edit_rent")
        {
            ?>
            <div class="my_vehicles_table_rent">
                <div class="my_vehicles">
                    <span class="col_01"><?php echo _VEHICLE_MANAGER_LABEL_RENT_TO . ":"; ?></span>
                    <span class="col_02"><?php echo $userlist; ?></span>
                </div>

                <div class="my_vehicles">
                    <span class="col_01"><?php echo _VEHICLE_MANAGER_LABEL_RENT_USER . ":"; ?></span>
                    <span class="col_02"><input type="text" name="user_name" class="inputbox" /></span>
                </div>
                <div class="my_vehicles">
                    <span class="col_01"><?php echo _VEHICLE_MANAGER_LABEL_RENT_EMAIL . ":"; ?></span>
                    <span class="col_02"><input type="text" name="user_email" class="inputbox" /></span>
                </div>

                <script type="text/javascript">
                jQuerOs(document).ready(function($) {

                    jQuerOs('#userid').change(function(event) {
                        if(jQuerOs(this).val() == '-1'){
                          jQuerOs('.my_vehicles [name=user_name]').val('');
                          jQuerOs('.my_vehicles [name=user_email]').val('');
                          jQuerOs('[name=user_name], [name=user_email]').removeAttr('readonly');
                        }else{
                          jQuerOs.ajax({
                            type: "POST",
                            url: "<?php echo $mosConfig_live_site;?>/index.php?option=com_vehiclemanager&task=getUserData&userId="+jQuerOs(this).val()+"&format=raw",
                            success: function(user){
                              var user = jQuerOs.parseJSON(user);
                              jQuerOs('[name=user_name], [name=user_email]').attr('readonly','readonly');
                              jQuerOs('.my_vehicles [name=user_name]').val(user.name);
                              jQuerOs('.my_vehicles [name=user_email]').val(user.email);
                            }
                          });
                        }
                    });
                });
                </script>

                <script>
                    Date.prototype.toLocaleFormat = function(format) {
                        var f = {Y : this.getYear() + 1900,m : this.getMonth() +
                          1,d : this.getDate(),H : this.getHours(),M : this.getMinutes(),S : this.getSeconds()}
                          for(k in f)
                            format = format.replace('%' + k, f[k] < 10 ? "0" + f[k] : f[k]);
                        return format;
                    };

                    window.onload = function ()

                    {
                        var today = new Date();
                        var date = today.toLocaleFormat("<?php echo $vehiclemanager_configuration['date_format'] ?>");
                        document.getElementById('rent_from').value = date;
                        document.getElementById('rent_until').value = date;
                    };

                </script>
                <!--///////////////////////////////calendar///////////////////////////////////////-->
                <script language="javascript" type="text/javascript">
                    <?php
                    $veh_id_fordate =  $main_veh->id;
                    $date_NA = available_dates($veh_id_fordate);
                    ?>
                    var unavailableDates = Array();
                    jQuerOs(document).ready(function() {
                    //var unavailableDates = Array();
                    var k=0;
                    <?php if(!empty($date_NA)){?>
                    <?php foreach ($date_NA as $N_A){ ?>
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
                    });

                  jQuerOs( "#rent_until" ).datepicker({
                    minDate: "+0",
                    dateFormat: "<?php echo transforDateFromPhpToJquery_vm();?>",
                    beforeShowDay: unavailableUntil,
                    });

});
</script>
<!--///////////////////////////////////////////////////////////////////////////-->
<div class="my_real">
    <span class="col_01"><?php echo _VEHICLE_MANAGER_LABEL_RENT_FROM . ":"; ?></span>
    <p><input type="text" id="rent_from" name="rent_from"></p>
</div>
<div class="my_real">
    <span class="col_01"><?php echo _VEHICLE_MANAGER_LABEL_RENT_UNTIL; ?></span>
    <p><input type="text" id="rent_until" name="rent_until"></p>
</div>
</div>
<?php } else
{ ?>
    &nbsp;
    <?php }
    $all = JFactory::getDBO();
    $query = "SELECT * FROM #__vehiclemanager_rent";
    $all->setQuery($query);
    $num = $all->loadObjectList();
    ?>

    <div class="basictable_19">
        <div class="row_01">
            <span class="col_01">
                <?php if ($type != 'rent')
                { ?>
                <input type="checkbox" name="toggle" value="" onClick="vm_checkAll(this);" />
                <span class="toggle_check">check All</span>
                <?php } ?>
            </span>

        </div>

        <?php
        if ($type == "rent")
        {
            ?>
            <td align="center">  <input class="inputbox"  type="checkbox"
              name="checkVehicle" id="checkVehicle" size="0" maxlength="0" value="on" /></td>
              <?php
          } else if ($type == "edit_rent"){ ?>
          <input type="hidden"  name="checkVehicle" id="checkVehicle" value="on" /></td>

          <?php
      }

      $assoc_title = '';
      for ($t = 0, $z = count($rows); $t < $z; $t++) {
          if($rows[$t]->id != $main_veh->id) $assoc_title .= $rows[$t]->vtitle;
      }

      print_r("
          <td align=\"center\">". $main_veh->id ."</td>
          <td align=\"center\">" . $main_veh->vehicleid . "</td>
          <td align=\"center\">" . $main_veh->vtitle . " ( " . $assoc_title ." ) " . "</td>
      </tr>");

      for ($j = 0, $n = count($rows); $j < $n; $j++) {
        $row = $rows[$j];
        ?>
        &nbsp;

        <input class="inputbox" type="hidden"  name="id" id="id" size="0"
        maxlength="0" value="<?php echo $main_veh->id; ?>" />
        <input class="inputbox" type="hidden"  name="vtitle" id="vtitle" size="0"
        maxlength="0" value="<?php echo $row->vtitle; ?>" />
        <?php
        $vehicle_id = $row->id;
        $data = JFactory::getDBO();
        $query = "SELECT * FROM #__vehiclemanager_rent WHERE fk_vehicleid =" . $vehicle_id .
                 " ORDER BY rent_return "; // AND id =50"
                 $data->setQuery($query);
                 $allrent = $data->loadObjectList();


                 $num = 1;
                 for ($i = 0, $n2 = count($allrent); $i < $n2; $i++) {
                    ?>

                    <div class="box_rent_vm">

                      <div class="row_01 row_rent_vm">
                        <?php if (!isset($allrent[$i]->rent_return) && $type != "rent")
                        { ?>
                        <span class="rent_check_vid">
                          <input type="checkbox" id="cb<?php echo $i; ?>" name="vid[]" value="<?php
                          echo $allrent[$i]->id; ?>" onClick="isChecked(this.checked);" />
                      </span>
                      <?php } else
                      { ?>
                      <?php } ?>
                      <span class="col_01">id</span>
                      <span class="col_02"><?php echo $num; ?></span>
                  </div>

                  <div class="row_02 row_rent_vm">
                    <span class="col_01"><?php echo _VEHICLE_MANAGER_LABEL_VEHICLEID; ?>:</span>
                    <span class="col_02"><?php echo $row->vehicleid; ?></span>
                </div>

                <div class="row_03 row_rent_vm">
                    <?php echo $row->vtitle; ?>
                </div>

                <div class="from_until_return">

                  <div class="row_04 row_rent_vm">
                    <span class="col_01"><?php echo _VEHICLE_MANAGER_LABEL_RENT_FROM; ?></span>
                    <span class="col_02"><?php echo date_transform_vm($allrent[$i]->rent_from); ?></span>
                </div>
                <br />
                <div class="row_05 row_rent_vm">
                    <span class="col_01"><?php echo _VEHICLE_MANAGER_LABEL_RENT_UNTIL; ?></span>
                    <span class="col_02"><?php echo date_transform_vm($allrent[$i]->rent_until); ?></span>
                </div>
                <br />
                <div class="row_06 row_rent_vm">
                    <span class="col_01"><?php echo _VEHICLE_MANAGER_LABEL_RENT_RETURN; ?></span>
                    <span class="col_02"><?php echo date_transform_vm($allrent[$i]->rent_return); ?></span>
                </div>

            </div>
        </div>
        <?php
        if ($allrent[$i]->fk_userid != null)
            print_r("<div class='rent_user'>" . $allrent[$i]->user_name . "</div>");
        else
            print_r("<div class='rent_user'>" . $allrent[$i]->user_name . ": " . $allrent[$i]->user_email . "</div>");
        $num++;
    }
}
?>
</div> <!-- basictable_19  -->

<input type="hidden" name="option" value="<?php echo $option; ?>" />
<input type="hidden" id="adminFormTaskInput" name="task" value="" />
<input type="hidden" name="save" value="1" />
<input type="hidden" name="boxchecked" value="1" />
<?php
if ($option != "com_vehiclemanager")
{
    ?>
    <input type="hidden" name="tab" value="getmyvehiclesTab" />
    <input type="hidden" name="is_show_data" value="1" />
    <?php
}
?>
<input type="hidden" name="Itemid" value="<?php echo $Itemid; ?>" />

<?php if ($type == "rent")
{ ?>
    <input type="button" name="rent_save"
    value="<?php echo _VEHICLE_MANAGER_LABEL_BUTTON_RENT; ?>" onclick="vm_buttonClickRent(this)"/>
    <?php } ?>
    <?php if ($type == "rent_return")
    { ?>
    <input type="button" name="rentout_save"
    value="<?php echo _VEHICLE_MANAGER_LABEL_RENT_RETURN; ?>" onclick="vm_buttonClickRent(this)"/>
    <?php } ?>
</form>
<?php
}




static function showCaptchaVehicles($key="") {
    global $my, $Itemid, $vehiclemanager_configuration;
    $user_guest = userGID_VM($my->id);
        // if (($user_guest == 0) || ($user_guest < 0)) {

    /*//Check enabled plugin captcha-recaptcha
    $recaptchaPluginEnabled = JPluginHelper::isEnabled('captcha', 'recaptcha');

    //Check enable option captcha-recaptcha in admin form
    $app = JFactory::getConfig();
    $recaptchaAdminEnabled = false;
    if($app->get('captcha') == 'recaptcha'){
      $recaptchaAdminEnabled = true;
    }

    //Check enable google recaptcha in vehicle settings
    $gooleRecaptchaShow = false;
    if($recaptchaPluginEnabled && $recaptchaAdminEnabled && $vehiclemanager_configuration['google_captcha']['show']){
      $gooleRecaptchaShow = true;
    }*/

    $googleRecaptchaEnabled = vh_check_enabled_google_captcha_recaptcha();

    // if($gooleRecaptchaShow){
    if($googleRecaptchaEnabled){
        $captcha = JCaptcha::getInstance('recaptcha', array('namespace' => 'captcha_keystring_'.$key ));
        echo $captcha->display('recaptcha', 'recaptcha','required');
    }else{
    ?>
    <div class="row_06">
        <span class="col_01">
            <!--*********************************   begin insetr image   **********************************-->
            <?php

            // It is only need for output message "Error Captcha plugin not set or not found. Please contact a site administrator" on add vehicle page
            if ( $vehiclemanager_configuration['google_captcha']['show'] == '1' && !$googleRecaptchaEnabled ) {
                JCaptcha::getInstance('recaptcha', array('namespace' => 'captcha_keystring_'.$key ));
            }

                // begin create kod
            $st = "      ";
            $algoritm = mt_rand(1, 2);
            switch ($algoritm) {
                case 1:
                for ($j = 0; $j < 6; $j+=2) {
                    $st = substr_replace($st, chr(mt_rand(97, 122)), $j, 1); //abc
                    $st = substr_replace($st, chr(mt_rand(50, 57)), $j + 1, 1); //23456789
                }
                break;
                case 2:
                for ($j = 0; $j < 6; $j+=2) {
                    $st = substr_replace($st, chr(mt_rand(50, 57)), $j, 1); //23456789
                    $st = substr_replace($st, chr(mt_rand(97, 122)), $j + 1, 1); //abc
                }
                break;
            }

        //**************   begin search in $st simbol 'o, l, i, j, t, f'   ********************************
            $st_validator = "olijtf";
            for ($j = 0; $j < 6; $j++) {
                for ($i = 0; $i < strlen($st_validator); $i++) {
                    if ($st[$j] == $st_validator[$i]) {
                    $st[$j] = chr(mt_rand(117, 122)); //uvwxyz
                }
            }
        }
        //**************   end search in $st simbol 'o, l, i, j, t, f'   **********************************

        $session = JFactory::getSession();
        $session->set('captcha_keystring_'.$key, $st);

        if (isset($_REQUEST['error']) && $_REQUEST['error'] != "")
            echo "<font style='color:red'>" . $_REQUEST['error'] . "</font><br />";
        $name_user = "";
        if (isset($_REQUEST['name_user']))
            $name_user = protectInjectionWithoutQuote('name_user','','STRING');

        if (isset($_REQUEST["err_msg"]))
            echo "<script> alert('Error: " . $_REQUEST["err_msg"] . "'); </script>\n";

        echo "<br /><img src='" . JRoute::_( "index.php?option=com_vehiclemanager&amp;task=secret_image_".$key."&Itemid=$Itemid&uniqid=".uniqid())."' alt='CAPTCHA_picture'/><br/>";
        ?>
        <!--**********************   end insetr image   *******************************-->
    </span>
</div>
<div class="row_08">
    <span classs="col_01"><?php echo _VEHICLE_MANAGER_LABEL_REVIEW_KEYGUEST; ?></span>
</div>
<div class="row_09">
    <span class="col_01">
        <input class="inputbox" type="text" name="keyguest" size="6" maxlength="6" />
    </span>
</div>
<!--****************************   end add antispam guest   ******************************-->
<?php
    }
}

    // ! ATENTION ! This function add_open_map() should not work. Read the comment below!
    static function add_open_map(&$rows,$map_uniq_id) {

        // If everything ok, this function add_open_map() is not needed anymore. Instead of this function used layout site_name/components/com_vehiclemanager/views/map/tmpl/open_map.php. The "return" below is made specially.
        return;

         global $vehiclemanager_configuration, $doc, $mosConfig_live_site, $database, $Itemid;
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

            window.onload = function() {
                vm_initialize2<?php echo $map_uniq_id; ?>();
            };

            function vm_initialize2<?php echo $map_uniq_id; ?>() {

                //if map already created: refresh
                if (map instanceof ol.Map) {
                    map.updateSize();
                    return ;
                }

                var container = document.getElementById('os_ol_popup');
                var content = document.getElementById('os_ol_popup-content');
                var closer = document.getElementById('os_ol_popup-closer');

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

                var london = ol.proj.fromLonLat([-0.12755, 51.507222]);

                var map_view = new ol.View({
                    center: london,
                    zoom: 8
                });

                var map = new ol.Map({
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
                var ol_point = "";
                var pointFeatures = new Array() ;

                <?php
                $newArr = explode(",", _VEHICLE_MANAGER_LOCATION_MARKER);
                $j = 0;
                for ($i = 0;$i < count($rows);$i++) {
                    // IF #1 START
                    if ($rows[$i]->vlatitude && $rows[$i]->vlongitude) {
                        $numPick = '';
                        if (isset($newArr[$rows[$i]->vtype])) {
                            $numPick = $newArr[$rows[$i]->vtype];
                        }
                        ?>

                        var srcForPic = "<?php echo $numPick; ?>";
                        var image = '';
                        if (srcForPic.length) {
                            var image_point_src = imgCatalogPath + srcForPic;
                        } else var image_point_src = imgCatalogPath + "/images/marker-2.png";

                        <?php
                        if (strlen($rows[$i]->vtitle) > 45) {
                            $vtitle = mb_substr($rows[$i]->vtitle, 0, 25) . '...';
                        }
                        else {
                            $vtitle = $rows[$i]->vtitle;
                        }
                        ?>
                        var title =  "<?php echo $vtitle ?>";
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

                        <?php if(!incorrect_price($rows[$i]->price)) { ?>
                            var price =  "<?php echo $rows[$i]->price; ?>";
                            var priceunit =  "<?php echo $rows[$i]->priceunit; ?>";
                        <?php } else { ?>
                            var price =  "<?php echo $rows[$i]->price; ?>";
                            var priceunit="";
                        <?php } ?>

                        var contentStr = '<div>'+
                            '<a onclick=window.open("<?php echo JRoute::_("index.php?option=com_vehiclemanager&task=view&id={$rows[$i]->id}&catid={$rows[$i]->idcat}&Itemid={$Itemid}");?>") >'+
                            '<img width = "100%" src = "'+imgUrl+'">'+
                            '</a>' +
                            '</div>'+
                            '<div id="marker_link">'+
                            '<a onclick=window.open("<?php echo JRoute::_("index.php?option=com_vehiclemanager&task=view&id={$rows[$i]->id}&catid={$rows[$i]->idcat}&Itemid={$Itemid}");?>") >' + title + '</a>'+
                            '</div>'+
                            '<div id="marker_price">'+
                            '<a onclick=window.open("<?php echo JRoute::_("index.php?option=com_vehiclemanager&task=view&id={$rows[$i]->id}&catid={$rows[$i]->idcat}&Itemid={$Itemid}");?>") >' + price +' ' + priceunit + '</a>'+
                        '</div>';

                        ol_point = ol.proj.fromLonLat([<?php echo $rows[$i]->vlongitude; ?>, <?php echo $rows[$i]->vlatitude; ?>]);
                        pointFeature = new ol.Feature(new ol.geom.Point(ol_point)) ;
                        pointFeatures.push( 1 );

                        map_layer = new ol.layer.Vector({
                            source: new ol.source.Vector({
                                features: [pointFeature]
                            }),
                            style: new ol.style.Style({
                                image: new ol.style.Icon(/** @type {olx.style.IconOptions} */ ({
                                    anchor: [0.52, 31],
                                    anchorXUnits: 'fraction',
                                    anchorYUnits: 'pixels',
                                    opacity: 0.95,
                                    src: image_point_src
                                }))
                            }),
                            os_text_cnt:contentStr,
                        });

                        map.addLayer(map_layer);

                        <?php
                        $j++;
                    } // IF #1 END
                }
                        ?>

            if(ol_point != "") {
               var select= new ol.interaction.Select({layers : map_layer });
               var selectedFeature=select.getFeatures().item(0); //the selected feature

              map.on('pointermove', function(evt) {
                var layer = map.forEachFeatureAtPixel(evt.pixel, function(feature, layer) {
                    //you can add a condition on layer to restrict the listener
                    return layer;
                    });
                if (layer ) {
                    //here you can add you code to display the coordinates or whatever you want to do
                    var coordinate = evt.coordinate;
                    content.innerHTML = layer.get('os_text_cnt') ;
                    overlay.setPosition(coordinate);
                }
              });

              //chnage map zoom and bound all markers
              if( pointFeatures.length > 1 && ol_point != "" ){

                //Create an empty extent that we will gradually extend
                var extent = ol.extent.createEmpty();

                map.getLayers().forEach(function(layer) {
                    //If this is actually a group, we need to create an inner loop to go through its individual layers
                    if(layer instanceof ol.layer.Group) {
                        layer.getLayers().forEach(function(groupLayer) {
                            //If this is a vector layer, add it to our extent
                            if(layer instanceof ol.layer.Vector)
                                ol.extent.extend(extent, groupLayer.getSource().getExtent());
                        });
                    }
                    else if(layer instanceof ol.layer.Vector)
                       ol.extent.extend(extent, layer.getSource().getExtent());
                });

                //Finally fit the map's view to our combined extent
                map.getView().fit(extent, map.getSize());
              } else if( pointFeatures.length == 1 && ol_point != ""){
                map.getView().setCenter( ol_point );
                map.getView().setZoom(10);
              }

            }


            }
          </script>
      <?php
    }

    // ! ATENTION ! This function add_google_map() should not work. Read comment below!
    // The function that made Andrew:
    static function add_google_map($rows, $map_uniq_id) {

        // If everything ok, this function add_google_map() is not needed anymore. Instead of this function is used layout site_name/components/com_vehiclemanager/views/map/tmpl/google_map.php. The "return" below is made specially.
        return;

        global $vehiclemanager_configuration, $mosConfig_live_site, $database, $Itemid ;

    $api_key = "key=" . $vehiclemanager_configuration['api_key'] ;
     ?>
    <script type="text/javascript" src="//maps.googleapis.com/maps/api/js?<?php echo $api_key ?>"></script>
        <script type="text/javascript">
            window.onload =  function() {
                vm_initialize2<?php echo $map_uniq_id; ?>();
            };
            function vm_initialize2<?php echo $map_uniq_id; ?>(){
                var map;
                var marker = new Array();
                var myOptions = {
                    scrollwheel: false,
                    zoomControlOptions: {
                        style: google.maps.ZoomControlStyle.LARGE
                    },
                    mapTypeId: google.maps.MapTypeId.ROADMAP
                };
                var imgCatalogPath = "<?php echo $mosConfig_live_site; ?>/components/com_vehiclemanager/";
                var map = new google.maps.Map(document.getElementById("vm_map_canvas<?php echo $map_uniq_id; ?>"), myOptions);
                var bounds = new google.maps.LatLngBounds ();
            <?php
            $newArr = explode(",", _VEHICLE_MANAGER_LOCATION_MARKER);
            $j=0;
            for ($i=0;$i < count($rows);$i++){
            if ($rows[$i]->vlatitude && $rows[$i]->vlongitude) {
                    $numPick = '';
                    if(isset($newArr[$rows[$i]->vtype])){
                        $numPick = $newArr[$rows[$i]->vtype];
                    }
         ?>
          var srcForPic = "<?php echo $numPick; ?>";
          var image = '';
            if(srcForPic.length){
                var image = new google.maps.MarkerImage(imgCatalogPath + srcForPic,
                    new google.maps.Size(32, 39),
                    new google.maps.Point(0,0),
                    new google.maps.Point(16, 39));
            }
                marker.push(new google.maps.Marker({
                    icon: image,
                    position: new google.maps.LatLng(<?php echo $rows[$i]->vlatitude; ?>,
                     <?php echo $rows[$i]->vlongitude; ?>),
                    map: map,
                    title: "<?php echo $database->Quote($rows[$i]->vtitle); ?>"
                }));

                bounds.extend(new google.maps.LatLng(<?php echo $rows[$i]->vlatitude; ?>,<?php
                 echo $rows[$i]->vlongitude; ?>));
                var infowindow  = new google.maps.InfoWindow({});
                google.maps.event.addListener(marker[<?php echo $j; ?>], 'mouseover', function() {
                    <?php
                    if (strlen($rows[$i]->vtitle) > 45)
                        $vtitle = substr($rows[$i]->vtitle, 0, 25) . '...';
                    else {
                        $vtitle = $rows[$i]->vtitle;
                    }
                    ?>
                    var title =  "<?php echo $vtitle ?>";
                    <?php
                        //for local images
                        $imageURL = ($rows[$i]->image_link);
                        if ($imageURL == '') $imageURL = _VEHICLE_MANAGER_NO_PICTURE_BIG;
                        $file_name = vm_picture_thumbnail($imageURL,150,150, $watermark);
                        $file = $mosConfig_live_site . '/components/com_vehiclemanager/photos/' . $file_name;
                    ?>
                    var imgUrl =  "<?php echo $file; ?>";

                    <?php if(!incorrect_price($rows[$i]->price)): ?>
                        var price =  "<?php echo $rows[$i]->price; ?>";
                        var priceunit =  "<?php echo $rows[$i]->priceunit; ?>";
                    <?php else: ?>
                        var price =  "<?php echo $rows[$i]->price; ?>";
                        var priceunit =  "";
                    <?php endif; ?>

                    var foto_width = 150;
                    var foto_height = 150;
                    var contentStr = '<div>'+
                            '<div id="marker_image">'+
                                '<img  style="cursor:pointer;" width = "'+foto_width+'" height="'+foto_height+'" src = '+imgUrl+
                                    ' onclick=window.open("index.php?option=com_vehiclemanager'+
                                      '&task=view_vehicle&id=<?php echo $rows[$i]->id; ?>'+
                                      '&catid=<?php echo $rows[$i]->idcat ?>&Itemid=<?php echo $Itemid;?>")>'+
                            '</div>'+
                            '<div id="marker_link">'+
                                '<a onclick=window.open("index.php?option=com_vehiclemanager'+
                                  '&task=view_vehicle&id=<?php echo $rows[$i]->id; ?>'+
                                  '&catid=<?php echo $rows[$i]->idcat ?>&Itemid=<?php echo $Itemid;?>")>' +
                                   title + '</a>'+
                            '</div>'+
                            '<div id="marker_price">'+
                                '<a onclick=window.open("index.php?option=com_vehiclemanager'+
                                '&task=view_vehicle&id=<?php echo $rows[$i]->id; ?>'+
                                '&catid=<?php echo $rows[$i]->idcat ?>&Itemid=<?php echo $Itemid;?>") >' +
                                 price +' ' + priceunit + '</a>'+
                            '</div>'+
                        '</div>';
                    infowindow.setContent(contentStr);
                    infowindow.setOptions( { maxWidth: <?php echo $vehiclemanager_configuration['fotogal']['width'] ; ?> });
                    infowindow.open(map,marker[<?php echo $j; ?>]);
                });
                var myLatlng = new google.maps.LatLng(<?php echo $rows[$i]->vlatitude; ?>,<?php
                 echo $rows[$i]->vlongitude; ?>);
                var myZoom = <?php echo $rows[$i]->map_zoom; ?>;
                <?php
                $j++;
                }
            }
            ?>
                if (<?php echo $j; ?>>1) map.fitBounds(bounds);
                else if (<?php echo $j; ?>==1) {map.setCenter(myLatlng);map.setZoom(myZoom)}
                else {map.setCenter(new google.maps.LatLng(0,0));map.setZoom(0);}
            }
        </script>
        <?php
    }
}
//END CLASS VEHICLE MANAGER HTML
?>
