<?php
/**
 * @package   DPCalendar
 * @author    Digital Peak http://www.digital-peak.com
 * @copyright Copyright (C) 2007 - 2020 Digital Peak. All rights reserved.
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */
namespace DPCalendar\Helper;

defined('_JEXEC') or die();

use Joomla\Registry\Registry;

if (!defined('DS')) {
	define('DS', DIRECTORY_SEPARATOR);
}

\JLoader::import('joomla.application.component.helper');
\JLoader::import('joomla.application.categories');
\JLoader::import('joomla.environment.browser');
\JLoader::import('joomla.filesystem.file');
\JLoader::import('joomla.filesystem.folder');
\JLoader::register('DPCalendarHelperRoute', JPATH_SITE . '/components/com_dpcalendar/helpers/route.php');

if (\DPCalendar\Helper\DPCalendarHelper::isJoomlaVersion('4', '>=')) {
	\JLoader::registerAlias('JFormFieldCategoryEdit', '\\Joomla\\Component\\Categories\\Administrator\\Field\\CategoryeditField');
	\JLoader::registerAlias('UsersModelRegistration', '\\Joomla\\Component\\Users\\Site\\Model\\RegistrationModel');
}
\JLoader::register('FieldsHelper', JPATH_ADMINISTRATOR . '/components/com_fields/helpers/fields.php');

class DPCalendarHelper
{
	public static $calendars = [];

	public static function getCalendar($id)
	{
		if (isset(self::$calendars[$id])) {
			return self::$calendars[$id];
		}
		$calendar = null;
		if (is_numeric($id) || $id == 'root') {
			$calendar = \JCategories::getInstance('DPCalendar')->get($id);
			if ($calendar == null) {
				return null;
			}
			$user = \JFactory::getUser();

			$calendar->params   = new Registry($calendar->params);
			$calendar->color    = str_replace('#', '', $calendar->params->get('color', '3366CC'));
			$calendar->external = false;
			$calendar->system   = 'joomla';
			$calendar->native   = true;

			$calendar->canCreate  = $user->authorise('core.create', 'com_dpcalendar.category.' . $calendar->id);
			$calendar->canEdit    = $user->authorise('core.edit', 'com_dpcalendar.category.' . $calendar->id);
			$calendar->canEditOwn = $user->authorise('core.edit.own', 'com_dpcalendar.category.' . $calendar->id);
			$calendar->canDelete  = $user->authorise('core.delete', 'com_dpcalendar.category.' . $calendar->id);
			$calendar->canBook    = $user->authorise('dpcalendar.book', 'com_dpcalendar.category.' . $calendar->id);

			$userId = $user->get('id');

			if (!empty($userId) && $user->authorise('core.edit.own', 'com_dpcalendar.category.' . $calendar->id)) {
				if ($userId == $calendar->created_user_id) {
					$calendar->canEdit = true;
				}
			}
		} else {
			\JPluginHelper::importPlugin('dpcalendar');
			$tmp = \JFactory::getApplication()->triggerEvent('onCalendarsFetch', [$id]);
			if (!empty($tmp)) {
				foreach ($tmp as $calendars) {
					foreach ($calendars as $fetchedCalendar) {
						$calendar = $fetchedCalendar;
					}
				}
			}
		}

		self::$calendars[$id] = $calendar;

		return $calendar;
	}

	public static function increaseEtag($calendarId)
	{
		// If we are not a native calendar do nothing
		if (!is_numeric($calendarId)) {
			return;
		}
		$calendar = self::getCalendar($calendarId);
		if (!$calendar || !$calendar->id) {
			return;
		}

		$params = new Registry($calendar->params);
		$params->set('etag', $params->get('etag', 1) + 1);

		$db = \JFactory::getDbo();
		$db->setQuery('update #__categories set params = ' . \JFactory::getDbo()->q($params->toString()) . ' where id = ' . $db->q($calendar->id));
		$db->execute();
	}

	public static function getComponentParameter($key, $defaultValue = null)
	{
		$params = \JComponentHelper::getParams('com_dpcalendar');

		return $params->get($key, $defaultValue);
	}

	public static function getFrLanguage()
	{
		$language = \JFactory::getApplication()->get('language');

		$user = \JFactory::getUser();
		if ($user->get('id')) {
			$userLanguage = $user->getParam('language');
			if (!empty($userLanguage)) {
				$language = $userLanguage;
			}
		}

		return $language;
	}

	/**
	 * Obfuscates the given string.
	 *
	 * @param $string
	 *
	 * @return string
	 */
	public static function obfuscate($string)
	{
		return base64_encode(@convert_uuencode($string));
	}

	/**
	 * Deobfuscates the given string.
	 *
	 * @param $string
	 *
	 * @return string
	 */
	public static function deobfuscate($string)
	{
		$tmp = @convert_uudecode(base64_decode($string));

		// Probably not obfuscated
		if (!$tmp) {
			return $string;
		}

		return $tmp;
	}

	public static function parseImages($event)
	{
		if (is_string($event->images)) {
			$event->images = json_decode($event->images);
		}

		$images        = $event->images;
		$event->images = new \stdClass();

		$event->images->image_intro         = isset($images->image_intro) ? $images->image_intro : null;
		$event->images->image_intro_alt     = isset($images->image_intro_alt) ? $images->image_intro_alt : null;
		$event->images->image_intro_caption = isset($images->image_intro_caption) ? $images->image_intro_caption : null;
		$event->images->image_full          = isset($images->image_full) ? $images->image_full : null;
		$event->images->image_full_alt      = isset($images->image_full_alt) ? $images->image_full_alt : null;
		$event->images->image_full_caption  = isset($images->image_full_caption) ? $images->image_full_caption : null;
	}

