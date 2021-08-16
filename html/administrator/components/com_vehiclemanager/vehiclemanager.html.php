<?php
if (!defined('_VALID_MOS') && !defined('_JEXEC')) die('Direct Access to ' . basename(__FILE__) . ' is not allowed.');


use Joomla\String\Inflector;

// Include the component HTML helpers.
JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');

JHtml::_('bootstrap.tooltip');
// JHtml::_('behavior.multiselect');
// JHtml::_('formbehavior.chosen', 'select');

/**
*
* @package  VehicleManager
* @copyright 2020 by Ordasoft
* @author Andrey Kvasnevskiy - OrdaSoft (akbet@mail.ru); Rob de Cleen (rob@decleen.com);
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* Homepage: https://ordasoft.com/
*
* */

// require_once ($mosConfig_absolute_path . "/libraries/joomla/factory.php");
require_once ( JPATH_BASE . '/includes/defines.php' );
require_once ( JPATH_BASE . '/includes/framework.php' );

$mainframe = JFactory::getApplication();
$templateDir = 'templates/' . $mainframe->getTemplate();
$GLOBALS['mainframe'] = $mainframe;
$GLOBALS['templateDir'] = $templateDir;
$mosConfig_live_site = JURI::root(true);
$GLOBALS['mosConfig_live_site'] = $mosConfig_live_site;
$doc = JFactory::getDocument();
$GLOBALS['doc'] = $doc;
// ensure this file is being included by a parent file
$vid = mosGetParam($_POST, 'vid', array(0));
require_once ($mosConfig_absolute_path .
"/administrator/components/com_vehiclemanager/admin.vehiclemanager.class.others.php");
$doc->addStyleSheet($css);
$doc->addStyleSheet('//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css');

class HTML_Categories{
  static function show(&$rows, $myid, &$pageNav, &$lists,$language, $type){
    global $my, $mainframe, $mosConfig_live_site;
    $section = "com_vehiclemanager";
    $section_name = "VehicleManager";
    // for 1.6
    global $doc, $css;
    $doc->addStyleSheet($css);
    $doc->addScript($mosConfig_live_site . '/components/com_vehiclemanager/includes/functions.js');
    $html = "<div class='vehicle_manager_caption' >" .
     "<i class='fa fa-car'></i> "
      . _VEHICLE_MANAGER_CATEGORIES_MANAGER . "</div>";
    $app = JFactory::getApplication();
    $app->JComponentTitle = $html;

    $saveOrderingUrl = 'index.php?option=com_vehiclemanager&task=saveCategoryOrder';
        JHtml::_('sortablelist.sortable', 'categoryList', 'adminForm', 'asc', $saveOrderingUrl, false, true);

    ?>
    <form action="index.php" method="post" name="adminForm" id="adminForm">
      <?php
      if (version_compare(JVERSION, "3.0.0", "ge")){ ?>
        <table width="100%" class="adminform adminform_02">
          <tr>
              <div style="float:right;" class="btn-group pull-left hidden-phone">
                <label for="limit" class="element-invisible">
                  <?php
                  echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC'); ?>
                </label>
                <label for="limit" class="element-invisible">
                  <?php
                  echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC'); ?>
                </label>
                <?php
                 echo $pageNav->getLimitBox(); ?>
              </div>
              <div style="float:right;" class="btn-group pull-left hidden-phone">
                <?php echo $language; ?></td>
              </div>
            </td>
          </tr>
        </table>
      <?php
      } ?>


      <table class="adminlist adminlist_01 table table-striped" id="categoryList">
      <thead>
        <tr>
          <th class="icon-menu-2"></th>
          <th width="20" align="center">
              #
          </th>
          <th width="20"><input type="checkbox" name="toggle"
           onClick="vm_checkAll(this<?php //echo count($rows);  ?>);" /></th>
          <th align = "center" class="title"><?php echo _VEHICLE_HEADER_CATEGORY; ?></th>
          <th align = "center" width="5%"><?php echo _VEHICLE_HEADER_NUMBER; ?></th>
          <th align = "center" width="10%"><?php echo _VEHICLE_HEADER_PUBLISHED; ?></th>
          <?php
          if ($section <> 'content'){
            ?>
            <!-- <th align = "center" colspan="2"><?php echo _VEHICLE_HEADER_REORDER; ?></th> -->
            <?php
          } ?>
          <th align = "center" width="10%"><?php echo _VEHICLE_HEADER_ACCESS; ?></th>
          <?php
          if ($section == 'content'){
            ?>
            <th width="12%" align="left">Section</th>
            <?php
          }?>
          <th align = "center" width="12%">ID</th>
          <th align = "center" width="12%"><?php echo _VEHICLE_HEADER_CHECKED_OUT; ?></th>
          <th align = "center"  width="5%" ><?php echo _VEHICLE_MANAGER_LABEL_LANGUAGE; ?></th>
        </tr>
      </thead>
      <tbody>
        <?php
        $k = 0;
        $i = 0;
        $n = count($rows);

        foreach ($rows as $row) {
          $img = $row->published ? 'tick.png' : 'publish_x.png';
          $task = $row->published ? 'unpublish' : 'publish';
          $alt = $row->published ? 'Published' : 'Unpublished';
          if (!$row->access){
            $color_access = 'style="color: green;"';
            $task_access = 'accessregistered';
          } else if ($row->access == 1){
            $color_access = 'style="color: red;"';
            $task_access = 'accessspecial';
          } else{
            $color_access = 'style="color: black;"';
            $task_access = 'accesspublic';
          }?>

          <?php
          // print_r("<pre>");
          // print_r($rows);
          // exit;

          $sortable_group_id = ($row->parent_id != 0) ? $row->parent_id : '1';;
          $item_id = $row->id;
          //adduction parents string to appropriate look
          $parents = ($row->parents_list != '') ? $row->parents_list : '';
          $parents = trim(str_ireplace('0', 'top', $parents));
          $parents .=" ".$sortable_group_id;
          $parents = array_reverse(explode(' ',$parents));
          $parents = implode(' ', $parents);
          $parents = " ".$parents;
          if($parents == ' 1 ') $parents = '';

          $level = ($row->level != 0) ? $row->level : '';


          ?>

          <tr class="row<?php echo $i % 2; ?>"
                    sortable-group-id="<?php echo $sortable_group_id;?>"
                    item-id="<?php echo $item_id;?>"
                    parents="<?php echo $parents;?>"
                    level="<?php echo $level;?>"
                    >

            <td class="order nowrap center hidden-phone" align = "center"  >

                <span class="sortable-handler" style="cursor: move;">
                    <span class="icon-menu"></span>
                </span>

                <input type="text" style="display:none" name="order[]" size="5"  class="width-20 text-area-order"
                value="<?php echo $row->ordering;?>" />

            </td>

            <td width="20" align="center"><?php echo $pageNav->getRowOffset/* rowNumber */($i); ?></td>
            <td width="20"><?php echo mosHTML::idBox($i, $row->id, ($row->checked_out_contact_category && $row->checked_out_contact_category != $my->id), 'vid'); ?></td>
            <td width="35%">
              <?php
              if ($row->checked_out_contact_category && ($row->checked_out_contact_category != $my->id)){
                ?>
                <?php echo $row->treename . ' ( ' . $row->title . ' )'; ?>
                &nbsp;[ <i>Checked Out</i> ]
                <?php
              } else{?>
                <a href="#edit" onClick="return listItemTask('cb<?php echo $i; ?>','edit')">
                  <?php
                  echo $row->treename . ' ( ' . $row->title . ' )'; ?>
                </a>
                <?php
              }
              ?>
            </td>
            <td align="center">
              <?php
              echo $row->nvehicle;
              global $templateDir;
              ?>
            </td>
            <td align="center">
              <a href="javascript: void(0);"
               onClick="return listItemTask('cb<?php echo $i; ?>','<?php echo $task; ?>')">
                <?php
                if (version_compare(JVERSION, "1.6.0", "lt")){
                  ?>
                  <img src="<?php echo $mosConfig_live_site .
                   "/administrator/images/" . $img; ?>" width="12" height="12"
                    border="0" alt="<?php echo $alt; ?>" />
                  <?php
                } else{
                  ?>
                  <img src="<?php echo $templateDir . "/images/admin/" .
                   $img; ?>" width="12" height="12" border="0" alt="<?php echo $alt; ?>" />
                  <?php
                }
                ?>
              </a>
            </td>
           <!--  <td align="center"><?php echo catOrderUpIcon($row->ordering - 1, $i); ?></td>
            <td align="center"><?php echo catOrderDownIcon($row->ordering - 1, $row->all_fields_in_list, $i); ?></td> -->
            <td align="center">
              <?php echo $row->groups; ?>
            </td>
            <td align="center"><?php echo $row->id; ?></td>
            <td align="center"><?php echo $row->checked_out_contact_category ? $row->editor : ""; ?></td>
            <td align="center">
              <?php echo $row->language; ?>
            </td>
            <?php
            $k = 1 - $k;
            ?>
          </tr>
          <?php
          $k = 1 - $k;
          $i++;
          }
          ?>
          </tbody>
        <tr>
          <td colspan = "10"><?php echo $pageNav->getListFooter(); ?>
          </td>
        </tr>
      </table>
      <input type="hidden" name="option" value="com_vehiclemanager" />
      <input type="hidden" name="section" value="categories" />
      <input type="hidden" name="task" value="" />
      <input type="hidden" name="chosen" value="" />
      <input type="hidden" name="act" value="" />
      <input type="hidden" name="boxchecked" value="0" />
      <input type="hidden" name="type" value="<?php echo $type; ?>" />
    </form>
    <?php
  }

  /**
   * Writes the edit form for new and existing categories
   *
   * @param mosCategory $ The category object
   * @param string $
   * @param array $
   */
  static function edit(&$row, $section, &$lists, $redirect, $associate_cat_arr)
  {
      global $my, $mosConfig_live_site, $mainframe, $option, $database;
      // for 1.6
      global $doc, $css;
      $doc->addStyleSheet($css);
      $doc->addScript($mosConfig_live_site . '/components/com_vehiclemanager/includes/functions.js');

      $html = "<div class='vehicle_manager_caption' >" .
       "<i class='fa fa-car'></i> " .
        _VEHICLE_MANAGER_CATEGORIES_MANAGER . "</div>";
      $app = JFactory::getApplication();
      $app->JComponentTitle = $html;

      if ($row->image == "")
      {
          $row->image = 'blank.png';
      }

      mosMakeHtmlSafe($row, ENT_QUOTES, 'description');
      ?>
      <script language="javascript" type="text/javascript">

          Joomla.submitbutton = function(pressbutton, section) {

          var form = document.adminForm;

              if (pressbutton == 'cancel') {
                  Joomla.submitform( pressbutton );
                  return;
              }

              if ( form.name.value == "" ) {
                  alert('<?php echo _VEHICLE_DML_CAT_MUST_SELECT_NAME; ?>');
              } else if ( form.title.value == "" ) {
                  alert('<?php echo _VEHICLE_DML_CAT_MUST_SELECT_NAME; ?>');
              } else {
                      Joomla.submitform(pressbutton);
                  }
              }

      </script>
      <form action="index.php" method="post" name="adminForm"  id="adminForm" >
          <table>
              <tr>
                  <th  class="vehicle_manager_caption"
                   align="left"><?php echo $row->id ? _VEHICLE_HEADER_EDIT : _VEHICLE_HEADER_ADD; ?> <?php
                    echo _VEHICLE_HEADER_CATEGORY; ?> <?php echo $row->name; ?></th>
              </tr>
          </table>
          <table width="100%"  class="table_01">
              <tr>
                  <td valign="top">
                      <table class="adminform adminform_03">
                          <tr>
                              <td width="185"><?php echo _VEHICLE_CATEGORIES__DETAILS; ?></td>
                          </tr>
                          <tr>
                              <td width="185"><?php echo _VEHICLE_CATEGORIES_HEADER_TITLE; ?>:</td>
                              <td><input class="text_area" type="text" name="title"
                               value="<?php echo $row->title; ?>" size="50" maxlength="250"
                                title="A short name to appear in menus" /></td>
                          </tr>
                          <tr>
                              <td width="185"><?php echo _VEHICLE_CATEGORIES_HEADER_NAME; ?>:</td>
                              <td><input class="text_area" type="text" name="name"
                               value="<?php echo $row->name; ?>" size="50" maxlength="255"
                                title="A short name to appear in menus" /></td>
                          </tr>
<?php
/*********************************************************************************************/

  if(!empty($associate_cat_arr) && !empty($row->language) && $row->language != '' && $row->language != '*'){
?>
              <tr>
                  <td width="15%"><?php echo 'language associate category' ?>:</td>
              </tr>

<?php
      $j =1;
      foreach ($associate_cat_arr as $lang=>$value) {
          $displ = '';
          if(!$value['list']){
              $displ = 'none';
          }
?>
              <tr style="display: <?php echo $displ?>">
                  <td width="15%"><?php echo $lang; ?>:</td>
                  <td width="60%"><?php echo $value['list']; ?>
                  <input class="inputbox" id="associate_category" type="text"
                   name="associate_category<?php echo $j;?>" size="20" readonly="readonly"
                    maxlength="20" style="width:25px;" value="<?php echo $value['assocId']; ?>" />
                  <input style="display: none" name="associate_category_lang<?php echo $j;?>" value="<?php echo $lang ?>"/>
                  </td>
              </tr>
<?php

      $j++;
      }
 }else{
?>
              <tr>
                  <td width="15%"><?php echo 'language associate category' ?>:</td>
                  <td width="60%"><?php echo 'this property only for category with language' ?>
              </tr>
<?php
 }

/*********************************************************************************************/
?>
<script>
  window.onload = function(){

      var languageParentId = document.querySelectorAll('#language_associate_category');

      for(var i = 0; i < languageParentId.length; i++){

          var el = languageParentId[i];
          var idField = languageParentId[i].nextSibling.nextSibling;
          el.value = idField.value;

          var field = (function(x){
              el.onchange= function(){
                  var el = languageParentId[x];
                  var idField = languageParentId[x].nextSibling.nextSibling;
                  idField.value = el.value;
              };
          })(i);
      }
  };
</script>
<tr>
  <td width="185"><?php echo _VEHICLE_MANAGER_LABEL_LANGUAGE; ?>:</td>
  <td><?php echo $lists['languages']; ?></td>
</tr>
<tr>
  <td width="185"><?php echo _VEHICLE_CATEGORIES__PARENTITEM; ?>:</td>
  <td><?php echo $lists['parent']; ?></td>
</tr>
<tr><?php $issetImage = substr_count($lists['image'],'<option');?>
<td width="185"><?php echo _VEHICLE_CATEGORIES_HEADER_IMAGE; ?>:</td>
  <td><?php
   if ($issetImage>=1){
    echo $lists['image'].'<br><span '.
    'style="font-size: 12px; position: absolute;">' .
    'To load images need to go Content->Media Manager.<br>' .
    ' There create a folder stories and load your pictures into it<span>';
  }
  else  echo $lists['image']; ?></td>
  <?php  echo $issetImage>=1 ?  "</tr><tr><td>" : '<td rowspan="4" width="50%">';?>
  <script language="javascript" type="text/javascript">
    if (document.forms[0].image.options.value!=''){
      jsimg='../images/stories/' + getSelectedValue( 'adminForm', 'image' );
    }
    else
    {
      jsimg='../images/M_images/blank.png';
    }
    document.write('<img src=' + jsimg +
      ' name="imagelib" width="80" height="80" border="2" ' +
      'alt="<?php echo _VEHICLE_CATEGORIES__IMAGEPREVIEW; ?>" />');
    </script>
  </td>
</tr>
<tr>
  <td width="185"><?php echo _VEHICLE_CATEGORIES_HEADER_IMAGEPOS; ?>:</td>
  <td><?php echo $lists['image_position']; ?></td>
</tr>
<tr>
  <td width="185"><?php echo _VEHICLE_CATEGORIES_HEADER_ORDER; ?>:</td>
  <td><?php echo $lists['ordering']; ?></td>
</tr>
<tr>
  <td width="185"><?php echo _VEHICLE_HEADER_PUBLISHED; ?>:</td>
  <td><?php echo $lists['published']; ?></td>
</tr>
</table>
</td>
</tr>
</table>
          <input type="hidden" name="option" value="com_vehiclemanager" />
          <input type="hidden" name="section" value="categories" />
          <input type="hidden" name="task" value="" />
          <input type="hidden" name="id" value="<?php echo $row->id; ?>" />
          <input type="hidden" name="sectionid" value="com_vehiclemanager" />
          <input type="hidden" name="redirect" value="<?php echo $redirect; ?>" />
      </form>
      <?php
  }

}

/**
* Vehiclemanager Import Export Class
* Handles the import and export of data from the VehicleManager.
*/
class HTML_vehiclemanager
{
  static function edit_review($option, $vehicle_id, &$review)
  {
      global $my, $mosConfig_live_site, $mainframe;
      // for 1.6
      global $doc, $css;
      $doc->addStyleSheet($css);
      $doc->addScript($mosConfig_live_site . '/components/com_vehiclemanager/includes/functions.js');

      $html = "<div class='vehicle_manager_caption' >" .
       "<i class='fa fa-car'></i> "
        . _VEHICLE_MANAGER_SHOW_REVIEW_MANAGER . "</div>";
      $app = JFactory::getApplication();
      $app->JComponentTitle = $html;
      // --
      ?>
      <form action="index.php" method="post" name="adminForm"  id="adminForm" enctype="multipart/form-data">

          <table cellpadding="4" cellspacing="5" border="0" width="100%" class="adminform adminform_04">
              <tr>
                  <td colspan="2"><?php echo _VEHICLE_MANAGER_LABEL_REVIEW_TITLE; ?></td>
              </tr>
              <tr>
                  <td colspan="2"><input class="inputbox" type="text" name="title" size="80" value="<?php echo $review[0]->title ?>" /></td>
              </tr>
              <tr>
                  <td><?php echo _VEHICLE_MANAGER_LABEL_REVIEW_COMMENT; ?></td>
                  <td align="left" ><?php echo _VEHICLE_MANAGER_LABEL_REVIEW_RATING; ?></td>
              </tr>
              <tr>
                  <td>
                      <?php
                      editorArea('editor1', $review[0]->comment, 'comment', '410', '200', '60', '10');
                      ?>
                  </td>
                  <td width="102" align='left'>
                      <?php
                      $k = 0;
                      while ($k < 11) {
                          ?>
                          <input type="radio" name="rating" value="<?php echo $k; ?>"
          <?php if ($k == $review[0]->rating) echo 'checked="checked"'; ?> alt="Rating" />

                          <img src="../components/com_vehiclemanager/images/rating-<?php echo $k; ?>.png"
                               alt="<?php echo ($k) / 2; ?>" border="0" /><br />
                               <?php
                               $k++;
                           }
                           ?>
                  </td>
              </tr>
          </table>
          <input type="hidden" name="option" value="<?php echo $option; ?>" />
          <input type="hidden" name="task" value="update_review" />
          <input type="hidden" name="vehicle_id" value="<?php echo $vehicle_id; ?>" />
          <input type="hidden" name="review_id" value="<?php echo $review[0]->id; ?>" />
      </form>
      <?php
  }

//*************   begin for manage reviews   ********************
  static function edit_manage_review($option, & $review)
  {
      global $my, $mosConfig_live_site, $mainframe;
      // for 1.6
      global $doc, $css;
      $doc->addStyleSheet($css);
      $doc->addScript($mosConfig_live_site . '/components/com_vehiclemanager/includes/functions.js');

      $html = "<div class='vehicle_manager_caption' >" .
       "<i class='fa fa-car'></i> "
        . _VEHICLE_MANAGER_SHOW_REVIEW_MANAGER . "</div>";
      $app = JFactory::getApplication();
      $app->JComponentTitle = $html;
      // --
      ?>
      <form action="index.php" method="post" name="adminForm"  id="adminForm" enctype="multipart/form-data">
          <table cellpadding="4" cellspacing="5" border="0" width="100%" class="adminform adminform_05">
              <tr>
                  <td colspan="2" align="left"><?php echo _VEHICLE_MANAGER_LABEL_REVIEW_TITLE; ?></td>
              </tr>
              <tr>
                  <td colspan="2" align="left"> <input class="inputbox" type="text" name="title" size="80" value="<?php echo $review[0]->title ?>" /></td>
              </tr>
              <tr>
                  <td><?php echo _VEHICLE_MANAGER_LABEL_REVIEW_COMMENT; ?></td>
                  <td align="left" ><?php echo _VEHICLE_MANAGER_LABEL_REVIEW_RATING; ?></td>
              </tr>
              <tr>
                  <td align="left">
                  <!--<textarea align= "top" name="comment" id="comment" cols="60" rows="10" style="width:400;height:100;"/></textarea>-->

                      <?php
                      editorArea('editor1', $review[0]->comment, 'comment', '410', '200', '60', '10');
                      ?>
                  </td>
                  <td width="40%" align='left'>
                      <?php
                      $k = 0;
                      while ($k < 11) {
                          ?>
                          <input type="radio" name="rating" value="<?php echo $k; ?>"
          <?php if ($k == $review[0]->rating) echo 'checked="checked"'; ?> alt="Rating" />
                          <img src="../components/com_vehiclemanager/images/rating-<?php echo $k; ?>.png"
                               alt="<?php echo ($k) / 2; ?>" border="0" /><br />
                               <?php
                               $k++;
                           }
                           ?>
                  </td>
              </tr>
          </table>
          <input type="hidden" name="option" value="<?php echo $option; ?>" />
          <input type="hidden" name="task" value="update_edit_manage_review" />
          <input type="hidden" name="review_id" value="<?php echo $review[0]->id; ?>" />
      </form>
      <?php
  }

//***************   end for manage reviews   ********************/

  static function showRequestRentVehicles($option, & $rent_requests, & $pageNav)
  {
      global $my, $mosConfig_live_site, $mainframe;
      // for 1.6
      global $doc, $css;
      $doc->addStyleSheet($css);
      $doc->addScript($mosConfig_live_site . '/media/system/js/core.js');
      $doc->addScript($mosConfig_live_site . '/components/com_vehiclemanager/includes/functions.js');
      $html = "<div class='vehicle_manager_caption' ><i class='fa fa-car'></i> " . _VEHICLE_MANAGER_ADMIN_REQUEST_RENT . "</div>";
      $app = JFactory::getApplication();
      $app->JComponentTitle = $html;
      ?>
      <form action="index.php" method="post" name="adminForm"  id="adminForm" >
          <?php if (version_compare(JVERSION, "3.0.0", "ge"))
          {
              ?>
              <table width="100%"  class="table_02">
                  <tr>
                      <td>
                          <div class="btn-group pull-right hidden-phone">
                              <label for="limit" class="element-invisible"><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC'); ?></label>
          <?php echo $pageNav->getLimitBox(); ?>
                          </div>
                      </td>
                  </tr>
              </table>
      <?php } ?>
          <table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminlist adminlist_02">
              <tr>
                  <th align = "center" width="20">
                      <input type="checkbox" name="toggle" onClick="vm_checkAll(this<?php //echo count( $rent_requests );   ?>);" />
                  </th>
                  <th align = "center" width="30">#</th>
                  <th align = "center" class="title" width="10%" nowrap="nowrap"><?php echo _VEHICLE_MANAGER_LABEL_RENT_FROM; ?></th>
                  <th align = "center" class="title" width="10%" nowrap="nowrap"><?php echo _VEHICLE_MANAGER_LABEL_RENT_UNTIL; ?></th>
                  <th align = "center" class="title" width="5%" nowrap="nowrap"><?php echo _VEHICLE_MANAGER_LABEL_VEHICLEID; ?></th>
                  <th align = "center" class="title" width="15%" nowrap="nowrap"><?php echo _VEHICLE_MANAGER_LABEL_TITLE; ?></th>
                  <th align = "center" class="title" width="15%" nowrap="nowrap"><?php echo _VEHICLE_MANAGER_LABEL_RENT_USER; ?></th>
                  <th align = "center" class="title" width="15%" nowrap="nowrap"><?php echo _VEHICLE_MANAGER_LABEL_RENT_EMAIL; ?></th>

                  <th align = "center" class="title" width="10%" nowrap="nowrap"><?php echo _VEHICLE_MANAGER_LABEL_RENT_PHONE; ?></th>

                  <th align = "center" class="title" width="20%" nowrap="nowrap"><?php echo _VEHICLE_MANAGER_LABEL_RENT_ADRES; ?></th>
              </tr>
              <?php
//                 $datarent = JFactory::getDBO();
//                 $query = "SELECT * FROM #__rem_rent_request  WHERE fk_houseid=4";
//                 $datarent->setQuery($query);
//                 $rentrequest = $datarent->loadObjectList();


              for ($i = 0, $n = count($rent_requests); $i < $n; $i++) {
                  $row = $rent_requests[$i];

                  ?>
                  <tr class="row<?php echo $i % 2; ?>">
                      <td width="20" align="center">
                          <?php
                          echo mosHTML::idBox($i, $row->id, false, 'vid');
                          ?>
                      </td>
                      <td align = "center"><?php echo $row->id; ?></td>
                      <td align = "center"><?php echo $row->rent_from; ?></td>
                      <td align = "center"><?php echo $row->rent_until; ?></td>
                      <td align = "center"><?php
                          $data = JFactory::getDBO();
                          $query = "SELECT vehicleid FROM #__vehiclemanager_vehicles where id = " . $row->fk_vehicleid . " ";
                          $data->setQuery($query);
                          $vehicleid = $data->loadObjectList();

                          echo $vehicleid[0]->vehicleid;
                          ?></td>
                      <td align = "center"><?php echo $row->vtitle . (isset($row->title_assoc)?(" ( " . $row->title_assoc ." ) "):("") ); ?></td>
                      <td align = "center"><?php echo $row->user_name; ?></td>
                      <td align = "center"><a href=mailto:"<?php echo $row->user_email; ?>"><?php echo $row->user_email; ?></a></td>

                      <td align = "center"><?php echo $row->user_phone; ?></td>

                      <td align = "center"><?php echo $row->user_mailing; ?></td>
                  </tr>
                  <?php
              }
              ?>
              <tr><td colspan = "11"><?php echo $pageNav->getListFooter(); ?></td></tr>
          </table>
          <input type="hidden" name="option" value="<?php echo $option; ?>" />
          <input type="hidden" name="task" value="rent_requests" />
          <input type="hidden" name="boxchecked" value="0" />
      </form>

      <?php
  }

  static function showRequestBuyingVehicles($option, $buy_requests, $pageNav)
  {
      global $my, $mosConfig_live_site, $mainframe;
      // for 1.6
      global $doc, $css;
      $doc->addStyleSheet($css);
      $doc->addScript($mosConfig_live_site . '/media/system/js/core.js');
      $doc->addScript($mosConfig_live_site . '/components/com_vehiclemanager/includes/functions.js');

      $html = "<div class='vehicle_manager_caption' ><i class='fa fa-car'></i> " . _VEHICLE_MANAGER_ADMIN_SALE_MANAGER . "</div>";
      $app = JFactory::getApplication();
      $app->JComponentTitle = $html;
      ?>
      <form action="index.php" method="post" name="adminForm"  id="adminForm" >
      <?php if (version_compare(JVERSION, "3.0.0", "ge"))
      {
          ?>
              <table width="100%"  class="table_03">
                  <tr>
                      <td>
                          <div class="btn-group pull-right hidden-phone">
                              <label for="limit" class="element-invisible"><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC'); ?></label>
          <?php echo $pageNav->getLimitBox(); ?>
                          </div>
                      </td>
                  </tr>
              </table>
      <?php } ?>
          <table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminlist adminlist_03">
              <tr>
                  <th align = "center" >
                      <input type="checkbox" name="toggle" onClick="vm_checkAll(this<?php //echo count( $buy_requests );   ?>);" />
                  </th>
                  <th align = "center" >#</th>
                  <th align = "center" class="title"  nowrap="nowrap"><?php echo _VEHICLE_MANAGER_LABEL_VEHICLEID; ?></th>
                  <th align = "center" class="title"  nowrap="nowrap"><?php echo _VEHICLE_MANAGER_LABEL_TITLE; ?></th>
                  <th align = "center" class="title"  nowrap="nowrap"><?php echo _VEHICLE_MANAGER_LABEL_RENT_USER; ?></th>
                  <th align = "center" class="title"  nowrap="nowrap"><?php echo _VEHICLE_MANAGER_LABEL_COMMENT; ?></th>
                  <th align = "center" class="title"  nowrap="nowrap"><?php echo _VEHICLE_MANAGER_LABEL_RENT_EMAIL; ?></th>
                  <th align = "center" class="title"  nowrap="nowrap"><?php echo _VEHICLE_MANAGER_LABEL_BUYING_ADRES; ?></th>

              </tr>
              <?php
              for ($i = 0, $n = count($buy_requests); $i < $n; $i++) {
                  $row = $buy_requests[$i];
                  ?>

                  <tr class="row<?php echo $i % 2; ?>">
                      <td width="20">
                          <?php if ($row->fk_rentid != 0)
                          {
                              ?>
                              &nbsp;
                                  <?php
                              } else
                              {
                                  ?>
                              <div align = "center">
                              <?php
                              echo mosHTML::idBox($i, $row->id, ($row->fk_rentid != 0), 'vid');
                              ?>
                              </div>
              <?php
          }
          ?>
                      </td>
                      <td align = "center"><?php echo $row->id; ?></td>
                      <td align = "center"><?php echo $row->fk_vehicleid; ?></td>
                      <td align = "center"><?php echo $row->vtitle; ?></td>
                      <td align = "center"><?php echo $row->customer_name; ?></td>
                      <td align = "center" widt="30%"><?php echo $row->customer_comment; ?></td>
                      <td align = "center">
                          <a href=mailto:"<?php echo $row->customer_email; ?>">
                  <?php echo $row->customer_email; ?>
                          </a>
                      </td>
                      <td align = "center"><?php echo $row->customer_phone; ?></td>
                  </tr>
      <?php } ?>
          </table>
          <?php echo $pageNav->getListFooter(); ?>
          <input type="hidden" name="option" value="<?php echo $option; ?>" />
          <input type="hidden" name="task" value="buying_requests" />
          <input type="hidden" name="boxchecked" value="0" />
      </form>
      <?php
  }

