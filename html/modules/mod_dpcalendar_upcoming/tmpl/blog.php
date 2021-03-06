<?php
/**
 * @package   DPCalendar
 * @author    Digital Peak http://www.digital-peak.com
 * @copyright Copyright (C) 2007 - 2020 Digital Peak. All rights reserved.
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */
defined('_JEXEC') or die();

if (!$events) {
	echo JText::_('MOD_DPCALENDAR_UPCOMING_NO_EVENT_TEXT');

	return;
}

require JModuleHelper::getLayoutPath('mod_dpcalendar_upcoming', '_scripts');
?>
<div class="mod-dpcalendar-upcoming mod-dpcalendar-upcoming-blog mod-dpcalendar-upcoming-<?php echo $module->id; ?> dp-locations"
	 data-popup="<?php echo $params->get('show_as_popup', 0); ?>">
	<div class="mod-dpcalendar-upcoming-blog__events">
		<?php foreach ($groupedEvents as $groupHeading => $events) { ?>
			<?php $calendar = DPCalendarHelper::getCalendar($event->catid); ?>
			<?php if ($groupHeading) { ?>
				<div class="mod-dpcalendar-upcoming-blog__group">
				<p class="mod-dpcalendar-upcoming-blog__heading dp-group-heading"><?php echo $groupHeading; ?></p>
			<?php } ?>
			<?php foreach ($events as $index => $event) { ?>
				<?php $displayData['event'] = $event; ?>
				<div class="mod-dpcalendar-upcoming-blog__event dp-event dp-event_<?php echo $event->ongoing_start_date ? 'started' : 'future'; ?>">
					<h3 class="mod-dpcalendar-upcoming-blog__heading">
						<?php if ($event->state == 3) { ?>
							<span class="dp-event_canceled">[<?php echo $translator->translate('MOD_DPCALENDAR_UPCOMING_CANCELED'); ?>]</span>
						<?php } ?>
						<a href="<?php echo $event->realUrl; ?>" target="_parent" class="dp-event-url dp-link">
							<?php echo $event->title; ?>
						</a>
					</h3>
					<?php if ($params->get('show_display_events') && $event->displayEvent->afterDisplayTitle) { ?>
						<div class="dp-event-display-after-title"><?php echo $event->displayEvent->afterDisplayTitle; ?></div>
					<?php } ?>
					<div class="dp-grid">
						<div class="mod-dpcalendar-upcoming-blog__information">
							<?php if ($calendar->canEdit || ($calendar->canEditOwn && $event->created_by == $user->id)) { ?>
								<a href="<?php echo $router->getEventFormRoute($event->id, $return); ?>" class="dp-link">
									<?php echo $layoutHelper->renderLayout(
										'block.icon',
										['icon' => \DPCalendar\HTML\Block\Icon::EDIT, 'title' => $translator->translate('JACTION_EDIT')]
									); ?>
								</a>
							<?php } ?>
							<?php if ($calendar->canDelete || ($calendar->canEditOwn && $event->created_by == $user->id)) { ?>
								<a href="<?php echo $router->getEventDeleteRoute($event->id, $return); ?>" class="dp-link">
									<?php echo $layoutHelper->renderLayout(
										'block.icon',
										['icon' => \DPCalendar\HTML\Block\Icon::DELETE, 'title' => $translator->translate('JACTION_DELETE')]
									); ?>
								</a>
							<?php } ?>
							<div class="mod-dpcalendar-upcoming-blog__date">
								<?php echo $translator->translate('MOD_DPCALENDAR_UPCOMING_DATE'); ?>:
								<?php echo $dateHelper->getDateStringFromEvent($event, $params->get('date_format'), $params->get('time_format')); ?>
							</div>
							<?php if ($event->rrule) { ?>
								<div class="mod-dpcalendar-upcoming-blog__rrule">
									<?php echo $dateHelper->transformRRuleToString($event->rrule, $event->start_date); ?>
								</div>
							<?php } ?>
							<div class="mod-dpcalendar-upcoming-blog__calendar">
								<?php echo $translator->translate('MOD_DPCALENDAR_UPCOMING_BLOG_CALENDAR'); ?>:
								<?php echo $calendar != null ? $calendar->title : $event->catid; ?>
							</div>
							<?php if (($params->get('show_location') || $params->get('show_map')) && !empty($event->locations)) { ?>
								<div class="mod-dpcalendar-upcoming-blog__location">
									<?php foreach ($event->locations as $location) { ?>
										<span class="mod-dpcalendar-upcoming-blog__location dp-location">
										<span class="dp-location__details"
											  data-latitude="<?php echo $location->latitude; ?>"
											  data-longitude="<?php echo $location->longitude; ?>"
											  data-title="<?php echo $location->title; ?>"
											  data-color="<?php echo $event->color; ?>"></span>
										<?php if ($params->get('show_location')) { ?>
											<a href="<?php echo $router->getLocationRoute($location); ?>"
											   class="mod-dpcalendar-upcoming-blog__url dp-location__url dp-link">
												<?php echo $location->title; ?>
											</a>
										<?php } ?>
										<span class="dp-location__description">
											<?php echo $layoutHelper->renderLayout('event.tooltip', $displayData); ?>
										</span>
									</span>
									<?php } ?>
								</div>
							<?php } ?>
							<div class="mod-dpcalendar-upcoming-blog__capacity">
								<?php if ($event->capacity === null) { ?>
									<?php echo $translator->translate('MOD_DPCALENDAR_UPCOMING_BLOG_CAPACITY'); ?>:
									<?php echo $translator->translate('MOD_DPCALENDAR_UPCOMING_BLOG_CAPACITY_UNLIMITED'); ?>
								<?php } ?>
								<?php if ($event->capacity > 0) { ?>
									<?php echo $translator->translate('MOD_DPCALENDAR_UPCOMING_BLOG_CAPACITY'); ?>:
									<?php echo ($event->capacity - $event->capacity_used) . '/' . (int)$event->capacity; ?>
								<?php } ?>
							</div>
							<?php if ($params->get('show_price')) { ?>
								<div class="mod-dpcalendar-upcoming-blog__price">
									<?php echo $translator->translate($event->price ? 'MOD_DPCALENDAR_UPCOMING_BLOG_PAID_EVENT' : 'MOD_DPCALENDAR_UPCOMING_BLOG_FREE_EVENT'); ?>
								</div>
							<?php } ?>
							<?php if ($params->get('show_booking', 1) && \DPCalendar\Helper\Booking::openForBooking($event)) { ?>
								<a href="<?php echo $router->getBookingFormRouteFromEvent($event, $return); ?>" class="dp-link dp-link_cta dp-button">
									<?php echo $layoutHelper->renderLayout('block.icon', ['icon' => \DPCalendar\HTML\Block\Icon::PLUS]); ?>
									<?php echo $translator->translate('MOD_DPCALENDAR_UPCOMING_BOOK'); ?>
								</a>
							<?php } ?>
						</div>
						<?php if ($event->images->image_intro) { ?>
							<div class="mod-dpcalendar-upcoming-blog__image">
								<figure class="dp-figure">
									<img class="dp-image" src="<?php echo $event->images->image_intro; ?>"
										 alt="<?php echo $event->images->image_intro_alt; ?>" loading="lazy">
									<?php if ($event->images->image_intro_caption) { ?>
										<figcaption class="dp-figure__caption"><?php echo $event->images->image_intro_caption; ?></figcaption>
									<?php } ?>
								</figure>
							</div>
						<?php } ?>
					</div>
					<?php if ($params->get('show_display_events') && $event->displayEvent->beforeDisplayContent) { ?>
						<div class="dp-event-display-before-content"><?php echo $event->displayEvent->beforeDisplayContent; ?></div>
					<?php } ?>
					<div class="mod-dpcalendar-upcoming-blog__description">
						<?php echo $event->truncatedDescription; ?>
					</div>
					<?php if ($params->get('show_display_events') && $event->displayEvent->afterDisplayContent) { ?>
						<div class="dp-event-display-after-content"><?php echo $event->displayEvent->afterDisplayContent; ?></div>
					<?php } ?>
					<?php echo $layoutHelper->renderLayout('schema.event', $displayData); ?>
				</div>
			<?php } ?>
			<?php if ($groupHeading) { ?>
				</div>
			<?php } ?>
		<?php } ?>
	</div>
	<?php if ($params->get('show_map')) { ?>
		<div class="mod-dpcalendar-upcoming-blog__map dp-map"
			 data-width="<?php echo $params->get('map_width', '100%'); ?>"
			 data-height="<?php echo $params->get('map_height', '350px'); ?>"
			 data-zoom="<?php echo $params->get('map_zoom', 4); ?>"
			 data-latitude="<?php echo $params->get('map_lat', 47); ?>"
			 data-longitude="<?php echo $params->get('map_long', 4); ?>"
			 data-ask-consent="<?php echo $params->get('map_ask_consent'); ?>">
		</div>
	<?php } ?>
</div>