	public static function dayToString($day, $abbr = false)
	{
		$date = new \JDate();

		return addslashes($date->dayToString($day, $abbr));
	}

	public static function monthToString($month, $abbr = false)
	{
		$date = new \JDate();

		return addslashes($date->monthToString($month, $abbr));
	}

	public static function getDate($date = null, $allDay = null, $tz = null)
	{
		if ($date instanceof \JDate) {
			$dateObj = clone $date;
		} else {
			$dateObj = \JFactory::getDate($date, $tz);
		}

		$timezone = \JFactory::getApplication()->getCfg('offset');
		$user     = \JFactory::getUser();
		if ($user->get('id')) {
			$userTimezone = $user->getParam('timezone');
			if (!empty($userTimezone)) {
				$timezone = $userTimezone;
			}
		}

		$timezone = \JFactory::getSession()->get('user-timezone', $timezone, 'DPCalendar');

		if (!$allDay) {
			$dateObj->setTimezone(new \DateTimeZone($timezone));
		}

		return $dateObj;
	}

	public static function getDateFromString($date, $time, $allDay, $dateFormat = null, $timeFormat = null)
	{
		$string = $date;
		if (!empty($time)) {
			$string = $date . ($allDay ? '' : ' ' . $time);
		}

		$replaces = [
			'JANUARY',
			'FEBRUARY',
			'MARCH',
			'APRIL',
			'MAY',
			'JUNE',
			'JULY',
			'AUGUST',
			'SEPTEMBER',
			'OCTOBER',
			'NOVEMBER',
			'DECEMBER',
			'JANUARY_SHORT',
			'FEBRUARY_SHORT',
			'MARCH_SHORT',
			'APRIL_SHORT',
			'MAY_SHORT',
			'JUNE_SHORT',
			'JULY_SHORT',
			'AUGUST_SHORT',
			'SEPTEMBER_SHORT',
			'OCTOBER_SHORT',
			'NOVEMBER_SHORT',
			'DECEMBER_SHORT',
			'SATURDAY',
			'SUNDAY',
			'MONDAY',
			'TUESDAY',
			'WEDNESDAY',
			'THURSDAY',
			'FRIDAY',
			'SAT',
			'SUN',
			'MON',
			'TUE',
			'WED',
			'THU',
			'FRI'
		];
		$lang     = \JLanguage::getInstance('en-GB');
		foreach ($replaces as $key) {
			$string = str_replace(\JText::_($key), $lang->_($key), $string);
		}

		if (empty($dateFormat)) {
			$dateFormat = self::getComponentParameter('event_form_date_format', 'm.d.Y');
		}
		if (empty($timeFormat)) {
			$timeFormat = self::getComponentParameter('event_form_time_format', 'g:i a');
		}

		$date = self::getDate(null, $allDay);
		$date = \DateTime::createFromFormat($dateFormat . ($allDay ? '' : ' ' . $timeFormat), $string, $date->getTimezone());
		if ($date == null) {
			$errors = \DateTime::getLastErrors();
			throw new \Exception('Could not interprete format: ' . ($dateFormat . ($allDay ? '' : ' ' . $timeFormat)) .
				' for date string : ' . $string . PHP_EOL .
				'Error was: ' . implode(',', $errors['warnings']) . ' ' . implode(',', $errors['errors']));
		}

		$date = self::getDate($date->format('U'), $allDay);

		return $date;
	}

	public static function getDateStringFromEvent($event, $dateFormat = null, $timeFormat = null, $noTags = false)
	{
		$text = \JLayoutHelper::render(
			'event.datestring',
			['event' => $event, 'dateFormat' => $dateFormat, 'timeFormat' => $timeFormat],
			null,
			['component' => 'com_dpcalendar', 'client' => 0]
		);

		if (!$noTags) {
			return $text;
		}

		$text = strip_tags($text);
		$text = preg_replace("/\s\s+/", ' ', $text);

		return trim($text);
	}

	public static function dateStringToDatepickerFormat($dateString)
	{
		$pattern = [
			'd',
			'j',
			'l',
			'z',
			'F',
			'M',
			'n',
			'm',
			'Y',
			'y'
		];
		$replace = [
			'dd',
			'd',
			'DD',
			'o',
			'MM',
			'M',
			'm',
			'mm',
			'yy',
			'y'
		];
		foreach ($pattern as &$p) {
			$p = '/' . $p . '/';
		}

		return preg_replace($pattern, $replace, $dateString);
	}

