<?php
/**
 * @package   DPCalendar
 * @author    Digital Peak http://www.digital-peak.com
 * @copyright Copyright (C) 2007 - 2020 Digital Peak. All rights reserved.
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */
defined('_JEXEC') or die();

?>
<div class="com-dpcalendar-eventform__tabbed-fields dp-tabs">
	<?php $checked = 'checked="checked"'; ?>
	<?php foreach ($this->form->getFieldSets() as $name => $fieldSet) { ?>
		<?php $fields = $this->form->getFieldset($name); ?>
		<?php if (!$fields) {
			continue;
		} ?>
		<input type="radio" class="dp-tabs__input" name="dp-event-form-tabs" id="dp-tab-<?php echo $name; ?>" <?php echo $checked; ?>>
		<label for="dp-tab-<?php echo $name; ?>" class="dp-tabs__label">
			<?php echo $this->translate($fieldSet->label); ?>
		</label>
		<div class="dp-tabs__tab dp-tabs__tab-<?php echo $name; ?>">
			<?php foreach ($fields as $field) { ?>
				<?php echo $field->renderField(['class' => 'dp-field-' . str_replace('_', '-', $field->fieldname)]); ?>
			<?php } ?>
			<?php if ($name == 'location' && $this->params->get('event_form_change_location', 1)) { ?>
				<?php echo $this->loadTemplate('map'); ?>
			<?php } ?>
		</div>
		<?php $checked = ''; ?>
	<?php } ?>
</div>
