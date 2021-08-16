<?php
defined( '_JEXEC' ) or die( 'Direct Access to ' . basename(__FILE__) . ' is not allowed.' );
  /**
*
* @package  VehicleManager
  * @copyright 2020 by Ordasoft
  * @author Andrey Kvasnevskiy - OrdaSoft (akbet@mail.ru); Rob de Cleen (rob@decleen.com);
  * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
  * Homepage: https://ordasoft.com/
*
* */
global $hide_js, $Itemid, $mainframe, $mosConfig_live_site, $doc, $vehiclemanager_configuration;
    if(!empty($params['page_heading']) && $params['show_page_heading'] != 0){
        $veh_header = $params['page_heading'];
    } else {
        $veh_header = $currentcat->header;
    }
positions_vm($params->get('showsearch01')); ?>
<div class="componentheading<?php echo $params->get('pageclass_sfx'); ?> vmpage-title">
  <h3><?php echo $veh_header; ?></h3>
</div>
<?php positions_vm($params->get('showsearch02')); ?>
<div class="basictable_39 basictable vmpage-logo">
  <div class="row_01">
    <?php
    if ($currentcat->img != null && $currentcat->align == 'left') {?>
      <span class="col_01">
        <img src="<?php echo $currentcat->img; ?>" alt="img" align="<?php echo $currentcat->align; ?>" />
      </span>
      <?php
    }?>
    <span class="col_02"></span>
    <?php
    if ($currentcat->img != null && $currentcat->align == 'right') {?>
      <span class="col_03">
        <img src="<?php echo $currentcat->img; ?>" align="<?php echo $currentcat->align; ?>"  alt = "logo"/>
      </span>
      <?php
    }
    ?>
  </div>
</div>
<br />

<script type="text/javascript">
  function vm_showDate(){
      if(document.userForm1.search_date_box.checked ){
        var x=document.getElementById("search_date_from");
        document.userForm1.search_date_from.type="text";
        var x=document.getElementById("search_date_until");
        document.userForm1.search_date_until.type="text";
      }else{
        var x=document.getElementById("search_date_from");
        document.userForm1.search_date_from.type="hidden";
        var x=document.getElementById("search_date_until");
        document.userForm1.search_date_until.type="hidden";
      }
  }
</script>
<noscript>Javascript is required to use Vehicle Manager <a href="https://ordasoft.com/vehicle-manager-joomla-car-rental-dealer-software-for-rent-and-sell-cars" title="car rental dealer software">car rental dealer software</a>, <a href="https://ordasoft.com/car-dealer-website-development.html" title="car dealer website development" >car dealer website development</a></noscript>

<?php positions_vm($params->get('showsearch03'));
$path = "index.php?option=" . $option . "&amp;task=search_vehicle&amp;Itemid=" . $Itemid; ?>

<form action="<?php echo sefRelToAbs($path);?>" method="get" name="userForm1" id="userForm1" class="vmsearch-form">
  <input type="hidden" name="Vehicleid" value="on"/>
  <input type="hidden" name="Address" value="on"/>
  <input type="hidden" name="City" value="on"/>
  <input type="hidden" name="Ownername" value="on"/>
  <input type="hidden" name="Engine_type" value="on"/>
  <input type="hidden" name="Wheelbase" value="on"/>
  <input type="hidden" name="Brakes_type" value="on"/>
  <input type="hidden" name="Interior_colors" value="on"/>
  <input type="hidden" name="Safety_options" value="on"/>
  <input type="hidden" name="Description" value="on"/>
  <input type="hidden" name="Country" value="on"/>
  <input type="hidden" name="District" value="on"/>
