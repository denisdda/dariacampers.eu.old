<?php
/**
 * @package		Joomla.Site
 * @subpackage	mod_xeon_slider
 * @copyright	Copyright (C) 2010 - 2013 JoomShaper. All rights reserved.
 * @license		GNU General Public License version 2 or later; 
 */

// no direct access
defined('_JEXEC') or die;

$count 		= count($data);

$i=1;
$j=1;

?>

<div id="xeon-team<?php echo $module->id; ?>" class="carousel scale<?php echo $params->get('moduleclass_sfx') ?>">

    <div class="carousel-inner">
    	<?php foreach ($data as $index => $value):  ?>
   			<!-- Carousel items active-->
   			<?php
   			if($i==1) { ?>
   			<div class="item<?php echo ($index==0) ? ' active' : ''; ?> ">
   				<div class="row-fluid">
   					<?php } ?>
            		<div class="span4 <?php echo ($index==$count-1) ? 'last-child': ''; ?> ">
                        <div class="member <?php echo ($index==$count-1) ? 'last-child': ''; ?>">
                            <p><img class="img-thumbnail img-circle" src="<?php echo $value['img'] ?>" alt="" ></p>
                            <h3 class"sp-team-name"><?php echo $value['name'] ?></h3>
                            <p class="sp-designation"><?php echo $value['desg'] ?></p>
                        </div>
                    </div>
                    <?php if(($i == 3) || ( $j==$count)){ ?>
                </div>
            </div><!--/.item-->

          <?php 
            	$i=0;
        	}
        	$i++;
        	$j++;
        ?>
    	<?php endforeach; ?>
    </div>

    <!-- Carousel nav -->
    <a class="left-arrow" href="#xeon-team<?php echo $module->id; ?>" data-slide="prev"><i class="icon-angle-left"></i></a>
    <a class="nav-right" href="#xeon-team<?php echo $module->id; ?>" data-slide="next"><i class="icon-angle-right"></i></a>
</div>