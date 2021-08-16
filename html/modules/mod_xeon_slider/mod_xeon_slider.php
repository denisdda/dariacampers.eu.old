<?php
/**
 * @package		Joomla.Site
 * @subpackage	mod_xeon_slider
 * @copyright	Copyright (C) 2010 - 2013 JoomShaper. All rights reserved.
 * @license		GNU General Public License version 2 or later; 
 */

// no direct access
defined('_JEXEC') or die;

$data = array();

//Slide 1
if( $params->get('slide1_title') ){
	$data[0][ 'title' ] 			= $params->get('slide1_title');
	$data[0][ 'desc' ] 				= $params->get('slide1_desc');
}

//Slide 2
if( $params->get('slide2_title') ){
	$data[1][ 'title' ] 			= $params->get('slide2_title');
	$data[1][ 'desc' ] 				= $params->get('slide2_desc');
}

//Slide 3
if( $params->get('slide3_title') ){
	$data[2][ 'title' ] 			= $params->get('slide3_title');
	$data[2][ 'desc' ] 				= $params->get('slide3_desc');
}

require JModuleHelper::getLayoutPath('mod_xeon_slider');