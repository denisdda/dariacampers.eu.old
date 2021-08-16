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

require_once ( JPATH_ROOT . "/components/com_vehiclemanager/functions.php" );


//*** Get language files
global $mosConfig_absolute_path, $mosConfig_lang, $database;

$mosConfig_absolute_path = $GLOBALS['mosConfig_absolute_path'];
require_once($mosConfig_absolute_path . "/administrator/components/com_vehiclemanager/menubar_ext.php");

class menucat
{

    static function NEW_CATEGORY(){
        mosMenuBarVehicle_ext::startTable();
        mosMenuBarVehicle_ext::save();
        mosMenuBarVehicle_ext::apply('apply', 'apply');
        mosMenuBarVehicle_ext::cancel();
        mosMenuBarVehicle_ext::spacer();
        mosMenuBarVehicle_ext::endTable();
    }

    static function EDIT_CATEGORY(){
        mosMenuBarVehicle_ext::startTable();
        mosMenuBarVehicle_ext::save();
        mosMenuBarVehicle_ext::apply('apply', 'apply');
        mosMenuBarVehicle_ext::cancel();
        mosMenuBarVehicle_ext::spacer();
        mosMenuBarVehicle_ext::endTable();
    }

    static function SHOW_CATEGORIES()
    {
        mosMenuBarVehicle_ext::startTable();
        mosMenuBarVehicle_ext::addNew();
        mosMenuBarVehicle_ext::editList();
        mosMenuBarVehicle_ext::publishList();
        mosMenuBarVehicle_ext::unpublishList();
        mosMenuBarVehicle_ext::deleteList();
        mosMenuBarVehicle_ext::spacer();
        mosMenuBarVehicle_ext::endTable();
    }

    static function DEFAULT_CATEGORIES()
    {
        mosMenuBarVehicle_ext::startTable();
         mosMenuBarVehicle_ext::addNew('new', 'Add');
        mosMenuBarVehicle_ext::editList();
        mosMenuBarVehicle_ext::publishList();
        mosMenuBarVehicle_ext::unpublishList();
        mosMenuBarVehicle_ext::deleteList();
        mosMenuBarVehicle_ext::endTable();
    }

}

class menufeaturedmanager
{

    static function NEW_FEATUREDMANAGER()
    {
        mosMenuBarVehicle_ext::startTable();
        mosMenuBarVehicle_ext::save();
        mosMenuBarVehicle_ext::cancel();
        mosMenuBarVehicle_ext::spacer();
        mosMenuBarVehicle_ext::endTable();
    }

    static function EDIT_FEATUREDMANAGER()
    {
        mosMenuBarVehicle_ext::startTable();
        mosMenuBarVehicle_ext::save();
        mosMenuBarVehicle_ext::cancel();
        mosMenuBarVehicle_ext::spacer();
        mosMenuBarVehicle_ext::endTable();
    }

    static function SHOW_FEATUREDMANAGER()
    {
        mosMenuBarVehicle_ext::startTable();
        mosMenuBarVehicle_ext::publishList();
        mosMenuBarVehicle_ext::unpublishList();
        mosMenuBarVehicle_ext::addNew();
        mosMenuBarVehicle_ext::editList();
        mosMenuBarVehicle_ext::deleteList();
        mosMenuBarVehicle_ext::spacer();
        mosMenuBarVehicle_ext::endTable();
    }

    static function MENU_FEATUREDMANAGER()
    {
        mosMenuBarVehicle_ext::startTable();
        mosMenuBarVehicle_ext::publishList();
        mosMenuBarVehicle_ext::unpublishList();
        mosMenuBarVehicle_ext::addNew('add', 'Add');
        mosMenuBarVehicle_ext::editList();
        mosMenuBarVehicle_ext::save('addFeature','Save category');
        mosMenuBarVehicle_ext::deleteList();
        mosMenuBarVehicle_ext::endTable();
    }

}

class menulanguagemanager
{

