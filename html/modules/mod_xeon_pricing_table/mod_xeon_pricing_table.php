<?php
/**
 * @package		Joomla.Site
 * @subpackage	mod_xeon_pricing_table
 * @copyright	Copyright (C) 2010 - 2013 JoomShaper. All rights reserved.
 * @license		GNU General Public License version 2 or later; 
 */

// no direct access
defined('_JEXEC') or die;

$data = array();

//Plan 1
if( $params->get('plan1_name') ){
	$data[0][ 'name' ] 				= $params->get('plan1_name');
	$data[0][ 'price' ] 			= $params->get('plan1_price');
	$data[0][ 'details' ] 			= $params->get('plan1_details');
	$data[0][ 'signup' ] 			= $params->get('plan1_signup');
	$data[0][ 'signuplink' ] 		= $params->get('plan1_signuplink');
	$data[0][ 'featured' ] 			= $params->get('plan1_is_featured');

}

//Plan 2
if( $params->get('plan2_name') ){
	$data[1][ 'name' ] 				= $params->get('plan2_name');
	$data[1][ 'price' ] 			= $params->get('plan2_price');
	$data[1][ 'details' ] 			= $params->get('plan2_details');
	$data[1][ 'signup' ] 			= $params->get('plan2_signup');
	$data[1][ 'signuplink' ] 		= $params->get('plan2_signuplink');
	$data[1][ 'featured' ] 			= $params->get('plan2_is_featured');

}


//Plan 3
if( $params->get('plan3_name') ){
	$data[2][ 'name' ] 				= $params->get('plan3_name');
	$data[2][ 'price' ] 			= $params->get('plan3_price');
	$data[2][ 'details' ] 			= $params->get('plan3_details');
	$data[2][ 'signup' ] 			= $params->get('plan3_signup');
	$data[2][ 'signuplink' ] 		= $params->get('plan3_signuplink');
	$data[2][ 'featured' ] 			= $params->get('plan3_is_featured');

}

//Plan 4
if( $params->get('plan4_name') ){
	$data[3][ 'name' ] 				= $params->get('plan4_name');
	$data[3][ 'price' ] 			= $params->get('plan4_price');
	$data[3][ 'details' ] 			= $params->get('plan4_details');
	$data[3][ 'signup' ] 			= $params->get('plan4_signup');
	$data[3][ 'signuplink' ] 		= $params->get('plan4_signuplink');
	$data[3][ 'featured' ] 			= $params->get('plan4_is_featured');

}

require JModuleHelper::getLayoutPath('mod_xeon_pricing_table');