  static function showVehicles($option, $rows_vehicle,& $language, & $clist,
              & $rentlist, & $publist, & $ownerlist, & $search, $search_list, & $pageNav)
  {
      global $my, $mosConfig_live_site, $mainframe, $session, $templateDir;
      // for 1.6
      global $doc, $css;
      $doc->addStyleSheet($css);
      $doc->addScript($mosConfig_live_site . '/components/com_vehiclemanager/includes/functions.js');
      //$doc->addScript($mosConfig_live_site.'/components/com_vehiclemanager/includes/functions.js', 'text/javascript');

      $html = "<div class='vehicle_manager_caption' ><i class='fa fa-car'></i> " . _VEHICLE_MANAGER_SHOW . "</div>";
      $app = JFactory::getApplication();
      $app->JComponentTitle = $html;

      $script = "
      function before_print_check(){
          var add = document.getElementsByName('vid[]');
          var count=0;

          for(var i=0;i<add.length;i++){
              if(add[i].checked){
                  count++;
                  break;
              }
          }

          if(count == 0){
              alert('Please choose some vehicles');
              exit;
          } else{
              document.adminForm.target = '_blank' ;
              document.adminForm.task.value='print_vehicles';
              document.adminForm.submit();
          }
      } ";
      $doc->addScriptDeclaration($script);
      // --

      $saveOrderingUrl = 'index.php?option=com_vehiclemanager&task=saveVehicleOrder';
        JHtml::_('sortablelist.sortable', 'vehicleList', 'adminForm', 'asc', $saveOrderingUrl);

      ?>
      <form action="index.php" method="post" name="adminForm"  class="vehicles_main"  id="adminForm" >
<!--            <table cellpadding="4" cellspacing="0" border="0" width="100%">
              <tr>
                  ********   begin add for button print in Manager Vehicles   *******************
                  </td>
                  ******   end add for button print in Manager Vehicles   *******************
              </tr>
          </table>-->
          <table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminlist adminlist_03">
              <tr>
                  <td><?php echo _VEHICLE_MANAGER_SHOW_SEARCH; ?></td>
                  <td><input type="text" name="search" value="<?php echo $search; ?>" class="inputbox" onChange="document.adminForm.submit();" /></td>
                  <td>by</td>
                  <td><?php echo $search_list; ?></td>
                  <td><?php echo $publist; ?></td>
                  <td><?php echo $ownerlist; ?></td>
                  <td><?php echo $rentlist; ?></td>
                  <td><?php echo $clist; ?></td>
                  <td><?php echo $language; ?></td>
                  <td width="30px">
                      <a href= "#"  onclick = "before_print_check();">
                          <img src="<?php echo $mosConfig_live_site; ?>/administrator/components/com_vehiclemanager/images/print.png" alt="Print" name="Print" title = "Print" align="middle" border="0" />
                      </a>
                          <?php if (version_compare(JVERSION, "3.0.0", "ge"))
                          {
                              ?>
                      <td>
                          <div class="btn-group pull-left hidden-phone">
                              <label for="limit" class="element-invisible"><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC'); ?></label>
          <?php echo $pageNav->getLimitBox(); ?>
                          </div>
                      </td>
      <?php } ?>
                  </td>
              </tr>
          </table>
          <table cellpadding="4" cellspacing="0" border="1px" width="100%" class="adminlist adminlist_04 table table-striped" id="vehicleList">
              <thead>
              <tr>
                  <th class="icon-menu-2"></th>
                  <th width="20"><input type="checkbox" name="toggle" onClick="vm_checkAll(this<?php //echo count( $rows_vehicle );   ?>);" /></th>
                  <th width="30">ID</th>
                  <th align = "center" class="title" width="5%" nowrap="nowrap"><?php echo _VEHICLE_MANAGER_LABEL_VEHICLEID; ?></th>
                  <th align = "center" class="title" width="27%" nowrap="nowrap"><?php echo _VEHICLE_MANAGER_LABEL_TITLE; ?></th>
                  <th align = "center" class="title" width="27%" nowrap="nowrap"><?php echo _VEHICLE_MANAGER_LABEL_MAKER, ', ', _VEHICLE_MANAGER_LABEL_MODEL; ?></th>
                  <th align = "center" class="title" width="16%" nowrap="nowrap"><?php echo _VEHICLE_MANAGER_LABEL_CATEGORY; ?></th>
                  <th align = "center" class="title" width="5%" nowrap="nowrap"><?php echo _VEHICLE_MANAGER_LABEL_OWNER; ?></th>
                  <th align = "center" class="title" width="5%" nowrap="nowrap"><?php echo _VEHICLE_MANAGER_LABEL_RENT; ?></th>
                  <th align = "center" class="title" width="5%" nowrap="nowrap"><?php echo _VEHICLE_MANAGER_LABEL_HITS; ?></th>
                  <th align = "center" class="title" width="5%" nowrap="nowrap"><?php echo _VEHICLE_MANAGER_LABEL_PUBLIC; ?></th>
                  <th align = "center" class="title" width="5%" nowrap="nowrap"><?php echo _VEHICLE_MANAGER_LABEL_APPROVED; ?></th>
                  <th align = "center" class="title" width="5%" nowrap="nowrap"><?php echo _VEHICLE_MANAGER_LABEL_CONTROL; ?></th>
                  <th align = "center" class="title" width="5%" nowrap="nowrap"><?php echo _VEHICLE_MANAGER_LABEL_LANGUAGE; ?></th>
              </tr>
              </thead>
              <tbody>
      <?php
      for ($i = 0, $n = count($rows_vehicle); $i < $n; $i++) {
          $row = $rows_vehicle[$i];
          ?>
                  <tr class="row<?php echo $i % 2; ?>">


                  <td class="order nowrap center hidden-phone" align = "center" sortable-group-id="none">
                    <span class="sortable-handler" style="cursor: move;">
                        <span class="icon-menu"></span>
                    </span>
                    <input type="text" style="display:none" name="order[]" size="5" value="<?php echo $row->ordering; ?>" class="width-20 text-area-order" />
                  </td>

                  <td width="20" align="left">
                      <?php if ($row->checked_out && $row->checked_out != $my->id)
                      {
                          ?>
                          &nbsp;
              <?php
          } else
              echo mosHTML::idBox($i, $row->id, ($row->checked_out && $row->checked_out != $my->id), 'vid');
          ?>
                      </td>
                      <td align = "center"><?php echo $row->id; ?></td>
                      <td align = "center"><?php echo $row->vehicleid; ?></td>

                      <td align="left">
                          <?php
                          if ($row->checked_out && $row->checked_out != $my->id)
                          {
                              echo $row->vtitle . " [ <i>Checked Out</i> ]";
                          } else
                          {
                              ?>
                              <a href="#edit" onClick="return listItemTask('cb<?php echo $i; ?>','edit')">
                              <?php echo $row->vtitle; ?>
                              </a>
                          <?php } ?>
                      </td>
                      <td align="left">
                          <?php
                          if ($row->checked_out && $row->checked_out != $my->id)
                          {
                              if ($row->maker == '' || $row->maker == 'other')
                                  echo $row->vmodel . " [ <i>Checked Out</i> ]";
                              else
                                  echo $row->maker . ', ' . $row->vmodel . " [ <i>Checked Out</i> ]";
                          } else
                          {
                              ?>
                              <a href="#edit" onClick="return listItemTask('cb<?php echo $i; ?>','edit')">
                              <?php
                              if ($row->maker == '' || $row->maker == 'other')
                                  echo $row->vmodel;
                              else
                                  echo $row->maker . ', ' . $row->vmodel;
                              ?>
                              </a>
          <?php } ?>
                      </td>
                      <!--td>
                          <?php echo $pageNav->orderUpIcon($i, ($row->catid == @$rows_vehicle[$i - 1]->catid), "vehicleorderup"); ?>
                      </td>
                            <td>
                          <?php echo $pageNav->orderDownIcon($i, $n, ($row->catid == @$rows_vehicle[$i + 1]->catid), "vehicleorderdown"); ?>
                      </td-->
                      <td align = "center"><?php echo $row->category; ?></td>
                      <td align = "center"><?php echo $row->editor; ?></td>
                      <td align = "center">
          <?php
          if ($row->listing_type == 1)
          {
              if ($row->rent_from == null)
              {
                  ?>
                                  <a href="javascript: void(0);" onClick="return listItemTask('cb<?php echo $i; ?>','rent')">
                                      <img src='./components/com_vehiclemanager/images/lend_f2.png' align='middle' width='15' height='15' border='0' alt='Rent out' />
                                      <br />
                                  </a>
                                  <?php
                              } else
                              {
                                  ?>
                                  <a href="javascript: void(0);" onClick="return listItemTask('cb<?php echo $i; ?>','rent_return')">
                                      <img src='./components/com_vehiclemanager/images/lend_return_f2.png' align='middle' width='15' height='15' border='0' alt='Return vehicle' />
                                      <br />
                                  </a>
                              <?php
                          }
                      }
                      ?>
                      </td>
                      <td align = "center"><?php echo $row->hits; ?></td>
                      <?php
                      $task = $row->published ? 'unpublish' : 'publish';
                      $alt = $row->published ? 'Unpublish' : 'Publish';
                      $img = $row->published ? 'tick.png' : 'publish_x.png';

                      $task1 = $row->approved ? 'unapprove' : 'approve';
                      $alt1 = $row->approved ? 'Unapproved' : 'Approved';
                      $img1 = $row->approved ? 'tick.png' : 'publish_x.png';
                      ?>
                      <td width="5%" align="center">
                          <a href="javascript: void(0);" onClick="return listItemTask('cb<?php echo $i; ?>','<?php echo $task; ?>')">
                              <?php
                              if (version_compare(JVERSION, "1.6.0", "lt"))
                              {
                                  ?>
                                  <img src="<?php echo $mosConfig_live_site . "/administrator/images/" . $img; ?>" width="12" height="12" border="0" alt="<?php echo $alt; ?>" />
                                  <?php
                              } else
                              {
                                  ?>
                                  <img src="<?php echo $templateDir . "/images/admin/" . $img; ?>" width="12" height="12" border="0" alt="<?php echo $alt; ?>" />
                                  <?php
                              }
                              ?>
                          </a>
                      </td>
                      <td width="5%" align="center">
                          <a href="javascript: void(0);" onClick="return listItemTask('cb<?php echo $i; ?>','<?php echo $task1; ?>')">
                              <?php
                              if (version_compare(JVERSION, "1.6.0", "lt"))
                              {
                                  ?>
                                  <img src="<?php echo $mosConfig_live_site . "/administrator/images/" . $img1; ?>" width="12" height="12" border="0" alt="<?php echo $alt1; ?>" />
                                  <?php
                              } else
                              {
                                  ?>
                                  <img src="<?php echo $templateDir . "/images/admin/" . $img1; ?>" width="12" height="12" border="0" alt="<?php echo $alt1; ?>" />
                          <?php
                      }
                      ?>
                          </a>
                      </td>

                      <?php
                      if ($row->checked_out)
                      {
                          ?>
                          <td align="center"><?php echo $row->editor1; ?></td>
                  <?php } else
                  {
                      ?>
                          <td align="center">&nbsp;</td>
          <?php } ?>
                          <td align = "center"><?php echo $row->language; ?></td>
                  </tr>
          <?php
      }//end for
      ?>
      </tbody>
              <tr><td colspan = "14"><?php echo $pageNav->getListFooter(); ?></td>
              </tr>
          </table>
          <input type="hidden" name="option" value="<?php echo $option; ?>" />
          <input type="hidden" name="task" value="" />
          <input type="hidden" name="boxchecked" value="0" />
      </form>

      <?php
  }

//**********   begin for manage reviews  *****************
  static function showManageReviews($option, & $pageNav, & $reviews)
  {
      global $my, $mosConfig_live_site, $mainframe, $templateDir;
      // for 1.6
      global $doc, $css;
      $doc->addStyleSheet($css);
      $doc->addScript($mosConfig_live_site . '/components/com_vehiclemanager/includes/functions.js');
      $doc->addScript($mosConfig_live_site . '/media/system/js/core.js');

      $html = "<div class='vehicle_manager_caption' ><i class='fa fa-car'></i> " . _VEHICLE_MANAGER_SHOW_REVIEW_MANAGER . "</div>";
      $app = JFactory::getApplication();
      $app->JComponentTitle = $html;
      ?>
      <form action="index.php" method="post" name="adminForm" id="adminForm" >
      <?php if (version_compare(JVERSION, "3.0.0", "ge"))
      {
          ?>
              <table width="100%"  class="table_04">
                  <tr>
                      <td>
                          <div class="btn-group pull-right hidden-phone">
                              <label for="limit" class="element-invisible"><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC'); ?></label>
          <?php echo $pageNav->getLimitBox(); ?>
                          </div>
                      </td>
                  </tr>
              </table>
      <?php } ?>
          <table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminlist adminlist_05">
              <tr>
                  <th width="20" align="center"><input type="checkbox" name="toggle" onClick="vm_checkAll(this<?php //echo count( $reviews );  ?>);" /></th>
                  <th align="center" width="30"><a href="#numer" onClick="return listItemTask('cb<?php echo "1"; ?>','sorting_manage_review_numer');">#</a></th>
                  <th align="center" class="title" width="25%" nowrap="nowrap"><a href="#vehicle_title" onClick="return listItemTask('cb<?php echo "1"; ?>','sorting_manage_review_title_vehicle');"><?php echo _VEHICLE_MANAGER_LABEL_TITLE_VEHICLE; ?></a></th>
                  <th align="center" class="title" width="13%" nowrap="nowrap"><a href="#title_catigory" onClick="return listItemTask('cb<?php echo "1"; ?>','sorting_manage_review_title_catigory');"><?php echo _VEHICLE_MANAGER_LABEL_CATEGORY; ?></a></th>
                  <th align="center" class="title" width="15%" nowrap="nowrap"><a href="#title_review" onClick="return listItemTask('cb<?php echo "1"; ?>','sorting_manage_review_title_review');"><?php echo _VEHICLE_MANAGER_LABEL_TITLE_COMMENT; ?></a></th>
                  <th align="center" class="title" width="10%" nowrap="nowrap"><a href="#user_name" onClick="return listItemTask('cb<?php echo "1"; ?>','sorting_manage_review_user_name');"><?php echo _VEHICLE_MANAGER_LABEL_RENT_USER; ?></a></th>
                  <th align="center" class="title" width="11%" nowrap="nowrap"><a href="#date" onClick="return listItemTask('cb<?php echo "1"; ?>','sorting_manage_review_date');"><?php echo _VEHICLE_MANAGER_REVIEW_DATE; ?></a></th>
                  <th align="center" class="title" width="11%" nowrap="nowrap"><a href="#rating" onClick="return listItemTask('cb<?php echo "1"; ?>','sorting_manage_review_rating');"><?php echo _VEHICLE_MANAGER_LABEL_RATING; ?></a></th>
                  <th align="center" class="title" width="10%" nowrap="nowrap"><a href="#published" onClick="return listItemTask('cb<?php echo "1"; ?>','sorting_manage_review_published');"><?php echo _VEHICLE_HEADER_PUBLISHED; ?></a></th>
              </tr>
                      <?php
                      for ($i = 0, $n = count($reviews); $i < $n; $i++) {
                          $row = $reviews[$i];
                          ?>
                  <tr class="row<?php echo $i % 2; ?>">
                      <td width="20" align="center">
                        <!--<input type="checkbox" id="cb<?php echo $i; ?>" name="vid[]" value="<?php echo $row->review_id; ?>" onClick="isChecked(this.checked);" />-->
                              <?php
                              echo mosHTML::idBox($i, $row->review_id, false, 'vid');
                              ?>
                      </td>
                      <td align="center" width="30"><?php echo $reviews[$i]->review_id; ?></td>
                      <td align="center" width="25%"><?php echo $reviews[$i]->vehicle_title; ?></td>
                      <td align="center" width="16%"><?php echo $reviews[$i]->title_catigory; ?></td>
                      <td align="center" width="25%">
                          <a href="#edit" onClick="return listItemTask('cb<?php echo $i; ?>','edit_manage_review');">
                              <?php
                              //if 'title_comment' >55 else 'title_comment' -- all
                              if (strlen($reviews[$i]->title_review) > 55)
                              {
                                  for ($j = 0; $j < 55; $j++) {
                                      echo $reviews[$i]->title_review[$j];
                                  }
                              } else
                              {
                                  echo $reviews[$i]->title_review; /* title comment */
                              }
                              ?>
                          </a>
                      </td>
                      <td align="center" width="7%"><?php echo $reviews[$i]->user_name; ?></td>
                      <td align="center" width="8%"><?php echo $reviews[$i]->date; ?></td>
                      <td align="center" width="7%">
                          <div><img src="../components/com_vehiclemanager/images/rating-<?php echo $reviews[$i]->rating; ?>.png" alt="<?php echo ($reviews[$i]->rating) * 2; ?>" border="0" align="right"/>&nbsp;</div>
                      </td>
                      <td align="center" width="7%">
                              <?php
                              $task = $reviews[$i]->published ? 'unpublish_manage_review' : 'publish_manage_review';
                              $alt = $reviews[$i]->published ? 'Unpublish' : 'Publish';
                              $img = $reviews[$i]->published ? 'tick.png' : 'publish_x.png';
                              ?>
                          <a href="javascript: void(0);" onClick="return listItemTask('cb<?php echo $i; ?>','<?php echo $task; ?>')">
                              <?php
                              if (version_compare(JVERSION, "1.6.0", "lt"))
                              {
                                  ?>
                                  <img src="<?php echo $mosConfig_live_site . "/administrator/images/" . $img; ?>" width="12" height="12" border="0" alt="<?php echo $alt; ?>" />
              <?php
          } else
          {
              ?>
                                  <img src="<?php echo $templateDir . "/images/admin/" . $img; ?>" width="12" height="12" border="0" alt="<?php echo $alt; ?>" />
                      <?php
                  }
                  ?>
                          </a>

                      </td>
                  </tr>
          <?php
      }//end for(...)
      ?>
              <tr><td colspan = "11"><?php echo $pageNav->getListFooter(); ?></td></tr>
          </table>

                  <!--input type="hidden" name="id" value="<?php /* echo $$row->fk_vehicleid;; */ ?>" /-->
          <input type="hidden" name="option" value="<?php echo $option; ?>" />
          <input type="hidden" name="task" value="manage_review" />
          <input type="hidden" name="boxchecked" value="0" />
      </form>

      <?php
  }

//*****************   end for manage reviews   ****************************************
//***************   begin add for button print in Manager Vehicles   ********************
  static function showPrintVehicles($rows)
  {
      global $my, $mosConfig_live_site, $mainframe;
      // for 1.6
      global $doc;
      $doc->addScript($mosConfig_live_site . '/components/com_vehiclemanager/includes/functions.js');
      // --
      ?>
      <html>
          <head>
              <title>
              </title>
          </head>
          <body>
              <form name="Print" action="<?php echo $mosConfig_live_site; ?>/administrator/index.php?option=com_vehiclemanager&task=print_item" method="post" target="_top">
                  <div class="adminform_49">
      <?php echo _VEHICLE_MANAGER_TOOLBAR_ADMIN_PRINT_FONT_SIZE; ?>:
                      <select name="font_size" title="Select size font!">
                          <option value="1">1
                          <option value="2">2
                          <option value="3">3
                          <option value="4">4
                          <option value="5">5
                          <option value="6">6
                          <option value="7">7
                          <option value="8" selected >8
                          <option value="9">9
                          <option value="10">10
                          <option value="11">11
                          <option value="12">12
                          <option value="13">13
                          <option value="14">14
                          <option value="15">15
                          <option value="16">16
                          <option value="17">17
                          <option value="18">18
                      </select>
                      <br /><?php echo _VEHICLE_MANAGER_TOOLBAR_ADMIN_PRINT_FORMAT; ?>:<br />
                      <!--
                          <input type="radio" name="format_w_h" value="verticall" title="Checked format!" checked/>Vertically
                          <input type="radio" name="format_w_h" value="horizontall" title="Checked format!"/>Horizontally<br />
                      -->
                      <input type="hidden" name="format_w_h" value="verticall" title="Checked format!" checked />

                      <select name="format" title="Select size font!">
                          <option value="A5">A5&nbsp;(148x210&nbsp;mm)
                          <option value="A4" selected >A4&nbsp;(210x297&nbsp;mm)
                          <option value="A3">A3&nbsp;(297x420&nbsp;mm)
                          <option value="Letter">Letter&nbsp;(8,5x11&nbsp;inch)
                          <option value="Legal">Legal&nbsp;(8,5x14&nbsp;inch)
                          <option value="Tabloid">Tabloid&nbsp;(11x17&nbsp;inch)
                          <option value="Executive">Executive&nbsp;(7,5x10&nbsp;inch)
                      </select>
                      <p><?php echo _VEHICLE_MANAGER_TOOLBAR_ADMIN_PRINT_SELECT; ?>
                          <br />
                          <input type="submit" value="Next" title="Next step for print!"/>
                      </p>
                  </div>

                  <table cellpadding="4" cellspacing="0" border="1px" style="width:180mm" class="adminlist adminlist_06">
                      <tr class="printbg">
                          <td width="5%" nowrap="nowrap" align="center"><?php echo _VEHICLE_MANAGER_TOOLBAR_ADMIN_PRINT; ?>
                              <input type="checkbox" name="print_id" value="1" title="Select for print!" checked />
                          </td>
                          <td width="5%" nowrap="nowrap" align="center"><?php echo _VEHICLE_MANAGER_TOOLBAR_ADMIN_PRINT; ?>
                              <input type="checkbox" name="print_vehicleid" value="1" title="Select for print!" checked />
                          </td>
                          <td width="20%" nowrap="nowrap" align="center"><?php echo _VEHICLE_MANAGER_TOOLBAR_ADMIN_PRINT; ?>
                              <input type="checkbox" name="print_title" value="1" title="Select for print!" checked />
                          </td>
                          <td width="10%" nowrap="nowrap" align="center"><?php echo _VEHICLE_MANAGER_TOOLBAR_ADMIN_PRINT; ?>
                              <input type="checkbox" name="print_category" value="1" title="Select for print!" checked />
                          </td>
                          <td width="10%" nowrap="nowrap" align="center"><?php echo _VEHICLE_MANAGER_TOOLBAR_ADMIN_PRINT; ?>
                              <input type="checkbox" name="print_rent_from" value="1" title="Select for print!" checked />
                          </td>
                          <td width="10%" nowrap="nowrap" align="center"><?php echo _VEHICLE_MANAGER_TOOLBAR_ADMIN_PRINT; ?>
                              <input type="checkbox" name="print_rent_until" value="1" title="Select for print!" checked />
                          </td>
                          <td width="5%" nowrap="nowrap" align="center"><?php echo _VEHICLE_MANAGER_TOOLBAR_ADMIN_PRINT; ?>
                              <input type="checkbox" name="print_user_name" value="1" title="Select for print!" checked />
                          </td>
                          <td width="10%" nowrap="nowrap" align="center"><?php echo _VEHICLE_MANAGER_TOOLBAR_ADMIN_PRINT; ?>
                              <input type="checkbox" name="print_user_email" value="1" title="Select for print!" checked />
                          </td>

                          <td width="5%" nowrap="nowrap" align="center"><?php echo _VEHICLE_MANAGER_TOOLBAR_ADMIN_PRINT; ?>
                              <input type="checkbox" name="print_user_phone" value="1" title="Select for print!" checked />
                          </td>

                          <td width="10%" nowrap="nowrap" align="center"><?php echo _VEHICLE_MANAGER_TOOLBAR_ADMIN_PRINT; ?>
                              <input type="checkbox" name="print_user_mailing" value="1" title="Select for print!" checked />
                          </td>
                          <td width="5%" nowrap="nowrap" align="center"><?php echo _VEHICLE_MANAGER_TOOLBAR_ADMIN_PRINT; ?>
                              <input type="checkbox" name="print_hits" value="1" title="Select for print!" checked />
                          </td>
                      </tr>
                      <tr class="printbg">
                          <th width="5%">#</th>
                          <th width="5%" nowrap="nowrap"><?php echo _VEHICLE_MANAGER_LABEL_VEHICLEID; ?></th>
                          <th width="20%" nowrap="nowrap"><?php echo _VEHICLE_MANAGER_LABEL_TITLE; ?></th>
                          <th width="10%" nowrap="nowrap"><?php echo _VEHICLE_MANAGER_LABEL_CATEGORY; ?></th>
                          <th width="10%" nowrap="nowrap"><?php echo _VEHICLE_MANAGER_LABEL_RENT_FROM; ?></th>
                          <th width="10%" nowrap="nowrap"><?php echo _VEHICLE_MANAGER_LABEL_RENT_UNTIL; ?></th>
                          <th width="5%" nowrap="nowrap"><?php echo _VEHICLE_MANAGER_LABEL_RENT_USER; ?></th>
                          <th width="10%" nowrap="nowrap"><?php echo _VEHICLE_MANAGER_LABEL_RENT_EMAIL; ?></th>

                          <th width="5%" nowrap="nowrap"><?php echo _VEHICLE_MANAGER_LABEL_RENT_PHONE; ?></th>

                          <th width="10%" nowrap="nowrap"><?php echo _VEHICLE_MANAGER_LABEL_RENT_ADRES; ?></th>
                          <th width="5%" nowrap="nowrap"><?php echo _VEHICLE_MANAGER_LABEL_HITS; ?></th>
                      </tr>

      <?php for ($i = 0; $i < count($rows); $i++) { ?>
                          <tr bgcolor="#<?php
          if (($i % 2) != 1)
          {
              echo "efefef";
          } else
          {
              echo "ffffff";
          }
          ?>" >
                              <td width="5%" align="center"><?php echo wordwrap($rows[$i]->id, 6, "<br />\n", 1); ?></td>
                              <td width="5%" nowrap="nowrap" align="center"><?php echo wordwrap($rows[$i]->vehicleid, 6, "<br />\n", 1); ?></td>
                              <td width="20%" nowrap="nowrap"><?php echo wordwrap($rows[$i]->vtitle, 20, "<br />\n", 1); ?></td>
                              <td width="10%" nowrap="nowrap"><?php echo wordwrap($rows[$i]->category, 10, "<br />\n", 1); ?></td>
                              <td width="10%" nowrap="nowrap">
                                  <?php
                                  if (isset($rows[$i]->rent_from))
                                  {
                                      for ($j = 0; $j < 10; $j++) {
                                          echo $rows[$i]->rent_from[$j];
                                      }
                                  } else
                                  {
                                      echo "--";
                                  }
                                  ?>
                              </td>
                              <td width="10%" nowrap="nowrap">
                                  <?php
                                  if (isset($rows[$i]->rent_until))
                                  {
                                      for ($j = 0; $j < 10; $j++) {
                                          echo $rows[$i]->rent_until[$j];
                                      }
                                  } else
                                  {
                                      echo "--";
                                  }
                                  ?>
                              </td>
                              <td width="10%" nowrap="nowrap">
                                  <?php
                                  if (isset($rows[$i]->user_name) && ($rows[$i]->user_name != ""))
                                  {
                                      echo wordwrap($rows[$i]->user_name, 10, "<br />\n", 1);
                                  } else
                                  {
                                      echo "--";
                                  }
                                  ?>
                              </td>
                              <td width="10%" nowrap="nowrap">
          <?php
          if (isset($rows[$i]->user_email) && ($rows[$i]->user_email != ""))
          {
              echo wordwrap($rows[$i]->user_email, 10, "<br />\n", 1);
          } else
          {
              echo "--";
          }
          ?>
                              </td>
                              <td width="10%" nowrap="nowrap">
          <?php
          if (isset($rows[$i]->user_mailing) && ($rows[$i]->user_mailing != ""))
          {
              echo wordwrap($rows[$i]->user_mailing, 10, "<br />\n", 1);
          } else
          {
              echo "--";
          }
          ?>
                              </td>
                              <td width="5%" nowrap="nowrap"><?php echo wordwrap($rows[$i]->hits, 6, "<br />\n", 1); ?></td>
                          </tr>
      <?php } ?>
                  </table>
              </form>
          </body>
      </html>
      <?php
      @ session_start();
      $_SESSION['rows'] = $rows;
      exit();
  }

//end showPrintVehicles($rows)
//*********************************************************************************

  static function showPrintItem($rows)
  {
      global $my, $mosConfig_live_site, $mainframe;
      // for 1.6
      global $doc, $css;
      $doc->addStyleSheet($css);
      $doc->addScript($mosConfig_live_site . '/components/com_vehiclemanager/includes/functions.js');
      // --
      ?>
      <html>
          <head>
              <title>
              </title>
              <style type="text/css">
                  .print_font_nik {
                      background-color: #ffffff;
                      font-size: <?php echo $_REQUEST['font_size']; ?>pt;
                      color: #000000;
                      font-family: Arial, Times, Helvetica, Zapf-Chancery, Western, Courier;
                  }
              </style>
              <script language="JavaScript" type="text/javascript">
                  function print_item_no_button() {
                      var el  = document.getElementById('print_button_off');
                      el.style.display = 'none';
                      window.print();
                  }
              </script>
          </head>
          <body>
              <div align="left" id="print_button_off">
                  <p align="left">
                      <a href="#" onClick="javascript:print_item_no_button();" title="Print">
                          <img src="<?php echo $mosConfig_live_site; ?>/administrator/components/com_vehiclemanager/images/print.png"  alt="Print" name="Print" align="center" border="0" />
                      </a>
                  </p>
              </div>
              <?php
              $kol = 0;
              if (isset($_REQUEST['print_id']))
                  $kol++;
              if (isset($_REQUEST['print_vehicleid']))
                  $kol++;
              if (isset($_REQUEST['print_title']))
                  $kol++;
              if (isset($_REQUEST['print_category']))
                  $kol++;
              if (isset($_REQUEST['print_rent_from']))
                  $kol++;
              if (isset($_REQUEST['print_rent_until']))
                  $kol++;
              if (isset($_REQUEST['print_user_name']))
                  $kol++;
              if (isset($_REQUEST['print_user_email']))
                  $kol++;

              if (isset($_REQUEST['print_user_phone']))
                  $kol++;

              if (isset($_REQUEST['print_user_mailing']))
                  $kol++;
              if (isset($_REQUEST['print_hits']))
                  $kol++;

              if (($kol < 11) && (isset($_REQUEST['print_title']) == 0) && ($kol != 0))
                  $k = (int) (100 / $kol);
              if (($kol < 11) && (isset($_REQUEST['print_title'])) && ($kol != 0))
              {
                  $k = (int) (100 / $kol);
                  if ($kol == 10)
                  {
                      $k_tit = $k + 9;
                      $k -= 1;
                  }
                  if ($kol == 9)
                  {
                      $k_tit = $k + 8;
                      $k -= 1;
                  }
                  if ($kol == 8)
                  {
                      $k_tit = $k + 15;
                      $k -= 2;
                  }
                  if ($kol == 7)
                  {
                      $k_tit = $k + 16;
                      $k -= 3;
                  }
                  if ($kol == 6)
                  {
                      $k_tit = $k + 20;
                      $k -= 4;
                  }
                  if ($kol == 5)
                  {
                      $k_tit = $k + 20;
                      $k -= 5;
                  }
                  if ($kol == 4)
                  {
                      $k_tit = $k + 15;
                      $k -= 5;
                  }
                  if ($kol == 3)
                  {
                      $k_tit = $k + 10;
                      $k -= 5;
                  }
                  if ($kol == 2)
                  {
                      $k_tit = $k;
                  }
              }//end if

              if ($kol != 0)
              {
                  if (($_REQUEST['format_w_h'] == 'verticall') && ($_REQUEST['format'] == 'A5'))
                  {
                      $width_tabl = 118;
                  }//138;}
                  if (($_REQUEST['format_w_h'] == 'verticall') && ($_REQUEST['format'] == 'A4'))
                  {
                      $width_tabl = 180;
                  }//200;}
                  if (($_REQUEST['format_w_h'] == 'verticall') && ($_REQUEST['format'] == 'A3'))
                  {
                      $width_tabl = 267;
                  }//287;}
                  if (($_REQUEST['format_w_h'] == 'verticall') && ($_REQUEST['format'] == 'Letter'))
                  {
                      $width_tabl = 185;
                  }//205;}
                  if (($_REQUEST['format_w_h'] == 'verticall') && ($_REQUEST['format'] == 'Legal'))
                  {
                      $width_tabl = 185;
                  }//205;}
                  if (($_REQUEST['format_w_h'] == 'verticall') && ($_REQUEST['format'] == 'Tabloid'))
                  {
                      $width_tabl = 249;
                  }//269;}
                  if (($_REQUEST['format_w_h'] == 'verticall') && ($_REQUEST['format'] == 'Executive'))
                  {
                      $width_tabl = 160;
                  }//180;}

                  if (($_REQUEST['format_w_h'] == 'horizontall') && ($_REQUEST['format'] == 'A5'))
                  {
                      $width_tabl = 200;
                  }
                  if (($_REQUEST['format_w_h'] == 'horizontall') && ($_REQUEST['format'] == 'A4'))
                  {
                      $width_tabl = 287;
                  }
                  if (($_REQUEST['format_w_h'] == 'horizontall') && ($_REQUEST['format'] == 'A3'))
                  {
                      $width_tabl = 410;
                  }
                  if (($_REQUEST['format_w_h'] == 'horizontall') && ($_REQUEST['format'] == 'Letter'))
                  {
                      $width_tabl = 269;
                  }
                  if (($_REQUEST['format_w_h'] == 'horizontall') && ($_REQUEST['format'] == 'Legal'))
                  {
                      $width_tabl = 343;
                  }
                  if (($_REQUEST['format_w_h'] == 'horizontall') && ($_REQUEST['format'] == 'Tabloid'))
                  {
                      $width_tabl = 421;
                  }
                  if (($_REQUEST['format_w_h'] == 'horizontall') && ($_REQUEST['format'] == 'Executive'))
                  {
                      $width_tabl = 244;
                  }
                  ?>
                  <table cellpadding="4" cellspacing="0" border="1px" style="width:<?php echo $width_tabl; ?>mm" class="print_font_nik">
                      <tr class="printbg">
                              <?php if (isset($_REQUEST['print_id']))
                              {
                                  ?>
                              <th width="<?php
                                  if (isset($k))
                                  {
                                      echo $k . "%";
                                  } else
                                  {
                                      echo "5%";
                                  }
                                  ?>">#</th>
                              <?php } ?>
                              <?php if (isset($_REQUEST['print_vehicleid']))
                              {
                                  ?>
                              <th width="<?php
                                  if (isset($k))
                                  {
                                      echo $k . "%";
                                  } else
                                  {
                                      echo "5%";
                                  }
                                  ?>" nowrap="nowrap"><?php echo _VEHICLE_MANAGER_LABEL_VEHICLEID; ?></th>
                          <?php } ?>
                          <?php if (isset($_REQUEST['print_title']))
                          {
                              ?>
                              <th width="<?php
              if (isset($k_tit))
              {
                  echo $k_tit . "%";
              } else if (($kol == 1) && (isset($_REQUEST['print_title'])))
              {
                  echo "100%";
              } else
              {
                  echo "20%";
              }
                              ?>" nowrap="nowrap"><?php echo _VEHICLE_MANAGER_LABEL_TITLE; ?></th>
                              <?php } ?>
                              <?php if (isset($_REQUEST['print_category']))
                              {
                                  ?>
                              <th width="<?php
                  if (isset($k))
                  {
                      echo $k . "%";
                  } else
                  {
                      echo "10%";
                  }
                                  ?>" nowrap="nowrap"><?php echo _VEHICLE_MANAGER_LABEL_CATEGORY; ?></th>
                          <?php } ?>
                          <?php if (isset($_REQUEST['print_rent_from']))
                          {
                              ?>
                              <th width="<?php
              if (isset($k))
              {
                  echo $k . "%";
              } else
              {
                  echo "10%";
              }
                              ?>" nowrap="nowrap"><?php echo _VEHICLE_MANAGER_LABEL_RENT_FROM; ?></th>
                          <?php } ?>
                          <?php if (isset($_REQUEST['print_rent_until']))
                          {
                              ?>
                              <th width="<?php
              if (isset($k))
              {
                  echo $k . "%";
              } else
              {
                  echo "10%";
              }
                              ?>" nowrap="nowrap"><?php echo _VEHICLE_MANAGER_LABEL_RENT_UNTIL; ?></th>
                              <?php } ?>
                              <?php if (isset($_REQUEST['print_user_name']))
                              {
                                  ?>
                              <th width="<?php
                                  if (isset($k))
                                  {
                                      echo $k . "%";
                                  } else
                                  {
                                      echo "10%";
                                  }
                                  ?>" nowrap="nowrap"><?php echo _VEHICLE_MANAGER_LABEL_RENT_USER; ?></th>
                              <?php } ?>
                          <?php if (isset($_REQUEST['print_user_email']))
                          {
                              ?>
                              <th width="<?php
              if (isset($k))
              {
                  echo $k . "%";
              } else
              {
                  echo "10%";
              }
                              ?>" nowrap="nowrap"><?php echo _VEHICLE_MANAGER_LABEL_RENT_EMAIL; ?></th>
                                  <?php } ?>

                                  <?php if (isset($_REQUEST['print_user_phone']))
                          {
                              ?>
                              <th width="<?php
              if (isset($k))
              {
                  echo $k . "%";
              } else
              {
                  echo "10%";
              }
                              ?>" nowrap="nowrap"><?php echo _VEHICLE_MANAGER_LABEL_RENT_PHONE; ?></th>
                                  <?php } ?>

                                  <?php if (isset($_REQUEST['print_user_mailing']))
                                  {
                                      ?>
                              <th width="<?php
                                      if (isset($k))
                                      {
                                          echo $k . "%";
                                      } else
                                      {
                                          echo "10%";
                                      }
                                      ?>" nowrap="nowrap"><?php echo _VEHICLE_MANAGER_LABEL_RENT_ADRES; ?></th>
                                  <?php } ?>
                                  <?php if (isset($_REQUEST['print_hits']))
                                  {
                                      ?>
                              <th width="<?php
                      if (isset($k))
                      {
                          echo $k . "%";
                      } else
                      {
                          echo "5%";
                      }
                      ?>" nowrap="nowrap"><?php echo _VEHICLE_MANAGER_LABEL_HITS; ?></th>
                          <?php } ?>
                      </tr>
                          <?php for ($i = 0; $i < count($rows); $i++) {
                              ?>
                          <tr bgcolor="#<?php
              if (($i % 2) != 1)
              {
                  echo "efefef";
              } else
              {
                  echo "ffffff";
              }
                              ?>" >
                                      <?php if (isset($_REQUEST['print_id']))
                                      {
                                          ?>
                                  <td align="center" width="<?php
                      if (isset($k))
                      {
                          echo $k . "%";
                      } else
                      {
                          echo "5%";
                      }
                                          ?>">
                                  <?php
                                  if (isset($k))
                                  {
                                      $symbol = $k;
                                  } else
                                  {
                                      $symbol = 6;
                                  } echo wordwrap($rows[$i]->id, $symbol, "<br />\n", 1);
                                  ?>
                                  </td>
                                      <?php } ?>
                                      <?php if (isset($_REQUEST['print_vehicleid']))
                                      {
                                          ?>
                                  <td align="center" width="<?php
                      if (isset($k))
                      {
                          echo $k . "%";
                      } else
                      {
                          echo "5%";
                      }
                                          ?>" nowrap="nowrap">
                                  <?php
                                  if (isset($k))
                                  {
                                      $symbol = $k;
                                  } else
                                  {
                                      $symbol = 6;
                                  } echo wordwrap($rows[$i]->vehicleid, $symbol, "<br />\n", 1);
                                  ?>
                                  </td>
                                      <?php } ?>
                              <?php if (isset($_REQUEST['print_title']))
                              {
                                  ?>
                                  <td width="<?php
                  if (isset($k_tit))
                  {
                      echo $k_tit . "%";
                  } else if (($kol == 1) && (isset($_REQUEST['print_title'])))
                  {
                      echo "100%";
                  } else
                  {
                      echo "20%";
                  }
                                  ?>" nowrap="nowrap">
                                          <?php
                                          if (isset($k_tit))
                                          {
                                              $symbol = $k_tit;
                                          } else if (($kol == 1) && (isset($_REQUEST['print_title'])))
                                          {
                                              $symbol = $k;
                                          } else
                                          {
                                              $symbol = 20;
                                          }
                                          echo wordwrap($rows[$i]->vtitle, $symbol, "<br />\n", 1);
                                          ?>
                                  </td>
                              <?php } ?>
                                  <?php if (isset($_REQUEST['print_category']))
                                  {
                                      ?>
                                  <td width="<?php
                      if (isset($k))
                      {
                          echo $k . "%";
                      } else
                      {
                          echo "10%";
                      }
                                      ?>" nowrap="nowrap">
                                          <?php
                                          if (isset($k))
                                          {
                                              $symbol = $k;
                                          } else
                                          {
                                              $symbol = 10;
                                          } echo wordwrap($rows[$i]->category, $symbol, "<br />\n", 1);
                                          ?>
                                  </td>
                              <?php } ?>
                              <?php if (isset($_REQUEST['print_rent_from']))
                              {
                                  ?>
                                  <td width="<?php
                  if (isset($k))
                  {
                      echo $k . "%";
                  } else
                  {
                      echo "10%";
                  }
                                  ?>" nowrap="nowrap">
                                          <?php
                                          if (isset($rows[$i]->rent_from))
                                          {
                                              for ($j = 0; $j < 10; $j++) {
                                                  echo $rows[$i]->rent_from[$j];
                                              }
                                          } else
                                          {
                                              echo "--";
                                          }
                                          ?>
                                  </td>
                              <?php } ?>
                              <?php if (isset($_REQUEST['print_rent_until']))
                              {
                                  ?>
                                  <td width="<?php
                  if (isset($k))
                  {
                      echo $k . "%";
                  } else
                  {
                      echo "10%";
                  }
                                  ?>" nowrap="nowrap">
                                          <?php
                                          if (isset($rows[$i]->rent_until))
                                          {
                                              for ($j = 0; $j < 10; $j++) {
                                                  echo $rows[$i]->rent_until[$j];
                                              }
                                          } else
                                          {
                                              echo "--";
                                          }
                                          ?>
                                  </td>
                              <?php } ?>
                              <?php if (isset($_REQUEST['print_user_name']))
                              {
                                  ?>
                                  <td width="<?php
                  if (isset($k))
                  {
                      echo $k . "%";
                  } else
                  {
                      echo "10%";
                  }
                                  ?>" nowrap="nowrap">
                                          <?php
                                          if (isset($rows[$i]->user_name) && ($rows[$i]->user_name != ""))
                                          {
                                              if (isset($k))
                                              {
                                                  $symbol = $k;
                                              } else
                                              {
                                                  $symbol = 10;
                                              }
                                              echo wordwrap($rows[$i]->user_name, $symbol, "<br />\n", 1);
                                          } else
                                          {
                                              echo "--";
                                          }
                                          ?>
                                  </td>
              <?php } ?>
              <?php if (isset($_REQUEST['print_user_email']))
              {
                  ?>
                                  <td width="<?php
                  if (isset($k))
                  {
                      echo $k . "%";
                  } else
                  {
                      echo "10%";
                  }
                  ?>" nowrap="nowrap">
                  <?php
                  if (isset($rows[$i]->user_email) && ($rows[$i]->user_email != ""))
                  {
                      if (isset($k))
                      {
                          $symbol = $k;
                      } else
                      {
                          $symbol = 10;
                      }
                      echo wordwrap($rows[$i]->user_email, $symbol, "<br />\n", 1);
                  } else
                  {
                      echo "--";
                  }
                  ?>
                                  </td>
                  <?php } ?>
                  <?php if (isset($_REQUEST['print_user_mailing']))
                  {
                      ?>
                                  <td width="<?php
                  if (isset($k))
                  {
                      echo $k . "%";
                  } else
                  {
                      echo "10%";
                  }
                  ?>" nowrap="nowrap">
                      <?php
                      if (isset($rows[$i]->user_mailing) && ($rows[$i]->user_mailing != ""))
                      {
                          if (isset($k))
                          {
                              $symbol = $k;
                          } else
                          {
                              $symbol = 10;
                          }
                          echo wordwrap($rows[$i]->user_mailing, $symbol, "<br />\n", 1);
                      } else
                      {
                          echo "--";
                      }
                      ?>
                                  </td>
                              <?php } ?>
              <?php if (isset($_REQUEST['print_hits']))
              {
                  ?>
                                  <td width="<?php
                  if (isset($k))
                  {
                      echo $k . "%";
                  } else
                  {
                      echo "5%";
                  }
                  ?>" nowrap="nowrap">
                                  <?php
                                  if (isset($k))
                                  {
                                      $symbol = $k;
                                  } else
                                  {
                                      $symbol = 6;
                                  } echo wordwrap($rows[$i]->hits, $symbol, "<br />\n", 1);
                                  ?>
                                  </td>
                              <?php } ?>
                          </tr>
                          <?php }/* end for */ ?>

                  </table>
                          <?php
                      }//end if($kol != 0)
                      ?>

              <br/>
              <!--<?php echo $mosConfig_live_site; ?>/administrator/index.php?option=com_vehiclemanager# -->
              <!--<form name="back_print" action="javascript:parent.history.back()" method="post" target="_top">-->>
              <form name="back_print" action="index.php" method="post" target="_top">
                  <input type="submit" value="Back" title="Select other items for printing" />
              </form>

          </body>
      </html>
                          <?php
                          exit();
                      }

//end function showPrintItem()
//*********************************************************************
//********   end add for button print in Manager Vehicles   ***********
//*********************************************************************
//*********************************************************************