	public static function renderEvents(array $events = null, $output, $params = null, $eventParams = [])
	{
		if ($events === null) {
			$events = [];
		}
		if ($params == null) {
			$params = \JComponentHelper::getParams('com_dpcalendar');
		}

		\JFactory::getLanguage()->load('com_dpcalendar', JPATH_ADMINISTRATOR . '/components/com_dpcalendar');

		$return = \JFactory::getApplication()->input->getInt('Itemid', null);
		if (!empty($return)) {
			$return = \JRoute::_('index.php?Itemid=' . $return, false);
		}

		$user = \JFactory::getUser();

		$lastHeading = '';

		$configuration           = $eventParams;
		$configuration['events'] = [];
		$locationCache           = [];
		foreach ($events as $event) {
			$variables = [];

			$calendar = self::getCalendar($event->catid);

			$variables['canEdit']    = $calendar && ($calendar->canEdit || ($calendar->canEditOwn && $event->created_by == $user->id));
			$variables['editLink']   = \DPCalendarHelperRoute::getFormRoute($event->id, $return);
			$variables['canDelete']  = $calendar && ($calendar->canDelete || ($calendar->canEditOwn && $event->created_by == $user->id));
			$variables['deleteLink'] = \JRoute::_(
				'index.php?option=com_dpcalendar&task=event.delete&e_id=' . $event->id . '&return=' . base64_encode($return)
			);

			$variables['canBook']  = \DPCalendar\Helper\Booking::openForBooking($event);
			$variables['bookLink'] = \DPCalendarHelperRoute::getBookingFormRouteFromEvent($event, $return);
			$variables['booking']  = isset($event->booking) ? (boolean)$event->booking : false;

			$variables['calendarLink'] = \DPCalendarHelperRoute::getCalendarRoute($event->catid);
			$variables['backLink']     = \DPCalendarHelperRoute::getEventRoute($event->id, $event->catid);
			$variables['backLinkFull'] = \DPCalendarHelperRoute::getEventRoute($event->id, $event->catid, true);

			// The date formats from http://php.net/date
			$dateformat = $params->get('event_date_format', 'm.d.Y');
			$timeformat = $params->get('event_time_format', 'g:i a');

			// These are the dates we'll display
			$startDate     = self::getDate($event->start_date, $event->all_day)->format($dateformat, true);
			$startTime     = self::getDate($event->start_date, $event->all_day)->format($timeformat, true);
			$endDate       = self::getDate($event->end_date, $event->all_day)->format($dateformat, true);
			$endTime       = self::getDate($event->end_date, $event->all_day)->format($timeformat, true);
			$dateSeparator = '-';

			$copyDateTimeFormat = 'Ymd';
			if ($event->all_day) {
				$startTime = '';
				$endTime   = '';
			} else {
				$copyDateTimeFormat = 'Ymd\THis';
			}
			if ($startDate == $endDate) {
				$endDate       = '';
				$dateSeparator = '';
			}

			$variables['color'] = $event->color;
			if (empty($variables['color']) && $calendar != null) {
				$variables['color'] = $calendar->color;
			}

			$variables['calendarName']  = $calendar != null ? $calendar->title : $event->catid;
			$variables['title']         = $event->title;
			$variables['date']          = strip_tags(self::getDateStringFromEvent($event, $dateformat, $timeformat));
			$variables['d']             = $variables['date'];
			$variables['startDate']     = $startDate;
			$variables['startDateIso']  = self::getDate($event->start_date, $event->all_day)->format('c');
			$variables['startTime']     = $startTime;
			$variables['endDate']       = $endDate;
			$variables['endDateIso']    = self::getDate($event->end_date, $event->all_day)->format('c');
			$variables['endTime']       = $endTime;
			$variables['dateSeparator'] = $dateSeparator;

			$variables['monthNr'] = self::getDate($event->start_date, $event->all_day)->format('m', true);
			$variables['year']    = self::getDate($event->start_date, $event->all_day)->format('Y', true);
			$variables['month']   = self::getDate($event->start_date, $event->all_day)->format('M', true);
			$variables['day']     = self::getDate($event->start_date, $event->all_day)->format('j', true);

			$location = '';
			if (isset($event->locations) && !empty($event->locations)) {
				$variables['location'] = $event->locations;
				foreach ($event->locations as $location) {
					if (key_exists($location->id, $locationCache)) {
						$location = $locationCache[$location->id];
					} else {
						$tmp                          = \DPCalendar\Helper\Location::format($location);
						$location->full               = $tmp;
						$locationCache[$location->id] = $tmp;
						$location                     = $tmp;
					}
				}
			}

			try {
				$variables['description'] = \JHTML::_('content.prepare', $event->description);
			} catch (\Exception $e) {
				$variables['description'] = $event->description;
			}
			if ($params->get('description_length', 0) > 0) {
				$variables['description'] = \JHtml::_('string.truncate', $variables['description'], $params->get('description_length', 0));
			}

			$variables['url']  = $event->url;
			$variables['hits'] = $event->hits;

			self::parseImages($event);
			$variables['images'] = $event->images;

			$author                  = \JFactory::getUser($event->created_by);
			$variables['author']     = $author->name;
			$variables['authorMail'] = $author->email;
			if (!empty($event->created_by_alias)) {
				$variables['author'] = $event->created_by_alias;
			}
			$variables['avatar'] = self::getAvatar($author->id, $author->email, $params);

			$variables['capacity']     = $event->capacity == null ? \JText::_('COM_DPCALENDAR_FIELD_CAPACITY_UNLIMITED') : $event->capacity;
			$variables['capacityUsed'] = $event->capacity_used;
			if (isset($event->bookings)) {
				foreach ($event->bookings as $booking) {
					if ($booking->user_id < 1) {
						continue;
					}
					$booking->avatar = self::getAvatar($booking->id, $booking->email, $params);
				}
				$variables['bookings'] = $event->bookings;
			}

			$end = self::getDate($event->end_date, $event->all_day);
			if ($event->all_day) {
				$end->modify('+1 day');
			}
			$variables['copyGoogleUrl'] = 'http://www.google.com/calendar/render?action=TEMPLATE&text=' . urlencode($event->title);
			$variables['copyGoogleUrl'] .= '&dates=' . self::getDate($event->start_date, $event->all_day)->format($copyDateTimeFormat, true) . '%2F' .
				$end->format($copyDateTimeFormat, true);
			$variables['copyGoogleUrl'] .= '&location=' . urlencode($location);
			$variables['copyGoogleUrl'] .= '&details=' . urlencode(\JHtml::_('string.truncate', $event->description, 200));
			$variables['copyGoogleUrl'] .= '&hl=' . self::getFrLanguage() . '&ctz=' .
				self::getDate($event->start_date, $event->all_day)->getTimezone()->getName();
			$variables['copyGoogleUrl'] .= '&sf=true&output=xml';

			$variables['copyOutlookUrl'] = \JRoute::_("index.php?option=com_dpcalendar&view=event&format=raw&id=" . $event->id);

			$groupHeading = self::getDate($event->start_date, $event->all_day)->format($params->get('grouping', ''), true);
			if ($groupHeading != $lastHeading) {
				$lastHeading         = $groupHeading;
				$variables['header'] = $groupHeading;
			}

			if (!empty($event->jcfields)) {
				foreach ($event->jcfields as $field) {
					$variables['field-' . $field->name] = $field;
				}
			}

			$configuration['events'][] = $variables;
		}

		$configuration['canCreate']  = self::canCreateEvent();
		$configuration['createLink'] = \DPCalendarHelperRoute::getFormRoute(0, $return);

		$configuration['calendarNameLabel'] = \JText::_('COM_DPCALENDAR_CALENDAR');
		$configuration['titleLabel']        = \JText::_('COM_DPCALENDAR_FIELD_CONFIG_EVENT_LABEL_TITLE');
		$configuration['dateLabel']         = \JText::_('COM_DPCALENDAR_DATE');
		$configuration['locationLabel']     = \JText::_('COM_DPCALENDAR_LOCATION');
		$configuration['descriptionLabel']  = \JText::_('COM_DPCALENDAR_DESCRIPTION');
		$configuration['commentsLabel']     = \JText::_('COM_DPCALENDAR_FIELD_CONFIG_EVENT_LABEL_COMMENTS');
		$configuration['eventLabel']        = \JText::_('COM_DPCALENDAR_EVENT');
		$configuration['authorLabel']       = \JText::_('COM_DPCALENDAR_FIELD_CONFIG_EVENT_LABEL_AUTHOR');
		$configuration['bookingsLabel']     = \JText::_('COM_DPCALENDAR_FIELD_CONFIG_EVENT_LABEL_BOOKINGS');
		$configuration['bookLabel']         = \JText::_('COM_DPCALENDAR_BOOK');
		$configuration['bookingLabel']      = \JText::_('COM_DPCALENDAR_BOOKED');
		$configuration['capacityLabel']     = \JText::_('COM_DPCALENDAR_FIELD_CAPACITY_LABEL');
		$configuration['capacityUsedLabel'] = \JText::_('COM_DPCALENDAR_FIELD_CAPACITY_USED_LABEL');
		$configuration['hitsLabel']         = \JText::_('COM_DPCALENDAR_FIELD_CONFIG_EVENT_LABEL_HITS');
		$configuration['urlLabel']          = \JText::_('COM_DPCALENDAR_FIELD_CONFIG_EVENT_LABEL_URL');
		$configuration['copyLabel']         = \JText::_('COM_DPCALENDAR_FIELD_CONFIG_EVENT_LABEL_COPY');
		$configuration['copyGoogleLabel']   = \JText::_('COM_DPCALENDAR_FIELD_CONFIG_EVENT_LABEL_COPY_GOOGLE');
		$configuration['copyOutlookLabel']  = \JText::_('COM_DPCALENDAR_FIELD_CONFIG_EVENT_LABEL_COPY_OUTLOOK');
		$configuration['language']          = substr(self::getFrLanguage(), 0, 2);
		$configuration['editLabel']         = \JText::_('JACTION_EDIT');
		$configuration['createLabel']       = \JText::_('JACTION_CREATE');
		$configuration['deleteLabel']       = \JText::_('JACTION_DELETE');

		$configuration['emptyText'] = \JText::_('COM_DPCALENDAR_FIELD_CONFIG_EVENT_LABEL_NO_EVENT_TEXT');

		try {
			$m = new \Mustache_Engine();

			return $m->render($output, $configuration);
		} catch (\Exception $e) {
			echo $e->getMessage();
		}
	}

