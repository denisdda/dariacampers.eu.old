<?php
/**
 * @package   DPCalendar
 * @author    Digital Peak http://www.digital-peak.com
 * @copyright Copyright (C) 2007 - 2020 Digital Peak. All rights reserved.
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */
defined('_JEXEC') or die();

JLoader::import('joomla.application.component.controller');

class DPCalendarControllerProfile extends JControllerLegacy
{
	public function change()
	{
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		$this->getModel()->setUsers(explode(',', $this->input->getString('users')), $this->input->getString('action'));
		\DPCalendar\Helper\DPCalendarHelper::sendMessage('');
	}

	public function tz()
	{
		$tz = new DateTimeZone($this->input->getString('tz'));
		JFactory::getSession()->set('user-timezone', $tz->getName(), 'DPCalendar');

		$this->setRedirect(base64_decode($this->input->getBase64('return', JUri::base())));
	}

	public function getModel($name = 'profile', $prefix = 'DPCalendarModel', $config = ['ignore_request' => true])
	{
		return parent::getModel($name, $prefix, $config);
	}
}
