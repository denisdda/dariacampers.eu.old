<?php
/**
 * @package		Joomla.Site
 * @subpackage	mod_xeon_slider
 * @copyright	Copyright (C) 2010 - 2013 JoomShaper. All rights reserved.
 * @license		GNU General Public License version 2 or later; 
 */

// no direct access
defined('_JEXEC') or die;

$count = count($data);

?>


<div id="xeon-slider<?php echo $module->id; ?>" class="carousel <?php echo $params->get('moduleclass_sfx') ?> ">
	
	<!-- Carousel items -->
	<div class="carousel-inner">
		<?php foreach($data as $index=>$value): ?>
			<div class="item<?php echo ($index==0) ? ' active' : ''; ?> ">
				<div class="container">
					<!-- Carousel content -->
					 <div class="carousel-content">
					 		<div class="slider-title">
					 			<?php 
					 				if(isset($value['title']) and !empty($value['title']) ) echo '<h1>' . $value['title'] . '</h1>';
					 			?>
					 		</div>
					 		<div class="slider-text">
					 			<?php 
					 				if(isset($value['desc']) and !empty($value['desc']) ) echo '<p class="lead">' . $value['desc'] . '</P>';
					 			?>
					 		</div>
					 </div>
					 <!-- Carousel content end -->

				</div>
			</div>
		<?php endforeach; ?>
	</div>

    <!-- Carousel nav -->
    <a class="nav-left" href="#xeon-slider<?php echo $module->id; ?>" data-slide="prev"><i class="icon-angle-left"></i></a>
    <a class="nav-right" href="#xeon-slider<?php echo $module->id; ?>" data-slide="next"><i class="icon-angle-right"></i></a>
</div>






