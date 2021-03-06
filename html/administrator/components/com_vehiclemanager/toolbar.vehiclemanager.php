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

$mainframe = $GLOBALS['mainframe'] = JFactory::getApplication(); // for 1.6


if (stristr($_SERVER['PHP_SELF'], 'administrator'))
{
    @define('_VM_IS_BACKEND', '1');
}
defined('_VM_TOOLBAR_LOADED') or define('_VM_TOOLBAR_LOADED', 1);

include_once( JPATH_ROOT . "/components/com_vehiclemanager/compat.joomla1.5.php" );



/* require_once( $mainframe->getPath( 'toolbar_html' ) );
  require_once( $mainframe->getPath( 'toolbar_default' ) ); */
// for 1.6
$path = JPATH_SITE . "/administrator/components/com_vehiclemanager/";
//require_once( $path . 'toolbar.vehiclemanager.php' );
require_once( $path . 'toolbar_ext.php' );
require_once( $path . 'toolbar.vehiclemanager.html.php' );
require_once ( JPATH_ROOT . "/components/com_vehiclemanager/functions.php" );

// --

vmLittleThings::language_load_VM();

//
$section = mosGetParam($_REQUEST, 'section', 'courses');

if (version_compare(JVERSION, "3.0.0", "ge"))
    if (isset($_REQUEST['task']))
    {
        $task = mosGetParam($_REQUEST, 'task', '');
    } else
    {
        $task = '';
    }

