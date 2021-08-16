<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_content
 * @copyright   Copyright (C) Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Layout\FileLayout;
use Joomla\CMS\Language\Associations;

HTMLHelper::addIncludePath(JPATH_COMPONENT . '/helpers');

// Create shortcuts to some parameters.
$params = $this->item->params;
$images = json_decode($this->item->images);
$urls = json_decode($this->item->urls);
$canEdit = $params->get('access-edit');
$user = Factory::getUser();
$info = $params->get('info_block_position', 0);

// Check if associations are implemented. If they are, define the parameter.
$assocParam = (Associations::isEnabled() && $params->get('show_associations'));

if (!empty($this->item->pagination) && $this->item->pagination && !$this->item->paginationposition && $this->item->paginationrelative) {
    echo $this->item->pagination;
}
?>
<article class="uk-article item-page<?php echo $this->pageclass_sfx; ?>" itemscope itemtype="https://schema.org/Article">

    <?php
    // Todo Not that elegant would be nice to group the params
    $useDefList = ($params->get('show_modify_date') ||
        $params->get('show_publish_date') ||
        $params->get('show_create_date') ||
        $params->get('show_hits') ||
        $params->get('show_category') ||
        $params->get('show_parent_category') ||
        $params->get('show_author') ||
        $assocParam);
    ?>

    <?php if ($params->get('show_page_heading') && !$params->get('show_title')) { ?>
    <h1 class="uk-article-title uk-margin-medium-bottom" itemprop="headline"><?php echo $this->escape($params->get('page_heading')); ?></h1>
    <?php } elseif ($params->get('show_title') != false) { ?>
    <h1 class="uk-article-title uk-margin-medium-bottom" itemprop="headline"><?php echo $this->escape($this->item->title); ?></h1>
    <?php } else { ?>
    <h1 class="uk-hidden" itemprop="headline"><?php echo $this->escape($this->item->title); ?></h1>
    <?php } ?>

    <meta itemprop="inLanguage" content="<?php echo ($this->item->language === '*') ? Factory::getConfig()->get('language') : $this->item->language; ?>" />

    <?php
    if (!$this->print) {
        if ($canEdit || $params->get('show_print_icon') || $params->get('show_email_icon')) {
            echo LayoutHelper::render('joomla.content.icons', array('params' => $params, 'item' => $this->item, 'print' => false));
        }
    } else {
        if ($useDefList) {
            echo HTMLHelper::_('icon.print_screen', $this->item, $params, ['class' => 'uk-button uk-button-link', 'data-uk-tooltip' => str_replace(['<', '>'], ['«', '»'], Text::sprintf('JGLOBAL_PRINT_TITLE', $this->item->title))]);
        }
    }

    // Content is generated by content plugin event "onContentAfterTitle"
    echo $this->item->event->afterDisplayTitle;
    ?>

    <div class="uk-article-meta">
        <?php if ($this->item->state == 0) { ?>
        <div class="uk-text-warning"><?php echo Text::_('JUNPUBLISHED'); ?></div>
        <?php } ?>

        <?php if (strtotime($this->item->publish_up) > strtotime(Factory::getDate())) { ?>
        <div class="uk-text-warning"><?php echo Text::_('JNOTPUBLISHEDYET'); ?></div>
        <?php } ?>

        <?php if ((strtotime($this->item->publish_down) < strtotime(Factory::getDate())) && $this->item->publish_down != Factory::getDbo()->getNullDate()) { ?>
        <div class="uk-text-warning"><?php echo Text::_('JEXPIRED'); ?></div>
        <?php } ?>

        <?php
        if ($useDefList && ($info == 0 || $info == 2)) {
            // Todo: for Joomla4 joomla.content.info_block.block can be changed to joomla.content.info_block
            echo LayoutHelper::render('joomla.content.info_block.block', array('item' => $this->item, 'params' => $params, 'position' => 'above'));
        }
        ?>
    </div>

    <?php

    if ($info == 0 && $params->get('show_tags', 1) && !empty($this->item->tags->itemTags)) {
        $this->item->tagLayout = new FileLayout('joomla.content.tags');

        echo $this->item->tagLayout->render($this->item->tags->itemTags);
    }

    // Content is generated by content plugin event "onContentBeforeDisplay"
    echo $this->item->event->beforeDisplayContent;

    if (isset($urls) && ((!empty($urls->urls_position) && ($urls->urls_position == '0')) || ($params->get('urls_position') == '0' && empty($urls->urls_position))) || (empty($urls->urls_position) && (!$params->get('urls_position')))) {
        echo $this->loadTemplate('links');
    }

    if ($params->get('access-view')) {
        echo LayoutHelper::render('joomla.content.full_image', $this->item);
    }

    if (!empty($this->item->pagination) && $this->item->pagination && !$this->item->paginationposition && !$this->item->paginationrelative) {
        echo $this->item->pagination;
    }

    if (isset($this->item->toc)) {
        echo $this->item->toc;
    }
    ?>

    <div itemprop="articleBody">
        <?php echo $this->item->text; ?>
    </div>

    <?php
    if ($info == 1 || $info == 2) {
        if ($useDefList) {
            // Todo: for Joomla4 joomla.content.info_block.block can be changed to joomla.content.info_block
            echo LayoutHelper::render('joomla.content.info_block.block', array('item' => $this->item, 'params' => $params, 'position' => 'below'));
        }
        if ($params->get('show_tags', 1) && !empty($this->item->tags->itemTags)) {
            $this->item->tagLayout = new FileLayout('joomla.content.tags');
            echo $this->item->tagLayout->render($this->item->tags->itemTags);
        }
    }

    if (!empty($this->item->pagination) && $this->item->pagination && $this->item->paginationposition && !$this->item->paginationrelative) {
        echo $this->item->pagination;
    }

    if (isset($urls) && ((!empty($urls->urls_position) && ($urls->urls_position == '1')) || ($params->get('urls_position') == '1'))) {
        echo $this->loadTemplate('links');
    }
    // Optional teaser intro text for guests
    elseif ($params->get('show_noauth') == true && $user->get('guest')) {
        echo LayoutHelper::render('joomla.content.intro_image', $this->item);
        echo HTMLHelper::_('content.prepare', $this->item->introtext);

        // Optional link to let them register to see the whole article.
        if ($params->get('show_readmore') && $this->item->fulltext != null) {
            $menu = Factory::getApplication()->getMenu();
            $active = $menu->getActive();
            $itemId = $active->id;
            $link = new Uri(Route::_('index.php?option=com_users&view=login&Itemid=' . $itemId, false));
            $link->setVar('return', base64_encode(ContentHelperRoute::getArticleRoute($this->item->slug, $this->item->catid, $this->item->language)));
    ?>
    <p class="readmore">
        <a href="<?php echo $link; ?>" class="uk-button uk-button-text">
            <?php
            $attribs = json_decode($this->item->attribs);
            if ($attribs->alternative_readmore == null) {
                echo Text::_('COM_CONTENT_REGISTER_TO_READ_MORE');
            } elseif ($readmore = $attribs->alternative_readmore) {
                echo $readmore;
                if ($params->get('show_readmore_title', 0) != 0) {
                    echo HTMLHelper::_('string.truncate', $this->item->title, $params->get('readmore_limit'));
                }
            } elseif ($params->get('show_readmore_title', 0) == 0) {
                echo Text::sprintf('COM_CONTENT_READ_MORE_TITLE');
            } else {
                echo Text::_('COM_CONTENT_READ_MORE');
                echo HTMLHelper::_('string.truncate', $this->item->title, $params->get('readmore_limit'));
            }
            ?>
        </a>
    </p>
    <?php
        }
    }

    if (!empty($this->item->pagination) && $this->item->pagination && $this->item->paginationposition && $this->item->paginationrelative) {
        echo $this->item->pagination;
    }

    // Content is generated by content plugin event "onContentAfterDisplay"
    echo $this->item->event->afterDisplayContent;
    ?>
</article>