  /**
   * Writes the edit form for new and existing records
   *
   */
  static function editVehicle($option,
                              & $row,
                              & $clist,
                              & $rating,
                              & $delete_edoc,
                              $videos,
                              $youtube,
                              $tracks,
                              & $reviews,
                              & $test_list,
                              & $vehicle_type_list,
                              & $listing_status_list,
                              & $condition_status_list,
                              & $transmission_type_list,
                              & $listing_type_list,
                              & $drive_type_list,
                              & $fuel_type_list,
                              & $num_speed_list,
                              & $num_cylinder_list,
                              & $num_doors_list,
                              & $vehicle_photo,
                              &$vehicle_temp_photos,
                              & $vehicle_photos,
                              & $maker,
                              & $arr,
                              & $currentmodel,
                              & $modellist,
                              & $vehicle_rent_sal,
                              & $vehicle_feature,
                              & $currency,
                              & $languages,
                              & $extra_list,
                              $owner_email,
                              $currency_spacial_price,
                              $associateArray,
                              & $country = '',
                              & $region = '',
                              & $city = ''
                              )
  {
      global $vehiclemanager_configuration, $database;
      global $my, $mosConfig_live_site, $mainframe;
      global $doc, $css;

      if($vehiclemanager_configuration['special_price']['show']){
        $switchTranslateDayNight = _VEHICLE_MANAGER_RENT_SPECIAL_PRICE_PER_DAY;
      }else{
        $switchTranslateDayNight = _VEHICLE_MANAGER_RENT_SPECIAL_PRICE_PER_NIGHT;
      }
      $doc->addStyleSheet($css);
      $doc->addScript($mosConfig_live_site . '/components/com_vehiclemanager/includes/functions.js');
      $doc->addStyleSheet( $mosConfig_live_site.'/components/com_vehiclemanager/includes/jquerOs-ui.min.css' );
      $html = "<div class='vehicle_manager_caption' ><i class='fa fa-car'></i> " . _VEHICLE_MANAGER_SHOW . "</div>";
      $app = JFactory::getApplication();
      $app->JComponentTitle = $html;
      ?>
      <script type="text/javascript" src="<?php echo $mosConfig_live_site .
               '/components/com_vehiclemanager/includes/jQuerOs-2.2.4.min.js'; ?>"></script>
      <script type="text/javascript">jQuerOs=jQuerOs.noConflict();</script>
      <script type="text/javascript" src="<?php echo $mosConfig_live_site.
            '/components/com_vehiclemanager/includes/jquerOs-ui.min.js' ?>"></script>




      <script language="javascript" type="text/javascript">

        jQuerOs(document).ready(function($) {

            jQuerOs('#addVehicleTabs > li > a').click(function(){
                setTimeout(function() {
                    vm_initialize();
                },20);
            });
        });


        function trim(string){
          return string.replace(/(^\s+)|(\s+$)/g, "");
        }

        Joomla.submitbutton = function(pressbutton, section) {
          var form = document.adminForm;

          if (pressbutton == 'save' || pressbutton == 'apply') {
            var post_max_size = <?php echo return_bytes(ini_get('post_max_size')) ?>;
            var upl_max_fsize = <?php echo return_bytes(ini_get('upload_max_filesize')) ?>;
            var file_upl = <?php echo ini_get('file_uploads') ?>;
            var total_file_size = 0;

            for (i = 1;document.getElementById('new_upload_video'+i); i++){
              if(document.getElementById('new_upload_video'+i).files.length){
                if(document.getElementById('new_upload_video'+i).value != ''){
                  total_file_size += document.getElementById('new_upload_video'+i).files[0].size;
                  if(!file_upl){
                    window.scrollTo(0,findPosY(document.getElementById('new_upload_video'+i))-100);
                    document.getElementById('error_video').innerHTML = "<?php
                     echo _VEHICLE_MANAGER_SETTINGS_VIDEO_ERROR_UPLOAD_OFF; ?>";
                    document.getElementById('new_upload_video'+i).style.borderColor = "#FF0000";
                    document.getElementById('new_upload_video'+i).style.color = "#FF0000";
                    document.getElementById('error_video').style.color = "#FF0000";
                    return;
                  }
                  if(document.getElementById('new_upload_video'+i).files[0].size >= post_max_size){
                    window.scrollTo(0,findPosY(document.getElementById('new_upload_video'+i))-100);
                    document.getElementById('error_video').innerHTML = "<?php
                     echo _VEHICLE_MANAGER_SETTINGS_VIDEO_ERROR_POST_MAX_SIZE; ?>";
                    document.getElementById('new_upload_video'+i).style.borderColor = "#FF0000";
                    document.getElementById('new_upload_video'+i).style.color = "#FF0000";
                    document.getElementById('error_video').style.color = "#FF0000";
                    return;
                  }
                  if(document.getElementById('new_upload_video'+i).files[0].size >= upl_max_fsize){
                    window.scrollTo(0,findPosY(document.getElementById('new_upload_video'+i))-100);
                    document.getElementById('error_video').innerHTML = "<?php
                     echo _VEHICLE_MANAGER_SETTINGS_VIDEO_ERROR_UPLOAD_MAX_SIZE; ?>";
                    document.getElementById('new_upload_video'+i).style.borderColor = "#FF0000";
                    document.getElementById('new_upload_video'+i).style.color = "#FF0000";
                    document.getElementById('error_video').style.color = "#FF0000";
                    return;
                  }
                }
              }
            }

            if(total_file_size >= post_max_size){
              if(document.getElementById('error_video')){
                window.scrollTo(0,findPosY(document.getElementById('error_video'))-100);
                document.getElementById('error_video').innerHTML = "<?php
                 echo JText::_('_VEHICLE_MANAGER_SETTINGS_VIDEO_ERROR_POST_MAX_SIZE'); ?>";
                document.getElementById('error_video').style.borderColor = "#FF0000";
                document.getElementById('error_video').style.color = "#FF0000";
                document.getElementById('error_video').style.color = "#FF0000";
                return;
              }
            }

            if (trim(form.vehicleid.value) == '') {
              // alert("<?php echo _VEHICLE_MANAGER_ADMIN_INFOTEXT_JS_EDIT_VEHICLEID_CHECK; ?>");
              // return;
              window.scrollTo(0,findPosY(document.getElementById('vehicleid'))-100);
              document.getElementById('vehicleid').placeholder = "<?php
               echo _VEHICLE_MANAGER_ADMIN_INFOTEXT_JS_EDIT_VEHICLEID_CHECK; ?>";
              document.getElementById('vehicleid').style.borderColor = "#FF0000";
              document.getElementById('vehicleid').style.color = "#FF0000";
              return;
            }
            else if (form.catid.value == '') {
              window.scrollTo(0,findPosY(document.getElementById('catid'))-100);
              //window.scrollTo(0,findPosY(document.getElementById('catid_chzn'))-100);
              //jQuerOs('#catid_chzn ul').css('borderColor','#FF0000');
              //jQuerOs('#catid_chzn ul').css('color','#FF0000');
              document.getElementById('catid').style.borderColor = "#FF0000";
              document.getElementById('catid').style.color = "#FF0000";
              return;
            }else if (form.vtitle.value == '') {
              window.scrollTo(0,findPosY(document.getElementById('vtitle'))-100);
              document.getElementById('vtitle').placeholder = "<?php
               echo _VEHICLE_MANAGER_ADMIN_INFOTEXT_JS_EDIT_TITLE; ?>";
              document.getElementById('vtitle').style.borderColor = "#FF0000";
              document.getElementById('vtitle').style.color = "#FF0000";
              return;
            }else if (form.maker.value == '') {
              window.scrollTo(0,findPosY(document.getElementById('maker'))-100);
              //window.scrollTo(0,findPosY(document.getElementById('maker_chzn'))-100);
              //jQuerOs('#maker_chzn .chzn-single').css('borderColor','#FF0000');
              //jQuerOs('#maker_chzn .chzn-single').css('color','#FF0000');
              document.getElementById('maker').style.borderColor = "#FF0000";
              document.getElementById('maker').style.color = "#FF0000";
              return;
            }else if (form.vmodel.value == '' && form.maker.value == 'other') {
              window.scrollTo(0,findPosY(document.getElementById('vmodel'))-100);
              document.getElementById('vmodel').style.borderColor = "#FF0000";
              document.getElementById('vmodel').style.color = "#FF0000";
              return;
            }else if(
              (document.getElementById('price').value == '')
              && (<?php echo $vehiclemanager_configuration['mandatory_price_field_require']; ?>)
            ) {
              window.scrollTo(0,findPosY(document.getElementById('price'))-100);
              document.getElementById('price').style.borderColor = "#FF0000";
              document.getElementById('price').style.color = "#FF0000";
              return;
            }else if ((form.year.value == '') && (<?php echo $vehiclemanager_configuration['mandatory_year_issue_field_require']; ?>)) {
              window.scrollTo(0,findPosY(document.getElementById('year'))-100);
              //window.scrollTo(0,findPosY(document.getElementById('year_chzn'))-100);
              document.getElementById('year').style.borderColor = "#FF0000";
              document.getElementById('year').style.color = "#FF0000";
              //jQuerOs('#year_chzn .chzn-single').css('borderColor','#FF0000');
              //jQuerOs('#year_chzn .chzn-single').css('color','#FF0000');
              return;
            }else if ((form.mileage.value == '') && (<?php echo $vehiclemanager_configuration['mandatory_mileage_field_require']; ?>)) {
            window.scrollTo(0,findPosY(document.getElementById('mileage'))-100);
            document.getElementById('mileage').style.borderColor = "#FF0000";
            document.getElementById('mileage').style.color = "#FF0000";
            return;
            }else if(form.new_upload_track_url1){
              for (i = 1;document.getElementById('new_upload_track_url'+i); i++) {
                if(document.getElementById('new_upload_track'+i).value != ''
                  || document.getElementById('new_upload_track_url'+i).value != ''){
                    if(document.getElementById('new_upload_track_kind'+i).value == ''){
                      window.scrollTo(0,findPosY(document.getElementById('new_upload_track_kind'+i))-100);
                      document.getElementById('new_upload_track_kind'+i).placeholder = "<?php
                       echo _VEHICLE_MANAGER_ADMIN_INFOTEXT_JS_TRACK_KIND; ?>";
                      document.getElementById('new_upload_track_kind'+i).style.borderColor = "#FF0000";
                      document.getElementById('new_upload_track_kind'+i).style.color = "#FF0000";
                      return;
                    }else if(document.getElementById('new_upload_track_scrlang'+i).value == ''){
                      window.scrollTo(0,findPosY(document.getElementById('new_upload_track_scrlang'+i))-100);
                      document.getElementById('new_upload_track_scrlang'+i).placeholder = "<?php
                       echo _VEHICLE_MANAGER_ADMIN_INFOTEXT_JS_TRACK_LANGUAGE; ?>";
                      document.getElementById('new_upload_track_scrlang'+i).style.borderColor = "#FF0000";
                      document.getElementById('new_upload_track_scrlang'+i).style.color = "#FF0000";
                      return;
                    }else if(document.getElementById('new_upload_track_label'+i).value == ''){
                      window.scrollTo(0,findPosY(document.getElementById('new_upload_track_label'+i))-100);
                      document.getElementById('new_upload_track_label'+i).placeholder = "<?php
                       echo _VEHICLE_MANAGER_ADMIN_INFOTEXT_JS_TRACK_TITLE; ?>";
                      document.getElementById('new_upload_track_label'+i).style.borderColor = "#FF0000";
                      document.getElementById('new_upload_track_label'+i).style.color = "#FF0000";
                      return;
                    }
                  }
              }
              Joomla.submitform( pressbutton );
            }else {
              Joomla.submitform( pressbutton );
            }
          } else {
            Joomla.submitform( pressbutton );
          }
        }
        <?php
          $user_group = userGID_VM($my->id);
          $user_group_mas = explode(',', $user_group);
          $max_count_foto = 0;
          foreach ($user_group_mas as $value) {
              $count_foto_for_single_group =
               $vehiclemanager_configuration['user_manager_vm'][$value]['count_foto'];
              if($count_foto_for_single_group>$max_count_foto){
                  $max_count_foto = $count_foto_for_single_group;
              }
          }
          $count_foto_for_single_group = $max_count_foto;
        ?>
        var photos=0;
        function new_photos(){
            div= document.getElementById("items");
            photos++;
            var allowed_files = <?php echo $count_foto_for_single_group;?>;
            var count_vehicle_photos = <?php echo (is_array($vehicle_photos))?(count($vehicle_photos)):("0"); ?>;

            if ( count_vehicle_photos < allowed_files) {
                newitem="" + "<?php echo _VEHICLE_MANAGER_ADMIN_NEW_PHOTO ?>" + photos + ": ";
                newitem="<input type=\"file\" multiple='true' name=\"new_photo_file[]";
                newitem+="\" value=\"\"size=\"45\"><br>";
                newnode= document.createElement("span");
                newnode.innerHTML=newitem;
                div.appendChild(newnode);
            }else{
                newitem="<p> <?php echo _VEHICLE_MANAGER_MAX_PHOTOS_LIMIT; ?>: "+
                  <?php echo $count_foto_for_single_group;?> + " </p>";
                newnode= document.createElement("span");
                newnode.innerHTML=newitem;
                div.appendChild(newnode);
            }
        }
      </script>
      <form action="index.php" method="post" name="adminForm" id="adminForm" class="veh_dd_tabs add_vehicle" enctype="multipart/form-data">
        <?php
            
            echo JHtml::_('bootstrap.startTabSet', 'addVehicle', array('active' => 'panel_1_addVehicle') );
            echo JHtml::_('bootstrap.addTab', 'addVehicle', 'panel_1_addVehicle', _VEHICLE_MANAGER_ADD_VEHICLE_TAB_LABEL_GENERAL_INFO);

        ?>
        <div style="clear: both;"></div>
          <h2><?php echo _VEHICLE_MANAGER_ADD_VEHICLE_TAB_LABEL_REQUIRED_FIELDS; ?></h2>
          <input class="inputbox" type="hidden" name="idtrue" id="idtrue" value="<?php echo $row->id_true; ?>"  />
          <table class="adminform adminform_07">
            <tr>
              <td width="185"><?php echo _VEHICLE_MANAGER_LABEL_VEHICLEID; ?>:*</td>
              <td><input class="inputbox" type="text" name="vehicleid" id="vehicleid" size="20" maxlength="20" value="<?php echo $row->vehicleid; ?>" /></td>
            </tr>
            <tr>
              <td width="185"><?php echo _VEHICLE_MANAGER_LABEL_VEHICLE_TITLE; ?>:*</td>
              <td><input id = "vtitle" class="inputbox" type="text" name="vtitle" size="80" value="<?php echo $row->vtitle; ?>" /></td>
            </tr>
            <tr>
            <tr>
              <td width="185"><?php echo _VEHICLE_MANAGER_LABEL_CATEGORY; ?>:*</td>
              <td><?php echo $clist; ?></td>
            </tr>
<?php
/*********************************************************************************************/

            if(!empty($associateArray) && !empty($row->language)
              && $row->language != '' && $row->language != '*'){
?>
              <tr>
                  <td width="15%"><?php echo 'Vehicle Associate Language' ?>:</td>
<?php
      $j =1;
   //   print_r($associateArray);exit;
      foreach ($associateArray as $lang=>$value) {
          $displ = '';
          if(!$value['list']){
              $displ = 'none';
          }
?>
              <tr style="display: <?php echo $displ?>">
                  <td width="15%"><?php echo $lang; ?>:</td>
                  <td width="60%"><?php echo $value['list']; ?>
                  <input class="inputbox" id="associate_vehicle" type="text" name="associate_vehicle<?php echo $j;?>" size="20" readonly="readonly" maxlength="20" style="width:25px; margin-bottom: -4px;" value="<?php echo $value['assocId']; ?>" />
                  <input style="display: none" name="associate_vehicle_lang<?php echo $j;?>" value="<?php echo $lang ;?>"/>
                  </td>
              </tr>
<?php

      $j++;
      }
 }else{
?>
              <tr>
                  <td width="15%"><?php echo 'Vehicle Associate Language' ?>:</td>
                  <td width="60%"><?php echo 'Only for vehicles with language' ?>
              </tr>
<?php
 }
/*********************************************************************************************/
?>
              <tr>
                  <td width="185"><?php echo _VEHICLE_MANAGER_LABEL_LANGUAGE; ?>:</td>
                  <td><?php echo $languages; ?></td>
              </tr>
          </table>
          <h2><?php echo _VEHICLE_MANAGER_ADD_VEHICLE_TAB_LABEL_VEHICLE_DETAILS; ?></h2>
          <table class="adminform adminform_08">
              <tr>
                  <td width="185"><?php echo _VEHICLE_MANAGER_LABEL_MAKER; ?>:*</td>
                  <td><?php echo $maker; ?></td>
              </tr>
              <tr>
                  <td width="185"><?php echo _VEHICLE_MANAGER_LABEL_MODEL; ?>:*</td>
                  <td><?php echo $modellist; ?></td>
              </tr>
              <script type="text/javascript">

                      <?php

                      $makers = $arr[0];
                      $models = $arr[1];
                      echo 'var modelscars = [];';
                      for ($c = 0; $c < count($makers); $c++):
                          $makers[$c] = '\'' . $makers[$c] . '\'';
                          foreach ($models[$c] as $temp => $model)
                              $models[$c][$temp] = '\'' . $model . '\'';
                          echo 'var temp=new Array(' . implode(',', $models[$c]) . ");\n";
                          echo 'modelscars[' . $c . "]=temp;\n";
                      endfor;
                      echo 'var makers=new Array(' . implode(',', $makers) . ');';
                      ?>

                  function changedMaker(maker){
                      var id = in_array(maker.value,makers);
                      //<select onchange="setCurentModel(this)" size="1" class="inputbox" id="maker" name="maker">
                      var model = document.getElementsByName('vmodel')[0]
                      if(model.tagName.toLowerCase()=='input'){
                          var select =  document.createElement('select');
                          select.name='vmodel';
                          select.type='text';
                          select.setAttribute('onchange','changedModel(this)');
                          model.parentNode.appendChild(select);
                          model.parentNode.removeChild(model);
                      }
                      if((maker.value=='other')||(maker.value=='')){
                          setTextfield();
                          return;
                      }
                      clearSelectModel();
                      for(var c=0;c<modelscars[id].length;c++){
                          //modelscars+id[c]
                          createOptionModel(modelscars[id][c],modelscars[id][c]);
                      }
                      createOptionModel('other','<?php echo _VEHICLE_MANAGER_LABEL_SELECT_OTHER ; ?>');
                  }
                  function clearSelectModel(){
                      var objSelect=document.getElementsByName('vmodel')[0];
                      while(objSelect.options.length > 0){objSelect.remove(0);}
                      return objSelect;
                  }
                  function in_array(what, where) {
                      for(var i=0; i<where.length; i++)
                          if(what == where[i])
                              return i;
                      return false;
                  }
                  function setTextfield(){
                      var select=document.getElementsByName('vmodel')[0];
                      var textfield = document.createElement('input');
                      select.parentNode.appendChild(textfield);
                      select.parentNode.removeChild(select);
                      textfield.name='vmodel';
                       textfield.id='vmodel';
                       textfield.type='text';
                      textfield.size='35';
                      if (maker.value=='') textfield.disabled=true;

                  }
                  function createOptionModel(newValue,newText){

                      var objSelect = document.getElementsByName('vmodel')[0];
                      var objOption = document.createElement("option");
                      objOption.text = newText
                      objOption.value = newValue

                      if(document.all && !window.opera)
                      {objSelect.add(objOption);}
                      else
                      {objSelect.add(objOption, null);};

                  }
                  function changedModel(select){
                      if(select.value=="other"){
                          setTextfield();
                      }
                  }
                  function onloadPage(){
                      var maker = document.getElementsByName('maker')[0];
                      var model = document.getElementsByName('vmodel')[0];
                      if((maker.value!='other')&&(maker.value!='')){
                          changedMaker(maker);
                      }
                      setModel();
                  }
                  function setModel(){
                      var model = document.getElementsByName('vmodel')[0];
                      model.value='<?php echo $currentmodel; ?>';
                  }
              </script>
              <tr>
                  <td width="185"><?php echo _VEHICLE_MANAGER_LABEL_VEHICLE_TYPE; ?>:</td>
                  <td><?php echo $vehicle_type_list; ?></td>
              </tr>
              <tr>
                  <td width="185"><?php echo _VEHICLE_MANAGER_LABEL_LISTING_TYPE; ?>:</td>
                  <td><?php echo $listing_type_list; ?></td>
              </tr>
              <tr>
                  <td width="185"><?php echo _VEHICLE_MANAGER_LABEL_PRICE; ?>:
                  <?php if($vehiclemanager_configuration['mandatory_price_field_require']==1) { ?>*<?php } ?>
                  </td>
                  <td>
                      <input class="inputbox price" type="text" id="price"  name="price" size="15" value="<?php echo $row->price; ?>" />&nbsp;
                      <!--<input class="inputbox" type="text" name="priceunit" size="15" value="<?php echo $row->priceunit; ?>" />-->
              <?php echo $currency; ?>
                  </td>
              </tr>
              <tr>
                  <td width="185"><?php echo _VEHICLE_MANAGER_LABEL_PRICE_TYPE; ?>:</td>
                  <td><?php echo $test_list; ?></td>
              </tr>
              </table>
<!-- /////////////////////////////////////////Special Price \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\ -->
                  <strong><?php echo $switchTranslateDayNight;  ?></strong>
                  <p>
                  <div class="SpecialPriseBlock">
                    <div class="vehicle_input">
                      <?php echo _VEHICLE_MANAGER_LABEL_RENT_REQUEST_FROM; ?>:<br />
                      <p><input type="text" id="price_from" name="price_from"></p>
                    </div>
                    <div class="vehicle_input">
                      <?php echo _VEHICLE_MANAGER_LABEL_RENT_REQUEST_UNTIL;
                      ?>:<br />
                      <p>
                        <input type="text" id="price_to" name="price_to">
                      </p>
                    </div>
                    <div id="sp_alert_scroll"></div>
                    <p>
                      <?php echo _VEHICLE_MANAGER_LABEL_PRICE; ?><br/>
                      <input id="special_price" class="inputbox price"
                            type="text" name="special_price" size="15" value="" />
                    </p>
                    <p>
                      <?php echo _VEHICLE_MANAGER_LABEL_REVIEW_COMMENT;?>
                      <br />
                      <textarea id="comment_price" rows="5" cols="25" name="comment_price"></textarea>
                    </p>
                    <p>
                      <input id="subPrice" class="inputbox" type="button" name="new_price"
                              value="<?php echo _VEHICLE_MANAGER_RENT_ADD_SPECIAL_PRICE; ?>"/>
                    </p>
                  </div>
                  <div id ="message-here" style ='color: red; font-size: 18px;' ></div>
                  <div id ='SpecialPriseBlock'>
                    <table class="adminlist_04">
                      <tr>
                        <th class="title" width ="20%"><?php
                          echo $switchTranslateDayNight; ?></th>
                        <th class="title" width ="15%"><?php
                          echo _VEHICLE_MANAGER_FROM; ?></th>
                        <th class="title" width ="15%"><?php
                          echo _VEHICLE_MANAGER_TO; ?></th>
                        <th class="title" width ="30%"><?php
                          echo _VEHICLE_MANAGER_LABEL_REVIEW_COMMENT; ?></th>
                        <th class="title" width ="20%"><?php
                          echo _VEHICLE_MANAGER_LABEL_CALENDAR_SELECT_DELETE; ?></th>
                      </tr>
                      <?php
                      for ($i = 0; isset($vehicle_rent_sal) && $i < count($vehicle_rent_sal); $i++) {
                        $DateToFormat = str_replace("D",'d',(str_replace("M",'m',(str_replace('%','',
                                                    $vehiclemanager_configuration['date_format'])))));
                        $date_from = new DateTime($vehicle_rent_sal[$i]->price_from);
                        $date_to = new DateTime($vehicle_rent_sal[$i]->price_to);
                        ?>
                        <tr>
                          <?php
                          if ($vehiclemanager_configuration['sale_separator'] == '1') { ?>
                            <td align ='center'><?php
                              echo formatMoney($vehicle_rent_sal[$i]->special_price, $vehiclemanager_configuration['sale_fraction'], $vehiclemanager_configuration['price_format']); ?>
                           </td>
                            <?php      } else { ?>
                            <td align ='center'>
                              <?php echo $vehicle_rent_sal[$i]->special_price; ?>
                            </td>
                          <?php
                          } ?>
                          <td align ='center'><?php echo date_format($date_from, "$DateToFormat"); ?></td>
                          <td align ='center'><?php echo date_format($date_to, "$DateToFormat"); ?></td>
                          <td align ='center'><?php echo $vehicle_rent_sal[$i]->comment_price; ?></td>
                          <td align ='center'>
                            <input type="checkbox" name="del_rent_sal[]"
                                    value="<?php echo $vehicle_rent_sal[$i]->id; ?>" />
                          </td>
                        </tr>
                      <?php
                      }
                      ?>
                    </table>
                  </div>
   <!---------------------------------Start AJAX load track------------------------------>

                  <script language="javascript" type="text/javascript">
                    var request = null;
                    var tid=1;
                    function createRequest_track() {
                        if (request != null)
                        return;
                        try {
                           request = new XMLHttpRequest();
                        } catch (trymicrosoft) {
                            try {
                               request = new ActiveXObject("Msxml2.XMLHTTP");
                            } catch (othermicrosoft) {
                                try {
                                    request = new ActiveXObject("Microsoft.XMLHTTP");
                                } catch (failed) {
                                    request = null;
                                }
                            }
                        }
                        if (request == null)
                            alert(" :-( ___ Error creating request object! ");
                    }

                    function testInsert_track(id1,upload){
                      for(var i=1; i< t_counter; i++){
                          if(upload.id != ('new_upload_track'+i) &&
                          document.getElementById('new_upload_track'+i).value == upload.value){
                              return false;
                          }
                      }
                      return true;
                    }

                    function refreshRandNumber_track(id1,upload){
                        id=id1;
                        if(testInsert_track(id1,upload)){
                            createRequest_track();
                            var url = "<?php echo $mosConfig_live_site . "/administrator/index.php?option=$option&task=checkFile&format=raw";
                            ?>&file="+encodeURI(upload.value)+"&path=<?php
                            echo str_replace("\\", "/", $mosConfig_live_site) . '/components/com_vehiclemanager/media/track/'?>";
                           try{
                            request.onreadystatechange = updateRandNumber_track;
                            request.open("GET", url,true);
                            request.send(null);
                            }catch (e)
                            {
                                alert(e);
                            }
                        }
                        else
                        {
                            alert("You alredy select this track file");
                            upload.value="";
                        }
                    }

                    function updateRandNumber_track() {
                        if (request.readyState == 4) {
                            document.getElementById("randNumTrack"+tid).innerHTML = request.responseText;
                        }
                    }
                  </script>

 <!-------------------------------- END Ajax load track---------------------------------->

<!-------------------------------- START Ajax load video---------------------------------->


            <script language="javascript" type="text/javascript">

               var request = null;
               var vid=1;

               function createRequest_video(){
                    if (request != null)
                    return;
                    try {
                       request = new XMLHttpRequest();
                    } catch (trymicrosoft) {
                        try {
                            request = new ActiveXObject("Msxml2.XMLHTTP");
                        } catch (othermicrosoft) {
                            try {
                                request = new ActiveXObject("Microsoft.XMLHTTP");
                            } catch (failed) {
                                request = null;
                            }
                        }
                    }
                    if (request == null)
                        alert(" :-( ___ Error creating request object! ");
                }

                function testInsertVideo(id1,upload){
                    for(var i=1 ;i< v_counter; i++){
                        if(upload.id != ('new_upload_video'+i) &&
                        document.getElementById('new_upload_video'+i).value == upload.value)
                        {
                          return false;
                        }
                    }
                    return true;
                }

                function refreshRandNumber_video(id1,upload){
                    id=id1;
                    if(testInsertVideo(id1,upload)){
                        createRequest_video();
                        var url = "<?php echo $mosConfig_live_site . "/administrator/index.php?option=$option&task=checkFile&format=raw";
                        ?>&file="+encodeURI(upload.value)+"&path=<?php
                        echo str_replace("\\", "/", $mosConfig_live_site) . '/components/com_vehiclemanager/media/video/' ?>";
                       try{
                        request.onreadystatechange = updateRandNumber_video;
                        request.open("GET",url,true);
                        request.send(null);
                        }catch (e)
                        {
                            alert(e);
                        }
                    }
                    else
                    {
                        alert("You alredy select this video file");
                        upload.value="";
                    }
                }

                function updateRandNumber_video() {
                    if (request.readyState == 4) {
                        document.getElementById("randNumVideo"+vid).innerHTML = request.responseText;
                    }
                }
                </script>


  <!-------------------------------- END Ajax load video---------------------------------->


                  <script language="javascript" type="text/javascript">
                    function changeButtomName() {
                      document.getElementById('v_add').value = "<?php echo _VEHICLE_MANAGER_LABEL_VIDEO_ADD_ALTERNATIVE_VIDEO ?>";
                    }

                    var v_counter=0;
                    function new_videos(){
                        div = document.getElementById("v_items");
                        button = document.getElementById("v_add");
                        v_counter++;
                        newitem='<table width="100%">'+
                                  '<tr>'+
                                    '<td width="160px">'+
                                      '<strong style="float:left">' +
                                        "<?php echo _VEHICLE_MANAGER_LABEL_VIDEO_UPLOAD ?>"+v_counter+
                                      ': </strong>'+
                                    '</td>'+
                                    '<td width="400px">'+
                                      '<input style="float:left; width:100%" type="file"'+
                                              'onClick="document.adminForm.new_upload_video_url'+
                                              v_counter+'" value =""'+
                                            ' onChange="refreshRandNumber_video('+v_counter+',this);"'+
                                            ' name="new_upload_video'+v_counter+'" id="new_upload_video'+v_counter+
                                            '" value="" size="45">'+
                                      '<span id="randNumVideo'+v_counter+'" style="color:red;"></span>'+
                                    '</td>'+
                                  '</tr>'+
                                  '<tr><td>OR</td><td></td></tr>'+
                                '</table>';
                        newnode = document.createElement("span");
                        newnode.innerHTML = newitem;
                        div.insertBefore(newnode,button);

                        newitem = '<table width="100%"">'+
                                    '<tr>'+
                                      '<td width="160px">'+
                                        '<strong>'+
                                          "<?php echo _VEHICLE_MANAGER_LABEL_VIDEO_UPLOAD_URL; ?>" +v_counter+
                                        ': </strong>'+
                                      '</td>'+
                                      '<td width="400px">'+
                                        '<input style="float:left; width:220px" type="text"'+
                                          ' name="new_upload_video_url'+v_counter+'"'+
                                          ' id="new_upload_video_url'+v_counter+'" value="" size="45">'+
                                      '</td>'+
                                    '</tr>'+
                                    '<tr><td>OR</td><td></td></tr>'+
                                  '</table>';
                        newnode = document.createElement("span");
                        newnode.innerHTML = newitem;
                        div.insertBefore(newnode,button);

                        newitem = '<table width="100%">'+
                                    '<tr>'+
                                      '<td width="160px">'+
                                        '<strong>'+
                                          "<?php echo _VEHICLE_MANAGER_LABEL_VIDEO_UPLOAD_YOUTUBE_CODE; ?>" +
                                        ': </strong>'+
                                      '</td>'+
                                      '<td width="400px">'+
                                        '<input style="float:left; width:220px" type="text"'+
                                              ' name="new_upload_video_youtube_code'+v_counter+'"'+
                                              ' id="new_upload_video_youtube_code'+v_counter+'" value="" size="45">'+
                                      '</td>'+
                                    '</tr>'+
                                  '</table>'+
                                  '<?php echo _VEHICLE_MANAGER_LABEL_PRIOTITY; ?>'
                        newnode=document.createElement("span");
                        newnode.innerHTML=newitem;
                        div.insertBefore(newnode,button);
                        var allowed_files = 5;
                        if(v_counter + <?php echo count($videos); ?> >= allowed_files) {
                          button.setAttribute("style","display:none");
                        }
                        changeButtomName();
                    }

                    var t_counter=0;
                    function new_tracks(){
                        div = document.getElementById("t_items");
                        button = document.getElementById("t_add");
                        t_counter++;
                        newitem = '<table width="100%">'+
                                    '<tr>'+
                                      '<td width="160px"">'+
                                        '<strong style="float:left">'+
                                          "<?php echo _VEHICLE_MANAGER_LABEL_TRACK_UPLOAD ?>"+t_counter+
                                        ': </strong></td>'+
                                      '<td width="400px">'+
                                        '<input style="float:left; width:100%" type="file"'+
                                              ' onClick="document.adminForm.new_upload_track'+t_counter+'" value =""'+
                                              ' onChange="refreshRandNumber_track('+t_counter+',this);"'+
                                              ' name="new_upload_track'+t_counter+'"'+
                                              ' id="new_upload_track'+t_counter+'" value="" size="45">'+
                                        '<span id="randNumTrack'+t_counter+'" style="color:red;"></span>'+
                                      '</td>'+
                                    '</tr>'+
                                    '<tr><td> OR </td><td></td></tr>'+
                                  '</table>';
                        newnode = document.createElement("span");
                        newnode.innerHTML = newitem;
                        div.insertBefore(newnode,button);

                        newitem = '<table width="100%">'+
                                    '<tr>'+
                                      '<td width="160px">'+
                                        '<strong>'+
                                          "<?php echo _VEHICLE_MANAGER_LABEL_TRACK_UPLOAD_URL; ?>"+t_counter+
                                        ': </strong></td>'+
                                      '<td width="400px"">'+
                                        '<input style="float:left; width:220px" type="text"'+
                                              ' name="new_upload_track_url'+t_counter+'"'+
                                              ' id="new_upload_track_url'+t_counter+'" value="" size="45">'+
                                      '</td>'+
                                    '</tr>'+
                                  '</table><br/>';
                        newnode = document.createElement("span");
                        newnode.innerHTML=newitem;
                        div.insertBefore(newnode,button);

                        newitem = '<table width="100%">'+
                                    '<tr>'+
                                      '<td width="160px">'+
                                        '<strong>'+
                                          "<?php echo _VEHICLE_MANAGER_LABEL_TRACK_UPLOAD_KIND; ?>"+t_counter+
                                        ':</strong>'+
                                      '</td>'+
                                      '<td width="400px">'+
                                        '<input style="float:left; width:220px" type="text"'+
                                              ' name="new_upload_track_kind'+t_counter+'"'+
                                              ' id="new_upload_track_kind'+t_counter+'" value="" size="45">'+
                                      '</td>'+
                                    '</tr>'+
                                  '</table><br/>';
                        newnode = document.createElement("span");
                        newnode.innerHTML=newitem;
                        div.insertBefore(newnode,button);

                        newitem = '<table width="100%">'+
                                    '<tr>'+
                                      '<td width="160px">'+
                                        '<strong>'+
                                          "<?php echo _VEHICLE_MANAGER_LABEL_TRACK_UPLOAD_SCRLANG; ?>"+t_counter+
                                        ':</strong>'+
                                      '</td>'+
                                      '<td width="400px">'+
                                        '<input style="float:left; width:220px" type="text"'+
                                              ' name="new_upload_track_scrlang'+t_counter+'"'+
                                              ' id="new_upload_track_scrlang'+t_counter+'" value="" size="45">'+
                                      '</td>'+
                                    '</tr>'+
                                  '</table><br/>';
                        newnode = document.createElement("span");
                        newnode.innerHTML = newitem;
                        div.insertBefore(newnode,button);

                        newitem = '<table width="100%">'+
                                    '<tr>'+
                                      '<td width="160px">'+
                                        '<strong>'+
                                          "<?php echo _VEHICLE_MANAGER_LABEL_TRACK_UPLOAD_LABEL; ?>"+t_counter+
                                        ':</strong>'+
                                      '</td>'+
                                      '<td width="400px">'+
                                        '<input style="float:left; width:220px" type="text"'+
                                              ' name="new_upload_track_label'+t_counter+'"'+
                                              ' id="new_upload_track_label'+t_counter+'" value="" size="45">'+
                                      '</td>'+
                                    '</tr>'+
                                  '</table><br/>';
                        newnode = document.createElement("span");
                        newnode.innerHTML=newitem;
                        div.insertBefore(newnode,button);
                    }

                    function isValidPrice(str){
                        myregexp = new RegExp("^[0-9]*[\.]?[0-9]{0,2}$");
                        mymatch = myregexp.exec(str);
                        if(str == ""){
                            return true;
                        }
                        if(mymatch != null){
                            return true;
                        }
                        return false;
                    }

                    function findPosY(obj) {
                        var curtop = 0;
                        if (obj.offsetParent) {
                            while (1) {
                                curtop+=obj.offsetTop;
                                if (!obj.offsetParent) {
                                    break;
                                }
                                obj=obj.offsetParent;
                            }
                        } else if (obj.y) {
                            curtop+=obj.y;
                        }
                        return curtop-20;
                    }
                    jQuerOs(document).ready(function() {
                      jQuerOs( "#price_from, #price_to" ).datepicker({
                        minDate: "+0",
                        dateFormat: "<?php echo transforDateFromPhpToJquery_vm();?>"
                      });
                      var form = document.adminForm;
                      jQuerOs(" #subPrice ").bind(" click ", function( event ) {
                        var rent_from = jQuerOs("#price_from").val();
                        var rent_to = jQuerOs("#price_to").val();
                        var special_price = jQuerOs("#special_price").val();
                        var comment_price = jQuerOs("#comment_price").val();
                        var currency_spacial_price = "<?php echo $row->priceunit; ?>";
                        var id = <?php echo (0 + $row->id);?> ;
                        if(id && id > 0){
                          if(isValidPrice(form.special_price.value)){
                            jQuerOs.ajax({
                              type: "POST",
                              url: "index.php?option=com_vehiclemanager&task=ajax_rent_price&vid="+id+
                                  "&rent_from="+rent_from+"&rent_until="+rent_to+
                                  "&special_price="+special_price+"&comment_price="+comment_price+
                                  "&currency_spacial_price="+currency_spacial_price,
                              data: { " #do " : " #1 " },
                              update: jQuerOs(" #SpecialPriseBlock "),
                              success: function( data ) {
                                jQuerOs("#SpecialPriseBlock").html(data);
                              }
                            });
                          }else{
                            window.scrollTo(0,findPosY(document.getElementById('sp_alert_scroll')));
                            document.getElementById('special_price').placeholder =
                              "<?php echo _VEHICLE_MANAGER_ADMIN_INFOTEXT_JS_EDIT_PRICE; ?>";
                            document.getElementById('special_price').style.color = "red";
                            return;
                          }
                        } else{
                          alert("<?php echo _VEHICLE_MANAGER_TO_ADD_SPRICE_YOU_NEED; ?>");
                        }
                      });
                    });
                  </script>
<!-- /////////////////////////////////////////END Special Price \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\ -->

              <table class="adminform adminform_08">
              <tr>
                  <td width="185"><?php echo _VEHICLE_MANAGER_LABEL_ISSUE_YEAR; ?>:
                  <?php if($vehiclemanager_configuration['mandatory_year_issue_field_require']==1) { ?>*<?php } ?>
                  </td>
                  <td>
                      <select name="year" id="year" class="inputbox" size="1" >
      <?php
      echo "<option value=''>";
      echo _VEHICLE_MANAGER_OPTION_SELECT;
      echo "</option>";
      //$num = 1900;
      //for ($i = 0; $num <= intval(date('Y')) + 1; $i++) {
      for ($i = intval(date('Y')) + 1; 1900 < $i; $i--) {
          echo "<option value=\"";
          echo $i;
          echo "\"";
          if ($i == $row->year)
          {
              echo " selected= \"true\" ";
          }
          echo ">";
          echo $i;
          echo "</option>";
          //$num++;
      }
      ?>
                      </select>
                  </td>
              </tr>
              <tr>
                  <td width="185"><?php echo _VEHICLE_MANAGER_LABEL_CONDITION_STATUS; ?>:</td>
                  <td><?php echo $condition_status_list; ?></td>
              </tr>
              <tr>
                  <td width="185"><?php echo _VEHICLE_MANAGER_LABEL_MILEAGE; ?>:
                    <?php if($vehiclemanager_configuration['mandatory_mileage_field_require']==1) { ?>*<?php } ?>
                  </td>
                  <td><input class="inputbox" type="text" id="mileage"  name="mileage" size="30" value="<?php echo $row->mileage; ?>" /></td>
              </tr>
              <tr>
                  <td width="185"><?php echo _VEHICLE_MANAGER_LABEL_LISTING_STATUS; ?>:</td>
                  <td><?php echo $listing_status_list; ?></td>
              </tr>
              <tr>
                  <td width="185"><?php echo _VEHICLE_MANAGER_LABEL_COMMENT; ?>:</td>
                  <td><?php editorArea('editor1', $row->description, 'description', 500, 250, '70', '10');?></td>
              </tr>
              <tr>
                  <td width="185"></td>
                  <td><span id="error_video"></span></td>
              </tr>
              <?php
              if($vehiclemanager_configuration['videos_tracks']['show']) {
                $out='';
                if (count($videos) > 0 && empty($youtube->code)) {
                  $out .= '<tr>'.
                            '<td colspan="2"></td>'.
                          '</tr>'.
                          '<tr>'.
                            '<td valign="top" align="left">'._VEHICLE_MANAGER_LABEL_VIDEO.':</td>'.
                          '</tr>';
                  for ($i = 0;$i < count($videos);$i++) {
                    $out .='<tr>' .
                              '<td>'._VEHICLE_MANAGER_LABEL_VIDEO_ATTRIBUTE.($i+1).':</td>'.
                            '<td>';
                          if(isset($videos[$i]->src) && substr($videos[$i]->src, 0, 4) != "http"
                            && empty($videos[$i]->youtube)){
                            $out .='<input type="text" name="video'.$i.'"'.
                                    ' id="video'.$i.'"' .
                                    ' size="60"'.
                                    ' value="'.$mosConfig_live_site . $videos[$i]->src.'"'.
                                    ' readonly="readonly" />';
                          }else{
                            $out .='<input type="text" name="video_url'.$i.'"'.
                                    ' id="video_url'.$i.'"'.
                                    ' size="60" value="'. $videos[$i]->src . '"'.
                                    ' readonly="readonly" />';
                          }
                $out .='</td>'.
                      '</tr>'.
                      '<tr>'.
                        '<td>'._VEHICLE_MANAGER_LABEL_VIDEO_DELETE . ':</td>'.
                        '<td>';
                          if(isset($videos[$i]->id))
                            $out .= '<input type="checkbox" name="video_option_del'. $videos[$i]->id .'"'.
                                       'value="' . $videos[$i]->id .'">'.
                        '</td>'.
                      '</tr>';
                  }
                } else if (!empty($youtube->code)) {
              $out .= '<tr>'.
                        '<td>'._VEHICLE_MANAGER_LABEL_VIDEO_ATTRIBUTE.':</td>'.
                        '<td>'.
                          '<input type="text"'.
                                  ' name="youtube_code'.$youtube->id.'"'.
                                  ' id="youtube_code'.$youtube->id.'"'.
                                  ' size="60" value="' . $youtube->code .'" />'.
                        '</td>'.
                      '</tr>'.
                      '<tr>'.
                        '<td>'._VEHICLE_MANAGER_LABEL_VIDEO_DELETE . ':</td>'.
                        '<td>'.
                          '<input type="checkbox"'.
                                ' name="youtube_option_del'.$youtube->id.'"'.
                                ' value="'.$youtube->id.'">'.
                        '</td>'.
                      '</tr>';
                }
              $out .= '<tr>';
              if(empty($youtube->code) && count($videos) < 5){
                if(count($videos) > 0)
                  $out .= '<td></td>';
                else
                  $out .= '<td>'._VEHICLE_MANAGER_LABEL_VIDEO.'</td>';
                    $out .= '<td id="v_items">'.
                              ' <input id="v_add" type="button"'.
                              ' name="new_video"'.
                              ' value="'._VEHICLE_MANAGER_LABEL_ADD_NEW_VIDEO_FILE.'"'.
                              ' onClick="new_videos()"/>'.
                            '</td>'.
                          '</tr>';
              }
                if (count($tracks) > 0) {
                  $out .= '<tr>'.
                            '<td colspan="2"></td>'.
                          '</tr>'.
                          '<tr>'.
                            '<td valign="top" align="left">'. _VEHICLE_MANAGER_LABEL_TRACK .':</td>'.
                          '</tr>';
                  for ($i = 0;$i < count($tracks);$i++) {
                    $out .='<tr>'.
                              '<td>' . _VEHICLE_MANAGER_LABEL_TRACK_UPLOAD_URL.($i+1).':</td>'.
                              '<td>';
                    if (isset($tracks[$i]->src) && substr($tracks[$i]->src, 0, 4) != "http"){
                      $out .='<input type="text"'.
                                  ' class="trackitems"'.
                                  ' size="60"'.
                                  ' value="'.$mosConfig_live_site.$tracks[$i]->src.'"'.
                                  ' readonly="readonly"/>';
                    }else{
                      $out .='<input type="text"'.
                                  ' class="trackitems"'.
                                  ' size="60"'.
                                  ' value="'.$tracks[$i]->src.'"'.
                                  ' readonly="readonly"/>';
                    }
                    if (!empty($tracks[$i]->kind))
                      $out .= '<input class="trackitems"'.
                                    ' type="text"'.
                                    ' size="60"'.
                                    ' value="'.$tracks[$i]->kind.'"'.
                                    ' readonly="readonly"/>';
                    if (!empty($tracks[$i]->scrlang))
                      $out .= '<input class="trackitems"'.
                                    ' type="text"'.
                                    ' size="60"'.
                                    ' value="'.$tracks[$i]->scrlang.'"'.
                                    ' readonly="readonly"/>';
                    if (!empty($tracks[$i]->label))
                      $out .= '<input class="trackitems"'.
                                    ' type="text"'.
                                    ' size="60"'.
                                    ' value="'.$tracks[$i]->label.'"'.
                                    ' readonly="readonly"/>';
                    $out .= '</td>'.
                          '</tr>'.
                          '<tr>'.
                            '<td>'._VEHICLE_MANAGER_LABEL_TRACK_DELETE.':</td>'.
                            '<td>';
                            if(isset($tracks[$i]->id))
                      $out .=  '<input type="checkbox"'.
                                      ' name="track_option_del'.$tracks[$i]->id.'"'.
                                      ' value="'.$tracks[$i]->id .'">';
                  }
                $out .= '<tr>';
                if(count($tracks) > 0)
                  $out .= '<td></td>';
                else
                  $out .= '<td>'._VEHICLE_MANAGER_LABEL_TRACK.'</td>';
                    $out .= '<td id="t_items">'.
                              ' <input id="t_add" type="button"'.
                              ' name="new_track"'.
                              ' value="'._VEHICLE_MANAGER_LABEL_ADD_NEW_TRACK.'"'.
                              ' onClick="new_tracks()"/>'.
                            '</td>'.
                          '</tr>';
                }else{
                  $out .='<tr>'.
                          '<td>'._VEHICLE_MANAGER_LABEL_TRACK.':</td>'.
                          '<td id="t_items">'.
                            '<input id="t_add" type="button" name="new_track"'.
                                    ' value="'._VEHICLE_MANAGER_LABEL_ADD_NEW_TRACK.'"'.
                                    ' onClick="new_tracks()"/>'.
                          '</td>'.
                        '</tr>';
                }
                echo $out;
              } ?>
          </table>
          <h2><?php echo _VEHICLE_MANAGER_LABEL_FEATURED_MANAGER_FEATURE_MANAGER; ?></h2>
          <table class="adminform adminform_09">
              <tr>
                  <td colspan="2" id="checkboxes_veh">
              <?php

             for($i = 0; $i < count($vehicle_feature); $i++)
              {

                  if ($i== 0 || $vehicle_feature[$i]->categories !== $vehicle_feature[$i - 1]->categories )
                      echo "<strong>" . constant('_VEHICLE_MANAGER_FEATURE_CATEGORY_'.trim($vehicle_feature[$i]->categories) ) . "</strong>";

                  if($vehicle_feature[$i]->name): ?>
                      <div class="checkbox_vm">
                          <input type="checkbox" class="checkbox_veh" id="checkbox_addveh<?php echo $i; ?>" <?php if ($vehicle_feature[$i]->check) echo "checked"; ?> name="feature[]" value="<?php echo $vehicle_feature[$i]->id; ?>">
                      <?php if ($vehicle_feature[$i]->image_link != ''){ ?>
                        <img alt="photo" src="<?php echo "$mosConfig_live_site/components/com_vehiclemanager/featured_ico/" . $vehicle_feature[$i]->image_link; ?>"></img>
                    <?php } ?>
                          <label for="checkbox_addveh<?php echo $i; ?>"><?php echo constant('_VEHICLE_MANAGER_FEATURE'.trim($vehicle_feature[$i]->id) ); ?></label>
                      </div>
                      <?php endif; ?>
              <?php } ?>
                  </td>
              </tr>

          </table>

                  <?php
                  if ($vehiclemanager_configuration['extra1'] == 0 && $vehiclemanager_configuration['extra2'] == 0 && $vehiclemanager_configuration['extra3'] == 0 && $vehiclemanager_configuration['extra4'] == 0 && $vehiclemanager_configuration['extra5'] == 0
                          && $vehiclemanager_configuration['extra6'] == 0 && $vehiclemanager_configuration['extra7'] == 0 && $vehiclemanager_configuration['extra8'] == 0 && $vehiclemanager_configuration['extra9'] == 0 && $vehiclemanager_configuration['extra10'] == 0)
                  {

                  } else
                  {
                  ?>
              <table class="adminform adminform_11">
                  <tr>
                      <td colspan="2"><h2><?php echo _VEHICLE_MANAGER_LABEL_EXTRA; ?></h2></td>
                  </tr>
                          <?php if ($vehiclemanager_configuration['extra1'] == 1)
                          {
                              ?>
                      <tr>
                          <td width="185"><?php echo _VEHICLE_MANAGER_LABEL_EXTRA1; ?>:</td>
                          <td><input class="inputbox" type="text" name="extra1" size="30" value="<?php echo $row->extra1; ?>" /></td>
                      </tr>
              <?php
          }
          if ($vehiclemanager_configuration['extra2'] == 1) { ?>
                      <tr>
                          <td width="185"><?php echo _VEHICLE_MANAGER_LABEL_EXTRA2; ?>:</td>
                          <td><input class="inputbox" type="text" name="extra2" size="30" value="<?php echo $row->extra2; ?>" /></td>
                      </tr>
          <?php
          }
          if ($vehiclemanager_configuration['extra3'] == 1)
          {
              ?>
                      <tr>
                          <td width="185"><?php echo _VEHICLE_MANAGER_LABEL_EXTRA3; ?>:</td>
                          <td><input class="inputbox" type="text" name="extra3" size="30" value="<?php echo $row->extra3; ?>" /></td>
                      </tr>
              <?php
          }
          if ($vehiclemanager_configuration['extra4'] == 1)
          {
              ?>
                      <tr>
                          <td width="185"><?php echo _VEHICLE_MANAGER_LABEL_EXTRA4; ?>:</td>
                          <td><input class="inputbox" type="text" name="extra4" size="30" value="<?php echo $row->extra4; ?>" /></td>
                      </tr>
              <?php
          }
          if ($vehiclemanager_configuration['extra5'] == 1)
          {
              ?>
                      <tr>
                          <td width="185"><?php echo _VEHICLE_MANAGER_LABEL_EXTRA5; ?>:</td>
                          <td><input class="inputbox" type="text" name="extra5" size="30" value="<?php echo $row->extra5; ?>" /></td>
                      </tr>
              <?php
          }
          if ($vehiclemanager_configuration['extra6'] == 1)
          {
              ?>
                      <tr>
                          <td width="185"><?php echo _VEHICLE_MANAGER_LABEL_EXTRA6; ?>:</td>
                          <td><?php echo $extra_list[0]; ?></td>
                      </tr>
              <?php
          }
          if ($vehiclemanager_configuration['extra7'] == 1)
          {
              ?>
                      <tr>
                          <td width="185"><?php echo _VEHICLE_MANAGER_LABEL_EXTRA7; ?>:</td>
                          <td><?php echo $extra_list[1]; ?></td>
                      </tr>
                  <?php
              }
              if ($vehiclemanager_configuration['extra8'] == 1)
              {
                  ?>
                      <tr>
                          <td width="185"><?php echo _VEHICLE_MANAGER_LABEL_EXTRA8; ?>:</td>
                          <td><?php echo $extra_list[2]; ?></td>
                      </tr>
                  <?php
              }
              if ($vehiclemanager_configuration['extra9'] == 1)
              {
                  ?>
                      <tr>
                          <td width="185"><?php echo _VEHICLE_MANAGER_LABEL_EXTRA9; ?>:</td>
                          <td><?php echo $extra_list[3]; ?></td>
                      </tr>
              <?php
          }
          if ($vehiclemanager_configuration['extra10'] == 1)
          {
              ?>
                      <tr>
                          <td width="185"><?php echo _VEHICLE_MANAGER_LABEL_EXTRA10; ?>:</td>
                          <td><?php echo $extra_list[4]; ?></td>
                      </tr>
          <?php } ?>
              </table>
                      <?php } ?>
      <?php
      if ($reviews > false) /* show, if review exist */
      {
        ?>
        <h2><?php echo _VEHICLE_MANAGER_LABEL_REVIEWS; ?>:</h2>
        <table class="adminlist adminlist_08">
          <tr class="row0">
            <td width="4%" valign="top" align="center"><div>#</div></td>
            <td width="4%" valign="top" align="center"><div></div></td>
            <td width="13%" valign="top" align="center"><?php echo _VEHICLE_MANAGER_LABEL_REVIEW_TITLE; ?>:</td>
            <td width="13%" valign="top" align="center"><?php echo _VEHICLE_MANAGER_LABEL_RENT_USER; ?>:</td>
            <td width="40%" valign="top" align="center"><?php echo _VEHICLE_MANAGER_LABEL_REVIEW_COMMENT; ?>:</td>
            <td width="13%" valign="top" align="center"><?php echo _VEHICLE_MANAGER_REVIEW_DATE; ?>:</td>
            <td width="13%" valign="top" align="center"><?php echo _VEHICLE_MANAGER_LABEL_REVIEW_RATING; ?>:</td>
          </tr>
          <?php for ($i = 0, $nn = 1; $i < count($reviews); $i++, $nn++) /* if not one comment */ {
            ?>
            <tr class="row0">
              <td valign="top" align="center"><div><?php echo $nn; ?></div></td>
              <td valign="top" align="center"><div><?php echo "<input type='radio' id='cb" . $i . "' name='vid[]' value='" . $row->id . "," . $reviews[$i]->id . "' onClick='Joomla.isChecked(this.checked);' />"; ?></div></td>
              <td valign="top" align="center"><div><?php print_r($reviews[$i]->title); ?></div></td>
              <td valign="top" align="left"><div><?php print_r($reviews[$i]->user_name); ?></div></td>
              <td valign="top" align="left"><div><?php print_r(strip_tags($reviews[$i]->comment)); ?></div></td>
              <td valign="top" align="left"><div><?php print_r($reviews[$i]->date); ?></div></td>
              <td valign="top" align="left"><div><img src="../components/com_vehiclemanager/images/rating-<?php echo $reviews[$i]->rating; ?>.png" alt="<?php echo ($reviews[$i]->rating) / 2; ?>" border="0" align="right"/>&nbsp;</div></td>
            </tr>
            <?php }/* end for(...) */ ?>

          </table>
          <?php }/* end if(...) */ ?>

      <?php

          echo JHtml::_('bootstrap.endTab');
          echo JHtml::_('bootstrap.addTab', 'addVehicle', 'panel_2_addVehicle', _VEHICLE_MANAGER_ADD_VEHICLE_TAB_LABEL_SPECIFICATIONS );            

      ?>
<div style="clear: both;"></div>
          <table class="adminform adminform_12">
              <tr>
                  <td width="185"><?php echo _VEHICLE_MANAGER_LABEL_ENGINE_TYPE; ?>:</td>
                  <td><input class="inputbox" type="text" name="engine" size="80" value="<?php echo $row->engine; ?>" /></td>
              </tr>
              <tr>
                  <td width="185"><?php echo _VEHICLE_MANAGER_LABEL_TRANSMISSION_TYPE; ?>:</td>
                  <td><?php echo $transmission_type_list; ?></td>
              </tr>
              <tr>
                  <td width="185"><?php echo _VEHICLE_MANAGER_LABEL_DRIVE_TYPE; ?>:</td>
                  <td><?php echo $drive_type_list; ?></td>
              </tr>
              <tr>
                  <td width="185"><?php echo _VEHICLE_MANAGER_LABEL_NUMBER_CYLINDERS; ?>:</td>
                  <td><?php echo $num_cylinder_list; ?></td>
              </tr>
              <tr>
                  <td width="185"><?php echo _VEHICLE_MANAGER_LABEL_NUMBER_SPEEDS; ?>:</td>
                  <td><?php echo $num_speed_list; ?></td>
              </tr>
              <tr>
                  <td width="185"><?php echo _VEHICLE_MANAGER_LABEL_FUEL_TYPE; ?>:</td>
                  <td><?php echo $fuel_type_list; ?></td>
              </tr>
              <tr>
                  <td width="185"><?php echo _VEHICLE_MANAGER_LABEL_CITY_FUEL_MPG; ?>:</td>
                  <td><input class="inputbox" type="text" name="city_fuel_mpg" size="30" value="<?php echo $row->city_fuel_mpg; ?>" />                </td>
              </tr>
              <tr>
                  <td width="185"><?php echo _VEHICLE_MANAGER_LABEL_HIGHWAY_FUEL_MPG; ?>:</td>
                  <td><input class="inputbox" type="text" name="highway_fuel_mpg" size="30" value="<?php echo $row->highway_fuel_mpg; ?>" />
                  </td>
              </tr>
              <tr>
                  <td width="185"><?php echo _VEHICLE_MANAGER_LABEL_WHEELBASE; ?>:</td>
                  <td><input class="inputbox" type="text" name="wheelbase" size="20" value="<?php echo $row->wheelbase; ?>" /></td>
              </tr>
              <tr>
                  <td width="185"><?php echo _VEHICLE_MANAGER_LABEL_WHEELTYPE; ?>:</td>
                  <td><input class="inputbox" type="text" name="wheeltype" size="80" value="<?php echo $row->wheeltype; ?>" /></td>
              </tr>
              <tr>
                  <td width="185"><?php echo _VEHICLE_MANAGER_LABEL_REARAXE_TYPE; ?>:</td>
                  <td><input class="inputbox" type="text" name="rear_axe_type" size="80" value="<?php echo $row->rear_axe_type; ?>" /></td>
              </tr>
              <tr>
                  <td width="185"><?php echo _VEHICLE_MANAGER_LABEL_BRAKES_TYPE; ?>:</td>
                  <td><input class="inputbox" type="text" name="brakes_type" size="80" value="<?php echo $row->brakes_type; ?>" /></td>
              </tr>
          </table>
          <table class="adminform adminform_13">
              <tr>
                  <td colspan="2"><h2><?php echo _VEHICLE_MANAGER_HEADER_EXTERIOR_OPTIONS ?></h2></td>
              </tr>
              <tr>
                  <td width="185"><?php echo _VEHICLE_MANAGER_LABEL_EXTERIOR_COLORS; ?>:</td>
                  <td><input class="inputbox" type="text" name="exterior_color" size="30" value="<?php echo $row->exterior_color; ?>" /></td>
              </tr>
              <tr>
                  <td width="185"><?php echo _VEHICLE_MANAGER_LABEL_NUMBER_DOORS; ?>:</td>
                  <td><?php echo $num_doors_list; ?></td>
              </tr>
              <tr>
                  <td width="185"><?php echo _VEHICLE_MANAGER_LABEL_EXTERIOR_EXTRAS; ?>:</td>
                  <td><textarea name="exterior_amenities" cols="50" rows="8" ><?php echo $row->exterior_amenities; ?></textarea></td>
              </tr>
              <tr>
                  <td colspan="2"><h2><?php echo _VEHICLE_MANAGER_HEADER_INTERIOR_OPTIONS ?></h2></td>
              </tr>
              <tr>
                  <td width="185"><?php echo _VEHICLE_MANAGER_LABEL_INTERIOR_COLORS; ?>:</td>
                  <td><input class="inputbox" type="text" name="interior_color" size="30" value="<?php echo $row->interior_color; ?>" /></td>
              </tr>
              <tr>
                  <td width="185"><?php echo _VEHICLE_MANAGER_LABEL_NUMBER_SEATINGS; ?>:</td>
                  <td><input class="inputbox" type="text" name="seating" size="30" value="<?php echo $row->seating; ?>" /></td>
              </tr>
              <tr>
                  <td width="185"><?php echo _VEHICLE_MANAGER_LABEL_DASHBOARD_OPTIONS; ?>:</td>
                  <td>
                      <textarea name="dashboard_options" cols="50" rows="8"><?php echo $row->dashboard_options; ?></textarea>
                  </td>
              </tr>
              <tr>
                  <td width="185"><?php echo _VEHICLE_MANAGER_LABEL_INTERIOR_EXTRAS; ?>:</td>
                  <td><textarea name="interior_amenities" cols="50" rows="8" ><?php echo $row->interior_amenities; ?></textarea></td>
              </tr>
          </table>
      <?php

          echo JHtml::_('bootstrap.endTab');
          echo JHtml::_('bootstrap.addTab', 'addVehicle', 'panel_3_addVehicle', _VEHICLE_MANAGER_ADD_VEHICLE_TAB_LABEL_PHOTOS_AND_DOCUMENETS );            
      ?>
<div style="clear: both;"></div>
          <h2><?php echo _VEHICLE_MANAGER_HEADER_PHOTO_MANAGE ; ?></h2>
          <table class="adminform adminform_14">
              <tr>
                  <td width="185"><?php echo _VEHICLE_MANAGER_LABEL_PICTURE_URL_UPLOAD; ?>:</td>
                  <td><input class="inputbox" type="file" name="image_link" value="<?php echo $row->image_link; ?>" size="50" maxlength="250" /></td>
              </tr>
              <tr>
          <?php
          if ($row->image_link != '' and !$row->id_true)
              echo '<td valign="bottom" align="right">'.
                _VEHICLE_MANAGER_LABEL_FEATURED_MANAGER_REMOVE.':</td>';
          else
              echo '<td>&nbsp</td>';
          ?>

                  <td>
                      <?php
                      if ($row->image_link != '')
                      {
                          $main_phot = pathinfo($row->image_link);
                          $main_photo_type = '.' . $main_phot['extension'];
                          $main_photo_name = basename($row->image_link, $main_photo_type);
                      } else
                      {
                          echo 'The main image is absent';
                          $main_photo_name = '';
                          $main_photo_type = '';
                      }
                      if ($vehicle_photo != '')
                      {
                          if(!$row->id_true)
                              echo '<input type="checkbox" name="del_main_photo" value="' . $vehicle_photo[0] . '" />';
                          ?>
                          <img alt="photo" src="<?php echo $mosConfig_live_site; ?>/components/com_vehiclemanager/photos/<?php echo $vehicle_photo[1]; ?>"/>
      <?php } ?>
                  </td>
              </tr>

      <?php if ( is_array($vehicle_photos) &&  count($vehicle_photos) != 0){ ?>
                <tr>
              <td width="185"><?php echo _VEHICLE_MANAGER_TEXT_PHOTOS_TO_REMOVE;
                        ?>:</td>
              <td>
                <div id="veh_img_sortable">
                  <?php
                  for ($i = 0; $i < count($vehicle_photos); $i++) { ?>
                    <div id="<?php echo $vehicle_temp_photos[$i]->main_img;?>" class="sortable_image">
                      <input type="checkbox" name="del_photos[]"
                        value="<?php echo $vehicle_photos[$i][0]; ?>" />
                      <img src="<?php echo $mosConfig_live_site;
                        ?>/components/com_vehiclemanager/photos/<?php
                        echo $vehicle_photos[$i][1]; ?>" alt="no such file"/>
                    </div>
                    <?php
                  } ?>
                </div>
                <input id="veh_img_ordering" type="hidden" name="veh_img_ordering" value="1">
                <script type="text/javascript">
                  jQuerOs( "#veh_img_sortable" ).sortable({
                      scroll: false,
                      'update': function (event, ui) {
                      var order = jQuerOs(this).sortable('toArray');
                        jQuerOs( "#veh_img_ordering" ).val(order);
                      }
                  });
                </script>
              </td>
            </tr>
            <?php
          } ?>
              <tr>
                  <td width="185"> <?php echo _VEHICLE_MANAGER_LABEL_OTHER_PICTURES_URL_UPLOAD; ?>:</td>
                  <td>
                      <div ID="items">
                          <input class="inputbox" type="button" name="new_photo"
                            value="<?php echo _VEHICLE_MANAGER_BUTTON_NEW_PICTURES_UPLOAD;
                             ?>" onClick="javascript:new_photos()" ID="add"/>
                      </div>
                  </td>
              </tr>
          </table>
          <h2><?php echo _VEHICLE_MANAGER_ADD_VEHICLE_TAB_LABEL_ATTACHMENT_DOCUMENTS; ?></h2>
          <table class="adminform adminform_15">
      <?php if ($vehiclemanager_configuration['edocs']['allow'])
      {
          ?>
                  <tr>
                      <td width="185"><?php echo _VEHICLE_MANAGER_LABEL_EDOCUMENT_UPLOAD; ?>:</td>
                      <td><input class="inputbox" type="file" name="edoc_file" value="" size="50" maxlength="250" onClick="document.adminForm.edok_link.value ='';"/>    <!-- //+ --></td>
                  </tr>
                  <tr>
                      <td width="185"><?php echo _VEHICLE_MANAGER_LABEL_EDOCUMENT_UPLOAD_URL; ?>:</td>
                      <td><input class="inputbox" type="text" name="edocument_Link" value="<?php echo $row->edok_link; ?>" size="50" maxlength="250"/></td>
                  </tr>
          <?php
      }
      if (strlen($row->edok_link) > 0 and !$row->id_true)
      {
          ?>
                  <tr>
                      <td width="185"><?php echo _VEHICLE_MANAGER_LABEL_EDOCUMENT_DELETE; ?>:</td>
                      <td><?php echo $delete_edoc; ?></td>
                  </tr>
      <?php } ?>
          </table>
      <?php
          echo JHtml::_('bootstrap.endTab');
          echo JHtml::_('bootstrap.addTab', 'addVehicle', 'panel_4_addVehicle', _VEHICLE_MANAGER_ADD_VEHICLE_TAB_LABEL_ADDITIONAL_INFO );      
      ?>
<div style="clear: both;"></div>
          <table class="adminform adminform_16">
              <tr>
                  <td width="185"><?php echo _VEHICLE_MANAGER_LABEL_SAFETY_OPTIONS; ?>:
                  <td><textarea name="safety_options" cols="50" rows="8"><?php echo $row->safety_options; ?></textarea></td>
                </tr>
          </table>
          <h2><?php echo _VEHICLE_MANAGER_LABEL_WARRANTY_OPTIONS; ?></h2>
          <table class="adminform adminform_17">
              <tr>
                  <td width="185"><?php echo _VEHICLE_MANAGER_LABEL_WARRANTY_BASIC; ?>:</td>
                  <td><input class="inputbox" type="text" name="w_basic" size="30" value="<?php echo $row->w_basic; ?>" /></td>
              </tr>
              <tr>
                  <td width="185"><?php echo _VEHICLE_MANAGER_LABEL_WARRANTY_DRIVETRAIN; ?>:</td>
                  <td><input class="inputbox" type="text" name="w_drivetrain" size="30" value="<?php echo $row->w_drivetrain; ?>" /></td>
              </tr>
              <tr>
                  <td width="185"><?php echo _VEHICLE_MANAGER_LABEL_WARRANTY_CORROSION; ?>:</td>
                  <td><input class="inputbox" type="text" name="w_corrosion" size="30" value="<?php echo $row->w_corrosion; ?>" /></td>
              </tr>
              <tr>
                  <td width="185"><?php echo _VEHICLE_MANAGER_LABEL_WARRANTY_ROADSIDE_ASSISTANCE; ?>:</td>
                  <td><input class="inputbox" type="text" name="w_roadside_ass" size="30" value="<?php echo $row->w_roadside_ass; ?>" /></td>
              </tr>
          </table>
          <h2><?php echo _VEHICLE_MANAGER_HEADER_ADVERTISMENT; ?></h2>
          <table class="adminform adminform_18">
              <tr>
                  <td width="185"><?php echo _VEHICLE_MANAGER_LABEL_FEATURED_CLICKS; ?>:</td>
                  <td><input class="inputbox" type="text" name="featured_clicks" size="30" value="<?php echo $row->featured_clicks; ?>" /></td>
              </tr>
              <tr>
                  <td width="185"><?php echo _VEHICLE_MANAGER_LABEL_FEATURED_SHOWS; ?>:</td>
                  <td><input class="inputbox" type="text" name="featured_shows" size="30" value="<?php echo $row->featured_shows; ?>" /></td>
              </tr>
          </table>
      <?php
          echo JHtml::_('bootstrap.endTab');
          echo JHtml::_('bootstrap.addTab', 'addVehicle', 'panel_5_addVehicle', _VEHICLE_MANAGER_ADD_VEHICLE_TAB_LABEL_SELLER_CONTACTS );      
      ?>
<div style="clear: both;"></div>
          <table class="adminform adminform_19">
              <tr>
                  <td width="185"><?php echo _VEHICLE_MANAGER_LABEL_CONTACTS; ?>:</td>
                  <td><input class="inputbox" type="text" name="contacts" size="30" value="<?php echo $row->contacts; ?>" /></td>
              </tr>
          </table>
               <table class="adminform adminform_20">
              <tr>
                <td width="185"><?php echo _VEHICLE_MANAGER_LABEL_OWNER_ID; ?>:</td>
                <td><input class="inputbox" type="text" name="owner_id" id="owner_id" size="20" maxlength="20" value="<?php echo $row->owner_id; ?>" /></td>
              </tr>
              <tr>
                  <td width="185"><?php echo _VEHICLE_MANAGER_LABEL_OWNER; ?>:</td>
                  <td>
                  <?php //if (trim($row->owneremail) != ""): ?>
                      <?php echo $row->getOwnerUsername(), '(', $owner_email, ')'; ?>
                  <?php //endif; ?>
                  </td>
              </tr>
              <tr>
                <td><?php echo _VEHICLE_MANAGER_LABEL_OWNER_CUSTOM_EMAIL; ?>:</td>
                <td>
                  <?php if (trim($row->owneremail) != ""): ?>
                    <input type='text' name='owneremail' value="<?php echo $owner_email; ?>"/>
                  <?php else: ?>
                    <input type='text' name='owneremail' value="<?php echo $my->email; ?>"/>
                  <?php endif; ?>
                </td>
              </tr>
          </table>
          <h2><?php echo _VEHICLE_MANAGER_HEADER_ADDRESS_FIELDS; ?></h2>
          <table class="adminform adminform_21">
              <tr>
                  <td width="185"><?php echo _VEHICLE_MANAGER_LABEL_ADDRESS; ?>:</td>
                  <td><input class="inputbox" type="text" id="vlocation" name="vlocation" size="80" value="<?php echo $row->vlocation; ?>" /></td>
              </tr>
              <?php if($vehiclemanager_configuration['show_country_region_city_as_text_field']==1) { ?>
              <tr>

                <td width="185"><?php echo _VEHICLE_MANAGER_LABEL_COUNTRY; ?>:</td>
                <td><input class="inputbox" type="text" id="country" name="country" size="80" value="<?php echo $row->country; ?>" /></td>
              </tr>
              <tr>
                <td width="185"><?php echo _VEHICLE_MANAGER_LABEL_REGION; ?>:</td>
                <td><input class="inputbox" type="text" id="region" name="region" size="80" value="<?php echo $row->region; ?>" /></td>
              </tr>
              <tr>
                  <td width="185"><?php echo _VEHICLE_MANAGER_LABEL_CITY; ?>:</td>
                  <td><input class="inputbox" type="text" id="city" name="city" size="80" value="<?php echo $row->city; ?>" /></td>
              </tr>
              <?php } else{ ?>
              <tr>
                <td width="185"><?php echo _VEHICLE_MANAGER_LABEL_COUNTRY; ?>:</td>
                <td><?php echo $country; ?></td>
              </tr>
              <tr>
                <td width="185"><?php echo _VEHICLE_MANAGER_LABEL_REGION; ?>:</td>
                <td><?php echo $region; ?></td>
              </tr>
              <tr>
                <td width="185"><?php echo _VEHICLE_MANAGER_LABEL_CITY; ?>:</td>
                <td><?php echo $city; ?></td>
              </tr>
              <?php } ?>

<script type="text/javascript">
      <?php

      $countrys=$arr[2];
      $regions=$arr[3];
      $regs=$arr[4];
      $citys=$arr[5];

      echo 'var countrysregions = [];';

      for($c=0; $c<count($countrys); $c++){
        $countrys[$c]='\''.$countrys[$c].'\'';
        foreach($regions[$c] as $temp=>$region)
          $regions[$c][$temp]='\''.$region.'\'';
        echo 'var temp=new Array("'._VEHICLE_MANAGER_ADMIN_PLEASE_SEL.'",'.implode(',',$regions[$c]).");\n";
        echo 'countrysregions['.$c."]=temp;\n";
      }
      echo 'var countrys=new Array('.implode(',',$countrys).');';

      echo 'var regionscitys = [];';

      for($c=0; $c<count($regs); $c++){
        $regs[$c]='\''.$regs[$c].'\'';
        foreach($citys[$c] as $temp=>$city)
          $citys[$c][$temp]='\''.$city.'\'';
        echo 'var temp=new Array("'._VEHICLE_MANAGER_ADMIN_PLEASE_SEL.'",'.implode(',',$citys[$c]).");\n";
        echo 'regionscitys['.$c."]=temp;\n";
      }
      echo 'var regs=new Array('.implode(',',$regs).');';?>

      function vm_changedCountry(country){
        var id = vm_in_array(country.value,countrys);
        var region = document.getElementsByName('region')[0];

        if(typeof(region) == 'undefined'){
          return;
        }

        vm_clearSelectList('region');
        vm_clearSelectList('city');
        vm_setAllselect('city');

        if(country.value=='<?php echo '';?>'){
          vm_setAllselect('region');

          //clear city select if country set 'all'
          //vm_clearSelectList('city');
          //vm_setAllselect('city');
          return;
        }

        for(var c=0;c<countrysregions[id].length;c++){
            if(countrysregions[id][c] == '<?php echo _VEHICLE_MANAGER_ADMIN_PLEASE_SEL ?>'){
                vm_createOptionList('region', '',countrysregions[id][c]);
            }else{
                vm_createOptionList('region', countrysregions[id][c],countrysregions[id][c]);
            }
        }
      }
      //end region

      //citys
      function vm_changedRegion(region){
        var id = vm_in_array(region.value,regs);
        var city = document.getElementsByName('city')[0];

        if(typeof(city) == 'undefined'){
          return;
        }

        vm_clearSelectList('city');

        if(region.value=='<?php echo '';?>'){
          vm_setAllselect('city');
          return;
        }

        for(var c=0;c<regionscitys[id].length;c++){
            if(regionscitys[id][c] == '<?php echo _VEHICLE_MANAGER_ADMIN_PLEASE_SEL ?>'){
                vm_createOptionList('city', '',regionscitys[id][c]);
            }else{
                vm_createOptionList('city', regionscitys[id][c],regionscitys[id][c]);
            }
        }
      }
      //end city

      function vm_clearSelectList(elementName){
        var objSelect=document.getElementsByName(elementName)[0];
        while(objSelect.options.length > 0){objSelect.remove(0);}
        return objSelect;
      }

      function vm_in_array(what, where) {
        for(var i=0; i<where.length; i++)
          if(what == where[i])
            return i;
        return false;
      }

      function vm_createOptionList(elementName, newValue,newText){
        var objSelect = document.getElementsByName(elementName)[0];
        var objOption = document.createElement("option");
        objOption.text = newText
        objOption.value = newValue
        if(document.all && !window.opera){
          objSelect.add(objOption);
        }else{
          objSelect.add(objOption, null);
        };
      }

      function vm_setAllselect(elementName){
        vm_createOptionList(elementName, '<?php echo '';?>','<?php echo _VEHICLE_MANAGER_ADMIN_PLEASE_SEL;?>');
      }

      </script>



              <tr>
                  <td width="185"><?php echo _VEHICLE_MANAGER_LABEL_DISTRICT; ?>:</td>
                  <td><input class="inputbox" type="text" name="district" size="80" value="<?php echo $row->district; ?>" /></td>
              </tr>
              <tr>
                  <td width="185"><?php echo _VEHICLE_MANAGER_LABEL_ZIPCODE; ?>:</td>
                  <td><input class="inputbox" type="text" id="zipcode" name="zipcode" size="80" value="<?php echo $row->zipcode; ?>" /></td>
              </tr>
              <tr>
                  <td width="185"><?php echo _VEHICLE_MANAGER_LABEL_GEOCOOR; ?></td>
                  <td> <input type="button" value="<?php echo _VEHICLE_MANAGER_BUTTON_SHOW_ADDRESS; ?>" onclick="codeAddress()"></td>
              </tr>
              <tr>
                  <td  width="185"><?php echo _VEHICLE_MANAGER_LABEL_CLICKMAP; ?></td>
                  <td>
                      <div id="map_canvas" class="vm_map_canvas_admin"></div>

        <?php
            if( $vehiclemanager_configuration['google_openmap']['show'] == 0 ){
            //show open map
        ?>

                <!--Image Open map-->
            <?php
              $doc->addScript("//cdn.polyfill.io/v2/polyfill.min.js?features=requestAnimationFrame,Element.prototype.classList,URL");
              // $doc->addScript("//openlayers.org/en/v4.6.5/build/ol.js");
              // $doc->addStyleSheet("//openlayers.org/en/v4.6.5/css/ol.css");
              $doc->addScript("//cdnjs.cloudflare.com/ajax/libs/ol3/4.6.5/ol.js");
              $doc->addStyleSheet("//cdnjs.cloudflare.com/ajax/libs/ol3/4.6.5/ol.css");
             ?>

            <script type="text/javascript">
                var map ;
                var image_point_src;
                var map_layer ;
                var pointFeature ;
                var ol_point ;

                setTimeout(function() {
                    vm_initialize();
                },20);

                function vm_initialize(){

                    //if map already created: refresh
                  if(map instanceof ol.Map ){
                    map.updateSize();
                    return ;
                  }

                  var imgCatalogPath = "<?php echo $mosConfig_live_site; ?>/components/com_vehiclemanager/";

                  image_point_src = imgCatalogPath + "/images/marker-2.png";

                  //var london = ol.proj.fromLonLat([-0.12755, 51.507222]);
                  ol_point = ol.proj.fromLonLat([<?php
                      if ($row->vlongitude && $row->vlongitude != '')
                        echo $row->vlongitude;
                      else
                        echo '-0.12755';
                      ?>,<?php
                      if ($row->vlatitude && $row->vlatitude != '')
                        echo $row->vlatitude;
                      else
                        echo '51.507222';
                      ?>]);

                  var zoom = <?php if ($row->map_zoom) echo $row->map_zoom; else echo 8; ?>;

                  var map_view = new ol.View({
                    center: ol_point,
                    zoom: zoom
                  });

                  map = new ol.Map({
                    layers: [
                      new ol.layer.Tile({
                        source: new ol.source.OSM()
                      })
                    ],
                    target: 'map_canvas',
                    controls: ol.control.defaults({
                      attributionOptions: {
                        collapsible: false
                      }
                    }),
                    view: map_view
                  });
            <?php
                  if ($row->vlatitude && $row->vlatitude != ''){
            ?>
                      pointFeature = new ol.Feature(new ol.geom.Point(ol_point)) ;
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
                            })
                          });