    static function EDIT_LANGUAGEMANAGER()
    {
        mosMenuBarVehicle_ext::startTable();
        mosMenuBarVehicle_ext::save();
        mosMenuBarVehicle_ext::cancel();
        mosMenuBarVehicle_ext::spacer();
        mosMenuBarVehicle_ext::endTable();
    }

    static function MENU_LANGUAGEMANAGER()
    {
        mosMenuBarVehicle_ext::startTable();
        mosMenuBarVehicle_ext::editList();
        mosMenuBarVehicle_ext::spacer();
        mosMenuBarVehicle_ext::endTable();
    }

}

class menuvehiclemanager
{

    static function MENU_NEW()
    {
        mosMenuBarVehicle_ext::startTable();
        mosMenuBarVehicle_ext::save();
        mosMenuBarVehicle_ext::cancel();
        mosMenuBarVehicle_ext::spacer();
        mosMenuBarVehicle_ext::endTable();
    }
    static function MENU_CLON()
    {
        mosMenuBarVehicle_ext::startTable();
        mosMenuBarVehicle_ext::save();
        mosMenuBarVehicle_ext::apply('apply', 'apply');
        mosMenuBarVehicle_ext::cancel();
        mosMenuBarVehicle_ext::spacer();
        mosMenuBarVehicle_ext::endTable();
    }

    static function MENU_EDIT()
    {
        mosMenuBarVehicle_ext::startTable();
        mosMenuBarVehicle_ext::save();
        mosMenuBarVehicle_ext::apply('apply', 'apply');
        //*******************  begin add for review edit  **********************
        mosMenuBarVehicle_ext::editList('edit_review', _VEHICLE_MANAGER_TOOLBAR_ADMIN_EDIT_REVIEW);
        mosMenuBarVehicle_ext::deleteList('', 'delete_review', _VEHICLE_MANAGER_TOOLBAR_ADMIN_DELETE_REVIEW);
        //*******************  end add for review edit  ************************

        mosMenuBarVehicle_ext::cancel();
        //mosMenuBar::help();
        mosMenuBarVehicle_ext::spacer();
        mosMenuBarVehicle_ext::endTable();
    }

    static function MENU_DELETE_REVIEW()
    {
        mosMenuBarVehicle_ext::startTable();
        mosMenuBarVehicle_ext::save();
        mosMenuBarVehicle_ext::spacer();
        mosMenuBarVehicle_ext::apply('apply', 'apply');
        mosMenuBarVehicle_ext::spacer();

        //*******************  begin add for review edit  **********************
        mosMenuBarVehicle_ext::editList('edit_review', _VEHICLE_MANAGER_TOOLBAR_ADMIN_EDIT_REVIEW);
        mosMenuBarVehicle_ext::deleteList('', 'delete_review', _VEHICLE_MANAGER_TOOLBAR_ADMIN_DELETE_REVIEW);
        //*******************  end add for review edit  ************************

        mosMenuBarVehicle_ext::spacer();
        mosMenuBarVehicle_ext::cancel();
        //mosMenuBar::help();
        mosMenuBarVehicle_ext::spacer();
        mosMenuBarVehicle_ext::endTable();
    }

    static function MENU_EDIT_REVIEW()
    {
        mosMenuBarVehicle_ext::startTable();
        mosMenuBarVehicle_ext::save('update_review');
        mosMenuBarVehicle_ext::cancel('cancel_review_edit');
        mosMenuBarVehicle_ext::spacer();
        mosMenuBarVehicle_ext::endTable();
    }

    static function MENU_CANCEL()
    {
        mosMenuBarVehicle_ext::startTable();
        mosMenuBarVehicle_ext::back();  //old valid  mosMenuBar::cancel();
        //mosMenuBar::help();
        mosMenuBarVehicle_ext::spacer();
        mosMenuBarVehicle_ext::endTable();
    }

