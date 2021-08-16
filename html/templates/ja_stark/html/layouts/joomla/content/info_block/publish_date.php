<?php
/**
 * @package     Joomla.Site
 * @subpackage  Layout
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

if (($j3 = \T4\Helper\Layout::j3(__FILE__))) {
	include $j3;
	return;
}

defined('JPATH_BASE') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

?>
<dd class="published">
	<time datetime="<?php echo HTMLHelper::_('date', $displayData['item']->publish_up, 'c'); ?>" itemprop="datePublished">
		<?php echo Text::sprintf('TPL_COM_CONTENT_PUBLISHED_DATE_ON', HTMLHelper::_('date', $displayData['item']->publish_up, Text::_('DATE_FORMAT_LC3'))); ?>
	</time>
</dd>
