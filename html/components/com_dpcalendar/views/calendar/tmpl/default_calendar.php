<?php
/**
 * @package   DPCalendar
 * @author    Digital Peak http://www.digital-peak.com
 * @copyright Copyright (C) 2007 - 2020 Digital Peak. All rights reserved.
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */
defined('_JEXEC') or die();

?>
<div class="com-dpcalendar-calendar__calendar dp-calendar"
	 data-popupwidth="<?php echo $this->params->get('popup_width'); ?>"
	 data-popupheight="<?php echo $this->params->get('popup_height'); ?>"
	 data-hidden-days='<?php echo json_encode(\Joomla\Utilities\ArrayHelper::toInteger($this->params->get('hidden_days', []))); ?>'
	 data-options="DPCalendar.view.calendar.<?php echo $this->input->getInt('Itemid', 0); ?>.options"></div>