    static function MENU_CONFIG()
    {
        mosMenuBarVehicle_ext::startTable();
        mosMenuBarVehicle_ext::save('config_save');
        //mosMenuBarVehicle_ext::cancel();
        //mosMenuBar::help();
        mosMenuBarVehicle_ext::spacer();
        mosMenuBarVehicle_ext::endTable();
    }

//**************   begin for manage reviews   *********************
    static function MENU_MANAGE_REVIEW()
    {
        mosMenuBarVehicle_ext::startTable();
        mosMenuBarVehicle_ext::editList('edit_manage_review', _VEHICLE_MANAGER_TOOLBAR_ADMIN_EDIT_REVIEW);
        mosMenuBarVehicle_ext::spacer();
        mosMenuBarVehicle_ext::deleteList('', 'delete_manage_review', _VEHICLE_MANAGER_TOOLBAR_ADMIN_DELETE_REVIEW);
        mosMenuBarVehicle_ext::endTable();
    }

    static function MENU_MANAGE_REVIEW_DELETE()
    {
        mosMenuBarVehicle_ext::startTable();
        mosMenuBarVehicle_ext::editList('edit_manage_review', _VEHICLE_MANAGER_TOOLBAR_ADMIN_EDIT_REVIEW);
        mosMenuBarVehicle_ext::spacer();
        mosMenuBarVehicle_ext::deleteList('', 'delete_manage_review', _VEHICLE_MANAGER_TOOLBAR_ADMIN_DELETE_REVIEW);
        mosMenuBarVehicle_ext::endTable();
    }

    static function MENU_MANAGE_REVIEW_EDIT()
    {
        mosMenuBarVehicle_ext::startTable();
        mosMenuBarVehicle_ext::save('update_edit_manage_review');
        mosMenuBarVehicle_ext::spacer();
        mosMenuBarVehicle_ext::cancel('cancel_edit_manage_review');
        mosMenuBarVehicle_ext::endTable();
    }

    static function MENU_MANAGE_REVIEW_EDIT_EDIT()
    {
        mosMenuBarVehicle_ext::startTable();
        mosMenuBarVehicle_ext::editList('edit_manage_review', _VEHICLE_MANAGER_TOOLBAR_ADMIN_EDIT_REVIEW);
        mosMenuBarVehicle_ext::spacer();
        mosMenuBarVehicle_ext::deleteList('', 'delete_manage_review', _VEHICLE_MANAGER_TOOLBAR_ADMIN_DELETE_REVIEW);
        mosMenuBarVehicle_ext::endTable();
    }

//**************   end for manage reviews   ***********************