                       map.addLayer(map_layer);
                       map.getView().setCenter( ol_point );

            <?php
                  }
            ?>


                    //If the zoom, then store it in the field map_zoom
                    map.getView().on('change:resolution', function(evt) {
                        document.getElementById("map_zoom").value=map.getView().getZoom();
                    });


                    map.on('singleclick', function(evt) {

                        //here you can add you code to display the coordinates or whatever you want to do
                        var coordinate = evt.coordinate;
                        pointFeature = new ol.Feature(new ol.geom.Point(coordinate)) ;
                //            var hdms = ol.coordinate.toStringHDMS(ol.proj.transform(
                //                 coordinate, 'EPSG:3857', 'EPSG:4326'));
                //        console.log("aaaaaaaaaaaaa1:",coordinate ) ;
                          map.removeLayer(map_layer);

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
                                })
                              });
                          log_lat = ol.proj.toLonLat(coordinate)
                          document.getElementById("vlongitude").value=parseFloat(log_lat[0]);
                          document.getElementById("vlatitude").value=parseFloat(log_lat[1]);


                           map.addLayer(map_layer);
                           map.getView().setCenter( coordinate );


                           //overlay.setPosition(coordinate);

                    });



                }

                function updateCoordinates(latlng)
                {
                    if(latlng.length > 0 )
                    {
                        document.getElementById('vlatitude').value = latlng[0].lat;
                        document.getElementById('vlongitude').value = latlng[0].lon;
                        document.getElementById("map_zoom").value=map.getView().getZoom();

                        //here you can add you code to display the coordinates or whatever you want to do
                        //console.log("1111111111111111:",[latlng[0].lon,latlng[0].lat] ) ;
                        ol_point = ol.proj.fromLonLat([ parseFloat(latlng[0].lon),parseFloat(latlng[0].lat) ]);
                        pointFeature = new ol.Feature(new ol.geom.Point(ol_point) ) ;

                        map.removeLayer(map_layer);

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
                            })
                          });

                        map.addLayer(map_layer)
                        map.getView().setCenter( ol_point );
                    }
                }


                function codeAddress() {

                    var address = document.getElementById('country').value+ " " + document.getElementById('region').value
                    + " " + document.getElementById('city').value+ " " + document.getElementById('zipcode').value 
                    + " " + document.getElementById('vlocation').value;

                    geo_url = 'https://nominatim.openstreetmap.org/search?format=json&limit=1&q='+encodeURI(address);

                    // use jQuery to call the API and get the JSON results
                    jQuery.getJSON(geo_url, function(data) {
                      // the full results JSON
                      //console.log(data);

                      if(data.length > 0 ){
                          updateCoordinates( data );
                      } else {
                            alert("Please check the accuracy of Address");
                      }

                    });

                }

            </script>

            <!--End Open map.-->

        <?php
            } else {
                //show google map
        ?>

                      <!--Image google map-->
                      <?php
                        // add api key
                        $api_key = "key=" . $vehiclemanager_configuration['api_key'] ;
                         //--------------------------------------------------------
                      ?>
                      <script src="//maps.googleapis.com/maps/api/js?<?php echo $api_key ?>" type="text/javascript"></script>
                      <script type="text/javascript">
                          var map;
                          var lastmarker = null;
                          var marker = null;
                          var mapOptions;

                          setTimeout(function() {
                              vm_initialize();
                          },20);
                          function vm_initialize(){
                              var myOptions = {
                                  zoom: <?php if($row->map_zoom) echo $row->map_zoom;
                                           else echo 1;?>,
                                  center: new google.maps.LatLng(<?php if ($row->vlatitude) echo $row->vlatitude; else echo 0; ?>,<?php if ($row->vlongitude) echo $row->vlongitude; else echo 0; ?>),
                                  scrollwheel: false,
                                  zoomControlOptions: {
                                      style: google.maps.ZoomControlStyle.LARGE
                                  },
                                  mapTypeId: google.maps.MapTypeId.ROADMAP
                              };
                              var geocoder = new google.maps.Geocoder();
                              var map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
                              var bounds = new google.maps.LatLngBounds ();

                          <?php if ($row->vlatitude && $row->vlongitude)
                          {
                          ?>
                                  //Set the marker coordinates
                                  var lastmarker = new google.maps.Marker({
                                      position: new google.maps.LatLng(<?php echo $row->vlatitude; ?>, <?php echo $row->vlongitude; ?>)
                                  });
                                  lastmarker.setMap(map);
                          <?php } ?>
                              //If the zoom, then store it in the field map_zoom
                              google.maps.event.addListener(map,"zoom_changed", function(){
                                  document.getElementById("map_zoom").value=map.getZoom();
                              });
                              google.maps.event.addListener(map,"click", function(e){

                                  //Initialize marker
                                                  marker = new google.maps.Marker({
                                                      position: new google.maps.LatLng(e.latLng.lat(),e.latLng.lng())
                                                  });

                                  //Delete marker
                                  if(lastmarker) lastmarker.setMap(null);;
                                  //Add marker to the map
                                  marker.setMap(map);
                                  //Output marker information
                                  document.getElementById("vlatitude").value=e.latLng.lat();
                                  document.getElementById("vlongitude").value=e.latLng.lng();
                                  //Memory marker to delete
                                  lastmarker = marker;
                              });
                          }

                          function updateCoordinates(latlng)
                          {
                              if(latlng)
                              {
                                  document.getElementById('vlatitude').value = latlng.lat();
                                  document.getElementById('vlongitude').value = latlng.lng();
                                  document.getElementById("map_zoom").value=map.getZoom();
                              }
                          }

                          function toggleBounce() {

                              if (marker.getAnimation() != null) {
                                  marker.setAnimation(null);
                              } else {
                                  marker.setAnimation(google.maps.Animation.BOUNCE);
                              }
                          }

                          function codeAddress() {
                              var geocoder = new google.maps.Geocoder();

                              myOptions = {
                                  zoom:14,
                                  scrollwheel: false,
                                  zoomControlOptions: {
                                      style: google.maps.ZoomControlStyle.LARGE
                                  },
                                  mapTypeId: google.maps.MapTypeId.ROADMAP
                              }
                              map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
                              var address = document.getElementById('vlocation').value + " " + document.getElementById('country').value+ " " + document.getElementById('region').value+ " " + document.getElementById('city').value+ " " + document.getElementById('zipcode').value + " " + document.getElementById('vlatitude').value + " " + document.getElementById('vlongitude').value;
                              geocoder.geocode( { 'address': address}, function(results, status) {
                                  if (status == google.maps.GeocoderStatus.OK) {
                                      map.setCenter(results[0].geometry.location);
                                      updateCoordinates(results[0].geometry.location);

                                      if (marker) marker.setMap(null);
                                      marker = new google.maps.Marker({
                                          map: map,
                                          position: results[0].geometry.location,
                                          draggable: true,
                                          animation: google.maps.Animation.DROP
                                      });
                                      google.maps.event.addListener(marker, 'click', toggleBounce);
                                      google.maps.event.addListener(marker, "dragend", function() {
                                          updateCoordinates(marker.getPosition());
                                      });
                                  } else {
                                      alert("Please check the accuracy of Address");
                                  }
                              });
                          }

                      </script>
                      <!--End google map.-->
                       <?php
                       }
                      ?>
                  </td>
              </tr>
              <tr>
                  <td width="185"><?php echo _VEHICLE_MANAGER_LABEL_LATITUDE; ?>:</td>
                  <td><input class="inputbox" type="text" id="vlatitude" name="vlatitude" size="20" value="<?php echo $row->vlatitude; ?>" readonly/></td>
              </tr>
              <tr>
                  <td width="185"><?php echo _VEHICLE_MANAGER_LABEL_LONGITUDE; ?>:</td>
                  <td>
                      <input class="inputbox" type="text" id="vlongitude" name="vlongitude" size="20" value="<?php echo $row->vlongitude; ?>" readonly/>
                      <input type="hidden" id="map_zoom" name="map_zoom" value="<?php echo $row->map_zoom; ?>" />
                  </td>
              </tr>
          </table>
      <?php
          echo JHtml::_('bootstrap.endTab');
          echo JHtml::_('bootstrap.endTabSet');
      ?>

          <input type="hidden" name="id" value="<?php echo $row->id; ?>" />
          <input type="hidden" name="option" value="<?php echo $option; ?>" />
          <input type="hidden" name="boxchecked" value="0" />
          <input type="hidden" name="task" value="" />
      </form>

      <script language="javascript" type="text/javascript">
          var task = "<?php echo $_REQUEST['task']; ?>";
          if(task === 'clon_vm'){
              if(sessionStorage.getItem('saver') !== 'null'){
                  sessionStorage.setItem('saver', 'null');
                  Joomla.submitform( 'apply' );
	}
	sessionStorage.setItem('saver', 'notnull');
          }

      </script>
      <!--************************   end change review ***********************-->

          <?php
      }

      static function showImportExportVehicles($params, $option)
      {

          global $my, $mosConfig_live_site, $mainframe;
          // for 1.6
          global $doc, $css;
          $doc->addStyleSheet($css);
          $doc->addScript($mosConfig_live_site . '/components/com_vehiclemanager/includes/functions.js');
          $doc->addScript($mosConfig_live_site . '/media/system/js/core.js');

          $html = "<div class='vehicle_manager_caption' ><i class='fa fa-car'></i> " . _VEHICLE_MANAGER_ADMIN_IMPEXP . "</div>";
          $app = JFactory::getApplication();
          $app->JComponentTitle = $html;
           //print_r($app->JComponentTitle); exit;
          ?>

      <div id="overDiv" style="position:absolute; visibility:hidden; z-index:10000;"></div>

      <script language="javascript" type="text/javascript">
          function impch()
          {
              var a = document.getElementById('import_type').value;
              if(a == 4)
                  document.getElementById('import_catid').disabled=true;
              else
                  document.getElementById('import_catid').disabled=false;
          }
          function expch()
          {
              var a = document.getElementById('export_type').value;
              if(a == 4)
                  document.getElementById('export_catid').disabled=true;
              else
                  document.getElementById('export_catid').disabled=false;
          }
          Joomla.submitbutton = function(pressbutton, section) {  
              var form = document.adminForm;
              if (pressbutton == 'import') {
                  if (form.import_type.value == '0') {
                      alert("<?PHP echo _VEHICLE_MANAGER_SHOW_IMPEXP_ERR1; ?>" );
                      return;
                  }
                  if (form.import_file.value == '' && form.import_type.value == '4') {
                      alert("<?PHP echo _VEHICLE_MANAGER_SHOW_IMPEXP_ERR3; ?>");
                      return;
                  }
                  if (form.import_catid.value == '' && form.import_type.value != '4' && form.import_type.value != '0') {
                      alert("<?PHP echo _VEHICLE_MANAGER_SHOW_IMPEXP_ERR2; ?>");
                      return;
                  }
                  if (form.import_catid.value != '' && form.import_file.value == '') {
                      alert("<?PHP echo _VEHICLE_MANAGER_SHOW_IMPEXP_ERR3; ?>");
                      return;
                  }
                  if ((form.import_type.value == '2') && (form.import_catid.value != '' && form.import_file.value != '')) {
                      Joomla.submitform( pressbutton );
                  }
                  if ((form.import_type.value == '1') && (form.import_catid.value != '' && form.import_file.value != '')) {
                      Joomla.submitform( pressbutton );
                  }
                  if (form.import_file.value != '' && form.import_type.value == '4') {
                      resultat_1 = confirm("<?php echo _VEHICLE_MANAGER_SHOW_IMPEXP_CONF; ?>");
                      if (resultat_1) Joomla.submitform( pressbutton );
                  }
              }
              if (pressbutton == 'export') {
                  if (form.export_type.value == '0') {
                      alert("<?PHP echo _VEHICLE_MANAGER_SHOW_IMPEXP_ERR4; ?>");
                      return;
                  }
                  if (form.export_type.value == '4') {
                      Joomla.submitform( pressbutton );
                  }
                  if (form.export_type.value == '1') {
                      if (form.export_catid.value == '') {
                          alert("<?php echo _VEHICLE_MANAGER_ADMIN_INFOTEXT_JS_EDIT_CATEGORY; ?>");
                          return;
                      }
                      Joomla.submitform( pressbutton );
                  }
                  if (form.export_type.value == '2') {
                      if (form.export_catid.value == '') {
                          alert("<?php echo _VEHICLE_MANAGER_ADMIN_INFOTEXT_JS_EDIT_CATEGORY; ?>");
                          return;
                      }
                      Joomla.submitform( pressbutton );
                  }
              }

          }//end function submitbutton(pressbutton)
      </script>
      <form action="index.php" method="post"  class="import_export veh_dd_tabs"  name="adminForm" id="adminForm"  enctype="multipart/form-data">

      <?php

            echo JHtml::_('bootstrap.startTabSet', 'impexPane', array('active' => 'panel_1_impexPane'));
            echo JHtml::_('bootstrap.addTab', 'impexPane', 'panel_1_impexPane', _VEHICLE_MANAGER_ADMIN_IMP);

      ?>
  <div class="adminform_22">
          <table class="adminform">
              <!--*   begin add Warning in 'Import' for 'CSV', 'XML', 'MySQL tables import'  -->
              <tr>
                  <td colspan="3"><?php echo _VEHICLE_MANAGER_SHOW_IMPORT_WARNING_MESSAG; ?>
                      <hr />
                  </td>
              </tr>
              <!--*****   end add Warning in 'Import' for 'CSV', 'XML', 'MySQL tables import'   -->
              <tr>
                  <td width="185"><?php echo _VEHICLE_MANAGER_SHOW_IMPEXP_LABEL_IMPORT_TYP; ?>:</td> <!-- Typ importu -->
                  <td width="20"><?php echo "<img src='" . JURI::base() . 'components/com_vehiclemanager/images/info.png' . "' title='" . _VEHICLE_MANAGER_ADMIN_SHOW_IMPEXP_LABEL_IMPORT_TYP . "' alt='info'>"; ?></td>
                  <td><?php echo $params['import']['type']; ?></td>
              </tr>
              <tr>
                  <td width="185"><?php echo _VEHICLE_MANAGER_SHOW_IMPEXP_LABEL_IMPORT_CATEGORY; ?>:</td> <!-- Kategoria -->
                  <td width="20"><?php echo "<img src='" . JURI::base() . 'components/com_vehiclemanager/images/info.png' . "' title='" . _VEHICLE_MANAGER_ADMIN_SHOW_IMPEXP_LABEL_IMPORT_CAT_TT_HEAD . "' alt='info'>"; ?></td>
                  <td><?php echo $params['import']['category']; ?></td>
              </tr>
              <tr>
                  <td width="185"><?php echo _VEHICLE_MANAGER_SHOW_IMPEXP_LABEL_IMPORT_FILE; ?>:</td>   <!-- Plik do importu -->
                  <td width="20"><?php echo "<img src='" . JURI::base() . 'components/com_vehiclemanager/images/info.png' . "' title='" . _VEHICLE_MANAGER_ADMIN_SHOW_IMPEXP_LABEL_IMPORT_FILE_TT_HEAD . "' alt='info'>"; ?></td>
                  <td><input class="inputbox" type="file" name="import_file" value="" size="50" maxlength="250" /></td>
              </tr>
      <?php ?>
     </table>
  </div>
      <?php

            echo JHtml::_('bootstrap.endTab');
            echo JHtml::_('bootstrap.addTab', 'impexPane', 'panel_2_impexPane', _VEHICLE_MANAGER_ADMIN_EXP );

      ?>

  <div class="adminform_23">
          <table class="adminform">
              <!--*****************************************************************************************************************-->
              <!--********************   begin add Warning in 'Export' for 'CSV', 'XML', 'MySQL tables import'   ******************-->
              <!--*****************************************************************************************************************-->
              <tr>
                  <td><?php echo _VEHICLE_MANAGER_SHOW_EXPORT_WARNING_MESSAG; ?></td>
              </tr>
              </table>
              <!--*****************************************************************************************************************-->
              <!--********************   end add Warning in 'Export' for 'CSV', 'XML', 'MySQL tables import'   ********************-->
              <!--*****************************************************************************************************************-->
              <table class="adminform">
              <tr>
                  <td width="185"><?php echo _VEHICLE_MANAGER_SHOW_IMPEXP_LABEL_EXPORT_TYP; ?>:</td>
                  <td width="20"><?php echo "<img src='" . JURI::base() . 'components/com_vehiclemanager/images/info.png' . "' title='" . _VEHICLE_MANAGER_ADMIN_SHOW_IMPEXP_LABEL_EXPORT_TYP_TT_HEAD . "' alt='info'>"; ?></td>
                  <td><?php echo $params['export']['type']; ?></td>
              </tr>
              <tr>
                  <td width="185"><?php echo _VEHICLE_MANAGER_SHOW_IMPEXP_LABEL_EXPORT_CATEGORY; ?>:</td>
                  <td width="20"><?php echo "<img src='" . JURI::base() . 'components/com_vehiclemanager/images/info.png' . "' title='" . _VEHICLE_MANAGER_ADMIN_SHOW_IMPEXP_LABEL_EXPORT_CAT_TT_HEAD . "' alt='info'>"; ?></td>
                  <td><?php echo $params['export']['category']; ?></td>
              </tr>
          </table>
</div>
      <?php
            echo JHtml::_('bootstrap.endTab');
            echo JHtml::_('bootstrap.endTabSet');
      ?>
          <input type="hidden" name="option" value="<?php echo $option; ?>" />
          <input type="hidden" name="task" value="" />
      </form>
                      <?php
                  }

 static function showRentVehicles($option, $main_veh, $rows, & $userlist, $type)
  {

    global $my, $mosConfig_live_site, $mainframe, $vehiclemanager_configuration;
    // for 1.6
    global $doc, $css;
    $doc->addStyleSheet($css);
    $doc->addScript($mosConfig_live_site . '/components/com_vehiclemanager/includes/functions.js');
    $doc->addStyleSheet($mosConfig_live_site . '/components/com_vehiclemanager/includes/jquerOs-ui.min.css');

    $html = "<div class='vehicle_manager_caption' ><i class='fa fa-car'></i> " . _VEHICLE_MANAGER_ADMIN_REQUEST_RENT . "</div>";
    $app = JFactory::getApplication();
    $app->JComponentTitle = $html;
    ?>
    <script type="text/javascript" src="<?php echo $mosConfig_live_site ?>/components/com_vehiclemanager/includes/jQuerOs-2.2.4.min.js"></script>
    <script type="text/javascript" src="<?php echo $mosConfig_live_site ?>/components/com_vehiclemanager/includes/jquerOs-ui.min.js"></script>
    <div id="overDiv" style="position:absolute; visibility:hidden; z-index:1000;"></div>
<?php
    $vid = $_REQUEST['vid'];
    $vehicle_id_fordate = $vid[0];
    $date_NA = available_dates($vehicle_id_fordate);
?>
    <script type="text/javascript">
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
        });

      jQuerOs( "#rent_until" ).datepicker({
        minDate: "+0",
        dateFormat: "<?php echo transforDateFromPhpToJquery_vm();?>",
        beforeShowDay: unavailableUntil,
        });



        jQuerOs('#userid').change(function(event) {
            if(jQuerOs(this).val() == '-1'){
              jQuerOs('[name=user_name]').val('');
              jQuerOs('[name=user_email]').val('');
              jQuerOs('[name=user_name], [name=user_email]').removeAttr('readonly');
            }else{
              jQuerOs.ajax({
                type: "POST",
                url: "<?php echo $mosConfig_live_site;?>/index.php?option=com_vehiclemanager&task=getUserData&userId="+jQuerOs(this).val()+"&format=raw",
                success: function(user){
                  var user = jQuerOs.parseJSON(user);
                  jQuerOs('[name=user_name], [name=user_email]').attr('readonly','readonly');
                  jQuerOs('[name=user_name]').val(user.name);
                  jQuerOs('[name=user_email]').val(user.email);
                }
              });
            }
        });


      });
    </script>



      <form action="index.php" method="post" name="adminForm"  id="adminForm">
          <table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminform  adminform_24">
              <tr>
                  <td width="100%" class="vehicle_manager_caption"  >
          <?php
          if ($type == "rent")
          {
              echo _VEHICLE_MANAGER_SHOW_RENT_VEHICLES;
          } else
          if ($type == "rent_return")
          {
              echo _VEHICLE_MANAGER_SHOW_RENT_RETURN;
          }if ($type == "edit_rent")
          {
              echo _VEHICLE_MANAGER_SHOW_RENT_EDIT;
          } else
          {

              echo "&nbsp;";
          }
          ?>
                  </td>
              </tr>
          </table>
              <?php
              if ($type == "rent" or $type == "edit_rent")
              {
                  ?>
              <table cellpadding="4" cellspacing="0" border="0" width="100%">
                  <tr>
                      <td align="center" nowrap="nowrap"><?php echo _VEHICLE_MANAGER_LABEL_RENT_TO . ':'; ?></td>
                      <td align="center" nowrap="nowrap"><?php echo $userlist; ?></td>
                      <td align="center" nowrap="nowrap"><?php echo _VEHICLE_MANAGER_LABEL_RENT_USER . ':'; ?></td>
                      <td><input type="text" name="user_name" class="inputbox" /></td>
                      <td width="1000%">
                      </td>
                  </tr>
                  <tr>
                      <td nowrap="nowrap"><?php echo _VEHICLE_MANAGER_LABEL_RENT_EMAIL . ':'; ?></td>
                      <td>    <input type="text" name="user_email" class="inputbox" /></td>
                  </tr>

                  <tr>
                      <td nowrap="nowrap"><?php echo _VEHICLE_MANAGER_LABEL_RENT_PHONE . ':'; ?></td>
                      <td>    <input type="text" name="user_phone" class="inputbox" /></td>
                  </tr>

                  <tr>
                      <td nowrap="nowrap"><?php echo "Rent from:"; ?></td>
                      <td nowrap="nowrap">
                        <input type="text" id="rent_from" name="rent_from" >
                      </td>
                      <td nowrap="nowrap"><?php echo _VEHICLE_MANAGER_LABEL_RENT_TIME . ':'; ?></td>
                      <td nowrap="nowrap">
                        <input type="text" id="rent_until" name="rent_until" >
                      </td>
                  </tr>
              </table>
                  <?php
              } else
              {
                  ?>
              &nbsp;
                  <?php
              }

              $all = JFactory::getDBO();
              $query = "SELECT * FROM #__vehiclemanager_rent";
              $all->setQuery($query);
              $num = $all->loadObjectList();
              ?>
          <table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminlist adminlist_09">
              <tr>
                  <th width="20" align="center">
                  <?php if ($type != 'rent')
                  {
                      ?> <input type="checkbox" name="toggle" onClick="vm_checkAll(this<?php //echo count( $num );  ?>);" />
                  <?php
                  } ?> </th>
                  <th align = "center" width="30">#</th>
                  <th align = "center" class="title" width="5%" nowrap="nowrap"><?php echo _VEHICLE_MANAGER_LABEL_VEHICLEID; ?></th>
                  <th align = "center" class="title" width="25%" nowrap="nowrap"><?php echo _VEHICLE_MANAGER_LABEL_TITLE; ?></th>
                  <th align = "center" class="title" width="15%" nowrap="nowrap"><?php echo _VEHICLE_MANAGER_LABEL_RENT_FROM; ?></th>
                  <th align = "center" class="title" width="20%" nowrap="nowrap"><?php echo _VEHICLE_MANAGER_LABEL_RENT_UNTIL; ?></th>
                  <th align = "center" class="title" width="15%" nowrap="nowrap"><?php echo "Rent return"; ?></th>
                  <th align = "center" class="title" width="20%" nowrap="nowrap"><?php echo _VEHICLE_MANAGER_LABEL_RENT_TO; ?></th>
              </tr>
                <?php
              if ($type == "rent")
              {
                  ?>
                      <td align="center">  <input class="inputbox"  type="checkbox"  name="checkVehicle" id="checkVehicle" size="0" maxlength="0" value="on" /></td>
          <?php
              } else if ($type == "edit_rent"){ ?>
                <input type="hidden"  name="checkVehicle" id="checkVehicle" value="on" /></td>

            <?php
              }
              $assoc_title = '';
              for ($t = 0, $z = count($rows); $t < $z; $t++) {
                if($rows[$t]->id != $main_veh->id) $assoc_title .= " ".$rows[$t]->vtitle;
              }

              print_r("
                <td align=\"center\">". $main_veh->id ."</td>
                <td align=\"center\">" . $main_veh->vehicleid . "</td>
                <td align=\"center\">" . $main_veh->vtitle . " ( " . $assoc_title ." ) " . "</td>
                <td align=\"center\">" . " " . "</td>
                <td align=\"center\">" . " " . "</td>
                <td align=\"center\">" . " " . "</td>
                <td align=\"center\">" . " " . "</td> </tr>");

              print_r("
                <td align=\"center\">-- </td>
                <td align=\"center\">--</td>
                <td align=\"center\">" . "---" . "</td>
                <td align=\"center\">" . "-----------------" . "</td>
                <td align=\"center\">" . " -------------" . "</td>
                <td align=\"center\">" . " ---------" . "</td>
                <td align=\"center\">" . " ---------------------" . "</td>
                <td align=\"center\">" . "------------------" . "</td> </tr>");


              for ($j = 0, $n = count($rows); $j < $n; $j++) {
                  $row = $rows[$j];
                  ?>
                          &nbsp;

                      <input class="inputbox" type="hidden"  name="id" id="id" size="0" maxlength="0" value="<?php echo $main_veh->id; ?>" />
                      <input class="inputbox" type="hidden"  name="vtitle" id="vtitle" size="0" maxlength="0" value="<?php echo $row->vtitle; ?>" />
              <?php
              $vehicle_id = $row->id;
              $data = JFactory::getDBO();

              $query = "SELECT * FROM #__vehiclemanager_rent WHERE fk_vehicleid =" . $vehicle_id . " ORDER BY rent_return "; // AND id =50"

              $data->setQuery($query);
              $allrent = $data->loadObjectList();
              ?>
             <?php
          $num = 1;
          for ($i = 0, $n2 = count($allrent); $i < $n2; $i++) {
              if (!isset($allrent[$i]->rent_return) && $type != "rent")
              {
              ?>
                  <td align="center"><input type="checkbox"  id="cb<?php echo $i; ?>" name="vid[]" value="<?php echo $allrent[$i]->id; ?>" onClick="isChecked(this.checked);" /></td>
              <?php
              } else
              {
                  ?>
                  <td align="center">
                  </td>
      <?php
              } ?>
              <?php
              print_r("
                <td align=\"center\">" . $num . "</td>
                <td align=\"center\">" . $row->vehicleid . "</td>
                <td align=\"center\">" . $row->vtitle . "</td>
                <td align=\"center\">" . $allrent[$i]->rent_from . "</td>
                <td align=\"center\">" . $allrent[$i]->rent_until . "</td>
                <td align=\"center\">" . $allrent[$i]->rent_return . "</td>
                <td align=\"center\">" . $allrent[$i]->user_name . ":  " . $allrent[$i]->user_email . "</td> </tr>");
              $num++;
          }
          ?>
      <?php } ?>
          </table>
          <input type="hidden" name="option" value="<?php echo $option; ?>" />
          <input type="hidden" name="task" value="" />
          <input type="hidden" name="boxchecked" value="1" />
          <input type="hidden" name="save" value="1" />
      </form>
      <?php
  }

  static function showRentHistory($option, $main_veh, $rows, & $userlist, $type){
    global $my, $mosConfig_live_site, $mainframe;
    // for 1.6
    global $doc, $css;
    $doc->addStyleSheet($css);
    $doc->addScript($mosConfig_live_site . '/components/com_vehiclemanager/includes/functions.js');
    $html = "<div class='vehicle_manager_caption' ><i class='fa fa-car'></i> " . _VEHICLE_MANAGER_ADMIN_REQUEST_RENT_HISTORY . "</div>";
    $app = JFactory::getApplication();
    $app->JComponentTitle = $html;
    ?>
    <div id="overDiv" style="position:absolute; visibility:hidden; z-index:1000;"></div>
    <table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminform  adminform_24">
      <tr>
        <td width="100%" class="vehicle_manager_caption"  >
          <?php
          echo '<h2 align="center">'._VEHICLE_MANAGER_ADMIN_REQUEST_RENT_HISTORY.'</h2>';
          ?>
        </td>
      </tr>
    </table>
    <table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminlist adminlist_09">
      <tr>
        <th align = "center" width="30">#</th>
        <th align = "center" class="title" width="5%" nowrap="nowrap"><?php echo _VEHICLE_MANAGER_LABEL_VEHICLEID; ?></th>
        <th align = "center" class="title" width="25%" nowrap="nowrap"><?php echo _VEHICLE_MANAGER_LABEL_TITLE; ?></th>
        <th align = "center" class="title" width="15%" nowrap="nowrap"><?php echo _VEHICLE_MANAGER_LABEL_RENT_FROM; ?></th>
        <th align = "center" class="title" width="20%" nowrap="nowrap"><?php echo _VEHICLE_MANAGER_LABEL_RENT_UNTIL; ?></th>
        <th align = "center" class="title" width="15%" nowrap="nowrap"><?php echo "Rent return"; ?></th>
        <th align = "center" class="title" width="20%" nowrap="nowrap"><?php echo _VEHICLE_MANAGER_LABEL_RENT_TO; ?></th>
      </tr>
      <?php
      for ($j = 0, $n = count($rows); $j < $n; $j++) {
        $row = $rows[$j];
        $vehicle_id = $row->id;
        $data = JFactory::getDBO();
        $query = "SELECT * FROM #__vehiclemanager_rent WHERE fk_vehicleid =" . $vehicle_id . " ORDER BY rent_return "; // AND id =50"
        $data->setQuery($query);
        $allrent = $data->loadObjectList();
        $num = 1;
        for ($i = 0, $n2 = count($allrent); $i < $n2; $i++) {
          print_r("
            <td align=\"center\">" . $num . "</td>
            <td align=\"center\">" . $row->vehicleid . "</td>
            <td align=\"center\">" . $row->vtitle . "</td>
            <td align=\"center\">" . $allrent[$i]->rent_from . "</td>
            <td align=\"center\">" . $allrent[$i]->rent_until . "</td>
            <td align=\"center\">" . $allrent[$i]->rent_return . "</td>
            <td align=\"center\">" . $allrent[$i]->user_name . ":  " . $allrent[$i]->user_email . "</td> </tr>");
          $num++;
        }
      } ?>
      </table>
    <?php
  }

  static function showUsersRentHistory($option, $allrent, &$userlist){
    global $my, $mosConfig_live_site, $mainframe;
    // for 1.6
    global $doc, $css;
    $doc->addStyleSheet($css);
    $doc->addScript($mosConfig_live_site . '/components/com_vehiclemanager/includes/functions.js');
    $html = "<div class='vehicle_manager_caption' >
            <i class='fa fa-car'></i> " .
             _VEHICLE_MANAGER_ADMIN_USER_RENT_HISTORY . "</div>";
    $app = JFactory::getApplication();
    $app->JComponentTitle = $html;
    ?>
    <div id="overDiv" style="position:absolute; visibility:hidden; z-index:1000;"></div>
    <form action="index.php" method="post" name="adminForm"  class="vehicles_main"  id="adminForm" >
      <table cellpadding="4" cellspacing="0" border="0" width="100%">
        <tr>
            <td nowrap="nowrap"><?php echo _VEHICLE_MANAGER_LABEL_RENT_USER . ':'.$userlist; ?></td>
        </tr>
      </table>
      <input type="hidden" name="task" value="users_rent_history"/>
      <input type="hidden" name="option" value="<?php echo $option; ?>" />
    </form>
    <table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminlist adminlist_09">
      <tr>
        <th align = "center" width="30">#</th>
        <th align = "center" class="title" width="5%" nowrap="nowrap"><a href="index.php?option=com_vehiclemanager&task=user_rent_history_sorted_by_id"><?php echo _VEHICLE_MANAGER_LABEL_VEHICLEID; ?></a></th>
        <th align = "center" class="title" width="25%" nowrap="nowrap"><?php echo _VEHICLE_MANAGER_LABEL_TITLE; ?></th>
        <th align = "center" class="title" width="15%" nowrap="nowrap"><a href="index.php?option=com_vehiclemanager&task=user_rent_history_sorted_by_rent_from"><?php echo _VEHICLE_MANAGER_LABEL_RENT_FROM; ?></a></th>
        <th align = "center" class="title" width="20%" nowrap="nowrap"><a href="index.php?option=com_vehiclemanager&task=user_rent_history_sorted_by_rent_until"><?php echo _VEHICLE_MANAGER_LABEL_RENT_UNTIL; ?></a></th>
        <th align = "center" class="title" width="15%" nowrap="nowrap"><a href="index.php?option=com_vehiclemanager&task=user_rent_history_sorted_by_rent_return"><?php echo _VEHICLE_MANAGER_LABEL_RENT_RETURN; ?></a></th>
        <th align = "center" class="title" width="20%" nowrap="nowrap"><?php echo _VEHICLE_MANAGER_LABEL_RENT_TO; ?></th>
      </tr>
      <?php
      if($allrent){
        for ($i = 0; $i < count($allrent); $i++) {
          print_r("
            <td align=\"center\">" . ($i+1) . "</td>
            <td align=\"center\">" . $allrent[$i]->vehicleid . "</td>
            <td align=\"center\">" . $allrent[$i]->vtitle . "</td>
            <td align=\"center\">" . $allrent[$i]->rent_from . "</td>
            <td align=\"center\">" . $allrent[$i]->rent_until . "</td>
            <td align=\"center\">" . $allrent[$i]->rent_return . "</td>
            <td align=\"center\">" . $allrent[$i]->user_name . ":  " . $allrent[$i]->user_email . "</td> </tr>");
        }
      }
      ?>
      </table>
    <?php
  }

