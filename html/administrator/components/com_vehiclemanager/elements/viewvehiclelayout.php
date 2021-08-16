<?php
/**
 * FileName: viewvehiclelayout.php
 * Date: July 2011
 * JOS Version #: 1.6.x
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
    class JFormFieldViewVehiclelayout extends JFormField
    {
        protected function getInput()
        {
            $all_categories_layout = getLayoutsVeh('com_vehiclemanager','view_vehicle');
            $options = Array();
            //$options[] = JHtml::_('select.option', '', 'Use Global');
            foreach($all_categories_layout as $value){
                $options[] = JHtml::_('select.option', "$value", "$value"); 
            }
            return JHtml::_('select.genericlist', $options, $this->name, 'class="inputbox"', 'value', 'text', $this->value, $this->id);
        }
    }
} else {
    echo "Sanity test. Error version check!";
    exit;
}