	public static function renderPrice($price, $currencySymbol = null, $separator = null, $thousandSeparator = null)
	{
		return self::renderLayout(
			'format.price',
			['price' => $price, 'currency' => $currencySymbol, 'separator' => $separator, 'thousands_separator' => $thousandSeparator]
		);
	}

	public static function renderLayout($layout, $data = [])
	{
		// Framework specific content is not loaded
		return \JLayoutHelper::render($layout, $data, null, ['component' => 'com_dpcalendar', 'client' => 0]);
	}

	public static function getStringFromParams($key, $default, $params)
	{
		$text = $params->get($key, $default);
		$text = trim(strip_tags($text));

		if (\JFactory::getLanguage()->hasKey($text)) {
			return \JText::_($text);
		}

		return $params->get($key, $default);
	}

	public static function getAvatar($userId, $email, $params)
	{
		$image          = null;
		$avatarProvider = $params->get('avatar', 1);
		if ($avatarProvider == 2) {
			$size = $params->get('avatar_width', 0);
			if ($size == 0) {
				$size = $params->get('avatar_height', 0);
			}
			if ($size == 0) {
				$size = '';
			} else {
				$size = '?s=' . $size;
			}
			$image = 'https://www.gravatar.com/avatar/' . md5(strtolower(trim($email))) . $size;
		}

		$cbLoader  = JPATH_ADMINISTRATOR . '/components/com_comprofiler/plugin.foundation.php';
		$jomsocial = JPATH_ROOT . '/components/com_community/libraries/core.php';
		$easy      = JPATH_ADMINISTRATOR . '/components/com_easysocial/includes/foundry.php';
		if ((($avatarProvider == 1 && !\JFile::exists($jomsocial)) || $avatarProvider == 4) && \JFile::exists($cbLoader)) {
			include_once $cbLoader;
			$cbUser = \CBuser::getInstance($userId);
			if ($cbUser !== null) {
				$image = $cbUser->getField('avatar', null, 'csv');
			}
			if (empty($image)) {
				$image = selectTemplate() . 'images/avatar/tnnophoto_n.png';
			}
		}
		if (($avatarProvider == 1 || $avatarProvider == 3) && \JFile::exists($jomsocial)) {
			include_once $jomsocial;
			$image = \CFactory::getUser($userId)->getThumbAvatar();
		}
		if (($avatarProvider == 1 || $avatarProvider == 5) && \JFile::exists($easy)) {
			$image = \Foundry::user($userId)->getAvatar();
		}
		if ($image != null) {
			$w = $params->get('avatar_width', 0);
			$h = $params->get('avatar_height', 0);
			if ($w != 0) {
				$w = 'width="' . $w . '"';
			} else if ($h == 0) {
				$w = 'width="80"';
			} else {
				$w = '';
			}
			if ($h != 0) {
				$h = 'height="' . $h . '"';
			} else {
				$h = '';
			}

			return '<img src="' . $image . '" ' . $w . ' ' . $h . ' loading="lazy"/>';
		}

		return '';
	}

