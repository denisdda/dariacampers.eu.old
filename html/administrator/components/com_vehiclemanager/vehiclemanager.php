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

  include_once(JPATH_ROOT . "/components/com_vehiclemanager/compat.joomla1.5.php");

  if (!defined('DS'))
    define('DS', DIRECTORY_SEPARATOR);
  defined('_VM_IS_BACKEND') or define('_VM_IS_BACKEND', '1');

  $mainframe = $GLOBALS['mainframe'] = JFactory::getApplication(); 
  $my = $GLOBALS['my'];
  $mosConfig_absolute_path = $GLOBALS['mosConfig_absolute_path'];

  jimport('joomla.html.pagination');
  jimport('joomla.application.pathway');
  jimport('joomla.filesystem.folder');

  $database = JFactory::getDBO();
  $GLOBALS['task'] = $task = mosGetParam($_REQUEST, 'task', '');
  require_once ($mosConfig_absolute_path . "/administrator/components/com_vehiclemanager/toolbar.vehiclemanager.php");

  $css = $mosConfig_live_site . '/components/com_vehiclemanager/admin_vehiclemanager.css';

  require_once ($mosConfig_absolute_path . "/components/com_vehiclemanager/vehiclemanager.class.php");
  require_once ($mosConfig_absolute_path . "/components/com_vehiclemanager/vehiclemanager.class.feature.php");
  require_once ($mosConfig_absolute_path . "/components/com_vehiclemanager/vehiclemanager.class.language.php");
  require_once ($mosConfig_absolute_path . "/administrator/components/com_vehiclemanager/vehiclemanager.html.php");
  require_once ($mosConfig_absolute_path . "/components/com_vehiclemanager/vehiclemanager.class.rent.php");
  require_once ($mosConfig_absolute_path . "/components/com_vehiclemanager/vehiclemanager.class.rent_request.php");
  require_once ($mosConfig_absolute_path . "/components/com_vehiclemanager/vehiclemanager.class.buying_request.php");
  require_once ($mosConfig_absolute_path . "/administrator/components/com_vehiclemanager/admin.vehiclemanager.class.impexp.php");
  require_once ($mosConfig_absolute_path . "/administrator/components/com_vehiclemanager/admin.vehiclemanager.class.conf.php");
  require_once ($mosConfig_absolute_path . "/components/com_vehiclemanager/functions.php");

