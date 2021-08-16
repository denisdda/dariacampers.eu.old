<?php
if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' );
if(!defined('DS')) define('DS', DIRECTORY_SEPARATOR);

/**
*
* @package VehicleManager
* @copyright 2020 by Ordasoft
* @author Andrey Kvasnevskiy - OrdaSoft (akbet@mail.ru); Rob de Cleen (rob@decleen.com);
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* Homepage: https://ordasoft.com/
*
* */

class com_VehicleManagerInstallerScript{

    /**
     * method to run before an install/update/uninstall method
     *
     * @return void
     */
    function preflight($type, $parent){
        // $parent is the class calling this method
        // $type is the type of change (install, update or discover_install)

        // Save admin settings before updete
        if ( $type == 'update' ) {
            // $vehiclemanager_configuration:
            if(is_file(JPATH_ROOT . '/administrator/components/com_vehiclemanager/admin.vehiclemanager.class.conf.php') ) {
                require(JPATH_ROOT . '/administrator/components/com_vehiclemanager/admin.vehiclemanager.class.conf.php');
                if(isset($vehiclemanager_configuration) )
                    $GLOBALS['vehiclemanager_configuration_old'] = $vehiclemanager_configuration;
            }
            if(is_file(JPATH_ROOT . '/components/com_vehiclemanager/regions_and_citys.txt') ) {
                copy(JPATH_ROOT . '/components/com_vehiclemanager/regions_and_citys.txt', JPATH_ROOT . '/components/com_vehiclemanager/regions_and_citys.txt_bak');
            }
            if(is_file(JPATH_ROOT . '/components/com_vehiclemanager/countrys_and_regions.txt') ) {
                copy(JPATH_ROOT . '/components/com_vehiclemanager/countrys_and_regions.txt', JPATH_ROOT . '/components/com_vehiclemanager/countrys_and_regions.txt_bak');
            }
            if(is_file(JPATH_ROOT . '/components/com_vehiclemanager/makers_and_models.txt') ) {
                copy(JPATH_ROOT . '/components/com_vehiclemanager/makers_and_models.txt', JPATH_ROOT . '/components/com_vehiclemanager/makers_and_models.txt_bak');
            }

        }

        $db = JFactory::getDBO();
        $db->setQuery("DELETE FROM #__update_sites WHERE name = 'Vehiclemanager`s Update'");
        $db->execute();
    }

    /**
     * method to install the component
     *
     * @return void
     */
    function install($parent){
        // $parent is the class calling this method
    }

    /**
     * method to update the component
     *
     * @return void
     */
    function update($parent){
        // $parent is the class calling this method
    }

    /**
     * method to uninstall the component
     *
     * @return void
     */
    function uninstall($parent){
        // $parent is the class calling this method
        if(file_exists(JPATH_ROOT."/administrator/components/com_vehiclemanager/uninstall.vehiclemanager.php"))
            require_once(JPATH_ROOT."/administrator/components/com_vehiclemanager/uninstall.vehiclemanager.php");
    }

    /**
     * method to run after an install/update/uninstall method
     *
     * @return void
     */
    function postflight($type, $parent){
        // $parent is the class calling this method
        // $type is the type of change (install, update or discover_install)

        // Marge old admin settings and new configuration after update
        global $vehiclemanager_configuration_old;

        if ( $type == 'update' ) {
            // $vehiclemanager_configuration new from update files
            if(is_file(JPATH_ROOT . '/administrator/components/com_vehiclemanager/admin.vehiclemanager.class.conf_new.php') ) {
                require(JPATH_ROOT . '/administrator/components/com_vehiclemanager/admin.vehiclemanager.class.conf_new.php');
                if(isset($vehiclemanager_configuration) && isset($vehiclemanager_configuration_old)  )
                    $vehiclemanager_configuration = array_replace_recursive($vehiclemanager_configuration, $vehiclemanager_configuration_old);
            }
            if(is_file(JPATH_ROOT . '/components/com_vehiclemanager/regions_and_citys.txt_bak') ) {
                copy(JPATH_ROOT . '/components/com_vehiclemanager/regions_and_citys.txt_bak', JPATH_ROOT . '/components/com_vehiclemanager/regions_and_citys.txt');
            }
            if(is_file(JPATH_ROOT . '/components/com_vehiclemanager/countrys_and_regions.txt_bak') ) {
                copy(JPATH_ROOT . '/components/com_vehiclemanager/countrys_and_regions.txt_bak', JPATH_ROOT . '/components/com_vehiclemanager/countrys_and_regions.txt');
            }
            if(is_file(JPATH_ROOT . '/components/com_vehiclemanager/makers_and_models.txt_bak') ) {
                copy(JPATH_ROOT . '/components/com_vehiclemanager/makers_and_models.txt_bak', JPATH_ROOT . '/components/com_vehiclemanager/makers_and_models.txt');
            }

        }

        // In J4 method postflight calling on uninstall too.
        // https://docs.joomla.org/Potential_backward_compatibility_issues_in_Joomla_4
        if ( $type == 'install' || $type == 'update' ) {

            if(file_exists(JPATH_ROOT."/administrator/components/com_vehiclemanager/install.vehiclemanager.php")){
                require_once(JPATH_ROOT."/administrator/components/com_vehiclemanager/install.vehiclemanager.php");
                com_install2();
            }
        }
    }
}