if (isset($section) && $section == 'categories')
{
    switch ($task) {
        //case "new":
        case "add":
            menucat::NEW_CATEGORY();
            vmLittleThings::addSubmenu("Categories");
            break;
        case "edit":
            menucat::EDIT_CATEGORY();
            vmLittleThings::addSubmenu("Categories");
            break;
        default:
            menucat::SHOW_CATEGORIES();
            vmLittleThings::addSubmenu("Categories");
            break;
    }
} elseif ($section == 'featured_manager')
{
    switch ($task) {
        case "add":
            menufeaturedmanager::NEW_FEATUREDMANAGER();
            vmLittleThings::addSubmenu("Features Manager");
            break;
        case "edit":
            menufeaturedmanager::EDIT_FEATUREDMANAGER();
            vmLittleThings::addSubmenu("Features Manager");
            break;
        default:
            menufeaturedmanager::MENU_FEATUREDMANAGER();
            vmLittleThings::addSubmenu("Features Manager");
            break;
    }
} elseif ($section == 'language_manager')
{
    switch ($task) {

        case "copy":
            menulanguagemanager::EDIT_LANGUAGEMANAGER();
            vmLittleThings::addSubmenu("Language Manager");
            break;
        case "edit":
            menulanguagemanager::EDIT_LANGUAGEMANAGER();
            vmLittleThings::addSubmenu("Language Manager");
            break;
        default:
            menulanguagemanager::MENU_LANGUAGEMANAGER();
            vmLittleThings::addSubmenu("Language Manager");
            break;
    }
} else
{
    switch ($task) {
        //case "new":
        case "add":
            menuvehiclemanager::MENU_SAVE_BACKEND();
            vmLittleThings::addSubmenu("Vehicles");
            break;

        case "edit":
            menuvehiclemanager::MENU_EDIT();
            vmLittleThings::addSubmenu("Vehicles");
            break;

        case "rent":
            menuvehiclemanager::MENU_RENT();
            vmLittleThings::addSubmenu("Rent Requests");
            break;
        case "rent_history":
            menuvehiclemanager::MENU_RENT_HISTORY();
            break;

        case "users_rent_history":
            menuvehiclemanager::MENU_USER_RENT_HISTORY();
            vmLittleThings::addSubmenu("User Rent History");
            break;

        //label 06.14.17 ???????????????????? ?????????????? ?? user rent history
        case "user_rent_history_sorted_by_id":
            menuvehiclemanager::MENU_USER_RENT_HISTORY();
            vmLittleThings::addSubmenu("User Rent History");
            break;

        case "user_rent_history_sorted_by_rent_from":
            menuvehiclemanager::MENU_USER_RENT_HISTORY();
            vmLittleThings::addSubmenu("User Rent History");
            break;

        case "user_rent_history_sorted_by_rent_until":
            menuvehiclemanager::MENU_USER_RENT_HISTORY();
            vmLittleThings::addSubmenu("User Rent History");
            break;

        case "user_rent_history_sorted_by_rent_return":
            menuvehiclemanager::MENU_USER_RENT_HISTORY();
            vmLittleThings::addSubmenu("User Rent History");
            break;
        //end label 06.14.17 ???????????????????? ?????????????? ?? user rent history

        case "edit_rent":
            menuvehiclemanager::MENU_EDIT_RENT();
            vmLittleThings::addSubmenu("Rent Requests");
            break;

        case "rent_return":
            menuvehiclemanager::MENU_RENT_RETURN();
            vmLittleThings::addSubmenu("Rent Requests");
            break;

        case "rent_requests":
            menuvehiclemanager::MENU_RENTREQUESTS();
            vmLittleThings::addSubmenu("Rent Requests");
            break;

        case "buying_requests":
            menuvehiclemanager::MENU_BUYINGREQUESTS();
            vmLittleThings::addSubmenu("Sale Manager");
            break;

        case "config":
            menuvehiclemanager::MENU_CONFIG();
            vmLittleThings::addSubmenu("Settings");
            break;

        case "config_save":
            menuvehiclemanager::MENU_CONFIG();
            vmLittleThings::addSubmenu("Settings");
            break;

        case "about":
            menuvehiclemanager::MENU_ABOUT();
            vmLittleThings::addSubmenu("About");
            break;

        case "delete_review":
            menuvehiclemanager::MENU_DELETE_REVIEW();
            vmLittleThings::addSubmenu("Vehicles");
            break;

        case "edit_review":
            menuvehiclemanager::MENU_EDIT_REVIEW();
            vmLittleThings::addSubmenu("Reviews");
            break;

        case "update_review":
            menuvehiclemanager::MENU_EDIT();
            vmLittleThings::addSubmenu("Vehicles");
            break;

        case "cancel_review_edit":
            menuvehiclemanager::MENU_EDIT();
            vmLittleThings::addSubmenu("Vehicles");
            break;

//**************   begin for manage reviews   *********************
        case "manage_review":
            menuvehiclemanager::MENU_MANAGE_REVIEW();
            vmLittleThings::addSubmenu("Reviews");
            break;

        case "delete_manage_review":
            menuvehiclemanager::MENU_MANAGE_REVIEW_DELETE();
            vmLittleThings::addSubmenu("Reviews");
            break;

        case "edit_manage_review":
            menuvehiclemanager::MENU_MANAGE_REVIEW_EDIT();
            vmLittleThings::addSubmenu("Reviews");
            break;

        case "update_edit_manage_review":
            menuvehiclemanager::MENU_MANAGE_REVIEW_EDIT_EDIT();
            vmLittleThings::addSubmenu("Reviews");
            break;

        case "cancel_edit_manage_review":
            menuvehiclemanager::MENU_MANAGE_REVIEW_EDIT_EDIT();
            vmLittleThings::addSubmenu("Reviews");
            break;

        case "sorting_manage_review_numer":
            menuvehiclemanager::MENU_MANAGE_REVIEW();
            vmLittleThings::addSubmenu("Reviews");
            break;

        case "sorting_manage_review_mls":
            menuvehiclemanager::MENU_MANAGE_REVIEW();
            vmLittleThings::addSubmenu("Reviews");
            break;

        case "sorting_manage_review_title_vehicle":
            menuvehiclemanager::MENU_MANAGE_REVIEW();
            vmLittleThings::addSubmenu("Reviews");
            break;

        case "sorting_manage_review_title_catigory":
            menuvehiclemanager::MENU_MANAGE_REVIEW();
            vmLittleThings::addSubmenu("Reviews");
            break;

        case "sorting_manage_review_title_review":
            menuvehiclemanager::MENU_MANAGE_REVIEW();
            vmLittleThings::addSubmenu("Reviews");
            break;

        case "sorting_manage_review_published":
            menuvehiclemanager::MENU_MANAGE_REVIEW();
            vmLittleThings::addSubmenu("Reviews");
            break;

        case "sorting_manage_review_user_name":
            menuvehiclemanager::MENU_MANAGE_REVIEW();
            vmLittleThings::addSubmenu("Reviews");
            break;

        case "sorting_manage_review_date":
            menuvehiclemanager::MENU_MANAGE_REVIEW();
            vmLittleThings::addSubmenu("Reviews");
            break;

        case "sorting_manage_review_rating":
            menuvehiclemanager::MENU_MANAGE_REVIEW();
            vmLittleThings::addSubmenu("Reviews");
            break;
//**************   end for manage reviews   ***********************

        default:
            menuvehiclemanager::MENU_DEFAULT();
            vmLittleThings::addSubmenu("Vehicles");
            break;
    }
}