// vmLittleThings::remove_langs();
// vmLittleThings::loadConstVechicle();exit;

  $GLOBALS['option'] = $option = mosGetParam($_REQUEST, 'option', 'com_vehiclemanager');
  $GLOBALS['vehiclemanager_configuration'] = $vehiclemanager_configuration;
  $GLOBALS['database'] = $database;
  $GLOBALS['my'] = $my;
  $GLOBALS['mosCondefine($item->constfig_absolute_path'] = $mosConfig_absolute_path;

  $vid = mosGetParam($_REQUEST, 'vid', array(0));
  $section = mosGetParam($_REQUEST, 'section', 'courses');

  if(isset ($_REQUEST["vid"]) AND isset ($_REQUEST["rent_from"]) AND isset($_REQUEST["rent_until"])
        AND isset($_REQUEST["special_price"])){
    $vid_ajax_rent = $_REQUEST["vid"];
    $rent_from = $_REQUEST["rent_from"];
    $rent_until = $_REQUEST["rent_until"];
    $special_price = $_REQUEST["special_price"];
    $currency_spacial_price = $_REQUEST["currency_spacial_price"];
    if(isset($_REQUEST["comment_price"]))
        $comment_price = $_REQUEST["comment_price"];
    else
        $comment_price = '';
  }

  $vehiclemanager_configuration['debug'] = '0';

  if ($vehiclemanager_configuration['debug'] == '1')
  {
    echo "[debug mode] Task: " . $task . "<br /><pre>";
    print_r($_REQUEST);
    echo "</pre><hr /><br />";
  }

  //print_r($task);exit;
  if (isset($section) && $section == 'categories')
  {
    switch ($task) {

        case "saveCategoryOrder":
            $jinput = JFactory::getApplication()->input;
            $order = $jinput->getCmd('order', array(0));

            sortCategories($vid, $order, $option);
            break;

        case "edit" :
            editCategory($option, $vid[0]);
            break;

        case "add":
            editCategory($option, 0);
            break;

        case "cancel":
            cancelCategory();
            break;

        case "save":
        case "apply":
            saveCategory();
            break;

        case "remove":
            removeCategories($option, $vid);
            break;

        case "publish":
            publishCategories("com_vehiclemanager", $id, $vid, 1);
            break;

        case "unpublish":
            publishCategories("com_vehiclemanager", $id, $vid, 0);
            break;

        case "orderup":
            orderCategory($vid[0], -1);
            break;

        case "orderdown":
            orderCategory($vid[0], 1);
            break;

        case "accesspublic":
            accessCategory($vid[0], 0);
            break;

        case "accessregistered":
            accessCategory($vid[0], 1);
            break;

        case "accessspecial":
            accessCategory($vid[0], 2);
            break;

        case "show":
        default:
            showCategories();
    }
  } elseif ($section == 'featured_manager')
  {
    switch ($task) {
        case "edit" :
                echo "<script> alert('". _VEHICLE_MANAGER_EDIT_CONSTANT.": ".
                    "_VEHICLE_MANAGER_FEATURE".$vid[0]."'); window.history.go(-1);</script>\n";
                exit;
            break;

        case "add":
            editFeaturedManager($option, 0);
            break;

        case "cancel":
            cancelFeaturedManager();
            break;

        case "save":
            saveFeaturedManager();
            break;

        case "remove":
            removeFeaturedManager($option, $vid);
            break;

        case "publish":
            publishFeaturedManager("com_vehiclemanager", $id, $vid, 1);
            break;

        case "unpublish":
            publishFeaturedManager("com_vehiclemanager", $id, $vid, 0);
            break;
        case "addFeature":
            save_featured_category($option);
            showFeaturedManager($option);
            break;
        default:
            showFeaturedManager($option);
            break;
    }
  } elseif ($section == 'language_manager')
  {
    switch ($task) {
        case "edit" :
            editLanguageManager($option, $vid[0]);
            break;

        case "cancel":
            cancelLanguageManager();
            break;

        case "save":
            saveLanguageManager();
            break;

        default:
            showLanguageManager($option);
            break;
    }
  } else
  {
    //print_r($task);exit;
    switch ($task) {

        case "saveVehicleOrder":
            $jinput = JFactory::getApplication()->input;
            $order = $jinput->getCmd('order', array(0));

            sortVehicles($vid, $order, $option);
            break;

        case "sorting_manage_rent_history_vehicle_id":
            break;
        case "deleteOrder": 

            deleteOrder();
            break;
        case "updateOrderStatus":
            updateOrderStatus();
            break;
             case "orders":
            orders($option);
            break;

        case "categories":
            echo "now work $section=='categories , this part not work";
            exit;
            mosRedirect("index.php?option=categories&section=com_vehiclemanager");
            break;

        case "add": 
            editVehicle($option, 0);
            break;

        case "ajax_rent_price":
          $get_tmp = protectInjectionWithoutQuote('get');
          if(isset ($get_tmp["bid"]) AND
            isset ($get_tmp["rent_from"]) AND
            isset($get_tmp["rent_until"]) AND
            isset($get_tmp["special_price"])){

                $vid_ajax_rent = $get_tmp["vid"];
                $rent_from = $get_tmp["rent_from"];
                $rent_until = $get_tmp["rent_until"];
                $special_price = $get_tmp["special_price"];
                $currency_spacial_price = $get_tmp["currency_spacial_price"];
                if(isset($get_tmp["comment_price"]))
                    $comment_price = $get_tmp["comment_price"];
                else
                    $comment_price = '';
            }

            rentPrice($vid_ajax_rent,$rent_from,$rent_until,$special_price,
              $comment_price,$currency_spacial_price);
            break;

        case "edit":
            editVehicle($option, array_pop($vid));
            break;

        case "apply":
        case "save":
            saveVehicle($option, $task);
            break;

        case "remove":
            removeVehicles($vid, $option);
            break;

        case "publish":
            publishVehicles($vid, 1, $option);
            break;

        case "unpublish":
            publishVehicles($vid, 0, $option);
            break;

        case "approve":
            approveVehicles($vid, 1, $option);
            break;

        case "unapprove":
            approveVehicles($vid, 0, $option);
            break;

        case "cancel":
            cancelVehicle($option);

            break;

        case "vehicleorderdown":
            orderVehicles($vid[0], 1, $option);
            break;

        case "vehicleorderup":
            orderVehicles($vid[0], -1, $option);
            break;

        case "show_import_export":
            importExportVehicles($option);
            break;

        case "import":
            import($option);
            break;

        case "export":
            mosVehicleManagerImportExport::exportVehicles($option);
            break;

  //***************   begin for manage reviews   ***********************
        case "manage_review":
            manage_review_s($option, "");
            break;

        case "publish_manage_review":
            publish_manage_review($vid[0], 1, $option);
            break;

        case "unpublish_manage_review":
            publish_manage_review($vid[0], 0, $option);
            break;

        case "delete_manage_review":
            delete_manage_review($option, $vid);
            manage_review_s($option, "");
            break;

        case "edit_manage_review":
            edit_manage_review($option, $vid);
            break;

        case "update_edit_manage_review":
            $title = mosGetParam($_POST, 'title');
            $comment = mosGetParam($_POST, 'comment');
            $rating = mosGetParam($_POST, 'rating');
            $vehicle_id = mosGetParam($_POST, 'vehicle_id');
            $review_id = mosGetParam($_POST, 'review_id');
            update_review($title, $comment, $rating, $review_id);
            manage_review_s($option, "");
            break;

        case "cancel_edit_manage_review":
            manage_review_s($option, "");
            break;

        case "sorting_manage_review_numer":
            manage_review_s($option, "review_id");
            break;

        case "sorting_manage_review_title_vehicle":
            manage_review_s($option, "vehicle_title");
            break;

        case "sorting_manage_review_title_catigory":
            manage_review_s($option, "title_catigory");
            break;

        case "sorting_manage_review_title_review":
            manage_review_s($option, "title_review");
            break;

        case "sorting_manage_review_user_name":
            manage_review_s($option, "user_name");
            break;

        case "sorting_manage_review_date":
            manage_review_s($option, "date");
            break;

        case "sorting_manage_review_rating":
            manage_review_s($option, "rating");
            break;

        case "sorting_manage_review_published":
            manage_review_s($option, "published");
            break;

  //***************   end for manage reviews   *************************
        case "config":
            configure($option);
            break;

        case "config_save":
            //uncomment if you will want reload watermark after every watermark config change
            $jinput = JFactory::getApplication()->input;

            configure_save_frontend($option);
            configure_save_backend($option);
            configure($option);
            break;

        case "rent":
            if (mosGetParam($_POST, 'save') == 1)
                saveRent($option, $vid); else
                rent($option, $vid);
            break;

        case "rent_history":
                rent_history($option, $vid);
            break;

        case "users_rent_history":
            users_rent_history($option, "");
            break;

         //label 06.14.17 сортировка таблицы в user rent history
        case "user_rent_history_sorted_by_id":
            users_rent_history($option, "fk_vehicleid");
            break;

        case "user_rent_history_sorted_by_rent_from":
            users_rent_history($option, "rent_from");
            break;

        case "user_rent_history_sorted_by_rent_until":
            users_rent_history($option, "rent_until");
            break;

        case "user_rent_history_sorted_by_rent_return":
            users_rent_history($option, "rent_return");
            break;

        case "rent_requests":
            rent_requests($option, $vid);
            break;

        case "buying_requests":
            buying_requests($option);
            break;

        case "accept_rent_requests":
            accept_rent_requests($option, $vid);
            break;

        case "decline_rent_requests":
            decline_rent_requests($option, $vid);
            break;

        case "accept_buying_requests":
            accept_buying_requests($option, $vid);
            break;

        case "decline_buying_requests":
            decline_buying_requests($option, $vid);
            break;

        case "about" :
            HTML_vehiclemanager::about();
            break;

        case "show_info":
            showInfo($option, $bid);
            break;

        case "rent_return":
            if (mosGetParam($_POST, 'save') == 1)
                saveRent_return($option, $vid);
            else
                rent_return($option, $vid);
            break;

        case "edit_rent":
            if (mosGetParam($_POST, 'save') == 1)
            {
                if (count($vid) > 1)
                {
                    echo "<script> alert('You must select only one item for edit'); window.history.go(-1); </script>\n";
                    exit;
                }
                saveRent($option, $vid, "edit_rent");
            } else
                edit_rent($option, $vid);
            break;

        case "delete_review":
            $ids = explode(',', $vid[0]);
            delete_review($option, $ids[1]);
            editVehicle($option, $ids[0]);
            break;

        case "edit_review":
            $ids = explode(',', $vid[0]);
            edit_review($option, $ids[1], $ids[0]);
            break;

        case "update_review":
            $title = mosGetParam($_POST, 'title');
            $comment = mosGetParam($_POST, 'comment');
            $rating = mosGetParam($_POST, 'rating');
            $vehicle_id = mosGetParam($_POST, 'vehicle_id');
            $review_id = mosGetParam($_POST, 'review_id');
            update_review($title, $comment, $rating, $review_id);
            editVehicle($option, $vehicle_id);
            break;

        case "cancel_review_edit":
            $vehicle_id = mosGetParam($_POST, 'vehicle_id');
            editVehicle($option, $vehicle_id);
            break;

  //******   begin add for button print in Manager vehicles   ***********
        case "print_vehicles":
            print_vehicles($option);
            showVehicles($option);
            break;

        case "print_item":
            print_item($option);
            break;
  //******   end add for button print in Manager vehicles  *************
        case "checkFile":
            vm_checkFile();
        break;


        default:
            showVehicles($option);
            break;
    }
  }

  /**
  * HTML Class
  * Utility class for all HTML drawing classes
  * @desc class General HTML creation class. We use it for back/front ends.
  */
  class HTML
  {

    static function categoryParentList($id, $action, $options = array())
    {
        global $database;
        $list = vmLittleThings::categoryArray();
        $cat = new mainVehiclemanagerCategories($database); 
        $cat->load($id);

        $this_treename = '';
        $childs_ids = Array();
        foreach ($list as $item) {
            if ($item->id == $cat->id || array_key_exists($item->parent_id, $childs_ids))
                $childs_ids[$item->id] = $item->id;
        }

        foreach ($list as $item) {
            if ($this_treename)
            {
                if ($item->id != $cat->id && strpos($item->treename, $this_treename) === false
                        && array_key_exists($item->id, $childs_ids) === false)
                {
                    $options[] = mosHTML::makeOption($item->id, $item->treename);
                }
            } else
            {
                if ($item->id != $cat->id)
                {
                    $options[] = mosHTML::makeOption($item->id, $item->treename);
                } else
                {
                    $this_treename = "$item->treename/";
                }
            }
        }

        $parent = null;
        $parent = mosHTML::selectList($options, 'parent_id', 'class="inputbox" size="1"', 'value', 'text', $cat->parent_id);

        return $parent;
    }

    static function imageList($name, &$active, $javascript = null, $directory = null)
    {
        global $mosConfig_absolute_path;
        if (!$javascript)
        {
            if (!is_dir(JPATH_ROOT . '/images/stories/'))
                mkdir(JPATH_ROOT . '/images/stories/', 0755);
            $javascript = "onchange=\"javascript:if (document.adminForm." . $name .
                    ".options[selectedIndex].value!='')    {document.imagelib.src='../images/stories/' + document.adminForm."
                    . $name . ".options[selectedIndex].value} else {document.imagelib.src='../images/blank.png'}\"";
        }
        if (!$directory)
        {
            $directory = '/images/stories';
        }

        $imageFiles = mosReadDirectory($mosConfig_absolute_path . $directory);
        $images = array(mosHTML::makeOption('', _VEHICLE_A_SELECT_IMAGE));
        foreach ($imageFiles as $file) {
            if (preg_match("/bmp|gif|jpg|jpeg|png/i", $file))
            {
                $images[] = mosHTML::makeOption($file);
            }
        }

        $images = mosHTML::selectList($images, $name, 'id="' . $name . '" class="inputbox" size="1" '
                        . $javascript, 'value', 'text', $active);
        return $images;
    }

  }

  function showCategories(){
    global $database, $my, $option, $menutype, $mainframe, $mosConfig_list_limit, $acl;
    $section = "com_vehiclemanager";
    $groups = get_group_children_vm();
    $sectionid = $mainframe->getUserStateFromRequest("sectionid{$section}{$section}", 'sectionid', 0);
    $limit = $mainframe->getUserStateFromRequest("viewlistlimit", 'limit', $mosConfig_list_limit);
    $limitstart = $mainframe->getUserStateFromRequest("view{$section}limitstart", 'limitstart', 0);
    $levellimit = $mainframe->getUserStateFromRequest("view{$option}limit$menutype", 'levellimit', 10);
    $language_owner = $mainframe->getUserStateFromRequest("language{$option}", 'language', '-1');
    $where = '';
    if ($language_owner != '0' and $language_owner != '*'and $language_owner != '-1' ){
      $where = " AND c.language='$language_owner'";
    }

    // Abort an user if he edit a categories more then 2 hours (7200 sec)

    // Old code was under $query:
    /*
    if (version_compare(JVERSION, "3.0.0", "lt")){
      $curdate = strtotime(JFactory::getDate()->toMySQL());
    } else {
      $curdate = strtotime(JFactory::getDate()->toSql());
    }
    foreach ($rows as $row) {
      $check = strtotime($row->checked_out_time);
      $remain = 7200 - ($curdate - $check);
      if (($remain <= 0) && ($row->checked_out != 0)){
        $item = new mainVehiclemanagerCategories($database);
        $item->checkin($row->id);
      }
    }
    */

    // New code:
    $user_checked_out_categories = " UPDATE #__vehiclemanager_main_categories SET checked_out=0, checked_out_time='0000-00-00 00:00:00'
        WHERE `checked_out_time` > 0 AND ( TIME_TO_SEC('" . date('Y-m-d H:i:s') . "') - TIME_TO_SEC(`checked_out_time`) ) >= 7200;";
    $database->setQuery($user_checked_out_categories);
    $database->execute();

    $query = "SELECT  c.*,'0' as cc, c.checked_out as checked_out_contact_category,
              c.parent_id as parent, u.name AS editor, c.params, COUNT(vc.id) AS cc"
            . "\n FROM #__vehiclemanager_main_categories AS c"
            . "\n LEFT JOIN #__vehiclemanager_categories AS vc ON vc.idcat=c.id"
            . "\n LEFT JOIN #__users AS u ON u.id = c.checked_out"
            . "\n WHERE c.section='$section'"
            . "\n AND c.published !=-2"
            . $where
            . "\n GROUP BY c.id"
            . "\n ORDER BY parent DESC, ordering";
    $database->setQuery($query);
    $rows = $database->loadObjectList();
    if ($database->getErrorNum()) {
           echo $database->stderr();
           return false;
       }

    foreach ($rows as $k => $v) {
      $rows[$k]->ncourses = 0;
      foreach ($rows as $k1 => $v1)
        if ($v->id == $v1->parent)
          $rows[$k]->cc +=$v1->cc;
      $aa = $v->cc;
      $rows[$k]->nvehicle = ($aa == 0) ?
           "-" :
          "<a href=\"?option=com_vehiclemanager&section=vehicle&catid=" . $v->id . "\">" . ($aa) . "</a>";
      $curgroup = array();
      $ss = explode(',', $v->params);
      foreach ($ss as $s) {
        if ($s == '')
          $s = '-2';
        $curgroup[] = $groups[$s];
      }
      $rows[$k]->groups = implode(', ', $curgroup);
    }

    if ($database->getErrorNum()){
      echo $database->stderr();
      return false;
    }
    // establish the hierarchy of the categories
    $children = array();
    // first pass - collect children
    foreach ($rows as $v) {
      $pt = $v->parent;
      $list = @$children[$pt] ? $children[$pt] : array();
      array_push($list, $v);
      $children[$pt] = $list;
    }
    // second pass - get an indent list of the items
    $list = vmLittleThings::vehicleManagerTreeRecurse(0, '', array(), $children, max(0, $levellimit - 1));
    $total = count($list);
    $pageNav = new JPagination($total, $limitstart, $limit); 
    $levellist = mosHTML::integerSelectList(1, 20, 1, 'levellimit',
                           'size="1" onchange="document.adminForm.submit();"', $levellimit);
    // slice out elements based on limits
    $list = array_slice($list, $pageNav->limitstart, $pageNav->limit);
    $count = count($list);
    // number of Active Items
    // get list of sections for dropdown filter
    $javascript = 'onchange="document.adminForm.submit();"';
    if (version_compare(JVERSION, "3.0.0", "lt"))
      $lists['sectionid'] = mosAdminMenus::SelectSection('sectionid', $sectionid, $javascript);
    $query = "DELETE  FROM #__vehiclemanager_categories
              WHERE iditem NOT IN (select id from #__vehiclemanager_vehicles  ) ";
    $database->setQuery($query);
    $cat1 = $database->execute();

    $language = array();
    $selectlanguage = "SELECT `language` FROM `#__vehiclemanager_vehicles` WHERE language <> '*' GROUP BY language ";
    $database->setQuery($selectlanguage);
    $languages = $database->loadObjectList();
    $language_list[]= mosHTML::makeOption('0', _VEHICLE_MANAGER_LABEL_SELECT_LANGUAGE);
    foreach ($languages as $language) {
      $language_list[] = mosHTML::makeOption($language->language, $language->language);
    }
    $language = mosHTML::selectList($language_list, 'language',
                      'class="inputbox input-medium" size="1" onchange="document.adminForm.submit();"',
                       'value', 'text', $language_owner);

    HTML_Categories::show($list, $my->id, $pageNav, $lists,$language, 'other');
  }

  function editCategory($section = '', $uid = 0)
  {
    global $database, $my, $acl;
    global $mosConfig_absolute_path, $mosConfig_live_site;

    $type = mosGetParam($_REQUEST, 'type', '');
    $redirect = mosGetParam($_POST, 'section', '');

    $row = new mainVehiclemanagerCategories($database); 
    // load the row from the db table
    $row->load($uid);
    // fail if checked out not by 'me'
    if ($row->checked_out && $row->checked_out <> $my->id)
    {
        mosRedirect('index.php?option=com_vehiclemanager&task=categories', 'The category ' . $row->title . ' is currently being edited by another administrator');
    }

    if ($uid)
    {
        // existing record
        $row->checkout($my->id);
        // code for Link Menu
    } else
    {
        // new record
        $row->section = $section;
        $row->published = 1;
    }

    /*****************************************************************************************************************************/
    $associateArray = array();
    if($row->id){
        $query = "SELECT lang_code FROM `#__languages` WHERE 1";
        $database->setQuery($query);
        $allLanguages =  $database->loadColumn();

        $query = "SELECT id,language,title FROM `#__vehiclemanager_main_categories` WHERE 1";
        $database->setQuery($query);
        $allInCategories =  $database->loadObjectlist();

        $query = "select associate_category from #__vehiclemanager_main_categories where id =".$row->id;
        $database->setQuery($query);
        $categoryAssociateCategory =  $database->loadResult();

        if(!empty($categoryAssociateCategory)){
            $categoryAssociateCategory = unserialize($categoryAssociateCategory);
        }else{
            $categoryAssociateCategory = array();
        }

        foreach ($allLanguages as &$oneLang) {
            $associate_category = array();
            $associate_category[] = mosHtml::makeOption(0, 'select');
            $i = 0;

            foreach($allInCategories as &$oneCat){
                if($oneLang == $oneCat->language && $oneCat->id != $row->id){
                    $associate_category[] = mosHtml::makeOption(($oneCat->id), $oneCat->title);
                }
            }

            if($row->language != $oneLang){
                $associate_category_list = mosHTML::selectList($associate_category, 'language_associate_category', 'class="inputbox" size="1"', 'value', 'text', "");
            }else{
                $associate_category_list = null;
            }

            $associateArray[$oneLang]['list'] = $associate_category_list;

            if(isset($categoryAssociateCategory[$oneLang])){
                $associateArray[$oneLang]['assocId'] = $categoryAssociateCategory[$oneLang];
            }else{
                $associateArray[$oneLang]['assocId'] = 0;
            }
        }
    }
    /*****************************************************************************************************************************/

    // make order list
    $order = array();

    $database->setQuery("SELECT COUNT(*) FROM #__vehiclemanager_main_categories WHERE section='$row->section'");
    $max = intval($database->loadResult()) + 1;

    for ($i = 1; $i < $max; $i++)
        $order[] = mosHTML::makeOption($i);
    // build the html select list for ordering
    $query = "SELECT ordering AS value, title AS text"
            . "\n FROM #__vehiclemanager_main_categories"
            . "\n WHERE section = '$row->section'"
            . "\n ORDER BY ordering";
    $lists = array();

    $lists['ordering'] = version_compare(JVERSION, '3.0', 'ge') ? NUll : mosAdminMenus::SpecificOrdering($row, $uid, $query);
    // build the select list for the image positions
    $active = ($row->image_position ? $row->image_position : 'left');
    $lists['image_position'] = version_compare(JVERSION, '3.0', 'ge') ? NUll : mosAdminMenus::Positions('image_position', $active, null, 0, 0);
    // Imagelist
    $lists['image'] = HTML::imageList('image', $row->image);
    // build the html select list for the group access
    $lists['access'] = version_compare(JVERSION, '3.0', 'ge') ? NUll : mosAdminMenus::Access($row);
    // build the html radio buttons for published
    $lists['published'] = mosHTML::yesnoRadioList('published', 'class="inputbox"', $row->published);
    // build the html select list for paraent item
    $options = array();
    $options[] = mosHTML::makeOption('0', _VEHICLE_A_SELECT_TOP);

    $lists['parent'] = HTML::categoryParentList($row->id, "", $options);

    //***********access category
    $gtree = get_group_children_tree_vm();

    $f = array();
    if (trim($row->params) == '')
        $row->params = '-2';
    $s = explode(',', $row->params);
    for ($i = 0; $i < count($s); $i++)
        $f[] = mosHTML::makeOption($s[$i]);
    $lists['category']['registrationlevel'] = mosHTML::selectList($gtree, 'category_registrationlevel[]', 'size="" multiple="multiple"', 'value', 'text', $f);
    //********end access category

    $query = "SELECT lang_code, title FROM #__languages";
    $database->setQuery($query);
    $languages = $database->loadObjectList();

    $languages_row[] = mosHTML::makeOption('*', 'All');
    foreach ($languages as $language) {
        $languages_row[] = mosHTML::makeOption($language->lang_code, $language->title);
    }

    $lists['languages'] = mosHTML::selectList($languages_row, 'language', 'class="inputbox" size="1"', 'value', 'text', $row->language);
    HTML_Categories::edit($row, $section, $lists, $redirect, $associateArray);
  }

  function saveCategory(){
    global $database;

    $row = new mainVehiclemanagerCategories($database); 

    $post = JFactory::getApplication()->input->post->getArray(array(), null, 'raw');        
  /***************************************************************************************************/
    $currentId = $post['id'];
    if($currentId){
        $i = 1;
        $assocArray = array();
        $assocCategoryId = array();
        while(isset($post['associate_category'.$i])){
            $langAssoc = $post['associate_category_lang'.$i];
            $valAssoc = $post['associate_category'.$i];
            $assocArray[$langAssoc] = $valAssoc;
            if($valAssoc){
              $assocCategoryId[] = $valAssoc;
            }
            $i++;
        }
        $currentLang = $post['language'];
        $assocArray[$currentLang] = $currentId;
        $assocStr = serialize($assocArray);
            $query = "SELECT `associate_category`
                        FROM #__vehiclemanager_main_categories
                        WHERE `id` = ".$currentId."";
            $database->setQuery($query);
            $oldAssociate = $database->loadResult();
            $oldAssociateArray = unserialize($oldAssociate);
            if($oldAssociateArray){
                foreach ($oldAssociateArray as $key => $value) {
                    if($value && !isset($assocCategoryId[$value])){
                        $assocCategoryId[] = $value;
                    }
                }
            }
            if(!isset($assocCategoryId[$currentId])){
                $assocCategoryId[] = $currentId;
            }
            $idToChange = implode(',' , $assocCategoryId);
          if(count($idToChange) && !empty($idToChange)){
            $query = "UPDATE #__vehiclemanager_main_categories
                        SET `associate_category`='".$assocStr."'
                        WHERE `id` in (".$idToChange.")";
            $database->setQuery($query);
            $database->execute();

        }
    }
  /***************************************************************************************************/
    $params2 = new stdClass();
    $params2->alone_category = $post['alone_category2'];
    $params2->view_vehicle = $post['view_vehicle'];
    $post['params2'] = serialize($params2);
    $row->bind($post) ;
    $row->section = 'com_vehiclemanager';
    $row->parent_id = $_REQUEST['parent_id'];
  //****set access level
    // $row->params = implode(',', mosGetParam($_POST, 'category_registrationlevel', ''));
    $row->params = -2;
  //****end set access level
    if (!$row->check()){
      return;
    }
    $row->store();
    $row->checkin();
    $row->updateOrder("section='com_vehiclemanager' AND parent_id='$row->parent_id'");
    if($_REQUEST['task'] == 'apply'){
      mosRedirect('index.php?option=com_vehiclemanager&section=categories&task=edit&vid[]='.$row->id);
    }
    mosRedirect('index.php?option=com_vehiclemanager&section=categories');
  }

  //this function check - is exist vehicles in this folder and folders under this category
  function is_exist_curr_and_subcategory_vehicles($catid)
  {
    global $database, $my;

    $query = "SELECT *, COUNT(a.id) AS numlinks FROM #__vehiclemanager_main_categories AS cc"
            . "\n LEFT JOIN #__vehiclemanager_vehicles AS a ON a.catid = cc.id"
            . "\n WHERE a.published='1' AND a.approved='1' AND section='com_vehiclemanager' AND cc.id='$catid' AND cc.published='1' AND cc.access <= '$my->gid'"
            . "\n GROUP BY cc.id"
            . "\n ORDER BY cc.ordering";
    $database->setQuery($query);
    $categories = $database->loadObjectList();

    if (count($categories) != 0)
        return true;

    $query = "SELECT id "
            . "FROM #__vehiclemanager_main_categories AS cc "
            . " WHERE section='com_vehiclemanager' AND parent_id='$catid' AND published='1' AND access<='$my->gid'";
    $database->setQuery($query);
    $categories = $database->loadObjectList();

    if (count($categories) == 0)
        return false;

    foreach ($categories as $k) {
        if (is_exist_curr_and_subcategory_vehicles($k->id))
            return true;
    }
    return false;
  }

  //end function


  function removeCategoriesFromDB($cid)
  {
    global $database, $my;

    $query = "SELECT id  "
            . "FROM #__vehiclemanager_main_categories AS cc "
            . " WHERE section='com_vehiclemanager' AND parent_id = '$cid' AND published='1' AND access<='$my->gid'";
    $database->setQuery($query);
    $categories = $database->loadObjectList();

    if (count($categories) != 0)
    {
        //delete child
        foreach ($categories as $k) {
            removeCategoriesFromDB($k->id);
        }
    }

    $sql = "DELETE FROM #__vehiclemanager_main_categories WHERE id = $cid ";
    $database->setQuery($sql);
    $database->execute();
  }

  /**
  * Deletes one or more categories from the categories table
  *
  * @param string $ The name of the category section
  * @param array $ An array of unique category id numbers
  */
  function removeCategories($section, $cid)
  {
    global $database;

    if (count($cid) < 1)
    {
        echo "<script> alert('Select a category to delete'); window.history.go(-1);</script>\n";
        exit;
    }

    foreach ($cid as $catid) {
        if (is_exist_curr_and_subcategory_vehicles($catid))
        {
            echo "<script> alert('Some category or subcategory from yours select contain vehicles. \\n Please remove vehicles first!'); window.history.go(-1); </script>\n";
            exit;
        }
    }

    foreach ($cid as $catid)
        removeCategoriesFromDB($catid);

    $msg = (count($err) > 1 ? "Categories " : _VEHICLE_CATEGORIES_NAME . " ") . _VEHICLE_DELETED;
    mosRedirect('index.php?option=com_vehiclemanager&section=categories&mosmsg=' . $msg);
  }

  /**
  * Publishes or Unpublishes one or more categories
  *
  * @param string $ The name of the category section
  * @param integer $ A unique category id (passed from an edit form)
  * @param array $ An array of unique category id numbers
  * @param integer $ 0 if unpublishing, 1 if publishing
  * @param string $ The name of the current user
  */
  function publishCategories($section, $categoryid = null, $cid = null, $publish = 1)
  {
    global $database, $my;

    if (!is_array($cid))
        $cid = array();
    if ($categoryid)
        $cid[] = $categoryid;

    if (count($cid) < 1)
    {
        $action = $publish ? _PUBLISH : _DML_UNPUBLISH;
        echo "<script> alert('" . _DML_SELECTCATTO . " $action'); window.history.go(-1);</script>\n";
        exit;
    }

    $cids = implode(',', $cid);

    $query = "UPDATE #__vehiclemanager_main_categories SET published='$publish'"
            . "\nWHERE id IN ($cids) AND (checked_out=0 OR (checked_out='$my->id'))";
    $database->setQuery($query);
    $database->execute();

    if (count($cid) == 1)
    {
        $row = new mainVehiclemanagerCategories($database); 
        $row->checkin($cid[0]);
    }
    mosRedirect('index.php?option=com_vehiclemanager&section=categories');
  }

  /**
  * Cancels an edit operation
  *
  * @param string $ The name of the category section
  * @param integer $ A unique category id
  */
  function cancelCategory()
  {
    global $database;
    $row = new mainVehiclemanagerCategories($database); 
    $row->bind($_POST);
    $row->checkin();
    mosRedirect('index.php?option=com_vehiclemanager&section=categories');
  }

  /**
  * Moves the order of a record
  *
  * @param integer $ The increment to reorder by
  */
  function orderCategory($uid, $inc)
  {
    global $database;
    $row = new mainVehiclemanagerCategories($database); 
    $row->load($uid);
    if ($row->ordering == 1 && $inc == -1)
        mosRedirect('index.php?option=com_vehiclemanager&section=categories');
    $new_order = $row->ordering + $inc;

    //change ordering - for other element
    $query = "UPDATE #__vehiclemanager_main_categories SET ordering='" . ($row->ordering) . "'"
            . "\nWHERE parent_id = $row->parent_id and ordering=$new_order";
    $database->setQuery($query);
    $database->execute();

    //change ordering - for this element
    $query = "UPDATE #__vehiclemanager_main_categories SET ordering='" . $new_order . "'"
            . "\nWHERE id = $uid";
    $database->setQuery($query);
    $database->execute();

    mosRedirect('index.php?option=com_vehiclemanager&section=categories');
  }

  /**
  * changes the access level of a record
  *
  * @param integer $ The increment to reorder by
  */
  function accessCategory($uid, $access)
  {
    global $database;
    $row = new mainVehiclemanagerCategories($database); 
    $row->load($uid);
    $row->access = $access;
    if (!$row->check())
        return ;
    $row->store();

    mosRedirect('index.php?option=com_vehiclemanager&section=categories');
  }

  function update_review($title, $comment, $rating, $review_id)
  {
    global $database;

    $review = new mosVehicleManager_review($database);
    $review->load($review_id);

    $review->bind($_POST);

    if (!$review->check())
    {
        return;
    }

    $review->store();

  }

  function edit_review($option, $review_id, $vehicle_id)
  {
    global $database;
    $database->setQuery("SELECT * FROM #__vehiclemanager_review WHERE id=" . $review_id . " ");
    $review = $database->loadObjectList();

    HTML_vehiclemanager::edit_review($option, $vehicle_id, $review);
  }

  /*
  * Function for delete coment
  * (comment for every vehicle)
  * in database.
  */

  function delete_review($option, $id)
  {
    global $database;
    $database->setQuery("DELETE FROM #__vehiclemanager_review WHERE #__vehiclemanager_review.id=" . $id . ";");
    $database->execute();
  }

  //*************************************************************************************************************
  //*********************************   begin for manage reviews   **********************************************
  //*************************************************************************************************************
  function delete_manage_review($option, $id)
  {
    global $database;
    for ($i = 0; $i < count($id); $i++) {
        $database->setQuery("DELETE FROM #__vehiclemanager_review WHERE #__vehiclemanager_review.id=" . $id[$i] . ";");
        $database->execute();
    }
  }

  function edit_manage_review($option, $review_id)
  {
    global $database;
    if (count($review_id) > 1)
    {
        echo "<script> alert('Please select one review for edit!!!'); window.history.go(-1); </script>\n";
    } else
    {
        $database->setQuery("SELECT * FROM #__vehiclemanager_review WHERE id=" . $review_id[0] . " ");
        $review = $database->loadObjectList();

        HTML_vehiclemanager::edit_manage_review($option, $review);
    }
  }

  //*************************************************************************************************************
  //*********************************   end for manage reviews   ************************************************
  //*************************************************************************************************************


  function showInfo($option, $vid)
  {
    if (is_array($vid) && count($vid) > 0)
        $vid = $vid[0];
    echo "Test: " . $vid;
  }

  function decline_rent_requests($option, $vids)
  {
    global $database, $vehiclemanager_configuration;
    $datas = array();
    foreach ($vids as $vid) {
        $rent_request = new mosVehicleManager_rent_request($database);
        $rent_request->load($vid);
        $rent_request->decline();
        foreach ($datas as $c => $data) {
            if ($rent_request->user_email == $data['email'])
            {
                $datas[$c]['ids'][] = $rent_request->fk_vehicleid;
                continue 2;
            }
        }
        $datas[] = array('email' => $rent_request->user_email, 'name' => $rent_request->user_name, 'id' => $rent_request->fk_vehicleid);

    }
    if ($vehiclemanager_configuration['rent_answer'])
    {
        sendMailRentRequest($datas, _VEHICLE_MANAGER_ADMIN_CONFIG_RENT_ANSWER_DECLINED);
    }
    mosRedirect("index.php?option=$option&task=rent_requests");
  }

  function accept_rent_requests($option, $vids)
  {
    global $database, $vehiclemanager_configuration;
  //    print_r($vid);exit;

   // $id = mosGetParam($_POST, 'id');


    $datas = array();
    foreach ($vids as $vid) {
        $rent_request = new mosVehicleManager_rent_request($database);
        $rent_request->load($vid);
        $tmp = $rent_request->accept();
        if ($tmp != null)
        {
            echo "<script> alert('" . $tmp . "'); window.history.go(-1); </script>\n";
            exit;
        }

        foreach ($datas as $c => $data) {
            if ($rent_request->user_email == $data['email'])
            {
                $datas[$c]['ids'][] = $rent_request->fk_vehicleid;
                continue 2;
            }
        }
        $datas[] = array('email' => $rent_request->user_email, 'name' => $rent_request->user_name, 'id' => $rent_request->fk_vehicleid);

    }
    if ($vehiclemanager_configuration['rent_answer'])
    {
        sendMailRentRequest($datas, _VEHICLE_MANAGER_ADMIN_CONFIG_RENT_ANSWER_ACCEPTED);
    }
    mosRedirect("index.php?option=$option&task=rent_requests");
  }

  function sendMailRentRequest($datas, $answer)
  {
    global $database, $mosConfig_mailfrom, $vehiclemanager_configuration;

    foreach ($datas as $key => $data) {
        $mess = null;
        $zapros = "SELECT vtitle FROM #__vehiclemanager_vehicles WHERE id=" . $data['id'];
        $database->setQuery($zapros);
        $item_book = $database->loadResult();

        $database->setQuery("SELECT u.name AS ownername,u.email as owneremail
                            \nFROM #__users AS u
                            \nLEFT JOIN #__vehiclemanager_vehicles AS vm ON vm.owner_id=u.id
                            \nWHERE vm.id=" . $data['id']);
        $ownerdata = $database->loadObjectList();

        $datas[$key]['title'] = $item_book;

        $message = _VEHICLE_MANAGER_EMAIL_NOTIFICATION_RENT_REQUEST_ANSWER;
        $message = str_replace("{title}", addslashes($datas[$key]['title']), $message);
        $message = str_replace("{answer}", addslashes($answer), $message);
        $message = str_replace("{username}", addslashes($datas[$key]['name']), $message);

        $oname = (isset($ownerdata[0]->ownername)) ? $ownerdata[0]->ownername : null;
        $oemail = (isset($ownerdata[0]->owneremail)) ? $ownerdata[0]->owneremail : null;
        $subject = _VEHICLE_MANAGER_EMAIL_RENT_ANSWER_SUBJECT;

        if ($answer == _VEHICLE_MANAGER_ADMIN_CONFIG_RENT_ANSWER_ACCEPTED){
            $message = str_replace("{ownername}", addslashes($oname), $message);
            $message = str_replace("{owneremail}", $oemail, $message);
            $from_name = $oname;
        }
        else{
            $message = str_replace("{ownername}", '', $message);
            $message = str_replace("{owneremail}", '', $message);
            $from_name = null;
        }

        $res = mosMail($mosConfig_mailfrom, $from_name, $data['email'], $subject, $message, true);

    }
  }

  function accept_buying_requests($option, $vids){
    global $database, $vehiclemanager_configuration;
    foreach ($vids as $vid) {
      $buying_request = new mosVehicleManager_buying_request($database);
      $buying_request->load($vid);
      $datas[] = array('name' => $buying_request->customer_name,
          'email' => $buying_request->customer_email,
          'id' => $buying_request->fk_vehicleid);
      $buying_request->delete($vid);
    }
    if ($vehiclemanager_configuration['buy_answer']) {
        sendMailBuyingRequest($datas, _VEHICLE_MANAGER_ADMIN_CONFIG_BUY_ANSWER_ACCEPTED);
    }
    mosRedirect("index.php?option=$option&task=buying_requests");
  }

  function decline_buying_requests($option, $bids)
  {
    global $database, $vehiclemanager_configuration;
    foreach ($bids as $vid) {
        $buying_request = new mosVehicleManager_buying_request($database);
        $buying_request->load($vid);
        $datas[] = array('name' => $buying_request->customer_name,
            'email' => $buying_request->customer_email,
            'id' => $buying_request->fk_vehicleid);
        $buying_request->decline();
    }
    if ($vehiclemanager_configuration['buy_answer']) {
        sendMailBuyingRequest($datas, _VEHICLE_MANAGER_ADMIN_CONFIG_BUY_ANSWER_DECLINED);
    }
    mosRedirect("index.php?option=$option&task=buying_requests");
  }

  function sendMailBuyingRequest($datas, $answer){
    global $database, $mosConfig_mailfrom, $vehiclemanager_configuration;
    $conf = JFactory::getConfig();
    foreach ($datas as $key => $data) {
      $mess = null;
      $zapros = "SELECT vtitle FROM #__vehiclemanager_vehicles WHERE id=" . $data['id'];
      $database->setQuery($zapros);
      $item_book = $database->loadResult();

      $database->setQuery("SELECT u.name AS ownername, u.email as owneremail
                          \nFROM #__users AS u
                          \nLEFT JOIN #__vehiclemanager_vehicles AS vm ON vm.owner_id=u.id
                          \nWHERE vm.id=" . $data['id']);
      $ownerdata = $database->loadObjectList();

      $datas[$key]['title'] = $item_book;
      $message = _VEHICLE_MANAGER_EMAIL_NOTIFICATION_BUYING_REQUEST_ANSWER;
      $message = str_replace("{title}", addslashes($datas[$key]['title']), $message);
      $message = str_replace("{answer}", addslashes($answer), $message);
      $message = str_replace("{username}", addslashes($datas[$key]['name']), $message);
      if ($answer == _VEHICLE_MANAGER_ADMIN_CONFIG_RENT_ANSWER_ACCEPTED){
        $message = str_replace("{ownername}", addslashes($ownerdata[0]->ownername), $message);
        $message = str_replace("{owneremail}", $ownerdata[0]->owneremail, $message);
      }else{
        $message = str_replace("{ownername}", '', $message);
        $message = str_replace("{owneremail}", '', $message);
      }
      mosMail($mosConfig_mailfrom, $conf->_registry['config']['data']->fromname, $data['email'], _VEHICLE_MANAGER_EMAIL_RENT_ANSWER_SUBJECT, $message, true);
    }
  }

  //*********   begin add for button print in Manager vehicles  ***********
  function print_vehicles($option)
  {
    global $mosConfig_live_site, $database, $mainframe, $mosConfig_list_limit;

    if (!array_key_exists('vid', $_POST))
    {
        echo "<script> alert('Please select some vehicle'); window.history.go(-1); </script>\n";
        exit;
    } else
    {
        $vid = $_POST['vid'];
        $vids = implode(',', $vid);
    }
  //*************   begin for vehicles request   **************************
    $limit = $mainframe->getUserStateFromRequest("viewlistlimit", 'limit', $mosConfig_list_limit);
    $limitstart = $mainframe->getUserStateFromRequest("view{$option}limitstart", 'limitstart', 0);
    $catid = $mainframe->getUserStateFromRequest("catid{$option}", 'catid', 0);
    $rent = $mainframe->getUserStateFromRequest("rent{$option}", 'rent', 0);
    $pub = $mainframe->getUserStateFromRequest("pub{$option}", 'pub', 0);
    $owner = $mainframe->getUserStateFromRequest("owner{$option}", 'owner', 0);

    $search = $mainframe->getUserStateFromRequest("search{$option}", 'search', '');
    $search = $database->getEscaped(trim(strtolower($search)));

    $where = array();

    if ($rent == "rent")
    {
        array_push($where, "a.fk_rentid <> 0");
    } else if ($rent == "not_rent")
    {
        array_push($where, "a.fk_rentid = 0");
    }

    if ($pub == "pub")
    {
        array_push($where, "a.published = 1");
    } else if ($pub == "not_pub")
    {
        array_push($where, "a.published = 0");
    }

    if ($catid > 0)
    {
        array_push($where, "a.catid='$catid'");
    }

    $database->setQuery("SELECT count(*) FROM #__vehiclemanager_vehicles AS a" .
            "\nLEFT JOIN #__vehiclemanager_rent AS l" .
            "\nON a.fk_rentid = l.id" .
            (count($where) ? "\nWHERE " . implode(' AND ', $where) : ""));
    $total = $database->loadResult();

    $pageNav = new JPagination($total, $limitstart, $limit);

    $selectstring = "SELECT a.*, GROUP_CONCAT(cc.title SEPARATOR ', ') AS category, " .
                " l.id as rentid, l.rent_from as rent_from, l.rent_return as  rent_return," .
                " l.rent_until as rent_until, u.name AS editor,  " .
                " l.user_name as user_name, l.user_email as user_email, l.user_mailing as user_mailing " .
                " FROM #__vehiclemanager_vehicles AS a " .
                " LEFT JOIN #__vehiclemanager_categories AS vc ON vc.iditem = a.id " .
                " LEFT JOIN #__vehiclemanager_main_categories AS cc ON cc.id = vc.idcat " .
                " LEFT JOIN #__vehiclemanager_rent AS l ON a.fk_rentid = l.id " .
                " LEFT JOIN #__users AS u ON u.id = a.checked_out " .
                " WHERE a.id IN ($vids) " .
                " GROUP BY a.id" .
                " ORDER BY a.vtitle " .
                " LIMIT $pageNav->limitstart,$pageNav->limit;";

    $database->setQuery($selectstring);
    $rows = $database->loadObjectList();

    if ($database->getErrorNum())
    {
        echo $database->stderr();
        return false;
    }
  //**********************   end for vehicles request   *****************************

    HTML_vehiclemanager::showPrintVehicles($rows);
  }

  function print_item($option)
  {
    $rows = $_SESSION['rows'];
    HTML_vehiclemanager::showPrintItem($rows);
  }

  //*********************   end add for button print in Manager vehicles   *************


  function rent_requests($option, $vid)
  {
    global $database, $mainframe, $mosConfig_list_limit;

    $limit = $mainframe->getUserStateFromRequest("viewlistlimit", 'limit', $mosConfig_list_limit);
    $limitstart = $mainframe->getUserStateFromRequest("view{$option}limitstart", 'limitstart', 0);

    $database->setQuery("SELECT count(*) FROM #__vehiclemanager_vehicles AS a" .
            "\nLEFT JOIN #__vehiclemanager_rent_request AS l" .
            "\nON l.fk_vehicleid = a.id" .
            "\nWHERE l.status = 0");
    $total = $database->loadResult();

    $pageNav = new JPagination($total, $limitstart, $limit); 

    $database->setQuery("SELECT * FROM #__vehiclemanager_vehicles AS a" .
            "\nLEFT JOIN #__vehiclemanager_rent_request AS l" .
            "\nON l.fk_vehicleid = a.id" .
            "\nWHERE l.status = 0" .
            "\nORDER BY l.id DESC" .
            "\nLIMIT $pageNav->limitstart,$pageNav->limit;");
    $rent_requests = $database->loadObjectList();

    foreach ($rent_requests as $request) {
        if($request->associate_vehicle){
            if($assoc_veh = getAssociateVehicle($request->fk_vehicleid)){
                $database->setQuery("SELECT group_concat(distinct a.vtitle ) FROM #__vehiclemanager_vehicles AS a" .
                  "\nLEFT JOIN #__vehiclemanager_rent_request AS l ON l.fk_vehicleid = a.id" .
                  "\nWHERE a.id in ($assoc_veh) AND a.id != $request->fk_vehicleid " ) ;

                $request->title_assoc = $database->loadResult();
            }
        }
    }

    HTML_vehiclemanager::showRequestRentVehicles($option, $rent_requests,  $pageNav);
  }

  function buying_requests($option)
  {
    global $database, $mainframe, $mosConfig_list_limit;
    $limit = $mainframe->getUserStateFromRequest("viewlistlimit", 'limit', $mosConfig_list_limit);
    $limitstart = $mainframe->getUserStateFromRequest("view{$option}limitstart", 'limitstart', 0);

    $database->setQuery("SELECT count(*) FROM #__vehiclemanager_vehicles AS a" .
            "\nLEFT JOIN #__vehiclemanager_buying_request AS s" .
            "\nON s.fk_vehicleid = a.id" .
            "\nWHERE s.status = 0");
    $total = $database->loadResult();

    $pageNav = new JPagination($total, $limitstart, $limit); 

    $database->setQuery("SELECT * FROM #__vehiclemanager_vehicles AS a" .
            "\nLEFT JOIN #__vehiclemanager_buying_request AS s" .
            "\nON s.fk_vehicleid = a.id" .
            "\nWHERE s.status = 0" .
            "\nORDER BY s.id DESC" .
            "\nLIMIT $pageNav->limitstart,$pageNav->limit;");
    $buy_requests = $database->loadObjectList();

    HTML_vehiclemanager::showRequestBuyingVehicles($option, $buy_requests, $pageNav);
  }

  /**
  * Compiles a list of records
  * @param database - A database connector object
  * select categories
  */
  function showVehicles($option)
  {
    global $database, $mainframe, $mosConfig_list_limit;

    $limit = $mainframe->getUserStateFromRequest("viewlistlimit", 'limit', $mosConfig_list_limit);
    $limitstart = $mainframe->getUserStateFromRequest("view{$option}limitstart", 'limitstart', 0);
    $catid = $mainframe->getUserStateFromRequest("catid{$option}", 'catid', '-1'); //old 0
    $language_owner = $mainframe->getUserStateFromRequest("language{$option}", 'language', '-1');
    $rent = $mainframe->getUserStateFromRequest("rent{$option}", 'rent', '-1'); //add nik
    $pub = $mainframe->getUserStateFromRequest("pub{$option}", 'pub', '-1'); //add nik
    $owner = $mainframe->getUserStateFromRequest("owner{$option}", 'owner', '-1'); //add nik

    $search_list = $mainframe->getUserStateFromRequest("search_list{$option}", 'search_list', '-1');
    $search = $mainframe->getUserStateFromRequest("search{$option}", 'search', '');
    $where = array();

    if ($rent == "rent")
    {
        array_push($where, "a.fk_rentid <> 0");
    } else if ($rent == "not_rent")
    {
        array_push($where, "a.fk_rentid = 0");
    }
    if ($pub == "pub")
    {
        array_push($where, "a.published = 1");
    } else if ($pub == "not_pub")
    {
        array_push($where, "a.published = 0");
    }
    if ($owner != -1)
        array_push($where, "a.owner_id = '$owner'");
    if ($catid > 0)
    {
        array_push($where, "vc.idcat='$catid'");
    }
    if ($language_owner != '0' and $language_owner != '*'and $language_owner != '-1' )
    {
        array_push($where, "a.language='$language_owner'");
    }


    if($search_list != '-1'){
         array_push($where, "(LOWER($search_list) LIKE '%$search%')");
    }else{
        array_push($where, "(LOWER(a.vtitle) LIKE '%$search%' " .
            " OR LOWER(a.maker) LIKE '%$search%' " .
            " OR LOWER(a.vmodel) LIKE '%$search%' " .
            " OR LOWER(a.description) LIKE '%$search%' " .
            " OR LOWER(a.vehicleid) LIKE '%$search%' " .
            " OR LOWER(a.vlocation) LIKE '%$search%' " .
            " OR LOWER(a.country) LIKE '%$search%' " .
            " OR LOWER(a.city) LIKE '%$search%' " .
            " OR LOWER(a.region) LIKE '%$search%' " .
            " OR LOWER(a.zipcode) LIKE '%$search%')");
    }


    $database->setQuery("SELECT count(*) FROM #__vehiclemanager_vehicles AS a " .
            "\nLEFT JOIN #__vehiclemanager_categories AS vc ON vc.iditem = a.id " .
            "\nLEFT JOIN #__vehiclemanager_main_categories AS cc ON cc.id = vc.idcat " .
            "\nLEFT JOIN #__vehiclemanager_rent AS l ON l.fk_vehicleid = a.id  and l.rent_return is null " .
            "\nLEFT JOIN #__users AS u ON u.id = a.owner_id " .
            "\nLEFT JOIN #__users AS ue ON ue.id = a.checked_out " .
            (count($where) ? "\nWHERE " . implode(' AND ', $where) : ""));

    $total = $database->loadResult();

    $pageNav = new JPagination($total, $limitstart, $limit); 

    // Abort an user if he edit a categories more then 2 hours (7200 sec)

    // Old code was under $selectstring:
    /*
    if (version_compare(JVERSION, "3.0.0", "lt"))
    {
        $curdate = strtotime(JFactory::getDate()->toMySQL());
    } else
    {
        $curdate = strtotime(JFactory::getDate()->toSql());
    }
    foreach ($rows as $row) {
        $check = strtotime($row->checked_out_time);
        $remain = 7200 - ($curdate - $check);
        if (($remain <= 0) && ($row->checked_out != 0))
        {
            $item = new mosVehicleManager($database);
            $item->checkin($row->id);
        }
    }
    */

    // New code:
    $user_checked_out_vehicles = " UPDATE #__vehiclemanager_vehicles SET checked_out=0, checked_out_time='0000-00-00 00:00:00'
      WHERE `checked_out_time` > 0 AND ( TIME_TO_SEC('" . date('Y-m-d H:i:s') . "') - TIME_TO_SEC(`checked_out_time`) ) >= 7200;";
    $database->setQuery($user_checked_out_vehicles);
    $database->execute();

    $selectstring = "SELECT a.*, GROUP_CONCAT(DISTINCT cc.title SEPARATOR ', ') AS category,
            l.id as rentid, l.rent_from as rent_from, l.rent_return as rent_return,
            l.rent_until as rent_until, u.id AS uid, u.name AS editor, ue.name AS editor1, u.username AS owner_name" .
            "\nFROM #__vehiclemanager_vehicles AS a" .
            "\nLEFT JOIN #__vehiclemanager_categories AS vc ON vc.iditem = a.id" .
            "\nLEFT JOIN #__vehiclemanager_main_categories AS cc ON cc.id = vc.idcat" .
            "\nLEFT JOIN #__vehiclemanager_rent AS l ON l.fk_vehicleid = a.id  and l.rent_return is null " .
            "\nLEFT JOIN #__users AS u ON u.id = a.owner_id" .
            "\nLEFT JOIN #__users AS ue ON ue.id = a.checked_out" .
            (count($where) ? "\nWHERE " . implode(' AND ', $where) : "") .
            "\nGROUP BY a.id" .
            "\nORDER BY a.ordering " .
            "\nLIMIT $pageNav->limitstart,$pageNav->limit;";
    $database->setQuery($selectstring);
    $rows = $database->loadObjectList();

    //set ordering if = 0
    $query = "SELECT id FROM #__vehiclemanager_vehicles WHERE ordering = '0'";
    $database->setQuery($query);
    $ids = $database->loadColumn();

    $query = "SELECT MAX(ordering) FROM #__vehiclemanager_vehicles";
    $database->setQuery($query);
    $max_ordering = $database->loadResult();

    $max = $max_ordering;
    foreach ($ids as $id) {
        $max++;
        $query ="UPDATE #__vehiclemanager_vehicles SET ordering = '".$max."' WHERE id = '".$id."'";
        $database->setQuery($query);
        $database->execute();
    }
    //set ordering if = 0
    if ($database->getErrorNum())
    {
        echo $database->stderr();
        return false;
    }

    // get list of categories
    /*
     * select list treeSelectList
     */
    $categories[] = mosHTML::makeOption('0', _VEHICLE_MANAGER_LABEL_SELECT_CATEGORIES);
    $categories[] = mosHTML::makeOption('-1', _VEHICLE_MANAGER_LABEL_SELECT_ALL_CATEGORIES);
  //*************   begin add for sub category in select in manager vehicles   *************
    $options = $categories;
    $id = 0; //$categories_array;
    $list = vmLittleThings::categoryArray();

    $cat = new mainVehiclemanagerCategories($database); 
    $cat->load($id);

    $this_treename = '';
    foreach ($list as $item) {
        if ($this_treename)
        {
            if ($item->id != $cat->id && strpos($item->treename, $this_treename) === false)
            {
                $options[] = mosHTML::makeOption($item->id, $item->treename);
            }
        } else
        {
            if ($item->id != $cat->id)
            {
                $options[] = mosHTML::makeOption($item->id, $item->treename);
            } else
            {
                $this_treename = "$item->treename/";
            }
        }
    }

    $clist = mosHTML::selectList($options, 'catid', 'class="inputbox input-medium" size="1" onchange="document.adminForm.submit();"', 'value', 'text', $catid); //new nik edit
  //*****  end add for sub category in select in manager vehicles   **********


    $searchmenu[] = mosHTML :: makeOption('-1', _VEHICLE_MANAGER_LABEL_SELECT_BY_ALL);
    $searchmenu[] = mosHTML :: makeOption('a.vtitle', _VEHICLE_MANAGER_LABEL_SELECT_BY_TITLE);
    $searchmenu[] = mosHTML :: makeOption('a.maker', _VEHICLE_MANAGER_LABEL_SELECT_BY_MAKER);
    $searchmenu[] = mosHTML :: makeOption('a.vmodel', _VEHICLE_MANAGER_LABEL_SELECT_BY_MODEL);
    $searchmenu[] = mosHTML :: makeOption('a.description', _VEHICLE_MANAGER_LABEL_SELECT_BY_DESC);
    $searchmenu[] = mosHTML :: makeOption('a.vehicleid', _VEHICLE_MANAGER_LABEL_SELECT_BY_VEHICLEID);
    $searchmenu[] = mosHTML :: makeOption('a.vlocation', _VEHICLE_MANAGER_LABEL_SELECT_BY_LOCATION);
    $searchmenu[] = mosHTML :: makeOption('a.country', _VEHICLE_MANAGER_LABEL_SELECT_BY_COUNTRY);
    $searchmenu[] = mosHTML :: makeOption('a.city', _VEHICLE_MANAGER_LABEL_SELECT_BY_CITY);
    $searchmenu[] = mosHTML :: makeOption('a.region', _VEHICLE_MANAGER_LABEL_SELECT_BY_REGION);
    $searchmenu[] = mosHTML :: makeOption('a.zipcode', _VEHICLE_MANAGER_LABEL_SELECT_BY_ZIPCODE);

    $search_list = mosHTML :: selectList($searchmenu, 'search_list',
     'class="inputbox" size="1" onchange="document.adminForm.submit();"',
      'value', 'text', $search_list);

    $rentmenu[] = mosHTML::makeOption('0', _VEHICLE_MANAGER_LABEL_SELECT_TO_RENT);
    $rentmenu[] = mosHTML::makeOption('-1', _VEHICLE_MANAGER_LABEL_SELECT_ALL_RENT);
    $rentmenu[] = mosHTML::makeOption('not_rent', _VEHICLE_MANAGER_LABEL_SELECT_NOT_RENT);
    $rentmenu[] = mosHTML::makeOption('rent', _VEHICLE_MANAGER_LABEL_SELECT_RENT);

    $rentlist = mosHTML::selectList($rentmenu, 'rent', 'class="inputbox input-medium" size="1" onchange="document.adminForm.submit();"', 'value', 'text', $rent);

    $pubmenu[] = mosHTML::makeOption('0', _VEHICLE_MANAGER_LABEL_SELECT_TO_PUBLIC);
    $pubmenu[] = mosHTML::makeOption('-1', _VEHICLE_MANAGER_LABEL_SELECT_ALL_PUBLIC);
    $pubmenu[] = mosHTML::makeOption('not_pub', _VEHICLE_MANAGER_LABEL_SELECT_NOT_PUBLIC);
    $pubmenu[] = mosHTML::makeOption('pub', _VEHICLE_MANAGER_LABEL_SELECT_PUBLIC);

    $publist = mosHTML::selectList($pubmenu, 'pub', 'class="inputbox input-medium" size="1" onchange="document.adminForm.submit();"', 'value', 'text', $pub);

    $ownermenu[] = mosHTML::makeOption('-1', _VEHICLE_MANAGER_LABEL_SELECT_ALL_USERS);
    $selectstring = "SELECT id,name FROM  #__users GROUP BY name ORDER BY id ";

    $database->setQuery($selectstring);
    $owner_list = $database->loadObjectList();

    if ($database->getErrorNum())
    {
        echo $database->stderr();
        return false;
    }
    $i = 2;
    foreach ($owner_list as $item) {
        $ownermenu[$i] = mosHTML::makeOption($item->id, $item->name);
        $i++;
    }

    $ownerlist = mosHTML::selectList($ownermenu, 'owner', 'class="inputbox input-medium" size="1" onchange="document.adminForm.submit();"', 'value', 'text', $owner);

    $language = array();
    $selectlanguage = "SELECT `language` FROM `#__vehiclemanager_vehicles` WHERE language <> '*' GROUP BY language ";

    $database->setQuery($selectlanguage);
    $languages = $database->loadObjectList();
    $language_list[]= mosHTML::makeOption('0', _VEHICLE_MANAGER_LABEL_SELECT_LANGUAGE);

    foreach ($languages as $language) {
        $language_list[] = mosHTML::makeOption($language->language, $language->language);

    }
    $language = mosHTML::selectList($language_list, 'language', 'class="inputbox input-medium" size="1" onchange="document.adminForm.submit();"', 'value', 'text', $language_owner);
    HTML_vehiclemanager::showVehicles($option, $rows, $clist,$language, $ownerlist, $rentlist, $publist, $search, $search_list, $pageNav);
  }

  /**
  * Compiles information to add or edit vehicles
  * @param integer bid The unique id of the record to edit (0 if new)
  * @param array option the current options
  */


  function editVehicle($option, $vid){
    global $database, $my, $mosConfig_live_site, $vehiclemanager_configuration, $mosConfig_absolute_path;
    if (isset($_GET['mess'])){ 
      // Vehicle is get from table throw the ID
      echo "<tt style='font-size:12px !important;'>" . $_GET['mess'] . "</tt>";
      if (isset($_GET['vid'])){
        $q = "SELECT id
              FROM `#__vehiclemanager_vehicles`
              WHERE  `vehicleid`= " . $_GET['vid'] . ";";
        $database->setQuery($q);
        $vid = $database->loadObjectList();
        if (isset($vid[0]->id))
          $vid = $vid[0]->id; // $vid - is exactly id, not an vehicleid from Vehicles table!
      } else
        echo "<script>window.history.go(-1);</script>";
    }
    $vehicle = new mosVehicleManager($database);
    // load the row from the db table
    $vehicle->load(intval($vid));
    $numeric_vehicleids = Array();
    if (empty($vehicle->vehicleid)
          && $vehiclemanager_configuration['vehicleid']['auto-increment']['boolean'] == 1){
      $database->setQuery("select vehicleid from #__vehiclemanager_vehicles order by vehicleid");
      $vehicleids = $database->loadObjectList();
      foreach ($vehicleids as $vehicleid) {
        if (is_numeric($vehicleid->vehicleid)){
          $numeric_vehicleids[] = intval($vehicleid->vehicleid);
        }
      }
      if (count($numeric_vehicleids) > 0) {
        sort($numeric_vehicleids);
        $vehicle->vehicleid = $numeric_vehicleids[count($numeric_vehicleids) - 1] + 1;
      }
      else
        $vehicle->vehicleid = 1;
    }
  /**************************************************************************************************/
    $associateArray = array();
    if($vid){

        $call_from = 'backend';
    $associateArray = edit_vehicle_associate($vehicle, $call_from, true);
    }
  /**************************************************************************************************/
    // get list of categories
    $categories = array();
    $query = "SELECT  id ,name, parent_id as parent"
            . "\n FROM #__vehiclemanager_main_categories"
            . "\n WHERE section='com_vehiclemanager'"
            . "\n ORDER BY parent_id, ordering";
    $database->setQuery($query);
    $rows = $database->loadObjectList();
    // establish the hierarchy of the categories
    $children = array();
    // first pass - collect children
    foreach ($rows as $v) {
      $pt = $v->parent;
      $list = @$children[$pt] ? $children[$pt] : array();
      array_push($list, $v);
      $children[$pt] = $list;
    }
    // second pass - get an indent list of the items
    $list = vmLittleThings::vehicleManagerTreeRecurse(0, '', array(), $children);
    foreach ($list as $i => $item) {
      $item->text = $item->treename;
      $item->value = $item->id;
      $list[$i] = $item;
    }
    $categories = array_merge($categories, $list);
    $vehicle->setCatIds();
    $clist = mosHTML::selectList($categories, 'catid[]', 'class="inputbox" ',
                                                            'value', 'text', $vehicle->catid);
    //get Rating
    $retVal2 = mosVehicleManagerOthers::getRatingArray();
    $rating = null;
    for ($i = 0, $n = count($retVal2); $i < $n; $i++) {
        $help = $retVal2[$i];
        $rating[] = mosHTML::makeOption($help[0], $help[1]);
    }
    //delete vehicle?
    $help = str_replace($mosConfig_live_site, "", $vehicle->edok_link);
    $delete_edoc_yesno[] = mosHTML::makeOption($help, _VEHICLE_MANAGER_YES);
    $delete_edoc_yesno[] = mosHTML::makeOption('0', _VEHICLE_MANAGER_NO);
    $delete_edoc = mosHTML::radioList($delete_edoc_yesno, 'delete_edoc', 'class="inputbox"', '0',
                                             'value', 'text');
    // fail if checked out not by 'me'
    if ($vehicle->checked_out && $vehicle->checked_out <> $my->id){
      mosRedirect("index.php?option=$option", _VEHICLE_MANAGER_IS_EDITED);
    }
    if ($vid){
      $vehicle->checkout($my->id);
    }else{
      // initialise new record
      $vehicle->published = 0;
      $vehicle->approved = 0;
    }
  //*****************************   begin for reviews **************************//
    $database->setQuery("select a.* from #__vehiclemanager_review a" .
            " WHERE a.fk_vehicleid=" . $vid . " ORDER BY date ;");
    $reviews = $database->loadObjectList();
  //**********************   end for reviews   *****************************//
    //Select list for vehicle type
    $vehicletype[] = mosHtml::makeOption(0, _VEHICLE_MANAGER_OPTION_SELECT);
    $vehicletype1 = explode(',', _VEHICLE_MANAGER_OPTION_VEHICLE_TYPE);
    $i = 1;
    foreach ($vehicletype1 as $vehicletype2) {
      $vehicletype[] = mosHtml::makeOption($i, $vehicletype2);
      $i++;
    }
    $vehicle_type_list = mosHTML::selectList($vehicletype, 'vtype', 'class="inputbox" size="1"',
                                                             'value', 'text', $vehicle->vtype);
    //Select list for listing type
    $listing_type[] = mosHtml::makeOption(0, _VEHICLE_MANAGER_OPTION_SELECT);
    $listing_type[] = mosHtml::makeOption(1, _VEHICLE_MANAGER_OPTION_FOR_RENT);
    $listing_type[] = mosHtml::makeOption(2, _VEHICLE_MANAGER_OPTION_FOR_SALE);
    $listing_type_list = mosHTML::selectList($listing_type, 'listing_type', 'class="inputbox" size="1"',
                                                              'value', 'text', $vehicle->listing_type);
    //Select list for price type
    $test[] = mosHtml::makeOption(0, _VEHICLE_MANAGER_OPTION_SELECT);
    $test1 = explode(',', _VEHICLE_MANAGER_OPTION_PRICE_TYPE);
    $i = 1;
    foreach ($test1 as $test2) {
      $test[] = mosHtml::makeOption($i, $test2);
      $i++;
    }
    $test_list = mosHTML::selectList($test, 'price_type', 'class="inputbox" size="1"',
                                              'value', 'text', $vehicle->price_type);
    //Select list for vehicle condition
    $condition[] = mosHtml::makeOption(0, _VEHICLE_MANAGER_OPTION_SELECT);
    $condition1 = explode(',', _VEHICLE_MANAGER_OPTION_VEHICLE_CONDITION);
    $i = 1;
    foreach ($condition1 as $condition2) {
      $condition[] = mosHtml::makeOption($i, $condition2);
      $i++;
    }
    $condition_status_list = mosHTML::selectList($condition, 'vcondition', 'class="inputbox" size="1"',
                                                               'value', 'text', $vehicle->vcondition);
    //Select list for listing status
    $listing_status[] = mosHtml::makeOption(0, _VEHICLE_MANAGER_OPTION_SELECT);
    $listing_status1 = explode(',', _VEHICLE_MANAGER_OPTION_LISTING_STATUS);
    $i = 1;
    foreach ($listing_status1 as $listing_status2) {
      $listing_status[] = mosHtml::makeOption($i, $listing_status2);
      $i++;
    }
    $listing_status_list = mosHTML::selectList($listing_status, 'listing_status', 'class="inputbox" size="1"',
                                                                  'value', 'text', $vehicle->listing_status);
    //Select list for vehicle transmission
    $transmission[] = mosHtml::makeOption(0, _VEHICLE_MANAGER_OPTION_SELECT);
    $transmission1 = explode(',', _VEHICLE_MANAGER_OPTION_TRANSMISSION);
    $i = 1;
    foreach ($transmission1 as $transmission2) {
      $transmission[] = mosHtml::makeOption($i, $transmission2);
      $i++;
    }
    $transmission_type_list = mosHTML::selectList($transmission, 'transmission', 'class="inputbox" size="1"',
                                                                   'value', 'text', $vehicle->transmission);
    //Select list for vehicle drive type
    $drivetype[] = mosHtml::makeOption(0, _VEHICLE_MANAGER_OPTION_SELECT);
    $drivetype1 = explode(',', _VEHICLE_MANAGER_OPTION_DRIVE_TYPE);
    $i = 1;
    foreach ($drivetype1 as $drivetype2) {
      $drivetype[] = mosHtml::makeOption($i, $drivetype2);
      $i++;
    }
    $drive_type_list = mosHTML::selectList($drivetype, 'drive_type', 'class="inputbox" size="1"',
                                                                        'value', 'text', $vehicle->drive_type);
    //Select list for number of cylinder
    $numcylinder[] = mosHtml::makeOption(0, _VEHICLE_MANAGER_OPTION_SELECT);
    $numcylinder1 = explode(',', _VEHICLE_MANAGER_OPTION_NUMBER_OF_CYLINDERS);
    $i = 1;
    foreach ($numcylinder1 as $numcylinder2) {
      $numcylinder[] = mosHtml::makeOption($i, $numcylinder2);
      $i++;
    }
    $num_cylinder_list = mosHTML::selectList($numcylinder, 'cylinder', 'class="inputbox" size="1"',
                                                                         'value', 'text', $vehicle->cylinder);
    //Select list for vehicle fuel type
    $fueltype[] = mosHtml::makeOption(0, _VEHICLE_MANAGER_OPTION_SELECT);
    $fueltype1 = explode(',', _VEHICLE_MANAGER_OPTION_FUEL_TYPE);
    $i = 1;
    foreach ($fueltype1 as $fueltype2) {
      $fueltype[] = mosHtml::makeOption($i, $fueltype2);
      $i++;
    }
    $fuel_type_list = mosHTML::selectList($fueltype, 'fuel_type', 'class="inputbox" size="1"',
                                                                    'value', 'text', $vehicle->fuel_type);
    //Select list for number of speed
    $numspeed[] = mosHtml::makeOption(0, _VEHICLE_MANAGER_OPTION_SELECT);
    $numspeed1 = explode(',', _VEHICLE_MANAGER_OPTION_NUMBER_OF_SPEEDS);
    $i = 1;
    foreach ($numspeed1 as $numspeed2) {
      $numspeed[] = mosHtml::makeOption($i, $numspeed2);
      $i++;
    }
    $num_speed_list = mosHTML::selectList($numspeed, 'num_speed', 'class="inputbox" size="1"',
                                                                    'value', 'text', $vehicle->num_speed);
    //Select list for number of doors
    $numdoors[] = mosHtml::makeOption(0, _VEHICLE_MANAGER_OPTION_SELECT);
    $numdoors1 = explode(',', _VEHICLE_MANAGER_OPTION_NUMBER_OF_DOORS);
    $i = 1;
    foreach ($numdoors1 as $numdoors2) {
      $numdoors[] = mosHtml::makeOption($i, $numdoors2);
      $i++;
    }
    $num_doors_list = mosHTML::selectList($numdoors, 'doors', 'class="inputbox" size="1"',
                                                                   'value', 'text', $vehicle->doors);
    $query = "SELECT main_img
              FROM #__vehiclemanager_photos
              WHERE fk_vehicleid='$vehicle->id'
              ORDER BY img_ordering,id";
    $database->setQuery($query);
    $vehicle_temp_photos = $database->loadObjectList();
    foreach ($vehicle_temp_photos as $vehicle_temp_photo) {
      $vehicle_photos[] = array($vehicle_temp_photo->main_img,
                vm_picture_thumbnail($vehicle_temp_photo->main_img,
                                 $vehiclemanager_configuration['foto']['high'],
                                 $vehiclemanager_configuration['foto']['width']));
    }
    $query = "SELECT image_link
              FROM #__vehiclemanager_vehicles
              WHERE id='$vehicle->id'";
    $database->setQuery($query);
    $vehicle_photo = $database->loadResult();
    if ($vehicle_photo != '')
      $vehicle_photo = array($vehicle_photo,       vm_picture_thumbnail($vehicle_photo,
                                  $vehiclemanager_configuration['foto']['high'],
                                  $vehiclemanager_configuration['foto']['width']));
    // Setting the resize parameters
    $makers = array();
    $opt[] = mosHtml::makeOption('', _VEHICLE_MANAGER_OPTION_SELECT);
    $opt[] = mosHtml::makeOption('other', _VEHICLE_MANAGER_LABEL_SELECT_OTHER);
    $temp = mosVehicleManagerOthers::getMakersArray();
    $makers = array_merge($makers, $temp[0]);
    foreach ($makers as $maker) {
      $opt[] = mosHtml::makeOption(trim($maker), trim($maker));
    }
    $nummaker = array_search($vehicle->maker, $temp[0]);
    foreach ($temp[1][$nummaker] as $model) {
      $opt1[] = mosHtml::makeOption(trim($model), trim($model));
    }
    $opt1[] = mosHtml::makeOption('other', _VEHICLE_MANAGER_LABEL_SELECT_OTHER);
    $currentmodel = $vehicle->vmodel;
    $maker = mosHTML::selectList($opt, 'maker', 'class="inputbox" size="1" onchange=changedMaker(this)', 'value', 'text', $vehicle->maker);
    if (in_array($currentmodel, $temp[1][$nummaker])){
      $modellist = mosHTML::selectList($opt1, 'vmodel',
                 'class="inputbox" size="1" id="vmodel" type="text" onchange=changedModel(this) ', 'value', 'text', $vehicle->vmodel);
    }else{
      $modellist = '<input name="vmodel" id="vmodel" type="text" value="' . $vehicle->vmodel . '"/>';
    }

//************ select list or text field for country region city
// $vcountry   =   _VEHICLE_MANAGER_LABEL_ALL;
// $vregion    =   _VEHICLE_MANAGER_LABEL_ALL;
// $vcity      =   _VEHICLE_MANAGER_LABEL_ALL;

// if(isset($vehicle->id) && !empty($vehicle->id)){
//     $query = "SELECT country, region, city
//                 FROM #__vehiclemanager_vehicles
//                 WHERE id=" . $vehicle->id;
//     $database->setQuery($query);
//     $vehicle_db = $database->loadObjectList();

//     $vcountry   =   $vehicle_db[0]->country;
//     $vregion    =   $vehicle_db[0]->region;
//     $vcity      =   $vehicle_db[0]->city;
// }
//**********country
    // $countrys[] = mosHtml::makeOption($vcountry, $vcountry);
    $countrys_and_regions = mosVehicleManagerOthers::getElementsArray('countrys_and_regions.txt');
    $regions_and_citys = mosVehicleManagerOthers::getElementsArray('regions_and_citys.txt');

    $temp[2] = $countrys_and_regions[0];
    $temp[3] = $countrys_and_regions[1];
    $temp[4] = $regions_and_citys[0];
    $temp[5] = $regions_and_citys[1];


    //------------------------------------Country-------------------------------//
    if(trim($vehicle->country) == ''){
        $vcountry  = '';
    }else{
        $vcountry = $vehicle->country;
    }

    $countrys[] = mosHtml::makeOption('',_VEHICLE_MANAGER_ADMIN_PLEASE_SEL);

        $countryList = $countrys_and_regions[0];

        foreach ($countryList as $country) {
            if (trim($country) != ''){
              $countrys[] = mosHtml::makeOption(trim($country), trim($country));
            }
        }

    if(!in_array($vcountry, $countrys_and_regions[0])){
        if($vcountry != ''){
            $countrys[] = mosHtml::makeOption($vcountry, $vcountry);
        }
    }

    $country = mosHTML::selectList($countrys, 'country', 'class="inputbox" size="1" onchange=vm_changedCountry(this)', 'value', 'text', $vcountry);

    // $vehicle->country = $country;

    //**********end countrys

    //**********region

     if(trim($vehicle->region) == ''){
        $vregion  = '';
    }else{
        $vregion = $vehicle->region;
    }

    $regions[] = mosHtml::makeOption('', _VEHICLE_MANAGER_ADMIN_PLEASE_SEL);

    if(in_array($vcountry, $countrys_and_regions[0])){
        $countrys_and_regions_tmp = array_flip($countrys_and_regions[0]) ;
        $countryIndex = $countrys_and_regions_tmp[$vcountry];
        $regionList = $countrys_and_regions[1][$countryIndex];

        foreach ($regionList as $region) {
            if (trim($region) != ''){
              $regions[] = mosHtml::makeOption(trim($region), trim($region));
            }
        }
    }else{
        if($vregion  != ''){
            $regions[] = mosHtml::makeOption(trim($vregion), trim($vregion));
        }
    }

    $region = mosHTML::selectList($regions, 'region', 'class="inputbox" size="1" onchange=vm_changedRegion(this)', 'value', 'text', $vregion);

    // $vehicle->region = $region;

    //**********end region

    //**********city

    if(trim($vehicle->city) == ''){
        $vcity  = '';
    }else{
        $vcity = $vehicle->city;
    }

    $citys[] = mosHtml::makeOption('', _VEHICLE_MANAGER_ADMIN_PLEASE_SEL);

    if(in_array($vregion, $regions_and_citys[0])){
        $regions_and_citys_tmp = array_flip($regions_and_citys[0]) ;
        $regionIndex = $regions_and_citys_tmp[$vregion];
        $cityList = $regions_and_citys[1][$regionIndex];

        foreach ($cityList as $city) {
            if (trim($city) != ''){
              $citys[] = mosHtml::makeOption(trim($city), trim($city));
            }
        }

    }else{
        if($vcity  != ''){
            $citys[] = mosHtml::makeOption($vcity, $vcity);
        }
    }

    $city = mosHTML::selectList($citys, 'city', 'class="inputbox" size="1"', 'value', 'text', $vcity);

    // $vehicle->city = $city;

    //**********end city



    $youtubeId = "";
///////////START check video/audio files\\\\\\\\\\\\\\\\\\\\\\
    $tracks = array();
    $videos = array();
    if (!empty($vehicle->id)) {
      $database->setQuery("SELECT * FROM #__vehiclemanager_video_source WHERE fk_vehicle_id=" . $vehicle->id);
      $videos = $database->loadObjectList();
    }
    $youtube = new stdClass();
    for ($i = 0;$i < count($videos);$i++) {
      if (!empty($videos[$i]->youtube)) {
        $youtube->code = $videos[$i]->youtube;
        $youtube->id = $videos[$i]->id;
        break;
      }
    }
    if (!empty($vehicle->id)) { //check video file
      $database->setQuery("SELECT * FROM #__vehiclemanager_track_source WHERE fk_vehicle_id=" . $vehicle->id);
      $tracks = $database->loadObjectList();
    }
////////////////////////////////END check video/audio files \\\\\\\\\\\\\\\\\\
    if (trim($vehicle->id) != ""){
      $query = "SELECT id, price_from, price_to, special_price, comment_price, priceunit
                FROM #__vehiclemanager_rent_sal
                WHERE fk_vehiclesid='$vehicle->id'
                ORDER BY id DESC";
      $database->setQuery($query);
      $vehicle_rent_sal = $database->loadObjectList();
    }
    $query = "SELECT * ";
    $query .= "FROM #__vehiclemanager_feature as f ";
    $query .= "WHERE f.published = 1 ";

    // Sorting features
    // $query .= "ORDER BY f.categories";
    $query .= "ORDER BY f.categories, f.name";

    $database->setQuery($query);
    $vehicle_feature = $database->loadObjectList();
    for ($i = 0; $i < count($vehicle_feature); $i++) {
      $feature = "";
      if (!empty($vehicle->id)){
        $query = "SELECT id ";
        $query .= "FROM #__vehiclemanager_feature_vehicles ";
        $query .= "WHERE fk_featureid =" . $vehicle_feature[$i]->id . " AND fk_vehicleid =" . $vehicle->id;
        $database->setQuery($query);
        $feature = $database->loadResult();
        if ($feature){
          $vehicle_feature[$i]->check = 1;
        }
        else
          $vehicle_feature[$i]->check = 0;
      }else{
        $vehicle_feature[$i]->check = 0;
      }
    }
    $currencys = explode(';', $vehiclemanager_configuration['currency']);
    foreach ($currencys as $row) {
      if ($row != ''){
        $row = explode("=", $row);
        $currency_temp[] = mosHTML::makeOption($row[0], $row[0]);
      }
    }
    $owner_email = '';
    if ($vehicle->owner_id > 0) {
      $database->setQuery("SELECT email FROM #__users WHERE `id`= '" .$vehicle->owner_id."'");
      $www= $database->loadResult();
      if (strlen( $vehicle->owneremail) > 0 && $www !=  $vehicle->owneremail)
        $owner_email = $vehicle->owneremail;
      else
        $owner_email = $www;
    }
    if($vid){
    }else{
        $vehicle->owner_id = $my->id;
    }
    $currency = mosHTML::selectList($currency_temp, 'priceunit', 'class="inputbox" size="1"', 'value', 'text', $vehicle->priceunit);
    $currency_spacial_price = mosHTML::selectList($currency_temp, 'currency_spacial_price[]',
                                         'class="inputbox" size="1"', 'value', 'text', $vehicle->priceunit);
    $query = "SELECT lang_code, title
              FROM #__languages";
    $database->setQuery($query);
    $languages = $database->loadObjectList();
    $languages_row[] = mosHTML::makeOption('*', 'All');
    foreach ($languages as $language) {
      $languages_row[] = mosHTML::makeOption($language->lang_code, $language->title);
    }
    $languages = mosHTML::selectList($languages_row, 'language',
                                       'class="inputbox" size="1"', 'value', 'text', $vehicle->language);
    for($i=6;$i<=10;$i++){
    $extraOption = array();
    $extraOption[] = mosHtml::makeOption(0, _VEHICLE_MANAGER_OPTION_SELECT);
  $name = "_VEHICLE_MANAGER_EXTRA" . $i . "_SELECTLIST";
  $extra = explode(',', constant($name));
  $j = 1;
  foreach($extra as $extr){
    $extraOption[] = mosHTML::makeOption($j, $extr);
    $j++;
  }
  switch ($i) {
    case 6:
      $extraSelect = $vehicle->extra6;
      break;
    case 7:
      $extraSelect = $vehicle->extra7;
      break;
    case 8:
      $extraSelect = $vehicle->extra8;
      break;
    case 9:
      $extraSelect = $vehicle->extra9;
      break;
    case 10:
      $extraSelect = $vehicle->extra10;
      break;
  }
  $extra_list[] = mosHTML::selectList($extraOption, 'extra' . $i, 'class="inputbox" size="1"', 'value', 'text', $extraSelect);
  }

  HTML_vehiclemanager::editVehicle($option, $vehicle, $clist, $ratinglist, $delete_edoc,$videos,$youtube, $tracks, $reviews, $test_list, $vehicle_type_list, $listing_status_list, $condition_status_list, $transmission_type_list, $listing_type_list, $drive_type_list, $fuel_type_list, $num_speed_list, $num_cylinder_list, $num_doors_list, $vehicle_photo,$vehicle_temp_photos, $vehicle_photos, $maker, $temp, $currentmodel, $modellist, $vehicle_rent_sal, $vehicle_feature, $currency, $languages, $extra_list,$owner_email,
          $currency_spacial_price, $associateArray, $country, $region, $city);
  }

  function getMonth($month)
  {

    switch ($month) {
        case 1:
            $smonth = JText::_('JANUARY');
            break;
        case 2:
            $smonth = JText::_('FEBRUARY');
            break;
        case 3:
            $smonth = JText::_('MARCH');
            break;
        case 4:
            $smonth = JText::_('APRIL');
            break;
        case 5:
            $smonth = JText::_('MAY');
            break;
        case 6:
            $smonth = JText::_('JUNE');
            break;
        case 7:
            $smonth = JText::_('JULY');
            break;
        case 8:
            $smonth = JText::_('AUGUST');
            break;
        case 9:
            $smonth = JText::_('SEPTEMBER');
            break;
        case 10:
            $smonth = JText::_('OCTOBER');
            break;
        case 11:
            $smonth = JText::_('NOVEMBER');
            break;
        case 12:
            $smonth = JText::_('DECEMBER');
            break;
    }

    return $smonth;
  }

  function guid()
  {
    if (function_exists('com_create_guid'))
    {
  return trim(com_create_guid(), '{}');

    } else
    {
        mt_srand((double) microtime() * 10000); //optional for php 4.2.0 and up.
        $charid = strtoupper(md5(uniqid(rand(), true)));
        $hyphen = chr(45);
        $uuid = substr($charid, 0, 8) . $hyphen
                . substr($charid, 8, 4) . $hyphen
                . substr($charid, 12, 4) . $hyphen
                . substr($charid, 16, 4) . $hyphen
                . substr($charid, 20, 12);
        return $uuid;
    }
  }

  function  saveVehicle($option, $task){
    global $database, $my, $mosConfig_absolute_path, $mosConfig_live_site, $vehiclemanager_configuration, $config;
    //check how the other info should be provided
    $vehicle = new mosVehicleManager($database);

    $post = JFactory::getApplication()->input->post->getArray(array(), null, 'raw');    
    $vehicle->bind($post) ;
    // if there is no owner, get your id as owner_id
    if ($vehicle->owneremail == ''){
        $vehicle->owner_id = $my->id;
    }else{
        $vehicle->owner_id = $post['owner_id'];
    }

    //get ordering value
    $query = "SELECT MIN(ordering) FROM #__vehiclemanager_vehicles WHERE ordering != '0'";
    $database->setQuery($query);
    $min_ordering = $database->loadResult();
    if($min_ordering){
        if($min_ordering == 1){
            $min_ordering = $min_ordering - 2;
        }else{
            $min_ordering = $min_ordering - 1;
        }
    }else{
        $min_ordering = 1;
    }

    //set ordering value
    if(isset($_POST['id'])){

        $vehicle_ordering = new mosVehicleManager($database);
        $vehicle_ordering->load((int)$_POST['id']);

        if($vehicle_ordering->ordering == 0){
            $vehicle->ordering = $min_ordering;
        }
    }


  /*******Call function to Save changes for associated vehicles**********/
    save_vehicle_associate();
  /*********************************************/
    if ($_POST['edocument_Link'] != ''){
      $vehicle->edok_link = $_POST['edocument_Link'];
    }
    //delete evehicle file if neccesary
    $delete_edoc = mosGetParam($_POST, 'delete_edoc', 0);
    if ($delete_edoc != '0'){
      $retVal = unlink($mosConfig_absolute_path . $delete_edoc);
      $vehicle->edok_link = "";
    }
    //storing e-vehicle
    $edfile = $_FILES['edoc_file'];
    //check if fileupload is correct
    if ($vehiclemanager_configuration['edocs']['allow']
        && intval($edfile['error']) > 0
        && intval($edfile['error']) < 4){
      echo "<script> alert('" . _VEHICLE_MANAGER_LABEL_EDOCUMENT_UPLOAD_ERROR .
      "'); window.history.go(-1); </script>\n";
      exit();
    } else if ($vehiclemanager_configuration['edocs']['allow']
          && intval($edfile['error']) != 4){
      $uploaddir = $mosConfig_absolute_path . $vehiclemanager_configuration['edocs']['location'];

        // delete spaces and forbidden characters in file name
        // $_FILES['edoc_file']['name'] = preg_replace('/\s+/', '-', $_FILES['edoc_file']['name']);
        $file_name_clear_ext = vh_remove_forbidden_characters_whitespaces_from_file_name( $_FILES['edoc_file']['name'] );

      // $file_new = $uploaddir . $_FILES['edoc_file']['name'];
        $file_new = $uploaddir . $file_name_clear_ext;

      $ext = pathinfo($_FILES['edoc_file']['name'], PATHINFO_EXTENSION);
      $ext = strtolower($ext);
      $allowed_exts = explode(",", $vehiclemanager_configuration['allowed_exts']);
      if (!in_array($ext, $allowed_exts)){
        echo "<script> alert(' File ext. not allowed to upload! - " . $edfile['name'] . "'); window.history.go(-1); </script>\n";
        exit();
      }
      $file['type'] = $_FILES['edoc_file']['type'];
      $db = JFactory::getDbo();
      $db->setQuery("SELECT mime_type FROM #__vehiclemanager_mime_types WHERE `mime_ext` = " . $db->quote($ext). " and mime_type = " . $db->quote($file['type']) );
      $file_db_mime = $db->loadResult();
      if ($file_db_mime != $file['type']){
        echo "<script> alert(' File mime type not match file ext. - " . $edfile['name'] . "'); window.history.go(-1); </script>\n";
        exit();
      }
      if (!copy($_FILES['edoc_file']['tmp_name'], $file_new)){
        echo "<script> alert('error: not copy'); window.history.go(-1); </script>\n";
        exit();
      } else {

        // $vehicle->edok_link = $mosConfig_live_site . $vehiclemanager_configuration['edocs']['location'] . $edfile['name'];
      // JPATH_ROOT
        $vehicle->edok_link = $mosConfig_live_site . $vehiclemanager_configuration['edocs']['location'] . $file_name_clear_ext;

      }
    }else{
      if ($_POST['edocument_Link'] != ''){
        $vehicle->edok_link = $_POST['edocument_Link'];
      }
      //delete evehicle file if neccesary
      $delete_edoc = mosGetParam($_POST, 'delete_edoc', 0);
      if ($delete_edoc != '0'){
        $retVal = unlink($mosConfig_absolute_path . $delete_edoc);
        $vehicle->edok_link = "";
      }
    }
    if ($vehiclemanager_configuration['publish_on_add']['show']){
      $vehicle->published = 1;
    } else {
      $vehicle->published = 0;
    }
    if ($vehiclemanager_configuration['approve_on_add']['show']){
      $vehicle->approved = 1;
    } else {
      $vehicle->approved = 0;
    }
    if (is_string($vehicle)){
      echo "<script> alert('" . $vehicle . "'); window.history.go(-1); </script>\n";
      exit();
    }
    $vehicle->date = date("Y-m-d H:i:s");

    if (!$vehicle->check()){
        return;
    }

    $vehicle->store();
    $vehicle->saveCatIds($vehicle->catid);
    $vehicle->checkin();
    // $vehicle->updateOrder("catid='$vehicle->catid'");
    $vehicle->updateOrder('catid="$vehicle->catid"');

    //save dynamic files in a folder 'photos'
    $uploaddir = $mosConfig_absolute_path . '/components/com_vehiclemanager/photos/';
    if($_REQUEST['idtrue']){
      $uploaddir = $mosConfig_absolute_path . '/components/com_vehiclemanager/photos/';
      $code = guid();
      $vehicle_true_id=$_REQUEST['idtrue'];
      $query = "select main_img from #__vehiclemanager_photos WHERE fk_vehicleid='$vehicle_true_id' order by img_ordering,id";
      $database->setQuery($query);
      $vehicle_temp_photos = $database->loadObjectList();
      $query = "select image_link from #__vehiclemanager_vehicles WHERE id='$vehicle_true_id' order by id";
      $database->setQuery($query);
      $vehicle_mail_photos = $database->loadObject();
      
      function createNewName($name){
        $regExp = '/^[A-Z0-9]{8}-[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{10}/';
        if(preg_match($regExp, $name)){
            $substrName = createNewName(mb_substr($name, 36, strlen($name)));
        }else{
            $substrName = $name;
        }
        return $substrName;
      }
      $vehicle_mail_photos_clon = $code.createNewName($vehicle_mail_photos->image_link);
      if (copy($uploaddir.$vehicle_mail_photos->image_link, $uploaddir.$vehicle_mail_photos_clon)){
        $database->setQuery("UPDATE #__vehiclemanager_vehicles SET image_link = '$vehicle_mail_photos_clon' WHERE id=" . $vehicle->id);
        $database->execute();
      }

      foreach($vehicle_temp_photos as $val){
        $trueImg = $uploaddir.$val->main_img;
        $nameImgWithoutCode = createNewName($val->main_img);
        $file_name = $code.$nameImgWithoutCode;
        $clonImg = $uploaddir.$file_name;
        if (copy($trueImg, $clonImg)){
          $database->setQuery("INSERT INTO #__vehiclemanager_photos (fk_vehicleid, main_img)
                              VALUES ( '$vehicle->id','$file_name')");
          $database->execute();
        }
      }
      $file_new_url = str_replace($vehiclemanager_configuration['edocs']['location'], $vehiclemanager_configuration['edocs']['location'].$code, $_REQUEST['edocument_Link']);
      $file_name = explode($vehiclemanager_configuration['edocs']['location'], $_REQUEST['edocument_Link']);
      $file_new = $mosConfig_absolute_path.$vehiclemanager_configuration['edocs']['location'].$code.$file_name[1];
      $file_true = $mosConfig_absolute_path.$vehiclemanager_configuration['edocs']['location'].$file_name[1];
      if (copy($file_true, $file_new)){
        $sql="UPDATE #__vehiclemanager_vehicles SET edok_link ='$file_new_url' WHERE id=" . $vehicle->id;
        $database->setQuery($sql);
        $database->execute();
      }
      //edok_link
      $vehicle->edok_link=$file_new_url;
      foreach ($vehicle_temp_photos as $vehicle_temp_photo) {
        $vehicle_temp_photo->main_img= $code.$vehicle_temp_photo->main_img;
        $vehicle_photos[] = array($vehicle_temp_photo->main_img, vm_picture_thumbnail($vehicle_temp_photo->main_img, $vehiclemanager_configuration['foto']['high'], $vehiclemanager_configuration['foto']['width']));
      }
      //end clon
  }
    /********************* if count photo group > count photo user not published******************/
      $count_foto_for_single_group = '';
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
      $query = "select main_img from #__vehiclemanager_photos WHERE fk_vehicleid='$vehicle->id' order by img_ordering,id";
      $database->setQuery($query);
      $vehicle_temp_photos = $database->loadObjectList();
      if(count($vehicle_temp_photos) != 0)
      {
          $count_foto_for_single_group = $count_foto_for_single_group - count($vehicle_temp_photos);
      }
  /**************************************************************************************************/
    if (array_key_exists("new_photo_file", $_FILES)){
      for ($i = 0; $i < $count_foto_for_single_group; $i++) {
          if (!empty($_FILES['new_photo_file']['name'][$i])) {

          // delete spaces and forbidden characters in file name
          // $_FILES['new_photo_file']['name'][$i] = preg_replace('/\s+/', '-', $_FILES['new_photo_file']['name'][$i]);
          $_FILES['new_photo_file']['name'][$i] = vh_remove_forbidden_characters_whitespaces_from_file_name( $_FILES['new_photo_file']['name'][$i] );
          //------------------------------
          $code = guid();
          $uploadfile = $uploaddir . $code . "_" . $_FILES['new_photo_file']['name'][$i];
          $file_name = $code . "_" . $_FILES['new_photo_file']['name'][$i];
          $ext = pathinfo($_FILES['new_photo_file']['name'][$i], PATHINFO_EXTENSION);
          $ext = strtolower($ext);
          $allowed_exts = explode(",", $vehiclemanager_configuration['allowed_exts_img']);
          if (!in_array($ext, $allowed_exts)){
            echo "<script> alert(' File ext. not allowed to upload! - " . $_FILES['new_photo_file']['name'][$i] . "'); window.history.go(-1); </script>\n";
            exit();
          }
          $file['type'] = $_FILES['new_photo_file']['type'][$i];
          $db = JFactory::getDbo();
          $db->setQuery("SELECT mime_type FROM #__vehiclemanager_mime_types WHERE `mime_ext` = " . $db->quote($ext). " and mime_type = " . $db->quote($file['type']) );
          $file_db_mime = $db->loadResult();
          if ($file_db_mime != $file['type']){
            echo "<script> alert(' File mime type not match file ext. - " . $_FILES['new_photo_file']['name'][$i] . "'); window.history.go(-1); </script>\n";
            exit();
          }
          if (copy($_FILES['new_photo_file']['tmp_name'][$i], $uploadfile)){

                // Add rotate exif img
                if( $vehiclemanager_configuration['rotate_img'] == '1' ){
                    vm_rotateImage( $uploaddir . $file_name );
                }

            $info = getimagesize($uploaddir . $file_name, $imageinfo);
            $file_width = $info[0];
            $file_height = $info[1];

            if ( $file_width > $vehiclemanager_configuration['fotoupload']['width'] || $file_height > $vehiclemanager_configuration['fotoupload']['high']) {
                $tmp_file = vm_picture_thumbnail($file_name, $vehiclemanager_configuration['fotoupload']['high'], $vehiclemanager_configuration['fotoupload']['width']);
                copy($uploaddir . $tmp_file, $uploaddir . $file_name);
                unlink($uploaddir . $tmp_file);
            }

            $database->setQuery("INSERT INTO #__vehiclemanager_photos (fk_vehicleid, main_img) VALUES ( '$vehicle->id','$file_name')");
            $database->execute();
          }
        }
    }
  }  //end if
  $uploaddir = $mosConfig_absolute_path . '/components/com_vehiclemanager/photos/';
  if ($_FILES['image_link']['name'] != ''){
    $code = guid();
    // delete spaces in file name
    // delete spaces and forbidden characters in file name
    // $_FILES['image_link']['name'] = preg_replace('/\s+/', '-', $_FILES['image_link']['name']);
    $_FILES['image_link']['name'] = vh_remove_forbidden_characters_whitespaces_from_file_name( $_FILES['image_link']['name'] );
    //--------------------------------
    $uploadfile = $uploaddir . $code . "_" . $_FILES['image_link']['name'];
    $file_name = $code . "_" . $_FILES['image_link']['name'];
    $ext = pathinfo($_FILES['image_link']['name'], PATHINFO_EXTENSION);
    $ext = strtolower($ext);
    $allowed_exts = explode(",", $vehiclemanager_configuration['allowed_exts_img']);

    if (!in_array($ext, $allowed_exts)){
        echo "<script> alert(' File ext. not allowed to upload! - " . $_FILES['image_link']['name'] . "'); window.history.go(-1); </script>\n";
        exit();
    }
    $file['type'] = $_FILES['image_link']['type'];
    $db = JFactory::getDbo();
    $db->setQuery("SELECT mime_type FROM #__vehiclemanager_mime_types WHERE `mime_ext` = " . $db->quote($ext). " and mime_type = " . $db->quote($file['type']) );
    $file_db_mime = $db->loadResult();
    if ($file_db_mime != $file['type']){
        echo "<script> alert(' File mime type not match file ext. - " . $_FILES['image_link']['name'] . "'); window.history.go(-1); </script>\n";
        exit();
    }

    if (copy($_FILES['image_link']['tmp_name'], $uploadfile)) {

        // Add rotate exif img
        if( $vehiclemanager_configuration['rotate_img'] == '1' ){
            vm_rotateImage( $uploaddir . $file_name );
        }

        $info = getimagesize($uploaddir . $file_name, $imageinfo);
        $file_width = $info[0];
        $file_height = $info[1];

        if ( $file_width > $vehiclemanager_configuration['fotoupload']['width'] || $file_height > $vehiclemanager_configuration['fotoupload']['high']) {
            $tmp_file = vm_picture_thumbnail($file_name, $vehiclemanager_configuration['fotoupload']['high'], $vehiclemanager_configuration['fotoupload']['width']);
            copy($uploaddir . $tmp_file, $uploaddir . $file_name);
            unlink($uploaddir . $tmp_file);
        }


        //deletion MAIN IMAGE WHICH WAS BEFORE
        $database->setQuery("select image_link  FROM  #__vehiclemanager_vehicles where  id ="
              . $vehicle->id . "");
        $image_link = $database->loadObjectList();

        if(!empty($image_link[0]->image_link) ) 
          unlink($mosConfig_absolute_path . 
              '/components/com_vehiclemanager/photos/'
              . $image_link[0]->image_link);

        //separation of the file name in the name and extension
        $del_main_phot = pathinfo($image_link[0]->image_link);
        $del_main_photo_type = '.' . $del_main_phot['extension'];
        $del_main_photo_name = basename($image_link[0]->image_link, $del_main_photo_type);

        if(!empty($del_main_photo_name) ){
          $path = $mosConfig_absolute_path . '/components/com_vehiclemanager/photos/';
          $check_files = JFolder::files($path, '^' . $del_main_photo_name . '.*$', false, true);
          foreach ($check_files as $check_file) {
              unlink($check_file);
          }
        }
        //END deletion MAIN IMAGE WHICH WAS BEFORE   



        $database->setQuery("UPDATE #__vehiclemanager_vehicles SET image_link='$file_name' WHERE id=" . $vehicle->id);
        $database->execute();
    }
  }//end if
  //ordering_photo


    if(protectInjectionWithoutQuote('veh_img_ordering', '', 'STRING')){
        $ordering = protectInjectionWithoutQuote('veh_img_ordering', '', 'STRING');


        $ordering = explode(',', $ordering);
        foreach ($ordering as $key => $value) {
            $query = "UPDATE #__vehiclemanager_photos SET img_ordering = $key WHERE main_img='".$value."'";
            $database->setQuery($query);
            $database->execute();
        }
    }
    //end ordering

  /////////////save video/tracks functions\\\\\\\\\\\\\\\\\\\\\\
    VHStoreVideo($vehicle);
    VHStoreTrack($vehicle);
  /////////////////////////\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\

    //check the files marked for deletion
    if (array_key_exists("del_main_photo", $_POST)){
        $del_main_photo = $_POST['del_main_photo'];
        if ($del_main_photo != ''){
            $file_inf = pathinfo($del_main_photo);
            $file_type = '.' . $file_inf['extension'];
            $file_name = basename($del_main_photo, $file_type);

            if(strlen($file_name) > 20){

                $path = $mosConfig_absolute_path . '/components/com_vehiclemanager/photos/';
                $check_files = JFolder::files($path, '^' . $file_name . '.*$', false, true);
                foreach ($check_files as $check_file) {
                    unlink($check_file);
                }

            }

        }
        //Database changes
        $database->setQuery("UPDATE #__vehiclemanager_vehicles SET image_link='' WHERE id=" . $vehicle->id);
        $database->execute();

    } //end if

    if (array_key_exists("del_photos", $_POST)){
        if (count($_POST['del_photos']) != 0){
            for ($i = 0; $i < count($_POST['del_photos']); $i++) {
                $del_photo = $_POST['del_photos'][$i];
                $database->setQuery("DELETE FROM #__vehiclemanager_photos WHERE main_img='$del_photo'");
                $database->execute() ;
                {
                    $file_inf = pathinfo($del_photo);
                    $file_type = '.' . $file_inf['extension'];
                    $file_name = basename($del_photo, $file_type);
                    if(strlen($file_name) < 20) continue;
                    $path = $mosConfig_absolute_path . '/components/com_vehiclemanager/photos/';
                    $check_files = JFolder::files($path, '^' . $file_name . '.*$', false, true);
                    foreach ($check_files as $check_file) {
                        unlink($check_file);
                    }
                }
            }
        }
    }

    if (isset($_POST['del_rent_sal'])){

        for ($i = 0; $i < count($_POST['del_rent_sal']); $i++) {
            $del_rent_sal = $_POST['del_rent_sal'][$i];
            $database->setQuery("DELETE FROM #__vehiclemanager_rent_sal WHERE id ='$del_rent_sal'");
            $database->execute();
        }
    }

    if (isset($_POST['feature'])) {
        $feature = $_POST['feature'];
        $database->setQuery("DELETE FROM #__vehiclemanager_feature_vehicles
                              WHERE fk_vehicleid = " . $vehicle->id);
        $database->execute();
        for ($i = 0; $i < count($feature); $i++) {
            $database->setQuery("INSERT INTO #__vehiclemanager_feature_vehicles (fk_vehicleid, fk_featureid)
                                VALUES (" . $vehicle->id . ", " . $feature[$i] . ")");
            $database->execute();
        }
    } else {
        $database->setQuery("DELETE FROM #__vehiclemanager_feature_vehicles WHERE fk_vehicleid = " . $vehicle->id);
        $database->execute();
    }
    VHDeleteVideos($vehicle->id);
    VHDeleteTracks($vehicle->id);

    switch ($task) {
        case 'apply':
            mosRedirect("index.php?option=" . $option . "&task=edit&vid[]=" . $vehicle->id);
            break;

        case 'save':
            mosRedirect("index.php?option=" . $option);
            break;
    }
  }


  /**
  * Deletes one or more records
  * @param array - An array of unique category id numbers
  * @param string - The current author option
  */
  // Delete a vehicle(s) on admin
  function removeVehicles($vid, $option,$if_clon=NULL){
    global $database, $mosConfig_absolute_path;

    if (!is_array($vid) || count($vid) < 1){
        echo "<script> alert('Select an item to delete'); window.history.go(-1);</script>\n";
        exit;
    }

    foreach ($vid as $item_id) {
        $vehicle = new mosVehicleManager($database);
        $vehicle->load($item_id);
        $vehicle->deleteVehicle();
        $vehicle = null ;
    }

  /***********************************************************************************************/
  // this more quicly remove all vehicles from array
  //   for($i = 0; $i < count($vid); $i++){

  //       $query = "select associate_vehicle from #__vehiclemanager_vehicles where id =".$vid[$i];
  //       $database->setQuery($query);
  //       $vehicleAssociateVehicle = $database->loadResult();

  //       $assocVehicleObj = unserialize($vehicleAssociateVehicle);
  //       $idWhereChange = array();
  //       if(!empty($assocVehicleObj)){
  //           foreach ($assocVehicleObj as $key => $value) {
  //               if($value == $vid[$i]){
  //                   $assocVehicleObj[$key] = null;
  //               }else if($value){
  //                   $idWhereChange[] = $value;
  //               }
  //           }

  //           $stringIdWhereChange = implode(',', $idWhereChange);
  //           $newAssocSerialize = serialize($assocVehicleObj);
  //           if(!empty($stringIdWhereChange)){
  //               $query = "update #__vehiclemanager_vehicles set associate_vehicle ='$newAssocSerialize' where id in($stringIdWhereChange)";
  //               $database->setQuery($query);
  //               $database->execute();
  //           }
  //       }
  //   }

  // /***********************************************************************************************/

  //   if (count($vid)){
  //       $vids = implode(',', $vid);
  //       $database->setQuery("SELECT image_link FROM  #__vehiclemanager_vehicles WHERE id IN (" . $vids . ")");

  //       $del_photo = $database->loadObjectList();
  //       for ($i = 0; $i < count($del_photo); $i++) {
  //           if ($del_photo[$i]->image_link != '' && !$if_clon){

  //               $del_photo_mask_inf = pathinfo($del_photo[$i]->image_link);    // arr
  //               $del_photo_mask_type = '.' . $del_photo_mask_inf['extension']; // .jpg
  //               $del_photo_mask = basename($del_photo[$i]->image_link, $del_photo_mask_type);

  //               if(strlen($del_photo_mask) < 20) continue;

  //               $path = $mosConfig_absolute_path . '/components/com_vehiclemanager/photos';
  //               $check_files = JFolder::files($path, '^' . $del_photo_mask . '.*$', false, true);

  //               if (!empty($check_files)) {
  //                   foreach ($check_files as $check_file) {
  //                       unlink($check_file);
  //                   }
  //               }

  //               $path = $mosConfig_absolute_path . '/components/com_vehiclemanager/photos/watermark';
  //               $check_files = JFolder::files($path, '^' . $del_photo_mask . '.*$', false, true);

  //               if (!empty($check_files)) {
  //                   foreach ($check_files as $check_file) {
  //                       unlink($check_file);
  //                   }
  //               }

  //           }
  //       }

  //       $database->setQuery("DELETE FROM #__vehiclemanager_review WHERE fk_vehicleid IN ($vids)");
  //       $database->execute();

  //       $database->setQuery("SELECT main_img FROM #__vehiclemanager_photos WHERE fk_vehicleid IN ($vids)");
  //       $del_photos = $database->loadObjectList();

  //       for ($i = 0; $i < count($del_photos); $i++) {
  //           if ($del_photos[$i]->main_img != ''){
  //               $del_photos_mask_inf = pathinfo($del_photos[$i]->main_img);
  //               $del_photos_mask_type = '.' . $del_photos_mask_inf['extension'];
  //               $del_photos_mask = basename($del_photos[$i]->main_img, $del_photos_mask_type);

  //               if(strlen($del_photos_mask) < 20) continue;

  //               $path = $mosConfig_absolute_path . '/components/com_vehiclemanager/photos';
  //               $check_files = JFolder::files($path, '^' . $del_photos_mask . '.*$', false, true);

  //               if (!empty($check_files)){
  //                   foreach ($check_files as $check_file) {
  //                       unlink($check_file);
  //                   }
  //               }

  //               $path = $mosConfig_absolute_path . '/components/com_vehiclemanager/photos/watermark';
  //               $check_files = JFolder::files($path, '^' . $del_photos_mask . '.*$', false, true);

  //               if (!empty($check_files)){
  //                   foreach ($check_files as $check_file) {
  //                       unlink($check_file);
  //                   }
  //               }

  //           }
  //       }

  //       $database->setQuery("DELETE FROM #__vehiclemanager_feature_vehicles WHERE fk_vehicleid IN ($vids)");
  //       $database->execute();

  //       $database->setQuery("DELETE FROM #__vehiclemanager_photos WHERE fk_vehicleid IN ($vids)");
  //       $database->execute();

  //       $database->setQuery("DELETE FROM #__vehiclemanager_categories WHERE iditem IN ($vids)");
  //       $database->execute();

  //       $database->setQuery("DELETE FROM #__vehiclemanager_vehicles WHERE id IN ($vids)");
  //       $database->execute();

  //       $database->setQuery("DELETE FROM #__vehiclemanager_rent_sal WHERE fk_vehiclesid IN ($vids)");
  //       $database->execute();

  //       $database->setQuery("DELETE FROM #__vehiclemanager_track_source WHERE fk_vehicle_id IN ($vids)");
  //       $database->execute();

  //       $database->setQuery("DELETE FROM #__vehiclemanager_video_source WHERE fk_vehicle_id IN ($vids)");
  //       $database->execute();

  //   }

    mosRedirect("index.php?option=$option");
  }

  /**
  * Publishes or Unpublishes one or more records
  * @param array - An array of unique category id numbers
  * @param integer - 0 if unpublishing, 1 if publishing
  * @param string - The current author option
  */


  function rentPrice($vid,$rent_from,$rent_until,$special_price,$comment_price,$currency_spacial_price){
    rentPriceVM($vid,$rent_from,$rent_until,$special_price,$comment_price,$currency_spacial_price);
  }


  function clonVehicle($vid, $option){}

  function publishVehicles($vid, $publish, $option){

    global $database, $my;

    $catid = mosGetParam($_POST, 'catid', array(0));

    if (!is_array($vid) || count($vid) < 1){
        $action = $publish ? 'publish' : 'unpublish';
        echo "<script> alert('Select an item to $action'); window.history.go(-1);</script>\n";
        exit;
    }

    $vids = implode(',', $vid);

    $database->setQuery("UPDATE #__vehiclemanager_vehicles SET published='$publish'" .
            "\nWHERE id IN ($vids) AND (checked_out=0 OR (checked_out='$my->id'))");
    $database->execute();

    if (count($vid) == 1){
        $row = new mosVehicleManager($database);
        $row->checkin($vid[0]);
    }

    mosRedirect("index.php?option=$option");
  }

  /**
  * Approve or Unapprove one or more records
  * @param array - An array of unique category id numbers
  * @param integer - 0 if unapprove, 1 if approve
  * @param string - The current author option
  */
  function approveVehicles($vid, $approve, $option)
  {
    global $database, $my;

    $catid = mosGetParam($_POST, 'catid', array(0));

    if (!is_array($vid) || count($vid) < 1)
    {
        $action = $approve ? 'approve' : 'unapprove';
        echo "<script> alert('Select an item to $action'); window.history.go(-1);</script>\n";
        exit;
    }

    $vids = implode(',', $vid);

    $database->setQuery("UPDATE #__vehiclemanager_vehicles SET  approved='$approve'" .
            "\nWHERE id IN ($vids) AND (checked_out=0 OR (checked_out='$my->id'))"); //published=$approve,
    $database->execute();

    if (count($vid) == 1)
    {
        $row = new mosVehicleManager($database);
        $row->checkin($vid[0]);
    }

    mosRedirect("index.php?option=$option");
  }

  /**
  * Moves the order of a record
  * @param integer - The increment to reorder by
  */
  function orderVehicles($vid, $inc, $option)
  {
    global $database;
    $vehicle = new mosVehicleManager($database);
    $vehicle->load($vid);
    $vehicle->move($inc);
    mosRedirect("index.php?option=$option");
  }


  function sortVehicles($vid = array(), $order = array(), $option) {
    global $database;
    $vehicle = new mosVehicleManager($database);

    for($i=0;$i<count($vid),$i<count($order);$i++){
        $vehicle->load($vid[$i]);
        $vehicle->ordering = $order[$i];
        $vehicle->store();
    }

    return true;
  }


  function sortCategories($vid = array(), $order = array(), $option) {
    global $database;
    $category = new mainVehiclemanagerCategories($database);

    for($i=0;$i<count($vid),$i<count($order);$i++){
        $category->load($vid[$i]);
        $category->ordering = $order[$i];
        $category->store();
    }

    return true;
  }

  /**
  * Cancels an edit operation
  * @param string - The current author option
  */
  function cancelVehicle($option){
    global $database;
    $row = new mosVehicleManager($database);
    if($_REQUEST['idtrue']){
        $vid[]=$_REQUEST['id'];

        removeVehicles($vid,$option,TRUE);
    }

    $row->bind($_POST);
    $row->checkin();
    mosRedirect("index.php?option=$option");
  }



    function reset_watermark() {

        $pathWat = JPATH_SITE . '/components/com_vehiclemanager/photos/watermark/';
        $start = microtime(true);
        array_map('unlink', glob($pathWat."*"));
        return;

    }


  function configure_save_frontend($option)
  {
    // global $my, $vehiclemanager_configuration;
    global $my, $vehiclemanager_configuration, $database, $mosConfig_absolute_path;

    $str = '';
    $supArr = array();
    $supArr = mosGetParam($_POST, 'edocs_registrationlevel', '');
    if ( isset($supArr) && $supArr > 0 ) {
        for ($i = 0; $i < count($supArr); $i++) {
            $str.=$supArr[$i] . ',';
        }
    }
    $str = substr($str, 0, -1);
    $vehiclemanager_configuration['edocs']['registrationlevel'] = -2;

    $str = '';
    $supArr = mosGetParam($_POST, 'reviews_registrationlevel', 0);
    if ( isset($supArr) && $supArr > 0 ) {
        for ($i = 0; $i < count($supArr); $i++) {
            $str.=$supArr[$i] . ',';
        }
    }
    $str = substr($str, 0, -1);
    $vehiclemanager_configuration['reviews']['registrationlevel'] = -2;

    $str = '';
    $supArr = mosGetParam($_POST, 'rentrequest_registrationlevel', 0);
    if ( isset($supArr) && $supArr > 0 ) {
        for ($i = 0; $i < count($supArr); $i++) {
            $str.=$supArr[$i] . ',';
        }
    }
    $str = substr($str, 0, -1);
    $vehiclemanager_configuration['rentrequest']['registrationlevel'] = -2;

    $str = '';
    $supArr = mosGetParam($_POST, 'buyrequest_registrationlevel', 0);
    if ( isset($supArr) && $supArr > 0 ) {
        for ($i = 0; $i < count($supArr); $i++) {
            $str.=$supArr[$i] . ',';
        }
    }
    $str = substr($str, 0, -1);
    $vehiclemanager_configuration['buyrequest']['registrationlevel'] = -2;

    $str = '';
    $supArr = mosGetParam($_POST, 'payment_buy_registrationlevel', 0);
    if ( isset($supArr) && $supArr > 0 ) {
        for ($i = 0; $i < count($supArr); $i++) {
            $str.=$supArr[$i] . ',';
        }
    }
    $str = substr($str, 0, -1);
    $vehiclemanager_configuration['payment_buy']['registrationlevel'] = 8;

    $str = '';
    $supArr = mosGetParam($_POST, 'payment_rent_registrationlevel', 0);
    if ( isset($supArr) && $supArr > 0 ) {
        for ($i = 0; $i < count($supArr); $i++) {
            $str.=$supArr[$i] . ',';
        }
    }
    $str = substr($str, 0, -1);
    $vehiclemanager_configuration['payment_rent']['registrationlevel'] = 8;

    $str = '';
    $supArr = mosGetParam($_POST, 'Location_vehicle_registrationlevel');
    if ( isset($supArr) && $supArr > 0 ) {
        for ($i = 0; $i < count($supArr); $i++) {
            $str.=$supArr[$i] . ',';
        }
    }
    $str = substr($str, 0, -1);
    $vehiclemanager_configuration ['Location_vehicle']['registrationlevel'] = -2;

    $str = '';
    $supArr = mosGetParam($_POST, 'contacts_registrationlevel', 0);
    if ( isset($supArr) && $supArr > 0 ) {
        for ($i = 0; $i < count($supArr); $i++) {
            $str.=$supArr[$i] . ',';
        }
    }
    $str = substr($str, 0, -1);
    $vehiclemanager_configuration ['contacts']['registrationlevel'] = -2;

    $str = '';
    $supArr = mosGetParam($_POST, 'cb_registrationlevel', 0);
    if ( isset($supArr) && $supArr > 0 ) {
        for ($i = 0; $i < count($supArr); $i++)
            $str.=$supArr[$i] . ',';
    }
    $str = substr($str, 0, -1);
    $vehiclemanager_configuration['cb']['registrationlevel'] = -2;

    $str = '';
    $supArr = mosGetParam($_POST, 'cb_myvehicle_registrationlevel', 0);
    if ( isset($supArr) && $supArr > 0 ) {
        for ($i = 0; $i < count($supArr); $i++)
            $str.=$supArr[$i] . ',';
    }
    $str = substr($str, 0, -1);
    $vehiclemanager_configuration['cb_myvehicle']['registrationlevel'] = -2;

    $str = '';
    $supArr = mosGetParam($_POST, 'cb_edit_registrationlevel', 0);
    if ( isset($supArr) && $supArr > 0 ) {
        for ($i = 0; $i < count($supArr); $i++) {
            $str.=$supArr[$i] . ',';
        }
    }
    $str = substr($str, 0, -1);
    $vehiclemanager_configuration['cb_edit']['registrationlevel'] = -2;

    $str = '';
    $supArr = mosGetParam($_POST, 'cb_rent_registrationlevel', 0);
    if ( isset($supArr) && $supArr > 0 ) {
        for ($i = 0; $i < count($supArr); $i++) {
            $str.=$supArr[$i] . ',';
        }
    }
    $str = substr($str, 0, -1);
    $vehiclemanager_configuration['cb_rent']['registrationlevel'] = -2;

    $str = '';
    $supArr = mosGetParam($_POST, 'cb_buy_registrationlevel', 0);
    if ( isset($supArr) && $supArr > 0 ) {
        for ($i = 0; $i < count($supArr); $i++) {
            $str.=$supArr[$i] . ',';
        }
    }
    $str = substr($str, 0, -1);
    $vehiclemanager_configuration['cb_buy']['registrationlevel'] = -2;

    $str = '';
    $supArr = mosGetParam($_POST, 'cb_history_registrationlevel', 0);
    if ( isset($supArr) && $supArr > 0 ) {
        for ($i = 0; $i < count($supArr); $i++) {
            $str.=$supArr[$i] . ',';
        }
    }
    $str = substr($str, 0, -1);
    $vehiclemanager_configuration['cb_history']['registrationlevel'] = -2;

    $str = '';
    $supArr = mosGetParam($_POST, 'Reviews_vehicle_registrationlevel');
    if ( isset($supArr) && $supArr > 0 ) {
        for ($i = 0; $i < count($supArr); $i++) {
            $str.=$supArr[$i] . ',';
        }
    }
    $str = substr($str, 0, -1);
    $vehiclemanager_configuration ['Reviews_vehicle']['registrationlevel'] = -2;

    $str = '';
    $supArr = mosGetParam($_POST, 'price_registrationlevel', 0);
    if ( isset($supArr) && $supArr > 0 ) {
        for ($i = 0; $i < count($supArr); $i++) {
            $str.=$supArr[$i] . ',';
        }
    }
    $str = substr($str, 0, -1);
    $vehiclemanager_configuration['price']['registrationlevel'] = -2;

    //*********   begin add send mail for admin   *******
    $str = '';
    $supArr = mosGetParam($_POST, 'addvehicle_email_registrationlevel', 0);
    if ( isset($supArr) && $supArr > 0 ) {
        for ($i = 0; $i < count($supArr); $i++) {
            $str.=$supArr[$i] . ',';
        }
    }
    $str = substr($str, 0, -1);
    $vehiclemanager_configuration['addvehicle_email']['registrationlevel'] = -2;
    $vehiclemanager_configuration['addvehicle_email']['show'] = mosGetParam($_POST, 'addvehicle_email_show', 0);

    //label 14.06.17
    $vehiclemanager_configuration['vehicle_notification_send_reply_to']['add'] = mosGetParam($_POST, 'vehicle_notification_send_reply_to_add');

    $str = '';
    $supArr = mosGetParam($_POST, 'review_added_email_registrationlevel', 0);
    if ( isset($supArr) && $supArr > 0 ) {
        for ($i = 0; $i < count($supArr); $i++) {
            $str.=$supArr[$i] . ',';
        }
    }
    $str = substr($str, 0, -1);
    $vehiclemanager_configuration['review_added_email']['registrationlevel'] = -2;
    $vehiclemanager_configuration['review_added_email']['show'] = mosGetParam($_POST, 'review_added_email_show', 0);

    $str = '';
    $supArr = mosGetParam($_POST, 'rentrequest_email_registrationlevel', 0);
    if ( isset($supArr) && $supArr > 0 ) {
        for ($i = 0; $i < count($supArr); $i++) {
            $str.=$supArr[$i] . ',';
        }
    }
    $str = substr($str, 0, -1);
    $vehiclemanager_configuration['rentrequest_email']['registrationlevel'] = -2;
    $vehiclemanager_configuration['rentrequest_email']['show'] = mosGetParam($_POST, 'rentrequest_email_show', 0);

    $str = '';
    $supArr = mosGetParam($_POST, 'buyingrequest_email_registrationlevel', 0);
    if ( isset($supArr) && $supArr > 0 ) {
        for ($i = 0; $i < count($supArr); $i++) {
            $str.=$supArr[$i] . ',';
        }
    }
    $str = substr($str, 0, -1);
    $vehiclemanager_configuration['buyingrequest_email']['registrationlevel'] = -2;
    $vehiclemanager_configuration['buyingrequest_email']['show'] = mosGetParam($_POST, 'buyingrequest_email_show', 0);
    //*********   end add send mail for admin   *********

    //bch
    //***********begin option access to edit vehicle
    $vehiclemanager_configuration['option_edit']['show'] = mosGetParam($_POST, 'option_edit', 0);
    $str = '';
    $supArr = mosGetParam($_POST, 'option_edit_registrationlevel', 0);
    if ( isset($supArr) && $supArr > 0 ) {
        for ($i = 0; $i < count($supArr); $i++) {
            $str.=$supArr[$i] . ',';
        }
    }
    $str = substr($str, 0, -1);
    $vehiclemanager_configuration['option_edit']['registrationlevel'] = '8';

    //***********end option access to edit vehicle


    $vehiclemanager_configuration['Contacts']['show'] = mosGetParam($_POST, 'Contacts_show_vehicle', 1);

    $vehiclemanager_configuration['cb']['show'] = mosGetParam($_POST, 'cb_show', 0);
    $vehiclemanager_configuration['cb_myvehicle']['show'] = mosGetParam($_POST, 'cb_show_myvehicle', 0);
    $vehiclemanager_configuration['cb_edit']['show'] = mosGetParam($_POST, 'cb_show_edit', 0);
    $vehiclemanager_configuration['cb_rent']['show'] = mosGetParam($_POST, 'cb_show_rent', 0);
    $vehiclemanager_configuration['cb_buy']['show'] = mosGetParam($_POST, 'cb_show_buy', 0);
    $vehiclemanager_configuration['cb_history']['show'] = mosGetParam($_POST, 'cb_show_history', 0);

  //**************************** end add for Tabs  ************
    $vehiclemanager_configuration['Location_vehicle']['show'] = mosGetParam($_POST, 'Location_show_vehicle', 1);
    $vehiclemanager_configuration['Reviews_vehicle']['show'] = mosGetParam($_POST, 'Reviews_show_vehicle', 1);

  //*******  begin  add for Manager add_vehicle: button 'Add vehicle'   *******

    $str = '';
    $supArr = mosGetParam($_POST, 'add_vehicle_registrationlevel', '');
    if ( isset($supArr) && $supArr > 0 ) {
        for ($i = 0; $i < count($supArr); $i++) {
            $str.=$supArr[$i] . ',';
        }
    }
    $str = substr($str, 0, -1);
    $vehiclemanager_configuration['add_vehicle']['registrationlevel'] = '8';

    $vehiclemanager_configuration['add_vehicle']['show'] = mosGetParam($_POST, 'add_vehicle_show', 0);
  //*******   end add for Manager add_vehicle: button 'Add vehicle'   *******
  //*******  begin  add for Manager print_pdf: button 'print PDF'   *******
    $str = '';
    $supArr = mosGetParam($_POST, 'print_pdf_registrationlevel', 0);
    if ( isset($supArr) && $supArr > 0 ) {
        for ($i = 0; $i < count($supArr); $i++) {
            $str.=$supArr[$i] . ',';
        }
    }
    $str = substr($str, 0, -1);
    $vehiclemanager_configuration['print_pdf']['registrationlevel'] = 8;

    $vehiclemanager_configuration['print_pdf']['show'] = mosGetParam($_POST, 'print_pdf_show', 0);
  //*******   end add for Manager print_pdf: button 'print PDF'   *******
  //*******  begin  add for Manager print_view: button 'print View'   *******
    $str = '';
    $supArr = mosGetParam($_POST, 'print_view_registrationlevel', 0);
    if ( isset($supArr) && $supArr > 0 ) {
        for ($i = 0; $i < count($supArr); $i++) {
            $str.=$supArr[$i] . ',';
        }
    }
    $str = substr($str, 0, -1);
    $vehiclemanager_configuration['print_view']['registrationlevel'] = 8;

    $vehiclemanager_configuration['print_view']['show'] = mosGetParam($_POST, 'print_view_show', 0);
  //*******   end add for Manager print_view: button 'print View'   *******
  //*******  begin  add for Manager mail_to: button 'mail_to'   *******
    $str = '';
    $supArr = mosGetParam($_POST, 'mail_to_registrationlevel', 0);
    if ( isset($supArr) && $supArr > 0 ) {
        for ($i = 0; $i < count($supArr); $i++) {
            $str.=$supArr[$i] . ',';
        }
    }
    $str = substr($str, 0, -1);
    $vehiclemanager_configuration['mail_to']['registrationlevel'] = 8;

    $vehiclemanager_configuration['mail_to']['show'] = mosGetParam($_POST, 'mail_to_show', 0);
  //*******   end add for Manager mail_to: button 'mail_to'   *******

    $vehiclemanager_configuration['reviews']['show'] = mosGetParam($_POST, 'reviews_show', 0);
    $vehiclemanager_configuration['rentstatus']['show'] = mosGetParam($_POST, 'rentstatus_show', 0);
    $vehiclemanager_configuration['buystatus']['show'] = mosGetParam($_POST, 'buystatus_show', 0);



    $vehiclemanager_configuration['payment_rent_status']['show'] = mosGetParam($_POST, 'payment_rent_status_show', 0);
    $vehiclemanager_configuration['payment_real_or_test']['show'] = mosGetParam($_POST, 'payment_real_or_test', 0);

    $vehiclemanager_configuration['special_price']['show'] = mosGetParam($_POST, 'special_price', 0);

    $vehiclemanager_configuration['google_openmap']['show'] = mosGetParam($_POST, 'google_openmap', 0);

    $vehiclemanager_configuration['edocs']['show'] = mosGetParam($_POST, 'edocs_show', 1);
    $vehiclemanager_configuration['ebooks']['allow'] = mosGetParam($_POST, 'ebooks_allow', 0);
    $vehiclemanager_configuration['videos_tracks']['show'] = mosGetParam($_POST, 'videos_tracks_allow', 0);
    $vehiclemanager_configuration['price']['show'] = mosGetParam($_POST, 'price_show', 0);
    $vehiclemanager_configuration['price']['string'] = mosGetParam($_POST, 'price_show', 0);
    $vehiclemanager_configuration['thumb_param']['show'] = mosGetParam($_POST, 'thumb_param_show', 0);
    $vehiclemanager_configuration['foto']['high'] = mosGetParam($_POST, 'foto_high');
    $vehiclemanager_configuration['foto']['width'] = mosGetParam($_POST, 'foto_width');
    $vehiclemanager_configuration['fotomain']['high'] = mosGetParam($_POST, 'fotomain_high');
    $vehiclemanager_configuration['fotomain']['width'] = mosGetParam($_POST, 'fotomain_width');

    $vehiclemanager_configuration['rotate_img'] = mosGetParam($_POST, 'rotate_img', 0);

    //add calendar year
    $vehiclemanager_configuration['initial_year'] = mosGetParam($_POST, 'initial_year');
    $vehiclemanager_configuration['final_year'] = mosGetParam($_POST, 'final_year');
    //end add calendar year
    $vehiclemanager_configuration['fotogallery']['high'] = mosGetParam($_POST, 'fotogallery_high');
    $vehiclemanager_configuration['fotogallery']['width'] = mosGetParam($_POST, 'fotogallery_width');
    $vehiclemanager_configuration['fotogal']['high'] = mosGetParam($_POST, 'fotogal_high');
    $vehiclemanager_configuration['fotogal']['width'] = mosGetParam($_POST, 'fotogal_width');
    $vehiclemanager_configuration['fotoupload']['high'] = mosGetParam($_POST, 'fotoupload_high');
    $vehiclemanager_configuration['fotoupload']['width'] = mosGetParam($_POST, 'fotoupload_width');
    $vehiclemanager_configuration['page']['items'] = mosGetParam($_POST, 'page_items');
    $vehiclemanager_configuration['license']['show'] = mosGetParam($_POST, 'license_show', 0);
    //add for show in category picture
    $vehiclemanager_configuration['cat_pic']['show'] = mosGetParam($_POST, 'cat_pic_show');
    //add for show subcategory
    $vehiclemanager_configuration['subcategory']['show'] = mosGetParam($_POST, 'subcategory_show');
    //add for show single subcategory
    $vehiclemanager_configuration['single_subcategory_show']['show'] = mosGetParam($_POST, 'single_subcategory_show');

    //***********begin approve on add
    $vehiclemanager_configuration['approve_on_add']['show'] = mosGetParam($_POST, 'approve_on_add', 1);

     //watermark
    $vehiclemanager_configuration['watermark']['show'] = mosGetParam($_POST, 'watermark_show', 0);
    $vehiclemanager_configuration['watermark']['type'] = mosGetParam($_POST, 'watermark_type', 'text');
    $vehiclemanager_configuration['watermark']['file'] = mosGetParam($_POST, 'watermark_img');
    $vehiclemanager_configuration['watermark']['text'] = mosGetParam($_POST, 'watermark_text', '');
    $vehiclemanager_configuration['watermark']['size'] = mosGetParam($_POST, 'watermark_size', 30);
    $vehiclemanager_configuration['watermark']['color'] = mosGetParam($_POST, 'watermark_color', 'rgba(0, 0, 0, 1)');
    $vehiclemanager_configuration['watermark']['angle'] = mosGetParam($_POST, 'watermark_angle', 0);
    $vehiclemanager_configuration['watermark']['position'] = mosGetParam($_POST, 'watermark_position', 'center');
    $vehiclemanager_configuration['watermark']['opacity'] = mosGetParam($_POST, 'watermark_opacity', 100);
    $vehiclemanager_configuration['watermark']['min_image_width'] = mosGetParam($_POST, 'watermark_min_image_width', '800');
    $vehiclemanager_configuration['watermark']['min_image_high'] = mosGetParam($_POST, 'watermark_min_image_high', '600');

    //watermark
    $vehiclemanager_configuration['category']['ordering'] = mosGetParam($_POST, 'category_ordering');

    $vehiclemanager_configuration['slider']['height'] = mosGetParam($_POST, 'slider_height', 56);
    $vehiclemanager_configuration['slider']['object_fit'] = mosGetParam($_POST, 'slider_object_fit', 'cover');


    $str = '';
    $supArr = mosGetParam($_POST, 'approve_on_add_registrationlevel', 0);
    if ( isset($supArr) && $supArr > 0 ) {
        for ($i = 0; $i < count($supArr); $i++) {
            $str.=$supArr[$i] . ',';
        }
    }
    $str = substr($str, 0, -1);
    $vehiclemanager_configuration['approve_on_add']['registrationlevel'] = -2;
    //***********end approve on add
    //***********begin publish on add
    $vehiclemanager_configuration['publish_on_add']['show'] = mosGetParam($_POST, 'publish_on_add', 1);
    $str = '';
    $supArr = mosGetParam($_POST, 'publish_on_add_registrationlevel', 0);
    if ( isset($supArr) && $supArr > 0 ) {
        for ($i = 0; $i < count($supArr); $i++) {
            $str.=$supArr[$i] . ',';
        }
    }
    $str = substr($str, 0, -1);
    $vehiclemanager_configuration['publish_on_add']['registrationlevel'] = -2;
    //***********end publish on add

    $vehiclemanager_configuration['approve_review']['show'] = mosGetParam($_POST, 'approve_review');
    $str = '';
    $supArr = mosGetParam($_POST, 'approve_review_registrationlevel', 0);
    if ( isset($supArr) && $supArr > 0 ) {
        for ($i = 0; $i < count($supArr); $i++) {
            $str.=$supArr[$i] . ',';
        }
    }
    $str = substr($str, 0, -1);
    $vehiclemanager_configuration['approve_review']['registrationlevel'] = -2;
    //***********end approve on add

    //***********begin RSS
    $vehiclemanager_configuration['rss']['show'] = mosGetParam($_POST, 'rss_show');
    $str = '';
    $supArr = mosGetParam($_POST, 'rss_registrationlevel', 0);
    if ( isset($supArr) && $supArr > 0 ) {
        for ($i = 0; $i < count($supArr); $i++) {
            $str.=$supArr[$i] . ',';
        }
    }
    $str = substr($str, 0, -1);
    $vehiclemanager_configuration['rss']['registrationlevel'] = 8;
    //***********end RSS
    //***********begin Wishlist
    $vehiclemanager_configuration['wishlist']['show'] = mosGetParam($_POST, 'wishlist_show', 0);
    $str = '';
    $supArr = mosGetParam($_POST, 'wishlist_registrationlevel', 0);
    if ( isset($supArr) && $supArr > 0 ) {
        for ($i = 0; $i < count($supArr); $i++) {
            $str.=$supArr[$i] . ',';
        }
    }
    $str = substr($str, 0, -1);
    $vehiclemanager_configuration['wishlist']['registrationlevel'] = -2;
    //***********end Wishlist
    //***********begin review captcha
    $vehiclemanager_configuration['review_captcha']['show'] = mosGetParam($_POST, 'review_captcha_show');
    $str = '';
    $supArr = mosGetParam($_POST, 'review_captcha_registrationlevel', 0);
    if ( isset($supArr) && $supArr > 0 ) {
        for ($i = 0; $i < count($supArr); $i++) {
            $str.=$supArr[$i] . ',';
        }
    }
    $str = substr($str, 0, -1);
    $vehiclemanager_configuration['review_captcha']['registrationlevel'] = -2;
    //***********end review captcha
    //***********begin contact captcha
    $vehiclemanager_configuration['contact_captcha']['show'] = mosGetParam($_POST, 'contact_captcha_show');
    $str = '';
    $supArr = mosGetParam($_POST, 'contact_captcha_registrationlevel', 0);
    if ( isset($supArr) && $supArr > 0 ) {
        for ($i = 0; $i < count($supArr); $i++) {
            $str.=$supArr[$i] . ',';
        }
    }
    $str = substr($str, 0, -1);
    $vehiclemanager_configuration['contact_captcha']['registrationlevel'] = -2;
    //***********end review captcha
    //***********begin booking captcha
    $vehiclemanager_configuration['booking_captcha']['show'] = mosGetParam($_POST, 'booking_captcha_show');
    $str = '';
    $supArr = mosGetParam($_POST, 'booking_captcha_registrationlevel', 0);
    if ( isset($supArr) && $supArr > 0 ) {
        for ($i = 0; $i < count($supArr); $i++) {
            $str.=$supArr[$i] . ',';
        }
    }
    $str = substr($str, 0, -1);
    $vehiclemanager_configuration['booking_captcha']['registrationlevel'] = -2;
    //***********end booking captcha

    //***********begin all vehicle captcha
    $vehiclemanager_configuration['add_vehicle_captcha']['show'] = mosGetParam($_POST, 'add_vehicle_captcha_show', 1);
    $str = '';
    $supArr = mosGetParam($_POST, 'add_vehicle_captcha_registrationlevel', 0);
    if ( isset($supArr) && $supArr > 0 ) {
        for ($i = 0; $i < count($supArr); $i++) {
            $str.=$supArr[$i] . ',';
        }
    }
    $str = substr($str, 0, -1);
    $vehiclemanager_configuration['add_vehicle_captcha']['registrationlevel'] = -2;
    //***********end all vehicle captcha

    //***********begin google captcha
    $vehiclemanager_configuration['google_captcha']['show'] = mosGetParam($_POST, 'google_captcha_show', 0);
    //***********end google captcha
    //***********begin vehicle slider
    $vehiclemanager_configuration['show_vehicle_slider'] = mosGetParam($_POST, 'show_vehicle_slider', 0);
    //***********end begin vehicle slider
    //***********begin show map for search result layout
    $vehiclemanager_configuration['show_map']['show'] = mosGetParam($_POST, 'show_map_show');
    $str = '';
    $supArr = mosGetParam($_POST, 'show_map_registrationlevel', 0);
    if ( isset($supArr) && $supArr > 0 ) {
        for ($i = 0; $i < count($supArr); $i++) {
            $str.=$supArr[$i] . ',';
        }
    }
    $str = substr($str, 0, -1);
    $vehiclemanager_configuration['show_map']['registrationlevel'] = $str;
    //***********end show map for search result layout    //***********begin show order by form for search-result layout
    $vehiclemanager_configuration['show_order_by']['show'] = mosGetParam($_POST, 'show_order_by_show');
    $str = '';
    $supArr = mosGetParam($_POST, 'show_order_by_registrationlevel', 0);
    if ( isset($supArr) && $supArr > 0 ) {
        for ($i = 0; $i < count($supArr); $i++) {
            $str.=$supArr[$i] . ',';
        }
    }
    $str = substr($str, 0, -1);
    $vehiclemanager_configuration['show_order_by']['registrationlevel'] = $str;
    //***********end show order by form for search-result layout

    //view type
    $vehiclemanager_configuration['all_categories'] = mosGetParam($_POST, 'all_categories');
    $vehiclemanager_configuration['view_type'] = mosGetParam($_POST, 'view_type2');
    $vehiclemanager_configuration['view_vehicle'] = mosGetParam($_POST, 'view_vehicle');
//    $vehiclemanager_configuration['show_search_vehicle'] = mosGetParam($_POST, 'show_search_vehicle');
    $vehiclemanager_configuration['show_search_vehicle'] = 'advanced';
    $vehiclemanager_configuration['all_vehicle_layout'] = mosGetParam($_POST, 'all_vehicle_layout');
    //owner show
    $vehiclemanager_configuration['owner']['show'] = mosGetParam($_POST, 'owner_show', 1);

    //***********begin Owners list
    $vehiclemanager_configuration['ownerslist']['show'] = mosGetParam($_POST, 'ownerslist_show', 0);
    $str = '';
    $supArr = mosGetParam($_POST, 'ownerslist_registrationlevel', 0);
    if ( isset($supArr) && $supArr > 0 ) {
        for ($i = 0; $i < count($supArr); $i++) {
            $str.=$supArr[$i] . ',';
        }
    }
    $str = substr($str, 0, -1);
    $vehiclemanager_configuration['ownerslist']['registrationlevel'] = -2;
    //***********end Owners list
    //calendar show
    $vehiclemanager_configuration['calendar']['show'] = mosGetParam($_POST, 'calendar_show', 1);

    //***********begin Calendar list
    $vehiclemanager_configuration['calendarlist']['show'] = mosGetParam($_POST, 'calendarlist_show', '');
    $str = '';
    $supArr = mosGetParam($_POST, 'calendarlist_registrationlevel', 0);
    if ( isset($supArr) && $supArr > 0 ) {
        for ($i = 0; $i < count($supArr); $i++) {
            $str.=$supArr[$i] . ',';
        }
    }
    $str = substr($str, 0, -1);
    $vehiclemanager_configuration['calendarlist']['registrationlevel'] = -2;
    //***********end Calendar list
    $vehiclemanager_configuration['contact']['show'] = mosGetParam($_POST, 'contact_show');
    //***********begin Contact Agent list
    $vehiclemanager_configuration['contactlist']['show'] = mosGetParam($_POST, 'contactlist_show');
    $str = '';
    $supArr = mosGetParam($_POST, 'contactlist_registrationlevel', 0);
    if ( isset($supArr) && $supArr > 0 ) {
    if ( isset($supArr) && $supArr > 0 ) {
        for ($i = 0; $i < count($supArr); $i++) {
            $str.=$supArr[$i] . ',';
        }
    }
    }
    $str = substr($str, 0, -1);
    $vehiclemanager_configuration['contactlist']['registrationlevel'] = $str;
    //***********end Contact Agent list

    //show location map
    $vehiclemanager_configuration['location_map'] = mosGetParam($_POST, 'location_map', 0);

    $vehiclemanager_configuration['manager_feature_image'] = mosGetParam($_POST, 'manager_feature_image', 0);

    $vehiclemanager_configuration['manager_feature_category'] = mosGetParam($_POST, 'manager_feature_category', 0);

    $vehiclemanager_configuration['sale_separator'] = mosGetParam($_POST, 'sale_separator', 0);

    //***********begin year of issue
    $vehiclemanager_configuration['year_search']['show'] = mosGetParam($_POST, 'year_search_show', 0);
    //***********begin mileage
    $vehiclemanager_configuration['mileage']['show'] = mosGetParam($_POST, 'mileage_show', 0);

    // 18_05_17
    //***********Show button search by form for search-result layout
    $vehiclemanager_configuration['search_button']['show'] = mosGetParam($_POST, 'search_button_show');
    $str = '';
    $supArr = mosGetParam($_POST, 'search_button_registrationlevel', 0);
    if ( isset($supArr) && $supArr > 0 ) {
        for ($i = 0; $i < count($supArr); $i++) {
            $str.=$supArr[$i] . ',';
        }
    }
    $str = substr($str, 0, -1);
    $vehiclemanager_configuration['search_button']['registrationlevel'] = $str;
    //***********end show button search by form for search-result layout
    //***********Add send reply to 'Email and notification setting'
    $vehiclemanager_configuration['rent_request_send_reply_to']['add'] = mosGetParam($_POST, 'rent_request_send_reply_to_add', 0);
    //***********End Add send reply to 'Email and notification setting'
    //***********Add send reply to 'Email and notification setting'
    $vehiclemanager_configuration['notification_review_send_reply_to']['add'] = mosGetParam($_POST, 'notification_review_send_reply_to_add');
    //***********End Add send reply to 'Email and notification setting'
    //***********Add send reply to vehicle notification'
    $vehiclemanager_configuration['buying_request_notification_send_reply_to']['add'] = mosGetParam($_POST, 'buying_request_notification_send_reply_to_add', 0);
    //***********End Add send reply to vehicle notification'


    //05.07.17
    //******  begin option default search layout  *****
    $vehiclemanager_configuration['default_search_layout'] = mosGetParam($_POST, 'default_search_layout', 0);
    //******  end begin option default search layout  *****
    //26.07.17
    //******  begin option order default  *****
    $vehiclemanager_configuration['order_by_default'] = mosGetParam($_POST, 'order_by_default', 0);
    //******  end begin option order default  *****

    $vehiclemanager_configuration['extra1'] = mosGetParam($_POST, 'extra1', '');
    $vehiclemanager_configuration['extra2'] = mosGetParam($_POST, 'extra2', '');
    $vehiclemanager_configuration['extra3'] = mosGetParam($_POST, 'extra3', '');
    $vehiclemanager_configuration['extra4'] = mosGetParam($_POST, 'extra4', '');
    $vehiclemanager_configuration['extra5'] = mosGetParam($_POST, 'extra5', '');
    $vehiclemanager_configuration['extra6'] = mosGetParam($_POST, 'extra6', '');
    $vehiclemanager_configuration['extra7'] = mosGetParam($_POST, 'extra7', '');
    $vehiclemanager_configuration['extra8'] = mosGetParam($_POST, 'extra8', '');
    $vehiclemanager_configuration['extra9'] = mosGetParam($_POST, 'extra9', '');
    $vehiclemanager_configuration['extra10'] = mosGetParam($_POST, 'extra10', '');
    //******** end add Custom Dropdown Field 1 options **********
    $vehiclemanager_configuration['extra1_advanced'] = mosGetParam($_POST, 'extra1_advanced', '');
    //******** end add Custom Dropdown Field 1 options **********
    //******** end add Custom Dropdown Field 2 options **********
    $vehiclemanager_configuration['extra2_advanced'] = mosGetParam($_POST, 'extra2_advanced', '');
    //******** end add Custom Dropdown Field 2 options **********
    //******** end add Custom Dropdown Field 3 options **********
    $vehiclemanager_configuration['extra3_advanced'] = mosGetParam($_POST, 'extra3_advanced', '');
    //******** end add Custom Dropdown Field 3 options **********
    //******** end add Custom Dropdown Field 4 options **********
    $vehiclemanager_configuration['extra4_advanced'] = mosGetParam($_POST, 'extra4_advanced', '');
    //******** end add Custom Dropdown Field 4 options **********
    //******** end add Custom Dropdown Field 5 options **********
    $vehiclemanager_configuration['extra5_advanced'] = mosGetParam($_POST, 'extra5_advanced', '');
    //******** end add Custom Dropdown Field 5 options **********
    //******** add Custom Dropdown Field 6 options **********
    $vehiclemanager_configuration['extra6_advanced'] = mosGetParam($_POST, 'extra6_advanced', '');
    //******** end add Custom Dropdown Field 6 options **********
    //******** add Custom Dropdown Field 7 options **********
    $vehiclemanager_configuration['extra7_advanced'] = mosGetParam($_POST, 'extra7_advanced', '');
    //******** end add Custom Dropdown Field 7 options **********
    //******** add Custom Dropdown Field 8 options **********
    $vehiclemanager_configuration['extra8_advanced'] = mosGetParam($_POST, 'extra8_advanced', '');
    //******** end add Custom Dropdown Field 6 options **********
    //******** add Custom Dropdown Field 8 options **********
    $vehiclemanager_configuration['extra9_advanced'] = mosGetParam($_POST, 'extra9_advanced', '');
    //******** end add Custom Dropdown Field 9 options **********
    //******** add Custom Dropdown Field 10 options **********
    $vehiclemanager_configuration['extra10_advanced'] = mosGetParam($_POST, 'extra10_advanced', '');
    //******** end add Custom Dropdown Field 10 options **********
    //******** add sale fraction **********
    $vehiclemanager_configuration['sale_fraction'] = mosGetParam($_POST, 'sale_fraction', 0);
    //******** end add sale fraction **********

    //******  add option features on admin Settings "Search Settings" tab  *****
    $query = "SELECT name, id FROM #__vehiclemanager_feature";
    $database->setQuery($query);
    $features = $database->loadObjectList();

    foreach ($features as $feature) {
        $vehiclemanager_configuration['search_form_features']["$feature->id"] = mosGetParam($_POST, "search_form_features_$feature->id", 3);
    }
    //******  end option features on admin Settings "Search Settings" tab  *****


    mosVehicleManagerOthers::setParams();
  }


  function configure_save_backend($option){
    global $my, $vehiclemanager_configuration;

    $gtree = get_group_children_tree_vm();
    foreach($gtree as $g){
        $vehiclemanager_configuration['user_manager_vm'][$g->value]['count_car'] = intval(mosGetParam($_POST, 'count_car' . $g->value, "999"));
        $vehiclemanager_configuration['user_manager_vm'][$g->value]['count_foto'] = intval(mosGetParam($_POST, 'count_foto' . $g->value, "5"));
    }
    $vehiclemanager_configuration['addvehicle_email']['address'] = mosGetParam($_POST, 'addvehicle_email_address', "");
    $vehiclemanager_configuration['review_email']['address'] = mosGetParam($_POST, 'review_email_address', "");
    //10/05/17

    $vehiclemanager_configuration['mandatory_year_issue_field_require'] = mosGetParam($_POST, 'mandatory_year_issue_field_require', "1");
    $vehiclemanager_configuration['mandatory_mileage_field_require'] = mosGetParam($_POST, 'mandatory_mileage_field_require', "1");
    $vehiclemanager_configuration['mandatory_price_field_require'] = mosGetParam($_POST, 'mandatory_price_field_require', "1");
    $vehiclemanager_configuration['rentrequest_email']['address'] =  mosGetParam($_POST, 'rentrequest_email_address', "");
    $vehiclemanager_configuration['buyingrequest_email']['address'] = mosGetParam($_POST, 'buyingrequest_email_address', "");
    $vehiclemanager_configuration['vehicleid']['auto-increment']['boolean'] = mosGetParam($_POST, 'vehicleid_auto_increment_boolean', 0);
    $vehiclemanager_configuration['edocs']['allow'] = mosGetParam($_POST, 'edocs_allow', 0);
    $vehiclemanager_configuration['edocs']['location'] = mosGetParam($_POST, 'edocs_location', "/components/com_vehiclemanager/edocs/");
    $vehiclemanager_configuration['rent_answer'] = mosGetParam($_POST, 'rent_answer', 0);
    $vehiclemanager_configuration['buy_answer'] = mosGetParam($_POST, 'buy_answer', 0);
    $vehiclemanager_configuration['price_format'] = mosGetParam($_POST, 'patern', '.');
    $vehiclemanager_configuration['date_format'] = mosGetParam($_POST, 'date_format');
    $vehiclemanager_configuration['price_unit_show'] = mosGetParam($_POST ,'price_unit_show', 0);
    $vehiclemanager_configuration['rent_before_end_notify'] = mosGetParam($_POST, 'rent_before_end_notify', 0);
    $vehiclemanager_configuration['rent_before_end_notify_days'] = mosGetParam($_POST, 'rent_before_end_notify_days', 0);
    $vehiclemanager_configuration['rent_before_end_notify_email'] = mosGetParam($_POST, 'rent_before_end_notify_email', "");
    //06.09.17
    $vehiclemanager_configuration['veh_data_columns_lg'] = mosGetParam($_POST, 'veh_data_columns_lg', 1);
    $vehiclemanager_configuration['veh_data_columns_md'] = mosGetParam($_POST, 'veh_data_columns_md', 1);
    $vehiclemanager_configuration['veh_data_columns_sm'] = mosGetParam($_POST, 'veh_data_columns_sm', 1);
    $vehiclemanager_configuration['veh_data_columns_xs'] = mosGetParam($_POST, 'veh_data_columns_xs', 1);

    if(!preg_match('/(^https?:\/\/)/i' , $_POST['patern_rent']) && $vehiclemanager_configuration['input_link_rent'] == 'redirect to input link'){
        $_POST['patern_rent'] = "http://".$_POST['patern_rent'];
    }


    if(!preg_match('/(^https?:\/\/)/i' , $_POST['patern_sale']) && $vehiclemanager_configuration['input_link_rent'] == 'redirect to input link'){
         $_POST['patern_sale'] = "http://". $_POST['patern_sale'];
    }

    $vehiclemanager_configuration['input_link_rent'] = mosGetParam($_POST,'patern_rent', 'default');
    $vehiclemanager_configuration['input_link_sale'] = mosGetParam($_POST,'patern_sale', 'dafault');
    //update
    $vehiclemanager_configuration['calendar']['placeholder'] = mosGetParam($_POST, 'calendar_placeholder', "");
    $vehiclemanager_configuration['contact']['placeholder'] = mosGetParam($_POST, 'contact_placeholder', "");
    //paypal

    $vehiclemanager_configuration['pay_pal_buy']['business'] = mosGetParam($_POST, 'pay_pal_buy_business', "");
    $vehiclemanager_configuration['pay_pal_buy']['return'] = mosGetParam($_POST, 'pay_pal_buy_return', "");
    $vehiclemanager_configuration['pay_pal_buy']['image_url'] = mosGetParam($_POST, 'pay_pal_buy_image_url', "");
    $vehiclemanager_configuration['pay_pal_buy']['cancel_return'] = mosGetParam($_POST, 'pay_pal_buy_cancel_return', "");

    $vehiclemanager_configuration['pay_pal_rent']['business'] = mosGetParam($_POST, 'pay_pal_rent_business', "");
    $vehiclemanager_configuration['pay_pal_rent']['return'] = mosGetParam($_POST, 'pay_pal_rent_return', "");
    $vehiclemanager_configuration['pay_pal_rent']['image_url'] = mosGetParam($_POST, 'pay_pal_rent_image_url', "");
    $vehiclemanager_configuration['pay_pal_rent']['cancel_return'] = mosGetParam($_POST, 'pay_pal_rent_cancel_return', "");

    $vehiclemanager_configuration['show_country_region_city_as_text_field'] = mosGetParam($_POST, 'show_city_as_text_field');

    $vehiclemanager_configuration['keywords_search_show_select'] = mosGetParam($_POST, 'keywords_search_show_select','1');

    //******  begin option radiuse range field on search page  *****
    $vehiclemanager_configuration['search_form_radiuse_range_field_show'] = mosGetParam($_POST, 'search_form_radiuse_range_field_show', 0);
    //******  end begin option radiuse range field on search page  *****
    //******  begin option radiuse range on search page  *****
    $vehiclemanager_configuration['search_form_radiuse_range'] = mosGetParam($_POST, 'search_form_radiuse_range', 0);
    //******  end begin option radiuse range on search page  *****

    $vehiclemanager_configuration['available_for_rent_show_select'] = mosGetParam($_POST, 'available_for_rent_show_select','1');
    $vehiclemanager_configuration['year_of_issue_show_select'] = mosGetParam($_POST, 'year_of_issue_show_select','1');
    $vehiclemanager_configuration['price_vehicle_show_select'] = mosGetParam($_POST, 'price_vehicle_show_select','1');
    $vehiclemanager_configuration['condition_status_show_select'] = mosGetParam($_POST, 'condition_status_show_select','1');
    $vehiclemanager_configuration['listing_status_show_select'] = mosGetParam($_POST, 'listing_status_show_select','1');
    $vehiclemanager_configuration['transmission_type_show_select'] = mosGetParam($_POST, 'transmission_type_show_select','1');
    $vehiclemanager_configuration['maker_show_select'] = mosGetParam($_POST, 'maker_show_select','1');
    $vehiclemanager_configuration['drive_type_show_select'] = mosGetParam($_POST, 'drive_type_show_select','1');
    $vehiclemanager_configuration['model_show_select'] = mosGetParam($_POST, 'model_show_select','1');
    $vehiclemanager_configuration['number_cylinders_show_select'] = mosGetParam($_POST, 'number_cylinders_show_select','1');
    $vehiclemanager_configuration['vehicle_type_show_select'] = mosGetParam($_POST, 'vehicle_type_show_select','1');
    $vehiclemanager_configuration['number_speeds_show_select'] = mosGetParam($_POST, 'number_speeds_show_select','1');
    $vehiclemanager_configuration['listing_type_show_select'] = mosGetParam($_POST, 'listing_type_show_select','1');
    $vehiclemanager_configuration['fuel_type_show_select'] = mosGetParam($_POST, 'fuel_type_show_select','1');
    $vehiclemanager_configuration['price_type_show_select'] = mosGetParam($_POST, 'price_type_show_select','1');
    $vehiclemanager_configuration['number_doors_show_select'] = mosGetParam($_POST, 'number_doors_show_select','1');
    $vehiclemanager_configuration['category_show_select'] = mosGetParam($_POST, 'category_show_select','1');
    $vehiclemanager_configuration['vehicleid_show_select'] = mosGetParam($_POST, 'vehicleid_show_select','1');
    $vehiclemanager_configuration['comment_show_select'] = mosGetParam($_POST, 'comment_show_select','1');
    $vehiclemanager_configuration['title_show_select'] = mosGetParam($_POST, 'title_show_select','1');
    $vehiclemanager_configuration['address_show_select'] = mosGetParam($_POST, 'address_show_select','1');
    $vehiclemanager_configuration['country_show_select'] = mosGetParam($_POST, 'country_show_select','1');
    $vehiclemanager_configuration['region_show_select'] = mosGetParam($_POST, 'region_show_select','1');
    $vehiclemanager_configuration['city_show_select'] = mosGetParam($_POST, 'city_show_select','1');
    $vehiclemanager_configuration['district_show_select'] = mosGetParam($_POST, 'district_show_select','1');
    $vehiclemanager_configuration['zipcode_show_select'] = mosGetParam($_POST, 'zipcode_show_select','1');
    $vehiclemanager_configuration['owner_show_select'] = mosGetParam($_POST, 'owner_show_select','1');
    $vehiclemanager_configuration['mileage_show_select'] = mosGetParam($_POST, 'mileage_show_select','1');
    $vehiclemanager_configuration['contacts_show_select'] = mosGetParam($_POST, 'contacts_show_select','1');
    $vehiclemanager_configuration['engine_type_show_select'] = mosGetParam($_POST, 'engine_type_show_select','1');
    $vehiclemanager_configuration['city_mpg_show_select'] = mosGetParam($_POST, 'city_mpg_show_select','1');
    $vehiclemanager_configuration['highway_mpg_show_select'] = mosGetParam($_POST, 'highway_mpg_show_select','1');
    $vehiclemanager_configuration['wheelbase_show_select'] = mosGetParam($_POST, 'wheelbase_show_select','1');
    $vehiclemanager_configuration['wheeltype_show_select'] = mosGetParam($_POST, 'wheeltype_show_select','1');
    $vehiclemanager_configuration['rearaxe_type_show_select'] = mosGetParam($_POST, 'rearaxe_type_show_select','1');
    $vehiclemanager_configuration['brakes_type_show_select'] = mosGetParam($_POST, 'brakes_type_show_select','1');
    $vehiclemanager_configuration['exterior_colors_show_select'] = mosGetParam($_POST, 'exterior_colors_show_select','1');
    $vehiclemanager_configuration['exterior_extras_show_select'] = mosGetParam($_POST, 'exterior_extras_show_select','1');
    $vehiclemanager_configuration['interior_colors_show_select'] = mosGetParam($_POST, 'interior_colors_show_select','1');
    $vehiclemanager_configuration['dashboard_options_show_select'] = mosGetParam($_POST, 'dashboard_options_show_select','1');
    $vehiclemanager_configuration['interior_extras_show_select'] = mosGetParam($_POST, 'interior_extras_show_select','1');
    $vehiclemanager_configuration['safety_options_show_select'] = mosGetParam($_POST, 'safety_options_show_select','1');
    $vehiclemanager_configuration['warranty_options_show_select'] = mosGetParam($_POST, 'warranty_options_show_select','1');
    $vehiclemanager_configuration['exactly_show_select'] = mosGetParam($_POST, 'exactly_show_select','1');
    //01.06.17
    $vehiclemanager_configuration['search_form_nothing_found_page_show'] = mosGetParam($_POST, 'search_form_nothing_found_page_show','1');
    $vehiclemanager_configuration['search_form_on_search_page_result_show'] = mosGetParam($_POST, 'search_form_on_search_page_result_show','1');

    $vehiclemanager_configuration['show_country_region_city_as_text_field'] = mosGetParam($_POST, 'show_country_region_city_as_text_field','0');


//    $supArr = mosGetParam($_POST, 'payment_buy_registrationlevel', 0);
//    for ($i = 0; $i < count($supArr); $i++)
//        $str.=$supArr[$i] . ',';
//    $str = substr($str, 0, -1);
//    $vehiclemanager_configuration['payment_buy']['registrationlevel'] = $str;
    $str = '';
    $supArr = mosGetParam($_POST, 'plugin_name_select', "");
    if ( isset($supArr) && $supArr > 0 ) {
    if ( isset($supArr) && $supArr > 0 ) {
        for ($i = 0; $i < count($supArr); $i++) {
            $str.=$supArr[$i] . ',';
        }
    }
    }
    $str = substr($str, 0, -1);
    $vehiclemanager_configuration['plugin_name_select'] = $str;


    $vehiclemanager_configuration['currency'] = mosGetParam($_POST, 'currency', "USD=1;EUR=8.125;RUB=240;");

    $vehiclemanager_configuration['payment_buy_status']['show'] = mosGetParam($_POST, 'payment_buy_status_show', 0);
                //---------------------start check currency-----------------------------//

    if($vehiclemanager_configuration['payment_buy_status']['show'] == 1 && $vehiclemanager_configuration['plugin_name_select'] == 'paypal'){
      //this array from paypal plugin,if you change something here, don't forget to change it in the paypal.php
      $defcurrency=array('AUD','CAD','CZK','DKK','EUR','HKD','HUF','JPY','NOK','NZD',
        'PLN','SGD','SEK','CHF','USD','RUB','ILS','MXN','PHP','GBP','THB');
      $currenc = explode(';', $vehiclemanager_configuration['currency']);
      foreach ($currenc as $row) {
          if ($row != '') {
              $row = explode("=", $row);
              if (!in_array($row[0],$defcurrency)) {
                  echo "<script> alert('Please insert only the correct currency value for paypal.'); window.history.go(-1);</script>\n";
                  exit;
              }
          }
      }
    }else{
      $currenc = explode(';', $vehiclemanager_configuration['currency']);
      foreach ($currenc as $row) {
          if ($row != '') {
              $row = explode("=", $row);
          }
      }
    }
    //---------------------------end check-------------------------//

    $vehiclemanager_configuration['allowed_exts'] = mosGetParam($_POST, 'allowed_exts', "");
    $vehiclemanager_configuration['allowed_exts_img'] = mosGetParam($_POST, 'allowed_exts_img', "");
    $vehiclemanager_configuration['allowed_exts_video'] = mosGetParam($_POST, 'allowed_exts_video', "");
    $vehiclemanager_configuration['allowed_exts_track'] = mosGetParam($_POST, 'allowed_exts_track', "");
    $vehiclemanager_configuration['api_key'] = mosGetParam($_POST, 'api_key', "");
    mosVehicleManagerOthers::setParams();
  }

  function configure($option)
  {
    //configure_frontend
    global $my, $vehiclemanager_configuration, $database;
    global $mosConfig_absolute_path; 
    $yesno[] = mosHTML::makeOption('1', _VEHICLE_MANAGER_YES);
    $yesno[] = mosHTML::makeOption('0', _VEHICLE_MANAGER_NO);
    $google_openmap[] = mosHTML::makeOption('1', _VEHICLE_MANAGER_GOOGLE_OPENMAP_GOOGLE);
    $google_openmap[] = mosHTML::makeOption('0', _VEHICLE_MANAGER_GOOGLE_OPENMAP_OPENMAP);

    $lists = array();

    $gtree = get_group_children_tree_vm();

    //Options for select list on search page
    $two_select_option = array();
    $two_select_option[] = JHtml::_('select.option', "1", "Show & search");
    //$two_select_option[] = JHtml::_('select.option', "2", "Search");
    $two_select_option[] = JHtml::_('select.option', "3", "None");

    //Options for checkbox on search page
    $three_select_option = array();
    $three_select_option[] = JHtml::_('select.option', "1", "Show & search");
    $three_select_option[] = JHtml::_('select.option', "2", "Search");
    $three_select_option[] = JHtml::_('select.option', "3", "None");

    foreach($gtree as $g) {
        $t['value'] = $g->value;
        $t['role'] = str_replace('&nbsp;', '', $g->text);
        $t['count_car'] = '<input type="text" name="count_car' . $g->value . '" value="' . $vehiclemanager_configuration['user_manager_vm'][$g->value]['count_car'] . '" class="inputbox" size="3" maxlength="5" />';
        $t['count_foto'] = '<input type="text" name="count_foto' . $g->value . '" value="' . $vehiclemanager_configuration['user_manager_vm'][$g->value]['count_foto'] . '" class="inputbox" size="3" maxlength="3" />';
        $lists['user_manager_vm'][] = $t;
    }

    // _______________- community builder section -_______________
    $f = array();
    $s = explode(',', $vehiclemanager_configuration['cb_myvehicle']['registrationlevel']);
    for ($i = 0; $i < count($s); $i++)
        $f[] = mosHTML::makeOption($s[$i]);

    $lists['cb_myvehicle']['show'] = mosHTML::radioList($yesno, 'cb_show_myvehicle', 'class="inputbox"', $vehiclemanager_configuration['cb_myvehicle']['show'], 'value', 'text');

    $lists['cb_myvehicle']['registrationlevel'] = mosHTML::selectList($gtree, 'cb_myvehicle_registrationlevel[]', 'size="4" multiple="multiple"', 'value', 'text', $f);

    $f = array();
    $s = explode(',', $vehiclemanager_configuration['cb_edit']['registrationlevel']);
    for ($i = 0; $i < count($s); $i++)
        $f[] = mosHTML::makeOption($s[$i]);

    $lists['cb_edit']['show'] = mosHTML::radioList($yesno, 'cb_show_edit', 'class="inputbox"', $vehiclemanager_configuration['cb_edit']['show'], 'value', 'text');

    $lists['cb_edit']['registrationlevel'] = mosHTML::selectList($gtree, 'cb_edit_registrationlevel[]', 'size="4" multiple="multiple"', 'value', 'text', $f);

    $f = array();
    $s = explode(',', $vehiclemanager_configuration['cb_rent']['registrationlevel']);
    for ($i = 0; $i < count($s); $i++)
        $f[] = mosHTML::makeOption($s[$i]);

    $lists['cb_rent']['show'] = mosHTML::radioList($yesno, 'cb_show_rent', 'class="inputbox"', $vehiclemanager_configuration['cb_rent']['show'], 'value', 'text');

    $lists['cb_rent']['registrationlevel'] = mosHTML::selectList($gtree, 'cb_rent_registrationlevel[]', 'size="4" multiple="multiple"', 'value', 'text', $f);

    $f = array();
    $s = explode(',', $vehiclemanager_configuration['cb_buy']['registrationlevel']);
    for ($i = 0; $i < count($s); $i++)
        $f[] = mosHTML::makeOption($s[$i]);

    $lists['cb_buy']['show'] = mosHTML::radioList($yesno, 'cb_show_buy', 'class="inputbox"', $vehiclemanager_configuration['cb_buy']['show'], 'value', 'text');

    $lists['cb_buy']['registrationlevel'] = mosHTML::selectList($gtree, 'cb_buy_registrationlevel[]', 'size="4" multiple="multiple"', 'value', 'text', $f);

    $f = array();
    $s = explode(',', $vehiclemanager_configuration['cb_history']['registrationlevel']);
    for ($i = 0; $i < count($s); $i++)
        $f[] = mosHTML::makeOption($s[$i]);

    $lists['cb_history']['show'] = mosHTML::radioList($yesno, 'cb_show_history', 'class="inputbox"', $vehiclemanager_configuration['cb_history']['show'], 'value', 'text');

    $lists['cb_history']['registrationlevel'] = mosHTML::selectList($gtree, 'cb_history_registrationlevel[]', 'size="4" multiple="multiple"', 'value', 'text', $f);
    // _______________- end community builder section -_______________

    $f = array();
    $s = explode(',', $vehiclemanager_configuration['reviews']['registrationlevel']);
    for ($i = 0; $i < count($s); $i++)
        $f[] = mosHTML::makeOption($s[$i]);

    $lists['reviews']['show'] = mosHTML::radioList($yesno, 'reviews_show', 'class="inputbox"', $vehiclemanager_configuration['reviews']['show'], 'value', 'text');
    $lists['reviews']['registrationlevel'] = mosHTML::selectList($gtree, 'reviews_registrationlevel[]', 'size="4" multiple="multiple"', 'value', 'text', $f);

    $lists['owner']['show'] = mosHTML::radioList($yesno, 'owner_show', 'class="inputbox"', $vehiclemanager_configuration['owner']['show'], 'value', 'text');

    //********** Calendar list ************
    $f = array();
    $s = explode(',', $vehiclemanager_configuration['calendarlist']['registrationlevel']);
    for ($i = 0; $i < count($s); $i++)
        $f[] = mosHTML::makeOption($s[$i]);
    $lists['calendarlist']['show'] = mosHTML::radioList($yesno, 'calendarlist_show', 'class="inputbox"', $vehiclemanager_configuration['calendarlist']['show'], 'value', 'text');
    $lists['calendarlist']['registrationlevel'] = mosHTML::selectList($gtree, 'calendarlist_registrationlevel[]', 'size="4" multiple="multiple"', 'value', 'text', $f);
    //********** END Calendar list ************

    //********** Contact Agent list ************
    $f = array();
    $s = explode(',', $vehiclemanager_configuration['contactlist']['registrationlevel']);
    for ($i = 0; $i < count($s); $i++)
        $f[] = mosHTML::makeOption($s[$i]);
    $lists['contactlist']['show'] = mosHTML::radioList($yesno, 'contactlist_show', 'class="inputbox"', $vehiclemanager_configuration['contactlist']['show'], 'value', 'text');
    $lists['contactlist']['registrationlevel'] = mosHTML::selectList($gtree, 'contactlist_registrationlevel[]', 'size="4" multiple="multiple"', 'value', 'text', $f);
    //********** END Contact Agent list ************

    $f = array();
    $s = explode(',', $vehiclemanager_configuration['rentrequest']['registrationlevel']);
    for ($i = 0; $i < count($s); $i++)
        $f[] = mosHTML::makeOption($s[$i]);

    $lists['rentstatus']['show'] = mosHTML::radioList($yesno, 'rentstatus_show', 'class="inputbox"', $vehiclemanager_configuration['rentstatus']['show'], 'value', 'text');

    $lists['rentrequest']['registrationlevel'] = mosHTML::selectList($gtree, 'rentrequest_registrationlevel[]', 'size="4" multiple="multiple"', 'value', 'text', $f);

    $f = array();
    $s = explode(',', $vehiclemanager_configuration['buyrequest']['registrationlevel']);
    for ($i = 0; $i < count($s); $i++)
        $f[] = mosHTML::makeOption($s[$i]);

    $lists['buystatus']['show'] = mosHTML::radioList($yesno, 'buystatus_show', 'class="inputbox"', $vehiclemanager_configuration['buystatus']['show'], 'value', 'text');

    $lists['buyrequest']['registrationlevel'] = mosHTML::selectList($gtree, 'buyrequest_registrationlevel[]', 'size="4" multiple="multiple"', 'value', 'text', $f);

    $f = array();

    $s = explode(',', $vehiclemanager_configuration['payment_buy']['registrationlevel']);
    for ($i = 0; $i < count($s); $i++)
        $f[] = mosHTML::makeOption($s[$i]);

    $lists['payment_buy_status']['show'] = mosHTML::radioList($yesno, 'payment_buy_status_show', 'class="inputbox"', $vehiclemanager_configuration['payment_buy_status']['show'], 'value', 'text');
    $lists['payment_buy']['registrationlevel'] = mosHTML::selectList($gtree, 'payment_buy_registrationlevel[]', 'size="4" multiple="multiple"', 'value', 'text', $f);

    $f = array();
    $s = explode(',', $vehiclemanager_configuration['payment_rent']['registrationlevel']);
    for ($i = 0; $i < count($s); $i++)
        $f[] = mosHTML::makeOption($s[$i]);

    $lists['payment_rent_status']['show'] = mosHTML::radioList($yesno, 'payment_rent_status_show', 'class="inputbox"', $vehiclemanager_configuration['payment_rent_status']['show'], 'value', 'text');
    $lists['payment_rent']['registrationlevel'] = mosHTML::selectList($gtree, 'payment_rent_registrationlevel[]', 'size="4" multiple="multiple"', 'value', 'text', $f);

    $lists['payment_real_or_test']['show'] = mosHTML::radioList($yesno, 'payment_real_or_test', 'class="inputbox"', $vehiclemanager_configuration['payment_real_or_test']['show'], 'value', 'text');

    $lists['special_price']['show'] = mosHTML::radioList($yesno, 'special_price', 'class="inputbox"', $vehiclemanager_configuration['special_price']['show'], 'value', 'text');

    $lists['google_openmap']['show'] = mosHTML::radioList($google_openmap, 'google_openmap', 'class="inputbox"', $vehiclemanager_configuration['google_openmap']['show'], 'value', 'text');

    $f = array();
    $s = explode(',', $vehiclemanager_configuration ['Location_vehicle']['registrationlevel']);
    for ($i = 0; $i < count($s); $i++)
        $f[] = mosHTML::makeOption($s[$i]);

    $lists['Location_vehicle']['show'] = mosHTML::radioList($yesno, 'Location_show_vehicle', 'class="inputbox"', $vehiclemanager_configuration['Location_vehicle']['show'], 'value', 'text');

    $lists['Location_vehicle']['registrationlevel'] = mosHTML::selectList($gtree, 'Location_vehicle_registrationlevel[]', 'size="4" multiple="multiple"', 'value', 'text', $f);

    $f = array();
    $s = explode(',', $vehiclemanager_configuration ['contacts']['registrationlevel']);
    for ($i = 0; $i < count($s); $i++)
        $f[] = mosHTML::makeOption($s[$i]);

    $lists['Contacts']['show'] = mosHTML::radioList($yesno, 'Contacts_show_vehicle', 'class="inputbox"', $vehiclemanager_configuration['Contacts']['show'], 'value', 'text');

    $lists['contacts']['registrationlevel'] = mosHTML::selectList($gtree, 'contacts_registrationlevel[]', 'size="4" multiple="multiple"', 'value', 'text', $f);

    $f = array();
    $s = explode(',', $vehiclemanager_configuration ['Reviews_vehicle']['registrationlevel']);
    for ($i = 0; $i < count($s); $i++)
        $f[] = mosHTML::makeOption($s[$i]);

    $lists['Reviews_vehicle']['show'] = mosHTML::radioList($yesno, 'Reviews_show_vehicle', 'class="inputbox"', $vehiclemanager_configuration['Reviews_vehicle']['show'], 'value', 'text');

    $lists['Reviews_vehicle']['registrationlevel'] = mosHTML::selectList($gtree, 'Reviews_vehicle_registrationlevel[]', 'size="4" multiple="multiple"', 'value', 'text', $f);

    $f = array();
    $s = explode(',', $vehiclemanager_configuration['edocs']['registrationlevel']);
    for ($i = 0; $i < count($s); $i++) {
        $f[] = mosHTML::makeOption($s[$i]);
    }

    $lists['edocs']['show'] = mosHTML::radioList($yesno, 'edocs_show', 'class="inputbox"', $vehiclemanager_configuration['edocs']['show'], 'value', 'text');
    $lists['videos_tracks']['show'] = mosHTML::radioList($yesno, 'videos_tracks_allow', 'class="inputbox"', $vehiclemanager_configuration['videos_tracks']['show'], 'value', 'text');
    $lists['videos']['location'] = '<input disabled="disabled" type="text" name="videos_location" value="' . $vehiclemanager_configuration['videos']['location'] . '" class="inputbox" size="50" maxlength="50" title="" />';
    $lists['tracks']['location'] = '<input disabled="disabled" type="text" name="tracks_location" value="' . $vehiclemanager_configuration['tracks']['location'] . '" class="inputbox" size="50" maxlength="50" title="" />';
    $lists['edocs']['registrationlevel'] = mosHTML::selectList($gtree, 'edocs_registrationlevel[]', 'size="4" multiple="multiple"', 'value', 'text', $f);

    $f = array();
    $s = explode(',', $vehiclemanager_configuration['price']['registrationlevel']);
    for ($i = 0; $i < count($s); $i++)
        $f[] = mosHTML::makeOption($s[$i]);

    $lists['price']['show'] = mosHTML::radioList($yesno, 'price_show', 'class="inputbox"', $vehiclemanager_configuration['price']['show'], 'value', 'text');

    $lists['price']['string'] = mosHTML::radioList($yesno, 'price_string', 'class="inputbox"', $vehiclemanager_configuration['price']['string'], 'value', 'text');

    $lists['price']['registrationlevel'] = mosHTML::selectList($gtree, 'price_registrationlevel[]', 'size="4" multiple="multiple"', 'value', 'text', $f);

    //********   begin add send mail for admin  ******************
    $f = array();
    $s = explode(',', $vehiclemanager_configuration['addvehicle_email']['registrationlevel']);
    for ($i = 0; $i < count($s); $i++)
        $f[] = mosHTML::makeOption($s[$i]);
    $lists['addvehicle_email']['show'] = mosHTML::radioList($yesno, 'addvehicle_email_show', 'class="inputbox"', $vehiclemanager_configuration['addvehicle_email']['show'], 'value', 'text');
    $lists['addvehicle_email']['registrationlevel'] = mosHTML::selectList($gtree, 'addvehicle_email_registrationlevel[]', 'size="4" multiple="multiple"', 'value', 'text', $f);

    $f = array();
    $s = explode(',', $vehiclemanager_configuration['review_added_email']['registrationlevel']);
    for ($i = 0; $i < count($s); $i++)
        $f[] = mosHTML::makeOption($s[$i]);
    $lists['review_added_email']['show'] = mosHTML::radioList($yesno, 'review_added_email_show', 'class="inputbox"', $vehiclemanager_configuration['review_added_email']['show'], 'value', 'text');
    $lists['review_added_email']['registrationlevel'] = mosHTML::selectList($gtree, 'review_added_email_registrationlevel[]', 'size="4" multiple="multiple"', 'value', 'text', $f);

    $f = array();
    $s = explode(',', $vehiclemanager_configuration['rentrequest_email']['registrationlevel'
            ]);
    for ($i = 0; $i < count($s); $i++)
        $f[] = mosHTML::makeOption($s[$i]);
    $lists['rentrequest_email']['show'] = mosHTML::radioList($yesno, 'rentrequest_email_show', 'class="inputbox"', $vehiclemanager_configuration['rentrequest_email']['show'], 'value', 'text');
    $lists['rentrequest_email']['registrationlevel'] = mosHTML::selectList($gtree, 'rentrequest_email_registrationlevel[]', 'size="4" multiple="multiple"', 'value', 'text', $f);
    //buying
    $s = explode(',', $vehiclemanager_configuration['buyingrequest_email']['registrationlevel'
            ]);
    for ($i = 0; $i < count($s); $i++)
        $f[] = mosHTML::makeOption($s[$i]);
    $lists['buyingrequest_email']['show'] = mosHTML::radioList($yesno, 'buyingrequest_email_show', 'class="inputbox"', $vehiclemanager_configuration['buyingrequest_email']['show'], 'value', 'text');
    $lists['buyingrequest_email']['registrationlevel'] = mosHTML::selectList($gtree, 'buyingrequest_email_registrationlevel[]', 'size="4" multiple="multiple"', 'value', 'text', $f);
  //********   end add send mail for admin   **********************

  //******   begin add for  Manager print_pdf: button 'print PDF'   *****
    $f = array();
    $s = explode(',', $vehiclemanager_configuration['print_pdf']['registrationlevel']);
    for ($i = 0; $i < count($s); $i++)
        $f[] = mosHTML::makeOption($s[$i]);

    $lists['print_pdf']['show'] = mosHTML::radioList($yesno, 'print_pdf_show', 'class="inputbox"', $vehiclemanager_configuration['print_pdf']['show'], 'value', 'text');

    $lists['print_pdf']['registrationlevel'] = mosHTML::selectList($gtree, 'print_pdf_registrationlevel[]', 'size="4" multiple="multiple"', 'value', 'text', $f);
  //*******   end add for Manager print_pdf: button 'print PDF'   *******
  //******   begin add for  Manager print_view: button 'print View'   *****
    $f = array();
    $s = explode(',', $vehiclemanager_configuration['print_view']['registrationlevel']);
    for ($i = 0; $i < count($s); $i++)
        $f[] = mosHTML::makeOption($s[$i]);

    $lists['print_view']['show'] = mosHTML::radioList($yesno, 'print_view_show', 'class="inputbox"', $vehiclemanager_configuration['print_view']['show'], 'value', 'text');

    $lists['print_view']['registrationlevel'] = mosHTML::selectList($gtree, 'print_view_registrationlevel[]', 'size="4" multiple="multiple"', 'value', 'text', $f);
  //*******   end add for Manager print_view: button 'print View'   *******
  //******   begin add for  Manager mail_to: button 'mail_to'   *****
    $f = array();
    $s = explode(',', $vehiclemanager_configuration['mail_to']['registrationlevel']);
    for ($i = 0; $i < count($s); $i++)
        $f[] = mosHTML::makeOption($s[$i]);

    $lists['mail_to']['show'] = mosHTML::radioList($yesno, 'mail_to_show', 'class="inputbox"', $vehiclemanager_configuration['mail_to']['show'], 'value', 'text');

    $lists['mail_to']['registrationlevel'] = mosHTML::selectList($gtree, 'mail_to_registrationlevel[]', 'size="4" multiple="multiple"', 'value', 'text', $f);
  //*******   end add for Manager mail_to: button 'mail_to'   *******
  //******   begin add for  Manager add_vehicle: button 'Add vehicle'   *****
    $f = array();
    $s = explode(',', $vehiclemanager_configuration['add_vehicle']['registrationlevel']);
    for ($i = 0; $i < count($s); $i++)
        $f[] = mosHTML::makeOption($s[$i]);

    $lists['add_vehicle']['show'] = mosHTML::radioList($yesno, 'add_vehicle_show', 'class="inputbox"', $vehiclemanager_configuration['add_vehicle']['show'], 'value', 'text');

    $lists['add_vehicle']['registrationlevel'] = mosHTML::selectList($gtree, 'add_vehicle_registrationlevel[]', 'size="4" multiple="multiple"', 'value', 'text', $f);
  //*******   end add for Manager add_vehicle: button 'Add vehicle'   *******
    //show location map
    $lists['location_map'] = mosHTML::radioList($yesno, 'location_map', 'class="inputbox"', $vehiclemanager_configuration['location_map'], 'value', 'text');

    $lists['thumb_param']['show'] = mosHTML::radioList($yesno, 'thumb_param_show', 'class="inputbox"',
     $vehiclemanager_configuration['thumb_param']['show'], 'value', 'text');

    // add rotate images
    $lists['rotate_img'] = mosHTML::RadioList($yesno, 'rotate_img', 'class="inputbox"', $vehiclemanager_configuration['rotate_img'], 'value', 'text');
    // end add rotate images

    //show image for feature manager
    $lists['manager_feature_image'] = mosHTML::radioList($yesno, 'manager_feature_image', 'class="inputbox"', $vehiclemanager_configuration['manager_feature_image'], 'value', 'text');
    //show category for feature manager
    $lists['manager_feature_category'] = mosHTML::radioList($yesno, 'manager_feature_category', 'class="inputbox"', $vehiclemanager_configuration['manager_feature_category'], 'value', 'text');

    //add show sale separator
    $lists['sale_separator'] = mosHTML::radioList($yesno, 'sale_separator', 'class="inputbox"', $vehiclemanager_configuration['sale_separator'], 'value', 'text');
    //end add show sale separator
    //add show sale fraction
    $lists['sale_fraction'] = mosHTML::radioList($yesno, 'sale_fraction', 'class="inputbox"', $vehiclemanager_configuration['sale_fraction'], 'value', 'text');
    //add show sale fraction

    $lists['extra1'] = mosHTML::radioList($yesno, 'extra1', 'class="inputbox"', $vehiclemanager_configuration['extra1'], 'value', 'text');
    $lists['extra2'] = mosHTML::radioList($yesno, 'extra2', 'class="inputbox"', $vehiclemanager_configuration['extra2'], 'value', 'text');
    $lists['extra3'] = mosHTML::radioList($yesno, 'extra3', 'class="inputbox"', $vehiclemanager_configuration['extra3'], 'value', 'text');
    $lists['extra4'] = mosHTML::radioList($yesno, 'extra4', 'class="inputbox"', $vehiclemanager_configuration['extra4'], 'value', 'text');
    $lists['extra5'] = mosHTML::radioList($yesno, 'extra5', 'class="inputbox"', $vehiclemanager_configuration['extra5'], 'value', 'text');
    $lists['extra6'] = mosHTML::radioList($yesno, 'extra6', 'class="inputbox"', $vehiclemanager_configuration['extra6'], 'value', 'text');
    $lists['extra7'] = mosHTML::radioList($yesno, 'extra7', 'class="inputbox"', $vehiclemanager_configuration['extra7'], 'value', 'text');
    $lists['extra8'] = mosHTML::radioList($yesno, 'extra8', 'class="inputbox"', $vehiclemanager_configuration['extra8'], 'value', 'text');
    $lists['extra9'] = mosHTML::radioList($yesno, 'extra9', 'class="inputbox"', $vehiclemanager_configuration['extra9'], 'value', 'text');
    $lists['extra10'] = mosHTML::radioList($yesno, 'extra10', 'class="inputbox"', $vehiclemanager_configuration['extra10'], 'value', 'text');
    //******** end add Custom Dropdown Field 1 options **********
    $lists['extra1_advanced'] = mosHTML::selectList($three_select_option, 'extra1_advanced', 'size="1"', 'value', 'text', $vehiclemanager_configuration['extra1_advanced']);
    //******** end add Custom Dropdown Field 1 options **********
    //******** end add Custom Dropdown Field 2 options **********
    $lists['extra2_advanced'] = mosHTML::selectList($three_select_option, 'extra2_advanced', 'size="1"', 'value', 'text', $vehiclemanager_configuration['extra2_advanced']);
    //******** end add Custom Dropdown Field 2 options **********
    //******** end add Custom Dropdown Field 3 options **********
    $lists['extra3_advanced'] = mosHTML::selectList($three_select_option, 'extra3_advanced', 'size="1"', 'value', 'text', $vehiclemanager_configuration['extra3_advanced']);
    //******** end add Custom Dropdown Field 3 options **********
    //******** end add Custom Dropdown Field 4 options **********
    $lists['extra4_advanced'] = mosHTML::selectList($three_select_option, 'extra4_advanced', 'size="1"', 'value', 'text', $vehiclemanager_configuration['extra4_advanced']);
    //******** end add Custom Dropdown Field 4 options **********
    //******** end add Custom Dropdown Field 5 options **********
    $lists['extra5_advanced'] = mosHTML::selectList($three_select_option, 'extra5_advanced', 'size="1"', 'value', 'text', $vehiclemanager_configuration['extra5_advanced']);
    //******** end add Custom Dropdown Field 5 options **********
    //******** add Custom Dropdown Field 6 options **********
    $lists['extra6_advanced'] = mosHTML::selectList($two_select_option, 'extra6_advanced', 'size="1"', 'value', 'text', $vehiclemanager_configuration['extra6_advanced']);
    //******** end add Custom Dropdown Field 6 options **********
    //******** add Custom Dropdown Field 7 options **********
    $lists['extra7_advanced'] = mosHTML::selectList($two_select_option, 'extra7_advanced', 'size="1"', 'value', 'text', $vehiclemanager_configuration['extra7_advanced']);
    //******** end add Custom Dropdown Field 7 options **********
    //******** add Custom Dropdown Field 8 options **********
    $lists['extra8_advanced'] = mosHTML::selectList($two_select_option, 'extra8_advanced', 'size="1"', 'value', 'text', $vehiclemanager_configuration['extra8_advanced']);
    //******** end add Custom Dropdown Field 8 options **********
    //******** add Custom Dropdown Field 6 options **********
    $lists['extra9_advanced'] = mosHTML::selectList($two_select_option, 'extra9_advanced', 'size="1"', 'value', 'text', $vehiclemanager_configuration['extra9_advanced']);
    //******** end add Custom Dropdown Field 9 options **********
    //******** add Custom Dropdown Field 10 options **********
    $lists['extra10_advanced'] = mosHTML::selectList($two_select_option, 'extra10_advanced', 'size="1"', 'value', 'text', $vehiclemanager_configuration['extra10_advanced']);
    //******** end add Custom Dropdown Field 10 options **********

    //******** add select list for feature in search tab **********
    $query = "SELECT name, id FROM #__vehiclemanager_feature";
    $database->setQuery($query);
    $features = $database->loadObjectList();

    foreach ($features as $feature) {
        if(!isset($vehiclemanager_configuration['search_form_features']["$feature->id"]) )
             $vehiclemanager_configuration['search_form_features']["$feature->id"] = 3 ;
        $lists["search_form_features_$feature->id"] = mosHTML::selectList($three_select_option, "search_form_features_$feature->id", 'size="1"', 'value', 'text', $vehiclemanager_configuration['search_form_features']["$feature->id"]);
    }
    //******** end add select list for feature in search tab **********


    $lists['foto']['high'] = '<input type="text" name="foto_high"
    value="' . $vehiclemanager_configuration['foto']['high'] .
            '" class="inputbox" size="4" maxlength="4" title="" />';

    $lists['foto']['width'] = '<input type="text" name="foto_width"
    value="' . $vehiclemanager_configuration['foto']['width'] .
            '" class="inputbox" size="4" maxlength="4" title="" />';

    $lists['fotomain']['high'] = '<input type="text" name="fotomain_high"
    value="' . $vehiclemanager_configuration['fotomain']['high'] .
            '" class="inputbox" size="4" maxlength="4" title="" />';

    $lists['fotomain']['width'] = '<input type="text" name="fotomain_width"
    value="' . $vehiclemanager_configuration['fotomain']['width'] .
            '" class="inputbox" size="4" maxlength="4" title="" />';

    $lists['fotogallery']['high'] = '<input type="text" name="fotogallery_high"
    value="' . $vehiclemanager_configuration['fotogallery']['high'] .
            '" class="inputbox" size="4" maxlength="4" title="" />';

    //add calendar year
    $year = date("Y", mktime(0, 0, 0, date('m'), 1, date('Y')));

    if( !isset($vehiclemanager_configuration['initial_year']) 
        || empty($vehiclemanager_configuration['initial_year']) 
        || intval($vehiclemanager_configuration['initial_year'] <= 0 )  
        ){
        $initial_year_val = $year;
    } else {
        $initial_year_val = intval($vehiclemanager_configuration['initial_year'] ) ;
    }

    if(!isset($vehiclemanager_configuration['final_year']) 
        || empty($vehiclemanager_configuration['final_year']) 
        || intval($vehiclemanager_configuration['final_year'] ) <= $initial_year_val){
        $final_year_val = $initial_year_val + 1;
    }
    else{
        $final_year_val = intval($vehiclemanager_configuration['final_year'] ) ;
    }

    $lists['initial_year'] = '<input type="text" name="initial_year"
    value="' . $initial_year_val . '" class="inputbox" size="4" maxlength="4" title="" />';

    $lists['final_year'] = '<input type="text" name="final_year"
    value="' . $final_year_val . '" class="inputbox" size="4" maxlength="4" title="" />';
    //end add calendar year

    $lists['fotogallery']['width'] = '<input type="text" name="fotogallery_width"
    value="' . $vehiclemanager_configuration['fotogallery']['width'] .
            '" class="inputbox" size="4" maxlength="4" title="" />';

    $lists['fotogal']['high'] = '<input type="text" name="fotogal_high"
    value="' . $vehiclemanager_configuration['fotogal']['high'] .
            '" class="inputbox" size="4" maxlength="4" title="" />';

    $lists['fotogal']['width'] = '<input type="text" name="fotogal_width"
    value="' . $vehiclemanager_configuration['fotogal']['width'] .
            '" class="inputbox" size="4" maxlength="4" title="" />';

    $lists['fotoupload']['high'] = '<input type="text" name="fotoupload_high"
    value="' . $vehiclemanager_configuration['fotoupload']['high'] .
            '" class="inputbox" size="4" maxlength="4" title="" />';

    $lists['fotoupload']['width'] = '<input type="text" name="fotoupload_width"
    value="' . $vehiclemanager_configuration['fotoupload']['width'] .
            '" class="inputbox" size="4" maxlength="4" title="" />';

    $lists['page']['items'] = '<input type="text" name="page_items"
    value="' . $vehiclemanager_configuration['page']['items'] .
            '" class="inputbox" size="3" maxlength="5" title="" />';

    $lists['license']['show'] = mosHTML::radioList($yesno, 'license_show', 'class="inputbox"', $vehiclemanager_configuration['license']['show'], 'value', 'text');

    $txt = $vehiclemanager_configuration['license']['text'];

    //add for show in category picture
    $lists['cat_pic']['show'] = mosHTML::radioList($yesno, 'cat_pic_show', 'class="inputbox"', $vehiclemanager_configuration['cat_pic']['show'], 'value', 'text');

    //add for show subcategory
    $lists['subcategory']['show'] = mosHTML::radioList($yesno, 'subcategory_show', 'class="inputbox"', $vehiclemanager_configuration['subcategory']['show'], 'value', 'text');
    //add for show single subcategory
    $lists['single_subcategory_show']['show'] = mosHTML::radioList($yesno, 'single_subcategory_show', 'class="inputbox"', $vehiclemanager_configuration['single_subcategory_show']['show'], 'value', 'text');
    //******   begin approve_on_add  *****
    $f = array();
    $s = explode(',', $vehiclemanager_configuration['approve_on_add']['registrationlevel']);
    for ($i = 0; $i < count($s); $i++)
        $f[] = mosHTML::makeOption($s[$i]);

    $lists['approve_on_add']['show'] = mosHTML::radioList($yesno, 'approve_on_add', 'class="inputbox"', $vehiclemanager_configuration['approve_on_add']['show'], 'value', 'text');
    $lists['approve_on_add']['registrationlevel'] = mosHTML::selectList($gtree, 'approve_on_add_registrationlevel[]', 'size="4" multiple="multiple"', 'value', 'text', $f);

  //*******   end approve_on_add   *******
   //******   begin publish_on_add  *****

    $f = array();
    $s = explode(',', $vehiclemanager_configuration['publish_on_add']['registrationlevel']);
    for ($i = 0; $i < count($s); $i++)
        $f[] = mosHTML::makeOption($s[$i]);

    $lists['publish_on_add']['show'] = mosHTML::radioList($yesno, 'publish_on_add', 'class="inputbox"', $vehiclemanager_configuration['publish_on_add']['show'], 'value', 'text');

    $lists['publish_on_add']['registrationlevel'] = mosHTML::selectList($gtree, 'publish_on_add_registrationlevel[]', 'size="4" multiple="multiple"', 'value', 'text', $f);

  //*******   end publish_on_add   *******


    //******   begin approve_review  *****
    $f = array();
    $s = explode(',', $vehiclemanager_configuration['approve_review']['registrationlevel']);
    for ($i = 0; $i < count($s); $i++)
        $f[] = mosHTML::makeOption($s[$i]);

    $lists['approve_review']['show'] = mosHTML::radioList($yesno, 'approve_review', 'class="inputbox"', $vehiclemanager_configuration['approve_review']['show'], 'value', 'text');
    $lists['approve_review']['registrationlevel'] = mosHTML::selectList($gtree, 'approve_review_registrationlevel[]', 'size="4" multiple="multiple"', 'value', 'text', $f);
    //*******   end approve_review   *******



    //********** RSS ************
    $f = array();
    $s = explode(',', $vehiclemanager_configuration['rss']['registrationlevel']);
    for ($i = 0; $i < count($s); $i++)
        $f[] = mosHTML::makeOption($s[$i]);
    $lists['rss']['show'] = mosHTML::radioList($yesno, 'rss_show', 'class="inputbox"', $vehiclemanager_configuration['rss']['show'], 'value', 'text');
    $lists['rss']['registrationlevel'] = mosHTML::selectList($gtree, 'rss_registrationlevel[]', 'size="4" multiple="multiple"', 'value', 'text', $f);
    //********** END RSS ************
    //********** Wishlist ************
    $f = array();
    $s = explode(',', $vehiclemanager_configuration['wishlist']['registrationlevel']);
    for ($i = 0; $i < count($s); $i++)
        $f[] = mosHTML::makeOption($s[$i]);
    $lists['wishlist']['show'] = mosHTML::radioList($yesno, 'wishlist_show', 'class="inputbox"', $vehiclemanager_configuration['wishlist']['show'], 'value', 'text');
    $lists['wishlist']['registrationlevel'] = mosHTML::selectList($gtree, 'wishlist_registrationlevel[]', 'size="4" multiple="multiple"', 'value', 'text', $f);
    //********** END Wishlist ************
    //********** review captcha ************
    $f = array();
    $s = explode(',', $vehiclemanager_configuration['review_captcha']['registrationlevel']);
    for ($i = 0; $i < count($s); $i++)
        $f[] = mosHTML::makeOption($s[$i]);
    $lists['review_captcha']['show'] = mosHTML::radioList($yesno, 'review_captcha_show', 'class="inputbox"', $vehiclemanager_configuration['review_captcha']['show'], 'value', 'text');
    $lists['review_captcha']['registrationlevel'] = mosHTML::selectList($gtree, 'review_captcha_registrationlevel[]', 'size="4" multiple="multiple"', 'value', 'text', $f);
    //********** END review captcha ************
    //********** contact captcha ************
    $f = array();
    $s = explode(',', $vehiclemanager_configuration['contact_captcha']['registrationlevel']);
    for ($i = 0; $i < count($s); $i++)
        $f[] = mosHTML::makeOption($s[$i]);
    $lists['contact_captcha']['show'] = mosHTML::radioList($yesno, 'contact_captcha_show', 'class="inputbox"', $vehiclemanager_configuration['contact_captcha']['show'], 'value', 'text', '0');
    $lists['contact_captcha']['registrationlevel'] = mosHTML::selectList($gtree, 'contact_captcha_registrationlevel[]', 'size="4" multiple="multiple"', 'value', 'text', $f);
    //********** END contact captcha ************
    //********** booking captcha ************
    $f = array();
    $s = explode(',', $vehiclemanager_configuration['booking_captcha']['registrationlevel']);
    for ($i = 0; $i < count($s); $i++)
        $f[] = mosHTML::makeOption($s[$i]);
    $lists['booking_captcha']['show'] = mosHTML::radioList($yesno, 'booking_captcha_show', 'class="inputbox"', $vehiclemanager_configuration['booking_captcha']['show'], 'value', 'text');
    $lists['booking_captcha']['registrationlevel'] = mosHTML::selectList($gtree, 'booking_captcha_registrationlevel[]', 'size="4" multiple="multiple"', 'value', 'text', $f);
    //********** END booking captcha ************

    //********** add vehicle captcha START ************
    $f = array();
    $s = explode(',', $vehiclemanager_configuration['add_vehicle_captcha']['registrationlevel']);
    for ($i = 0; $i < count($s); $i++)
        $f[] = mosHTML::makeOption($s[$i]);
    $lists['add_vehicle_captcha']['show'] = mosHTML::radioList($yesno, 'add_vehicle_captcha_show', 'class="inputbox"', $vehiclemanager_configuration['add_vehicle_captcha']['show'], 'value', 'text');
    $lists['add_vehicle_captcha']['registrationlevel'] = mosHTML::selectList($gtree, 'add_vehicle_captcha_registrationlevel[]', 'size="4" multiple="multiple"', 'value', 'text', $f);
    //********** END add vehicle captcha ************

    //********** google captcha ************
    $lists['google_captcha']['show'] = mosHTML::radioList($yesno, 'google_captcha_show', 'class="inputbox"', $vehiclemanager_configuration['google_captcha']['show'], 'value', 'text');
    //**********END google captcha ************
    //********** vehicle slider ************
    $lists['show_vehicle_slider'] = mosHTML::radioList($yesno, 'show_vehicle_slider', 'class="inputbox"', $vehiclemanager_configuration['show_vehicle_slider'], 'value', 'text');
    //**********END vehicle slider ************
    //********** show map for show-search-result layout ************
    $f = array();
    $s = explode(',', $vehiclemanager_configuration['show_map']['registrationlevel']);
    for ($i = 0; $i < count($s); $i++)
        $f[] = mosHTML::makeOption($s[$i]);
    $lists['show_map']['show'] = mosHTML::radioList($yesno, 'show_map_show', 'class="inputbox"', $vehiclemanager_configuration['show_map']['show'], 'value', 'text');
    $lists['show_map']['registrationlevel'] = mosHTML::selectList($gtree, 'show_map_registrationlevel[]', 'size="4" multiple="multiple"', 'value', 'text', $f);

    //********** END show map for  show-search-result-layout************
    //******** show order by form for show-search-result layout **********
    $f = array();
    $s = explode(',', $vehiclemanager_configuration['show_order_by']['registrationlevel']);
    for ($i = 0; $i < count($s); $i++)
        $f[] = mosHTML::makeOption($s[$i]);
    $lists['show_order_by']['show'] = mosHTML::radioList($yesno, 'show_order_by_show', 'class="inputbox"', $vehiclemanager_configuration['show_order_by']['show'], 'value', 'text');
    $lists['show_order_by']['registrationlevel'] = mosHTML::selectList($gtree, 'show_order_by_registrationlevel[]', 'size="4" multiple="multiple"', 'value', 'text', $f);
    //****** END show order by form for  show-search-result-layout********
    //********** Owners list ************
    $f = array();
    $s = explode(',', $vehiclemanager_configuration['ownerslist']['registrationlevel']);
    for ($i = 0; $i < count($s); $i++)
        $f[] = mosHTML::makeOption($s[$i]);
    $lists['ownerslist']['show'] = mosHTML::radioList($yesno, 'ownerslist_show', 'class="inputbox"', $vehiclemanager_configuration['ownerslist']['show'], 'value', 'text');
    $lists['ownerslist']['registrationlevel'] = mosHTML::selectList($gtree, 'ownerslist_registrationlevel[]', 'size="4" multiple="multiple"', 'value', 'text', $f);
  //********** END Owners list ************

    //bch
    //******   begin option access to edit vehicle  *****
    $f = array();
    $s = explode(',', $vehiclemanager_configuration['option_edit']['registrationlevel']);
    for ($i = 0; $i < count($s); $i++)
        $f[] = mosHTML::makeOption($s[$i]);

    $lists['option_edit']['show'] = mosHTML::radioList($yesno, 'option_edit',
     'class="inputbox"', $vehiclemanager_configuration['option_edit']['show'], 'value', 'text');

    $lists['option_edit']['registrationlevel'] = mosHTML::selectList($gtree,
     'option_edit_registrationlevel[]', 'size="4" multiple="multiple"', 'value', 'text', $f);
    //*******   end option access to edit vehicle   *******



    $lists['calendar']['show'] = mosHTML::radioList($yesno, 'calendar_show', 'class="inputbox"', $vehiclemanager_configuration['calendar']['show'], 'value', 'text');
    // $lists['contact']['show'] = mosHTML::radioList($yesno, 'contact_show', 'class="inputbox"', $vehiclemanager_configuration['contact']['show'], 'value', 'text');

  //configure_backend
    $lists['addvehicle_email']['address'] = '<input type="text" name="addvehicle_email_address" value="' . $vehiclemanager_configuration['addvehicle_email']['address'] . '" class="inputbox" size="50" maxlength="50" title="" />';
    $lists['review_email']['address'] = '<input type="text" name="review_email_address" value="' . $vehiclemanager_configuration['review_email']['address'] . '" class="inputbox" size="50" maxlength="50" title="" />';
    $lists['rentrequest_email']['address'] = '<input type="text" name="rentrequest_email_address" value="' . $vehiclemanager_configuration['rentrequest_email']['address'] . '" class="inputbox" size="50" maxlength="50" title="" />';
    $lists['buyingrequest_email']['address'] = '<input type="text" name="buyingrequest_email_address" value="' . $vehiclemanager_configuration['buyingrequest_email']['address'] . '" class="inputbox" size="50" maxlength="50" title="" />';

    $lists['vehicleid']['auto-increment']['boolean'] = mosHTML::radioList($yesno, 'vehicleid_auto_increment_boolean', 'class="inputbox"', $vehiclemanager_configuration['vehicleid']['auto-increment']['boolean'], 'value', 'text');

    $lists['edocs']['allow'] = mosHTML::radioList($yesno, 'edocs_allow', 'class="inputbox"', $vehiclemanager_configuration['edocs']['allow'], 'value', 'text');

    $lists['edocs']['location'] = '<input type="text" name="edocs_location" readonly="readonly" value="' . $vehiclemanager_configuration['edocs']['location'] . '" class="inputbox" size="50" maxlength="50" title="" />';

    $lists['calendar']['placeholder'] = '<input type="text" name="calendar_placeholder" value="' . $vehiclemanager_configuration['calendar']['placeholder'] . '" class="inputbox" size="50" maxlength="50" title=""/>';
    $lists['contact']['placeholder'] = '<input type="text" name="contact_placeholder" value="' . $vehiclemanager_configuration['contact']['placeholder'] . '" class="inputbox" size="50" maxlength="50" title=""/>';

    $lists['featuredmanager']['placeholder'] = '<input type="text" name="featuredmanager_placeholder" value="' . $vehiclemanager_configuration['featuredmanager']['placeholder'] . '" class="inputbox" size="50" maxlength="500" title=""/>';

    $lists['currency'] = '<input type="text" name="currency" value="' . $vehiclemanager_configuration['currency'] . '" class="inputbox" size="50" maxlength="500" title=""/>';

    //PayPal

    $lists['allowed_exts'] = '<input type="text" name="allowed_exts" value="' . $vehiclemanager_configuration['allowed_exts'] . '" class="inputbox" size="50" maxlength="1500" title=""/>';
    $lists['allowed_exts_img'] = '<input type="text" name="allowed_exts_img" value="' . $vehiclemanager_configuration['allowed_exts_img'] . '" class="inputbox" size="50" maxlength="1500" title=""/>';
    $lists['allowed_exts_video'] = '<input type="text" name="allowed_exts_video" value="' . $vehiclemanager_configuration['allowed_exts_video'] . '" class="inputbox" size="50" maxlength="1500" title=""/>';
    $lists['allowed_exts_track'] = '<input type="text" name="allowed_exts_track" value="' . $vehiclemanager_configuration['allowed_exts_track'] . '" class="inputbox" size="50" maxlength="1500" title=""/>';
    //rent request answer
    $lists['rent_answer'] = mosHTML::radioList($yesno, 'rent_answer', 'class="inputbox"', $vehiclemanager_configuration['rent_answer'], 'value', 'text');

    $lists['buy_answer'] = mosHTML::radioList($yesno, 'buy_answer', 'class="inputbox"', $vehiclemanager_configuration['buy_answer'], 'value', 'text');

    //Options for order default
    $default_order_option = array();
    $default_order_option[] = JHtml::_('select.option', "date", "Date");
    $default_order_option[] = JHtml::_('select.option', "price", "Price");
    $default_order_option[] = JHtml::_('select.option', "maker", "Model");
    $default_order_option[] = JHtml::_('select.option', "vtitle", "Title");
    $default_order_option[] = JHtml::_('select.option', "ordering", "User order");



    //Options for select menu on search page
    $search_show_select = array();
    $search_show_select[] = JHtml::_('select.option', "1", "Show & search");
    $search_show_select[] = JHtml::_('select.option', "3", "None");

    //Options for checkbox on search page
    $search_show_checkbox = array();
    $search_show_checkbox[] = JHtml::_('select.option', "1", "Show & search");
    $search_show_checkbox[] = JHtml::_('select.option', "2", "Search");
    $search_show_checkbox[] = JHtml::_('select.option', "3", "None");

    //Redirect settings on frontend page
    $redirect_option = array();
    $redirect_option[] = JHtml::_('select.option', "default", "Default");
    $redirect_option[] = JHtml::_('select.option', "redirect to current vehicle", "Redirect to current vehicle");
    $redirect_option[] = JHtml::_('select.option', "redirect to input link", "Redirect to input link");

    $money_ditlimer = array();
    $money_ditlimer[] = JHtml::_('select.option', ".", "Point (12.134.123,12)");
    $money_ditlimer[] = JHtml::_('select.option', ",", "Comma (12,134,123.12)");
    $money_ditlimer[] = JHtml::_('select.option', "space", "Space (12 134 123,12)");
    $money_ditlimer[] = JHtml::_('select.option', "other", "Youre ditlimer: ");

    $veh_columns_lg = array();
    $veh_columns_lg[] = JHtml::_('select.option', "1", "1");
    $veh_columns_lg[] = JHtml::_('select.option', "2", "2");
    $veh_columns_lg[] = JHtml::_('select.option', "3", "3");
    $veh_columns_lg[] = JHtml::_('select.option', "4", "4");

    $veh_columns_md = array();
    $veh_columns_md[] = JHtml::_('select.option', "1", "1");
    $veh_columns_md[] = JHtml::_('select.option', "2", "2");
    $veh_columns_md[] = JHtml::_('select.option', "3", "3");
    $veh_columns_md[] = JHtml::_('select.option', "4", "4");

    $veh_columns_sm = array();
    $veh_columns_sm[] = JHtml::_('select.option', "1", "1");
    $veh_columns_sm[] = JHtml::_('select.option', "2", "2");
    $veh_columns_sm[] = JHtml::_('select.option', "3", "3");

    $veh_columns_xs = array();
    $veh_columns_xs[] = JHtml::_('select.option', "1", "1");
    $veh_columns_xs[] = JHtml::_('select.option', "2", "2");

    $price_unit_show = array();
    $price_unit_show[] = mosHTML::makeOption('1', _VEHICLE_MANAGER_PRICE_UNIT_SHOW_AFTER);
    $price_unit_show[] = mosHTML::makeOption('0', _VEHICLE_MANAGER_PRICE_UNIT_SHOW_BEFORE);

    $selecter = '';
    switch ($vehiclemanager_configuration['price_format']) {
        case '.':
            $selecter = '.';
            break;
        case ',':
            $selecter = ',';
            break;
        case '&nbsp;':
            $selecter = 'space';
            break;
        default:
            $selecter = 'other';
    }

    //06.06.17 redirect rent options
    $redirect_rent_selecter = '';
    switch ($vehiclemanager_configuration['input_link_rent']) {
        case 'default':
            $redirect_rent_selecter = 'default';
            break;
        case 'redirect to current vehicle':
            $redirect_rent_selecter = 'redirect to current vehicle';
            break;
        default:
            $redirect_rent_selecter = 'redirect to input link';
    }

    //06.06.17 redirect sale options
    $redirect_sale_selecter = '';
    switch ($vehiclemanager_configuration['input_link_sale']) {
        case 'default':
            $redirect_sale_selecter = 'default';
            break;
        case 'redirect to current vehicle':
            $redirect_sale_selecter = 'redirect to current vehicle';
            break;
        default:
            $redirect_sale_selecter = 'redirect to input link';
    }

    //redirect rent
    $lists['redirect_rent'] = mosHTML::selectList($redirect_option, 'redirect_rent', 'size="1"  onchange="set_rent_options(this)"', 'value', 'text', $redirect_rent_selecter);
    $lists['patern_rent'] = '<input id="patern_rent" type="hidden" readonly="true" value="' . $vehiclemanager_configuration['input_link_rent'] . '" name="patern_rent" size="2"/>';
    //end redirect rent
    //redirect rent
    $lists['redirect_sale'] = mosHTML::selectList($redirect_option, 'redirect_sale', 'size="1"  onchange="set_sale_options(this)"', 'value', 'text', $redirect_sale_selecter);
    $lists['patern_sale'] = '<input id="patern_sale" type="hidden" readonly="true" value="' . $vehiclemanager_configuration['input_link_sale'] . '" name="patern_sale" size="2"/>';
    //end redirect rent

    $lists['price_unit_show'] = mosHTML::radioList($price_unit_show, 'price_unit_show', 'class="inputbox"', $vehiclemanager_configuration['price_unit_show'], 'value', 'text');

    //set price format
    $lists['money_ditlimer'] = mosHTML::selectList($money_ditlimer, 'money_select', 'size="1"  onchange="set_pricetype(this)"', 'value', 'text', $selecter);
    $lists['patern'] = '<input id="patt" type="hidden" readonly="true" value="' . $vehiclemanager_configuration['price_format'] . '" name="patern" size="2"/>';
    //end set price format

    $lists['date_format'] = '<input type="text" name="date_format" value="' . $vehiclemanager_configuration['date_format'] . '" class="inputbox"  title="" />';
    $lists['api_key'] = '<input type="text" id="api_key" name="api_key" value="' . $vehiclemanager_configuration['api_key'] . '" class="inputbox" title="" />';


    //start watermark
    $lists['watermark']['show'] = mosHTML::RadioList($yesno, 'watermark_show',
     'class="inputbox"', $vehiclemanager_configuration['watermark']['show'], 'value', 'text');
    $watermark_type[] = mosHTML::makeOption('text','Text');
    $watermark_type[] = mosHTML::makeOption('image','Image');
    $lists['watermark']['type'] = mosHTML::selectList($watermark_type,
     'watermark_type', '', 'value', 'text', $vehiclemanager_configuration['watermark']['type']);
    $lists['watermark']['text'] = '<input type="text" name="watermark_text" value="' .
    $vehiclemanager_configuration['watermark']['text'] . '" class="inputbox" size="50" maxlength="50" title="" />';
    $lists['watermark']['size'] = '<input type="number" name="watermark_size" value="' .
    $vehiclemanager_configuration['watermark']['size'] . '" class="inputbox" min="0" max="500" size="50" maxlength="50" title="" />';
    $lists['watermark']['color'] = '<input type="text" name="watermark_color" value="' .
    $vehiclemanager_configuration['watermark']['color'] . '" class="inputbox" max="300" size="50" maxlength="50" title="" />';

    $lists['watermark']['min_image_width'] = '<input type="number" name="watermark_min_image_width" value="' .
    $vehiclemanager_configuration['watermark']['min_image_width'] . '" style="width:70px;" class="inputbox" min="0" max="20000" size="5" maxlength="5" title="" />';
    $lists['watermark']['min_image_high'] = '<input type="number" name="watermark_min_image_high" value="' .
    $vehiclemanager_configuration['watermark']['min_image_high'] . '" style="width:70px;" class="inputbox" min="0" max="20000" size="5" maxlength="5" title="" />';

    $watermark_angle = array();
    $watermark_angle[] = mosHTML::makeOption(0,'0'.'&deg;');
    $watermark_angle[] = mosHTML::makeOption(45,'45'.'&deg;');
    $watermark_angle[] = mosHTML::makeOption(90,'90'.'&deg;');
    $lists['watermark']['angle'] = mosHTML::selectList($watermark_angle,
     'watermark_angle', '', 'value', 'text', $vehiclemanager_configuration['watermark']['angle']);

    $watermark_position = array();
    $watermark_position[] = mosHTML::makeOption('top_right','Top right');
    $watermark_position[] = mosHTML::makeOption('top_left','Top left');
    $watermark_position[] = mosHTML::makeOption('center','Center');
    $watermark_position[] = mosHTML::makeOption('bottom_right','Bottom right');
    $watermark_position[] = mosHTML::makeOption('bottom_left','Bottom left');
    $lists['watermark']['position'] = mosHTML::selectList($watermark_position,
     'watermark_position', '', 'value', 'text', $vehiclemanager_configuration['watermark']['position']);


    $category_ordering = array();
    $category_ordering[] = mosHTML::makeOption('name','name');
    $category_ordering[] = mosHTML::makeOption('ordering','ordering');
    $lists['category']['ordering'] = mosHTML::selectList($category_ordering,
     'category_ordering', '', 'value', 'text', $vehiclemanager_configuration['category']['ordering']);


    $lists['watermark']['opacity'] = '<input type="number" name="watermark_opacity" value="' .
    $vehiclemanager_configuration['watermark']['opacity'] . '" class="inputbox" min="0" max="100"  title="" />';
    //end watermark


    //slider
    $lists['slider']['height'] = '<input style="width:70px" type="number" name="slider_height" value="' .
    $vehiclemanager_configuration['slider']['height'] . '" size="5" maxlength="5" class="inputbox" min="1" max="500"  title="" />';

    $slider_object_fit = array();
    $slider_object_fit[] = mosHTML::makeOption('cover','Filling in');
    $slider_object_fit[] = mosHTML::makeOption('contain','Proportionally');
    $lists['slider']['object_fit'] = mosHTML::selectList($slider_object_fit,
     'slider_object_fit', '', 'value', 'text', $vehiclemanager_configuration['slider']['object_fit']);
    //slider

    //notify before end rent
    $lists['rent_before_end_notify'] = mosHTML::radioList($yesno, 'rent_before_end_notify', 'class="inputbox"', $vehiclemanager_configuration['rent_before_end_notify'], 'value', 'text');
    $lists['rent_before_end_notify_days'] = '<input type="text" name="rent_before_end_notify_days" value="' . $vehiclemanager_configuration['rent_before_end_notify_days'] . '" class="inputbox" size="2" maxlength="2" title="" />';
    $lists['rent_before_end_notify_email'] = '<input type="text" name="rent_before_end_notify_email" value="' . $vehiclemanager_configuration['rent_before_end_notify_email'] . '" class="inputbox" size="50" maxlength="50" title="" />';

    //******** show search button by form for show-search-result layout **********
    $f = array();
    $s = explode(',', $vehiclemanager_configuration['search_button']['registrationlevel']);
    for ($i = 0; $i < count($s); $i++)
        $f[] = mosHTML::makeOption($s[$i]);
    $lists['search_button']['show'] = mosHTML::radioList($yesno, 'search_button_show', 'class="inputbox"', $vehiclemanager_configuration['search_button']['show'], 'value', 'text');
    $lists['search_button']['registrationlevel'] = mosHTML::selectList($gtree, 'search_button_registrationlevel[]', 'size="4" multiple="multiple"', 'value', 'text', $f);
    //****** END show search button by form for  show-search-result-layout********
    //******** Add send reply to 'Email and notification setting' **********
    $lists['rent_request_send_reply_to']['add'] = mosHTML::radioList($yesno, 'rent_request_send_reply_to_add', 'class="inputbox"', $vehiclemanager_configuration['rent_request_send_reply_to']['add'], 'value', 'text');
    //********end Add send reply to 'Email and notification setting' **********
    //******** Add send reply to 'Email and notification setting' **********
    $lists['notification_review_send_reply_to']['add'] = mosHTML::radioList($yesno, 'notification_review_send_reply_to_add', 'class="inputbox"', $vehiclemanager_configuration['notification_review_send_reply_to']['add'], 'value', 'text');
    //********end Add send reply to 'Email and notification setting' **********
    //******** Add send reply to 'Email and notification setting' **********
    $lists['vehicle_notification_send_reply_to']['add'] = mosHTML::radioList($yesno, 'vehicle_notification_send_reply_to_add', 'class="inputbox"', $vehiclemanager_configuration['vehicle_notification_send_reply_to']['add'], 'value', 'text');
    //********end Add send reply to 'Email and notification setting' **********
    //******** Add send buying request notification' **********
    $lists['buying_request_notification_send_reply_to']['add'] = mosHTML::radioList($yesno, 'buying_request_notification_send_reply_to_add', 'class="inputbox"', $vehiclemanager_configuration['buying_request_notification_send_reply_to']['add'], 'value', 'text');
    //********end Add send buying request notification' **********

    //******** add option show keywords in search page **********
    $lists['keywords_search_show_select'] = mosHTML::selectList($search_show_select, 'keywords_search_show_select', 'size="1"', 'value', 'text', $vehiclemanager_configuration['keywords_search_show_select']);
    //******** end add option show keywords in search page **********

    //******** add option Radiuse range in search page **********
    $lists['search_form_radiuse_range_field_show'] = mosHTML::selectList($three_select_option, 'search_form_radiuse_range_field_show', 'size="1"', 'value', 'text', $vehiclemanager_configuration['search_form_radiuse_range_field_show']);

    $vh_range = array();
    $vh_range[] = mosHtml::makeOption(0, _VEHICLE_MANAGER_LABEL_MOD_MAP_SEARCH_RANGE);
    $vh_ranges = explode(',', _VEHICLE_MANAGER_OPTION_SEARCH_RADIUS_RANGE_LISTING);
    foreach ( $vh_ranges as $range ) {
        $vh_range[] = mosHtml::makeOption($range, $range);
    }

    $lists['search_form_radiuse_range'] = mosHTML::selectList($vh_range, 'search_form_radiuse_range',
      'class="inputbox" size="1"', 'value', 'text', $vehiclemanager_configuration['search_form_radiuse_range']);
    //******** end add option Radiuse range in search page **********

    //******** add option show year of issue in search page **********
    $lists['year_of_issue_show_select'] = mosHTML::selectList($search_show_select, 'year_of_issue_show_select', 'size="1"', 'value', 'text', $vehiclemanager_configuration['year_of_issue_show_select']);
    //******** end add option show year of issue in search page **********

    //******** add option show price of vehicle in search page **********
    $lists['price_vehicle_show_select'] = mosHTML::selectList($search_show_select, 'price_vehicle_show_select', 'size="1"', 'value', 'text', $vehiclemanager_configuration['price_vehicle_show_select']);
    //******** end add option show price of vehicle in search page **********

    //******** add option show condition status in search page **********
    $lists['condition_status_show_select'] = mosHTML::selectList($search_show_select, 'condition_status_show_select', 'size="1"', 'value', 'text', $vehiclemanager_configuration['condition_status_show_select']);
    //******** end add option show condition status in search page **********

    //******** add option show listing status in search page **********
    $lists['listing_status_show_select'] = mosHTML::selectList($search_show_select, 'listing_status_show_select', 'size="1"', 'value', 'text', $vehiclemanager_configuration['listing_status_show_select']);
    //******** end add option show listing status in search page **********

    //******** add option show transmission type in search page **********
    $lists['transmission_type_show_select'] = mosHTML::selectList($search_show_select, 'transmission_type_show_select', 'size="1"', 'value', 'text', $vehiclemanager_configuration['transmission_type_show_select']);
    //******** end add option show transmission type in search page **********

    //******** add option show maker in search page **********
     $lists['maker_show_select'] = mosHTML::selectList($search_show_select, 'maker_show_select', 'size="1"', 'value', 'text', $vehiclemanager_configuration['maker_show_select']);
    //******** end add option show maker in search page **********

    //******** add option show drive type in search page **********
    $lists['drive_type_show_select'] = mosHTML::selectList($search_show_select, 'drive_type_show_select', 'size="1"', 'value', 'text', $vehiclemanager_configuration['drive_type_show_select']);
    //******** end add option show drive type in search page **********

    //******** add option show model in search page **********
    $lists['model_show_select'] = mosHTML::selectList($search_show_select, 'model_show_select', 'size="1"', 'value', 'text', $vehiclemanager_configuration['model_show_select']);
    //******** end add option show model in search page **********

    //******** add option show number cylinders in search page **********
    $lists['number_cylinders_show_select'] = mosHTML::selectList($search_show_select, 'number_cylinders_show_select', 'size="1"', 'value', 'text', $vehiclemanager_configuration['number_cylinders_show_select']);
    //******** end add option show number cylinders in search page **********

    //******** add option show vehicle_type in search page **********
    $lists['vehicle_type_show_select'] = mosHTML::selectList($search_show_select, 'vehicle_type_show_select', 'size="1"', 'value', 'text', $vehiclemanager_configuration['vehicle_type_show_select']);
    //******** end add option show vehicle_type in search page **********

    //******** add option show number speeds in search page **********
    $lists['number_speeds_show_select'] = mosHTML::selectList($search_show_select, 'number_speeds_show_select', 'size="1"', 'value', 'text', $vehiclemanager_configuration['number_speeds_show_select']);
    //******** end add option show number_speeds in search page **********

    //******** add option show listing type in search page **********
    $lists['listing_type_show_select'] = mosHTML::selectList($search_show_select, 'listing_type_show_select', 'size="1"', 'value', 'text', $vehiclemanager_configuration['listing_type_show_select']);
    //******** end add option show listing type in search page **********

    //******** add option show fuel type in search page **********
    $lists['fuel_type_show_select'] = mosHTML::selectList($search_show_select, 'fuel_type_show_select', 'size="1"', 'value', 'text', $vehiclemanager_configuration['fuel_type_show_select']);
    //******** end add option show fuel type in search page **********

    //******** add option show price type in search page **********
    $lists['price_type_show_select'] = mosHTML::selectList($search_show_select, 'price_type_show_select', 'size="1"', 'value', 'text', $vehiclemanager_configuration['price_type_show_select']);
    //******** end add option show price type in search page **********

    //******** add option show number doors in search page **********
    $lists['number_doors_show_select'] = mosHTML::selectList($search_show_select, 'number_doors_show_select', 'size="1"', 'value', 'text', $vehiclemanager_configuration['number_doors_show_select']);
    //******** end add option show number doors in search page **********

    //******** add option show category in search page **********
    $lists['category_show_select'] = mosHTML::selectList($search_show_select, 'category_show_select', 'size="1"', 'value', 'text', $vehiclemanager_configuration['category_show_select']);
    //******** end add option show category in search page **********

    //******** add option show vehicleid in search page **********
    $lists['vehicleid_show_select'] = mosHTML::selectList($search_show_checkbox, 'vehicleid_show_select', 'size="1"', 'value', 'text', $vehiclemanager_configuration['vehicleid_show_select']);
    //******** end add option show vehicleid in search page **********

    //******** add option show comment in search page **********
    $lists['comment_show_select'] = mosHTML::selectList($search_show_checkbox, 'comment_show_select', 'size="1"', 'value', 'text', $vehiclemanager_configuration['comment_show_select']);
    //******** end add option show comment in search page **********

    //******** add option show title in search page **********
    $lists['title_show_select'] = mosHTML::selectList($search_show_checkbox, 'title_show_select', 'size="1"', 'value', 'text', $vehiclemanager_configuration['title_show_select']);
    //******** end add option show title in search page **********

    //******** add option show address in search page **********
    $lists['address_show_select'] = mosHTML::selectList($search_show_checkbox, 'address_show_select', 'size="1"', 'value', 'text', $vehiclemanager_configuration['address_show_select']);
    //******** end add option show address in search page **********

    $sel = $search_show_select;
    // if($vehiclemanager_configuration['show_country_region_city_as_text_field']==1){
    if($vehiclemanager_configuration['show_country_region_city_as_text_field']==0){
        $sel = $search_show_checkbox;
    }

    //******** add option show country checkbox in search page **********
    // $lists['country_show_select'] = mosHTML::selectList($search_show_checkbox, 'country_show_select', 'size="1"', 'value', 'text', $vehiclemanager_configuration['country_show_select']);
    $lists['country_show_select'] = mosHTML::selectList($sel, 'country_show_select', 'size="1"', 'value', 'text', $vehiclemanager_configuration['country_show_select']);
    //******** end add option show country in search page **********

    //******** add option show region in search page **********
    // $lists['region_show_select'] = mosHTML::selectList($search_show_checkbox, 'region_show_select', 'size="1"', 'value', 'text', $vehiclemanager_configuration['region_show_select']);
    $lists['region_show_select'] = mosHTML::selectList($sel, 'region_show_select', 'size="1"', 'value', 'text', $vehiclemanager_configuration['region_show_select']);
    //******** end add option show region in search page **********

    //******** add option show city in search page **********
    $lists['city_show_select'] = mosHTML::selectList($sel, 'city_show_select', 'size="1"', 'value', 'text', $vehiclemanager_configuration['city_show_select']);
    //******** end add option show city in search page **********

    //******** add option show district in search page **********
    $lists['district_show_select'] = mosHTML::selectList($search_show_checkbox, 'district_show_select', 'size="1"', 'value', 'text', $vehiclemanager_configuration['district_show_select']);
    //******** end add option show district in search page **********

    //******** add option show zipcode in search page **********
    $lists['zipcode_show_select'] = mosHTML::selectList($search_show_checkbox, 'zipcode_show_select', 'size="1"', 'value', 'text', $vehiclemanager_configuration['zipcode_show_select']);
    //******** end add option show zipcode in search page **********

    //******** add option show owner in search page **********
    $lists['owner_show_select'] = mosHTML::selectList($search_show_checkbox, 'owner_show_select', 'size="1"', 'value', 'text', $vehiclemanager_configuration['owner_show_select']);
    //******** end add option show owner in search page **********

    //******** add option show mileage in search page **********
    $lists['mileage_show_select'] = mosHTML::selectList($search_show_select, 'mileage_show_select', 'size="1"', 'value', 'text', $vehiclemanager_configuration['mileage_show_select']);
    //******** end add option show mileage in search page **********

    //******** add option show contacts in search page **********
    $lists['contacts_show_select'] = mosHTML::selectList($search_show_checkbox, 'contacts_show_select', 'size="1"', 'value', 'text', $vehiclemanager_configuration['contacts_show_select']);
    //******** end add option show contacts in search page **********

    //******** add option show engine type in search page **********
    $lists['engine_type_show_select'] = mosHTML::selectList($search_show_checkbox, 'engine_type_show_select', 'size="1"', 'value', 'text', $vehiclemanager_configuration['engine_type_show_select']);
    //******** end add option show engine type in search page **********

    //******** add option show city mpg in search page **********
    $lists['city_mpg_show_select'] = mosHTML::selectList($search_show_checkbox, 'city_mpg_show_select', 'size="1"', 'value', 'text', $vehiclemanager_configuration['city_mpg_show_select']);
    //******** end add option show city mpg in search page **********

    //******** add option show highway mpg in search page **********
    $lists['highway_mpg_show_select'] = mosHTML::selectList($search_show_checkbox, 'highway_mpg_show_select', 'size="1"', 'value', 'text', $vehiclemanager_configuration['highway_mpg_show_select']);
    //******** end add option show highway mpg in search page **********

    //******** add option show wheelbase in search page **********
    $lists['wheelbase_show_select'] = mosHTML::selectList($search_show_checkbox, 'wheelbase_show_select', 'size="1"', 'value', 'text', $vehiclemanager_configuration['wheelbase_show_select']);
    //******** end add option show wheelbase in search page **********

    //******** add option show wheeltype in search page **********
    $lists['wheeltype_show_select'] = mosHTML::selectList($search_show_checkbox, 'wheeltype_show_select', 'size="1"', 'value', 'text', $vehiclemanager_configuration['wheeltype_show_select']);
    //******** end add option show wheeltype in search page **********

    //******** add option show rearaxe type in search page **********
    $lists['rearaxe_type_show_select'] = mosHTML::selectList($search_show_checkbox, 'rearaxe_type_show_select', 'size="1"', 'value', 'text', $vehiclemanager_configuration['rearaxe_type_show_select']);
    //******** end add option show rearaxe type in search page **********

    //******** add option show brakes type in search page **********
    $lists['brakes_type_show_select'] = mosHTML::selectList($search_show_checkbox, 'brakes_type_show_select', 'size="1"', 'value', 'text', $vehiclemanager_configuration['brakes_type_show_select']);
    //******** end add option show brakes type in search page **********

    //******** add option show exterior colors in search page **********
    $lists['exterior_colors_show_select'] = mosHTML::selectList($search_show_checkbox, 'exterior_colors_show_select', 'size="1"', 'value', 'text', $vehiclemanager_configuration['exterior_colors_show_select']);
    //******** end add option show exterior colors in search page **********

    //******** add option show exterior extras in search page **********
    $lists['exterior_extras_show_select'] = mosHTML::selectList($search_show_checkbox, 'exterior_extras_show_select', 'size="1"', 'value', 'text', $vehiclemanager_configuration['exterior_extras_show_select']);
    //******** end add option show exterior extras in search page **********

    //******** add option show interior colors in search page **********
    $lists['interior_colors_show_select'] = mosHTML::selectList($search_show_checkbox, 'interior_colors_show_select', 'size="1"', 'value', 'text', $vehiclemanager_configuration['interior_colors_show_select']);
    //******** end add option show interior colors in search page **********

    //******** add option show dashboard in search page **********
    $lists['dashboard_options_show_select'] = mosHTML::selectList($search_show_checkbox, 'dashboard_options_show_select', 'size="1"', 'value', 'text', $vehiclemanager_configuration['dashboard_options_show_select']);
    //******** end add option show dashboard in search page **********

    //******** add option show interior extras in search page **********
    $lists['interior_extras_show_select'] = mosHTML::selectList($search_show_checkbox, 'interior_extras_show_select', 'size="1"', 'value', 'text', $vehiclemanager_configuration['interior_extras_show_select']);
    //******** end add option show interior extras in search page **********

    //******** add option show safety options in search page **********
    $lists['safety_options_show_select'] = mosHTML::selectList($search_show_checkbox, 'safety_options_show_select', 'size="1"', 'value', 'text', $vehiclemanager_configuration['safety_options_show_select']);
    //******** end add option show safety options in search page **********

    //******** add option show warranty options in search page **********
    $lists['warranty_options_show_select'] = mosHTML::selectList($search_show_checkbox, 'warranty_options_show_select', 'size="1"', 'value', 'text', $vehiclemanager_configuration['warranty_options_show_select']);
    //******** end add option show warranty options in search page **********

    //******** add option show exactly in search page **********
    $lists['exactly_show_select'] = mosHTML::selectList($search_show_checkbox, 'exactly_show_select', 'size="1"', 'value', 'text', $vehiclemanager_configuration['exactly_show_select']);
    //******** end add option show exactly in search page **********

    //******** add option default search layout **********
    $lists['default_search_layout'] = getSearchLayouts();
    //******** end add option default search layout **********

    //******** add option order default **********
    $lists['order_by_default'] = mosHTML::selectList($default_order_option, 'order_by_default', 'size="1"', 'value', 'text', $vehiclemanager_configuration['order_by_default']);
    //******** end add option order default **********

    //01.06.17
    //******** add option show form in search page **********
    $lists['search_form_nothing_found_page_show'] = mosHTML::radioList($yesno, 'search_form_nothing_found_page_show', 'class="inputbox"', $vehiclemanager_configuration['search_form_nothing_found_page_show'], 'value', 'text');
    //******** end add option show form in search page **********
    //******** add option show form in result search page **********
    $lists['search_form_on_search_page_result_show'] = mosHTML::radioList($yesno, 'search_form_on_search_page_result_show', 'class="inputbox"', $vehiclemanager_configuration['search_form_on_search_page_result_show'], 'value', 'text');
    //******** end add option show form in result search page **********
    
    //******** add option show available for rent in search page **********
    // Remove here from line 5333:
    // $lists['available_for_rent_show_select'] = mosHTML::selectList($search_show_select, 'available_for_rent_show_select', 'size="1"', 'value', 'text', $vehiclemanager_configuration['available_for_rent_show_select']);
    $lists['available_for_rent_show_select'] = mosHTML::radioList($yesno, 'available_for_rent_show_select', 'class="inputbox"', $vehiclemanager_configuration['available_for_rent_show_select'], 'value', 'text');
    //********end add option show available for rent in search page **********
    
    //******** add option show mandatory year of issue field on global page  **********
    $lists['mandatory_year_issue_field_require'] = mosHTML::radioList($yesno, 'mandatory_year_issue_field_require', 'class="inputbox"', $vehiclemanager_configuration['mandatory_year_issue_field_require'], 'value', 'text');
    //******** end add option show mandatory year of issue field on global page  **********
    //******** add option show mandatory mile age field on global page  **********
    $lists['mandatory_mileage_field_require'] = mosHTML::radioList($yesno, 'mandatory_mileage_field_require', 'class="inputbox"', $vehiclemanager_configuration['mandatory_mileage_field_require'], 'value', 'text');
    //******** end add option show mandatory mile age field on global page  **********
    //******** add option show mandatory price field on global page  **********
    $lists['mandatory_price_field_require'] = mosHTML::radioList($yesno, 'mandatory_price_field_require', 'class="inputbox"', $vehiclemanager_configuration['mandatory_price_field_require'], 'value', 'text');
    //******** end add option show mandatory price field on global page  **********
    //******** add option show country region city as text field on frontend setting page **********
    $lists['show_country_region_city_as_text_field'] = mosHTML::radioList($yesno, 'show_country_region_city_as_text_field', 'class="inputbox"', $vehiclemanager_configuration['show_country_region_city_as_text_field'], 'value', 'text');
    //******** end add option show country region city as text field on frontend setting page **********
    //06.09.17
    //******** add option row vehicle lg resolution **********
    $lists['veh_data_columns_lg'] = mosHTML::selectList($veh_columns_lg, 'veh_data_columns_lg', 'size="1"', 'value', 'text', $vehiclemanager_configuration['veh_data_columns_lg']);
    //********end add option row vehicle lg resolution **********
    //******** add option row vehicle md resolution **********
    $lists['veh_data_columns_md'] = mosHTML::selectList($veh_columns_md, 'veh_data_columns_md', 'size="1"', 'value', 'text', $vehiclemanager_configuration['veh_data_columns_md']);
    //********end add option row vehicle md resolution **********
    //******** add option row vehicle sm resolution **********
    $lists['veh_data_columns_sm'] = mosHTML::selectList($veh_columns_sm, 'veh_data_columns_sm', 'size="1"', 'value', 'text', $vehiclemanager_configuration['veh_data_columns_sm']);
    //********end add option row vehicle sm resolution **********
    ////******** add option row vehicle xs resolution **********
    $lists['veh_data_columns_xs'] = mosHTML::selectList($veh_columns_xs, 'veh_data_columns_xs', 'size="1"', 'value', 'text', $vehiclemanager_configuration['veh_data_columns_xs']);
    //********end add option row vehicle xs resolution **********

    $f = array();

    $s = explode(',', $vehiclemanager_configuration['plugin_name_select']);
    for ($i = 0; $i < count($s); $i++)
        $f[] = mosHTML::makeOption($s[$i]);

    $plugin_name_select = array();
    $plugin_name_mass = getSelect();
    $selecter_plugin_name = array();

    if(!$plugin_name_mass){
        $lists['plugin_name_select'] = "You must get public plugin group payment";
    }else{
        //$plugin_name_select[] = mosHTML::makeOption('select', "Select plugin");
        foreach ($plugin_name_mass as $value) {


            if(isset($vehiclemanager_configuration['plugin_name_select']) && !empty($vehiclemanager_configuration['plugin_name_select']) && in_array($value, $s)){
                $selecter_plugin_name[] = $value;
            }
            $plugin_name_select[] = JHtml::_('select.option', "$value", "$value");
        }

        $lists['plugin_name_select'] = mosHTML::selectList($plugin_name_select, 'plugin_name_select[]', 'multiple="multiple" size="' .count($plugin_name_mass) .'"', 'value', 'text', $selecter_plugin_name);
    }
    HTML_vehiclemanager::showConfiguration($lists, $option, $txt);
  }

    function getSearchLayouts(){
      global $vehiclemanager_configuration;
      $all_categories_layout = getLayoutsVeh('com_vehiclemanager','show_search_vehicle');
      $layouts = Array();
      //$layouts[] = JHtml::_('select.option', '', 'default');
      foreach($all_categories_layout as $value){
          $layouts[] = JHtml::_('select.option', "$value", "$value");
      }

      return mosHTML::selectList($layouts, 'default_search_layout', 'size="1"', 'value', 'text', $vehiclemanager_configuration['default_search_layout']);
    }

  function getSelect(){
    $db = JFactory::getDBO();
    $condtion = array(0 => '\'payment\'');
    $condtionatype = join(',',$condtion);

    if(JVERSION >= '1.6.0')
    {
      $query = "SELECT extension_id as id,name,element,enabled as published
            FROM #__extensions
            WHERE folder in ($condtionatype) AND enabled=1";
    }
    else
    {
      $query = "SELECT id,name,element,published
            FROM #__plugins
            WHERE folder in ($condtionatype) AND published=1";
    }
    $db->setQuery($query);
    $gatewayplugin = $db->loadobjectList();
    if(!getNamePluginForValSelect($gatewayplugin)){

    }else{
        return getNamePluginForValSelect($gatewayplugin);
    }

  }

  function getNamePluginForValSelect($gatewayplugin){

          $new_plugin_name = array();

          $retr = count($gatewayplugin);

           if($retr>0){

                for($i=0;$i<$retr;$i++){
                      $plugin_name_strtolower = mb_strtolower(trim($gatewayplugin[$i]->name));
                      $plugin_name_mass = explode(" ", $plugin_name_strtolower);
                      $lenght_mass = count($plugin_name_mass);
                      array_push($new_plugin_name,$plugin_name_mass[$lenght_mass-1]);
                }
                return $new_plugin_name;

          }
          else{
                return false;
          }

      }

  //****************   begin for manage reviews   *******************
  function manage_review_s($option, $sorting)
  {
    global $database, $mainframe, $mosConfig_list_limit;

    $limit = $mainframe->getUserStateFromRequest("viewlistlimit", 'limit', $mosConfig_list_limit);
    $limitstart = $mainframe->getUserStateFromRequest("view{$option}limitstart", 'limitstart', 0);

    $database->setQuery("SELECT count(*) FROM #__vehiclemanager_review;");
    $total = $database->loadResult();

    $pageNav = new JPagination($total, $limitstart, $limit); 
  //********************   begin request for reviews manager   **********************
    //if for sorting
    if ($sorting != "")
    {
        $request_string = "SELECT b.id as fk_vehicleid, a.id as review_id, b.vtitle as vehicle_title, " .
                " GROUP_CONCAT(c.title SEPARATOR ', ') as title_catigory, a.title as title_review, a.comment," .
                " a.user_name, a.date, a.rating,  a.published as published " .
                " FROM #__vehiclemanager_review as a, #__vehiclemanager_vehicles as b, #__vehiclemanager_main_categories as c, " .
                " #__vehiclemanager_categories as vc " .
                " WHERE a.fk_vehicleid = b.id AND vc.iditem = b.id and c.id = vc.idcat " .
                " GROUP BY a.id " .
                " ORDER by " . $sorting .
                " LIMIT $pageNav->limitstart,$pageNav->limit;";
        $database->setQuery($request_string);
        $reviews = $database->loadObjectList();
    } else
    {
        $request_string = "SELECT b.id as fk_vehicleid, a.id as review_id, b.vtitle as vehicle_title, " .
                " GROUP_CONCAT(c.title SEPARATOR ', ') as title_catigory, a.title as title_review, " .
                " a.comment, a.user_name, a.date, a.rating,  a.published" .
                " FROM #__vehiclemanager_review as a, #__vehiclemanager_vehicles as b, #__vehiclemanager_main_categories as c, " .
                " #__vehiclemanager_categories as vc " .
                " WHERE a.fk_vehicleid = b.id AND vc.iditem = b.id and c.id = vc.idcat " .
                " GROUP BY a.id " .
                " ORDER by date " .
                " LIMIT $pageNav->limitstart,$pageNav->limit;";
        $database->setQuery($request_string);
        $reviews = $database->loadObjectList();
    }

  //**************   end request for reviews manager   ***************************
    HTML_vehiclemanager::showManageReviews($option, $pageNav, $reviews);
  }

  //*********************   end for manage reviews   ****************************

  function publish_manage_review($vid, $publish, $option)
  {
    global $database;

    $database->setQuery("UPDATE #__vehiclemanager_review SET published = $publish WHERE id  = $vid ");
    $database->execute();

    mosRedirect("index.php?option=$option&task=manage_review");
  }

  function import($option){
    global $database, $my;
    $file = file($_FILES['import_file']['tmp_name']);
    $catid = mosGetParam($_POST, 'import_catid', array(0));
  //***********************   begin add for XML format   ***************************************
    $type = mosGetParam($_POST, 'import_type');
    switch ($type) {
      //CSV=='1' XML=='2'
      case '1':
          $retVal = mosVehicleManagerImportExport::importVehiclesCSV($file, $catid);
          HTML_vehiclemanager::showImportResult($retVal, $option);
          break;

      case '2':
          $retVal = mosVehicleManagerImportExport::importVehiclesXML($_FILES['import_file']['tmp_name'], $catid);
          HTML_vehiclemanager::showImportResult($retVal, $option);
          break;

      case '4':
          $retVal = mosVehicleManagerImportExport::importVehiclesXML($_FILES['import_file']['tmp_name'], null);
          HTML_vehiclemanager::showImportResult($retVal, $option);
          break;
  //***********************   end add for XML format   *****************************************
    }
  }
  /******************************************************************************/


  function importExportVehicles($option)
  {
    global $database;

    // get list of categories
    $categories = array();

    $query = "SELECT  id ,name, parent_id as parent"
            . "\n FROM #__vehiclemanager_main_categories"
            . "\n WHERE section='com_vehiclemanager'"
            . "\n AND published > 0"
            . "\n ORDER BY parent_id, ordering";

    $database->setQuery($query);
    $rows = $database->loadObjectList();


    // establish the hierarchy of the categories
    $children = array();
    // first pass - collect children
    foreach ($rows as $v) {
        $pt = $v->parent;
        $list = @$children[$pt] ? $children[$pt] : array();
        array_push($list, $v);
        $children[$pt] = $list;
    }

    // second pass - get an indent list of the items
    $list = vmLittleThings::vehicleManagerTreeRecurse(0, '', array(), $children);

    foreach ($list as $i => $item) {
        $item->text = $item->treename;
        $item->value = $item->id;
        $list[$i] = $item;
    }


    $categories = array_merge($categories, $list);

    /* if (count($categories) <= 1) {
      mosRedirect("index.php?option=com_vehiclemanager&section=categories",
      _VEHICLE_MANAGER_ADMIN_IMPEXP_ADD);
      } */
    $impclist = mosHTML::selectList($categories, 'import_catid[]', 'class="inputbox"  multiple id="import_catid"', 'value', 'text', 0);
    $expclist = mosHTML::selectList($categories, 'export_catid[]', 'class="inputbox"  multiple id="export_catid"', 'value', 'text', 0);

    $params = array();
    $params['import']['category'] = $impclist;
    $params['export']['category'] = $expclist;

    $importtypes[0] = mosHTML::makeOption('0', _VEHICLE_MANAGER_ADMIN_PLEASE_SEL);
    $importtypes[1] = mosHTML::makeOption('1', _VEHICLE_MANAGER_ADMIN_FORMAT_CSV);
    $importtypes[2] = mosHTML::makeOption('2', _VEHICLE_MANAGER_ADMIN_FORMAT_XML);
    $importtypes[4] = mosHTML::makeOption('4', _VEHICLE_MANAGER_ADMIN_FULL_XML);

    $params['import']['type'] = mosHTML::selectList($importtypes, 'import_type', 'id="import_type" class="inputbox" size="1" onchange = "impch();"', 'value', 'text', 0);

    $exporttypes[0] = mosHTML::makeOption('0', _VEHICLE_MANAGER_ADMIN_PLEASE_SEL);
    $exporttypes[1] = mosHTML::makeOption('1', _VEHICLE_MANAGER_ADMIN_FORMAT_CSV);
    $exporttypes[2] = mosHTML::makeOption('2', _VEHICLE_MANAGER_ADMIN_FORMAT_XML);
    $exporttypes[4] = mosHTML::makeOption('4', _VEHICLE_MANAGER_ADMIN_FULL_XML);

    $params['export']['type'] = mosHTML::selectList($exporttypes, 'export_type', 'id="export_type" class="inputbox" size="1" onchange="expch();"', 'value', 'text', 0);

    HTML_vehiclemanager::showImportExportVehicles($params, $option);
  }

  function showFeaturedManager($option)
  {
    global $database, $mainframe, $mosConfig_list_limit, $menutype;

    $section = "com_vehiclemanager";

    $query = "SELECT * FROM #__vehiclemanager_feature";
    $database->setQuery($query);
    $features = $database->loadObjectList();

    $sectionid = $mainframe->getUserStateFromRequest("sectionid{$section}{$section}", 'sectionid', 0);
    $limit = $mainframe->getUserStateFromRequest("viewlistlimit", 'limit', $mosConfig_list_limit);
    $limitstart = $mainframe->getUserStateFromRequest("view{$section}limitstart", 'limitstart', 0);
    $levellimit = $mainframe->getUserStateFromRequest("view{$option}limit$menutype", 'levellimit', 10);

    $total = count($features);

    $pageNav = new JPagination($total, $limitstart, $limit); 

    $features = array_slice($features, $pageNav->limitstart, $pageNav->limit);

    HTML_vehiclemanager::showFeaturedManager($features, $pageNav);
  }

  function editFeaturedManager($section = '', $uid = 0)
  {
    global $database, $my, $acl, $vehiclemanager_configuration;
    global $mosConfig_absolute_path, $mosConfig_live_site;

    $row = new mosVehicleManager_feature($database); 
    // load the row from the db table
    $row->load($uid);

    // build the html radio buttons for published
    $lists['published'] = mosHTML::yesnoRadioList('published', 'class="inputbox"', $row->published);


    //Select list for number of doors

    $select_value=mosHtml::makeOption($row->categories,$row->categories)->value; // select value
    $categories[] = mosHtml::makeOption("", _VEHICLE_MANAGER_OPTION_SELECT);
    if($vehiclemanager_configuration['featuredmanager']['placeholder']!='')
        $categ = explode(',', $vehiclemanager_configuration['featuredmanager']['placeholder']);
    else
        $categ = array();
    if (isset($row->categories)and !in_array($select_value,$categ))
        $categories[] = mosHtml::makeOption($row->categories, $row->categories);
    for ($i = 0; $i < count($categ); $i++)
        $categories[] = mosHtml::makeOption($categ[$i], $categ[$i]);
    $lists['categories'] = mosHTML::selectList($categories, 'categories', 'class="inputbox" size="1"', 'value', 'text', $row->categories);

    HTML_vehiclemanager::editFeaturedManager($row, $lists);
  }

  function saveFeaturedManager()
  {
    global $database, $mosConfig_absolute_path;

    global $vehiclemanager_configuration;
    

    $row = new mosVehicleManager_feature($database);
    $post = JFactory::getApplication()->input->getArray($_POST);

    $idd = $_POST['id'];

    if (array_key_exists("del_main_photo", $_POST) && $idd)
    {
        $del_main_photo = $_POST['del_main_photo'];
        if ($del_main_photo != '')
        {
            $database->setQuery("SELECT image_link FROM #__vehiclemanager_feature WHERE id=$idd");
            $image_link = $database->loadResult();
            $database->setQuery("UPDATE #__vehiclemanager_feature SET image_link='' WHERE id=$idd");
            $database->execute() ;

            unlink($mosConfig_absolute_path . '/components/com_vehiclemanager/featured_ico/' . $image_link);
        }
    }
    //save main image
    if ($_FILES['image_link']['name'] != '')
    {
        $uploaddir = $mosConfig_absolute_path . '/components/com_vehiclemanager/featured_ico/';
        $code = guid();
        $uploadfile = $uploaddir . $code . "_" . $_FILES['image_link']['name'];
        $file_name = $code . "_" . $_FILES['image_link']['name'];
        if (copy($_FILES['image_link']['tmp_name'], $uploadfile))
        {
            if($idd){
                $database->setQuery("UPDATE #__vehiclemanager_feature SET image_link='$file_name' WHERE id=$idd");
                $database->execute();
            }else{
                $row->image_link = $file_name;
            }

        }
    }
    $row->bind($post) ;

    if (!$row->check())
    {
        return;
    }

    $row->store();
    
    // add new features constant
    $query = "insert into #__vehiclemanager_const ( `const`,`sys_type`) values('_VEHICLE_MANAGER_FEATURE".$row->id."','Features')";
    $database->setQuery($query);
    $database->execute();
    $const_id = $database->insertid();

    $query = "select * from #__vehiclemanager_languages";
    $database->setQuery($query);
    $defined_languages = $database->loadobjectList();
    foreach ($defined_languages as $defined_language) {
        $query = "insert into #__vehiclemanager_const_languages ( `fk_constid`,`fk_languagesid`,`value_const`) values(".$const_id.",".$defined_language->id .",".$database->Quote($row->name).")";
        $database->setQuery($query);
        $database->execute();
    }
    vm_add_new_features_constant();

    // Add default options ('3') for advanced search layout fields on front-end in admin.vehiclemanager.class.conf.php
    $vehiclemanager_configuration['search_form_features'][$row->id]='3';
    mosVehicleManagerOthers::setParams();
    
    mosRedirect('index.php?option=com_vehiclemanager&section=featured_manager');
  }

  function cancelFeaturedManager()
  {
    global $database;
    $row = new mosVehicleManager_feature($database); 
    $row->bind($_POST);
    mosRedirect('index.php?option=com_vehiclemanager&section=featured_manager');
  }

  function removeFeaturedManager($section, $fids)
  {
    global $database;

    if (count($fids) < 1)
    {
        echo "<script> alert('Select a feature to delete'); window.history.go(-1);</script>\n";
        exit;
    }

    foreach ($fids as $fid){
        removeFeaturedManagerFromDB($fid);
    }

    mosRedirect('index.php?option=com_vehiclemanager&section=featured_manager');

  }

  function removeFeaturedManagerFromDB($fid)
  {
    global $database, $my, $mosConfig_absolute_path;

    $database->setQuery("SELECT image_link FROM #__vehiclemanager_feature WHERE id=$fid");
    $image_link = $database->loadResult();
    unlink($mosConfig_absolute_path . '/components/com_vehiclemanager/featured_ico/' . $image_link);

    $sql = "DELETE FROM #__vehiclemanager_feature WHERE id = $fid ";
    $database->setQuery($sql);
    $database->execute();

    $sql = "DELETE FROM #__vehiclemanager_feature_vehicles WHERE fk_featureid = $fid ";
    $database->setQuery($sql);
    $database->execute();

    //delete features laguages constant
    $query = "select * from #__vehiclemanager_const where `const` = '_VEHICLE_MANAGER_FEATURE".$fid."'";
    $database->setQuery($query);
    $vehiclemanager_consts = $database->loadobjectList();
    foreach ($vehiclemanager_consts as $vehiclemanager_const) {
        $query = "DELETE FROM #__vehiclemanager_const_languages where `fk_constid` = ".$vehiclemanager_const->id ;
        $database->setQuery($query);
        $database->execute();
    }

    //delete features constant
    $query = "DELETE FROM #__vehiclemanager_const where `const` = '_VEHICLE_MANAGER_FEATURE".$fid."'";
    $database->setQuery($query);
    $database->execute();

  }

  function publishFeaturedManager($section, $featureid = null, $cid = null, $publish = 1)
  {
    global $database, $my;

    if (!is_array($cid))
        $cid = array();
    if ($featureid)
        $cid[] = $featureid;

    if (count($cid) < 1)
    {
        $action = $publish ? _PUBLISH : _DML_UNPUBLISH;
        echo "<script> alert('" . _DML_SELECTCATTO . " $action'); window.history.go(-1);</script>\n";
        exit;
    }

    $cids = implode(',', $cid);

    $query = "UPDATE #__vehiclemanager_feature SET published='$publish'"
            . "\nWHERE id IN ($cids)";
    $database->setQuery($query);
    $database->execute();

    if (count($cid) == 1)
    {
        $row = new mosVehicleManager_feature($database); 
        $row->checkin($cid[0]);
    }
    mosRedirect('index.php?option=com_vehiclemanager&section=featured_manager');
  }

  function showLanguageManager($option)
  {
    global $database, $mainframe, $mosConfig_list_limit, $menutype, $mosConfig_absolute_path;


    $section = "com_vehiclemanager";

    $search['const'] = mosGetParam($_POST, 'search_const', '');
    $search['const_value'] = mosGetParam($_POST, 'search_const_value', '');
    $search['languages'] = $mainframe->getUserStateFromRequest("search_languages{$option}", 'search_languages', '');
    $search['sys_type']  = $mainframe->getUserStateFromRequest("search_sys_type{$option}", 'search_sys_type', '');


    $where_query = array();
    if ($search['const'] != '')
        $where_query[] = "c.const LIKE '%" . $search['const'] . "%'";
    if ($search['const_value'] != '')
        $where_query[] = "cl.value_const LIKE '%" . $search['const_value'] . "%'";
    if ($search['languages'] != '')
        $where_query[] = "cl.fk_languagesid = " .$database->quote( $search['languages']) . " ";
    if ($search['sys_type'] != '')
        $where_query[] = "c.sys_type LIKE '" . $search['sys_type'] . "'";

    $where = "";
    $i = 0;
    if (count($where_query) > 0)
        $where = "WHERE ";
    foreach ($where_query as $item) {
        if ($i == 0)
            $where .= "( $item ) ";
        else
            $where .= "AND ( $item ) ";
        $i++;
    }
    $limit = $mainframe->getUserStateFromRequest("viewlistlimit", 'limit', $mosConfig_list_limit);
    $limitstart = $mainframe->getUserStateFromRequest("view{$section}limitstart", 'limitstart', 0);
    $levellimit = $mainframe->getUserStateFromRequest("view{$option}limit$menutype", 'levellimit', 10);

    $query = "SELECT count(cl.id) ";
    $query .= "FROM #__vehiclemanager_const_languages as cl ";
    $query .= "LEFT JOIN #__vehiclemanager_languages AS l ON cl.fk_languagesid=l.id ";
    $query .= "LEFT JOIN #__vehiclemanager_const AS c ON cl.fk_constid=c.id $where";

    $database->setQuery($query);
    $total = $database->loadResult();
    $pageNav = new JPagination($total, $limitstart, $limit); 

    $query = "SELECT cl.id, cl.value_const, c.sys_type, l.title, c.const ";
    $query .= " FROM #__vehiclemanager_const_languages as cl ";
    $query .= " LEFT JOIN #__vehiclemanager_languages AS l ON cl.fk_languagesid=l.id ";
    $query .= " LEFT JOIN #__vehiclemanager_const AS c ON cl.fk_constid=c.id $where";
    $query .= " LIMIT $pageNav->limitstart,$pageNav->limit";
    $database->setQuery($query);
    $const_languages = $database->loadObjectList();

    $sectionid = $mainframe->getUserStateFromRequest("sectionid{$section}{$section}", 'sectionid', 0);


  //print_r($total);exit;

  //print_r($pageNav);exit;
    //$const_languages = array_slice($const_languages, $pageNav->limitstart, $pageNav->limit);

    $query = "SELECT sys_type FROM #__vehiclemanager_const GROUP BY sys_type";
    $database->setQuery($query);
    $sys_types = $database->loadObjectList();

    $sys_type_row[] = mosHTML::makeOption('', '--Select sys type--');
    foreach ($sys_types as $sys_type) {
        $sys_type_row[] = mosHTML::makeOption($sys_type->sys_type, $sys_type->sys_type);
    }

    $search['sys_type'] = mosHTML::selectList($sys_type_row, 'search_sys_type', 'class="inputbox input-medium" size="1" onchange="document.adminForm.submit();"', 'value', 'text', $search['sys_type']);

    $query = "SELECT id, title FROM #__vehiclemanager_languages";
    $database->setQuery($query);
    $languages = $database->loadObjectList();

    $languages_row[] = mosHTML::makeOption('', '--Select language--');
    foreach ($languages as $language) {
        $languages_row[] = mosHTML::makeOption($language->id, $language->title);
    }

    $search['languages'] = mosHTML::selectList($languages_row, 'search_languages', 'class="inputbox input-medium" size="1" onchange="document.adminForm.submit();"', 'value', 'text', $search['languages']);


    if(protectInjectionWithoutQuote('task', '', 'STRING') == 'loadLang'){
        vmLittleThings::loadConstVechicle();
        vmLittleThings::language_check();
    }


    HTML_vehiclemanager::showLanguageManager($const_languages, $pageNav, $search);
  }

  function editLanguageManager($section = '', $uid = 0)
  {
    global $database, $my, $acl, $vehiclemanager_configuration;
    global $mosConfig_absolute_path, $mosConfig_live_site;

    $row = new mosVehicleManager_language($database); 
    // load the row from the db table
    $row->load($uid);

    $query = "SELECT * FROM #__vehiclemanager_const WHERE id = " . $row->fk_constid;
    $database->setQuery($query);
    $const = $database->loadObject();

    $lists['const'] = $const->const;
    $lists['sys_type'] = $const->sys_type;

    $query = "SELECT title FROM #__vehiclemanager_languages WHERE id = " . $row->fk_languagesid;
    $database->setQuery($query);
    $language = $database->loadResult();

    $lists['languages'] = $language;

    HTML_vehiclemanager::editLanguageManager($row, $lists);
  }

  function saveLanguageManager()
  {
    global $database, $mosConfig_absolute_path;

    $row = new mosVehicleManager_language($database); 

    $post = JFactory::getApplication()->input->post->getArray(array(), null, 'raw');    
    $row->bind($post);

    if (!$row->check())
    {
        return;
    }

    $row->store();

    mosRedirect('index.php?option=com_vehiclemanager&section=language_manager');
  }

  function cancelLanguageManager()
  {
    global $database, $mosConfig_absolute_path;

    $row = new mosVehicleManager_feature($database); 
    $row->bind($_POST);
    mosRedirect('index.php?option=com_vehiclemanager&section=language_manager');
  }

  function save_featured_category($option)
  {
        global $vehiclemanager_configuration, $database;

        if(trim($vehiclemanager_configuration['featuredmanager']['placeholder']) !== trim(mosGetParam($_POST, 'featuredmanager_placeholder', "")) ) {
            $vehiclemanager_configuration['featuredmanager']['placeholder'] = mosGetParam($_POST, 'featuredmanager_placeholder', "");
            if( trim($vehiclemanager_configuration['featuredmanager']['placeholder']) !== "" )
            {
                vm_add_new_features_category_constant();
            }
            mosVehicleManagerOthers::setParams();
        }
    }

  function orders($option) {
    global $database, $my, $user_configuration, $acl, $mosConfig_live_site, $mosConfig_absolute_path;
    global $mainframe, $mosConfig_list_limit;
    $search = '';
    $order = 'ORDER BY o.id  DESC';
    $where = '';
    if(isset($_REQUEST['search'])) {
        $search = $_REQUEST['search'];
        $where = "WHERE o.usr_email LIKE '%{$search}%' OR o.usr_name LIKE '%{$search}%'";
    }
    if(isset($_GET['orderby']) && $_GET['orderby'] == 'user') {
        $order = 'ORDER BY o.usr_name';
    }elseif(isset($_GET['orderby']) && $_GET['orderby'] == 'email') {
        $order = 'ORDER BY o.usr_email';
    }elseif(isset($_GET['orderby']) && $_GET['orderby'] == 'status') {
        $order = "ORDER BY o.status = 'Completed' DESC";
    }elseif(isset($_GET['orderby']) && $_GET['orderby'] == 'data') {
        $order = "ORDER BY o.data <> 'NULL' DESC";
    }elseif(isset($_GET['orderby']) && $_GET['orderby'] == 'id') {
        $order = "ORDER BY o.id  ASC";
    }

    $limit = $mainframe->getUserStateFromRequest("viewlistlimit", 'limit', $mosConfig_list_limit);
    $limitstart = $mainframe->getUserStateFromRequest("view{$option}limitstart", 'limitstart', 0);
    if(isset($_REQUEST['order_details'])){
        if(!$order && !$where)
        $order = "ORDER BY o.order_date  DESC";
        if(isset($_GET['orderby']) && $_GET['orderby'] == 'order_date') {
            $order = "ORDER BY o.order_date  ASC";
        }
         if($where)
            $where = "WHERE o.usr_email LIKE '%{$search}%' OR o.usr_name LIKE '%{$search}%'
                        AND fk_order_id = ".$_REQUEST['order_id']."";
        else
            $where = "WHERE fk_order_id = ".$_REQUEST['order_id']."";
        $sql = "SELECT count(*)  ".
                " FROM #__vehiclemanager_orders_details AS o ".
                " LEFT JOIN #__users AS u ".
                " ON o.fk_user_id = u.id ".
                " LEFT JOIN #__vehiclemanager_vehicles AS g ".
                " ON o.fk_vehicle_id = g.id ".
                " LEFT JOIN #__vehiclemanager_orders AS veho ".
                " ON veho.id = o.fk_order_id ".
                 $where." ".$order ;
        $database->setQuery($sql);
        $total = $database->loadResult();
        $pageNav = new JPagination($total, $limitstart, $limit);
        $sql = "SELECT u.username, ".
                        "o.*, ".
                    "g.price, g.priceunit".
                " FROM #__vehiclemanager_orders_details AS o ".
                " LEFT JOIN #__users AS u ".
                " ON o.fk_user_id = u.id ".
                " LEFT JOIN #__vehiclemanager_vehicles AS g ".
                " ON o.fk_vehicle_id = g.id ".
                " LEFT JOIN #__vehiclemanager_orders AS veho ".
                " ON veho.id = o.fk_order_id ".
                 $where." ".$order. " LIMIT " . $pageNav->limitstart." , ". $pageNav->limit;
        $database->setQuery($sql);
        $orders = $database->loadobjectList();

        HTML_vehiclemanager::orders_details($orders, $search, $pageNav);
    }else{
      $sql = "SELECT count(*)  ".
            " FROM #__vehiclemanager_orders as o ".
            " LEFT JOIN #__users as u ".
            " ON o.fk_user_id = u.id ".
            " LEFT JOIN #__vehiclemanager_vehicles AS g ".
            " ON o.fk_vehicle_id = g.id ". $where." ".$order;
      $database->setQuery($sql);
      $total = $database->loadResult();
      $pageNav = new JPagination($total, $limitstart, $limit);
      $sql = "SELECT u.username, ".
                    "o.*,".
                    "g.price as g_price, g.priceunit as g_price_unit".
            " FROM #__vehiclemanager_orders AS o ".
            " LEFT JOIN #__users AS u ".
            " ON o.fk_user_id = u.id ".
            " LEFT JOIN #__vehiclemanager_vehicles AS g ".
            " ON o.fk_vehicle_id = g.id ". $where." ".$order
            . " LIMIT " . $pageNav->limitstart." , ". $pageNav->limit;
      $database->setQuery($sql);
      $orders = $database->loadobjectList();
      HTML_vehiclemanager::orders($orders, $search, $pageNav);
    }
  }
  function updateOrderStatus() {
  global $database;
  $orderId = $_POST['cb'];
  $status = $_POST['order_status'];
  $status = $status[$orderId[0]];
  $option = $_POST['option'];
  $sql = "UPDATE #__vehiclemanager_orders SET status = '".$status."' WHERE id = ".$orderId[0]."";
  $database->setQuery($sql);
  $database->execute();

  $sql = "SELECT * FROM #__vehiclemanager_orders WHERE id = ".$orderId[0]."";
  $database->setQuery($sql);
  $order = $database->loadobjectList();

  $order = $order['0'];
  $order->txn_type = 'Order status changed (set:'.$status.') by the administrator';
  $sql = "INSERT INTO `#__vehiclemanager_orders_details`(fk_order_id,fk_user_id,usr_email,usr_name,
                                                          fk_vehicle_vtitle,
                                                          status,order_date,fk_vehicle_id,
                                                          txn_type,txn_id,payer_id,payer_status,
                                                          order_calculated_price)
          VALUES ('".$orderId[0]."',
          '".$order->fk_user_id."',
          '".$order->usr_email."',
          '".$order->usr_name."',
          '".$order->fk_vehicle_vtitle."',
          '".$order->status."',
          now(),
          '".$order->fk_vehicle_id."',
          '".$order->txn_type."',
          '".$order->txn_id."',
          '".$order->payer_id."',
          '".$order->payer_status."',
          '".$order->order_calculated_price."')";
      $database->setQuery($sql);
      $database->execute();

      mosRedirect("index.php?option=$option&task=orders");
  }

  function deleteOrder() {

      global $database;

        $orderIds = $_POST['cb'];
        $option = $_POST['option'];
        foreach($orderIds as $key=>$orderId){
            $sql = "DELETE FROM #__vehiclemanager_orders WHERE id = ".$orderId." ";
            $database->setQuery($sql);
            $database->execute();
            $sql = "DELETE FROM #__vehiclemanager_orders_details WHERE fk_order_id = ".$orderId." ";
            $database->setQuery($sql);
            $database->execute();
        }

        mosRedirect("index.php?option=$option&task=orders");
  }


  function rent_history($option, $vid){
    global $database, $my,$mainframe;
    if (!is_array($vid) || count($vid) !== 1){
      echo "<script> alert('Select one item to show'); window.history.go(-1);</script>\n";
      exit;
    }
    $vid_veh = implode(',', $vid);
    $select = "SELECT a.*, cc.name AS category, l.id as rentid, l.rent_from as rent_from, " .
            "l.rent_return as rent_return, l.rent_until as rent_until, " .
            "l.user_name as user_name, l.user_email as user_email " .
            "\nFROM #__vehiclemanager_vehicles AS a" .
            "\nLEFT JOIN #__vehiclemanager_main_categories AS cc ON cc.id = a.catid" .
            "\nLEFT JOIN #__vehiclemanager_rent AS l ON l.id = a.fk_rentid" .
            "\nWHERE a.id = $vid_veh";

    $database->setQuery($select);
    $vehicle = $database->loadObject();
    if($vehicle->listing_type=='2'){
        echo "<script> alert('This vehicle is not for rent'); window.history.go(-1);</script>\n";
        exit;
    }
    $vids = implode(',', $vid);
    $vids = getAssociateVehicle($vids);
    $vehicles_assoc[]= $vehicle;
    if($vids){
      $select = "SELECT a.*, cc.name AS category, l.id as rentid, l.rent_from as rent_from, " .
              "l.rent_return as rent_return, l.rent_until as rent_until, " .
              "l.user_name as user_name, l.user_email as user_email " .
              "\nFROM #__vehiclemanager_vehicles AS a" .
              "\nLEFT JOIN #__vehiclemanager_main_categories AS cc ON cc.id = a.catid" .
              "\nLEFT JOIN #__vehiclemanager_rent AS l ON l.id = a.fk_rentid" .
              "\nWHERE a.id in ($vids)";

      $database->setQuery($select);
      $vehicles_assoc = $database->loadObjectList();

      //for rent or not
      $count = count($vehicles_assoc);
      for ($i = 0; $i < $count; $i++) {
        if ($vehicles_assoc[$i]->listing_type != 1){
          ?>
          <script type = "text/JavaScript" language = "JavaScript">
              alert('This vehicle has associated vehicle not for rent');
              window.history.go(-1);
          </script>
          <?php
          exit;
        }
      }
    }
    // get list of categories
    HTML_vehiclemanager::showRentHistory($option, $vehicle, $vehicles_assoc,  $usermenu, "rent");
}

  function users_rent_history($option, $sorting){

    global $database, $my,$mainframe;
    $owner = $mainframe->getUserStateFromRequest("owner_h{$option}", 'owner_h', '-1'); //add nik
    $vehicle = '';
    $vehicles_assoc = array();
    $allrent = '';

    if($sorting==''){
        $sorting = 'rent_return';
    }

    if($owner !=-1){
        $select = "SELECT l.rent_from as rent_from, " .
                "a.vtitle, a.vehicleid,l.rent_return as rent_return, l.rent_until as rent_until, " .
                "l.user_name as user_name, l.user_email as user_email " .
                "\n FROM #__vehiclemanager_rent AS l" .
                "\n LEFT JOIN #__vehiclemanager_vehicles AS a ON l.fk_vehicleid = a.id" .
                "\n WHERE l.user_name = ".$database->Quote($owner).
                //"\n OR l.fk_userid = ".$database->Quote($owner) ." ORDER BY l.rent_return";
                "\n OR l.fk_userid = ".$database->Quote($owner) ." ORDER BY l." . $sorting;
        $database->setQuery($select);
        $allrent = $database->loadObjectList();
    }
    $userlist[] = mosHTML::makeOption('-1', 'Select User');
    $database->setQuery("SELECT DISTINCT fk_userid AS value, user_name AS text from #__vehiclemanager_rent ORDER BY user_name");
    $userlist = array_merge($userlist, $database->loadObjectList());
    foreach ($userlist as $value) {
        if(!$value->value)
            $value->value = $value->text;
    }
    $usermenu = mosHTML::selectList($userlist, 'owner_h', 'class="inputbox input-medium" size="1"
        onchange="document.adminForm.submit();"', 'value', 'text', $owner);

    HTML_vehiclemanager::showUsersRentHistory($option, $allrent, $usermenu);
}
