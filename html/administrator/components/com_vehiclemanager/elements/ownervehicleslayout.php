<?php
/**
 * FileName: ownervehicleslayout.php
 * Date:July 2011
 * JOS Version #: 2.5
 * Development OrdaSoft(http://ordasoft.com)
 */
/**
*
* @package VehicleManager
* @copyright 2020 by Ordasoft
* @author Andrey Kvasnevskiy - OrdaSoft (akbet@mail.ru); Rob de Cleen (rob@decleen.com);
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* Homepage: https://ordasoft.com/
*
* */
defined('_JEXEC') or die('Restricted access');
require_once ( JPATH_SITE.'/components/com_vehiclemanager/functions.php' );
if (version_compare(JVERSION, "1.6.0", "ge"))
{
    class JFormFieldOwnerVehicleslayout extends JFormField
    {
        protected function getInput()
        {
            $all_categories_layout = getLayoutsVeh('com_vehiclemanager','owner_vehicles');
            $layouts = Array();
            //$layouts[] = JHtml::_('select.option', '', 'Use Global');
            foreach($all_categories_layout as $value){
                $layouts[] = JHtml::_('select.option', "$value", "$value"); 
            }
            return JHtml::_('select.genericlist', $layouts, $this->name, 'class="inputbox"', 'layout', 'title', $this->value, $this->name);
        }
    }
} else {
    echo "Sanity test. Error version check!";
    exit;
}
