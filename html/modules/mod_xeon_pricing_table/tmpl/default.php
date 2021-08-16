<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_xeon_pricing_table
 * @copyright   Copyright (C) 2010 - 2013 JoomShaper. All rights reserved.
 * @license     GNU General Public License version 2 or later; 
 */

// no direct access
defined('_JEXEC') or die;
$count = count($data);
?>
    <div class="row-fluid">
        <?php foreach($data as $index=>$value): ?>
             <div class="span<?php echo round(12/$count); ?> <?php echo ($value==$count-1) ? ' last-child': ''; ?> ">
                <ul class="plan<?php echo $value['featured'] ? ' featured' : ''; ?>">
                    <li class="plan-name"><?php echo $value['name']; ?></li>
                    <li class="plan-price"><?php echo $value['price']; ?></li>
                    <li><?php echo $value['details']; ?></li>
                    <li class="plan-action"><a href="<?php echo $value['signuplink'];?>" class="btn btn-primary"><?php echo $value['signup'];?></a></li>
                </ul>
            </div>
        <?php endforeach; ?>
    </div>

       


















