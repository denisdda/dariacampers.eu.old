<?php
/**
 * @package   DPCalendar
 * @author    Digital Peak http://www.digital-peak.com
 * @copyright Copyright (C) 2007 - 2020 Digital Peak. All rights reserved.
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */
defined('_JEXEC') or die();

JLoader::import('joomla.application.component.view');
JLoader::import('libraries.dpcalendar.fullcalendar', JPATH_COMPONENT);

class DPCalendarViewCalendar extends JViewLegacy
{

	public function display($tpl = null)
	{
		// Initialise variables
		$state = $this->get('State');
		$items = $this->get('AllItems');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JFactory::getApplication()->enqueueMessage(implode("\n", $errors), 'warning');
			return false;
		}

		if ($items === false) {
			throw new Exception(JText::_('JGLOBAL_CATEGORY_NOT_FOUND'), 404);
		}

		$app = JFactory::getApplication();
		$params = $app->getParams();

		$tmp = clone $state->params;
		$tmp->merge($params);

		// Escape strings for HTML output
		$this->pageclass_sfx = htmlspecialchars($params->get('pageclass_sfx'));

		$this->params = $tmp;
		$this->items = $items;

		$selectedCalendars = [];
		foreach ($items as $calendar) {
			$selectedCalendars[] = $calendar->id;
		}
		$this->selectedCalendars = $selectedCalendars;

		$doNotListCalendars = [];
		foreach ($this->params->get('idsdnl', []) as $id) {
			$parent = DPCalendarHelper::getCalendar($id);
			if ($parent == null) {
				continue;
			}

			if ($parent->id != 'root') {
				$doNotListCalendars[$parent->id] = $parent;
			}

			if (! $parent->external) {
				foreach ($parent->getChildren(true) as $child) {
					$doNotListCalendars[$child->id] = DPCalendarHelper::getCalendar($child->id);
				}
			}
		}
		// if none are selected, use selected calendars
		$this->doNotListCalendars = empty($doNotListCalendars) ? $this->items : $doNotListCalendars;

		parent::display($tpl);
	}
}