    static function MENU_DEFAULT()
    {
        global $task,$mosConfig_absolute_path ;




        mosMenuBarVehicle_ext::startTable();
        mosMenuBarVehicle_ext::addNew();
        mosMenuBarVehicle_ext::publishList();
        mosMenuBarVehicle_ext::unpublishList();

        mosMenuBarVehicle_ext::spacer();
        mosMenuBarVehicle_ext::NewCustom('rent', 'adminForm', "../administrator/components/com_vehiclemanager/images/dm_lend.png", "../administrator/components/com_vehiclemanager/images/dm_lend_32.png", _VEHICLE_MANAGER_TOOLBAR_RENT_VEHICLES, _VEHICLE_MANAGER_TOOLBAR_ADMIN_RENT, true, 'adminForm');

        mosMenuBarVehicle_ext::NewCustom('rent_return', 'adminForm', "../administrator/components/com_vehiclemanager/images/dm_lend_return.png", "../administrator/components/com_vehiclemanager/images/dm_lend_return_32.png", _VEHICLE_MANAGER_TOOLBAR_RETURN_VEHICLE, _VEHICLE_MANAGER_TOOLBAR_ADMIN_RETURN, true, 'adminForm');
        mosMenuBarVehicle_ext::editList('edit_rent', _VEHICLE_MANAGER_TOOLBAR_ADMIN_EDIT_RENT);
        mosMenuBarVehicle_ext::editList('rent_history', _VEHICLE_MANAGER_TOOLBAR_ADMIN_RENT_HISTORY);
        mosMenuBarVehicle_ext::spacer();
        mosMenuBarVehicle_ext::deleteList();
        mosMenuBarVehicle_ext::spacer();

        //get update version for PR0
        if( checkVMVersionProFree() == "pro" ) {


            function checkVersion($newversion, $oldversion) {

                if (strpos($newversion, " ") !== false ) 
                    $newversion = explode('.', substr($newversion, 0, strpos($newversion, ' ')));
                else  $newversion = explode('.', $newversion);

                $oldversion = explode('.', substr($oldversion, 0, strpos($oldversion, ' ')));

                return $oldversion === max($newversion, $oldversion);
            } 

            {
                //check update
                $url="http://ordasoft.com/xml_update/vehiclemanager.xml";
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT ,0); 
                curl_setopt($ch, CURLOPT_TIMEOUT, 1);

                $data = curl_exec($ch);
                $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

                $ordasoft_version = "";
                $creationDate = "";
                if( !curl_errno($ch) && $code == 200 ){
        
                    $xml = simplexml_load_string($data);
                    $ordasoftNewV = (string)$xml->version;
                    $ordasoftCreationDate = (string)$xml->creationDate;
                    unset($xml);

                    $xml = simplexml_load_file($mosConfig_absolute_path . "/administrator/components/com_vehiclemanager/vehiclemanager.xml");
                    $ordasoft_version = (string)$xml->version;
                    $creationDate = (string)$xml->creationDate;

                    unset($xml);

                }
                curl_close($ch);

            }

            $avaibleUpdate = false ;
            if (!empty($ordasoftNewV) && !checkVersion($ordasoftNewV, $ordasoft_version)) {
                $avaibleUpdate = true ;
                $message = "Available new version $ordasoftNewV" . 
                ", creation date $ordasoftCreationDate";
                JFactory::getApplication()->enqueueMessage($message);
            }

            $update = 'unavaible';
            if( $avaibleUpdate ||  checkVMActivationNeed()  ) $update = 'avaible';
            JToolBarHelper::custom('about_version', 'update-'.$update, '', '', false);
        ?>
        
        <!-- about form  start-->
        <div id="ordasoft-notification">
            <p></p>
        </div>
        <div id="about-modal" class="ordasoft-dashboard-apps-dialog  ordasoft-dashboard-about modal hide fade">
          <div class="modal-body">
            <div class="about-image">
                <img src="<?php echo JURI::root().'components/com_vehiclemanager/images/vm_logo.png'?>" alt="vehiclemanager">
            </div>
          </div>
          <div class="modal-footer">
                <span class="span3">
                    <div class="about-col-1"><?php echo _VEHICLE_MANAGER_DOC_VERSION; ?></div><span id="ospackage-version" class="about-col-2"><?php echo $ordasoft_version.' Pro'?></span>
                </span>
                <span class="span3">
                    <div class="about-col-1"><?php echo _VEHICLE_MANAGER_DOC_RELEASE_DATE; ?></div><span id="ospackage-version" class="about-col-2"><?php echo $creationDate; ?></span>
                </span>
            <?php if(!checkVMActivationNeed() &&  $avaibleUpdate ){?>
                <span class="span3">
                        <div class="about-col-1 new-version"><?php echo _VEHICLE_MANAGER_NEW_VERSION_AVALIABLE; ?>: <span class="version-ospackage"><?php echo $ordasoftNewV; ?></span></div><a class="update-link dashboard-link-action" href="#" ><?php echo _VEHICLE_MANAGER_UPDATE; ?></a>
                </span>
            <?php }else if( $avaibleUpdate  ) {?>
                <span class="span3">
                        <div class="about-col-1 new-version"><?php echo _VEHICLE_MANAGER_NEW_VERSION_AVALIABLE; ?>:<span class="version-ospackage"><?php echo $ordasoftNewV; ?></span></div>
                </span>
            <?php }?>
                <span class="span3">

        <?php
                $need_activation = checkVMActivationNeed();

        ?>
        <link rel="stylesheet" type="text/css" href="<?php echo JUri::root(); ?>components/com_vehiclemanager/includes/os-about.css">

        <script type="text/javascript">
        var JUri = '<?php echo JUri::root(); ?>';
        </script>
        <script src="<?php echo JUri::root(); ?>components/com_vehiclemanager/includes/os-about.js"></script>


                <div class="forms-deactivate-license"
                    <?php echo !($need_activation) ? '' : 'style="display:none;"'; ?>>
                    <i class="zmdi zmdi-shield-check"></i>
                    <span><?php echo _VEHICLE_MANAGER_YOUR_LICENSE_ACTIVE; ?></span>
                    <a class="deactivate-link dashboard-link-action" href="#"><?php echo _VEHICLE_MANAGER_DEACTIVATE; ?></a>
                </div>
                <div class="forms-activate-license"
                    <?php echo ($need_activation) ? '' : 'style="display:none;"'; ?>>
                    <i class="zmdi zmdi-shield-check"></i>
                    <span><?php echo _VEHICLE_MANAGER_YOUR_LICENSE_NEED_ACTIVE; ?></span>
                    <a class="activate-link dashboard-link-action" href="#"><?php echo _VEHICLE_MANAGER_ACTIVATE; ?></a>
                </div>


                </span>

                <span class="span12 submit-close">
                    <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
                </span>
             
          </div>
        </div>
        <div id="deactivate-dialog" class="ordasoft-modal-sm modal hide" style="display:none">
            <div class="modal-body">
                <h3><?php echo _VEHICLE_MANAGER_LICENSE_DEACTIVATION; ?></h3>
                <p class="modal-text can-delete"><?php echo _VEHICLE_MANAGER_ARE_YOU_SURE_DEACTIVATE; ?></p>
            </div>
            <div class="modal-footer">
                <a href="#" class="ordasoft-btn" data-dismiss="modal">
                    <?php echo _VEHICLE_MANAGER_CANCEL; ?>
                </a>
                <a href="#" class="ordasoft-btn-primary red-btn" id="apply-deactivate">
                    <?php echo _VEHICLE_MANAGER_APPLY; ?>
                </a>
            </div>
        </div>
        <div id="login-modal" class="ordasoft-modal-sm modal hide" aria-hidden="true" style="display: none;">
            <div class="modal-body">
                
            </div>
        </div>

        <script>
            Joomla.submitbutton = function(pressbutton) {
                document.adminForm.task.value = pressbutton;
                
                if(pressbutton == "about_version"){
                    jQuery("#about-modal").modal('show');
                    return;
                }else{
                    document.adminForm.submit();
                }
            }

            function listItemTask(id, task, frmName){
                var form = document.adminForm;
                cb = eval( id );
                if (cb) {
                    cb.checked = true;
                    form.task.value = task;
                    form.submit();
                }
                return false;
            }

            jQuery(document).ready(function(){
                setTimeout(function(){
                    jQuery("#system-message-container").empty();
                }, 3000);
            });
        </script>    


<?php
        }  else  addInfoAboutUpdate(); //only for free version



