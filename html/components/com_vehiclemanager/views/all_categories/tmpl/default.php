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


    global $hide_js, $Itemid, $acl, $mosConfig_live_site, $my,$mainframe;

    if(!empty($params['page_heading']) && $params['show_page_heading'] != 0){
        $veh_header = $params['page_heading'];
    } else {
        $veh_header = $currentcat->header;
    }
    ?>
<!-- main stylesheet ends, CC with new stylesheet below... -->

<!--[if IE]>
<style type="text/css">
  .basictable {
    zoom: 1;     /* triggers hasLayout */
    }  /* Only IE can see inside the conditional comment
    and read this CSS rule. Don't ever use a normal HTML
    comment inside the CC or it will close prematurely. */
</style>
<![endif]-->

<?php positions_vm($params->get('allcategories01'));?>
    <div class="componentheading<?php echo $params->get( 'pageclass_sfx' ); ?>">
      <?php echo $veh_header; ?>
    </div>
<?php positions_vm($params->get('allcategories02'));?>
    <div class="category_title basictable">
        <div class="row_01">
            <div class="VEH-row">

                <div class="VEH-collumn-xs-12 VEH-collumn-sm-12 VEH-collumn-md-12 VEH-collumn-lg-12">
                    <img src="<?php echo $mosConfig_live_site; ?>/components/com_vehiclemanager/images/vm_logo.png" alt="Vehicles"/>
                </div>

            </div>
            <div class="VEH-row">

                <div
                <?php if ($params->get('show_search') && $params->get('show_input_button_search')): ?>
                    class="VEH-collumn-lg-9 VEH-collumn-md-8 VEH-collumn-sm-7 VEH-collumn-xs-12"
                <?php  else: ?>
                    class="VEH-collumn-lg-12 VEH-collumn-md-12 VEH-collumn-sm-12 VEH-collumn-xs-12"
                <?php endif; ?>
                >

                    <div class="vm_cat_title">
                        <?php echo $currentcat->descrip; ?>
                    </div>

                </div>

                <?php //if ($params->get('show_search')){
                if($params->get('show_input_button_search')){?>
                    <div class="basictable_44 basictable VEH-collumn-lg-3 VEH-collumn-md-4 VEH-collumn-sm-5 VEH-collumn-xs-12">
                    <?php
                    $link = 'index.php?option=com_vehiclemanager&amp;task=show_search_vehicle&amp;Itemid='. $Itemid;
                    //$link = 'index.php?option=com_vehiclemanager&amp;task=show_search_vehicle&amp;catid='. $catid. '&amp;Itemid='. $Itemid;
                    ?>
                        <div class="search_button_vehicle">
                            <a href="<?php echo JRoute::_($link, false); ?>" class="category<?php echo $params->get( 'pageclass_sfx' ); ?>">
                            <i class="fa fa-search"></i>
                            <?php echo _VEHICLE_MANAGER_LABEL_SEARCH; ?>
                            </a>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
<?php positions_vm($params->get('allcategories03'));?>
    <br/>
    <?php
      HTML_vehiclemanager::listCategories($params, $categories, $catid, $tabclass , $currentcat);
    ?>
  <div class="basictable_59">
    <?php
    mosHTML::BackButton($params, $hide_js); ?>
  </div>

    <br/>

<div class="vm_all_cat_button_container">
        <?php
        positions_vm($params->get('allcategories07'));
        positions_vm($params->get('allcategories08'));
        positions_vm($params->get('allcategories09'));
        positions_vm($params->get('allcategories10'));
        ?>
</div>
<p style="text-align: center; font-size: 12px;"><a title="Car rental dealer software" href="https://ordasoft.com/vehicle-manager-joomla-car-rental-dealer-software-for-rent-and-sell-cars" target="_blank">Car rental dealer software</a> by OrdaSoft.</p>