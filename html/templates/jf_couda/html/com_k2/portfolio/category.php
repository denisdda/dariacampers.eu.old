<?php
/**
 * @version    2.7.x
 * @package    K2
 * @author     JoomlaWorks http://www.joomlaworks.net
 * @copyright  Copyright (c) 2006 - 2016 JoomlaWorks Ltd. All rights reserved.
 * @license    GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 */

// no direct access
defined('_JEXEC') or die;

?>

<!-- Start K2 Category Layout -->
<div id="k2Container" class="itemListView<?php if($this->params->get('pageclass_sfx')) echo ' '.$this->params->get('pageclass_sfx'); ?>">

	<?php if($this->params->get('show_page_title')): ?>
	<!-- Page title -->
	<div class="componentheading<?php echo $this->params->get('pageclass_sfx')?>">
		<?php echo $this->escape($this->params->get('page_title')); ?>
	</div>
	<?php endif; ?>

	<?php if($this->params->get('catFeedIcon')): ?>
	<!-- RSS feed icon -->
	<div class="k2FeedIcon">
		<a href="<?php echo $this->feed; ?>" title="<?php echo JText::_('K2_SUBSCRIBE_TO_THIS_RSS_FEED'); ?>">
			<span><?php echo JText::_('K2_SUBSCRIBE_TO_THIS_RSS_FEED'); ?></span>
		</a>
		<div class="clr"></div>
	</div>
	<?php endif; ?>

	<?php if(isset($this->category) || ( $this->params->get('subCategories') && isset($this->subCategories) && count($this->subCategories) )): ?>
	<!-- Blocks for current category and subcategories -->
	<div class="itemListCategoriesBlock">

		<?php if(isset($this->category) && ( $this->params->get('catImage') || $this->params->get('catTitle') || $this->params->get('catDescription') || $this->category->event->K2CategoryDisplay )): ?>
		<!-- Category block -->
		<div class="itemListCategory">

			<?php if(isset($this->addLink)): ?>
			<!-- Item add link -->
			<span class="catItemAddLink">
				<a data-k2-modal="edit" href="<?php echo $this->addLink; ?>">
					<?php echo JText::_('K2_ADD_A_NEW_ITEM_IN_THIS_CATEGORY'); ?>
				</a>
			</span>
			<?php endif; ?>

			<?php if($this->params->get('catImage') && $this->category->image): ?>
			<!-- Category image -->
			<img alt="<?php echo K2HelperUtilities::cleanHtml($this->category->name); ?>" src="<?php echo $this->category->image; ?>" style="width:<?php echo $this->params->get('catImageWidth'); ?>px; height:auto;" />
			<?php endif; ?>

			<?php if($this->params->get('catTitle')): ?>
			<!-- Category title -->
			<h2><?php echo $this->category->name; ?><?php if($this->params->get('catTitleItemCounter')) echo ' ('.$this->pagination->total.')'; ?></h2>
			<?php endif; ?>

			<?php if($this->params->get('catDescription')): ?>
			<!-- Category description -->
			<div><?php echo $this->category->description; ?></div>
			<?php endif; ?>

			<!-- K2 Plugins: K2CategoryDisplay -->
			<?php echo $this->category->event->K2CategoryDisplay; ?>

			<div class="clr"></div>
		</div>
		<?php endif; ?>

		<?php if($this->params->get('subCategories') && isset($this->subCategories) && count($this->subCategories)): ?>
		<!-- Subcategories -->
		<div class="portfolio-filter">
			<ul class="reset-list">
				<li class="filter-item active" data-filter="*">All</li>
				<?php foreach($this->subCategories as $key=>$subCategory): ?>
				
					<li class="filter-item" data-filter=".<?php echo $subCategory->alias ?>">
						<?php echo $subCategory->name; ?>
					</li>
					
				<?php endforeach; ?>
			</ul>
		</div>
		<?php endif; ?>

	</div>
	<?php endif; ?>

	<?php if((isset($this->leading) || isset($this->primary) || isset($this->secondary) || isset($this->links)) && (count($this->leading) || count($this->primary) || count($this->secondary) || count($this->links))): ?>
	<!-- Item list -->
	<div class="itemList portfolio-wrapper">

		<?php if(isset($this->leading) && count($this->leading)): ?>
		<!-- Leading items -->
		
			<?php foreach($this->leading as $key=>$item): ?>

			<div class="portfolio-item <?php echo $item->categoryalias; ?>">
				<?php
					// Load category_item.php by default
					$this->item = $item;
					echo $this->loadTemplate('item');
				?>
			</div>

			<?php endforeach; ?>
		
		<?php endif; ?>

		<?php if(isset($this->primary) && count($this->primary)): ?>
		<!-- Primary items -->
		
			<?php foreach($this->primary as $key=>$item): ?>

			<div class="portfolio-item <?php echo $item->categoryalias; ?>">
				<?php
					// Load category_item.php by default
					$this->item = $item;
					echo $this->loadTemplate('item');
				?>
			</div>

			<?php endforeach; ?>
		
		<?php endif; ?>

		<?php if(isset($this->secondary) && count($this->secondary)): ?>
		<!-- Secondary items -->
		
			<?php foreach($this->secondary as $key=>$item): ?>

			<div class="portfolio-item <?php echo $item->categoryalias; ?>">
				<?php
					// Load category_item.php by default
					$this->item = $item;
					echo $this->loadTemplate('item');
				?>
			</div>

			<?php endforeach; ?>
		
		<?php endif; ?>

		<?php if(isset($this->links) && count($this->links)): ?>
		<!-- Link items -->

			<?php foreach($this->links as $key=>$item): ?>

			<div class="portfolio-item <?php echo $item->categoryalias; ?>">
				<?php
					// Load category_item.php by default
					$this->item = $item;
					echo $this->loadTemplate('item');
				?>
			</div>

			<?php endforeach; ?>
		
		<?php endif; ?>

	</div>

	<!-- Pagination -->
	<?php if($this->pagination->getPagesLinks()): ?>
	<div class="k2Pagination">
		<?php if($this->params->get('catPagination')) echo $this->pagination->getPagesLinks(); ?>
		<div class="clr"></div>
		<?php if($this->params->get('catPaginationResults')) echo $this->pagination->getPagesCounter(); ?>
	</div>
	<?php endif; ?>

	<?php endif; ?>
</div>

<!-- End K2 Category Layout -->
