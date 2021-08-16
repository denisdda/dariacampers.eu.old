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
		    <a class="nivo-image" href="<?php echo $this->item->imageLarge; ?>" data-lightbox-gallery="gallery">
		    	<img src="<?php echo $this->item->image; ?>" alt="<?php if(!empty($this->item->image_caption)) echo K2HelperUtilities::cleanHtml($this->item->image_caption); else echo K2HelperUtilities::cleanHtml($this->item->title); ?>" style="width:<?php echo $this->item->imageWidth; ?>px; height:auto;" />
		    </a>
		  </span>
	  </div>
	  <?php endif; ?>

	  <!-- Plugins: AfterDisplay -->
	  <?php echo $this->item->event->AfterDisplay; ?>

	  <!-- K2 Plugins: K2AfterDisplay -->
	  <?php echo $this->item->event->K2AfterDisplay; ?>

</div>
<!-- End K2 Item Layout -->