<!--   <input type="hidden" name="Mileage" value="on"/> -->
  <input type="hidden" name="City_fuel_mpg" value="on"/>
  <input type="hidden" name="Wheeltype" value="on"/>
  <input type="hidden" name="Exterior_colors" value="on"/>
  <input type="hidden" name="Dashboard_options" value="on"/>
  <input type="hidden" name="Warranty_options" value="on"/>
  <input type="hidden" name="Title" value="on"/>
  <input type="hidden" name="Region" value="on"/>
  <input type="hidden" name="Zipcode" value="on"/>
  <input type="hidden" name="Contacts" value="on"/>
  <input type="hidden" name="Highway_fuel_mpg" value="on"/>
  <input type="hidden" name="Rear_axe_type" value="on"/>
  <input type="hidden" name="Exterior_extras" value="on"/>
  <input type="hidden" name="Interior_extras" value="on"/>

  <input type="hidden" name="option" value="<?php echo $option; ?>" />
  <input type="hidden" name="Itemid" value="<?php echo $Itemid; ?>" />
  <input type="hidden" name="task" value="search_vehicle" />
  <div class="search_filter default_search_layout">
    <div class="VEH-row">

      <div class="VEH-collumn-xs-12 VEH-collumn-sm-4 VEH-collumn-md-4 VEH-collumn-lg-4 ">
        <div class="vmsearch-group">

      <?php if($vehiclemanager_configuration['keywords_search_show_select'] == '1') { ?>

        <span class="col_01"><?php echo _VEHICLE_MANAGER_LABEL_SEARCH_KEYWORD; ?></span>
        <input class="inputbox" type="text" name="searchtext" size="20" maxlength="20"/>
        <br />

      <?php } ?>

        <input type="submit" name="submit"
               value="<?php echo _VEHICLE_MANAGER_LABEL_SEARCH_BUTTON; ?>" class="button search_f" />
      </div>

      </div><!--end VEH-collumn-xs-4 VEH-collumn-sm-4 VEH-collumn-md-4 VEH-collumn-lg-4-->

 <?php if($vehiclemanager_configuration['available_for_rent_show_select'] == '1'){ ?>

      <div class="VEH-collumn-xs-12 VEH-collumn-sm-4 VEH-collumn-md-4 VEH-collumn-lg-4 ">



         <div class="vmsearch-group">
            <span class="col_04"><?php echo _VEHICLE_MANAGER_LABEL_AVAILABLE_FOR_RENT; ?></span>
           <div class="VEH-row">
               <div class="VEH-collumn-xs-12 VEH-collumn-sm-12 VEH-collumn-md-3 VEH-collumn-lg-3 search-label_container">
                   <span class="col_05 search-label">
                       <?php echo _VEHICLE_MANAGER_LABEL_AVAILABLE_FOR_RENT_FROM; ?>
                   </span>
               </div>
               <div class="VEH-collumn-xs-12 VEH-collumn-sm-12 VEH-collumn-md-9 VEH-collumn-lg-9">
                   <input type="text" id="search_date_from" name="search_date_from">
               </div>
           </div>
           <div class="VEH-row">
             <div class="VEH-collumn-xs-12 VEH-collumn-sm-12 VEH-collumn-md-3 VEH-collumn-lg-3 search-label_container">
               <span class="col_07 search-label"><?php echo _VEHICLE_MANAGER_LABEL_AVAILABLE_FOR_RENT_UNTIL; ?></span>
             </div>
             <div class="VEH-collumn-xs-12 VEH-collumn-sm-12 VEH-collumn-md-9 VEH-collumn-lg-9">
               <input type="text" id="search_date_until" name="search_date_until">
             </div>
           </div>
         </div>


      </div><!--end VEH-collumn-xs-4 VEH-collumn-sm-4 VEH-collumn-md-4 VEH-collumn-lg-4-->

        <?php } ?>

      <script type="text/javascript">
        jQuerOs(document).ready(function() {
          jQuerOs( "#search_date_from, #search_date_until" ).datepicker({
            minDate: "+0",
            dateFormat: "<?php echo transforDateFromPhpToJquery_vm();?>"
          });
        });
        jQuerOs(function() {
          jQuerOs("#slider").slider({
            min: <?php echo $params->get('pricefrom_one'); ?>,
            max: <?php echo $params->get('priceto_one'); ?>,
            values: [<?php echo $params->get('pricefrom_one'); ?>,<?php echo $params->get('priceto_one'); ?>],
            range: true,
            stop: function(event, ui) {
              jQuerOs("input#pricefrom").val(jQuerOs("#slider").slider("values",0));
              jQuerOs("input#priceto").val(jQuerOs("#slider").slider("values",1));
            },
            slide: function(event, ui){
              jQuerOs("input#pricefrom").val(jQuerOs("#slider").slider("values",0));
              jQuerOs("input#priceto").val(jQuerOs("#slider").slider("values",1));
            }
          });

          jQuerOs("input#pricefrom").change(function(){
            var value1=jQuerOs("input#pricefrom").val();
            var value2=jQuerOs("input#priceto").val();
            if(parseInt(value1) > parseInt(value2)){
              value1 = value2;
              jQuerOs("input#pricefrom").val(value1);
            }
            jQuerOs("#slider").slider("values",0,value1);
          });

          jQuerOs("input#priceto").change(function(){
            var value1=jQuerOs("input#pricefrom").val();
            var value2=jQuerOs("input#priceto").val();
            if (value2 > <?php echo $params->get('priceto_one'); ?>) { value2 = <?php echo $params->get('priceto_one'); ?>; jQuerOs("input#priceto").val(<?php echo $params->get('priceto_one'); ?>)}
            if(parseInt(value1) > parseInt(value2)){
              value2 = value1;
              jQuerOs("input#priceto").val(value2);
            }
            jQuerOs("#slider").slider("values",1,value2);
          });
        });
      </script>

      <?php if($vehiclemanager_configuration['price_vehicle_show_select'] == '1') { ?>

      <div class="VEH-collumn-xs-12 VEH-collumn-sm-4 VEH-collumn-md-4 VEH-collumn-lg-4 ">

          <div class="vmsearch-group">
          <span class="col_01"><?php echo _VEHICLE_MANAGER_LABEL_PRICE;?></span>
            <div class="VEH-row">
              <div class="VEH-collumn-xs-12 VEH-collumn-sm-12 VEH-collumn-md-3 VEH-collumn-lg-3 search-label_container">
                <span class="col_02 search-label"><?php echo _VEHICLE_MANAGER_LABEL_PRICE_FROM;?></span>
              </div>
              <div class="VEH-collumn-xs-12 VEH-collumn-sm-12 VEH-collumn-md-9 VEH-collumn-lg-9">
                <input type="text" name="pricefrom" id="pricefrom" value="<?php echo $params->get('pricefrom_one'); ?>"/>
              </div>
            </div>
            <div class="VEH-row">
              <div class="VEH-collumn-xs-12 VEH-collumn-sm-12 VEH-collumn-md-3 VEH-collumn-lg-3 search-label_container">
                <span class="col_03 search-label"><?php echo _VEHICLE_MANAGER_LABEL_PRICE_TO;?></span>
              </div>
              <div class="VEH-collumn-xs-12 VEH-collumn-sm-12 VEH-collumn-md-9 VEH-collumn-lg-9">
                <input type="text" name="priceto" id="priceto" value="<?php echo $params->get('priceto_one'); ?>"/>
              </div>
            </div><!--end inp_label_from-->
            <div class="slider_price">
              <div id="slider" class="ui-slider ui-slider-horizontal ui-widget ui-widget-content ui-corner-all"></div>
            </div><!--end slider_price-->
            </div>

      </div><!--end VEH-collumn-xs-4 VEH-collumn-sm-4 VEH-collumn-md-4 VEH-collumn-lg-4-->

    <?php } ?>