	public static function getGoogleLanguage()
	{
		$languages = [
			'ar',
			'bg',
			'bn',
			'ca',
			'cs',
			'da',
			'de',
			'el',
			'en',
			'en-AU',
			'en-GB',
			'es',
			'eu',
			'fa',
			'fi',
			'fil',
			'fr',
			'gl',
			'gu',
			'hi',
			'hr',
			'hu',
			'id',
			'it',
			'iw',
			'ja',
			'kn',
			'ko',
			'lt',
			'lv',
			'ml',
			'mr',
			'nl',
			'nn',
			'no',
			'or',
			'pl',
			'pt',
			'pt-BR',
			'pt-PT',
			'rm',
			'ro',
			'ru',
			'sk',
			'sl',
			'sr',
			'sv',
			'tl',
			'ta',
			'te',
			'th',
			'tr',
			'uk',
			'vi',
			'zh-CN',
			'zh-TW'
		];
		$lang      = self::getFrLanguage();
		if (!in_array($lang, $languages)) {
			$lang = substr($lang, 0, strpos($lang, '-'));
		}
		if (!in_array($lang, $languages)) {
			$lang = 'en';
		}

		return $lang;
	}

	public static function fetchContent($uri)
	{
		if (empty($uri)) {
			return '';
		}

		$content = '';
		try {
			$internal = !filter_var($uri, FILTER_VALIDATE_URL);

			if ($internal && strpos($uri, '/') !== 0) {
				$uri = JPATH_ROOT . '/' . $uri;
			}

			if ($internal) {
				if (\JFolder::exists($uri)) {
					foreach (\JFolder::files($uri, '\.ics', true, true) as $file) {
						$content .= file_get_contents($file);
					}
				} else {
					$content = file_get_contents($uri);
				}
			} else {
				$options = new Registry();
				$options->set('userAgent', 'DPCalendar');
				foreach (['curl', 'socket', 'stream'] as $adapter) {
					$class = 'JHttpTransport' . ucfirst($adapter);
					$http  = new \JHttp($options, new $class($options));

					$u   = \JUri::getInstance($uri);
					$uri = $u->toString(['scheme', 'user', 'pass', 'host', 'port', 'path']);
					$uri .= $u->toString(['query', 'fragment']);

					$language = \JFactory::getUser()->getParam('language', \JFactory::getLanguage()->getTag());
					$headers  = ['Accept-Language' => $language];
					$response = $http->get($uri, $headers);
					$content  = $response->body;

					if (isset($response->headers['Content-Encoding']) && $response->headers['Content-Encoding'] == 'gzip') {
						$content = gzinflate(substr($content, 10, -8));
					}

					break;
				}
			}
		} catch (\Exception $e) {
			return $e;
		}
		if (!empty($content)) {
			return $content;
		}

		return '';
	}