        mosMenuBarVehicle_ext::endTable();
    }

    static function MENU_RENT_HISTORY()
    {
        mosMenuBarVehicle_ext::startTable();
        mosMenuBarVehicle_ext::spacer();
        mosMenuBarVehicle_ext::back();
        mosMenuBarVehicle_ext::spacer();
        mosMenuBarVehicle_ext::endTable();
    }



    static function MENU_SAVE_BACKEND()
    {
        mosMenuBarVehicle_ext::startTable();
        mosMenuBarVehicle_ext::spacer();
        mosMenuBarVehicle_ext::save();
        mosMenuBarVehicle_ext::spacer();
        mosMenuBarVehicle_ext::apply('apply', 'apply');
        mosMenuBarVehicle_ext::spacer();
        mosMenuBarVehicle_ext::back();
        mosMenuBarVehicle_ext::spacer();
        mosMenuBarVehicle_ext::endTable();
    }

    static function MENU_RENT()
    {
        mosMenuBarVehicle_ext::startTable();
        mosMenuBarVehicle_ext::NewCustom('rent', 'adminForm', "../administrator/components/com_vehiclemanager/images/dm_lend.png", "../administrator/components/com_vehiclemanager/images/dm_lend_32.png", _VEHICLE_MANAGER_TOOLBAR_RENT_VEHICLES, _VEHICLE_MANAGER_TOOLBAR_ADMIN_RENT, true, 'adminForm');
        mosMenuBarVehicle_ext::cancel();
        mosMenuBarVehicle_ext::spacer();
        mosMenuBarVehicle_ext::endTable();
    }

    static function MENU_EDIT_RENT()
    {
        mosMenuBarVehicle_ext::startTable();

        mosMenuBarVehicle_ext::NewCustom('edit_rent', 'adminForm', "../administrator/components/com_vehiclemanager/images/dm_lend.png", "../administrator/components/com_vehiclemanager/images/dm_lend_32.png", _VEHICLE_MANAGER_TOOLBAR_RENT_VEHICLES, _VEHICLE_MANAGER_TOOLBAR_ADMIN_EDIT_RENT, true, 'adminForm');

        mosMenuBarVehicle_ext::cancel();
        mosMenuBarVehicle_ext::spacer();
        mosMenuBarVehicle_ext::endTable();
    }

    static function MENU_RENTREQUESTS()
    {
        global $mosConfig_absolute_path;
        mosMenuBarVehicle_ext::startTable();
        mosMenuBarVehicle_ext::back();
        mosMenuBarVehicle_ext::NewCustom('accept_rent_requests', 'adminForm', '../administrator/components/com_vehiclemanager/images/dm_accept.png', '../administrator/components/com_vehiclemanager/images/dm_accept_32.png', _VEHICLE_MANAGER_TOOLBAR_ACCEPT_REQUEST, _VEHICLE_MANAGER_TOOLBAR_ADMIN_ACCEPT, true, 'adminForm');

        mosMenuBarVehicle_ext::NewCustom('decline_rent_requests', 'adminForm', '../administrator/components/com_vehiclemanager/images/dm_decline.png', '../administrator/components/com_vehiclemanager/images/dm_decline_32.png', _VEHICLE_MANAGER_TOOLBAR_DECLINE_REQUEST, _VEHICLE_MANAGER_TOOLBAR_ADMIN_DECLINE, true, 'adminForm');

        //mosMenuBarVehicle_ext::cancel();
        mosMenuBarVehicle_ext::spacer();
        mosMenuBarVehicle_ext::endTable();
    }

    static function MENU_BUYINGREQUESTS()
    {
        global $mosConfig_absolute_path;
        mosMenuBarVehicle_ext::startTable();

        mosMenuBarVehicle_ext::NewCustom('accept_buying_requests', 'adminForm', '../administrator/components/com_vehiclemanager/images/dm_accept.png', '../administrator/components/com_vehiclemanager/images/dm_accept_32.png', _VEHICLE_MANAGER_TOOLBAR_ACCEPT_REQUEST, _VEHICLE_MANAGER_TOOLBAR_ADMIN_ACCEPT, true, 'adminForm');

        mosMenuBarVehicle_ext::NewCustom('decline_buying_requests', 'adminForm', '../administrator/components/com_vehiclemanager/images/dm_decline.png', '../administrator/components/com_vehiclemanager/images/dm_decline_32.png', _VEHICLE_MANAGER_TOOLBAR_DECLINE_REQUEST, _VEHICLE_MANAGER_TOOLBAR_ADMIN_DECLINE, true, 'adminForm');


        //mosMenuBarVehicle_ext::cancel();
        mosMenuBarVehicle_ext::spacer();
        mosMenuBarVehicle_ext::endTable();
    }

    static function MENU_RENT_RETURN()
    {
        mosMenuBarVehicle_ext::startTable();
        mosMenuBarVehicle_ext::NewCustom('rent_return', 'adminForm', "../administrator/components/com_vehiclemanager/images/dm_lend_return.png", "../administrator/components/com_vehiclemanager/images/dm_lend_return_32.png", _VEHICLE_MANAGER_TOOLBAR_RETURN_VEHICLE, _VEHICLE_MANAGER_TOOLBAR_ADMIN_RETURN, true, 'adminForm');
        mosMenuBarVehicle_ext::cancel();
        //mosMenuBarVehicle_ext::spacer();
        mosMenuBarVehicle_ext::endTable();
    }

    function MENU_REFETCH_INFOS()
    {
        mosMenuBarVehicle_ext::startTable();
        mosMenuBarVehicle_ext::NewCustom('refetchInfos', 'adminForm', '../administrator/components/com_vehiclemanager/images/dm_refetchInfos.png', '../administrator/components/com_vehiclemanager/images/dm_refetchInfos_32.png', _VEHICLE_MANAGER_TOOLBAR_REFETCH_INFORMATION, _VEHICLE_MANAGER_TOOLBAR_ADMIN_REFRESH, true, 'adminForm');
        mosMenuBarVehicle_ext::cancel();

        mosMenuBarVehicle_ext::spacer();
        mosMenuBarVehicle_ext::endTable();
    }

    static function MENU_IMPORT_EXPORT()
    {
        mosMenuBarVehicle_ext::startTable();
        mosMenuBarVehicle_ext::NewCustom_I('import', 'adminForm', '../administrator/components/com_vehiclemanager/images/dm_import.png', '../administrator/components/com_vehiclemanager/images/dm_import_32.png', _VEHICLE_MANAGER_TOOLBAR_IMPORT, _VEHICLE_MANAGER_TOOLBAR_ADMIN_IMPORT, true, 'adminForm');

        mosMenuBarVehicle_ext::NewCustom_E('export', 'adminForm', '../administrator/components/com_vehiclemanager/images/dm_export.png', '../administrator/components/com_vehiclemanager/images/dm_export_32.png', _VEHICLE_MANAGER_TOOLBAR_EXPORT, _VEHICLE_MANAGER_TOOLBAR_ADMIN_EXPORT, true, 'adminForm');


        //mosMenuBarVehicle_ext::back();
        mosMenuBarVehicle_ext::spacer();
        mosMenuBarVehicle_ext::endTable();
    }

    static function MENU_ABOUT()
    {
        mosMenuBarVehicle_ext::startTable();
        mosMenuBarVehicle_ext::back();
        mosMenuBarVehicle_ext::endTable();
    }
    static function MENU_ORDERS()
    {
        mosMenuBarVehicle_ext::deleteList('', 'deleteOrder','Delete Order');
        //mosMenuBarVehicle_ext::startTable();
        //mosMenuBarVehicle_ext::cancel();
        // mosMenuBarVehicle_ext::back();
        //mosMenuBarVehicle_ext::endTable();
    }

    static function MENU_ORDERS_DETAILS()
    {
        mosMenuBarVehicle_ext::startTable();
        mosMenuBarVehicle_ext::back();
        mosMenuBarVehicle_ext::endTable();
    }


    static function MENU_USER_RENT_HISTORY()
    {
        mosMenuBarVehicle_ext::startTable();
        mosMenuBarVehicle_ext::spacer();
        mosMenuBarVehicle_ext::back();
        mosMenuBarVehicle_ext::spacer();
        mosMenuBarVehicle_ext::endTable();
    }
}

