<?php

/**
 * FileName: user.php
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
if (version_compare(JVERSION, "1.6.0", "ge"))
{
    class JFormFieldUser extends JFormField
    {
        protected function getInput()
        {
            $db = JFactory::getDbo(); 
            $query = "SELECT u.name AS user, u.name AS title 
                      FROM #__users AS u
                      LEFT JOIN #__vehiclemanager_vehicles AS v ON v.owneremail=u.email
                      WHERE v.published = 1
                      GROUP BY u.name
                      ORDER BY u.name";
            $db->setQuery($query);
            $showownervehicles = $db->loadObjectList();
            return JHtml::_('select.genericlist', $showownervehicles, $this->name, 'class="inputbox"', 'user', 'user', $this->value, $this->name);
        }
    }
} else {
    echo "Sanity test. Error version check!";
    exit;
}