<?php if($vehiclemanager_configuration['category_show_select'] == '1') { ?>
      <div class="VEH-collumn-xs-12 VEH-collumn-sm-4 VEH-collumn-md-4 VEH-collumn-lg-4 ">



        <div class="vmsearch-group">
          <div class="row">
              <div class="VEH-collumn-xs-12 VEH-collumn-sm-12 VEH-collumn-md-4 VEH-collumn-lg-4 search-label_container">
                  <span class="search-label"><?php echo _VEHICLE_MANAGER_LABEL_CATEGORY; ?></span>
              </div>
              <div class="VEH-collumn-xs-12 VEH-collumn-sm-12 VEH-collumn-md-8 VEH-collumn-lg-8">
      <?php echo $clist; ?>
              </div>
          </div>
        </div>



      </div><!--end fix_width_4-->

       <?php } ?>

      <?php if($vehiclemanager_configuration['listing_status_show_select'] == '1') { ?>

      <div class="VEH-collumn-xs-12 VEH-collumn-sm-4 VEH-collumn-md-4 VEH-collumn-lg-4 ">
        <div class="vmsearch-group">
          <div class="row">
            <div class="VEH-collumn-xs-12 VEH-collumn-sm-12 VEH-collumn-md-4 VEH-collumn-lg-4 search-label_container">
             <span class="search-label"><?php echo _VEHICLE_MANAGER_LABEL_LISTING_STATUS; ?></span>
            </div>

            <div class="VEH-collumn-xs-12 VEH-collumn-sm-12 VEH-collumn-md-8 VEH-collumn-lg-8">
             <?php echo $params->get('listing_status_list'); ?>
            </div>
          </div>
        </div>
      </div>

      <?php } ?>

      <?php if($vehiclemanager_configuration['listing_type_show_select'] == '1') { ?>

      <div class="VEH-collumn-xs-12 VEH-collumn-sm-4 VEH-collumn-md-4 VEH-collumn-lg-4 ">
          <div class="vmsearch-group">
                <div class="row">
                <div class="VEH-collumn-xs-12 VEH-collumn-sm-12 VEH-collumn-md-4 VEH-collumn-lg-4 search-label_container">
                  <span class="search-label"><?php echo _VEHICLE_MANAGER_LABEL_LISTING_TYPE; ?></span>

                </div>

                <div class="VEH-collumn-xs-12 VEH-collumn-sm-12 VEH-collumn-md-8 VEH-collumn-lg-8">
                  <?php echo $params->get('listing_type_list'); ?>
                </div>
            </div>
          </div>
      </div>

      <?php } ?>

    </div><!--end row_02-->



  </div> <!--  search_filter  -->
  <br />
  <div class="basictable_59">
    <?php
    mosHTML::BackButton($params, $hide_js); ?>
  </div>
</form>

<?php positions_vm($params->get('showsearch04'));
