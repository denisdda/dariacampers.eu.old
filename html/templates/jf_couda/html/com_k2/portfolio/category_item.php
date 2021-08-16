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

// Define default image size (do not change)
K2HelperUtilities::setDefaultImage($this->item, 'itemlist', $this->params);

?>

<!-- Start K2 Item Layout -->
<div class="catItemView group<?php echo ucfirst($this->item->itemGroup); ?><?php echo ($this->item->featured) ? ' catItemIsFeatured' : ''; ?><?php if($this->item->params->get('pageclass_sfx')) echo ' '.$this->item->params->get('pageclass_sfx'); ?>">

	<!-- Plugins: BeforeDisplay -->
	<?php echo $this->item->event->BeforeDisplay; ?>

	<!-- K2 Plugins: K2BeforeDisplay -->
	<?php echo $this->item->event->K2BeforeDisplay; ?>
	
	<?php if($this->item->params->get('catItemImage') && !empty($this->item->image)): ?>
	  <!-- Item Image -->
	  <div class="catItemImageBlock">
		  <span class="catItemImage">
		    <a href="<?php echo $this->item->link; ?>" title="<?php if(!empty($this->item->image_caption)) echo K2HelperUtilities::cleanHtml($this->item->image_caption); else echo K2HelperUtilities::cleanHtml($this->item->title); ?>">
		    	<img src="<?php echo $this->item->image; ?>" alt="<?php if(!empty($this->item->image_caption)) echo K2HelperUtilities::cleanHtml($this->item->image_caption); else echo K2HelperUtilities::cleanHtml($this->item->title); ?>" style="width:<?php echo $this->item->imageWidth; ?>px; height:auto;" />
		    </a>
		  </span>
	  </div>
	  <?php endif; ?>
	
	<div class="portfolio-caption"><div class="portfolio-caption-inner">
		<div class="catItemHeader">
			<?php if($this->item->params->get('catItemDateCreated')): ?>
			<!-- Date created -->
			<span class="catItemDateCreated">
				<?php echo JHTML::_('date', $this->item->created , JText::_('K2_DATE_FORMAT_LC2')); ?>
			</span>
			<?php endif; ?>

		  <?php if($this->item->params->get('catItemTitle')): ?>
		  <!-- Item title -->
		  <h3 class="catItemTitle">
				<?php if(isset($this->item->editLink)): ?>
				<!-- Item edit link -->
				<span class="catItemEditLink">
					<a data-k2-modal="edit" href="<?php echo $this->item->editLink; ?>">
						<?php echo JText::_('K2_EDIT_ITEM'); ?>
					</a>
				</span>
				<?php endif; ?>

			<?php if ($this->item->params->get('catItemTitleLinked')): ?>
				<a href="<?php echo $this->item->link; ?>">
				<?php echo $this->item->title; ?>
			</a>
			<?php else: ?>
			<?php echo $this->item->title; ?>
			<?php endif; ?>

			<?php if($this->item->params->get('catItemFeaturedNotice') && $this->item->featured): ?>
			<!-- Featured flag -->
			<span>
				<sup>
					<?php echo JText::_('K2_FEATURED'); ?>
				</sup>
			</span>
			<?php endif; ?>
		  </h3>
		  <?php endif; ?>

			<?php if($this->item->params->get('catItemAuthor')): ?>
			<!-- Item Author -->
			<span class="catItemAuthor">
				<?php echo JText::_('TPL_JF_COUDA_BY_AUTHOR'); ?>				
				<?php echo $this->item->author->name; ?>
			</span>
			<?php endif; ?>
	  </div>

	  <!-- Plugins: AfterDisplayTitle -->
	  <?php echo $this->item->event->AfterDisplayTitle; ?>

	  <!-- K2 Plugins: K2AfterDisplayTitle -->
	  <?php echo $this->item->event->K2AfterDisplayTitle; ?>

		<?php if ($this->item->params->get('catItemReadMore')): ?>
		<!-- Item "read more..." link -->
		<div class="catItemReadMore">
			<a class="k2ReadMore" href="<?php echo $this->item->link; ?>">
				<?php echo JText::_('K2_READ_MORE'); ?>
			</a>
		</div>
		<?php endif; ?>

	</div></div>

	  <!-- Plugins: AfterDisplay -->
	  <?php echo $this->item->event->AfterDisplay; ?>

	  <!-- K2 Plugins: K2AfterDisplay -->
	  <?php echo $this->item->event->K2AfterDisplay; ?>

</div>
<!-- End K2 Item Layout -->