	public static function exportCsv($name, $fieldsToLabels, $valueParser)
	{
		$name     = strtolower($name);
		$realName = str_replace('admin', '', $name);
		$model    = \JModelLegacy::getInstance(ucfirst($name) . 's', 'DPCalendarModel', ['ignore_request' => false]);
		$model->setState('list.limit', 1000);
		$items = $model->getItems();

		$f         = fopen('php://memory', 'w');
		$delimiter = ',';
		$fields    = [];
		if ($items) {
			$line = [];
			foreach ($fieldsToLabels as $fieldLabel) {
				$line[] = $fieldLabel;
			}
			$fields = array_merge($fields, \FieldsHelper::getFields('com_dpcalendar.' . $realName));
			foreach ($fields as $field) {
				$line[] = $field->label;
			}

			fputcsv($f, $line, $delimiter);
		}

		foreach ($items as $item) {
			\JFactory::getApplication()->triggerEvent('onContentPrepare', ['com_dpcalendar.' . $realName, &$item, &$item->params, 0]);
			$line = [];
			foreach ($fieldsToLabels as $fieldName => $fieldLabel) {
				$line[] = $valueParser($fieldName, $item);
			}

			if ($fields) {
				foreach ($fields as $field) {
					if (isset($item->jcfields) && key_exists($field->id, $item->jcfields)) {
						$line[] = html_entity_decode(strip_tags($item->jcfields[$field->id]->value));
					}
				}
			}
			fputcsv($f, $line, $delimiter);
		}
		fseek($f, 0);
		// echo stream_get_contents($f);die;
		header('Content-Type: application/csv');
		header('Content-Disposition: attachement; filename="' . $realName . 's-' . date('YmdHi') . '.csv";');
		fpassthru($f);

		\JFactory::getApplication()->close();
	}

	public static function doPluginAction($plugin, $action, $data = null)
	{
		\JPluginHelper::importPlugin('dpcalendar');

		$enabled = \JPluginHelper::isEnabled('dpcalendar', $plugin);
		if (!$enabled) {
			$db    = \JFactory::getDbo();
			$query = $db->getQuery(true)
				->select('folder AS type, element AS name, params')
				->from('#__extensions')
				->where('type = ' . $db->quote('plugin'))
				->where('folder = ' . $db->quote('dpcalendar'))
				->where('element = ' . $db->quote($plugin));
			$p     = $db->setQuery($query)->loadObject();

			\JLoader::import('dpcalendar.' . $plugin . '.' . $plugin, JPATH_PLUGINS);

			$className  = 'Plg' . $p->type . $p->name;
			$dispatcher = \JEventDispatcher::getInstance();
			$p          = (array)$p;
			new $className($dispatcher, $p);
		}

		$result = \JFactory::getApplication()->triggerEvent('onDPCalendarDoAction', [$action, $plugin]);

		return $result;
	}

	public static function isJoomlaVersion($version, $operator = '==')
	{
		$release = (new \JVersion())->getShortVersion();
		switch ($operator) {
			case '>=':
				return substr($release, 0, strlen($version)) >= $version;
			case '<':
				return substr($release, 0, strlen($version)) < $version;
			case '<=':
				return substr($release, 0, strlen($version)) <= $version;
		}

		return substr($release, 0, strlen($version)) == $version;
	}

	public static function canCreateEvent()
	{
		$user   = \JFactory::getUser();
		$canAdd = $user->authorise('core.create', 'com_dpcalendar') || count($user->getAuthorisedCategories('com_dpcalendar', 'core.create'));

		if (!$canAdd) {
			\JPluginHelper::importPlugin('dpcalendar');
			$tmp = \JFactory::getApplication()->triggerEvent('onCalendarsFetch');
			if (!empty($tmp)) {
				foreach ($tmp as $tmpCalendars) {
					foreach ($tmpCalendars as $calendar) {
						if ($calendar->canCreate) {
							$canAdd = true;
							break;
						}
					}
				}
			}
		}

		return $canAdd;
	}

	public static function isFree()
	{
		return !\JFile::exists(JPATH_ADMINISTRATOR . '/components/com_dpcalendar/tables/booking.php');
	}

	public static function isCaptchaNeeded()
	{
		\JPluginHelper::importPlugin('captcha');

		$userGroups    = \JAccess::getGroupsByUser(\JFactory::getUser()->id, false);
		$accessCaptcha = array_intersect(self::getComponentParameter('captcha_groups', [1]), $userGroups);

		return \JPluginHelper::isEnabled('captcha') && $accessCaptcha;
	}

	public static function sendMessage($message, $error = false, array $data = [])
	{
		ob_clean();

		if ($message) {
			\JFactory::getApplication()->enqueueMessage($message, $error ? 'error' : 'message');
		}
		echo new \JResponseJson($data);

		\JFactory::getApplication()->close();
	}