static function editRentVehicles($option, $main_veh, $rows, $title_assoc, & $userlist, & $all_assosiate_rent, $type)
  {



    global $my, $mosConfig_live_site, $mainframe, $vehiclemanager_configuration;
    // for 1.6
    global $doc, $css;
    $doc->addStyleSheet($css);
    $doc->addScript($mosConfig_live_site . '/components/com_vehiclemanager/includes/functions.js');
    $doc->addStyleSheet($mosConfig_live_site . '/components/com_vehiclemanager/includes/jquerOs-ui.min.css');
    $html = "<div class='vehicle_manager_caption' ><i class='fa fa-car'></i> " . _VEHICLE_MANAGER_ADMIN_REQUEST_RENT . "</div>";
    $app = JFactory::getApplication();
    $app->JComponentTitle = $html;

    ?>
    <script type="text/javascript" src="<?php echo $mosConfig_live_site ?>/components/com_vehiclemanager/includes/jQuerOs-2.2.4.min.js"></script>
    <script type="text/javascript" src="<?php echo $mosConfig_live_site ?>/components/com_vehiclemanager/includes/jquerOs-ui.min.js"></script>
    <div id="overDiv" style="position:absolute; visibility:hidden; z-index:1000;"></div>

<?php

    $vid = $_REQUEST['vid'];
    $vehicle_id_fordate = $vid[0];
    $date_NA = available_dates($vehicle_id_fordate);
?>
    <script type="text/javascript">
    var unavailableDates = Array();
    jQuerOs(document).ready(function() {
      var k=0;
      <?php if(!empty($date_NA)){
        foreach ($date_NA as $N_A){ ?>
          unavailableDates[k]= '<?php echo $N_A; ?>';
          k++;
        <?php } ?>
      <?php } ?>
      // function unavailableFrom(date) {
      //     dmy = date.getFullYear() + "-" + ('0'+(date.getMonth() + 1)).slice(-2) + "-"
      //       + ('0'+date.getDate()).slice(-2);
      //     if (jQuerOs.inArray(dmy, unavailableDates) == -1) {
      //         return [true, ""];
      //     } else {
      //         return [false, "", "Unavailable"];
      //     }
      // }

      // function unavailableUntil(date) {
      //     dmy = date.getFullYear() + "-" + ('0'+(date.getMonth() + 1)).slice(-2) +
      //       "-" + ('0'+(date.getDate()-("<?php  if(!$vehiclemanager_configuration['special_price']['show']) echo '1';?>"))).slice(-2);
      //     if (jQuerOs.inArray(dmy, unavailableDates) == -1) {
      //         return [true, ""];
      //     } else {
      //         return [false, "", "Unavailable"];
      //     }
      // }

      jQuerOs( "#rent_from" ).datepicker({
        minDate: "+0",
        dateFormat: "<?php echo transforDateFromPhpToJquery_vm();?>",
        // beforeShowDay: unavailableFrom,
        });

      jQuerOs( "#rent_until" ).datepicker({
        minDate: "+0",
        dateFormat: "<?php echo transforDateFromPhpToJquery_vm();?>",
        // beforeShowDay: unavailableUntil,
        });

      jQuerOs('#userid').change(function(event) {
            if(jQuerOs(this).val() == '-1'){
              jQuerOs('[name=user_name]').val('');
              jQuerOs('[name=user_email]').val('');
              jQuerOs('[name=user_name], [name=user_email]').removeAttr('readonly');
            }else{
              jQuerOs.ajax({
                type: "POST",
                url: "<?php echo $mosConfig_live_site;?>/index.php?option=com_vehiclemanager&task=getUserData&userId="+jQuerOs(this).val()+"&format=raw",
                success: function(user){
                  var user = jQuerOs.parseJSON(user);
                  jQuerOs('[name=user_name], [name=user_email]').attr('readonly','readonly');
                  jQuerOs('[name=user_name]').val(user.name);
                  jQuerOs('[name=user_email]').val(user.email);
                }
              });
            }
        });

      });
    </script>

      <form action="index.php" method="post" name="adminForm"  id="adminForm">
          <table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminform  adminform_24">
              <tr>
                  <td width="100%" class="vehicle_manager_caption"  >
          <?php
          if ($type == "rent")
          {
              echo _VEHICLE_MANAGER_SHOW_RENT_VEHICLES;
          } else
          if ($type == "rent_return")
          {
              echo _VEHICLE_MANAGER_SHOW_RENT_RETURN;
          }if ($type == "edit_rent")
          {
              echo _VEHICLE_MANAGER_SHOW_RENT_EDIT;
          } else
          {

              echo "&nbsp;";
          }
          ?>
                  </td>
              </tr>
          </table>
              <?php
              if ( $type == "edit_rent")
              {
                  ?>
              <table cellpadding="4" cellspacing="0" border="0" width="100%">
                  <tr>
                      <td align="center" nowrap="nowrap"><?php echo _VEHICLE_MANAGER_LABEL_RENT_TO . ':'; ?></td>
                      <td align="center" nowrap="nowrap"><?php echo $userlist; ?></td>
                      <td align="center" nowrap="nowrap"><?php echo _VEHICLE_MANAGER_LABEL_RENT_USER . ':'; ?></td>
                      <td><input type="text" name="user_name" class="inputbox" /></td>
                      <td width="1000%">
                      </td>
                  </tr>
                  <tr>
                      <td nowrap="nowrap"><?php echo _VEHICLE_MANAGER_LABEL_RENT_EMAIL . ':'; ?></td>
                      <td>    <input type="text" name="user_email" class="inputbox" /></td>
                  </tr>

                  <tr>
                      <td nowrap="nowrap"><?php echo _VEHICLE_MANAGER_LABEL_RENT_PHONE . ':'; ?></td>
                      <td>    <input type="text" name="user_phone" class="inputbox" /></td>
                  </tr>

                  <tr>
                      <td nowrap="nowrap"><?php echo "Rent from:"; ?></td>
                      <td nowrap="nowrap">
                        <input type="text" id="rent_from" name="rent_from">
                      </td>
                      <td nowrap="nowrap"><?php echo _VEHICLE_MANAGER_LABEL_RENT_TIME . ':'; ?></td>
                      <td nowrap="nowrap">
                        <input type="text" id="rent_until" name="rent_until">
                      </td>
                  </tr>
              </table>

                  <?php
              }

              $all = JFactory::getDBO();
              $query = "SELECT * FROM #__vehiclemanager_rent";
              $all->setQuery($query);
              $num = $all->loadObjectList();
              ?>
          <table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminlist adminlist_09">
              <tr>
                  <th width="20" align="center">
                  <input type="checkbox" name="toggle" onClick="vm_checkAll(this<?php //echo count( $num );  ?>);" />
                  <th align = "center" width="30">#</th>
                  <th align = "center" class="title" width="5%" nowrap="nowrap"><?php echo _VEHICLE_MANAGER_LABEL_VEHICLEID; ?></th>
                  <th align = "center" class="title" width="25%" nowrap="nowrap"><?php echo _VEHICLE_MANAGER_LABEL_TITLE; ?></th>
                  <th align = "center" class="title" width="15%" nowrap="nowrap"><?php echo _VEHICLE_MANAGER_LABEL_RENT_FROM; ?></th>
                  <th align = "center" class="title" width="20%" nowrap="nowrap"><?php echo _VEHICLE_MANAGER_LABEL_RENT_UNTIL; ?></th>
                  <th align = "center" class="title" width="15%" nowrap="nowrap"><?php echo "Rent return"; ?></th>
                  <th align = "center" class="title" width="20%" nowrap="nowrap"><?php echo _VEHICLE_MANAGER_LABEL_RENT_TO; ?></th>
              </tr>
          <?php
              if ($type == "edit_rent"){ ?>
                <input type="hidden"  name="checkVehicle" id="checkVehicle" value="on" /></td>

            <?php
              }
              $assoc_title = '';
              for ($t = 0, $z = count($title_assoc); $t < $z; $t++) {
                if($title_assoc[$t]->vtitle != $main_veh->vtitle) $assoc_title .= " ".$title_assoc[$t]->vtitle;
              }

                //show rent history what we may change
                  ?>
                      &nbsp;
                  <input class="inputbox" type="hidden"  name="id" id="id" size="0" maxlength="0" value="<?php echo $main_veh->id; ?>" />
                  <input class="inputbox" type="hidden"  name="vtitle" id="vtitle" size="0" maxlength="0" value="<?php echo $assoc_title; ?>" />
                  <?php
                  $num = 1;
                  for ($i = 0, $n2 = count($all_assosiate_rent[0]); $i < $n2; $i++) {
                      $assoc_rent_ids = '';
                      for ($j = 0, $n3 = count($all_assosiate_rent); $j < $n3; $j++) {
                          if($assoc_rent_ids != "" ) $assoc_rent_ids .= ",".$all_assosiate_rent[$j][$i]->id;
                          else $assoc_rent_ids = $all_assosiate_rent[$j][$i]->id;
                      }

                      ?>
                          <td align="center"><input type="checkbox"  id="cb<?php echo $i; ?>" name="vid[]"
                            value="<?php echo $assoc_rent_ids; ?>" onClick="isChecked(this.checked);" /></td>
                      <?php
                      print_r("
                        <td align=\"center\">" . $num . "</td>
                        <td align=\"center\"> </td>
                        <td align=\"center\">" . $main_veh->vtitle . " ( " . $assoc_title ." ) " . "</td>
                        <td align=\"center\">" . $all_assosiate_rent[0][$i]->rent_from . "</td>
                        <td align=\"center\">" . $all_assosiate_rent[0][$i]->rent_until . "</td>
                        <td align=\"center\">" . $all_assosiate_rent[0][$i]->rent_return . "</td>
                        <td align=\"center\">" . $all_assosiate_rent[0][$i]->user_name . ":  " . $all_assosiate_rent[0][$i]->user_email . "</td> </tr>");
                      $num++;
                  }

              print_r("
                <td align=\"center\">-- </td>
                <td align=\"center\">--</td>
                <td align=\"center\">" . "---" . "</td>
                <td align=\"center\">" . "-----------------" . "</td>
                <td align=\"center\">" . " -------------" . "</td>
                <td align=\"center\">" . " ---------" . "</td>
                <td align=\"center\">" . " ---------------------" . "</td>
                <td align=\"center\">" . "------------------" . "</td> </tr>");

                //show rent history what we can't change
                for ($j = 0, $n = count($rows); $j < $n; $j++) {
                  $row = $rows[$j];
                   if($row->rent_return == "" ) continue ;
                  ?>
                      &nbsp;
                  <!--input class="inputbox" type="hidden"  name="vehicleid" id="vehicleid" size="0" maxlength="0" value="<?php /*echo $row->vehicleid; */ ?>" /-->
                  <input class="inputbox" type="hidden"  name="id" id="id" size="0" maxlength="0" value="<?php echo $main_veh->id; ?>" />
                  <input class="inputbox" type="hidden"  name="vtitle" id="vtitle" size="0" maxlength="0" value="<?php echo $row->vtitle; ?>" />
                  <?php


                  $num = 1;

                      {
                          ?>
                          <td align="center">
                          </td>
              <?php
                      } ?>
                      <?php
                      print_r("
                        <td align=\"center\">" . $num . "</td>
                        <td align=\"center\">" . $row->vehicleid . "</td>
                        <td align=\"center\">" . $row->vtitle . "</td>
                        <td align=\"center\">" . $row->rent_from . "</td>
                        <td align=\"center\">" . $row->rent_until . "</td>
                        <td align=\"center\">" . $row->rent_return . "</td>
                        <td align=\"center\">" . $row->user_name . ":  " . $row->user_email . "</td> </tr>");
                      $num++;
                   } ?>
          </table>
          <input type="hidden" name="option" value="<?php echo $option; ?>" />
          <input type="hidden" name="task" value="" />
          <input type="hidden" name="boxchecked" value="1" />
          <input type="hidden" name="save" value="1" />
      </form>
      <?php
  }


  static function showConfiguration($lists, $option, $txt)
  {
      // global $my, $mosConfig_live_site, $mainframe, $act, $task, $vehiclemanager_configuration;
      global $my, $mosConfig_live_site, $mainframe, $act, $task, $vehiclemanager_configuration, $database;
      // for 1.6
      global $doc, $css;
      $doc->addStyleSheet($css);
      $doc->addScript($mosConfig_live_site . '/components/com_vehiclemanager/includes/functions.js');
      $doc->addScript($mosConfig_live_site . '/components/com_vehiclemanager/includes/jQuerOs-2.2.4.min.js');
      $doc->addScriptDeclaration('jQuerOs=jQuerOs.noConflict();');


      $doc->addScript($mosConfig_live_site . '/components/com_vehiclemanager/includes/jQuerVEH.minicolors.js');
      $doc->addStyleSheet($mosConfig_live_site .'/components/com_vehiclemanager/includes/jQuerVEH.minicolors.css');

      $html = "<div class='vehicle_manager_caption' ><i class='fa fa-car'></i> " . _VEHICLE_MANAGER_ADMIN_CONFIG . "</div>";
      $app = JFactory::getApplication();
      $app->JComponentTitle = $html;
      ?>
      <script>
          window.onload=function()
          {
            if (document.getElementById('money_select').options[document.getElementById('money_select').selectedIndex].value == 'other') {
              document.getElementById('patt').type="text";
              document.getElementById('patt').removeAttribute('readonly');
            }
            //redirect rent options
            if (document.getElementById('redirect_rent').options[document.getElementById('redirect_rent').selectedIndex].value == 'redirect to input link') {
              document.getElementById('patern_rent').type="text";
              document.getElementById('patern_rent').removeAttribute('readonly');
            }
            //redirect sale options
            if (document.getElementById('redirect_sale').options[document.getElementById('redirect_sale').selectedIndex].value == 'redirect to input link') {
              document.getElementById('patern_sale').type="text";
              document.getElementById('patern_sale').removeAttribute('readonly');
            }
          }

          function set_pricetype(sel) {
              var value = sel.options[sel.selectedIndex].value;
              if (value=="space") {
                  document.getElementById('patt').value="&nbsp;";
                  //      document.getElementById('patt').setAttribute('readonly', true);
                  //     document.getElementById('patt').type="hidden";
              }
              else if (value!="other") {
                  document.getElementById('patt').value=value;
                  document.getElementById('patt').setAttribute('readonly', true);
                  document.getElementById('patt').type="hidden";
              } else
              {
                  document.getElementById('patt').value="";
                  document.getElementById('patt').type="text";
                  document.getElementById('patt').removeAttribute('readonly');
              }
          }

          //06.06.17 rent settings for frontend options
          function set_rent_options(sel) {
              var value = sel.options[sel.selectedIndex].value;

              if (value!="redirect to input link") {
                  document.getElementById('patern_rent').value=value;
                  document.getElementById('patern_rent').setAttribute('readonly', true);
                  document.getElementById('patern_rent').type="hidden";
              } else
              {
                  document.getElementById('patern_rent').value="http://";
                  document.getElementById('patern_rent').type="url";
                  document.getElementById('patern_rent').removeAttribute('readonly');
              }
          }
          //06.06.17 end rent settings for frontend options

          //06.06.17 sale settings for frontend options
          function set_sale_options(sel) {
              var value = sel.options[sel.selectedIndex].value;

              if (value!="redirect to input link") {
                  document.getElementById('patern_sale').value=value;
                  document.getElementById('patern_sale').setAttribute('readonly', true);
                  document.getElementById('patern_sale').type="hidden";
              } else
              {
                  document.getElementById('patern_sale').value="http://";
                  document.getElementById('patern_sale').type="url";
                  document.getElementById('patern_sale').removeAttribute('readonly');
              }
          }

          //06.06.17 end sale settings for frontend options

          function findPosY(obj) {
            var curtop = 0;
            if (obj.offsetParent) {
                while (1) {
                    curtop+=obj.offsetTop;
                    if (!obj.offsetParent) {
                        break;
                    }
                    obj=obj.offsetParent;
                }
            } else if (obj.y) {
                curtop+=obj.y;
            }
            return curtop-20;
        }

        Joomla.submitbutton = function(pressbutton) {
            var form = document.adminForm;
            if (pressbutton == 'config_save') {
                Joomla.submitform(pressbutton);
            }
            Joomla.submitform(pressbutton);
        }
      </script>
       <form action="index.php"  class="veh_settings veh_dd_tabs" method="post" name="adminForm"  id="adminForm" enctype = "multipart/form-data">
      <?php
            echo JHtml::_('bootstrap.startTabSet', 'configurePane', array('active' => 'panel_1_configurePane'));
            echo JHtml::_('bootstrap.addTab', 'configurePane', 'panel_1_configurePane', _VEHICLE_MANAGER_SETTINGS_TAB_LABEL_VEHICLE_PAGE_SETTINGS);
      ?>

      <!-- slider image settings -->

               <tr><td colspan="6"><hr /></td></tr>

            <!-- ********************************************* -->

          <h2 style="text-align:center;"><?php echo _VEHICLE_MANAGER_SETTINGS_VIEW_VEHICLE_LAYOUT_SETTINGS; ?></h2>
          <hr />
   <!--        </table> -->
          <h2 class="small17 center"><?php echo _VEHICLE_MANAGER_SETTINGS_HEADER_LABEL_VEHICLE_IMAGE_SETTINGS; ?></h2>
            <table class="adminform adminform_25" cellpadding="5px">
            <tr>
                <td width="185"><?php echo _VEHICLE_MANAGER_ADMIN_CONFIG_FOTOMAIN_SIZE; ?>:</td>
                <td><?php echo "<img src='" . JURI::base() . 'components/com_vehiclemanager/images/info.png' . "' title='" . _VEHICLE_MANAGER_ADMIN_CONFIG_FOTOMAIN_SIZE_TT_BODY . "' alt='info'>"; ?></td>
                <td><?php echo ($lists['fotomain']['width']) ." / ". "<span class='slash'>&nbsp;&nbsp;</span>  " . ($lists['fotomain']['high']); ?></td>
            </tr>
            <tr>
                <td width="185"><?php echo _VEHICLE_MANAGER_ADMIN_CONFIG_FOTOGAL_SIZE; ?>:</td>
                <td><?php echo "<img src='" . JURI::base() . 'components/com_vehiclemanager/images/info.png' . "' title='" . _VEHICLE_MANAGER_ADMIN_CONFIG_FOTOGAL_SIZE_TT_BODY . "' alt='info'>"; ?></td>
                <td><?php echo ($lists['fotogal']['width']) ." / ". " <span class='slash'>&nbsp;&nbsp;</span>  " . ($lists['fotogal']['high']); ?></td>
            </tr>
          </table>
          <hr />
          <h2 class="small17 center"><?php echo _VEHICLE_MANAGER_SETTINGS_HEADER_LABEL_TABS_MANAGER; ?></h2>
          <table class="adminform adminform_25" cellpadding="5px">
           <tr>
                <td width="185"><?php echo _VEHICLE_MANAGER_ADMIN_CONFIG_REVIEWS_TAB_SHOW ; ?>:</td>
                <td><?php echo "<img src='" . JURI::base() . 'components/com_vehiclemanager/images/info.png' . "' title='" . _VEHICLE_MANAGER_ADMIN_CONFIG_REVIEWS_TAB_SHOW_TT_BODY . "' alt='info'>"; ?></td>
                <td class="yesno"><?php echo $lists['Reviews_vehicle']['show']; ?></td>
            </tr>
            <!-- add calendar year -->
            <tr>
              <td width="185"><?php echo _VEHICLE_MANAGER_CONFIG_CALENDARLIST_VALUE_YEAR; ?>:</td>
              <td><?php echo "<img src='" . JURI::base() . 'components/com_vehiclemanager/images/info.png' . "' title='" . _VEHICLE_MANAGER_CONFIG_CALENDARLIST_VALUE_YEAR_TT_BODY . "' alt='info'>"; ?></td>
              <td><?php echo ($lists['initial_year']) ." / ". "<span class='slash'>&nbsp;&nbsp;</span>  " . ($lists['final_year']); ?></td>
            </tr>
            <!-- end add calendar year -->
            <tr><td colspan="6"><hr /></td></tr>
          </table>
          <h2 class="small17 center"><?php echo _VEHICLE_MANAGER_HEADER_BUTTON ; ?></h2>
          <table class="adminform adminform_31" cellpadding="5px">
            <tr>
                <td width="185"><?php echo _VEHICLE_MANAGER_ADMIN_CONFIG_BUYSTATUS_SHOW; ?>:</td>
                <td><?php echo "<img src='" . JURI::base() . 'components/com_vehiclemanager/images/info.png' . "' title='" . _VEHICLE_MANAGER_ADMIN_CONFIG_BUYSTATUS_SHOW_TT_BODY . "' alt='info'>"; ?></td>
                <td class="yesno"><?php echo $lists['buystatus']['show']; ?></td>
            </tr>
            <tr>
                <td width="185"><?php echo _VEHICLE_MANAGER_ADMIN_CONFIG_RENTSTATUS_SHOW; ?>:</td>
                <td width="20"><?php echo "<img src='" . JURI::base() . 'components/com_vehiclemanager/images/info.png' . "' title='" . _VEHICLE_MANAGER_ADMIN_CONFIG_RENTSTATUS_SHOW_TT_BODY . "' alt='info'>"; ?></td>
                <td class="yesno"><?php echo $lists['rentstatus']['show']; ?></td>
            </tr>
            <!-- __REVIEW__ -->
            <tr>
                <td width="185"><?php echo _VEHICLE_MANAGER_ADMIN_CONFIG_REVIEWS_SHOW ; ?>:</td>
                <td width="20"><?php echo "<img src='" . JURI::base() . 'components/com_vehiclemanager/images/info.png' . "' title='" . _VEHICLE_MANAGER_ADMIN_CONFIG_REVIEWS_SHOW_TT_BODY . "' alt='info'>"; ?></td>
                <td class="yesno"><?php echo $lists['reviews']['show']; ?></td>
            </tr>
            <!-- END__REVIEW__ -->
            <tr><td colspan="6"><hr /></td></tr>
          </table>
            <hr />
          <h2 class="small17 center"><?php echo _VEHICLE_MANAGER_SETTINGS_HEADER_LABEL_FEATURE_LIST_SETTINGS; ?></h2>
          <table class="adminform adminform_28" cellpadding="5px">
            <tr>
                <td width="185"><?php echo _VEHICLE_MANAGER_ADMIN_CONFIG_MANAGER_FEATURE_IMAGE; ?>:</td>
                <td width="20"><?php echo "<img src='" . JURI::base() . 'components/com_vehiclemanager/images/info.png' . "' title='" . _VEHICLE_MANAGER_ADMIN_CONFIG_MANAGER_FEATURE_IMAGE_TT_HEAD . "' alt='info'>"; ?></td>
                <td class="yesno"><?php echo $lists['manager_feature_image']; ?><br></td>
            </tr>
           <tr>
                <td width="185"><?php echo _VEHICLE_MANAGER_ADMIN_CONFIG_MANAGER_FEATURE_CATEGORIES_SHOW; ?>:</td>
                <td width="20"><?php echo "<img src='" . JURI::base() . 'components/com_vehiclemanager/images/info.png' . "' title='" . _VEHICLE_MANAGER_ADMIN_CONFIG_MANAGER_FEATURE_CATEGORIES_SHOW_TT_HEAD . "' alt='info'>"; ?></td>
                <td class="yesno"><?php echo $lists['manager_feature_category']; ?></td>
            </tr>
          </table>
	        <hr />
          <h2 class="small17 center"><?php echo _VEHICLE_MANAGER_SETTINGS_HEADER_LABEL_EXTRA_FIELDS_MANAGER; ?></h2>
          <table class="adminform adminform_30" cellpadding="5px">
            <tr>
                <td width="210"><?php echo _VEHICLE_MANAGER_ADMIN_CONFIG_EXTRA1_SHOW; ?>:</td>
                <td class="yesno"><?php echo $lists['extra1']; ?><br></td>
            </tr>
            <tr>
                <td><?php echo _VEHICLE_MANAGER_ADMIN_CONFIG_EXTRA2_SHOW; ?>:</td>
                <td class="yesno"><?php echo $lists['extra2']; ?><br></td>
            </tr>
            <tr>
                <td><?php echo _VEHICLE_MANAGER_ADMIN_CONFIG_EXTRA3_SHOW; ?>:</td>
                <td class="yesno"><?php echo $lists['extra3']; ?><br></td>
            </tr>
            <tr>
                <td><?php echo _VEHICLE_MANAGER_ADMIN_CONFIG_EXTRA4_SHOW; ?>:</td>
                <td class="yesno"><?php echo $lists['extra4']; ?><br></td>
            </tr>
            <tr>
                <td><?php echo _VEHICLE_MANAGER_ADMIN_CONFIG_EXTRA5_SHOW; ?>:</td>
                <td class="yesno"><?php echo $lists['extra5']; ?><br></td>
            </tr>
            <tr>
                <td><?php echo _VEHICLE_MANAGER_ADMIN_CONFIG_EXTRA6_SHOW; ?>:</td>
                <td class="yesno"><?php echo $lists['extra6']; ?><br></td>
            </tr>
            <tr>
                <td><?php echo _VEHICLE_MANAGER_ADMIN_CONFIG_EXTRA7_SHOW; ?>:</td>
                <td class="yesno"><?php echo $lists['extra7']; ?><br></td>
            </tr>
            <tr>
                <td><?php echo _VEHICLE_MANAGER_ADMIN_CONFIG_EXTRA8_SHOW; ?>:</td>
                <td class="yesno"><?php echo $lists['extra8']; ?><br></td>
            </tr>
            <tr>
                <td><?php echo _VEHICLE_MANAGER_ADMIN_CONFIG_EXTRA9_SHOW; ?>:</td>
                <td class="yesno"><?php echo $lists['extra9']; ?><br></td>
            </tr>
            <tr>
                <td><?php echo _VEHICLE_MANAGER_ADMIN_CONFIG_EXTRA10_SHOW; ?>:</td>
                <td class="yesno"><?php echo $lists['extra10']; ?></td>
            </tr>
          </table>
          <hr/>
          <h2 style="text-align:center;"><?php echo _VEHICLE_MANAGER_HEADER_CATEGORY_OPTIONS; ?></h2>
          <hr/>
          <table class="adminform adminform_32" cellpadding="5px">
            <tr>
              <td width="185"><?php echo _VEHICLE_MANAGER_ADMIN_CONFIG_FOTO_SIZE; ?>:</td>
              <td width="20"><?php echo "<img src='" . JURI::base() . 'components/com_vehiclemanager/images/info.png' . "' title='" . _VEHICLE_MANAGER_ADMIN_CONFIG_FOTO_SIZE_TT_BODY . "' alt='info'>"; ?></td>
              <td><?php echo ($lists['foto']['width']) . " / " . ($lists['foto']['high']); ?></td>
            </tr>
            <tr>
              <td width="185"><?php echo _VEHICLE_MANAGER_ADMIN_CONFIG_FOTOGALLERY_SIZE; ?>:</td>
              <td width="20"><?php echo "<img src='" . JURI::base() . 'components/com_vehiclemanager/images/info.png' . "' title='" . _VEHICLE_MANAGER_ADMIN_CONFIG_FOTOGALLERY_SIZE_TT_BODY . "' alt='info'>"; ?></td>
              <td><?php echo ($lists['fotogallery']['width']) . " / " . ($lists['fotogallery']['high']); ?></td>
            </tr>
          </table>

          <hr/>

          <!-- add count vehicles in row -->
          <h2 class="small17 center"><?php echo _VEHICLE_MANAGER_ADMIN_CONFIG_COUNT_VEHICLES_LABEL; ?></h2>
          <table class="adminform adminform_32" cellpadding="5px">
            <tr>
              <td width="185"><?php echo _VEHICLE_MANAGER_ADMIN_CONFIG_RESOLUTION_LG; ?>:</td>
              <td width="20"><?php echo "<img src='" . JURI::base() . 'components/com_vehiclemanager/images/info.png' . "' title='" . _VEHICLE_MANAGER_ADMIN_CONFIG_RESOLUTION_LG_TT_BODY . "' alt='info'>"; ?></td>
              <td><?php echo $lists['veh_data_columns_lg']; ?></td>
            </tr>
            <tr>
              <td width="185"><?php echo _VEHICLE_MANAGER_ADMIN_CONFIG_RESOLUTION_MD; ?>:</td>
              <td width="20"><?php echo "<img src='" . JURI::base() . 'components/com_vehiclemanager/images/info.png' . "' title='" . _VEHICLE_MANAGER_ADMIN_CONFIG_RESOLUTION_MD_TT_BODY . "' alt='info'>"; ?></td>
              <td><?php echo $lists['veh_data_columns_md']; ?></td>
            </tr>
            <tr>
              <td width="185"><?php echo _VEHICLE_MANAGER_ADMIN_CONFIG_RESOLUTION_SM; ?>:</td>
              <td width="20"><?php echo "<img src='" . JURI::base() . 'components/com_vehiclemanager/images/info.png' . "' title='" . _VEHICLE_MANAGER_ADMIN_CONFIG_RESOLUTION_SM_TT_BODY . "' alt='info'>"; ?></td>
              <td><?php echo $lists['veh_data_columns_sm']; ?></td>
            </tr>
            <tr>
              <td width="185"><?php echo _VEHICLE_MANAGER_ADMIN_CONFIG_RESOLUTION_XS; ?>:</td>
              <td width="20"><?php echo "<img src='" . JURI::base() . 'components/com_vehiclemanager/images/info.png' . "' title='" . _VEHICLE_MANAGER_ADMIN_CONFIG_RESOLUTION_XS_TT_BODY . "' alt='info'>"; ?></td>
              <td><?php echo $lists['veh_data_columns_xs']; ?></td>
            </tr>
          </table>
          <!-- end add count vehicles in row -->
          <hr/>
          <h2 style="text-align:center;"><?php echo _VEHICLE_MANAGER_HEADER_ALL_CATEGORY_OPTIONS; ?></h2>
          <hr/>
          <table class="adminform adminform_33">

            <tr>
              <td width="185"><?php echo _VEHICLE_MANAGER_ADMIN_CONFIG_ORDERING_CATEGORY; ?>:</td>
              <td width="50"?><?php echo "<img src='" . JURI::base() . 'components/com_vehiclemanager/images/info.png' . "' title='" . _VEHICLE_MANAGER_ADMIN_CONFIG_ORDERING_CATEGORY . "' alt='info'>"; ?></td>
              <td  ><?php echo $lists['category']['ordering']; ?><br></td>
            </tr>

            <tr>
              <td width="185"><?php echo _VEHICLE_MANAGER_ADMIN_CONFIG_PICTURE_IN_CATEGORY; ?>:</td>
              <td width="20"><?php echo "<img src='" . JURI::base() . 'components/com_vehiclemanager/images/info.png' . "' title='" . _VEHICLE_MANAGER_ADMIN_CONFIG_PICTURE_IN_CATEGORY_TT_BODY . "' alt='info'>"; ?></td>
              <td class="yesno"><?php echo $lists['cat_pic']['show']; ?><br></td>
            </tr>
            <tr>
                <td width="185"><?php echo _VEHICLE_MANAGER_ADMIN_CONFIG_SUBCATEGORY_SHOW ; ?>:</td>
                <td width="20"><?php echo "<img src='" . JURI::base() . 'components/com_vehiclemanager/images/info.png' . "' title='" . _VEHICLE_MANAGER_ADMIN_CONFIG_SUBCATEGORY_SHOW_TT_BODY . "' alt='info'>"; ?></td>
                <td class="yesno"><?php echo $lists['subcategory']['show']; ?><br></td>
            </tr>
             <tr>
                <td width="185"><?php echo _VEHICLE_MANAGER_ADMIN_CONFIG_SINGLE_SUBCATEGORY_SHOW ; ?>:</td>
                <td width="20"><?php echo "<img src='" . JURI::base() . 'components/com_vehiclemanager/images/info.png' . "' title='" . _VEHICLE_MANAGER_ADMIN_CONFIG_SINGLE_SUBCATEGORY_SHOW_TT_BODY . "' alt='info'>"; ?></td>
                <td class="yesno"><?php echo $lists['single_subcategory_show']['show']; ?><br></td>
            </tr>
            <!-- </table>
            <table class="adminform adminform_32" cellpadding="5px"> -->
          </table>
          <hr/>
          <h2 style="text-align:center;"><?php echo _VEHICLE_MANAGER_HEADER_FRONTEND_COMMON ; ?></h2>
          <hr/>
          <table class="adminform adminform_33" cellpadding="5px">
            <tr>
              <td width="185"><?php echo _VEHICLE_MANAGER_ADMIN_CONFIG_PAGE_ITEMS; ?>:</td>
              <td width="20"><?php echo "<img src='" . JURI::base() . 'components/com_vehiclemanager/images/info.png' . "' title='" . _VEHICLE_MANAGER_ADMIN_CONFIG_PAGE_ITEMS_TT_BODY . "' alt='info'>"; ?></td>
              <td><?php echo $lists['page']['items']; ?></td>
            </tr>
            <tr>
                <td width="185"><?php echo _VEHICLE_MANAGER_ADMIN_CONFIG_SHOW_LOCATION_MAP; ?>:</td>
                <td width="20"><?php echo "<img src='" . JURI::base() . 'components/com_vehiclemanager/images/info.png' . "' title='" . _VEHICLE_MANAGER_ADMIN_CONFIG_SHOW_LOCATION_MAP_TT_BODY . "' alt='info'>"; ?></td>
                <td class="yesno"><?php echo $lists['location_map']; ?></td>
            </tr>
            <tr>
                <td width="185" rowspan="2"><?php echo _VEHICLE_MANAGER_DATE_TIME_FORMAT; ?>:</td>
                <td width="20"><?php echo "<img src='" . JURI::base() . 'components/com_vehiclemanager/images/info.png' . "' title='" . _VEHICLE_MANAGER_DATE_FORMAT .": ". _VEHICLE_MANAGER_DATE . "' alt='info'>"; ?></td>
                <td width="185"><?php echo $lists['date_format'] ?></td>
             </tr>
            <tr>
                <td colspan="6"><hr /></td>
            </tr>
          </table>
          <h2 class="small17 center"><?php echo _VEHICLE_MANAGER_SETTINGS_HEADER_LABEL_PRICE_OPTIONS; ?></h2>
          <table class="adminform adminform_41" cellpadding="5px">
            <tr>
                <td width="185"><?php echo _VEHICLE_MANAGER_ADMIN_CONFIG_PRICE_SHOW; ?>:</td>
                <td width="20"><?php echo "<img src='" . JURI::base() . 'components/com_vehiclemanager/images/info.png' . "' title='" . _VEHICLE_MANAGER_ADMIN_CONFIG_PRICE_SHOW_TT_BODY . "' alt='info'>"; ?></td>
                <td class="yesno"><?php echo $lists['price']['show']; ?></td>
            </tr>
            <tr>
                <td width="185"><?php echo _VEHICLE_MANAGER_ADMIN_CONFIG_PRICE_STRING; ?>:</td>
                <td width="20"><?php echo "<img src='" . JURI::base() . 'components/com_vehiclemanager/images/info.png' . "' title='" . _VEHICLE_MANAGER_ADMIN_CONFIG_PRICE_STRING_DESC . "' alt='info'>"; ?></td>
                <td class="yesno"><?php echo $lists['price']['string']; ?></td>
            </tr>
            <tr>
                <td width="185"><?php echo _VEHICLE_MANAGER_ADMIN_CONFIG_SALE_SEPARATOR_SHOW; ?>:</td>
                <td width="20"><?php echo "<img src='" . JURI::base() . 'components/com_vehiclemanager/images/info.png' . "' title='" . _VEHICLE_MANAGER_ADMIN_CONFIG_SALE_SEPARATOR_SHOW_TT_HEAD . "' alt='info'>"; ?></td>
                <td class="yesno"><?php echo $lists['sale_separator']; ?></td>
            </tr>
            <!-- show sale fraction -->
            <tr>
                <td width="185"><?php echo _VEHICLE_MANAGER_ADMIN_CONFIG_SALE_FRACTION_SHOW; ?>:</td>
                <td width="20"><?php echo "<img src='" . JURI::base() . 'components/com_vehiclemanager/images/info.png' . "' title='" . _VEHICLE_MANAGER_ADMIN_CONFIG_SALE_FRACTION_SHOW_TT_HEAD . "' alt='info'>"; ?></td>
                <td class="yesno"><?php echo $lists['sale_fraction']; ?></td>
            </tr>
            <!-- end show sale fraction -->
            <tr>
                <td width="185"><?php echo _VEHICLE_MANAGER_ADMIN_CONFIG_CURRENCY; ?>:</td>
                <td width="20"><?php echo "<img src='" . JURI::base() . 'components/com_vehiclemanager/images/info.png' . "' title='" . _VEHICLE_MANAGER_ADMIN_CONFIG_CURRENCY_TT_BODY . "' alt='info'>"; ?></td>
                <td class="yesno"><?php echo $lists['currency']; ?></td>
            </tr>
            <tr>
                <td width="185"><?php echo _VEHICLE_MANAGER_PRICE_FORMAT; ?>:</td>
                <td width="20"><?php echo "<img src='" . JURI::base() . 'components/com_vehiclemanager/images/info.png' . "' title='" . _VEHICLE_MANAGER_PRICE_FORMAT .": ". _VEHICLE_MANAGER_PRICE_FORMAT_INFO . "' alt='info'>"; ?></td>
                <td width="185"><?php echo $lists['money_ditlimer'] ?></td>
                <td width="20"><?php echo $lists['patern']; ?></td>
            </tr>
            <tr>
                <td width="185"><?php echo _VEHICLE_MANAGER_PRICE_UNIT_SHOW; ?>:</td>
                <td width="20"><?php echo "<img src='" . JURI::base() . 'components/com_vehiclemanager/images/info.png' . "' title='" . _VEHICLE_MANAGER_PRICE_UNIT_SHOW .": ". _VEHICLE_MANAGER_PRICE_UNIT_SHOW_INFO . "' alt='info'>"; ?></td>
                <td class="yesno"><?php echo $lists['price_unit_show'] ?></td>
            </tr>
            <tr>
                <td colspan="6"><hr /></td>
            </tr>
          </table>

          <!-- ADD CAPTCHA SETTINGS -->
          <h2 class="small17 center"><?php echo _VEHICLE_MANAGER_SETTINGS_HEADER_FRONTEND_CAPTCHA_OPTIONS; ?></h2>
          <table class="adminform adminform_41" cellpadding="5px">
            <tr>
                <td width="185"><?php echo _VEHICLE_MANAGER_ADMIN_CONFIG_REVIEW_CAPTCHA_SHOW; ?>:</td>
                <td><?php echo "<img src='" . JURI::base() . 'components/com_vehiclemanager/images/info.png' . "' title='" . _VEHICLE_MANAGER_ADMIN_CONFIG_REVIEW_CAPTCHA_SHOW_TT_BODY . "' alt='info'>"; ?></td>
                <td class="yesno"><?php echo $lists['review_captcha']['show']; ?></td>
            </tr>
            <tr>
                <td width="185"><?php echo _VEHICLE_MANAGER_ADMIN_CONFIG_CONTACT_CAPTCHA_SHOW; ?>:</td>
                <td><?php echo "<img src='" . JURI::base() . 'components/com_vehiclemanager/images/info.png' . "' title='" . _VEHICLE_MANAGER_ADMIN_CONFIG_CONTACT_CAPTCHA_SHOW_TT_BODY . "' alt='info'>"; ?></td>
                <td class="yesno"><?php echo $lists['contact_captcha']['show']; ?></td>
            </tr>
            <tr>
                <td width="185"><?php echo _VEHICLE_MANAGER_ADMIN_CONFIG_BOOKING_CAPTCHA_SHOW; ?>:</td>
                <td><?php echo "<img src='" . JURI::base() . 'components/com_vehiclemanager/images/info.png' . "' title='" . _VEHICLE_MANAGER_ADMIN_CONFIG_BOOKING_CAPTCHA_SHOW_TT_BODY . "' alt='info'>"; ?></td>
                <td class="yesno"><?php echo $lists['booking_captcha']['show']; ?></td>
            </tr>
          </table>
            <tr>
              <td colspan="6"><hr /></td>
            </tr>
            <!-- GOOGLE and OPEN MAP SETTINGS -->
            <h2 class="small17 center" ><?php echo _VEHICLE_MANAGER_ADMIN_CONFIG_GOOGLE_OPENMAP; ?></h2>
            <table class="adminform adminform_43">
               <tr>
                  <td width="185"><?php echo _VEHICLE_MANAGER_ADMIN_CONFIG_GOOGLE_OPENMAP_TEXT; ?>:</td>
                  <td><?php echo "<img src='" . JURI::base() . 'components/com_vehiclemanager/images/info.png' . "' title='" . _VEHICLE_MANAGER_ADMIN_CONFIG_GOOGLE_OPENMAP_HELP . "' alt='info'>"; ?></td>
                  <td class="yesno"><?php echo $lists['google_openmap']['show']; ?></td>
                </tr>
            </table>
            <tr>
                <td colspan="6"><hr /></td>
            </tr>
            <!-- ADD GOOGLE MAP API KEY SETTINGS -->
            <h2 class="small17 center"><?php echo _VEHICLE_MANAGER_SETTINGS_GOOGLE_MAP; ?></h2>
            <table class="adminform adminform_41" cellpadding="5px">
              <tr>
                  <td width="185"><?php echo _VEHICLE_MANAGER_ADMIN_CONFIG_API_KEY; ?>:</td>
                  <td width="20"><?php echo "<img src='" . JURI::base() . 'components/com_vehiclemanager/images/info.png' . "' title='" . _VEHICLE_MANAGER_ADMIN_CONFIG_API_KEY_TT_BODY . "' alt='info'>"; ?></td>
                  <td class="yesno"><?php echo $lists['api_key']; ?></td>
              </tr>
            </table>
            <!-- END ADD GOOGLE MAP API KEY SETTINGS -->
            <tr>
              <td colspan="6"><hr /></td>
            </tr>

            <h2 class="center"><?php echo _VEHICLE_MANAGER_FRONTEND_SETTINGS_REDIRECT_SETTINGS; ?></h2>
            <hr>
            <table class="adminform adminform_41" cellpadding="5px">
              <tr>
                  <td width="185"><?php echo _VEHICLE_MANAGER_FRONTEND_SETTINGS_REDIRECT_SETTINGS_AFTER_RENT_REQUEST; ?>:</td>
                  <td width="20"><?php echo "<img src='" . JURI::base() . 'components/com_vehiclemanager/images/info.png' . "' title='" . _VEHICLE_MANAGER_FRONTEND_SETTINGS_REDIRECT_SETTINGS_AFTER_RENT_REQUEST .": ". _VEHICLE_MANAGER_FRONTEND_SETTINGS_REDIRECT_SETTINGS_AFTER_RENT_REQUEST_HELP . "' alt='info'>"; ?></td>
                  <td width="185"><?php echo $lists['redirect_rent']; ?></td>
                  <td width="20"><?php echo $lists['patern_rent']; ?></td>
              </tr>
              <tr>
                  <td width="185"><?php echo _VEHICLE_MANAGER_FRONTEND_SETTINGS_REDIRECT_SETTINGS_AFTER_BUY_REQUEST; ?>:</td>
                  <td width="20"><?php echo "<img src='" . JURI::base() . 'components/com_vehiclemanager/images/info.png' . "' title='" . _VEHICLE_MANAGER_FRONTEND_SETTINGS_REDIRECT_SETTINGS_AFTER_BUY_REQUEST .": ". _VEHICLE_MANAGER_FRONTEND_SETTINGS_REDIRECT_SETTINGS_AFTER_BUY_REQUEST_HELP . "' alt='info'>"; ?></td>
                  <td width="185"><?php echo $lists['redirect_sale'] ?></td>
                  <td width="20"><?php echo $lists['patern_sale']; ?></td>
              </tr>
            </table>
            <hr>

            <h2 class="center"><?php echo _VEHICLE_MANAGER_SETTINGS_ORDER_FIELD; ?></h2>
            <hr>
            <table class="adminform adminform_41" cellpadding="5px">
              <tr>
                <td width="185"><?php echo _VEHICLE_MANAGER_ADMIN_CONFIG_SHOW_ORDER_BY_FORM; ?>:</td>
                <td><?php echo "<img src='" . JURI::base() . 'components/com_vehiclemanager/images/info.png' . "' title='" . _VEHICLE_MANAGER_ADMIN_CONFIG_SHOW_ORDER_BY_FORM_TT_BODY . "' alt='info'>"; ?></td>
                <td class="yesno"><?php echo $lists['show_order_by']['show']; ?></td>
                <td width="185"><?php echo _VEHICLE_MANAGER_ADMIN_CONFIG_SHOW_ORDER_BY_FORM_REGISTRATIONLEVEL; ?>:</td>
                <td><?php echo "<img src='" . JURI::base() . 'components/com_vehiclemanager/images/info.png' . "' title='" . _VEHICLE_MANAGER_ADMIN_CONFIG_SHOW_ORDER_BY_FORM_REGISTRATIONLEVEL_TT_BODY . "' alt='info'>"; ?></td>
                <td><?php echo $lists['show_order_by']['registrationlevel']; ?><br><br></td>
              </tr>
              <tr>
                  <td width="185"><?php echo _VEHICLE_MANAGER_SETTINGS_SELECT_ORDER_DEFAULT; ?>:</td>
                  <td width="20"><?php echo "<img src='" . JURI::base() . 'components/com_vehiclemanager/images/info.png' . "' title='" . _VEHICLE_MANAGER_SETTINGS_SELECT_ORDER_DEFAULT .": ". _VEHICLE_MANAGER_SETTINGS_ORDER_DEFAULT_HELP . "' alt='info'>"; ?></td>
                  <td width="185"><?php echo $lists['order_by_default']; ?></td>
              </tr>
            </table>

            <hr>

      <?php
            echo JHtml::_('bootstrap.endTab');
            echo JHtml::_('bootstrap.addTab', 'configurePane', 'panel_2_configurePane', _VEHICLE_MANAGER_SETTINGS_TAB_LABEL_ADMINISTRATOR_SETTINGS );
      ?>
          <!--Show mandatory options-->
          <h2 align="center"><?php echo _VEHICLE_MANAGER_MANDATORY_FIELDS_REQUIRE; ?></h2>
          <hr>
          <table class="adminform adminform_40" cellpadding="5px">
            <tr>
              <td width="185"><?php echo _VEHICLE_MANAGER_MANDATORY_YEAR_ISSUE_FIELD_REQUIRE; ?>:</td>
              <td width="20"><?php echo "<img src='" . JURI::base() . 'components/com_vehiclemanager/images/info.png' . "' title='" .  _VEHICLE_MANAGER_MANDATORY_YEAR_ISSUE_FIELD_REQUIRE_HELP . "' alt='info'>"; ?></td>
              <td  class="yesno"><?php echo $lists['mandatory_year_issue_field_require']; ?></td>
            </tr>
            <tr>
              <td width="185"><?php echo _VEHICLE_MANAGER_MANDATORY_MILEAGE_FIELD_REQUIRE; ?>:</td>
              <td width="20"><?php echo "<img src='" . JURI::base() . 'components/com_vehiclemanager/images/info.png' . "' title='" .  _VEHICLE_MANAGER_MANDATORY_MILEAGE_FIELD_REQUIRE_HELP . "' alt='info'>"; ?></td>
              <td class="yesno"><?php echo $lists['mandatory_mileage_field_require']; ?></td>
            </tr>
            <tr>
              <td width="185"><?php echo _VEHICLE_MANAGER_MANDATORY_PRICE_FIELD_REQUIRE; ?>:</td>
              <td width="20"><?php echo "<img src='" . JURI::base() . 'components/com_vehiclemanager/images/info.png' . "' title='" .  _VEHICLE_MANAGER_MANDATORY_PRICE_FIELD_REQUIRE_HELP . "' alt='info'>"; ?></td>
              <td class="yesno"><?php echo $lists['mandatory_price_field_require']; ?></td>
            </tr>
          </table>
          <hr>
          <!--END Show mandatory options-->
          <h2 align="center"><?php echo _VEHICLE_MANAGER_SETTINGS_HEADER_LABEL_EDOCUMENT_OPTIONS; ?></h2>
          <hr>
          <table class="adminform adminform_40" cellpadding="5px">
              <tr>
                  <td width="185"><?php echo _VEHICLE_MANAGER_ADMIN_CONFIG_EDOCUMENTS_DOWNLOAD; ?>:</td>
                  <td width="20"><?php echo "<img src='" . JURI::base() . 'components/com_vehiclemanager/images/info.png' . "' title='" . _VEHICLE_MANAGER_ADMIN_CONFIG_EDOCUMENTS_DOWNLOAD_TT_BODY . "' alt='info'>"; ?></td>
                  <td class="yesno"><?php echo $lists['edocs']['allow']; ?></td>
              </tr>

              <tr>
                  <td width="185"><?php echo _VEHICLE_MANAGER_ALLOWED_EXTS; ?>:</td>
                  <td width="20"></td>
                  <td colspan="4" ><?php echo $lists['allowed_exts']; ?></td>
              </tr>
              <tr>
                  <td width="185"><?php echo _VEHICLE_MANAGER_ADMIN_CONFIG_EDOCUMENTS_DOWNLOAD_LOCATION; ?>:</td>
                  <td width="20"><?php echo "<img src='" . JURI::base() . 'components/com_vehiclemanager/images/info.png' . "' title='" . _VEHICLE_MANAGER_ADMIN_CONFIG_EDOCUMENTS_DOWNLOAD_LOCATION_TT_BODY . "' alt='info'>"; ?></td>
                  <td><?php echo $lists['edocs']['location']; ?></td>
              </tr>
                  <td colspan="6"><hr /></td>
              </tr>
          </table>
          <h2 align="center"><?php echo _VEHICLE_MANAGER_SETTINGS_HEADER_LABEL_VIDEOTRATCK_OPTIONS; ?></h2>
          <hr>
          <table class="adminform adminform_40">
              <tr>
                  <td width="185"><?php echo _VEHICLE_MANAGER_ADMIN_CONFIG_VIDEO; ?>:</td><td></td>
                  <td class="yesno"><?php echo $lists['videos_tracks']['show']; ?></td>
              </tr>
              <tr>
                <td colspan="2"><?php echo _VEHICLE_MANAGER_ALLOWED_EXTS_VIDEO; ?>:</td>
                <td><?php echo $lists['allowed_exts_video']; ?></td>
              </tr>
              <tr>
                <td colspan="2"><?php echo _VEHICLE_MANAGER_ALLOWED_EXTS_TRACK; ?>:</td>
                <td><?php echo $lists['allowed_exts_track']; ?></td>
              </tr>
              <tr>
                  <td width="185"><?php echo _VEHICLE_MANAGER_ADMIN_CONFIG_VIDEOS_DOWNLOAD_LOCATION; ?>:</td><td></td>
                  <td class="yesno"><?php echo $lists['videos']['location']; ?></td>
              </tr>
              <tr>
              <tr>
                  <td width="185"><?php echo _VEHICLE_MANAGER_ADMIN_CONFIG_TRACKS_DOWNLOAD_LOCATION; ?>:</td><td></td>
                  <td class="yesno"><?php echo $lists['tracks']['location']; ?></td>
              </tr>
          </table>
          <hr />
          <h2 align="center"><?php echo _VEHICLE_MANAGER_SETTINGS_COMMON_SETTINGS; ?></h2>
          <hr />
          <table class="adminform adminform_42" cellpadding="5px">

            <tr>
                <td><?php echo _VEHICLE_MANAGER_APPRAVE_PUBLISHED; ?></td>
                <td><?php echo "<img src='" . JURI::base() . 'components/com_vehiclemanager/images/info.png' . "' title='" . _VEHICLE_MANAGER_ADMIN_CONFIG_APPROVE_ON_ADD_TT_BODY . "' alt='info'>"; ?></td>
                <td class="yesno"><?php echo $lists['approve_review']['show']; ?></td>
            </tr>
            </table>
            <table class="adminform adminform_42" cellpadding="5px">
              <tr>
                  <td width="185">
                  <span class="tooltip_link" rel="tooltip" data-placement="top" data-toggle="tooltip"
                   title='<?php echo _VEHICLE_MANAGER_ADMIN_THUMBNAIL_TT_BODY;?>'>
                  <?php echo _VEHICLE_MANAGER_ADMIN_THUMBNAIL_SETTINGS; ?>:</span></td>
                  <td width="20"><?php echo "<img src='" . JURI::base() . 'components/com_vehiclemanager/images/info.png' . "' title='" . _VEHICLE_MANAGER_ADMIN_THUMBNAIL_TT_BODY . "' alt='info'>"; ?></td>
                  <td class="yesno"><?php echo $lists['thumb_param']['show']; ?></td>
                  <td></td>
              </tr>
              <tr class="hw">
                <td width="185"><?php echo _VEHICLE_MANAGER_ADMIN_CONFIG_FOTOUPLOAD_SIZE; ?>:</td>
                <td width="20"><?php echo "<img src='" . JURI::base() . 'components/com_vehiclemanager/images/info.png' . "' title='" . _VEHICLE_MANAGER_ADMIN_CONFIG_FOTOUPLOAD_SIZE_TT_BODY . "' alt='info'>"; ?></td>
                <td width="50">Width: &nbsp;<?php echo $lists['fotoupload']['width']; ?></td>
                <td width="50">Height: &nbsp;<?php echo $lists['fotoupload']['high']; ?></td>
              </tr>

              <!-- // Add settings "Rotate image according to EXIF-orientation data" -->
              <tr>
                  <td width="185">
                  <span class="tooltip_link" rel="tooltip" data-placement="top" data-toggle="tooltip"
                   title='<?php echo _VEHICLE_MANAGER_ADMIN_ROTATE_IMAGE_SETTINGS_TT_BODY;?>'>
                  <?php echo _VEHICLE_MANAGER_ROTATE_IMAGE_SETTINGS; ?>:</span></td>
                  <td width="20"><?php echo "<img src='" . JURI::base() . 'components/com_vehiclemanager/images/info.png' . "' title='" . _VEHICLE_MANAGER_ADMIN_ROTATE_IMAGE_SETTINGS_TT_BODY . "' alt='info'>"; ?></td>
                  <td class="yesno"><?php echo $lists['rotate_img']; ?></td>
                  <td></td>
              </tr>

              <tr>
                <td width="185"><?php echo _VEHICLE_MANAGER_ALLOWED_EXTS_IMG; ?>:</td>
                <td width="20" ></td>
                <td colspan="2" ><?php echo $lists['allowed_exts_img']; ?></td>
              </tr>
            </table>
            <hr />
        <table class="adminform adminform_43" cellpadding="5px">
          <hr />
          <h2 align="center" id="special_price" ><?php echo _VEHICLE_MANAGER_RENT_SPECIAL_PRICE_AND_RENT_TIME; ?></h2>
          <hr />
          <tr>
            <td width="185"><?php echo _VEHICLE_MANAGER_RENT_SPECIAL_PRICE_YES_NO; ?>:</td>
            <td width="20"><?php echo "<img src='" . JURI::base() . 'components/com_vehiclemanager/images/info.png' . "' title='"._VEHICLE_MANAGER_RENT_SPECIAL_PRICE_YES_NO_HELP."' alt='info'>"; ?></td>
            <td class="yesno"><?php echo $lists['special_price']['show']; ?></td>
          </tr>
        </table>
        <hr />
        <table class="adminform adminform_43">
          <h2 align="center" id="special_price" ><?php echo _VEHICLE_MANAGER_HOW_COUNTRY_REGION_CITY_USE; ?></h2>
          <hr />
          <tr>
            <td width="185"><?php echo _VEHICLE_MANAGER_SHOW_COUNTRY_REGION_CITY_AS_TEXT_FIELD; ?>:</td>
            <td width="20"><?php echo "<img src='" . JURI::base() . 'components/com_vehiclemanager/images/info.png' . "' title='"._VEHICLE_MANAGER_SHOW_COUNTRY_REGION_CITY_AS_TEXT_FIELD_HELP."' alt='info'>"; ?></td>
            <td class="yesno"><?php echo $lists['show_country_region_city_as_text_field']; ?></td>
          </tr>
        </table>

      <?php

            echo JHtml::_('bootstrap.endTab');
            echo JHtml::_('bootstrap.addTab', 'configurePane', 'panel_6_configurePane', _VEHICLE_MANAGER_SETTINGS_SEARCH );

      ?>
      <!-- Options for search form -->
      <!-- ADD SETTINGS LAYOUT SEARCH RESULTS  -->
      <h2 class="center"><?php echo _VEHICLE_MANAGER_SETTINGS_HEADER_LAYOUT_SEARCH_RESULTS; ?></h2>
      <hr>
      <table class="adminform adminform_41" cellpadding="5px">
        <tr>
          <td width="185"><?php echo _VEHICLE_MANAGER_ADMIN_CONFIG_SHOW_GOOGLE_MAP; ?>:</td>
          <td><?php echo "<img src='" . JURI::base() . 'components/com_vehiclemanager/images/info.png' . "' title='" . _VEHICLE_MANAGER_ADMIN_CONFIG_SHOW_GOOGLE_MAP_TT_BODY . "' alt='info'>"; ?></td>
          <td class="yesno"><?php echo $lists['show_map']['show']; ?></td>
          <td width="185"><?php echo _VEHICLE_MANAGER_ADMIN_CONFIG_SHOW_GOOGLE_MAPS_REGISTRATIONLEVEL; ?>:</td>
          <td><?php echo "<img src='" . JURI::base() . 'components/com_vehiclemanager/images/info.png' . "' title='" . _VEHICLE_MANAGER_ADMIN_CONFIG_SHOW_GOOGLE_MAP_REGISTRATIONLEVEL_TT_BODY . "' alt='info'>"; ?></td>
          <td><?php echo $lists['show_map']['registrationlevel']; ?><br><br></td>
        </tr>
      </table>
      <hr>

      <!-- <h2 class="center"><?php echo _VEHICLE_MANAGER_SETTINGS_SEARCH_LAYOUT; ?></h2>
      <hr>
      <table class="adminform adminform_41" cellpadding="5px">
        <tr>
          <td width="185"><?php echo _VEHICLE_MANAGER_SETTINGS_DEFAULT_SEARCH_LAYOUT; ?>:<td>
          <td><?php echo "<img src='" . JURI::base() . 'components/com_vehiclemanager/images/info.png' . "' title='" . _VEHICLE_MANAGER_SETTINGS_DEFAULT_SEARCH_LAYOUT_TT_BODY . "' alt='info'>"; ?></td>
          <td><?php echo $lists['default_search_layout']; ?></td>
        </tr>
      </table>
      <hr> -->
      <h2 class="center"><?php echo _VEHICLE_MANAGER_SEARCH_FORM_SETTINGS; ?></h2>
      <hr>
      <table class="adminform adminform_41" cellpadding="5px">
        <tr>
          <td><?php echo _VEHICLE_MANAGER_SHOW_SEARCH_FORM_ON_NOTHING_FOUND_PAGE; ?>:</td>
          <td><?php echo "<img src='" . JURI::base() . 'components/com_vehiclemanager/images/info.png' . "' title='" . _VEHICLE_MANAGER_SHOW_SEARCH_FORM_ON_NOTHING_FOUND_PAGE . "' alt='info'>"; ?></td>
          <td class="yesno"><?php echo $lists['search_form_nothing_found_page_show']; ?><br></td>
        </tr>
        <tr>
          <td><?php echo _VEHICLE_MANAGER_SHOW_SEARCH_FORM_ON_RESULT_SEARCH_PAGE; ?>:</td>
          <td><?php echo "<img src='" . JURI::base() . 'components/com_vehiclemanager/images/info.png' . "' title='" . _VEHICLE_MANAGER_SHOW_SEARCH_FORM_ON_RESULT_SEARCH_PAGE_HELP . "' alt='info'>"; ?></td>
          <td class="yesno"><?php echo $lists['search_form_on_search_page_result_show']; ?><br></td>
        </tr>
        <!-- show available for rent -->
        <tr>
          <td><?php echo _VEHICLE_MANAGER_SEARCH_AVAILABLE_FOR_RENT_SHOW; ?>:</td>
          <td><?php echo "<img src='" . JURI::base() . 'components/com_vehiclemanager/images/info.png' . "' title='" . _VEHICLE_MANAGER_SEARCH_AVAILABLE_FOR_RENT_SHOW_HELP . "' alt='info'>"; ?></td>
          <td class="yesno"><?php echo $lists['available_for_rent_show_select']; ?></td>
        </tr>
        <!-- end show available for rent -->
      </table>
      <hr>

      <h2 class="center"><?php echo _VEHICLE_MANAGER_SEARCH_BUTTON_SETTINGS; ?></h2>
      <hr>
      <table class="adminform adminform_41" cellpadding="5px">
        <!-- Show search button -->
        <tr>
          <td width="185"><?php echo _VEHICLE_MANAGER_SEARCH_BUTTON_SHOW; ?>:</td>
          <td><?php echo "<img src='" . JURI::base() . 'components/com_vehiclemanager/images/info.png' . "' title='" . _VEHICLE_MANAGER_SEARCH_BUTTON_HELP . "' alt='info'>"; ?></td>
          <td class="yesno"><?php echo $lists['search_button']['show']; ?></td>
          <td width="185"><?php echo _VEHICLE_MANAGER_SEARCH_BUTTON_SHOW_REGISTRATIONLEVEL; ?>:</td>
          <td><?php echo "<img src='" . JURI::base() . 'components/com_vehiclemanager/images/info.png' . "' title='" . _VEHICLE_MANAGER_SEARCH_BUTTON_SHOW_REGISTRATIONLEVEL_HELP . "' alt='info'>"; ?></td>
          <td><?php echo $lists['search_button']['registrationlevel']; ?><br><br></td>
        </tr>
        <!-- End Show search button -->
      </table>
      <?php
            echo JHtml::_('bootstrap.endTab');
            echo JHtml::_('bootstrap.endTabSet');
      ?>
        <input type="hidden" name="option" value="<?php echo $option; ?>" />
        <input type="hidden" name="task" value="config_save" />
      </form>
      <?php
  }

  static function about()
  {
      global $mosConfig_live_site, $mainframe;
      // for 1.6
      global $doc, $css;
      $doc->addStyleSheet($css);
      // --
      $html = "<div class='vehicle_manager_caption' ><i class='fa fa-car'></i> " . _VEHICLE_MANAGER_ADMIN_ABOUT . "</div>";
      $app = JFactory::getApplication();
      $app->JComponentTitle = $html;
      ?>
      <div id="overDiv" style="position:absolute; visibility:hidden; z-index:10000;"></div>

      <form action="index.php"  class="veh_about veh_dd_tabs"  method="post" name="adminForm" id="adminForm">

      <?php
        echo JHtml::_('bootstrap.startTabSet', 'aboutPane', array('active' => 'panel_1_aboutPane'));
        echo JHtml::_('bootstrap.addTab', 'aboutPane', 'panel_1_aboutPane', _VEHICLE_MANAGER_ADMIN_ABOUT_ABOUT);
      ?>
   <div class="adminform_44">
          <table class="adminform">
              <tr>
                  <td width="80%"><h3><?PHP echo _VEHICLE_MANAGER__HTML_ABOUT; ?></h3><?PHP echo _VEHICLE_MANAGER__HTML_ABOUT_INTRO; ?></td>
                  <td width="20%"><img src="../components/com_vehiclemanager/images/vm_logo.png" align="right" alt="Vehicle" /></td>
              </tr>
          </table>
  </div>
      <?php
//******************************   tab--2 about   **************************************
      if (version_compare(JVERSION, "3.0.0", "ge"))
      echo JHtml::_('bootstrap.endTab');
      echo JHtml::_('bootstrap.addTab', 'aboutPane', 'panel_2_aboutPane', _VEHICLE_MANAGER_ADMIN_ABOUT_RELEASENOTE );  

      include_once("./components/com_vehiclemanager/doc/releasenote.php");

//******************************   tab--3 about--changelog.txt   ***********************

      echo JHtml::_('bootstrap.endTab');
      echo JHtml::_('bootstrap.addTab', 'aboutPane', 'panel_3_aboutPane', _VEHICLE_MANAGER_ADMIN_ABOUT_CHANGELOG );  

      include_once("./components/com_vehiclemanager/doc/changelog.html");

//End Pane
      echo JHtml::_('bootstrap.endTab');
      echo JHtml::_('bootstrap.endTabSet');

      ?>

      </form>
              <?php
          }

          static function showImportResult($table, $option)
          {
              global $my, $mosConfig_live_site, $mainframe;
              // for 1.6
              global $doc;
              $doc->addScript($mosConfig_live_site . '/components/com_vehiclemanager/includes/functions.js');
              // --
              ?>
      <form action="index.php" method="post" name="adminForm"  id="adminForm">
          <table cellpadding='4' cellspacing='0' border='1' width='100%' class='adminform  adminform_45'>
              <tr>
                  <td align="center">#</td>
                  <td align="center"><?php echo _VEHICLE_MANAGER_LABEL_VEHICLEID; ?></td>
                  <td align="center"><?php echo _VEHICLE_MANAGER_LABEL_TITLE; ?></td>
                  <td align="center"><?php echo _VEHICLE_MANAGER_LABEL_MODEL; ?></td>
                  <td align="center"><?php echo _VEHICLE_MANAGER_LABEL_PRICE; ?></td>
                  <td align="center"><?php echo _VEHICLE_MANAGER_LABEL_STATUS; ?></td>
              </tr>

                          <?php foreach ($table as $entry) {
                              ?>
                  <tr>
                      <td align="center"><?php echo $entry[0] + 1; ?></td>
                      <td><?php echo $entry[1]; ?></td>
                      <td><?php echo $entry[2]; ?></td>
                      <td><?php echo $entry[3]; ?></td>
                      <td><?php echo $entry[4]; ?></td>
                      <td><?php echo $entry[5]; ?></td>
                  </tr>
                  <?php
              }
              ?>
          </table>
          <input type="hidden" name="option" value="<?php echo $option; ?>" />
          <input type="hidden" name="task" value="cancel" />
      </form>

      <?php
  }

  static function showExportResult($InformationArray, $option)
  {
      $html = "<div class='vehicle_manager_caption' ><i class='fa fa-car'></i> " . _VEHICLE_MANAGER_ADMIN_FEATURED_MANAGER . "</div>";
      $app = JFactory::getApplication();
      $app->JComponentTitle = $html;

      ?>
      <form action="index.php" method="post" name="adminForm"  id="adminForm">
          <div id="overDiv" style="position:absolute; visibility:hidden; z-index:10000;"></div>
          <table border="0" class="adminheading" cellpadding="0" cellspacing="0" width="100%" class="adminform adminform_46">
              <tr valign="middle">
                  <th class="config"><?php echo _VEHICLE_MANAGER_SHOW_IMPEXP_RESULT; ?></th>
                  <td align="right"></td>
              </tr>
          </table>

      <?php echo _VEHICLE_MANAGER_SHOW_IMPEXP_RESULT_DOWNLOAD; ?>  <br />
          <a href="<?php echo $InformationArray['urlBase'] . $InformationArray['out_file']; ?>" target="blank"><?php echo $InformationArray['urlBase'] . $InformationArray['out_file']; ?></a>
          <br />
      <?php echo _VEHICLE_MANAGER_SHOW_IMPEXP_RESULT_REMEMBER; ?>  <br />
          <input type="hidden" name="option" value="<?php echo $option; ?>" />
          <input type="hidden" name="task" value="cancel" />
      </form>
          <?php
      }

      static function showFeaturedManager($features, $pageNav)
      {
          global $my, $mosConfig_live_site, $mainframe, $templateDir;
          // for 1.6
          global $doc, $css;
          $doc->addStyleSheet($css);
          $doc->addScript($mosConfig_live_site . '/components/com_vehiclemanager/includes/functions.js');

          $html = "<div class='vehicle_manager_caption' ><i class='fa fa-car'></i> " . _VEHICLE_MANAGER_ADMIN_FEATURED_MANAGER . "</div>";
          $app = JFactory::getApplication();
          $app->JComponentTitle = $html;
          ?>
      <form action="index.php" method="post"  name="adminForm" id="adminForm">

      <?php
      // Features tabs
      echo JHtml::_('bootstrap.startTabSet', 'featurePane', array('active' => 'panel_1_featurePane'));
      echo JHtml::_('bootstrap.addTab', 'featurePane', 'panel_1_featurePane', _VEHICLE_MANAGER_ADMIN_CONFIG_MANAGER_FEATURE_CATEGORIES_TT_HEAD);      
      ?>
    <div class="table_07">
      <table width="100%">
          <tr>
          <?php $feature_cat = '<input type="text" name="featuredmanager_placeholder" value="' . $GLOBALS['vehiclemanager_configuration']['featuredmanager']['placeholder'] . '" class="inputbox" size="50" maxlength="500" title=""/>'; ?>
            <td width="185"><?php echo _VEHICLE_MANAGER_ADMIN_CONFIG_MANAGER_FEATURE_CATEGORIES; ?>:</td>
            <td width="20"><?php echo "<img src='" . JURI::base() . 'components/com_vehiclemanager/images/info.png' . "' title='" . _VEHICLE_MANAGER_ADMIN_CONFIG_MANAGER_FEATURE_CATEGORIES_TT_BODY . "' alt='info'>"; ?></td>
            <td class="yesno"><?php echo $feature_cat; ?></td>
         </tr>
       </table>
    </div>
      <?php
      echo JHtml::_('bootstrap.endTab');
      echo JHtml::_('bootstrap.addTab', 'featurePane', 'panel_2_featurePane', _VEHICLE_MANAGER_LABEL_FEATURED_MANAGER_FEATURE );

      ?>
          <table width="100%"  class="table_08">
              <tr>
                  <td>
                      <div class="btn-group pull-right hidden-phone">
                          <label for="limit" class="element-invisible">
                                <?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC'); ?>
                          </label>
                      <?php echo $pageNav->getLimitBox(); ?>
                      </div>
                  </td>
              </tr>
          </table>
          <table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminlist adminlist_10">
              <tr>
                  <th width="5" align="center"><input type="checkbox" name="toggle" onClick="vm_checkAll(this<?php //echo count( $features );   ?>);" /></th>
                  <th align = "center" class="title" width="45%" nowrap="nowrap"><?php echo _VEHICLE_MANAGER_LABEL_FEATURED_MANAGER_FEATURE; ?></th>
                  <th align = "center" class="title" width="35%" nowrap="nowrap"><?php echo _VEHICLE_MANAGER_LABEL_FEATURED_MANAGER_CATEGORY; ?></th>
                  <th align = "center" class="title" width="15%" nowrap="nowrap"><?php echo _VEHICLE_MANAGER_LABEL_FEATURED_MANAGER_PUBLISHED; ?></th>
              </tr>

      <?php
      $i = 0;
      foreach ($features as $feature) {
          $task = $feature->published ? 'unpublish' : 'publish';
          $alt = $feature->published ? 'Unpublish' : 'Publish';
          $img = $feature->published ? 'tick.png' : 'publish_x.png';

              if($feature->name):?>
                  <tr>
                      <td align="center"><?php echo mosHTML::idBox($i, $feature->id, false, 'vid'); ?></td>
                      <td><?php echo $feature->name; ?></td>
                      <td><?php echo $feature->categories; ?></td>
                      <td align="center">
                          <a href="javascript: void(0);" onClick="return listItemTask('cb<?php echo $i; ?>','<?php echo $task; ?>')">
          <?php
          if (version_compare(JVERSION, "1.6.0", "lt"))
          {
              ?>
                     <img src="<?php echo $mosConfig_live_site . "/administrator/images/" . $img; ?>" width="12" height="12" border="0" alt="<?php echo $alt; ?>" />
              <?php
          } else
          {
              ?>
                     <img src="<?php echo $templateDir . "/images/admin/" . $img; ?>" width="12" height="12" border="0" alt="<?php echo $alt; ?>" />
              <?php
          }
          ?>
                        </a>
                  </tr>
          <?php endif;
          $i++;
      }
      ?>
              <tr><td colspan = "13"><?php echo $pageNav->getListFooter(); ?></td></tr>
          </table>
      <?php
            echo JHtml::_('bootstrap.endTab');
            echo JHtml::_('bootstrap.endTabSet');
      ?>

          <input type="hidden" name="option" value="com_vehiclemanager" />
          <input type="hidden" name="section" value="featured_manager" />
          <!--<input type="hidden" name="option" value="<?php echo $option; ?>" />-->
          <input type="hidden" name="task" value="" />
          <input type="hidden" value="0" name="boxchecked">
      </form>
      <?php
  }

  static function editFeaturedManager($row, $lists)
  {
      global $mosConfig_live_site;
      global $doc, $css;
      $doc->addStyleSheet($css);
      $doc->addScript($mosConfig_live_site . '/components/com_vehiclemanager/includes/functions.js');

      $html = "<div class='vehicle_manager_caption' ><i class='fa fa-car'></i> " . _VEHICLE_MANAGER_ADMIN_FEATURED_MANAGER . "</div>";
      $app = JFactory::getApplication();
      $app->JComponentTitle = $html;
      ?>

      <form action="index.php" method="post" name="adminForm"  id="adminForm" enctype="multipart/form-data">
          <table >
              <tr>
                  <th  class="vehicle_manager_caption" align="left"><?php echo $row->id ? _VEHICLE_HEADER_EDIT : _VEHICLE_HEADER_ADD; ?> <?php echo _VEHICLE_MANAGER_LABEL_FEATURED_MANAGER_FEATURE; ?> </th>
              </tr>
          </table>

          <table width="100%"  class="table_9">
              <tr>
                  <td valign="top">
                      <table class="adminform adminform_47" style="height: 150px;">
                          <tr>
                              <th colspan="3"><?php echo _VEHICLE_CATEGORIES__DETAILS; ?></th>
                          </tr>
                          <tr>
                              <td><?php echo _VEHICLE_MANAGER_LABEL_FEATURED_MANAGER_FEATURE; ?>:</td>
                              <td colspan="2"><input class="text_area" type="text" name="name" value="<?php echo $row->name; ?>" size="50" maxlength="250" title="A short name to appear in menus" /></td>
                          </tr>
                          <tr>
                              <td><?php echo _VEHICLE_MANAGER_LABEL_FEATURED_MANAGER_CATEGORY; ?>:</td>
                              <td colspan="2"><?php echo $lists['categories']; ?></td>
                          </tr>
                          <tr>
                              <td><?php echo _VEHICLE_MANAGER_LABEL_FEATURED_MANAGER_PUBLISHED; ?>:</td>
                              <td colspan="2"><?php echo $lists['published']; ?></td>
                          </tr>
                          <tr>
                              <td><?php echo _VEHICLE_MANAGER_LABEL_FEATURED_MANAGER_IMAGE; ?>:</td>
                              <td>
                                  <input class="inputbox" type="file" name="image_link"  size="50" maxlength="250" /><br>
                                  <i><?php echo _VEHICLE_MANAGER_LABEL_FEATURED_MANAGER_RECOMMENDED_IMAGE; ?></i>
                              </td>
                          </tr>
                          <tr>
                              <td colspan="2">
      <?php
      if ($row->image_link != '')
      {
        echo _VEHICLE_MANAGER_LABEL_FEATURED_MANAGER_REMOVE; ?>:
            <input type="checkbox" name="del_main_photo" value="<?php echo $row->image_link; ?>" />
            <img alt="photo" src="<?php echo "$mosConfig_live_site/components/com_vehiclemanager/featured_ico/$row->image_link"; ?>"></img>
      <?php } else echo "&nbsp"; ?>
                              </td>
                          </tr>
                      </table>
                  </td>
              </tr>
          </table>
          <input type="hidden" name="option" value="com_vehiclemanager" />
          <input type="hidden" name="section" value="featured_manager" />
          <input type="hidden" name="task" value="" />
          <input type="hidden" name="id" value="<?php echo $row->id; ?>" />
          <input type="hidden" name="sectionid" value="com_vehiclemanager" />
      </form>
      <?php
  }

  static function showLanguageManager($const_languages, $pageNav, $search)
  {
      global $my, $mosConfig_live_site, $mainframe, $templateDir;
      // for 1.6
      global $doc, $css;
      $doc->addStyleSheet($css);
      $doc->addScript($mosConfig_live_site . '/components/com_vehiclemanager/includes/functions.js');
      $html = "<div class='vehicle_manager_caption' ><i class='fa fa-car'></i> " . _VEHICLE_MANAGER_ADMIN_LANGUAGE_MANAGER . "</div>";
      $app = JFactory::getApplication();
      $app->JComponentTitle = $html;
      ?>
      <form action="index.php" method="post" name="adminForm" id="adminForm">
          <table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminlist language_manager">
              <tr>
                  <td><?php echo _VEHICLE_MANAGER_SHOW_SEARCH; ?></td>
                  <td><input type="text" name="search_const" value="<?php echo $search['const']; ?>" class="inputbox input-medium" onChange="document.adminForm.submit();" /></td>
                  <td><input type="text" name="search_const_value" value="<?php echo $search['const_value']; ?>" class="inputbox input-medium" onChange="document.adminForm.submit();" /></td>
                  <td><?php echo $search['languages']; ?></td>
                  <td><?php echo $search['sys_type']; ?></td>
      <?php if (version_compare(JVERSION, "3.0.0", "ge"))
      {
          ?>
                      <td>
                          <div class="btn-group pull-right hidden-phone">
                              <label for="limit" class="element-invisible"><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC'); ?></label>
                                      <?php echo $pageNav->getLimitBox(); ?>
                          </div>
                      </td>
      <?php } ?>
              </tr>
              <tr>
                  <th width="5" align="center"></th>
                  <th align = "center" class="title" width="30%" nowrap="nowrap"><?php echo _VEHICLE_MANAGER_ADMIN_LANGUAGE_MANAGER_CONST; ?></th>
                  <th align = "center" class="title" width="30%" nowrap="nowrap"><?php echo _VEHICLE_MANAGER_ADMIN_LANGUAGE_MANAGER_VALUE_CONST; ?></th>
                  <th align = "center" class="title" width="5%" nowrap="nowrap"><?php echo _VEHICLE_MANAGER_LABEL_LANGUAGE; ?></th>
                  <th align = "center" class="title" width="25%" nowrap="nowrap"><?php echo _VEHICLE_MANAGER_ADMIN_LANGUAGE_MANAGER_SYS_TYPE; ?></th>
                  <th align = "center" class="title" width="10%" nowrap="nowrap">
                    <input class="inputbox" type="button" name="loadLang"
                      value="Load languages"
                      onclick="window.location.replace('index.php?option=com_vehiclemanager&section=language_manager&task=loadLang')"/></th>
              </tr>

      <?php
      $i = 0;
      foreach ($const_languages as $const_language) {
          ?>
                  <tr>
                      <td align="center"><?php echo mosHTML::idBox($i, $const_language->id, false, 'vid'); ?></td>
                      <td><a href="#edit" onClick="return listItemTask('cb<?php echo $i; ?>','edit')"><?php echo $const_language->const; ?></a></td>
                      <td><a href="#edit" onClick="return listItemTask('cb<?php echo $i; ?>','edit')"><?php echo $const_language->value_const; ?></a></td>
                      <td align="center"><?php echo $const_language->title; ?></td>
                      <td align="center"><?php echo $const_language->sys_type; ?></td>
                      <td align="center"></td>
                  </tr>
          <?php
          $i++;
      }
      ?>
              <tr><td colspan = "13"><?php echo $pageNav->getListFooter(); ?></td></tr>
          </table>
          <input type="hidden" name="option" value="com_vehiclemanager" />
          <input type="hidden" name="section" value="language_manager" />
          <input type="hidden" name="task" value="" />
          <input type="hidden" value="0" name="boxchecked" />
      </form>
      <?php
  }

  static function editLanguageManager($row, $lists)
  {
      global $mosConfig_live_site;
      global $doc, $css;
      $doc->addStyleSheet($css);
      $doc->addScript($mosConfig_live_site . '/components/com_vehiclemanager/includes/functions.js');

      $html = "<div class='vehicle_manager_caption' ><i class='fa fa-car'></i> " . _VEHICLE_MANAGER_ADMIN_LANGUAGE_MANAGER . "</div>";
      $app = JFactory::getApplication();
      $app->JComponentTitle = $html;
      ?>
      <form action="index.php" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">
          <table >
              <tr>
                  <th  class="vehicle_manager_caption" align="left"><?php echo $row->id ? _VEHICLE_HEADER_EDIT : _VEHICLE_HEADER_ADD; ?> <?php echo _VEHICLE_MANAGER_ADMIN_LANGUAGE_MANAGER_CONST; ?> </th>
              </tr>
          </table>
          <table width="100%"  class="table_10">
              <tr>
                  <td valign="top">
                      <table class="adminform adminform_48" style="height: 150px;">
                          <tr>
                              <td><?php echo _VEHICLE_MANAGER_ADMIN_LANGUAGE_MANAGER_CONST; ?>:</td>
                              <td colspan="2"><?php echo $lists['const']; ?></td>
                          </tr>
                          <tr>
                              <td><?php echo _VEHICLE_MANAGER_ADMIN_LANGUAGE_MANAGER_VALUE_CONST; ?>:</td>
                              <td colspan="2"><textarea class="text_area" type="text" name="value_const"><?php echo $row->value_const; ?></textarea></td>
                          </tr>
                          <tr>
                              <td><?php echo _VEHICLE_MANAGER_ADMIN_LANGUAGE_MANAGER_SYS_TYPE; ?>:</td>
                              <td colspan="2"><?php echo $lists['sys_type']; ?></td>
                          </tr>
                          <tr>
                              <td><?php echo _VEHICLE_MANAGER_LABEL_LANGUAGE; ?>:</td>
                              <td colspan="2"><?php echo $lists['languages']; ?></td>
                          </tr>
                      </table>
                  </td>
              </tr>
          </table>
          <input type="hidden" name="const" value="<?php echo $lists['const']; ?>"/>
          <input type="hidden" name="option" value="com_vehiclemanager" />
          <input type="hidden" name="section" value="language_manager" />
          <input type="hidden" name="task" value="" />
          <input type="hidden" name="id" value="<?php echo $row->id; ?>" />
          <input type="hidden" name="sectionid" value="com_vehiclemanager" />
      </form>
      <?php
  }

  static function orders($orders, $search, & $pageNav) {
      global $my, $mosConfig_live_site, $mainframe, $session, $templateDir,$css,$doc;
      $doc->addStyleSheet($css);
      $doc->addScript($mosConfig_live_site . '/components/com_vehiclemanager/includes/functions.js');
      $html = "<div class='vehicle_manager_caption' ><i class='fa fa-car'></i> " . _VEHICLE_MANAGER_ADMIN_ORDERS . "</div>";
      $app = JFactory::getApplication();
      $app->JComponentTitle = $html;
      $countOrders = count($orders);
      $optionStatus[] = mosHTML::makeOption('Pending', "Pending");
      $optionStatus[] = mosHTML::makeOption('Completed', 'Completed');
      ?>
      <script type="text/javascript">
          function qqlistItemTask(a,b){
              var c=document.adminForm,d=c[a];
              if(d){
                  for(var f=0;;f++){
                      var e=c["cb"+f];
                      if(!e)break;
                      e.checked=!1
                  }
                  d.checked=!0;
                  c.boxchecked.value=1;
                  submitbutton(b)
              }
              return!1
          }
      </script>
      <form action="index.php" method="post" name="adminForm"  class="vehicles_main"  id="adminForm" >
      <table cellpadding="4" cellspacing="0" border="0" width="100%"
             class="adminlist">
          <tr>
              <td width="20"><?php echo _VEHICLE_MANAGER_SHOW_SEARCH; ?></td>
              <td><input type="text" name="search" value="<?php echo $search; ?>"
                         class="inputbox" onChange="document.adminForm.submit();" /></td>

              <?php if (version_compare(JVERSION, '3.0.0', 'ge')) { ?>
                  <td>
                      <div class="btn-group pull-right hidden-phone">
                          <label for="limit" class="element-invisible"><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC'); ?></label>

                        <?php echo $pageNav->getLimitBox(); ?>
                      </div>
                  </td>
              <?php
              } ?>
          </tr>
      </table>
          <table cellpadding="4" cellspacing="0" border="1px" width="100%" class="adminlist adminlist_04">
              <tr>
                  <th align = "center" colspan="" name="toggle">
                      <input type="checkbox" name="toggle" value=""
                          onClick="Joomla.checkAll(this);" /></th>
                  <th align = "center" nowrap="nowrap" class="title" colspan="">
		<a href="index.php?option=com_vehiclemanager&task=orders&orderby=id">
		    <?php echo _VEHICLE_MANAGER_ORDERS_ID;?>
		</a>
	    </th>
                  <th align = "center" nowrap="nowrap" class="title" colspan="">
		<a href="index.php?option=com_vehiclemanager&task=orders&orderby=user">
		    <?php echo _VEHICLE_MANAGER_ORDERS_USER;?>
		</a>
	    </th>
                  <th align = "center" nowrap="nowrap" class="title" colspan="">
		<?php echo _VEHICLE_MANAGER_ORDERS_USER_NAME;?>
	    </th>
                  <th align = "center" nowrap="nowrap" class="title" colspan="">
		<a href="index.php?option=com_vehiclemanager&task=orders&orderby=email">
		    <?php echo _VEHICLE_MANAGER_ORDERS_EMAIL;?>
		</a>
	    </th>
                  <th align = "center" nowrap="nowrap" class="title" colspan="">
		<a href="index.php?option=com_vehiclemanager&task=orders&orderby=data">
		    <?php echo _VEHICLE_MANAGER_ORDERS_DATE;?>
		</a>
	    </th>
                  <th align = "center" nowrap="nowrap" class="title" colspan="">
		<a href="index.php?option=com_vehiclemanager&task=orders&orderby=status">
		    <?php echo _VEHICLE_MANAGER_ORDERS_STATUS;?>
		</a>
	    </th>
                  <th align = "center" nowrap="nowrap" class="title" colspan="">
		<?php echo _VEHICLE_MANAGER_ORDERS_TITLE;?>
	    </th>
                  <th align = "center" nowrap="nowrap" class="title" colspan="">
		<?php echo _VEHICLE_MANAGER_ORDERS_PRICE;?>
	    </th>
	    <th align = "center" nowrap="nowrap" class="title" colspan="">
		<?php echo _VEHICLE_MANAGER_ORDERS_PAID;?>
	    </th>
	    <th align = "center" nowrap="nowrap" class="title" colspan=""></th>
              </tr>
              <?php for($i = 0; $i < $countOrders; $i++) { ?>
                  <tr>
                      <td align = "center"><?php echo mosHTML::idBox($i,$orders[$i]->id, false, 'cb');?></td>
                      <td><?php echo $orders[$i]->id;?></td>
                      <td>
                          <?php if(!isset($orders[$i]->usr_name) && $orders[$i]->usr_name == '') {
                              echo 'Anonimus';
                          } else {
                              echo $orders[$i]->usr_name;
                          }?>
                      </td>
                      <td><?php if(!isset($orders[$i]->username) && $orders[$i]->username == '' ) {
                              echo 'Anonimus';
                          } else {
                              echo $orders[$i]->username;
                          }?>
                      </td>
                      <td><?php echo $orders[$i]->usr_email;?></td>
                      <td><?php echo $orders[$i]->order_date;?></td>
                      <td>
                          <?php
                          $status = $orders[$i]->status;
                          if($status == 'Success'){
                                $status = 'Completed';
                          }
                          $attr = 'class="inputbox input-medium" size="1" onchange="return listItemTask(\'cb'.$i.'\',\'updateOrderStatus\')"';
                          echo mosHTML::selectList($optionStatus, 'order_status['.$orders[$i]->id.']', $attr, 'value', 'text', $status);
                          ?>

                      </td>
                      <td><?php echo $orders[$i]->fk_vehicle_vtitle;?></td>
                      <td><?php echo $orders[$i]->order_calculated_price;?></td>
                      <td><?php echo $orders[$i]->order_price." ".$orders[$i]->order_currency_code;?></td>
		                  <td>
                          <a href="<?php echo 'index.php?option=com_vehiclemanager'.
                                              '&task=orders&order_details=order_details&order_id='.$orders[$i]->id ?>">
                          <?php echo _VEHICLE_MANAGER_ORDERS_DETAILS; ?></a>
                      </td>
                  </tr>
              <?php } ?>
          </table>
          <?php echo $pageNav->getListFooter(); ?>
          <input type="hidden" name="option" value="com_vehiclemanager" />
          <input type="hidden" name="task" value="orders" />
          <input type="hidden" name="sectionid" value="com_vehiclemanager" />
          <input type="hidden" value="0" name="boxchecked" />
      </form><?php
  }

  static function orders_details($orders, $search, & $pageNav) {
      global $my, $mosConfig_live_site, $mainframe, $session, $templateDir,$css,$doc;
            $doc->addStyleSheet($css);
      $doc->addScript($mosConfig_live_site . '/components/com_vehiclemanager/includes/functions.js');
      $html = "<div class='vehicle_manager_caption' ><i class='fa fa-car'></i> " . _VEHICLE_MANAGER_ADMIN_ORDERS . "</div>";
      $app = JFactory::getApplication();
      $app->JComponentTitle = $html;
      $countOrders = count($orders);
      $orderId = protectInjectionWithoutQuote('order_id');
      $optionStatus[] = mosHTML::makeOption('Pending', "Pending");
      $optionStatus[] = mosHTML::makeOption('Completed', 'Completed');
      ?>
      <script type="text/javascript">
          function qqlistItemTask(a,b){
              var c=document.adminForm,d=c[a];
              if(d){
                  for(var f=0;;f++){
                      var e=c["cb"+f];
                      if(!e)break;
                      e.checked=!1
                  }
                  d.checked=!0;
                  c.boxchecked.value=1;
                  submitbutton(b)
              }
              return!1
          }
      </script>
      <form action="index.php" method="post" name="adminForm"  class="vehicles_main"  id="adminForm" >
      <table cellpadding="4" cellspacing="0" border="0" width="100%"
             class="adminlist">
          <tr>
              <td><?php echo _VEHICLE_MANAGER_SHOW_SEARCH; ?></td>
              <td><input type="text" name="search" value="<?php echo $search; ?>"
                         class="inputbox" onChange="document.adminForm.submit();" /></td>

              <?php if (version_compare(JVERSION, '3.0.0', 'ge')) { ?>
                  <td>
                      <div class="btn-group pull-right hidden-phone">
                          <label for="limit" class="element-invisible"><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC'); ?></label>

                        <?php echo $pageNav->getLimitBox(); ?>
                      </div>
                  </td>
              <?php
              } ?>
          </tr>
      </table>
          <table cellpadding="4" cellspacing="0" border="1px" width="100%" class="adminlist adminlist_04">
              <tr>
                  <th align = "center" nowrap="nowrap" class="title" colspan="">
                      <a href="<?php echo 'index.php?option=com_vehiclemanager&task=orders'.
                                  '&orderby=user&order_details=order_details&order_id='.$orderId?>">
		             <?php echo _VEHICLE_MANAGER_ORDERS_USER;?>
		             </a>
                  </th>
                  <th align = "center" nowrap="nowrap" class="title" colspan=""><?php echo _VEHICLE_MANAGER_ORDERS_USER_NAME;?></th>
                  <th align = "center" nowrap="nowrap" class="title" colspan="">
                      <a href="<?php echo 'index.php?option=com_vehiclemanager&task=orders'.
                                  '&orderby=email&order_details=order_details&order_id='.$orderId ?>"><?php
                                   echo _VEHICLE_MANAGER_ORDERS_EMAIL;?></a>
                  </th>
                  <th align = "center" nowrap="nowrap" class="title" colspan="">
                      <a href="<?php echo 'index.php?option=com_vehiclemanager&task=orders'.
                                  '&orderby=order_date&order_details=order_details&order_id='.
                                  $orderId ?>"><?php echo _VEHICLE_MANAGER_ORDERS_DATE;?></a>
                  </th>
                  <th align = "center" nowrap="nowrap" class="title" colspan="">
                      <a href="<?php echo 'index.php?option=com_vehiclemanager&task=orders'.
                       '&orderby=status&order_details=order_details&order_id='.$orderId ?>"><?php
                        echo _VEHICLE_MANAGER_ORDERS_STATUS;?></a>
                  </th>
                  <th align = "center" nowrap="nowrap" class="title" colspan=""><?php echo _VEHICLE_MANAGER_ORDERS_TITLE;?></th>
                  <th align = "center" nowrap="nowrap" class="title" colspan=""><?php echo _VEHICLE_MANAGER_ORDERS_PRICE;?></th>
                  <th align = "center" nowrap="nowrap" class="title" colspan=""><?php echo _VEHICLE_MANAGER_ORDERS_PAID;?></th>
                  <th align = "center" nowrap="nowrap" class="title" colspan=""><?php echo _VEHICLE_MANAGER_ORDERS_DETAILS;?></th>
              </tr>
              <?php for($i = 0; $i < $countOrders; $i++) {
              $payment_details = unserialize($orders[$i]->payment_details);
                  $details_text='';
                  if($orders[$i]->txn_type)
                      $details_text = _VEHICLE_MANAGER_ORDERS_DET_ACCEPT.$orders[$i]->txn_type;
                  if(!empty($payment_details)){
                      if(isset($payment_details['view']))
                          $details_text .= '<br>'._VEHICLE_MANAGER_ORDERS_DET_SYSTEM.$payment_details['view'];
                      if(isset($payment_details['payer_email']))
                          $details_text .= '<br>'._VEHICLE_MANAGER_ORDERS_DET_EMAIL.'<br>'.$payment_details['payer_email'];
                      if(isset($payment_details['pending_reason']))
                          $details_text .= '<br>'._VEHICLE_MANAGER_ORDERS_DET_REASON.'<br>'.$payment_details['pending_reason'];
                  }
	    ?>
                  <tr>
                      <td>
                          <?php if(!isset($orders[$i]->usr_name) && $orders[$i]->usr_name == '') {
                              echo _VEHICLE_MANAGER_LABEL_ANONYMOUS;
                          } else {
                              echo $orders[$i]->usr_name;
                          }?>
                      </td>
                      <td><?php if(!isset($orders[$i]->username) && $orders[$i]->username == '' ) {
                              echo _VEHICLE_MANAGER_LABEL_ANONYMOUS;
                          } else {
                              echo $orders[$i]->username;
                          }?>
                      </td>
                      <td><?php echo $orders[$i]->usr_email;?></td>
                      <td><?php echo $orders[$i]->order_date;?></td>
                      <td>
                          <?php
                          echo $orders[$i]->status;
                          ?>
                      </td>
                      <td><?php echo $orders[$i]->fk_vehicle_vtitle;?></td>
                      <td><?php echo $orders[$i]->order_calculated_price;?></td>
                      <td><?php echo $orders[$i]->order_price." ".$orders[$i]->order_currency_code;?></td>
                      <td><?php echo $details_text?></td>
                  </tr>
              <?php } ?>
          </table>
          <?php echo $pageNav->getListFooter(); ?>
          <input type="hidden" name="option" value="com_vehiclemanager" />
          <input type="hidden" name="task" value="orders" />
          <input type="hidden" name="order_id" value="<?php echo $orderId;?>" />
          <input type="hidden" name="order_details" value="order_details" />
          <input type="hidden" name="sectionid" value="com_vehiclemanager" />
          <input type="hidden" value="0" name="boxchecked" />
      </form><?php
  }
}