	/**
	 * Sends a mail to all users of the given component parameter groups.
	 * The user objects are returned where a mail is sent to.
	 *
	 * @param $subject
	 * @param $message
	 * @param $parameter
	 *
	 * @return array
	 */
	public static function sendMail($subject, $message, $parameter, $additionalGroups = [])
	{
		$groups = self::getComponentParameter($parameter);

		if (!is_array($groups)) {
			$groups = [$groups];
		}

		$groups = array_unique(array_filter(array_merge($groups, $additionalGroups)));
		if (empty($groups)) {
			return [];
		}

		$users = [];
		foreach ($groups as $groupId) {
			$users = array_merge($users, \JAccess::getUsersByGroup($groupId));
		}

		$users     = array_unique($users);
		$userMails = [];
		foreach ($users as $user) {
			$u = \JUser::getTable();
			if ($u->load($user)) {
				$mailer = \JFactory::getMailer();
				$mailer->setSubject($subject);
				$mailer->setBody($message);
				$mailer->IsHTML(true);
				$mailer->addRecipient($u->email);
				$mailer->Send();
				$userMails[$user] = $u;
			}
		}

		return $userMails;
	}

	/**
	 * Checks if the haystack starts with the needle.
	 *
	 * @param string $haystack
	 * @param string $needle
	 *
	 * @return boolean
	 */
	public static function startsWith($haystack, $needle)
	{
		return (substr($haystack, 0, strlen($needle)) === $needle);
	}

	/**
	 * Checks if the haystack ends with the needle.
	 *
	 * @param string $haystack
	 * @param string $needle
	 *
	 * @return boolean
	 */
	public static function endsWith($haystack, $needle)
	{
		$length = strlen($needle);
		if ($length == 0) {
			return true;
		}

		return (substr($haystack, -$length) === $needle);
	}

	public static function matches($text, $query)
	{
		$query = str_replace('+', '', $query);
		$tmp   = str_getcsv($query, ' ');

		// str_getcsv creates from '-"foo bar"' > ['-"foo', 'bar"'] it needs to
		// be combined back
		$criteria  = [];
		$appending = null;
		foreach ($tmp as $key => $value) {
			if (self::startsWith($value, '-"')) {
				$criteria[$key] = str_replace('-"', '-', $value);
				$appending      = $key;
				continue;
			}

			if ($appending) {
				$criteria[$appending] .= ' ' . str_replace('"', '', $value);
				if (self::endsWith($value, '"')) {
					$appending = null;
				}
				continue;
			}

			$criteria[$key] = $value;
		}

		$criteria = array_values($criteria);

		foreach ($criteria as $q) {
			if (empty($q)) {
				continue;
			}
			if (self::startsWith($q, '-')) {
				if (strpos($text, substr($q, 1)) !== false) {
					return false;
				}
			} else if (self::startsWith($q, '+')) {
				if (strpos($text, substr($q, 1)) === false) {
					return false;
				}
			} else if (strpos($text, $q) === false) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Sort the given fields array based on the given order. The order is a setting from a subform field.
	 *
	 * @param $fields
	 * @param $order
	 */
	public static function sortFields(&$fields, $order)
	{
		$order = (array)$order;

		// Move captcha to bottom
		if (!in_array('captcha', $order)) {
			foreach ($fields as $index => $field) {
				if (!$field instanceof \JFormField || $field->fieldname != 'captcha') {
					continue;
				}

				unset($fields[$index]);
				$fields[$index] = $field;
				break;
			}
		}

		// Check if empty
		if (!$order) {
			return;
		}

		// Get the field names out of the object
		$order = array_values(array_map(function ($f) {
			return isset($f->field) ? $f->field : $f['field'];
		}, $order));

		// Sort the fields array when needed
		if (!$order) {
			return;
		}

		// get the keys
		$keys = array_keys($fields);

		// Sort the fields
		usort(
			$fields,
			function ($f1, $f2) use ($order, $keys) {
				$fieldName = property_exists($f1, 'fieldname') ? 'fieldname' : 'name';
				$k1        = in_array($f1->{$fieldName}, $order) ? array_search($f1->{$fieldName}, $order) : -1;
				$k2        = in_array($f2->{$fieldName}, $order) ? array_search($f2->{$fieldName}, $order) : -1;

				if ($k1 >= 0 && $k2 < 0) {
					return -1;
				}
				if ($k1 < 0 && $k2 >= 0) {
					return 1;
				}
				if ($k1 >= 0 && $k2 >= 0) {
					return $k1 > $k2 ? 1 : -1;
				}

				return array_search($f1->id, $keys) - array_search($f2->id, $keys);
			}
		);
	}

	public static function parseHtml($text)
	{
		$text = str_replace('\n', PHP_EOL, $text);

		// IE does not handle &apos; entity!
		$text                 = preg_replace('/&apos;/', '&#39;', $text);
		$section_html_pattern = '%# Rev:20100913_0900 github.com/jmrware/LinkifyURL
		# Section text into HTML <A> tags  and everything else.
		(                              # $1: Everything not HTML <A> tag.
		[^<]+(?:(?!<a\b)<[^<]*)*     # non A tag stuff starting with non-"<".
		|      (?:(?!<a\b)<[^<]*)+     # non A tag stuff starting with "<".
		)                              # End $1.
		| (                              # $2: HTML <A...>...</A> tag.
		<a\b[^>]*>                   # <A...> opening tag.
		[^<]*(?:(?!</a\b)<[^<]*)*    # A tag contents.
		</a\s*>                      # </A> closing tag.
		)                              # End $2:
		%ix';
		$text                 = preg_replace_callback($section_html_pattern, [
			'DPCalendarHelper',
			'linkifyHtmlCallback'
		], $text);
		$text                 = nl2br($text);

		return $text;
	}

	public static function linkify($text)
	{
		$url_pattern = '/# Rev:20100913_0900 github.com\/jmrware\/LinkifyURL
		# Match http & ftp URL that is not already linkified.
		# Alternative 1: URL delimited by (parentheses).
		(\()                     # $1  "(" start delimiter.
		((?:ht|f)tps?:\/\/[a-z0-9\-._~!$&\'()*+,;=:\/?#[\]@%]+)  # $2: URL.
		(\))                     # $3: ")" end delimiter.
		| # Alternative 2: URL delimited by [square brackets].
		(\[)                     # $4: "[" start delimiter.
		((?:ht|f)tps?:\/\/[a-z0-9\-._~!$&\'()*+,;=:\/?#[\]@%]+)  # $5: URL.
		(\])                     # $6: "]" end delimiter.
		| # Alternative 3: URL delimited by {curly braces}.
		(\{)                     # $7: "{" start delimiter.
		((?:ht|f)tps?:\/\/[a-z0-9\-._~!$&\'()*+,;=:\/?#[\]@%]+)  # $8: URL.
		(\})                     # $9: "}" end delimiter.
		| # Alternative 4: URL delimited by <angle brackets>.
		(<|&(?:lt|\#60|\#x3c);)  # $10: "<" start delimiter (or HTML entity).
		((?:ht|f)tps?:\/\/[a-z0-9\-._~!$&\'()*+,;=:\/?#[\]@%]+)  # $11: URL.
		(>|&(?:gt|\#62|\#x3e);)  # $12: ">" end delimiter (or HTML entity).
		| # Alternative 5: URL not delimited by (), [], {} or <>.
		(                        # $13: Prefix proving URL not already linked.
		(?: ^                  # Can be a beginning of line or string, or
		| [^=\s\'"\]]          # a non-"=", non-quote, non-"]", followed by
		) \s*[\'"]?            # optional whitespace and optional quote;
		| [^=\s]\s+              # or... a non-equals sign followed by whitespace.
		)                        # End $13. Non-prelinkified-proof prefix.
		( \b                     # $14: Other non-delimited URL.
		(?:ht|f)tps?:\/\/      # Required literal http, https, ftp or ftps prefix.
		[a-z0-9\-._~!$\'()*+,;=:\/?#[\]@%]+ # All URI chars except "&" (normal*).
		(?:                    # Either on a "&" or at the end of URI.
		(?!                  # Allow a "&" char only if not start of an...
		&(?:gt|\#0*62|\#x0*3e);                  # HTML ">" entity, or
		| &(?:amp|apos|quot|\#0*3[49]|\#x0*2[27]); # a [&\'"] entity if
		[.!&\',:?;]?        # followed by optional punctuation then
		(?:[^a-z0-9\-._~!$&\'()*+,;=:\/?#[\]@%]|$)  # a non-URI char or EOS.
		) &                  # If neg-assertion true, match "&" (special).
		[a-z0-9\-._~!$\'()*+,;=:\/?#[\]@%]* # More non-& URI chars (normal*).
		)*                     # Unroll-the-loop (special normal*)*.
		[a-z0-9\-_~$()*+=\/#[\]@%]  # Last char can\'t be [.!&\',;:?]
		)                        # End $14. Other non-delimited URL.
		/imx';
		$url_replace = '$1$4$7$10$13<a href="$2$5$8$11$14">$2$5$8$11$14</a>$3$6$9$12';

		return preg_replace($url_pattern, $url_replace, $text);
	}

	public static function linkifyHtmlCallback($matches)
	{
		if (isset($matches[2])) {
			return $matches[2];
		}

		return self::linkify($matches[1]);
	}

	public static function fixImageLinks($buffer)
	{
		$base = \JUri::base(true) . '/';

		// Copied from SEF plugin
		// Check for all unknown protocals (a protocol must contain at least one alpahnumeric character followed by a ":").
		$protocols  = '[a-zA-Z0-9\-]+:';
		$attributes = ['href=', 'src=', 'poster='];

		foreach ($attributes as $attribute) {
			if (strpos($buffer, $attribute) !== false) {
				$regex  = '#\s' . $attribute . '"(?!/|' . $protocols . '|\#|\')([^"]*)"#m';
				$buffer = preg_replace($regex, ' ' . $attribute . '"' . $base . '$1"', $buffer);
			}
		}

		return $buffer;
	}

	public static function increaseMemoryLimit($limit)
	{
		$memMax = trim(@ini_get('memory_limit'));
		if ($memMax) {
			$last = strtolower($memMax[strlen($memMax) - 1]);
			switch ($last) {
				case 'g':
					$memMax = (int)$memMax * 1024;
				// Gigabyte
				case 'm':
					$memMax = (int)$memMax * 1024;
				// Megabyte
				case 'k':
					$memMax = (int)$memMax * 1024;
				// Kilobyte
			}

			if ($memMax > $limit) {
				return true;
			}

			@ini_set('memory_limit', $limit);
		}

		return trim(@ini_get('memory_limit')) == $limit;
	}

	public static function getOppositeBWColor($color)
	{
		$color = trim($color, '#');
		$r     = hexdec(substr($color, 0, 2));
		$g     = hexdec(substr($color, 2, 2));
		$b     = hexdec(substr($color, 4, 2));
		$yiq   = (($r * 299) + ($g * 587) + ($b * 114)) / 1000;

		return ($yiq >= 128) ? '000000' : 'ffffff';
	}
}
